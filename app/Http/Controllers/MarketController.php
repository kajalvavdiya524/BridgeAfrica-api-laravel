<?php

namespace App\Http\Controllers;

use App\Traits\Transformer;
use App\Business;
use App\Market;
use App\Category;
use App\Subcategory;
use App\Filter;
use App\CategoryMarket;
use App\MarketSubcategory;
use App\FilterMarket;
use Illuminate\Http\Request;
use App\Http\Requests\MarketRequest;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MarketController extends Controller
{
    private $limit;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->limit = config()->get('app.pagination');
    }

    public function index()
    {
        $markets = Market::paginate($this->limit);
        $meta = Transformer::transformCollection($markets);
        $data = Transformer::markets($markets);
        $message = 'Market Product Listing Successfully.';
        return apiResponse($data, $message , 200, $meta);        
    }

    public function businessProducts(Request $request, $id){
        $request->merge(['business_id' => $id]);
        $request->validate([
            'business_id' => 'required|integer|exists:businesses,id',
        ]);
        $businessMarkets = Market::where('business_id', $id)->paginate($this->limit);
        $meta = Transformer::transformCollection($businessMarkets);
        $data = Transformer::markets($businessMarkets);

        $message = 'Business Market Product Listing Successfully.';
        return apiResponse($data, $message , 200, $meta);
    }

    /**
     * list of business Categories
     */
    public function businessCategoryList(Request $request, $id){
        $request->merge(['business_id' => $id]);
        $request->validate([
            'business_id' => 'required|integer|exists:businesses,id'
        ]);

        try{
            $business = Business::findorfail($id);
            $data = $business->category()->get();

            $message = 'Business Categories Listing Successfully.';
            $statusCode = '200';
        }
        catch(ModelNotFoundException $exception){
            $data = [];
            $message = 'Business does not exists!';
            $statusCode = '404';
        }
        catch(Exception $exception){
            $data = [];
            $message = $exception->getMessage();
            $statusCode = '500';
        }
        
        return apiResponse($data, $message , 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function store(MarketRequest $request)
    {
        $DirName = 'market';
        $file = $request->file('picture');
        $fileDetails = $request->picture;

        $data = $request->except(['categoryId']);
        $data = $request->except(['subCategoryId']);
        $data = $request->except(['filterId']);

        $authUser = $request->user();
        $data = $request->all();
        $data['picture'] = imageStorage($DirName, $file, $fileDetails);
        $market = Market::create($data);

        if($request->has('categoryId')){
            $categoryID = explode(',', $request->categoryId);
            $market->categoryMarket()->attach($categoryID);
        }
        if($request->has('subCategoryId')){
            $subCategoryID = explode(',', $request->subCategoryId);
            $market->marketSubcategory()->attach($subCategoryID);
        }
        if($request->has('filterId')){
            $filterID = explode(',', $request->filterId);
            $market->filterMarket()->attach($filterID);
        }

        $message = 'Product added successfully.';
        return apiResponse($data, $message, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try{
            $market= Market::findorfail($id);
            $data = Transformer::market($market);
            $message = 'Product listed successfully.';
            $statusCode = '200';
        }
        catch(ModelNotFoundException $exception){
            $data = [];
            $message = 'Product does not exists!';
            $statusCode = '404';
        }
        catch(Exception $exception){
            $data = [];
            $message = $exception->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Market $market)
    {
        $request->validate([
            'name' =>  'string',
            'description' =>  'string',
            'picture' =>  'mimes:jpeg,png,jpg|max:2048',
            'price' =>  'numeric',
            'discount_price' =>  'numeric|nullable',
            'on_discount' => 'boolean',
            'condition' =>  'string',
            'discount_price'=> 'numeric',
            'business_id' =>  'integer|exists:businesses,id',
            'categoryId.*' => 'integer|exists:categories,id',
            'subCategoryId.*' => 'integer|exists:subcategories,id',
            'filterId.*' => 'integer|exists:filters,id'
        ]);

        try{
            if($request->hasfile('picture')){
                Storage::delete($market->picture);
            }

            $data = $request->all();

            if($request->hasfile('picture')){
                $DirName = 'market';
                $file = $request->file('picture');
                $fileDetails = $request->picture; 
                $data['picture'] = imageStorage($DirName, $file, $fileDetails);
            }
            else{
                $data['picture'] = $market->picture;
            }

            $markets = $market->update($data);

            if($request->has('categoryId')){
                $categoryID = explode(',', $request->categoryId);
                $market->categoryMarket()->sync($categoryID);
            }

            if($request->has('sub_category_id')){
                $subCategoryID = explode(',', $request->subCategoryId);
                $market->marketSubcategory()->sync($subCategoryID);
            }

            if($request->has('filter_id')){
                $filterID = explode(',', $request->filterId);
                $market->filterMarket()->sync($filterID);
            }

            $message = 'Product updated successfully.';
            $statusCode = '200';

        } catch(ModelNotFoundException $expection){
            $data = [];
            $message = 'Product does not exists!';
            $statusCode = '404';
        } catch(Exception $exception){
            $data = [];
            $message = $exception->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try{
            $market = Market::findorfail($id);
            $market->delete();
            $data = [];
            $message = 'Product deleted successfully.';
            $statusCode = '200';
        }
        catch(ModelNotFoundException $exception){
            $data = [];
            $message = 'Product does not exists!';
            $statusCode = '404';
        }
        catch(Exception $exception){
            $data = [];
            $message = $exception->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Add Category
     */
    public function addCategory(Request $request){
        $request->validate([
            'name' => 'required|string|',
            'cat_image' => 'required|mimes:jpeg,png,jpg|max:512'
        ]);
        $DirName = 'market/categories';
        $file = $request->file('cat_image');
        $fileDetails = $request->cat_image;

        $data['name'] = $request->name;
        $data['cat_image'] = imageStorage($DirName, $file, $fileDetails); 
        Category::create($data);
        $message = 'Category created successfully.';
        return apiResponse($data, $message, 201);
    }

    /**
     * Add SubCategory
     * @param  int  $id
     */
    public function addSubCategory(Request $request){
        $request->validate([
            'cat_id' => 'required|integer|exists:categories,id',
            'name' => 'required|string|',
            'cat_image' => 'required|mimes:jpeg,png,jpg|max:512'
        ]);
        $DirName = 'market/subcategories';
        $file = $request->file('cat_image');
        $fileDetails = $request->cat_image; 

        $data = $request->all();
        $data['cat_image'] = imageStorage($DirName, $file, $fileDetails); 
        Subcategory::create($data);
        $message = 'Sub category created successfully.';
        return apiResponse($data, $message, 201);
    }

    /**
     * list of Categories
     */
    public function categoriesList(){
        $data = Category::select('id', 'name', 'cat_image')->get();
        $message = 'Category Listing successfuly.';
        return apiResponse($data, $message , 200);
    }

    /**
     * list of Sub Categories
     * @param  int  $id
     */
    public function subCategoriesList(Request $request, $parent_category_id){
        $request->merge(['cat_id' => $parent_category_id]);
        $request->validate([
            'cat_id' => 'required|integer|exists:categories,id'
        ]);    
        $data = Subcategory::where('cat_id', $parent_category_id)->select('id', 'name', 'cat_image')->get();
        if($data->isEmpty()){
            $message = 'No sub category found for this category.';
        }
        else{
            $message = 'Sub category Listing successfuly.';
        }
        
        return apiResponse($data, $message , 200);
    }

    /**
     * Edit Category
     * @param  int  $id
     */
    public function editCategory(Request $request, $id){
        try{
            $category = Category::findorfail($id);
            $request->merge(['id' => $id]);
            $request->validate([
                'id' => 'required|integer|exists:categories,id',
                'name' => 'string',
                'cat_image' => 'mimes:jpeg,png,jpg|max:512'
            ]);
            if($request->hasfile('cat_image')){
                Storage::delete($category->cat_image);
            }

            $DirName = 'market/categories';
            $file = $request->file('cat_image');
            $fileDetails = $request->cat_image;

            $data = $request->all();
            if($request->hasfile('cat_image')){
                $data['cat_image'] = imageStorage($DirName, $file, $fileDetails);
            }
            else{
                $data['cat_image'] = $category->cat_image;
            }
            
            Category::where('id', $id)->update(['name' => $data['name'], 'cat_image' => $data['cat_image']]);
            $message = 'category updated successfully.';
            $statusCode = '200';

        } catch(ModelNotFoundException $expection){
            $data = [];
            $message = 'Category does not exists!';
            $statusCode = '404';
        } catch(Exception $exception){
            $data = [];
            $message = $exception->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Edit Sub Category
     * @param  int  $id
     */
    public function editSubCategory(Request $request, $id){
        try{
            $subcategory = Subcategory::findorfail($id);
            
            $request->merge(['id' => $id]);
            $request->validate([
                'id' => 'required|integer|exists:subcategories,id',
                'cat_id' => 'integer|exists:categories,id',
                'name' => 'string',
                'cat_image' => 'mimes:jpeg,png,jpg|max:512'
            ]);
            if($request->hasfile('cat_image')){
                Storage::delete($subcategory->cat_image);
            }

            $DirName = 'market/subcategories';
            $file = $request->file('cat_image');
            $fileDetails = $request->cat_image;

            $data = $request->all();
            if($request->hasfile('cat_image')){
                $data['cat_image'] = imageStorage($DirName, $file, $fileDetails);
            }
            else{
                $data['cat_image'] = $subcategory->cat_image;
            }
            
            Subcategory::where('id', $id)->update(['cat_id' => $data['cat_id'], 'name' => $data['name'], 'cat_image' => $data['cat_image']]);
            $message = 'Sub category updated successfully.';
            $statusCode = '200';

        } catch(ModelNotFoundException $expection){
            $data = [];
            $message = 'Sub Category does not exists!';
            $statusCode = '404';
        } catch(Exception $exception){
            $data = [];
            $message = $exception->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);        
    }

    /**
     * Delete Category
     */
    public function deleteCategory(Request $request, $id){
        try{
            $request->merge(['id' => $id]);
            $request->validate([
                'id' => 'required|integer|exists:categories,id',
            ]);
            $category = Category::findorfail($id);
            $category->delete();

            $data = [];
            $message = 'Category removed sucessfully.';
            $statusCode = '200';

        } catch(ModelNotFoundException $expection){
            $data = [];
            $message = 'Category does not exist.';
            $statusCode = '404';
        } catch(Exception $expection){
            $data = [];
            $message = $expection->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Delete Sub Category
     * @param  int  $id
     */
    public function deleteSubCategory(Request $request, $id){
        try{
            $request->merge(['id' => $id]);
            $request->validate([
                'id' => 'required|integer|exists:subcategories,id',
            ]);
            $category = Subcategory::findorfail($id);
            $category->delete();

            $data = [];
            $message = 'Sub-Category removed sucessfully.';
            $statusCode = '200';

        } catch(ModelNotFoundException $expection){
            $data = [];
            $message = 'Sub Category does not exist.';
            $statusCode = '404';
        } catch(Exception $expection){
            $data = [];
            $message = $expection->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }

    /**
     * Add category/subcategory Filters
     */
    public function addCategoriesFilter(Request $request){
        $request->validate([
            'cat_id' => 'integer|exists:categories,id|nullable',
            'subcat_id' => 'integer|exists:subcategories,id|nullable', 
            'name' => 'required|string'
        ]);

        $data = $request->all();
        Filter::create($data);
        $message = 'Filter has created successfully.';
        return apiResponse($data, $message, 201);
    }

    /**
     * listing filters for categories
     * @param int $id
     */
    public function categoriesFilter(Request $request, $id){
        $request->merge(['cat_id' => $id]);
        $request->validate([
            'cat_id' => 'required|integer|exists:categories,id',
        ]);
        $data = Filter::where('cat_id', $id)->select('id', 'name')->get();
        if($data->isEmpty()){
            $message = 'No filter found for this category.';
        }
        else{
            $message = 'Category Filter Listing successfuly.';
        }
        
        return apiResponse($data, $message , 200);
    }

    /**
     * listing filters for sub-categories
     * @param int $id
     */
    public function subCategoriesFilter(Request $request, $id){
        $request->merge(['subcat_id' => $id]);
        $request->validate([
            'subcat_id' => 'required|integer|exists:subcategories,id',
        ]);
        $data = Filter::where('subcat_id', $id)->select('id', 'name')->get();
        if($data->isEmpty()){
            $message = 'No filter found for this sub category.';
        }
        else{
            $message = 'Sub Category Filter Listing successfuly.';
        }
        
        return apiResponse($data, $message , 200);
    }

    /**
     * Delete filter for  categories/subcategories
     */
    public function delCategoriesFilter($id){
        try{
            $filter = Filter::findorfail($id);
            $filter->delete();

            $data = [];
            $message = 'Category Filter removed sucessfully.';
            $statusCode = '200';
        } catch(ModelNotFoundException $expection){
            $data = [];
            $message = 'Category does not exist.';
            $statusCode = '404';
        } catch(Exception $expection){
            $data = [];
            $message = $expection->getMessage();
            $statusCode = '500';
        }
        return apiResponse($data, $message, $statusCode);
    }
    
    /**
     * Market Search results
     */
    public function searchMarket(Request $request){
        $request->validate([
            'keyword' => 'string|nullable',
            'cat_id' => 'integer|exists:categories,id|nullable',
            'sub_cat_id.*' => 'integer|exists:subcategories,id|nullable',
            'filter_id.*' => 'integer|exists:filters,id|nullable|nullable',
            'price_range.*' => 'integer|nullable',
            'neighbourhood.*' => 'interger|nullable'
        ]);

        $keyword = $request->keyword;
        $cat_id = $request->cat_id;
        $sub_cat_id = $request->sub_cat_id;
        $filter_id = $request->filter_id;
        $price = explode(',',$request->price_range);

        $market = Market::orderBy('id', 'desc')->with('categoryMarket', 'marketSubcategory', 'filterMarket');        
        
        if($keyword){
            $market = $market->where('name', 'LIKE', "%{$keyword}%")
                                ->orWhere('name', 'LIKE', "%{$keyword}%");
        }
        
        if($cat_id){
            $market = $market->whereHas('categoryMarket', function ($cat) use ($cat_id) {
                $cat->where('category_id', $cat_id);
            });
        }
        
        if($sub_cat_id){
            $market = $market->whereHas('marketSubcategory', function ($sub_cat) use ($sub_cat_id) {
                $sub_cat->whereIn('subcategory_id', [$sub_cat_id]);
            });
        }
        
        if($request->has('filter_id')){
            $market = $market->whereHas('filterMarket', function ($filter) use ($filter_id) {
                $filter->whereIn('filter_id', [$filter_id]);
            });
        }
        
        if(sizeof($price)>1){
            $min = $price[0];
            $max = $price[1];
            $market = $market->whereBetween('price', [$min, $max]);
        }
        
        $searchMarket = $market->paginate($this->limit);
        $meta = Transformer::transformCollection($searchMarket);
        $data = Transformer::markets($searchMarket);
        $message = 'Market Product Listing Successfully.';
        return apiResponse($data, $message , 200, $meta);
    }
}
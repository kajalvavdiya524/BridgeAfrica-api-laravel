<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\Transformer;
use App\Business;
use App\Market;
use App\Network;
use App\Post;
use App\User;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class SearchController extends Controller
{
    private $limit;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct()
    {
        $this->limit = 3;
    }

    public function businessSearch(Request $request){
        $request->validate([
            'keyword' => 'string|nullable',
            'catId' => 'integer|exists:categories,id|nullable',
            'subCatId.*' => 'integer|exists:subcategories,id|nullable',
            'filterId.*' => 'interger|exists:filters,id|nullable',
            'neighbourhood.*' => 'interger|nullable'
        ]);
        $keyword = $request->keyword;
        $catId = $request->catId;
        $subCatId = $request->subCatId;
        $filterId = $request->filterId;
        $neighbourhood = $request->neighbourhood;

        $business = Business::orderBy('id', 'desc')->with('category', 'subcategory', 'filters')->where('is_sponsored', 0);

        if($keyword){
            $business = $business->where('name', 'LIKE', "%{$keyword}%");
        }

        if($catId){
            $business = $business->whereHas('category', function ($cat) use ($catId){
                 $cat->where('category_id', $catId);
            });
        }

        if($subCatId){
            $business = $business->whereHas('subcategory', function ($subCat) use ($subCatId){
                $subCat->where('subcategory_id', $subCatId);
            });
        }

        if($filterId){
            $business = $business->whereHas('filters', function ($filter) use ($filterId){
                $filter->where('filter_id', $filterId);
            });
        }
        
        $searchBusiness = $business->paginate($this->limit);
        $meta = Transformer::transformCollection($searchBusiness);
        $data = Transformer::businesses($searchBusiness);

        $message = 'All listing Successfully.';
        return apiResponse($data, $message , 200, $meta);
    }

    public function userSearch(Request $request){
        $request->validate([
            'keyword' => 'string|nullable',
            'catId' => 'integer|exists:categories,id|nullable',
            'subCatId.*' => 'integer|exists:subcategories,id|nullable',
            'filterId.*' => 'interger|exists:filters,id|nullable',
            'neighbourhood.*' => 'interger|nullable'
        ]);
        $keyword = $request->keyword;
        $catId = $request->catId;
        $subCatId = $request->subCatId;
        $filterId = $request->filterId;
        $neighbourhood = $request->neighbourhood;

        $user = User::orderBy('id', 'desc')->where('status', 1);
        $business = Business::orderBy('id', 'desc')->with('category', 'subcategory', 'filters', 'user')->where('is_sponsored', 0);

        if($keyword){
            $business = $business->where('name', 'LIKE', "%{$keyword}%");

        }
        if($catId){
            $business = $business->whereHas('category', function ($cat) use ($catId){
                 $cat->where('category_id', $catId);
            });
        }

        if($subCatId){
            $business = $business->whereHas('subcategory', function ($subCat) use ($subCatId){
                $subCat->where('subcategory_id', $subCatId);
            });
        }

        if($filterId){
            $business = $business->whereHas('filters', function ($filter) use ($filterId){
                $filter->where('filter_id', $filterId);
            });
        }
        
        $searchBusiness = $business->paginate($this->limit);
        $meta = Transformer::transformCollection($searchBusiness);
        $data = $searchBusiness;
        $message = 'All listing Successfully.';
        return apiResponse($data, $message , 200, $meta);

    }

    public function networkSearch(Request $request){
        $request->validate([
            'keyword' => 'string|nullable',
            'catId' => 'integer|exists:categories,id|nullable',
            'neighbourhood.*' => 'interger|nullable'
        ]);
        $keyword = $request->keyword;
        $catId = $request->catId;
        $neighbourhood = $request->neighbourhood;

        $network = Network::orderBy('id', 'desc')->with('networkCategory')->where('is_approve', 1);

        if($keyword){
            $network = $network->where('name', 'LIKE', "%{$keyword}%");
        } 

        if($catId){
            $network = $network->whereHas('networkCategory', function($cat) use ($catId){
                $cat->where('category_id', $catId);
            });
        }

        $searchNetwork = $network->paginate($this->limit);
        $meta = Transformer::transformCollection($searchNetwork);
        $data = Transformer::networks($searchNetwork);

        $message = 'All listing Successfully.';
        return apiResponse($data, $message , 200, $meta);
    }

    public function marketSearch(Request $request){
        $request->validate([
            'keyword' => 'string|nullable',
            'catId' => 'integer|exists:categories,id|nullable',
            'subCatId.*' => 'integer|exists:subcategories,id|nullable',
            'filterId.*' => 'interger|exists:filters,id|nullable',
            'neighbourhood.*' => 'interger|nullable'
        ]);
        $keyword = $request->keyword;
        $catId = $request->catId;
        $subCatId = $request->subCatId;
        $filterId = $request->filterId;
        $neighbourhood = $request->neighbourhood;

        $market = Market::orderBy('id', 'desc')->with('categoryMarket', 'marketSubcategory', 'filterMarket');

        if($keyword){
            $market = $market->where('name', 'LIKE', "%{$keyword}%");
        }

        if($catId){
            $market = $market->whereHas('categoryMarket', function ($cat) use ($catId) {
                $cat->where('category_id', $catId);
            });
        }

        if($subCatId){
            $market = $market->whereHas('marketSubcategory', function ($subCat) use ($subCatId) {
                $subCat->whereIn('subcategory_id', [$subCatId]);
            }); 
        }

        if($filterId){
            $market = $market->whereHas('filterMarket', function ($filter) use ($filterId) {
                $filter->whereIn('filter_id', [$filterId]);
            });
        }
        
        $searchMarket = $market->paginate($this->limit);
        $meta = Transformer::transformCollection($searchMarket);
        $data = Transformer::markets($searchMarket);  

        $message = 'All listing Successfully.';
        return apiResponse($data, $message , 200, $meta);
    }

    public function postSearch(Request $request){
        $request->validate([
            'keyword' => 'string|nullable',
            'catId' => 'integer|exists:categories,id|nullable',
            'subCatId.*' => 'integer|exists:subcategories,id|nullable',
            'neighbourhood.*' => 'interger|nullable'
        ]);
        $keyword = $request->keyword;
        $catId = $request->catId;
        $subCatId = $request->subCatId;
        $neighbourhood = $request->neighbourhood;

        $post = Post::orderBy('id', 'desc')->where('is_approve', 1);

        if($keyword){
            $post = $post->where('content', 'LIKE', "%{$keyword}%");
        }
        
        $searchPost = $post->paginate($this->limit);
        $meta = Transformer::transformCollection($searchPost);
        $data = Transformer::markets($searchPost);

        $message = 'All listing Successfully.';
        return apiResponse($data, $message , 200, $meta);
    }
}

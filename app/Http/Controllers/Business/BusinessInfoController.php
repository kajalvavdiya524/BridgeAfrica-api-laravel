<?php

namespace App\Http\Controllers\Business;

use Exception;
use App\Business;
use App\BusinessAlbum;
use App\BusinessFollower;
use App\BusinessMedia;
use App\BusinessNotification;
use App\BusinessOpenHours;
use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\AddBusinessRequest;
use App\Keyword;
use App\Models\BusinessUserRole;
use Facade\FlareClient\Api;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BusinessInfoController extends Controller
{
  /**
   * Displays Business info.
   * 
   * @param  businessID  
   * @return \Illuminate\Http\Response
   */
  public function displayInfo($businessId)
  {
    $data = [];
    $message = "Business Info";
    $statusCode = 200;
    $business = Business::find($businessId);
    $coverAlbum = $business->businessAlbum()->where('name', 'cover_photo')->first();
    $cover = [];
    if (!empty($coverAlbum)) {
      $cover = $coverAlbum->businessMedia->map(function ($media, $key) {
        $data = [
          'id' => $media->id,
          'media_url' => getMediaUrl($media->media_url),
          'media_type' => $media->media_type
        ];
        return $data;
      });
    };
    $data = [
      'category' => $business->category,
      'keywords' => $business->keywords,
      'location_description' => $business->location_description,
      'website' => $business->website,
      'community' => $business->businessfollower->count(),
      'phone' => $business->phone,
      'email' => $business->email,
      'region' => $business->region,
      'city' => $business->city,
      'country' => $business->country,
      'address' => $business->address,
      'business_open_hours' => $business->businessOpenHours->map(function ($item, $key) {
        $data = [
          'day' => $item->day,
          'opening_time' => $item->opening_time,
          'closing_time' => $item->closing_time,
        ];
        return $data;
      }),
      'email' => $business->email,
      'about_business' => $business->about_business,
      'lat' => $business->lat,
      'lng' => $business->lng,
      'name' => $business->name,
      'logo_path' => $business->logo_path,
      'cover' => $cover
    ];
    return apiResponse($data, $message, $statusCode);
  }

  public function updateAbout(Request $request, Business $business, BusinessOpenHours $businessHours)
  {
    $request->validate([
      'name' => ['required', 'string'],
      'category' => ['required', 'string'],
      'keywords' => ['required', 'string'],
      'city' => ['required', 'string'],
      'country' => ['required', 'string'],
      'email' => ['required', 'email'],
      'phone' => ['required', 'integer']
    ]);

    $data = [];
    $message = " ";
    $statusCode = 200;
    $business->update([
      'name' => $request->name,
      'category' => $request->category,
      'keywords' => $request->keywords,
      'city' => $request->city,
      'country' => $request->country,
      'neighbourhood' => $request->neighbourhood,
      'phone' => $request->phone,
      'email' => $request->email,
    ]);

    $hours = $this->updateHours($businessHours, $business, $request->openHours);

    //dd($hours);

    $message = "Successfull updated";
    $data = [
      'business' => $business
    ];
    return apiResponse($data, $message, $statusCode);
  }

  public function updateHours(BusinessOpenHours $businessHours, Business $business, $openHours)
  {
    foreach ($openHours as $value) {
      $businessHours->create([
        'business_id' => $business['id'],
        'day' => $value[0],
        'opening_time' => $value[1],
        'closing_time' => $value[2]
      ]);
    }

    return 'hours have been saved';
  }

  public function updateInfo(Request $request, $businessId)
  {
    $data = [];
    $message = " ";
    $statusCode = 200;
    $business = Business::find($businessId);
    $business->update([
      'name' => $request->name,
      'category' => $request->category,
      'timezone' => $request->timezone,
      'keywords' => $request->keywords,
      'language' => $request->language,
      'about_business' => $request->about_business,
      'region' => $request->region,
      'city' => $request->city,
      'country' => $request->country,
      'address' => $request->address,
      'phone' => $request->phone,
      'lat' =>  $request->lat,
      'lng' =>  $request->lng,
      'email' => $request->email,
    ]);
    $message = "Successfull updated";
    $data = [
      'business' => $business
    ];
    return apiResponse($data, $message, $statusCode);
  }

  /**
   * upload cover photo.
   *
   * @param  businessID  $id which is the album id
   * @return \Illuminate\Http\Response
   */
  public function uploadCover(Request $request, $businessId)
  {
    $data = [];
    $message = " ";
    $statusCode = 200;
    $request->validate([
      'image' => 'required|file|mimes:mp4,jpg,jpeg,png|max:24000'
    ]);
    $coverAlbum = BusinessAlbum::where(['name' => 'cover_photo', 'business_id' => $businessId])->first();
    if (empty($coverAlbum)) {
      $coverAlbum = BusinessAlbum::create([
        'name' => 'cover_photo',
        'business_id' => $businessId
      ]);
    }
    $path = "public/business/" . $businessId . "/album";
    $coverAlbum->businessMedia()->create([
      'media_type' => $request->image->getmimetype(),
      'media_url' => imageUpload($path, $request->image),
      'business_id' => $businessId
    ]);
    $message = "Uploaded Successfully";
    return apiResponse($data, $message, $statusCode);
  }

  /**
   * upload logo picture.
   *
   * @param  businessID  $id which is the album id
   * @return \Illuminate\Http\Response
   */
  public function uploadLogo(Request $request, $businessId)
  {
    $data = [];
    $message = " ";
    $statusCode = 200;
    $request->validate([
      'image' => 'required|file|mimes:mp4,jpg,jpeg,png|max:24000'
    ]);
    $logoAlbum = BusinessAlbum::where(['name' => 'logo', 'business_id' => $businessId])->first();
    if (empty($logoAlbum)) {
      $logoAlbum = BusinessAlbum::create([
        'name' => 'logo',
        'business_id' => $businessId
      ]);
    }
    $path = "public/business/" . $businessId . "/album";
    $storagePath = imageUpload($path, $request->image);
    $logoAlbum->businessMedia()->create([
      'media_type' => $request->image->getmimetype(),
      'media_url' => $storagePath,
      'business_id' => $businessId
    ]);
    $logoAlbum->business->update([
      'logo_path' => $storagePath
    ]);
    $message = "Uploaded Successfully";
    return apiResponse($data, $message, $statusCode);
  }
  public function businessInfoSetting(Request $request, Business $business, BusinessOpenHours $businessHours, $id)
  {
    $request->validate([
      'name' => ['required', 'string'],
      'category' => ['required', 'string'],
      'keywords' => ['required', 'string'],
      'city' => ['required', 'string'],
      'country' => ['required', 'string'],
      'email' => ['required', 'email'],
      'phone' => ['required', 'integer'],
      'website' => ['nullable', 'string'],
      'lat' => ['nullable', 'integer'],
      'lng' => ['nullable', 'integer'],
      'phone2' => ['nullable', 'integer'],
      'location' => ['nullable', 'string'],
      'marketplace' => ['required', 'bool'],
      'post_approval' => ['required', 'numeric', 'max:1', 'in:0,1']
    ]);

    $data = [];
    $message = " ";
    $statusCode = 200;
    $business::where('id', $id)->update([
      'name' => $request->name,
      'category' => $request->category,
      'keywords' => $request->keywords,
      'city' => $request->city,
      'country' => $request->country,
      'neighbourhood' => $request->neighbourhood,
      'phone' => $request->phone,
      'secondary_phone' => $request->phone2,
      'email' => $request->email,
      'website' => $request->website,
      'location_description' => $request->location,
      'lat' => $request->lat,
      'lng' => $request->lng,
      'marketplace' => $request->marketplace
    ]);

    BusinessSetting::where('business_id', $id)->update(['post_approval' => $request->post_approval]);

    $hours = $this->updateHours($businessHours, $business, $request->openHours);

    $message = "Successfull updated";
    $data = [
      'business' => $business
    ];
    return apiResponse($data, $message, $statusCode);
  }

  public function updateGeneralInfo(Request $request, $id)
  {
    $request->validate([
      'visibility' => ['required', 'string', 'max:10', 'in:publish,unpublish'],
      'permissions' => ['required', 'string', 'max:255', 'in:Admin only,Allow visitors/followers to post'],
      'post_approval' => ['required', 'numeric', 'max:1', 'in:0,1'],
      'keywords_alert' => ['required'],
      'marketplace' => ['required', 'numeric', 'max:1', 'in:0,1']
    ]);

    try {
      $data = [];
      $message = 'Business general info has been saved';
      $statusCode = 200;
      Business::findOrfail($id);
      DB::table('business_settings')
        ->updateOrInsert(
          ['business_id' => $id],
          [
            'visibility' => $request->visibility,
            'permissions' => $request->permissions,
            'post_approval' => $request->post_approval,
            'keywords_alert' => $request->keywords_alert
          ]
        );
      Business::where('id', $id)->update(['marketplace' => $request->marketplace]);
    } catch (ModelNotFoundException $exception) {
      $data = [];
      $message = 'business not exists!';
      $statusCode = '404';
    } catch (Exception $exception) {
      $data = [];
      $message = $exception->getMessage();
      $statusCode = '500';
    }
    return apiResponse($data, $message, $statusCode);
  }

  public function deleteBusiness($id)
  {
    $data = [];
    $message = "Business deleted successfuly";
    $statusCode = 200;
    Business::where('id', $id)->delete();
    BusinessMedia::where('reference_id', $id)->delete();
    BusinessSetting::where('business_id', $id)->delete();
    BusinessNotification::where('business_id', $id)->delete();
    BusinessFollower::where('business_id', $id)->delete();
    BusinessUserRole::where('business_id', $id)->delete();
    BusinessAlbum::where('business_id', $id)->delete();
    BusinessOpenHours::where('business_id', $id)->delete();

    return apiResponse($data, $message, $statusCode);
  }

  /**
   * returns current properties of business
   * for editing
   *
   * @param  id  $id which is the album id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
    $data = [];
    $message = "Successful";
    $statusCode = 200;
    $business = Business::findorfail($id);
    $data = [
      'name' => $business->name,
      'logo_path' => $business->logo_path,
      'lat' => $business->lat,
      'lng' => $business->lng,
      'phone1' => $business->phone,
      'phone2' => $business->secondary_phone,
      'website' => $business->website,
      'email' => $business->email,
      'about_business' => $business->about_business,
      'keywords' => $business->keywords()->select('id', 'name')->get(),
      'language' => $business->language,
      'address' => $business->address,
      'city' => $business->city,
      'category' => $business->category()->select('category_id', 'name')->get(),
      'subCategory' => $business->subcategory()->select('subcategory_id', 'name')->get(),
      'filter' => $business->filters()->select('filter_id', 'name')->get(),
      'country' => $business->country()->select('country_id', 'name')->get(),
      'region' => $business->region()->select('region_id', 'name')->get(),
      'council' => $business->council()->select('council_id', 'name')->get(),
      'division' => $business->division()->select('division_id', 'name')->get(),
      'neigborhood' => $business->neighborhood()->select('neighborhood_id', 'name')->get(),
    ];
    return apiResponse($data, $message, $statusCode);
  }

  /**
   * update user business
   * 
   * @param request
   * @param  id  $id which is the album id
   * @return \Illuminate\Http\Response
   */
  public function update(AddBusinessRequest $request, $businessId)
  {
    $message = 'Your business have been updated successfully';
    $statusCode = 200;
    $data = [];
    $business = Business::find($businessId);
    $business->update([
      'name' => $request->name,
      'timezone' => $request->timezone,
      'about_business' => $request->about_business,
      'phone' => $request->phone1,
      'secondary_phone' => $request->phone2,
      'website' => $request->website,
      'email' => $request->email,
      'city' => $request->city,
      'address' => $request->address
    ]);
    // dd($request->categoryId);
    $business->category()->sync($request->categoryId);
    $business->subcategory()->sync($request->subCategoryId);
    $business->filters()->sync($request->filterId);
    $business->country()->sync($request->country);
    $business->region()->sync($request->region);
    $business->division()->sync($request->division);
    $business->council()->sync($request->council);
    $business->neighborhood()->sync($request->neigborhood);
    if ($request->hasfile('logo_path')) {
      $logoAlbum = BusinessAlbum::where(['name' => 'logo', 'business_id' => $businessId])->first();
      if (empty($logoAlbum)) {
        $logoAlbum = BusinessAlbum::create([
          'name' => 'logo',
          'business_id' => $businessId
        ]);
      }
      $path = "public/business/" . $businessId . "/album";
      $storagePath = imageUpload($path, $request->logo_path);
      $logoAlbum->businessMedia()->create([
        'media_type' => $request->logo_path->getmimetype(),
        'media_url' => $storagePath,
        'business_id' => $businessId
      ]);
      $logoAlbum->business->update([
        'logo_path' => $storagePath
      ]);
    }  
 
    if ($request->keywords) {
      $keys = $business->keywords()->select('id')->get();
      Keyword::whereIn('id',$keys)->delete();
      foreach ($request->keywords as $key => $value) {
        $business->keywords()->create([
          'name' => $value
        ]);
      }
    }
    return apiResponse($data,$message,$statusCode);
  }
}

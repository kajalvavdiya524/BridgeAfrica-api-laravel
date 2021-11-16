<?php

namespace App;

use App\Post;
use App\Share;
use App\Message;
use App\BusinessAlbum;
use App\Market;
use App\BusinessFollower;
use App\BusinessOpenHours;
use App\BusinessNotification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;


class Business extends Authenticatable
{
    use HasFactory,SoftDeletes;
    use HasRoles;

    protected $guard_name = 'web';

    protected $table = 'businesses';

    protected $guarded = ['id'];

    protected $hidden = ['pivot'];

    protected $fillable = [
        'name',
        'category',
        'about_business',
        'location_description',
        'user_id',
        'region',
        'city',
        'country',
        'address',
        'phone',
        'secondary_phone',
        'neighbourhood',
        'website',
        'email',
        'lat',
        'lng',
        'logo_path',
        'timezone',
        'logo_path',
        'language',
        'keywords'
    ];
    
    public function businesscommunity()
    {
        return $this->hasMany(BusinessCommunity::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function networks(){
        return $this->belongsTo(Network::class);
    }

    public function message()
    {
        return $this->hasMany(Message::class)->where('is_read', '=', 1);
    }

    public function notification()
    {
        return $this->hasMany(BusinessNotification::class)->where('is_read', '=', 1);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function shares()
    {
        return $this->hasMany(Share::class)->get('post_id');
    }

    public function getLogoPathAttribute($value)
    {
        return env('APP_URL') . ':' . $_SERVER['SERVER_PORT'] . $value;
    }

    public function businessOpenHours()
    {
        return $this->hasMany(BusinessOpenHours::class);
    }

    public function markets()
    {
        return $this->hasMany(Market::class);
    }

    public function communitys()
    {
        return $this->hasMany(Market::class);
    }

    public function businessAlbum()
    {
        return $this->hasMany(BusinessAlbum::class);
    }

    public function post()
    {
        return $this->hasMany(Post::class);
    }

    public function businessCategory(){
        return $this->belongsToMany(Category::class);
    }

    public function category()
    {
        return $this->belongsToMany(Category::class,'business_category', 'business_id', 'category_id');
    }

    public function subcategory()
    {
        return $this->belongsToMany(Subcategory::class,'business_subcategory', 'business_id', 'subcategory_id');
    }

    public function filters()
    {
        return $this->belongsToMany(Filter::class,'business_filter', 'business_id', 'filter_id');
    }

    public function keywords()
    {
        return $this->hasMany(Keyword::class);
    }

    public function country()
    {
        return $this->belongsToMany(Country::class,'business_country','business_id','country_id');
    }

    public function region()
    {
        return $this->belongsToMany(Region::class,'business_region','business_id','region_id');
    }

    public function division()
    {
        return $this->belongsToMany(Division::class,'business_division','business_id','division_id');
    }

    public function council()
    {
        return $this->belongsToMany(Council::class,'business_council','business_id','council_id');
    }

    public function neighborhood()
    {
        return $this->belongsToMany(Region::class,'business_neighborhood','business_id','neighborhood_id');
    }

    public function businessSettings()
    {
        return $this->hasOne(BusinessSetting::class);
    }
}

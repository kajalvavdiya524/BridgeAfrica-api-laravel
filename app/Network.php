<?php

namespace App;

use App\Post;
use App\NetworkAlbum;
use App\NetworkFollower;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\User;

class Network extends Model
{
    use Hasfactory;
    
    protected $fillable = [
        'user_id',
        'business_id',
        'name',
        'description',
        'purpose',
        'special_needs',
        'address',
        'country_id',
        'region_id',
        'division_id',
        'council_id',
        'neighborhood_id',
        'image',
        'allow_business',
        'email',
        'primary_phone',
        'secondary_phone',
        'network_categories',
        'is_approve'
    ];

    public function networkUsers()
    {
        return $this->belongsToMany(User::class);
    }

    public function businessNetworks(){
        return $this->belongsToMany(business::class);
    }

    public function networkCategory(){
        return $this->belongsToMany(Category::class);
    }
 
    public function country(){
        return $this->belongsTo(Country::class);
    }

    public function region(){
        return $this->belongsTo(Region::class);
    }

    public function division(){
        return $this->belongsTo(Division::class);
    }

    public function council(){
        return $this->belongsTo(Council::class, 'council_id');
    } 
    
    public function NeighborhoodNetworks(){
        return $this->belongsToMany(Neighborhood::class);
    }

    public function business(){
        return $this->belongsTo(Business::class);
    }

    public function networkFollower()
    {
        return $this->hasMany(NetworkFollower::class);
    }

    public function networkRole(){
        return $this->hasMany(NetworkRole::class);
    }

    public function post()
    {
        return $this->hasMany(Post::class);
    }

    public function networkAlbum()
    {
        return $this->hasMany(NetworkAlbum::class);
    }

    public function users(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getImageAttribute($value)
    {
        return env('APP_URL') . ':' . $_SERVER['SERVER_PORT'] . '/storage' . substr($value, 6);
    }

}

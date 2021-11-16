<?php

namespace App;

use App\Post;
use App\UserAddress;
use App\UserContact;
use App\UserWebsite;
use App\UserEducation;
use App\PrivacySetting;
use App\UserSocialProfile;
use App\UserWorkExperience;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{

    use HasApiTokens, Notifiable, HasFactory;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'verified_at',
        'provider',
        'provider_id',
        'status',
        'country_id',
        'city',
        'neighborhood_id',
        'profile_picture',
        'gender',
        'dob',
        'lat',
        'lng',
        'region_id',
        'address',
        'biography',
        'profile_complete',
        'division_id',
        'council_id',
        'cover_picture'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] =  Hash::make($value);
    }

    public function getProfilePictureAttribute($value)
    {
        if (!$value) return null;
        return env('APP_URL') . ':' . $_SERVER['SERVER_PORT'] . $value;
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function business()
    {
        return $this->hasMany(Business::class);
    }

    public function userfollower()
    {
        return $this->hasMany(UserCommunity::class);
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function userWorkExperiences()
    {
        return $this->hasMany(UserWorkExperience::class);
    }

    public function userWebsites()
    {
        return $this->hasMany(UserWebsite::class);
    }

    public function userContacts()
    {
        return $this->hasMany(UserContact::class);
    }

    public function userAddresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    public function education()
    {
        return $this->hasMany(UserEducation::class);
    }

    public function userNetwork()
    {
        return $this->belongsToMany(Network::class);
    }

    public function networks()
    {
        return $this->hasMany(Network::class);
    }

    public function messages(){
        return $this->hasMany(Message::class, 'sender_id', 'id');
    }

    public function userAlbum()
    {
        return $this->hasMany(UserAlbum::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function division()
    {
        return $this->hasOne(Division::class);
    }

    public function council()
    {
        return $this->hasOne(Council::class);
    }

    public function neigborhood()
    {
        return $this->hasOne(Neighborhood::class);
    }

    public function message()
    {
        return $this->hasMany(Message::class);
    }

    public function getCoverPictureAttribute($value)
    {
        if (!$value) return '/default_cover_photo/banner.jpg';
        return  $value;
    }
}

<?php

namespace App;

use App\Business;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BusinessSetting extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = "business_settings";
   
    protected $fillable = ['visibility','permissions','post_approval','keywords_alert','business_id'];

    public function business()
    {
        return $this->BelongsTo(Business::class);
    }
}

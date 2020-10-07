<?php

namespace App;

use App\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
class Video extends Model
{
    // protected $dates = [
    //     'converted_for_downloading_at',
    //     'converted_for_streaming_at',
    // ];

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
        // return $this->hasMany(Media_file::class);
    }

    public static function boot() {
        parent::boot();
       
        static::deleting(function($video) { 
         
            $video->each(function($i) {  
                    $vids = explode("/",$i->video_path);    
                    Storage::delete('public/uploads/'.Auth::user()->company_id.'/'.end($vids)); 
            }); 
          
            return true;
        });
    }
}
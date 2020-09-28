<?php

namespace App;

use App\Item;
use App\User;
use App\Hotspot;
use App\Video;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
class Product extends Model
{
    protected $guarded = [];

 

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function hotspots()
    {
        return $this->hasMany(Hotspot::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function boot() {
        parent::boot();
       
        static::deleting(function($product) {
            
            //remove related rows products, media_files, hotspots_settings
            $product->items->each(function($items) { 
                $items->media_file()->each(function($i){ 
                    Storage::delete('public/uploads/'.Auth::user()->company_id.'/'.$i->path);
                    Storage::delete('public/uploads/'.Auth::user()->company_id.'/original/'.$i->original_name.".png");
                    Storage::delete('public/uploads/'.Auth::user()->company_id.'/original/'.$i->original_name.".jpg");
                }); 
               
                $items->media_file()->delete();                
                $items->hotspot_setting()->delete();
            });
            $product->items()->delete(); 
            $product->hotspots()->delete(); 

            $product->videos->each(function($i) {  
                  //  $vids = explode("/",$i->video_path);
                    Storage::delete('public/uploads/'.Auth::user()->company_id.'/'.$i->video_path); 
            });
            $product->videos()->delete(); 
            return true;
        });
    }
}

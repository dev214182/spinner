<?php

namespace App;

//use App\Hotspot;
use App\Hotspot_setting;
use App\Product;
use App\User_file;
use App\Media_file;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
class Item extends Model
{
    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    // public function hotspots()
    // {
    //     return $this->belongsToMany(Hotspot::class);
    // }
    public function media_file()
    {
        return $this->belongsTo(Media_file::class);
        // return $this->hasMany(Media_file::class);
    }
    public function hotspot_setting()
    {
        return $this->hasMany(Hotspot_setting::class);
    }

    public static function boot() {
        parent::boot();
       
        static::deleting(function($items) {
            
            //remove related rows products, media_files, hotspots_settings
            
                $items->media_file()->each(function($i){  
                    Storage::delete('public/uploads/'.Auth::user()->company_id.'/'.$i->path);
                    Storage::delete('public/uploads/'.Auth::user()->company_id.'/original/'.$i->original_name.".png");
                    Storage::delete('public/uploads/'.Auth::user()->company_id.'/original/'.$i->original_name.".jpg");
               });
               
                $items->media_file()->delete();                
                $items->hotspot_setting()->delete();
          
            return true;
        });
    }
}

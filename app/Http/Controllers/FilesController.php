<?php

namespace App\Http\Controllers;

use App\Item;
use App\Watermark;
use App\Media_file;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
class FilesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Images
     * https://drive.google.com/drive/folders/1aaI4LEJKpWq1paURHcrIeXnJrGUb6JcG
     */

    public function upload(Request $request)
    {
        // dd($request->with_watermark);
        // Validate request
        // $this->validate($request, [
        //     'file' => 'required|image|mimes:jpeg,png,jpg|max:204800',
        // ]);

        // Media_files table
        $userFiles = new Media_file;
        $itemsArray = array();
        $fileArray = array();

        $uploadKey = Carbon::now()->format('YmdHis');
        // $uploadKey = "";
        // $uploadKey = Str::random();

        // $userId = auth()->id();
        $user = Auth::user();
        $companyId = $user->company_id;
        $userStorage = '/public/uploads/' . $companyId;
        if (!Storage::exists($userStorage)) {
            Storage::makeDirectory($userStorage, 0755, true);
            Storage::makeDirectory($userStorage."/original", 0755, true);
        }

        // Wrap the files to collection
        $files = Collection::wrap(request()->file('file'));

        // Get the Company's Watermark settings
        if($request->with_watermark == "true"){
            $watermark = Watermark::where('id', $request->selected_watermark)->first();
        }
        
        // Do something on each files uploaded
        $files->each(function ($file, $key) use (&$watermark, &$companyId, &$userStorage, &$itemsArray, &$fileArray, &$request, &$uploadKey) {
            $userStorageDir = storage_path() . '/app' . $userStorage;
            // $fileName = $uploadKey."-".$file->getClientOriginalName();
            $fileName = $file->getClientOriginalName();
            $title = pathinfo($fileName, PATHINFO_FILENAME);
            $extn = $file->getClientOriginalExtension();
            $slugTitle = Str::slug($title, '-');
            $path = $slugTitle."-".$uploadKey.".".$extn;
            $webP = $slugTitle."-".$uploadKey.".webp";

            $jpgExtensions = array('jpeg', 'jpg', 'JPEG', 'JPG');
            $pngExtensions = array('png', 'PNG');
            $format = 'jpg';
            if (in_array($extn, $pngExtensions)) {
                $format = 'png';
            }

            // Check file if image or video
            if($request->item_type == 'video'){
                $file->move('storage/uploads/'.$companyId.'/', $path); // add user id
                $webP = $path;
            }else{
                // File Optimization
                // $img = Image::make($file)->fit(3840,2160); // UHD
                $img = Image::make($file);

                $img->save($userStorageDir . '/original/' . $path); // Save to directory

                $img->encode('webp', 20);

                if($request->with_watermark == "true"){
                    if($watermark && $request->item_type != "panorama" && ($watermark->path != null || $watermark->path != "")){
                        $img->insert('storage/uploads/'.$companyId.'/watermark/'.$watermark->path, $watermark->position, $watermark->offset_space, $watermark->offset_space);
                    }
                }
                $img->save($userStorageDir . '/' . $webP); // Save to directory
            }

            /**
             * Set item array
             */
            if ($request->add_items == 'true') {
                array_push($itemsArray, array(
                    'item_type' => $request->item_type,
                    'product_id' => $request->product,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ));
            }

            /**
             * Set file array
             */
            $mediaFileType = "";
            if($request->item_type == 'video'){
                $mediaFileType = "video";
            }else{
                $mediaFileType = "image";
            }
            array_push($fileArray, array(
                'file_type' => $mediaFileType,
                'title' => $title,
                'original_name' => $slugTitle."-".$uploadKey,
                'disk' => 'uploads',
                'path' => $webP,
                'user_id' => auth()->id(),
                'company_id' => $companyId,
                'item_id' => null,
                'created_at' => Carbon::now(),
            ));
        });

        // Save the files to Media_files table
        Media_file::insert($fileArray);


        // If addItems == true in UploadZone component
        if ($request->add_items == 'true') {

            // Get the files by original_name
            $originaNamesArray = array_column($fileArray, 'original_name');
            $recentlySavedFiles = Media_file::whereIn('original_name', $originaNamesArray )->get();

            foreach ($recentlySavedFiles as $key => $file) {
                $itemsArray[$key]['media_file_id'] = $file->id;
            }
            // Save to Items table
            Item::insert($itemsArray);
        }

        // Return response
        return response()->json([
            'message' => 'Upload Success',
        ], 200);
    }

    public function apply_watermark(Request $request)
    {

        $user = Auth::user();
        $companyId = $user->company_id;
        $userStorage = public_path('storage/uploads/'). $companyId;

        $watermark = Watermark::where(['company_id' => $companyId, 'default' => 1])->first();

        $files = Collection::wrap($request->selected);
       
        // Do something on each files uploaded
        foreach($files AS $k => $file) {
            
            $ext = explode(".", $file); 
            $filename = $ext[0].'.jpg';
            $filename2 = $ext[0].'.JPG';
            $filename3 = $ext[0].'.png';
            $source1 = storage_path() . '/app/public/uploads/'.$companyId.'/original/'.$filename;
            $source2 = storage_path() . '/app/public/uploads/'.$companyId.'/original/'.$filename2;
            $source3 = storage_path() . '/app/public/uploads/'.$companyId.'/original/'.$filename3;
           
            if(file_exists($source1)){
                $source = $source1;
               
            }elseif(file_exists($source2)){
                $source = $source2;
                
            }else{
                $source = $source3;
                
            }
          
            $selectedImg = Image::make($source);
            // if($watermark && $watermark->status == true ){
            if($watermark){
                $selectedImg->insert('storage/uploads/'.$companyId.'/watermark/'.$watermark->path, $watermark->position, $watermark->offset_space, $watermark->offset_space);
            }
            
            $selectedImg->save($userStorage . '/' . $file); // Save to directory
        }
        return response()->json("Success", 200);
    }

    public function remove_watermark(Request $request)
    {
        $user = Auth::user();
        $companyId = $user->company_id;
        $userStorage = public_path('storage/uploads/'). $companyId;

        $watermark = Watermark::where(['company_id' => $companyId, 'default' => 1])->first();

        $files = Collection::wrap($request->selected);

        // Do something on each files uploaded
        foreach($files AS $k => $file) { 
            
            $ext = explode(".", $file); 
            $filename = $ext[0].'.jpg';
            $filename2 = $ext[0].'.JPG';
            $filename3 = $ext[0].'.png';
            $source1 = storage_path() . '/app/public/uploads/'.$companyId.'/original/'.$filename;
            $source2 = storage_path() . '/app/public/uploads/'.$companyId.'/original/'.$filename2;
            $source3 = storage_path() . '/app/public/uploads/'.$companyId.'/original/'.$filename3;
           
            if(file_exists($source1)){
                $source = $source1;
                
            }elseif(file_exists($source2)){
                $source = $source2;
                
            }else{
                $source = $source3;
                 
            }
            $selectedImg = Image::make($source);

            $selectedImg->save($userStorage . '/' . $file); // Save to directory
        }
        return response()->json("Success", 200);
    }

    // has static, used in UploadVideo component
    // public function getItemImages()
    // {
    //     $files = Media_file::where(['user_id' => 1, 'file_type' => 'image'])->take(50)->get();
    //     return response()->json($files, 200);
    // } 

    public function getUserFilesByID($id)
    {
        $companyId = Auth::user()->company_id;
        $files = Media_file::where(['company_id' => $companyId])->orderBy('created_at', 'DESC')->paginate(200);
        return response()->json($files, 200);
    }

    public function searchData($search)
    {
       
        $company_id = Auth::user()->company_id;
        $files = Media_file::where('company_id', $company_id)->where('title', 'like', '%'.$search.'%')->orWhere('path', 'like', '%'.$search.'%')->orderBy('title', 'asc')->paginate(200);
        
        return response()->json($files, 200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Watermark;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Auth as Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class WatermarksController extends Controller
{

    public function __construct()
    { 
        $this->middleware('auth'); 
    }

    public function fetchWatermarksForUploadZone()
    {
        $companyId = Auth::user()->company_id;
        $watermarks = Watermark::where('company_id', $companyId)->orderBy('title', 'asc')->get();
        return response()->json($watermarks, 200);
    }

    public function fetchWatermarks()
    {
        $companyId = Auth::user()->company_id;
        $watermarks = Watermark::where('company_id', $companyId)->orderBy('created_at', 'desc')->paginate(10);
        return response()->json($watermarks, 200);
    }

    public function update(Request $request, $id)
    {
        $companyId = Auth::user()->company_id;
        $uploadDate = Carbon::now()->format('YmdHis');

        // Set the previous default watermark to 0
        
        if($request->default == true){ 
            $updateOldDefault = Watermark::where([
                'default' => 1,
                'company_id' => $companyId])->update(['default' => 0]);
            
        }

        // Create Director if does not exist
        $userStorage = '/public/uploads/' . $companyId.'/watermark';
        if (!Storage::exists($userStorage)) {
            Storage::makeDirectory($userStorage, 0755, true);
        }
        $userWatermakrStorageDir = storage_path() . '/app' . $userStorage;
        $watermark = Image::make(public_path('storage/uploads/'.$companyId.'/'.$request->watermark));

        // Convert the image to png first. The opacity() is currently not working on jpg.
        // $watermark->encode('png');
        // $watermark->encode('png')->trim();

        // Get the converted png call make() again.

        // Resize image
        $watermark->resize($request->image_width, null, function ($constraint) {
            $constraint->aspectRatio();
        });

        // Set opacity then save to company's directory
        $watermark->opacity($request->image_opacity);
        $watermark->save($userWatermakrStorageDir . '/'.$request->watermark);

        // Check if company has Watermark
        $watermarkToUpdate = Watermark::where('id', $id)->first();

        // Set Watermark Array
        // $createWaterMarkArray = [
        //     'path' => $request['watermark'],
        //     'media_file_id' => $request['media_file_id'],
        //     'position' => $request['position'],
        //     'offset_space' => $request['offset_space'],
        //     'image_width' => $request['image_width'],
        //     'image_opacity' => $request['image_opacity'],
        //     'company_id' => $companyId,
        //     'status' => $request['status'],
        //     'updated_at' => Carbon::now(),
        // ];
        $updateWaterMarkArray = [
            'title' => $request['title'],
            'path' => $request['watermark'],
            'media_file_id' => $request['media_file_id'] != null ? $request['media_file_id'] : $watermarkToUpdate->media_file_id,
            'position' => $request['position'],
            'offset_space' => $request['offset_space'],
            'image_width' => $request['image_width'],
            'image_opacity' => $request['image_opacity'],
            // 'status' => $request['status'],
            'default' => $request['default'],
            'updated_at' => Carbon::now(),
        ];
        // dd($updateWaterMarkArray);
        // if($watermarkExist){
        $watermarkQuery = $watermarkToUpdate->update($updateWaterMarkArray);
        // }else{
        //     $watermarkQuery = Watermark::create($createWaterMarkArray);
        // }

        // Return response
        if($watermarkQuery){
            return response()->json([
                'message' => 'Water mark has been saved',
            ], 200);
        }else{
            return response()->json([
                'message' => 'Error saving in database',
            ], 500);
        }
    }

    public function addWatermark(Request $request)
    {
        $request['company_id'] = Auth::user()->company_id;
        $validatedData = $request->validate([
            'title' => 'required|min:3|max:50',
            'company_id' => 'required',

        ]);
        $watermark = Watermark::create($validatedData);
        return response()->json($watermark);
    }
    /**
     * Edit Watermark
     */
    public function getWatermark($id)
    {
        $watermark = Watermark::where('id', $id)->with('media_file')->first();
        return response()->json($watermark);
    }

    public function destroy($id)
    {
        $watermarkDelete = Watermark::where('id', $id)->first();
        $watermarkDelete->delete();
        return response()->json([
            'message' => 'Watermark has been deleted',
        ], 200);
    }
}

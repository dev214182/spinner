<?php

namespace App\Http\Controllers;

use App\Item;
use App\Media_file;
use Illuminate\Http\Request;

class ItemsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function getItemsByProduct($id)
    {
        $items = Item::where([
            'product_id' => $id,
            'item_type' => '360'
        ])->with('media_file')->get();

        return response()->json([
            'items' => $items,
        ], 200);
    }

    public function destroy($id)
    {

        $item = Item::where('id', '=', $id)->firstOrFail();
        $item->delete();

        // No need to update media_file since the media_file_id is now attached to items table
        // $file = Media_file::where('item_id', '=', $id)->firstOrFail();
        // $file->update(['item_id' => null]);

        return response()->json('Item has been deleted', 200);
    }

    public function setPosition(Request $request){
       
        $toReplaceItem = Media_file::where('id', '=', $request->item_id)->firstOrFail();
        $toReplaceItem->update(['interior_settings' => $request->interior_settings]);

        // Return response
        return response()->json([
            'status' => 'success',
            'message' => 'Item image has been updated',
        ], 200);
    }

    public function saveItem(Request $request)
    {
        // Decode data string
        // $requestData = json_decode($request, true);
        $requestData = $request;
        // dd($requestData['selected'][0]);
        // Check file Media_file id if exist
        // $selectedFile = Media_file::where('id', '=', $requestData['selected'])->firstOrFail();

        // // Check if already assigned
        // if ($selectedFile->item_id != null) {
        //     // Return error response
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => 'The selected file is already assigned to other item',
        //     ], 200);
        // }

        // Replace File
        if ($requestData['action'] == 'replace') {

            /**
             * NEEDS UPDATE
             */
            // Set the original to null
            // $originalFile = Media_file::where('item_id', '=', $requestData['item'])->firstOrFail();
            // $originalFile->update(['item_id' => null]);

            // Get and Update media_file_id value in Item
            $toReplaceItem = Item::where('id', '=', $requestData['item'])->firstOrFail();
            $toReplaceItem->update(['media_file_id' => $requestData['selected'][0]]);

            // Return response
            return response()->json([
                'status' => 'success',
                'message' => 'Item image has been updated',
            ], 200);
        }

        // Save File
        if ($requestData['action'] == 'save') {
            if ($requestData['product'] == null) { // Check if product exist
                return response()->json([
                    'request' => $requestData,
                    'status' => 'error',
                    'message' => 'No product was selected',
                ], 200);
            }

            // Create Item with media_file id
            $newItem = Item::create([
                'item_type' => $requestData['item_type'],
                'product_id' => $requestData['product'],
                'media_file_id' => $requestData['selected'][0],
            ]);

            // Assign file id to the created Item
            // $updateFile = Media_file::where('id', '=', $requestData['selected'])->firstOrFail();
            // $updateFile->update(['item_id' => $newItem->id]);

            // Return response
            return response()->json([
                'item' => $newItem,
                'status' => 'success',
                'message' => 'Panoramic Item has been saved',
            ], 200);
        }
    }

    /**
     * Panoramic Images
     */
    public function getScenesByProductId($id)
    {
        $scenes = Item::where([
            'item_type' => 'panorama',
            'product_id' => $id,
        ])->with('media_file')->get();
        return response()->json($scenes, 200);
    }

}

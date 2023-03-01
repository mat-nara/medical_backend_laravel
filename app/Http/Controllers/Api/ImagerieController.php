<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Storage;

class ImagerieController extends Controller
{
    /**
     * Upload item.
     *
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request)
    {
        if($request->hasFile('item')){
            $path           = $request->file('item')->store('public/imageries');
            $storage_path   = str_replace('public', 'storage', $path);
            return response(['error' => 0, 'message' => 'File is uploaded', 'item_path' => $storage_path]);
        }
        return response(['error' => 1, 'message' => 'File not found.']);
    }

    /**
     * Delete item.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($image_name)
    {
        $path = 'public/imageries/'.$image_name;

        if(Storage::exists($path)){
            Storage::delete($path);
            return response(['error' => 0, 'message' => 'File '.$image_name.' is deleted']);
        }
        return response(['error' => 1, 'message' => 'File '.$image_name.' doesn\'t exist']);
    }

}

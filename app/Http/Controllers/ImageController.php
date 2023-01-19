<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ImageRequest;
use Exception;
use Illuminate\Support\Facades\Storage;
use App\Models\Image;
use Illuminate\Support\Str;

class ImageController extends Controller
{
    public function index(Request $request){
        try{
            $page =  request()->get("page", 1);
            $content = Image::paginate(4);
            $count = Image::count();
        }
        catch(Exception $e){
            $response = array(
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            );
            return json_encode($response);
        }
        return compact('content','count');
    }

    public function store(ImageRequest $request){
        try{
            $image = new Image();
            $image->fill($request->all());
            $fileExtension = $request->image_url['file']->getClientOriginalExtension();
            $name =  Str::random(16) . "." .$fileExtension;
            Storage::disk('public')->putFileAs('images', $request->image_url['file'], $name);
            $image->image_url = '/storage/images/' . $name;
            $image->save();
            $response = array(
                'status' => 200,
                'message' => 'Success',
                'data' => $image
            );
        }
        catch(Exception $e){
            $response = array(
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            );
        }
        return json_encode($response);
    }

    public function destroy($id){
        try{
            $image = Image::findOrfail($id);
            $image->delete();
            $response = array(
                'status' => 200,
                'message' => 'Success',
                'data' => $image
            );
        }
        catch(Exception $e){
            $response = array(
                'status' => $e->getCode(),
                'message' => $e->getMessage()
            );
        }
        return json_encode($response);
    }
}

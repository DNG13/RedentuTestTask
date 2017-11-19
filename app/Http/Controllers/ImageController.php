<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Image as ImageModel;
use Image;
use App\Helpers\ImageBrightnessHelper;

class ImageController extends Controller
{
    public function index()
    {
        return view('image.upload');
    }
    public function upload(Request $request){
        $this->validate($request,[
            'file' => 'required|image|max:3000',
        ]);
        if($request->hasFile('file')){
            $image = new ImageModel;

            //$imageFile = $request->file('file');
            //$imageName = uniqid(). $imageFile->getClientOriginalName();
            //$imageFile->move(public_path('uploads'), $imageName);

            $file = $request->file('file');
            $extension = $file->clientExtension();
            $fileObject = $file->openFile();
            $fileObject->rewind();
            $content = $fileObject->fread($fileObject->getSize());
            $image->image = $content;
            $image->save();
            return response()->json(['Status'=>true, 'Message'=>'Image uploaded']);

//            $image = new Image;
//            $image->image =  $request->file->store('file');
//            $image->save();
        }
        return redirect('/show');
    }

    public function show()
    {
        $images = ImageModel::pluck('image');;
        $all_images= [];
        foreach ($images as $image){
            $all_images[] = $image;
        }
        return view('image.show', compact('all_images'));
    }

    public function index1()
    {
        return view('image.upload1');
    }
    public function upload1(Request $request, ImageBrightnessHelper $imageBright){
        $this->validate($request,[
            'main_file' => 'required|image|mimes:jpeg,jpg,png',
            'file' => 'required_without:text|nullable|image|mimes:jpeg,jpg,png',
            'text' => 'required_without:file|nullable|string|max:100',
            'wm'=>'required'
        ]);
        if($request->hasFile('main_file')){

            //$image = new ImageModel;
            $imageFile = $request->file('main_file');
            $imageName = uniqid(). $imageFile->getClientOriginalName();
            $extension = $imageFile->extension();
            $imageFile->move(public_path('uploads'), $imageName);
            $imagePath = public_path('uploads').'/'.$imageName;

            //get image height and width
            $imageSize = getimagesize( $imagePath);
            $imageWidth = $imageSize[0];
            $imageHeight = $imageSize[1];

            //find out color of image
            $luminance = $imageBright->run($imagePath, $extension);
            // create Image from file
            $img = Image::make($imagePath);

            if($request->get('wm')=='file'){
                if($request->hasFile('file')){
                    $imageFileWm = $request->file('file');
                    $imageRealPath = $imageFileWm->getRealPath();

                    //parameters for resizing watermark
                    $wmWidth  = $imageWidth/5;
                    $wmHeight = $imageHeight/5;
                    // create a new Image instance for inserting
                    $watermark = Image::make($imageRealPath);
                    $watermark->resize( $wmWidth, $wmHeight );

                    //insert a watermark
                    $img->insert( $watermark, 'center');

                    //save the image as a new file
                    $img->save($imagePath);

//                    return response()->json(['Status'=>true, 'Message'=>'ok with file']);
                }else{
                    return response()->json(['Status'=>true, 'Message'=>'Error with watermark file']);
                }
            }
            elseif($request->get('wm')=='text'){
                if($request->get('text')){
                    //color of text
                    $color =($luminance ==('dark')?'#FFFFFF':'#000000');
                    dd($color);
                    // write text
                    $img->text($request->get('text'), 0, 0, function($font) use ($color) {
                        $font->size(80);
                        $font->color($color);
                        $font->align('center');
                        $font->valign('top');
                        $font->angle(45);
                    });
                    $img->save($imagePath);
//                    return response()->json(['Status'=>true, 'Message'=>'ok with text']);
                }
                else{
                    return response()->json(['Status'=>true, 'Message'=>'Error with watermark text']);
                }
            }else{
//                return response()->json(['Status'=>true, 'Message'=>'Error with watermark']);
            }
//            $image = new ImageModel;
//            $file = $request->file('main_file');
//            $extension = $file->extension();
//            $fileObject = $file->openFile();
//            $fileObject->rewind();
//            $content = $fileObject->fread($fileObject->getSize());
//            $image->image = $content;
//            $image->save();
//            return response()->json(['Status'=>true, 'Message'=>'Image uploaded']);
        }
        return view('image.upload1');
    }
}
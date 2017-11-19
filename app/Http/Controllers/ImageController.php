<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Image as ImageModel;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
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

            $imageFile = $request->file('main_file');
            $imageName = uniqid(). $imageFile->getClientOriginalName();
            $extension = $imageFile->extension();
            $imageFile->move(public_path('uploads'), $imageName);
            $imagePath = public_path('uploads').'/'.$imageName;

            //find out color of image
            $luminance = $imageBright->run($imagePath, $extension);
            // create Image from file
            $img = Image::make($imagePath);
            //get image height and width
            $imageHeight = $img->height();
            $imageWidth = $img->width();

            if($request->get('wm')=='file'){
                if($request->hasFile('file')){

                    $imageFileWm = $request->file('file');
                    $imageRealPath = $imageFileWm->getRealPath();

                    //parameters for resizing watermark
                    $wmHeight = $imageHeight/10;
                    // create a new Image instance for inserting
                    $watermark = Image::make($imageRealPath);
                    $watermark->resize( null, $wmHeight, function ($constraint) {
                        $constraint->aspectRatio();
                    });

                    //get extension for watermark
                    $wmExtension = ($watermark->mime()=='image/png')?'png':'';

                    //find out color of watermark
                    $wmLuminance = $imageBright->run($imageRealPath, $wmExtension);
                    // invert colour of watermark if the same as image
                    if( $luminance ==  $wmLuminance){
                        $watermark->invert();
                    }
                }else{
                    return response()->json(['Status'=>true, 'Message'=>'Error with watermark file']);
                }
            }
            elseif($request->get('wm')=='text'){
                if($request->get('text')){
                    //color of text
                    $color =($luminance ==('dark')?'#FFFFFF':'#000000');
                    $size = $imageHeight/15;
                    // create a new empty image resource
                    $watermark = Image::canvas($imageWidth, $size*3);
                    // write text
                    $watermark->text($request->get('text'), 0, 0, function($font) use ($color, $size) {
                        $font->file( public_path('fonts/Marlboro.ttf'));
                        $font->size($size);
                        $font->color($color);
                        $font->align('left');
                        $font->valign('top');
                    });
                    //save watermark if need
                    $watermark->save('uploads/watermarks'.uniqid().'.png');
                }
                else{
                    return response()->json(['Status'=>true, 'Message'=>'Error with watermark text']);
                }
            }else{
                return response()->json(['Status'=>true, 'Message'=>'Error with watermark']);
            }

            // create new Intervention Image and turn it into greyscale version
            $watermark->greyscale();

            //set transparency to 50%
            $watermark->opacity(50);

            //insert a watermark
            $img->insert( $watermark, 'bottom-left');

            //save the image as a new file
            $img->save($imagePath);

            //save image to database
            $image = new ImageModel;
            $content = File::get($imagePath);
            $image->image = base64_encode($content);
            $image->save();

            //destroy resource
            $watermark->destroy();
            $img->destroy();
        }
        return view('image.upload1');
    }
}
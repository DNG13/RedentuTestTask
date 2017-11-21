<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Image as ImageModel;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
use App\Helpers\ImageBrightnessHelper;

class ImageController extends Controller
{
    /**
     * @var
     */
    private $imageBright;

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show()
    {
        $images = ImageModel::pluck('image');;
        $all_images= [];
        foreach ($images as $image){
            $all_images[] = $image;
        }
        return view('image.show', compact('all_images'));
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('image.upload');
    }

    /**
     * @param Request $request
     * @param ImageBrightnessHelper $imageBright
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function upload(Request $request, ImageBrightnessHelper $imageBright){

        $this->imageBright = $imageBright;

        $this->validate($request,[
            'main_file' => 'required|image|mimes:jpeg,jpg,png|max:4096',
            'file' => 'required_without:text|nullable|image|mimes:jpeg,jpg,png',
            'text' => 'required_without:file|nullable|string|max:200',
            'wm'=>'required',
            'resize' =>'nullable',
            'width'=>'nullable|numeric|max:4096|min:100',
            'height'=>'nullable|numeric|max:4096|min:100',
        ]);

        if($request->hasFile('main_file')){

            $imageFile = $request->file('main_file');
            $imageName = uniqid(). $imageFile->getClientOriginalName();
            $extension = $imageFile->extension();
            $imageFile->move(public_path('uploads'), $imageName);
            $imagePath = public_path('uploads').'/'.$imageName;

            //find out color of image
            $luminance = $this->imageBright->run($imagePath, $extension);

            // create Image from file
            $img = Image::make($imagePath);

            //resizing image if need
            if($request->resize == 'yes') {
                $img = $this->resizeImage($img, $request);
            }

            //create watermark
            if($request->get('wm')=='file') {
                if($request->hasFile('file')) {
                    $watermark = $this->prepareImageWatermark($img, $request, $luminance);
                } else {
                    return response()->json(['Status'=>true, 'Message'=>'Error with watermark file']);
                }
            }
            elseif($request->get('wm')=='text') {
                if($request->get('text')) {
                    $watermark = $this->prepareTextWatermark($img, $request, $luminance);
                    //save watermark if need
                    //$watermark->save('uploads/watermarks'.uniqid().'.png');
                } else {
                    return response()->json(['Status'=>true, 'Message'=>'Error with watermark text']);
                }
            } else {
                return response()->json(['Status'=>true, 'Message'=>'Error with watermark']);
            }

            // create new Intervention Image and turn it into greyscale version
            $watermark->greyscale();

            //set transparency to 30%
            $watermark->opacity(30);

            //insert a watermark
            $img->insert( $watermark, 'center');

            //save the image as a new file
            $img->save($imagePath);

            //save image to database
            $image = new ImageModel;
            $content = File::get($imagePath);
            $image->image = base64_encode($content);
            $image->save();

            //destroy resources
            unlink($imagePath);
            $watermark->destroy();
            $img->destroy();
        }

        return view('image.upload');
    }

    /**
     * @param $img
     * @param $request
     * @return mixed
     */
    private function resizeImage($img, $request)
    {
        $resizeWidth = $request->width;
        $resizeHeight = $request->height;
        if ($resizeWidth || $resizeHeight) {

            $heightRatio = $resizeHeight == null ? $heightRatio = 0 : $heightRatio = $img->height()/$resizeHeight;

            $widthRatio = $resizeWidth == null ? $widthRatio = 0 : $widthRatio = $img->width()/$resizeWidth;

            if ($heightRatio > $widthRatio) {
                $img->resize(null, $resizeHeight, function ($constraint) {
                    $constraint->aspectRatio();
                });
            } else {
                $img->resize($resizeWidth, null, function ($constraint) {
                    $constraint->aspectRatio();
                });
            }
        }

        return $img;
    }

    /**
     * @param $img
     * @param $request
     * @param $luminance
     * @return mixed
     */
    private function prepareImageWatermark($img, $request, $luminance)
    {
        $imageFileWm = $request->file('file');
        $imageRealPath = $imageFileWm->getRealPath();

        //parameters for resizing watermark
        $wmHeight = $img->height()/5;

        // create a new Image instance for inserting
        $watermark = Image::make($imageRealPath);
        $watermark->resize( null, $wmHeight, function ($constraint) {
            $constraint->aspectRatio();
        });

        //get extension for watermark
        $wmExtension = ($watermark->mime()=='image/png')?'png':'';

        //find out color of watermark
        $wmLuminance = $this->imageBright->run($imageRealPath, $wmExtension);

        // invert colour of watermark if the same as image
        if( $luminance == $wmLuminance){
            $watermark->invert();
        }

        return $watermark;
    }

    /**
     * @param $img
     * @param $request
     * @param $luminance
     * @return mixed
     */
    private function prepareTextWatermark($img, $request, $luminance)
    {
        //color of text
        $color =($luminance ==('dark')?'#FFFFFF':'#000000');
        $size = ceil($img->height()/15);

        // create a new empty image resource
        $watermark = Image::canvas($img->width(), $size*3);

        // write text
        $watermark->text($request->get('text'), $img->width()/2, $size*1.5, function($font) use ($color, $size) {
            $font->file( public_path('fonts/Roboto-Regular.ttf'));
            $font->size($size);
            $font->color($color);
            $font->align('center');
            $font->valign('center');
        });

        return $watermark;
    }
}
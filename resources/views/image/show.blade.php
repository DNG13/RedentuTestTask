@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Show image</div>
                    <div class="panel-body">
                        @foreach($all_images as $key=>$image)
                          <div>{{$key}}<img src="data:image/jpeg;base64, {{ base64_encode($image) }}"/></div>
                        @endforeach
                    </div>
                    <a class="btn btn-primary" href="/upload">To form for uploading image.</a>
                </div>
            </div>
        </div>
    </div>
@endsection
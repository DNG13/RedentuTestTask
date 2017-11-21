@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-body">
                        @foreach($all_images as $key=>$image)
                            <div>
                                <p>{{$key}}</p>
                                <div><img src="data:image/jpeg;base64, {{$image }}"/></div>
                            </div>
                        @endforeach
                    </div>
                    <a class="btn btn-primary" href="/">To form for uploading image.</a>
                </div>
            </div>
        </div>
    </div>
@endsection
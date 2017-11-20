@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Upload picture</div>

                    <div class="panel-body">
                        <form action="{{ route('upload1') }}" class="dropzone data-dz-remove" enctype="multipart/form-data" id="my-awesome-dropzone" >
                            {{ csrf_field() }}
                            {{--<div class="fallback">--}}
                                {{--<input name="file" type="file"  multiple required/>--}}
                                {{--@if ($errors->has('file'))--}}
                                    {{--<div class="alert alert-danger">--}}
                                        {{--There were some problems with your input.<br><br>--}}
                                        {{--<ul>--}}
                                            {{--@foreach ($errors->all() as $error)--}}
                                                {{--<li>{{ $error }}</li>--}}
                                            {{--@endforeach--}}
                                        {{--</ul>--}}
                                    {{--</div>--}}
                                {{--@endif--}}
                            {{--</div>--}}
                            {{--<button type="submit" class="btn btn-primary">Save</button>--}}
                        </form>
                    </div>
                    <a class="btn btn-primary" href="/show">Show for uploading image.</a>

                </div>
            </div>
        </div>
    </div>
@endsection
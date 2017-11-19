@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Upload picture</div>

                    <div class="panel-body">
                        <form action="{{ route('upload1') }}" enctype="multipart/form-data" method="post">
                            {{ csrf_field() }}
                            <h3>Завантажити зображення для накладання водяного знаку</h3>
                            <div>Максимальний розмір файлу: 100 МБ</div>
                            <div>
                                <label for="image_uploads">Choose images to upload (PNG, JPG, JPEG)</label>
                                <input name="main_file" type="file"  accept=".jpeg, .jpg, .png" required/>
                            </div>
                            @if ($errors->has('main_file'))
                                <div class="alert alert-danger">
                                    There were some problems with your input.<br>
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <hr>
                            <h3>Завантажити водяний знак</h3>
                            <div>
                                <label for="wm">
                                <input type="radio" value="file" name="wm"  checked required>
                                    Картинку
                                </label>
                                <div>
                                    <div>Щоб отримати найкращі результати, використовуйте прозорі одноколірні зображення.</div>
                                    <div>Максимальний розмір файлу: 1 МБ</div>
                                </div>
                                <div>
                                    <label for="image_uploads">Choose images or text to upload (PNG, JPG, JPEG)</label>
                                    <input name="file" type="file"/>
                                </div>
                                @if ($errors->has('file'))
                                    <div class="alert alert-danger">
                                        There were some problems with your input.<br>
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                <label for="wm_text">
                                    <input type="radio" value="text" name="wm">
                                    Або текст
                                </label>
                                <div>
                                    <textarea rows="3" cols="60" name="text"></textarea>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
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
                            <div>
                                <label for="image_uploads">Оберіть файл для завантаження(PNG, JPG, JPEG)</label>
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
                                </div>
                                <div>
                                    <label for="image_uploads">Оберіть файл для завантаження(PNG, JPG, JPEG)</label>
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
                                    @if ($errors->has('text'))
                                        <div class="alert alert-danger">
                                            There were some problems with your input.<br>
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif

                                    Або введіть текст
                                </label>
                                <div>
                                    <textarea rows="3" cols="60" name="text"></textarea>
                                </div>
                                <hr>
                                <div>
                                    <h3>Можливість для зміни розмірів зображення за поданними параметрами</h3>
                                    <input name="resize" type="checkbox" value="yes">
                                    <label for="resize">Використати</label>
                                    @if ($errors->has('resize'))
                                        <div class="alert alert-danger">
                                            There were some problems with your input.<br>
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    <div>
                                        <label for="width">Ширина</label>
                                        <input name="width" type="number" max="4096"/>
                                        @if ($errors->has('width'))
                                            <div class="alert alert-danger">
                                                There were some problems with your input.<br>
                                                <ul>
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                        <label for="height">Висота</label>
                                        <input name="height" type="number" max="4096"/>
                                        @if ($errors->has('height'))
                                            <div class="alert alert-danger">
                                                There were some problems with your input.<br>
                                                <ul>
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
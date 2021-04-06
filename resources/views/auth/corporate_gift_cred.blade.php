
@extends('layouts.layoutiframe')
@section('content')
    <style>
        .content {
            position: absolute;
            left: 50%;
            top: 50%;
            -webkit-transform: translate(-50%, -50%);
            transform: translate(-50%, -50%);
        }

    /*.container {*/
    /*height: 100%;*/
    /*display: flex;*/
    /*justify-content: center;*/
    /*align-items: center;*/
    /*}*/
    </style>
{{--<input class="form-control form-control-lg" type="text" placeholder=".form-control-lg">--}}
<div class="container h-100">
    <div class="row h-100 justify-content-center align-items-center">
        <form>
            <div class="form-group">
                <div class="col-md-12 col-sm-12">
                    <label for="staticEmail" class="col-sm-2 col-form-label">Enter Corporate Gift Token ID</label>
                </div>


                <div class="col-md-12 col-sm-12">
                    <input type="text"  class="form-control form-control-lg" id="staticEmail" value="">
                </div>
            </div>

        </form>
    </div>

</div>
@stop
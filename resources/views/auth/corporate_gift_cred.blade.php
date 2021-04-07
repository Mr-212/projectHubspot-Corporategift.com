
@extends('layouts.layoutiframe')
@section('content')

<div class="row h-100">
    <div class="col-md-12 my-auto">
        <form  action="{{url('/').'/post_corporate_gift_token'}}" method="post">
            @csrf
            <input type="hidden" name="hub_id" value="{{$hub_id}}">
            <div class="form-group">
                <div class="col-md-12">
                    <label for="staticEmail" class=""><strong>Corporate Gift Token</strong></label>
                </div>

                <div class="col-md-12">
                    <input type="text"  class="form-control form-control-lg" id="staticEmail" name="corporate_gift_token" value="" required>
                </div>

            </div>
            <div class="col-md-12">
                <button type="submit"  class="btn btn-primary float-right" id="" value="Submit">Submit</button>
            </div>
        </form>
    </div>
</div>
@stop
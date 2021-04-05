@extends('layouts.layoutiframe')
@section('content')
    <div class="container">
        <div class="card w-75">
            <div class="card-body">
                <h5 class="card-title">Card title</h5>
                <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                <a href="#" class="btn btn-primary">Button</a>
            </div>
        </div>

        <div class="card w-50">
            <div class="card-body">
                <h5 class="card-title">Card title</h5>
                <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                <a href="#" class="btn btn-primary">Button</a>
            </div>
        </div>
        @if(isset($gift_products) && count($gift_products) > 0)
            @foreach($gift_products as $card)
            <div class="card w-75">
                <div class="card-body">
                    <h5 class="card-title">Card title</h5>
                    <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                    <a href="#" class="btn btn-primary">Button</a>
                </div>
            </div>

            <div class="card w-50">
                <div class="card-body">
                    <h5 class="card-title">Card title</h5>
                    <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
                    <a href="#" class="btn btn-primary">Button</a>
                </div>
            </div>

            {{--<form id="contact" action="{{url('/').'/post_hubspot_send_gift_request'}}" method="post">--}}
                {{--@csrf--}}
                {{--<h3>Send Gift</h3>--}}
                {{--<fieldset>--}}
                    {{--<input placeholder="Subject" type="text" name="subject" tabindex="1" required autofocus>--}}
                {{--</fieldset>--}}
                {{--<fieldset>--}}
                    {{--<input placeholder="Email Address" type="email" name="email" value="{{$email}}" tabindex="2" disabled>--}}
                {{--</fieldset>--}}
                {{--<fieldset>--}}
                    {{--<textarea placeholder="Type your message here...." name="message" tabindex="5" required></textarea>--}}
                {{--</fieldset>--}}
                {{--<fieldset>--}}
                    {{--<button name="submit" type="submit" id="contact-submit" data-submit="...Sending">Submit</button>--}}
                {{--</fieldset>--}}
    {{----}}
            {{--</form>--}}
            @endforeach

         @endif
    </div>
@stop
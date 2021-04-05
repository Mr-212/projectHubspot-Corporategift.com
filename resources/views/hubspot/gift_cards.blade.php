@extends('layouts.layoutiframe')
@section('content')
    <div class="container">
   <div class="row">

        @if(isset($gift_products) && count($gift_products) > 0)
            @foreach($gift_products as $card)
                <div class="col-md-4 col-sm-4 mt-4" >

                <div class="card" style="width: 20rem;">
                    <img class="card-img-top" src="{{url('/uploads/gifts/gift.jpeg')}}" alt="Card image cap">
                    <div class="card-body">
                        <h5 class="card-title" style="height: 50px; overflow: auto">{{$card['name']}}</h5>
                        <p class="card-text overflow-scroll"  style="height: 80px; overflow: auto">{{ strip_tags($card['description'])}}</p>

                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Price: <strong>{{$card['price']?:'--'}}</strong></li>
                    </ul>
                    <div class="card-footer">
                        {{--<p>{{$card['price']}}</p>--}}
                        {{--<button  class="btn btn-primary float-right" type="button"  data-target="#modal-{{$card['id']}}" data-toggle="modal" >Send</button>--}}
                        <a class="btn btn-primary float-right" href="{{url('/').'/get_hupspot_send_gift_request?product_id='.$card["id"].'&email='.$email}}" >Send</a>
                        {{--<a href="#" class="card-link">View</a>--}}
                    </div>
                </div>
                </div>

               {{--<div class="modal fade" id="modal-{{$card['id']}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">--}}
                   {{--<div class="modal-dialog" role="document">--}}
                       {{--<div class="modal-content">--}}
                           {{--<div class="modal-header">--}}
                               {{--<h5 class="modal-title" id="exampleModalLabel">Modal title</h5>--}}
                               {{--<button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
                                   {{--<span aria-hidden="true">&times;</span>--}}
                               {{--</button>--}}
                           {{--</div>--}}
                           {{--<div class="modal-body">--}}
                               {{--@include('hubspot.hubspot-sendgift',['email' =>$email])--}}
                               {{--<form id="contact" action="{{url('/').'/post_hubspot_send_gift_request'}}" method="post">--}}
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

                               {{--</form>--}}
                           {{--</div>--}}
                           {{--<div class="modal-footer">--}}
                               {{--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>--}}
                               {{--<button type="button" class="btn btn-primary">Save changes</button>--}}
                           {{--</div>--}}
                       {{--</div>--}}
                   {{--</div>--}}
               {{--</div>--}}

            @endforeach

         @endif
   </div>
    </div>



@stop

@push('scripts')

@endpush
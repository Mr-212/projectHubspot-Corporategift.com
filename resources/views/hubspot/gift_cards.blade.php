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
                        {{--<a class="card-link" href="{{url('/').'/get_hupspot_send_gift_request?product_id='.$card["id"].'&email='.$email}}" >Send</a>--}}
                        <a class="btn btn-primary float-right" href="{{url('/').'/get_hupspot_send_gift_request?product_id='.$card["id"].'&email='.$email}}" >Send</a>
                        {{--<a href="#" class="card-link">View</a>--}}
                    </div>
                </div>
                </div>

            @endforeach

         @endif
   </div>
    </div>
@stop
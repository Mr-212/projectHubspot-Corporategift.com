@extends('layouts.layoutiframe')
@section('content')
    <div class="container">
   <div class="row">


        @if(isset($gift_products) && count($gift_products) > 0)
            @foreach($gift_products as $card)
                <div class="col-md-4 col-sm-4">


                <div class="card" style="width: 18rem;">
                    <img class="card-img-top" src=".../100px180/?text=Image cap" alt="Card image cap">
                    <div class="card-body">
                        <h5 class="card-title">Card title</h5>
                        <p class="card-text overflow-auto" >{{$card['description']}}</p>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Status: <strong>In Process</strong></li>
                        {{--<li class="list-group-item">Dapibus ac facilisis in</li>--}}
                        {{--<li class="list-group-item">Vestibulum at eros</li>--}}
                    </ul>
                    <div class="card-body">
                        <a href="{{url('/').'/post_hubspot_send_gift_request'}}" class="card-link">Send</a>
                        {{--<a href="#" class="card-link">View</a>--}}
                    </div>
                </div>
                </div>

            @endforeach

         @endif
   </div>
    </div>
@stop
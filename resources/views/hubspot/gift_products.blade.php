@extends('layouts.layoutiframe')
@section('content')
    <div class="">

        @if(isset($gift_products) && count($gift_products) > 0)
            <div class="row">
            @foreach($gift_products as $card)
               @php
             //  $card = $card->toArray();
               @endphp
                <div class="col-md-4 col-sm-4 mt-4 pl-2">

                <div class="card" style="width: 18rem;">
                    {{--<img class="card-img-top" src="{{url('/uploads/gifts/gift.jpeg')}}" alt="Card image cap">--}}
                    <img class="card-img-top" src="{{'https://development.corporategift.com/media/catalog/product/e/t/uploads/gifts/'.$card['data']['image']}}" alt="Card image cap">
                    <div class="card-body">
                        <h5 class="card-title" style="height: 50px; overflow: auto">{{$card['data']['name']}}</h5>
                        <p class="card-text overflow-scroll"  style="height: 80px; overflow: auto">{{ strip_tags($card['data']['description'])}}</p>

                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Price: <strong>{{$card['data']['price']?:'--'}}</strong></li>
                    </ul>
                    <div class="card-footer">
                        {{--<p>{{$card['price']}}</p>--}}
                        <a  class="btn btn-primary float-right" type="button" href="#modal-{{$card['data']['id']}}" data-target="#modal-{{$card['data']['id']}}" data-toggle="modal" >Send</a>
                        {{--<a class="btn btn-primary float-right" href="{{url('/').'/get_hupspot_send_gift_request?product_id='.$card["id"].'&email='.$email}}" >Send</a>--}}
                        {{--<a href="#" class="card-link">View</a>--}}
                    </div>
                </div>
                </div>

               <div class="modal fade subject_modal" id="modal-{{$card['data']['id']}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                   <div class="modal-dialog" role="document">
                       <div class="modal-content">
                           <form id="contact" action="{{url('/')."/post_hubspot_send_gift_request?product_id={$card['data']['id']}&name={$name}"}}" method="post">

                           <div class="modal-header">
                               <h5 class="modal-title" id="exampleModalLabel">Add Subject</h5>
                               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                   <span aria-hidden="true">&times;</span>
                               </button>
                           </div>
                           <div class="modal-body">
                               {{--@include('hubspot.hubspot-sendgift1',['email' =>$email])--}}
                                   @csrf
                                   <input type="hidden" name="identifier" value="{{$identifier}}">
                                   {{--<input type="hidden" name="identifier" value="{{$identifier}}">--}}
                                   <div class="form-group">
                                   <fieldset>
                                       <input placeholder="Email Address" class="form-control" type="text" name="email" value="{{$email}}" tabindex="2" readonly>
                                   </fieldset>
                                   </div>
                                   <div class="form-group">
                                   <fieldset>
                                       <input placeholder="Subject" class="form-control" type="text" name="subject" tabindex="1" required autofocus>
                                   </fieldset>
                                   </div>
                                   <div class="form-group">
                                   <fieldset>
                                       <textarea placeholder="Type your message here...." class="form-control" name="message" tabindex="5" required></textarea>
                                   </fieldset>
                                   </div>

                                   {{--<fieldset>--}}
                                       {{--<button name="submit" class="btn btn-info float-right form-control-sm" type="submit" id="contact-submit" data-submit="...Sending">Send</button>--}}
                                   {{--</fieldset>--}}


                           </div>
                           <div class="modal-footer">
                               <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                               <button type="submit" class="btn btn-primary">Send</button>
                           </div>
                           </form>
                       </div>
                   </div>
               </div>

            @endforeach

            </div>

            <div class="row">
                <div class="col-12 d-flex justify-content-center pt-4">

                {{--{{ $gift_products->links() }}--}}
                </div>
            </div>


         @endif

    </div>




@stop

@push('scripts')
<script>
    $('.subject_modal').model('hidden.bs.modal',function () {
        //$(this).find("input['name'='subject']").empty();
    })
</script>
@endpush
@extends('layouts.layoutiframe')
@section('content')
    <div class="">

        @if(isset($gift_products) && count($gift_products) > 0)
            <div class="row">
            @foreach($gift_products as $card)
               @php
        
               @endphp
                <div class="col-md-4 col-sm-4 mt-4 pl-2">

                <div class="card" style="width: 18rem;">
                    <img class="card-img-top" src="{{'https://development.corporategift.com/media/catalog/product/'.@$card['data']['image']}}" height="200px" width="100px" alt="Card image cap">
                    <div class="card-body">
                        <h5 class="card-title" style="height: 50px; overflow: auto">{{$card['data']['name']}}</h5>
                        <p class="card-text overflow-scroll"  style="height: 80px; overflow: auto">{{ strip_tags($card['data']['description'])}}</p>

                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Price: <strong>{{$card['data']['price']?:'--'}}</strong></li>
                    </ul>
                    <div class="card-footer">
                        <a  class="btn btn-primary float-right" type="button" href="#modal-{{$card['data']['id']}}" data-target="#modal-{{$card['data']['id']}}" data-toggle="modal" >Send</a>
                        {{--<a class="btn btn-primary float-right" href="{{url('/').'/get_hupspot_send_gift_request?product_id='.$card["id"].'&email='.$email}}" >Send</a>--}}
                    </div>
                </div>
                </div>

               <div class="modal fade subject_modal" id="modal-{{$card['data']['id']}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true"  data-backdrop="static" data-keyboard="false">
                   <div class="modal-dialog" role="document">
                       <div class="modal-content">
                           @php
                              $params['product_id'] = $card['data']['id'];
                              $query = http_build_query($params);
                           @endphp


                           <form id="form-{{$card['data']['id']}}" action="{{url('/')."/post_hubspot_send_gift_request?$query"}}" method="post">

                           <div class="modal-header">
                               <h5 class="modal-title" id="exampleModalLabel">Add Subject</h5>
                               <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                   <span aria-hidden="true">&times;</span>
                               </button>
                           </div>
                           <div class="modal-body">
                               <div id="message">

                               </div>
                                   @csrf
                                   <div class="form-group">
                                   <fieldset>
                                       <input placeholder="Email Address" class="form-control" type="text" name="email" value="{{$params['email']}}" tabindex="2" readonly>
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

                           </div>
                           <div class="modal-footer">
                               <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                               {{--<button type="button" id="send_gift_button" data-id="{{$card['data']['id']}}" data-url="{{url('/')."/post_hubspot_send_gift_request?product_id={$card['data']['id']}&name={$name}"}}" class="btn btn-primary">Send</button>--}}
                               <button type="button" id="send_gift_button" data-id="{{$card['data']['id']}}" data-url="{{url('/')."/post_hubspot_send_gift_request?{$query}"}}" class="btn btn-primary">Send</button>

                               <button class="btn btn-info" id="sending_button" type="button" style="display:none" disabled>
                                   <span class="spinner-border spinner-border-sm"></span>
                                   Sending...
                               </button>
                           </div>
                           </form>
                       </div>
                   </div>
               </div>

            @endforeach

            </div>

            <div class="row">
                <div class="col-12 d-flex justify-content-center pt-4">

                </div>
            </div>


         @endif

    </div>




@stop

@push('scripts')
<script>
    // $('.subject_modal').model('hidden.bs.modal',function () {
    //     //$(this).find("input['name'='subject']").empty();
    // });
    $(document).on('click','#send_gift_button',function (e) {
        e.preventDefault();
        var url = $(this).data('url');
        var form_id = '#form-'+$(this).data('id');
        var model_id = '#modal-'+$(this).data('id');
        var result = { };
        $.each($(form_id).serializeArray(), function() {
            result[this.name] = this.value;
        });

        var _this = $(this);

        $.ajax({
            url: url,
            data:result,
            dataType:'json',
            method:'post',

            beforeSend: function( ) {
              _this.hide();
              $(_this).siblings('#sending_button').show();
            }
        }).done(function(data) {
            console.log(data);
            //_this.show();
            $(_this).siblings('#sending_button').hide();
            // $(model_id).find('#message').html('<span class="col-md-12 bg-success"><strong  class="">Gift sent successfully.</strong></span>');
            $(model_id).find('#message').html('<div class="alert alert-success alert-dismissible fade show" role="alert">\n' +
                '  <strong>Gift sent successfully.</strong>' +
                '  <button type="button" class="close" data-dismiss="alert" aria-label="Close">\n' +
                '    <span aria-hidden="true">&times;</span>\n' +
                '  </button>\n' +
                '</div>');

        });

    });
</script>
@endpush
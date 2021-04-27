
@extends('layouts.app')
@push('styles')
<style>
    /* ! normalize.css v8.0.1 | MIT License | github.com/necolas/normalize.csshtml{line-height:1.15;-webkit-text-size-adjust:100%}body{margin:0}a{background-color:transparent}[hidden]{display:none}html{font-family:system-ui,-apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Helvetica Neue,Arial,Noto Sans,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji;line-height:1.5}*,:after,:before{box-sizing:border-box;border:0 solid #e2e8f0}a{color:inherit;text-decoration:inherit}svg,video{display:block;vertical-align:middle}video{max-width:100%;height:auto}.bg-white{--bg-opacity:1;background-color:#fff;background-color:rgba(255,255,255,var(--bg-opacity))}.bg-gray-100{--bg-opacity:1;background-color:#f7fafc;background-color:rgba(247,250,252,var(--bg-opacity))}.border-gray-200{--border-opacity:1;border-color:#edf2f7;border-color:rgba(237,242,247,var(--border-opacity))}.border-t{border-top-width:1px}.flex{display:flex}.grid{display:grid}.hidden{display:none}.items-center{align-items:center}.justify-center{justify-content:center}.font-semibold{font-weight:600}.h-5{height:1.25rem}.h-8{height:2rem}.h-16{height:4rem}.text-sm{font-size:.875rem}.text-lg{font-size:1.125rem}.leading-7{line-height:1.75rem}.mx-auto{margin-left:auto;margin-right:auto}.ml-1{margin-left:.25rem}.mt-2{margin-top:.5rem}.mr-2{margin-right:.5rem}.ml-2{margin-left:.5rem}.mt-4{margin-top:1rem}.ml-4{margin-left:1rem}.mt-8{margin-top:2rem}.ml-12{margin-left:3rem}.-mt-px{margin-top:-1px}.max-w-6xl{max-width:72rem}.min-h-screen{min-height:100vh}.overflow-hidden{overflow:hidden}.p-6{padding:1.5rem}.py-4{padding-top:1rem;padding-bottom:1rem}.px-6{padding-left:1.5rem;padding-right:1.5rem}.pt-8{padding-top:2rem}.fixed{position:fixed}.relative{position:relative}.top-0{top:0}.right-0{right:0}.shadow{box-shadow:0 1px 3px 0 rgba(0,0,0,.1),0 1px 2px 0 rgba(0,0,0,.06)}.text-center{text-align:center}.text-gray-200{--text-opacity:1;color:#edf2f7;color:rgba(237,242,247,var(--text-opacity))}.text-gray-300{--text-opacity:1;color:#e2e8f0;color:rgba(226,232,240,var(--text-opacity))}.text-gray-400{--text-opacity:1;color:#cbd5e0;color:rgba(203,213,224,var(--text-opacity))}.text-gray-500{--text-opacity:1;color:#a0aec0;color:rgba(160,174,192,var(--text-opacity))}.text-gray-600{--text-opacity:1;color:#718096;color:rgba(113,128,150,var(--text-opacity))}.text-gray-700{--text-opacity:1;color:#4a5568;color:rgba(74,85,104,var(--text-opacity))}.text-gray-900{--text-opacity:1;color:#1a202c;color:rgba(26,32,44,var(--text-opacity))}.underline{text-decoration:underline}.antialiased{-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale}.w-5{width:1.25rem}.w-8{width:2rem}.w-auto{width:auto}.grid-cols-1{grid-template-columns:repeat(1,minmax(0,1fr))}@media (min-width:640px){.sm\:rounded-lg{border-radius:.5rem}.sm\:block{display:block}.sm\:items-center{align-items:center}.sm\:justify-start{justify-content:flex-start}.sm\:justify-between{justify-content:space-between}.sm\:h-20{height:5rem}.sm\:ml-0{margin-left:0}.sm\:px-6{padding-left:1.5rem;padding-right:1.5rem}.sm\:pt-0{padding-top:0}.sm\:text-left{text-align:left}.sm\:text-right{text-align:right}}@media (min-width:768px){.md\:border-t-0{border-top-width:0}.md\:border-l{border-left-width:1px}.md\:grid-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}}@media (min-width:1024px){.lg\:px-8{padding-left:2rem;padding-right:2rem}}@media (prefers-color-scheme:dark){.dark\:bg-gray-800{--bg-opacity:1;background-color:#2d3748;background-color:rgba(45,55,72,var(--bg-opacity))}.dark\:bg-gray-900{--bg-opacity:1;background-color:#1a202c;background-color:rgba(26,32,44,var(--bg-opacity))}.dark\:border-gray-700{--border-opacity:1;border-color:#4a5568;border-color:rgba(74,85,104,var(--border-opacity))}.dark\:text-white{--text-opacity:1;color:#fff;color:rgba(255,255,255,var(--text-opacity))}.dark\:text-gray-400{--text-opacity:1;color:#cbd5e0;color:rgba(203,213,224,var(--text-opacity))}} */
</style>

@endpush
    
@section('content')  
    <div class="antialiased">    
    <div class="row mt-5">
    <div class="col-md-6">
        <div class="col-md-12 mx-auto sm:px-6 lg:px-8">
            <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-1">
                    
                    <div class="p-1">
                       
                        <div class="Col-md-12">
                                <p>Name:  <strong>{{ auth()->user()->name}}</strong> </p>
                                <p>Email: <strong>{{ auth()->user()->email}}</strong> </p>
                        </div>

                        @if(isset(auth()->user()->app->unique_app_id))
                        <div class="ml-12">
                            <div class="col-md-12">
                                 <p>App ID: <strong>{{ auth()->user()->app->unique_app_id}}</strong> </h4>
                            </div>
                          
                            <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                                <form  action="{{url('/').'/post_corporate_gift_token'}}" method="post">
                                    @csrf
                                    <input type="hidden" name="identifier" value="{{auth()->user()->app->identifier}}">
                                    <div class="row col form-group">
                                        <div class="col-md-12">
                                            <label for="staticEmail" class=""><strong>Corporate Gift Token</strong></label>
                                        </div>
                        
                                        <div class="col-md-12">
                                            <input type="text"  class="form-control form-control" id="corporate_gift_token_input" name="corporate_gift_token" disabled value="{{ auth()->user()->app->corporate_gift_token }}" required>
                                        </div>

                                        <div class="col-md-12 my-4">

                                            <button type="submit"  class="btn btn-primary float-right update_token_btn" id="" style="display: none" value="Submit">Update</button>

                                            <button type="button"  class="btn btn-info float-right edit_token_btn" id="" value="" >Edit</button>

                                        </div>
                                    </div>
                                   
                                </form>
                            </div>
                        </div>
                        @endif
                    </div>
                   
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="col-md-6 float-right mx-auto sm:px-6 lg:px-8 {{ isset( auth()->user()->app->unique_app_id)?'disabled':'' }}" >
            <div class="mt-8 bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-1">
                    <div class="p-1">
                        <div class="flex items-center">
                            <div class="text-center text-lg leading-7 font-semibold">
                                <h3>Connect to </h3>
                                <a href="https://app.hubspot.com/oauth/authorize?client_id=3cbf1a7e-914a-4934-9b8b-285dd93fe43b&redirect_uri=https://corporategift.dev-techloyce.com/hupspot-authentication&scope=contacts%20oauth%20tickets" class="underline text-gray-900 dark:text-white"><img border="0" alt="W3Schools" src="{{url('/').'/uploads/system/hubspotlogo-web-color.svg'}}" width="180" height="60"></a>
                            </div>
                        </div>
                        <div class="ml-12">
                            <div class="mt-2 text-gray-600 dark:text-gray-400 text-sm">
                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
             
        </div>
    </div>  
@stop

@push('scripts')
<script>
    $(document).ready(function (){
      $('.edit_token_btn').on('click', function(){
            // $('#corporate_gift_token_input').attr('disabled', false);
            $('.update_token_btn').toggle();
            if($('.update_token_btn').is(':visible'))
                $('#corporate_gift_token_input').attr('disabled', false);
            else
                $('#corporate_gift_token_input').attr('disabled', true);

      });

    });
</script>

@endpush
@extends('layouts.layoutiframe')
@section('content')
  <div class="container">

    <form id="contact" action="{{url('/').'/post_hubspot_send_gift_request'}}" method="post">
      @csrf
      <h3>Send Gift</h3>
      <fieldset>
        <input placeholder="Subject" type="text" name="subject" tabindex="1" required autofocus>
      </fieldset>
      <fieldset>
        <input placeholder="Email Address" type="email" name="email" value="{{$email}}" tabindex="2" disabled>
      </fieldset>
      <fieldset>
        <textarea placeholder="Type your message here...." name="message" tabindex="5" required></textarea>
      </fieldset>
      <fieldset>
        <button name="submit" type="submit" id="contact-submit" data-submit="...Sending">Submit</button>
      </fieldset>
  
    </form>
  </div>
  @stop

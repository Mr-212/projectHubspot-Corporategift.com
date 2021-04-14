
@extends('layouts.layoutiframe')
@section('content')

<div class="row h-100">
    <div class="col-md-12">
    <strong>Success!</strong>
    </div>
</div>
@stop

@push('scripts')
    <script type="text/javascript">setTimeout("window.close();", 3000);</script>
@endpush
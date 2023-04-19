@extends('layouts.app')

@section('content')

    <div class="w-100 p-3">
        <help-center video-image="{{ asset('images/videoPlaceholder.jpg') }}" platform-url="{{ env('PLATFORM_URL') }}">
        </help-center>
    </div>
@stop

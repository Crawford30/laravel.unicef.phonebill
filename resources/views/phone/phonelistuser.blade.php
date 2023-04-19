@extends('layouts.app')
@section('content')
    <div class="w-100 p-3">
        <phone-list-user billownerid="{{ auth()->user()->id }}" docOne={{ $docOne }} docTwo={{ $docTwo }} ></phone-list-user>
    </div>
    @push('custom-script')
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.2.228/pdf.min.js"></script>
    <script src="{{ asset('js/html2pdf.bundle.js') }}"></script>
@endpush
@stop




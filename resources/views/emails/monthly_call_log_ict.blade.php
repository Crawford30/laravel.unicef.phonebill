@component('mail::message')
Hello {{ $username }},

The monthly phone bills for the period of <b>{{ $fromDate }}</b> - <b>{{$toDate  }}</b>  have been dispatched to all staff. Herewith is a summary: <br><br>


<div class="justify-content-center mt-4 mb-4">
   
    <h6>Total Records in file: <strong>{{ $totalRecords }}</strong> </h6>
    <hr>
    <h6>Total Unique Mobile Numbers: <strong>{{ $totalUniqueMobileNumbersCalled }}</strong> </h6>
    <hr>
    {{-- <h6>Total Unique Phones: <strong>{{ $totalUniquePhones }}</strong> </h6>
    <hr> --}}
    <h6>Start Date: <strong>{{ $fromDate }}</strong> </h6>
    <hr>
    <h6>End Date: <strong>{{ $toDate }}</strong> </h6>

  
</div>



Regards,<br>
UNICEF Platform Team
@endcomponent
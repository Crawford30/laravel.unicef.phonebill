@component('mail::message')
Hello {{ $username }},

Your monthly phone bills are now avaliable. Herewith the summary: <br> 

<div class="justify-content-center mt-4 mb-4">
    <h6>Total calls between  <strong>{{ $fromDate }}</strong>  and  <strong>{{ $toDate }}</strong> </h6>
    <hr>
    <h6>Total Unique Number(s) Dialed: <strong>{{ $totalUniqueNumbersCalled }}</strong> </h6>
    <hr>
    <h6>Total Cost: <strong>MWK: {{ $totalCost }}</strong> </h6>
    <hr>
    <h6>Identification Deadline: <strong>{{ $identificationDeadline }}</strong> </h6>
    <hr>
</div>


@component('mail::button', ['url' => route("call-log-identification", $phoneBillId)])
LOGIN TO IDENTIFY
@endcomponent

Please note the deadline for the identification is in <strong>{{ $remainingDays }} days</strong>.

Regards,<br>
UNICEF Platform Team
@endcomponent

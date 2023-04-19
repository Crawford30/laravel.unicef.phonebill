@component('mail::message')
Hello {{ $username }},

You have completed the identification of your monthly calls. Herewith is a summary:

<div class="justify-content-center mt-4 mb-4">
    <h6>Total Calls between : <strong>{{ $fromDate }}</strong> and <strong>{{ $toDate }}</strong></h6>
    <hr>
    <h6>Official Calls: <strong>{{ $officialCalls }} ({{ $officialCallsPercentage }}%) </strong> </h6>
    <hr>
    <h6>Personal Calls: <strong>{{  $personalCalls }} ({{ $personalCallsPercentage }}%) </strong> </h6>
    <hr>
    <h6>Amount Owed (after MWK {{$allowanceAmount}} subsidy): <strong>MWK {{ $amountOwed }}</strong> </h6>
    <hr>
    <h6>Preferred Payment Method: <strong>{{ $paymentMethod }}</strong> </h6>
</div>

Upon confirmation of this position by Administration, you will then be required to effect payment(if any).




Regards,<br>
UNICEF Platform Team
@endcomponent
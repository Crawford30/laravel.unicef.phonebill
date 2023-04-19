@component('mail::message')
Hello BSC,

{{ $fromName }} has uploaded the Payment Evidence for his/her phone bills for the period  of <strong>{{ $fromDate }}</strong> and <strong>{{ $toDate }}</strong>.

Herewith is a summary:

<div class="justify-content-center mt-4 mb-4">
    <h6>Total Calls between : <strong>{{ $fromDate }}</strong> and <strong>{{ $toDate }}</strong></h6>
    <hr>
    <h6>Official Calls: <strong>{{ $officialCalls }} ({{ $officialCallsPercentage }}%) </strong> </h6>
    <hr>
    <h6>Personal Calls: <strong>{{  $personalCalls }} ({{ $personalCallsPercentage }}%) </strong> </h6>
    <hr>
    <h6>Amount Owed (after MWK {{$allowanceAmount}} subsidy): <strong>MWK {{ $amountOwed }}</strong> </h6>
    <hr>
    <h6>Preferred Payment Method: <strong>Bank Remittance</strong> </h6>
</div>

@component('mail::button', ['url' => env('BSC_APP_URL') . '/phonebill-reconciliation' ])
CONFIRM RECEIPT OF FUNDS
@endcomponent




Regards,<br>
UNICEF Platform Team
@endcomponent
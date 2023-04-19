@component('mail::message')
Hello BSC,

{{ $fromName }}'s monthly phone bills for the period  of <strong>{{ $fromDate }}</strong> - <strong>{{ $toDate }}</strong> was at <strong>MWK {{ $amountOwed }}</strong>.

{{ $fromName }} opted to reconcile this bill making a deposit to any of the UNICEF Bank Accounts:

<b>Eco Bank Malawi, Branch: Hillton, Acc: 9021998921, Cur: MWK</b>

On {{ $dateUploadedPayment }}, {{ $fromName }} upload confirmation of this bill. Kindly check and confirm receipt of these funds.

@component('mail::button', ['url' => env('BSC_APP_URL') . '/phonebill-reconciliation' ])
CONFIRM RECEIPT OF FUNDS
@endcomponent

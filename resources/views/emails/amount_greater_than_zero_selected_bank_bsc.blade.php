@component('mail::message')
Hello BSC,


UNICEF Administration  has completed reviewing  monthly phone bills for {{ $fromName }} for the period of {{ $fromDate }} - {{ $toDate }}.

The amount owed by {{ $fromName }} to UNICEF is MWK {{ $reviewedAmount }}.


As per {{ $fromName }}'s  preferred payment method, a remittance will be made to any of the following UNICEF Bank Accounts:

<b>Eco Bank Malawi, Branch: Hillton, Acc: 9021998921, Cur: MWK</b>

Once this remittance is made, you will be informed at which point, you may click the button below to complete the reconciliation. <br>

@component('mail::button', ['url' => env('BSC_APP_URL') . '/phonebill-reconciliation'])
COMPLETE RECONCILIATION
@endcomponent




Regards,<br>
UNICEF Platform Team
@endcomponent

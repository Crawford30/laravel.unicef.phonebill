@component('mail::message')
Hello BSC,


UNICEF Administration  has completed reviewing  monthly phone bills for {{ $fromName }} for the period of {{ $fromDate }} - {{ $toDate }}.

The amount owed by {{ $fromName }} to UNICEF is MWK {{ $reviewedAmount }}.

As per {{ $fromName }}'s  preferred payment method, this amount is to debitted from his/her month-end salary.


Prior to running payroll, Kindly reconcile this position by clicking the button below. <br>


@component('mail::button', ['url' => env('BSC_APP_URL') . '/phonebill-reconciliation'])
COMPLETE RECONCILIATION
@endcomponent





Regards,<br>
UNICEF Platform Team
@endcomponent

@component('mail::message')
Hello {{ $name }},

Your payment for the Phone Bill (MWK {{ $reconcilledAmount }}) for the period of {{ $fromDate }} - {{ $toDate }} has been received by BSC.


The Phone Bill status has therefore been updated to "Reconciled".


Regards,<br>
UNICEF Platform Team
@endcomponent

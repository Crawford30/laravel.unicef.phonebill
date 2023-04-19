@component('mail::message')
Hello {{ $name }},

Your payment for the Phone Bill (MWK {{ $reconcilledAmount }}) for the period of {{ $fromDate }} - {{ $toDate }} has been reconcilled by BSC from payroll.


The Phone Bill status has therefore been updated to "Reconciled".


Regards,<br>
UNICEF Platform Team
@endcomponent

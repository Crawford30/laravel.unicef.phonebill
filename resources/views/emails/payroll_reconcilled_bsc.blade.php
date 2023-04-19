@component('mail::message')
Hello BSC,

{{ $forName }}'s payment of MWK {{ $reconcilledAmount }} for the monthly phone bills for the period of {{ $fromDate }} - {{ $toDate }} has been reconcilled from Payroll by BSC.


The Phone Bill's status has therefore been updated to "Reconciled".


Regards,<br>
UNICEF Platform Team
@endcomponent

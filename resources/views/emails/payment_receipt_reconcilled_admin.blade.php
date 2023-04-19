@component('mail::message')
Hello {{ $toName }},

{{ $forName }}'s payment of MWK {{ $reconcilledAmount }} for the monthly phone bills for the period of {{ $fromDate }} - {{ $toDate }} has been marked as received by BSC.


The Phone Bill's status has therefore been updated to "Reconciled".


Regards,<br>
UNICEF Platform Team
@endcomponent

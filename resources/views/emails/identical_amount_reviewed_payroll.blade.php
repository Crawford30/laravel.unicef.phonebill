@component('mail::message')
Hello {{ $username }},

UNICEF Administration  has reviewed your monthly phone bills for the period of {{ $fromDate }} - {{ $toDate }}.

Your bill remains MWK {{ $identifiedAmount }}.

This bill has been passed onto BSC for reconciliation.


Regards,<br>
UNICEF Platform Team
@endcomponent




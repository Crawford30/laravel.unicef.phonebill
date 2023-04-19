@component('mail::message')
Hello {{ $username }},

UNICEF Administration  has reviewed your monthly phone bills for the period of {{ $fromDate }} - {{ $toDate }}.

Your new bill is MWK {{ $reviewedAmount }}.

This  bill has now been passed onto BSC for reconciliation.

As per your preferred payment method, once this amount has been debited from payroll, you will be advised accordingly.


Regards,<br>
UNICEF Platform Team
@endcomponent

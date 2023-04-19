@component('mail::message')
Hello {{ $username }},

UNICEF Administration has  reviewed  your monthly phone bills for the period of {{ $fromDate }} - {{ $toDate }}.


Your bill is MWK 0.


No further action is required at this point.


Regards,<br>
UNICEF Platform Team
@endcomponent

@component('mail::message')
Hello {{ $reviewedByName }},

You have succesfully reviewed  {{ $whoseCallReviewedName }}'s monthly phone bills for the period of {{ $fromDate }} - {{ $toDate }}.


{{ $whoseCallReviewedName }}'s bill is MWK 0.


No further action is required at this point.




Regards,<br>
UNICEF Platform Team
@endcomponent

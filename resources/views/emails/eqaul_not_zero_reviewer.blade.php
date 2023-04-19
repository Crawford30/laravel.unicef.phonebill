@component('mail::message')
Hello {{ $reviewedByName }},

You have succesfully reviewed  {{ $whoseCallReviewedName }}'s monthly phone bills for the period of {{ $fromDate }} - {{ $toDate }}.


{{ $whoseCallReviewedName }}'s  phone bills  remains MWK {{ $submittedAmount }}.


The new bill will now be passed onto BSC.


Regards,<br>
UNICEF Platform Team
@endcomponent

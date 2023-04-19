@component('mail::message')
Hello {{ $reviewedByName }},

You have succesfully reviewed   {{ $whoseCallReviewedName }}'s monthly phone bills for the period of {{ $fromDate }} - {{ $toDate }}.


{{ $whoseCallReviewedName }}'s new phone bills will be MWK {{ $reviewedAmount }} 

@if (($reviewedAmount  - $submittedAmount) < 0.00)
(a decrease from MWK {{ $submittedAmount }}).
@elseif (($reviewedAmount  - $submittedAmount) > 0.00)
(an increase from MWK {{ $submittedAmount }}).
@else

{{-- (an increase/decrease from MWK {{ $reviewedAmount }}). --}}


No further action is required at this time.




Regards,<br>
UNICEF Platform Team
@endcomponent



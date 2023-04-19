@component('mail::message')
Hello {{ $username }},

{{$reviewedByName }} has completed reviewing {{ $whoseCallReviewedName }}'s monthly phone bills for the period of {{ $fromDate }} - {{ $toDate }}.


{{ $whoseCallReviewedName }}'s new bill will be MWK {{ $reviewedAmount }} 
 @if (($reviewedAmount  - $submittedAmount) < 0)
 (a decrease from MWK {{ $submittedAmount }}).
@elseif (($reviewedAmount  - $submittedAmount) > 0)
(an increase from MWK {{ $submittedAmount }}).
@else

@endif



This new bill will now be passed on to BSC.


Regards,<br>
UNICEF Platform Team
@endcomponent

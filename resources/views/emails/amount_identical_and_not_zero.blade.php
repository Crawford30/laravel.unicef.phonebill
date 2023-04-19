@component('mail::message')
Hello {{ $username }},

{{$reviewedByName }} has completed reviewing {{ $whoseCallReviewedName }}'s monthly phone bills for the period of {{ $fromDate }} - {{ $toDate }}.


{{ $whoseCallReviewedName }}'s  bill remians MWK {{ $reviewedAmount }}.


This new bill will now be passed on to BSC.

Regards,<br>
UNICEF Platform Team
@endcomponent

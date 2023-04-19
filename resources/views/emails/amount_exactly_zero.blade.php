@component('mail::message')
Hello {{ $username }},

{{$reviewedByName }}  has completed reviewing {{ $whoseCallReviewedName }}'s monthly phone bills for the period of {{ $fromDate }} - {{ $toDate }}.


{{ $whoseCallReviewedName }}'s bill is MWK 0.


No further action is required at this time.


Regards,<br>
UNICEF Platform Team
@endcomponent

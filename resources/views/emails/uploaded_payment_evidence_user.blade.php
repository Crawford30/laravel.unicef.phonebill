@component('mail::message')
Hello {{ $username }},

You have succesfully uploaded payment evidence for the Phone Bill  (MWK {{ $reviewedAmount }})  for the period of {{ $fromDate }} - {{ $toDate }}.

BSC has been informed accordingly.


Once  BSC confirms receipt of the funds, the Phone Bill Status will be updated to "Reconciled"


Regards,<br>
UNICEF Platform Team
@endcomponent

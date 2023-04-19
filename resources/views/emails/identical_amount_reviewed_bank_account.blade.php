@component('mail::message')
Hello {{ $username }},

UNICEF Administration  has reviewed your monthly phone bills for the period of {{ $fromDate }} - {{ $toDate }}.

Your bill remains MWK {{ $identifiedAmount }}.

This bill has been passed onto BSC for reconciliation.

As per your preferred payment method, please arrange to make remittance to any of the following UNICEF bank accounts:

<b>Eco Bank Malawi, Branch: Hillton, Acc: 9021998921, Cur: MWK</b>

Upon successful remittance, kindly click the button below to finish reconciliation. <br>

@component('mail::button', ['url' => route("home")])
UPLOAD PAYMENT RECEIPT
@endcomponent

Regards,<br>
UNICEF Platform Team
@endcomponent

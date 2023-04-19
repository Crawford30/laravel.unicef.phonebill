@component('mail::message')
Hello {{ $username }},

Your monthly phone bills for the period of {{ $fromDate }} - {{ $toDate }} stand at MWK {{ $reviewedAmount }}.

As per your preferred payment method, please arrange to make remittance to any of the following UNICEF bank accounts:

<b>Eco Bank Malawi, Branch: Hillton, Acc: 9021998921, Cur: MWK</b>

Upon successful remittance, kindly check the button below to close reconciliation. <br>

@component('mail::button', ['url' => route("home")])
UPLOAD PAYMENT RECEIPT
@endcomponent





Regards,<br>
UNICEF Platform Team
@endcomponent

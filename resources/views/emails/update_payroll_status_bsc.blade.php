@component('mail::message')
Hello BSC,

According to system records, some UNICEF Staff owe money to UNICEF and they elected to have these funds debited via Payroll.

Kindly update the status of these accounts as soon as possible.

@component('mail::button', ['url' => env('BSC_APP_URL') . '/phonebill-reconciliation' ])
UPDATE PAYROLL
@endcomponent




Regards,<br>
UNICEF Platform Team
@endcomponent
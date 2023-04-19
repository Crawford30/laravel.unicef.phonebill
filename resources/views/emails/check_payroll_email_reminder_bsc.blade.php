@component('mail::message')
Hello BSC,

This is a courtesy reminder to check the platform for any debits due to be deducted from staff salaries prior to running Payroll this month.

@component('mail::button', ['url' => env('BSC_APP_URL') . '/phonebill-reconciliation' ])
CHECK PAYROLL
@endcomponent



Regards,<br>
UNICEF Platform Team
@endcomponent
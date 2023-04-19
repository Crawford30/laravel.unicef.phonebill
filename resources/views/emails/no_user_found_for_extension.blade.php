@component('mail::message')
Hello  {{$username}},

The phonebills for the period of   <b>{{ $fromDate }}</b> to <b>{{ $toDate }}</b>  with name,  <b>{{ $name }}</b>  can't be found. Hence, no further action is taken at this point. <br> <br>

Regards,<br>
UNICEF Platform Team
@endcomponent





 @extends('layouts.app')
 @section('content')
     <div class="w-100 p-3">
         <call-log-identification phonebillid="{{ $phoneBillId }}"></call-log-identification>
     </div>
 @stop

@extends('master')

@section('title', 'Tiesnešu statistika')

@section('content')
    

@if (count($referees) > 0)
<table class="table table-striped"> 
	<thead> 
		<tr> 
			<th>Vieta</th> 
			<th>Vārds</th> 
			<th>Uzvārds</th>
			<th>Spēles</th> 
			<th>Sodītie spēlētāji</th>			
			<th>Vidēji sodītie spēlētāji vienā spēlē</th> 
		</tr> 
	</thead> 

	<tbody> 

		<?php $i=0; ?>
		@foreach ($referees as $referee)
		<tr>
			<td>{{ ++$i }}</td>
			<td>{{ $referee->name }}</td>
			<td>{{ $referee->surname }}</td>
			<td>{{ $referee->games }}</td>
			<td>{{ $referee->penalties }}</td>
			<td>{{ number_format($referee->stats,1) }}</td>
		</tr>
		@endforeach
	</tbody> 
</table>
@else
    Nav neviena rezultāta ko parādīt!
@endif 

@stop
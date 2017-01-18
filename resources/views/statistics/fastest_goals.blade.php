@extends('master')

@section('title', 'Ātrāk gūtie vārti')

@section('content')
    

@if (count($time) > 0)
<table class="table table-striped"> 
	<thead> 
		<tr> 
			<th>#</th> 
			<th>Vārds</th> 
			<th>Uzvārds</th>
			<th>Pozīcija</th>
			<th>Komanda</th> 		
			<th>Vārtu gūšanas laiks</th> 
		</tr> 
	</thead> 
	<tbody> 

		<?php $i=0; ?>
		@foreach ($time as $goal)
		<tr>
			<td>{{ ++$i }}</td>
			<td>{{ $goal->player->name }}</td>
			<td>{{ $goal->player->surname }}</td>
			<td>{{ $goal->player->position }}</td>
			<td>{{ $goal->player->team->name }}</td>
			<td>{{ floor($goal->time/60) }}:{{ sprintf("%02d", ($goal->time-floor($goal->time/60)*60))  }}</td>
		</tr>
		@endforeach
	</tbody> 
</table>
@else
    Nav neviena rezultāta ko parādīt!
@endif 

@stop
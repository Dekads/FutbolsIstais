@extends('master')

@section('title', 'Garākās spēles')

@section('content')
    

@if (count($time) > 0)
<table class="table table-striped"> 
	<thead> 
		<tr> 
			<th>#</th> 
			<th>Komanda 1</th> 
			<th>Komanda 2</th>
			<th>Stadions</th> 
			<th>Skatītāju skaits</th>			
			<th>Spēles ilgums</th> 
		</tr> 
	</thead> 
	<tbody> 

		<?php $i=0; ?>
		@foreach ($time as $match)
		<tr>
			<td>{{ ++$i }}</td>
			<td>{{ $match->game->team1->name }}</td>
			<td>{{ $match->game->team2->name }}</td>
			<td>{{ $match->game->location }}</td>
			<td>{{ $match->game->viewers }}</td>
			@if($match->longest < 3600)
			<td>60:00</td>
			@else
			<td>{{ floor($match->longest/60) }}:{{ sprintf("%02d", ($match->longest-floor($match->longest/60)*60))  }}</td>
			@endif
		</tr>
		@endforeach
	</tbody> 
</table>
@else
    Nav neviena rezultāta ko parādīt!
@endif 

@stop
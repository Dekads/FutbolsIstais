@extends('master')

@section('title', 'Spēlētāju saraksts')

@section('content')
    

@if (count($data) > 1)
<table class="table table-striped"> 
	<thead> 
		<tr> 
			<th>Numurs</th> 
			<th>Pozīcija</th>
			<th>Vārds</th> 
			<th>Uzvārds</th>
			<th>Spēles</th> 
			<th>Spēles pamatsastāvā</th> 
			<th>Minūtes</th> 
			<th>Gūtie vārti</th>
			<th>Rezultatīvās piespēles</th> 
			<th>Dzeltenās kartītes</th> 
			<th>Sarkanās kartītes</th> 
		</tr> 
	</thead> 

	<tbody> 


		@foreach ($data as $i => $player)
		<tr>
			<td>{{ $player->number }}</td>
			<td>{{ $player->position }}</td>
			<td>{{ $player->name }}</td>
			<td>{{ $player->surname }}</td>
			<td>{{ $player->games()->count() }}</td>
			<td>{{ $player->games_pamats()->count() }}</td>
			<td>{{ floor($player->played_time[0]->onfield/60) }}:{{ sprintf("%02d", ($player->played_time[0]->onfield-floor($player->played_time[0]->onfield/60)*60))  }}</td>
			<td>{{ $player->goals()->count() }}</td>
			<td>{{ $player->passes()->count() }}</td>
			<td>{{ $player->penalty_yellow()->count() }}</td>
			<td>{{ $player->penalty_red()->count() }}</td>
		</tr>
		@endforeach
	</tbody> 
</table>
@else
    Nav neviena rezultāta ko parādīt!
@endif 

@stop
@extends('master')

@section('title', 'Vārtsargu statistika')

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
			<th>Minūtes</th>			
			<th>Ielaistie vārti</th>
			<th>Vidēji ielaistie vārti vienā spēlē</th> 
			<th>Iesistie vārti</th> 
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
			<td>{{ floor($player->played_time[0]->onfield/60) }}:{{ sprintf("%02d", ($player->played_time[0]->onfield-floor($player->played_time[0]->onfield/60)*60))  }}</td>
			<td>{{ $player->goalie_goals()->count() }}</td>
			<td>{{ number_format($player->goalie_goals()->count()/$player->games()->count(),1) }}</td>
			<td>{{ $player->goals()->count() }}</td>
		</tr>
		@endforeach
	</tbody> 
</table>
@else
    Nav neviena rezultāta ko parādīt!
@endif 

@stop
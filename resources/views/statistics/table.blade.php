@extends('master')

@section('title', 'Tabula')

@section('content')
    

@if (count($data) > 1)
<table class="table table-striped"> 
	<thead> 
		<tr> 
			<th>Vieta</th> 
			<th>Nosaukums</th> 
			<th>Spēles</th>
			<th>Punkti</th> 
			<th>Uzvaras</th> 
			<th>Uzvaras papildlaikā</th>
			<th>Zaudējumi papildlaikā</th> 
			<th>Zaudējumi</th> 
			<th>Iesistie vārti</th>
			<th>Ielaistie vārti</th>  
			<th>Vārtu attiecība</th>  
		</tr> 
	</thead> 

	<tbody> 
		@foreach ($data as $i => $team)
		<tr>
			<td>{{ ++$i }}</td>
			<td>{{ Html::link('/statistics/team_players/'.$team->id, $team->name)}}</td>
			<td>{{ $team->games }}</td>
			<td>{{ $team->points }}</td>
			<td>{{ $team->w }}</td>
			<td>{{ $team->wot }}</td>
			<td>{{ $team->lot }}</td>
			<td>{{ $team->l }}</td>
			<td>{{ $team->goalsplus }}</td>
			<td>{{ $team->goalsminus }}</td>
			<td>{{ $team->goalsplus-$team->goalsminus }}</td>
		</tr>
		@endforeach
	</tbody> 
</table>
@else
    Nav neviena rezultāta ko parādīt!
@endif  

@stop
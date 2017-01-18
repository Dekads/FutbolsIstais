@extends('master')

@section('title', 'Top10 rezultatīvākie spēlētāji')

@section('content')
    

@if (count($data) > 1)
<table class="table table-striped"> 
	<thead> 
		<tr> 
			<th>Vieta</th> 
			<th>Vārds</th> 
			<th>Uzvārds</th>
			<th>Komanda</th> 
			<th>Gūtie vārti</th> 
			<th>Rezutlatīvas piespēles</th> 
		</tr> 
	</thead> 

	<tbody> 


		@foreach ($data as $i => $player)
		<tr>
			<td>{{ ++$i }}</td>
			<td>{{ $player->name }}</td>
			<td>{{ $player->surname }}</td>
			<td>{{ $player->team->name }}</td>
			<td>{{ $player->goals()->count() }}</td>
			<td>{{ $player->passes()->count() }}</td>
		</tr>
		@endforeach
	</tbody> 
</table>
@else
    Nav neviena rezultāta ko parādīt!
@endif 

@stop
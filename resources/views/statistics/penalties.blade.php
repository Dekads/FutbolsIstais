@extends('master')

@section('title', 'Turnīra rupjākie spēlētāji')

@section('content')



@if (count($data) > 1)
<table class="table table-striped"> 
	<thead> 
		<tr> 
			<th>Vieta</th> 
			<th>Vārds</th> 
			<th>Uzvārds</th>
			<th>Komanda</th> 
			<th>Saņemto sodu skaits</th> 
		</tr> 
	</thead> 

	<tbody> 

			
		@foreach ($data as $i => $player)
		<tr>
			<td>{{ ++$i }}</td>
			<td>{{ $player->name }}</td>
			<td>{{ $player->surname }}</td>
			<td>{{ $player->team->name }}</td>
			<td>{{ $player->penalties()->count() }}</td>
		</tr>
		@endforeach
	</tbody> 
</table>
@else
    Nav neviena rezultāta ko parādīt!
@endif
     


@stop
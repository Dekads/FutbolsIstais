@extends('master')

@section('title', 'Top5 labākie vārtsargi')

@section('content')
    

@if (count($data) > 0)
<table class="table table-striped"> 
	<thead> 
		<tr>
			<th>Vieta</th> 
			<th>Vārds</th> 
			<th>Uzvārds</th>
			<th>Komanda</th> 
			<th>Vidēji ielaistie vārti vienā spēlē</th> 
			<th></th> 
		</tr> 
	</thead> 

	<tbody>
	 	<?php $i = 0; ?>
		@foreach ($data as $player)
		<tr>
			<td>{{ ++$i }}</td>
			<td>{{ $player->name }}</td>
			<td>{{ $player->surname }}</td>
			<td>{{ $player->team->name }}</td>
			<td>{{ number_format($player->stats, 1) }}</td>

		</tr>
		@endforeach
	</tbody> 
</table>
@else
    Nav neviena rezultāta ko parādīt!
@endif 

@stop
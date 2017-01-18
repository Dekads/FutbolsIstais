@extends('master')

@section('title', 'Failu ievade')

@section('content')
    

@if(Session::has('success'))
	<div class="alert alert-success" role="alert">
	{{ Session::get('success') }}
	</div>
@endif  

@if(Session::has('error'))
	<div class="alert alert-danger" role="alert">
	{{ Session::get('error') }}
	</div>
@endif   



@if (count($data) > 0)
<table class="table table-striped"> 
	<tbody> 
		@foreach ($data as $id => $file)
		<tr>
			<td>{{ Html::link('/import/'.pathinfo($file)['filename'], pathinfo($file)['filename']) }}</td>
		</tr>
		@endforeach
	</tbody> 
</table>
@else
    Nav neviena faila!
@endif 



@stop

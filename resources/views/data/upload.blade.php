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

{!! Form::open(array('action' => 'UploadController@upload', 'files' => true, 'class' => 'form-inline')) !!}

<div class="row">
<div class="col-lg-6">
<div class="input-group">
<input type="text" class="form-control" id="subfile" placeholder="">
<span class="input-group-btn">
<button class="btn btn-default" onclick="$('#file').click();" type="button">Browse...</button>
</span>
</div><!-- /input-group -->
<button type="submit" class="btn btn-primary">Upload</button>
</div><!-- /.col-lg-6 -->
<input type="file" name="file" style="visibility:hidden;" id="file" />
</div><!-- /.row -->
<div class="row">
<div class="col-lg-6">

</div>
</div>

{!! Form::close() !!}



@stop

@section('scripts')

	<script>
 	$(document).ready(function(){
 		// This is the simple bit of jquery to duplicate the hidden field to subfile
 		$('#file').change(function(){
			$('#subfile').val($(this).val());
		});
 	});
 	</script>

@stop
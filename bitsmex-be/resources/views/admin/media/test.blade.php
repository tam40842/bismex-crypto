<form action="" method="post" enctype="multipart/form-data">
	{!! csrf_field() !!}
	<input type="file" name="file">
	<button type="submit">Submit</button>
</form>
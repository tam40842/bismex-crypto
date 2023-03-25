@extends('admin.app')
@section('title', 'Add Advantages')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>Add Advantages</h3>
	</div>
	@include('admin.includes.boxes.notify')
	<div class="page_content">
		<form action="" method="POST">
			@csrf
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="row">
						<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
							<div class="x_panel">
								<div class="x_title">
									<h2 class="text-info">Add Advantages</h2>
								</div>
								<div class="x_content">
									<table class="table admin_table">
										<tr>
                                            <th>Advantages name</th>
                                            <td>
                                                <input type="text" name="name" class="form-control" placeholder="Advantages name" value="{{ isset($advantage->name) ? $advantage->name : old('name') }}">
                                            </td>
                                        </tr>
										<tr>
                                            <th>Advantages image</th>
                                            <td>
                                                <div class="input-group choose_img_lib post_single_image">
                                                    <span class="input-group-addon btn open_img_lib" gallery="false">Choose image</span>
                                                    <input type="text" name="image" class="form-control inline fill_img_lib" placeholder="Image" value="{{ isset($advantage->image) ? $advantage->image : old('image') }}">
                                                </div>
                                            </td>
                                        </tr>
										<tr>
                                            <th>Advantages content</th>
                                            <td>
                                                @include('admin.includes.boxes.editor', ['name' => 'content', 'content' => isset($advantage->content) ? $advantage->content : old('content')])
                                            </td>
                                        </tr>
										<tr>
                                            <th></th>
                                            <td>
                                                <button class="btn btn-primary" type="submit">Save Advantages</button>
                                            </td>
                                        </tr>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</div>
@include('admin.includes.boxes.media')
@stop
@extends('admin.app')
@section('title', 'Cấu hình menu')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>Cấu hình menu</h3>
	</div>
	@include('admin.includes.boxes.notify')
	<div class="page_content">
        <form action="" method="post">
            @csrf
                <div class="card-header py-3">
                    <div class="d-flex justify-content-between">
                        <select id="menu_select" class="form-control form-control-sm w-25">
                            @foreach($menus as $key => $value)
                            <option value="{{ $value->menu_id }}" {{ $value->menu_id == $menu->menu_id ? 'selected' : '' }}>{{ $value->menu_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 col-12">
                            <div class="card-group" id="accordion" role="tablist" aria-multiselectable="true" data-id="{{ $menu->menu_id }}">
                                <div class="card card-default">
                                    <div class="card-heading" role="tab" id="add_item_from_custom">
                                        <h4 class="card-header h6">
                                            <a class="accordion_custom" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse_custom" aria-expanded="true" aria-controls="collapse_custom">
                                                <b>Liên kết tùy chỉnh</b> <i class="fa fa-chevron-down" aria-hidden="true"></i>
                                            </a>
                                        </h4>
                                    </div>
                                    <div id="collapse_custom" class="card-collapse collapse show" role="tabpanel" aria-labelledby="add_item_from_custom">
                                        <div class="card-body">
                                            <div class="content_add_item_area">
                                                <div class="content_add_item">
                                                    <div class="form-group form_horizontal">
                                                        <label for="add_custom_link_text"><i>Tên liên kết</i></label>
                                                        <div class="form-line">
                                                        <input type="text" id="add_custom_link_text" class="form-control add_menu_item_name form-control-sm" placeholder="Link Text">
                                                        </div>
                                                    </div>
                                                    <div class="form-group form_horizontal">
                                                        <label for="add_custom_url"><i>URL liên kết</i></label>
                                                        <div class="form-line">
                                                        <input type="text" id="add_custom_url" class="form-control form-control-sm add_menu_item_link" placeholder="https://">
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="button" data-type="custom" class="btn btn-dark btn-sm pull-right add_item_button mt-3 mb-3">Thêm vào Menu</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8 col-12">
                            <h3>{{ $menu->menu_name }}</h3>
                            <div id="nestable" class="dd" data-toggle="nestable" data-group="1" data-max-depth="5">
                            @if(!empty($menu_items))
                            {!! $menu_items !!}
                            @else
                            <ol class="dd-list border"></ol>
                            @endif
                            
                            <br>
                            <div class="section-block">
                            <textarea class="form-control" hidden name="menu_output" id="nestable-output"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary btn-sm">{{ __('Save Settings') }}</button>
                </div>
            </form>
	</div>
</div>
@include('admin.includes.boxes.media')
@stop
@push('js')
<script src="{{ asset('contents/admin/js/jquery.nestable.min.js') }}"></script>
<script src="{{ asset('contents/admin/js/popper.min.js') }}"></script>
<script>
    $(document).ready(function() {
        var updateOutput = function(e) {
            var list = e.length ? e : $(e.target), output = list.data('output');
            if (window.JSON) {
                $('#nestable-output').val(window.JSON.stringify(list.nestable('serialize')));
            }
        };
        
        $('#nestable').nestable({group: 1}).on('change', updateOutput);
        updateOutput($('#nestable').data('output', $('#nestable-output')));

        $(document).on('click', '.open_menu_modify', function(e){
            e.preventDefault();
            $(this).closest('.dd-item').find('.modify_menu_item').slideToggle(200);
        });

        $(document).on('click', '.close_menu_modify', function(e){
            e.preventDefault();
            $(this).closest('.modify_menu_item').slideUp(200);
        });

        $(document).on('click', '.remove_menu_item', function(e){
			e.preventDefault();
			var remove_confirm = confirm("Are you sure you want to delete this item?");
			if(remove_confirm == true){
				$(this).closest('.dd-item').remove();
                updateOutput($('#nestable').data('output', $('#nestable-output')));
			}
        });
        $(document).on('click', '.add_item_button', function(e){
			e.preventDefault();
			var add_item_type = $(this).attr('data-type');
			if(add_item_type == 'custom'){
				var add_item_name = $(this).closest('.content_add_item_area').find('.add_menu_item_name').val();
				var add_item_link = $(this).closest('.content_add_item_area').find('.add_menu_item_link').val();
				var add_menu_id = $(this).closest('.card-group').attr('data-id');
				if(add_item_name.trim() != ''){
					if(add_item_link.trim() != ''){
						$(this).closest('.content_add_item_area').find('.add_menu_item_name').val('');
						$(this).closest('.content_add_item_area').find('.add_menu_item_link').val('');
						add_item_to_menu(add_menu_id, add_item_name, add_item_link);
					} else {
						$(this).closest('.content_add_item_area').find('.add_menu_item_link').focus();
						$(this).closest('.content_add_item_area').find('.add_menu_item_link').closest('form-group').addClass('has-error');
					}
					
				}else{
					$(this).closest('.content_add_item_area').find('.add_menu_item_name').focus();
					$(this).closest('.content_add_item_area').find('.add_menu_item_name').closest('form-group').addClass('has-error');
				}
				
			}
		});

		function add_item_to_menu(menu_id, menu_item_name, menu_item_link){
			var _token = $('meta[name="csrf-token"]').attr('content');
			$.ajax({
				url: '{{ url('/admin/settings/menu/add/menu-item') }}',
				type: 'POST',
				data: {_token: _token, menu_id: menu_id, menu_item_name: menu_item_name, menu_item_link: menu_item_link},
			}).done(function(data){
				$('#nestable>ol.dd-list').append(data);
				updateOutput($('#nestable').data('output', $('#nestable-output')));
			}).fail(function(){
				alert("Error");
			});
        }
        
        $(document).on('change', '#menu_select', function() {
            var menu_id = $(this).find('option:selected').val();
            window.location.href = '/admin/settings/menu/' + menu_id;
        })
    });
</script>
@endpush
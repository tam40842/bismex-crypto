@if(!is_null($currencies))
@foreach($currencies as $value)
<tr>
	<td><input type="checkbox" value="{!! $value->id !!}" class="flat check_item" name="table_records"></td>
	<td>
		@if(!is_null($value->logo))
		<img style="height:31px; width: auto;" src="{{ $value->logo }}" alt="{!! $value->name !!}">
		@else
		<i class="{{ $value->icon }}" style="color:{{ $value->color }};"></i>
		@endif
	</td>
	<td>
		<div class="table_title">
			<a href="{!! route('admin.currencies.edit', ['id' => $value->id]) !!}" title="Edit currency">{!! $value->name !!}</a>
		</div>
	</td>
	<td>{{ strtoupper($value->symbol) }}</td>
	{{-- <td>{{ number_format($value->balance, 8) }}</td> --}}
	<td>{{ number_format($value->deposit_fee, 2) }}</td>
	<td>{{ number_format($value->withdraw_fee, 2) }}</td>
	<td>
		@if($value->actived == 1)
		<div class="badge badge-success">Actived</div>
		@else
		<div class="badge badge-danger">Not Actived</div>
		@endif
	</td>
</tr>
@endforeach
@else
<tr>
	<td colspan="8">Items not found.</td>
</tr>
@endif
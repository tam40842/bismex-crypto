@php
	use Illuminate\Support\Str;
@endphp
@if(count($packages) > 0)
@foreach($packages as $value)
<tr>
	<td><input type="checkbox" value="{!! $value->id !!}" class="flat check_item" name="table_records"></td>
	<td>
		<div class="table_title">
			<a href="{!! url('/admin/users/edit/' . $value->userid) !!}">{{ $value->username }}</a>
		</div>
	</td>
	<td>
		<div class="table_title">
			<a href="{{ route('admin.autotrade.edit', ['id' => $value->package_id]) }}" target="_blank" title="Edit package">{{ $value->package_id }}</a>
		</div>
	</td>
	<td>{{ Str::upper($value->start_date) }}</td>
	<td>{{ Str::upper($value->end_date) }}</td>
	<td style="text-align: right;">
		<span style="font-size: 14px;" class="badge badge-success">+{{ number_format($value->received,2) }}</span>
	</td>
	<td>{{ number_format($value->borrow_amount,2) }}</td>
	<td>
		@if($value->withdraw_status == 1)
			<span style="font-size: 14px;" class="badge badge-danger">Yêu cầu rút</span>
		@else
			<span style="font-size: 14px;">---</span>
		@endif
	</td>
	<td>
		@if($value->status == 0)
			<span style="font-size: 14px;" class="badge badge-danger">Bị hủy</span>
		@elseif($value->status == 1)
			<span style="font-size: 14px;" class="badge badge-success">Đang chạy</span>
		@else
			<span style="font-size: 14px;" class="badge badge-success">Hoàn tất</span>
		@endif
	</td>
</tr>
@endforeach
@else
<tr>
	<td colspan="9" class="text-center">Chưa có giao dịch nạp nào.</td>
</tr>
@endif
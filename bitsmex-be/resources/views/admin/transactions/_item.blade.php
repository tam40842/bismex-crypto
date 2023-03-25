@php
	use Illuminate\Support\Str;
@endphp
@if(count($transactions) > 0)
@foreach($transactions as $value)
<tr>
	<td><input type="checkbox" value="{!! $value->id !!}" class="flat check_item" name="table_records"></td>
	<td>{{ $value->username }}</td>
	<td>{{ Str::upper($value->wallet_type) }}</td>
	<td>{{ Str::upper($value->type) }}</td>
	<td>{{ number_format($value->original,2) }}</td>
	<td style="text-align: right;">
		@if($value->change < 0)
			<span style="font-size: 14px;" class="badge badge-danger">{{ number_format($value->change,2) }}</span>
		@else
			<span style="font-size: 14px;" class="badge badge-success">+{{ number_format($value->change,2) }}</span>
		@endif
	</td>
	<td>{{ number_format($value->balance,2) }}</td>
	<td>{{ $value->message }}</td>
	<td>{{ $value->created_at }}</td>
</tr>
@endforeach
@else
<tr>
	<td colspan="9" class="text-center">Chưa có giao dịch nạp nào.</td>
</tr>
@endif
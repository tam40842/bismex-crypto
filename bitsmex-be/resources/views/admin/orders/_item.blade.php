@if(count($orders) > 0)
@foreach($orders as $value)
<tr>
	<td><input type="checkbox" value="{!! $value->id !!}" class="flat check_item" name="table_records"></td>
	<td>
		<div class="table_title text-uppercase">
			<small>
				<a href="{{ route('admin.orders.edit', ['id' => $value->id]) }}" title="Edit order">{{ $value->round }}</a>
			</small>
		</div>
	</td>
	
	<td>
		<div class="table_title">
			<small>
				<a href="{{ route('admin.users.edit', ['id' => $value->userid]) }}" title="Edit" target="_blank"><i class="fa fa-user fa-fw"></i>{{ $value->username }}</a>
			</small>
			{{ $value->admin_setup == 1 ? '(User setup)' : '' }}
		</div>
	</td>
	<td>{!! $value->action == 'BUY' ? '<strong class="badge badge-success">BUY</strong>' : '<strong class="badge badge-danger">SELL</strong>' !!}</td>
	<td>{{ $value->market_name }}</td>
	<td>${{ number_format($value->amount) }}</td>
	<td>{{ $value->profit_percent }}%</td>
	<td>{{ ($value->status == 1) ? '$'.($value->amount + ($value->amount * $value->profit_percent / 100)) : '---' }}</td>
	<td>{!! $order_status[$value->status] !!}</td>
	<td>{{ $value->created_at }}</td>
</tr>
@endforeach
@else
<tr>
	<td colspan="13" class="text-center">Items not found.</td>
</tr>
@endif
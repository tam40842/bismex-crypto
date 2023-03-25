@extends('admin.app')
@section('title', 'Tạo lệnh trừ tiền')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>Tạo lệnh trừ tiền</h3>
	</div>
	@include('admin.includes.boxes.notify')
	<div class="page_content">
		<form action="{{ route('admin.withdraw.add') }}" method="POST" class="withdraw-form">
			@csrf
			<div class="x_panel">
                <div class="x_title">
                    <h2 class="text-info"><span class="text-uppercase">Thông tin người trừ</h2>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="col-md-8 col-12">
                            <table class="table admin_table">
                                <tr>
                                    <th>Username trừ tiền</th>
                                    <td>
                                        <input type="text" name="recipient" class="form-control w-100" placeholder="Nhập username hoặc email" required value="{{ old('recipient') }}">
                                    </td>
                                </tr>
                                <tr>
                                    <th>Số lượng muốn trừ</th>
                                    <td>
                                        <input type="number" name="amount" step="any" class="form-control w-100" placeholder="Số lượng muốn trừ" required value="{{ old('recipient') }}">
                                    </td>
                                </tr>
                                <tr>
                                    <th></th>
                                    <td>
                                        <button type="button" class="btn btn-primary withdraw_btn">Xác nhận</button>
                                    </td>
                                </tr>
                            </table>
                            <div class="modal" id="withdraw_confirmation">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Xác nhận trừ tiền</h4>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <p>
                                                Bạn đang thực hiện trừ <strong id="amount">0</strong> <strong id="currency"></strong> của <strong id="recipient"></strong>. Xin vui lòng kiểm tra chính xác trước khi thực hiện.
                                            </p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success">Xác nhận</button>
                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Hủy</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
		</form>
	</div>
</div>
@stop
@push('js')
<script>
    $(document).on('click', '.withdraw_btn', function() {
        var o = $(this);
        var withdraw_form = o.closest('form');
        var recipient = withdraw_form.find('[name="recipient"]');
        var currency = withdraw_form.find('[name="currency"]');
        var amount = withdraw_form.find('[name="amount"]');
        if(recipient.val() == '') {
            recipient.addClass('is-invalid').removeClass('is-valid');
            return false;
        } else {
            recipient.addClass('is-valid').removeClass('is-invalid');
            $('#withdraw_confirmation').find('#recipient').html(recipient.val());
        }
        if(currency.val() == '') {
            currency.addClass('is-invalid').removeClass('is-valid');
            return false;
        } else {
            currency.addClass('is-valid').removeClass('is-invalid');
            $('#withdraw_confirmation').find('#currency').html(currency.val());
        }
        if(amount.val() == '') {
            amount.addClass('is-invalid').removeClass('is-valid');
            return false;
        } else {
            amount.addClass('is-valid').removeClass('is-invalid');
            $('#withdraw_confirmation').find('#amount').html(amount.val());
        }
        $('#withdraw_confirmation').modal('show');
        return false;
    });
</script>
@endpush
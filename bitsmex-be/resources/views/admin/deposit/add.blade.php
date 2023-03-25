@extends('admin.app')
@section('title', 'Tạo lệnh nạp tiền')
@section('content')
<div class="content_wrapper">
	<div class="page_title">
		<h3>Tạo lệnh nạp tiền</h3>
	</div>
	@include('admin.includes.boxes.notify')
	<div class="page_content">
		<form action="{{ route('admin.deposit.add') }}" method="POST" class="deposit-form">
			@csrf
			<div class="x_panel">
                <div class="x_title">
                    <h2 class="text-info"><span class="text-uppercase">Thông tin người nạp</h2>
                </div>
                <div class="x_content">
                    <div class="row">
                        <div class="col-md-8 col-12">
                            <table class="table admin_table">
                                <tr>
                                    <th>Username người nhận</th>
                                    <td>
                                        <input type="text" name="recipient" class="form-control w-100" placeholder="Nhập username hoặc email người nhận" required>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Số lượng</th>
                                    <td>
                                        <input type="number" name="amount" step="any" class="form-control w-100" placeholder="Số lượng muốn nạp" required>
                                    </td>
                                </tr>
                                <tr>
                                    <th></th>
                                    <td>
                                        <button type="button" class="btn btn-primary deposit_btn">Xác nhận</button>
                                    </td>
                                </tr>
                            </table>
                            <div class="modal" id="deposit_confirmation">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Xác nhận nạp tiền</h4>
                                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                                        </div>
                                        <div class="modal-body">
                                            <p>
                                                Bạn đang thực hiện nạp <strong id="amount">0</strong> <strong id="currency"></strong> cho <strong id="recipient"></strong>. Xin vui lòng kiểm tra chính xác trước khi thực hiện.
                                            </p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-success btn-submit">Xác nhận</button>
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
    $(document).on('click', '.deposit_btn', function() {
        var o = $(this);
        var deposit_form = o.closest('form');
        var recipient = deposit_form.find('[name="recipient"]');
        var currency = deposit_form.find('[name="currency"]');
        var amount = deposit_form.find('[name="amount"]');
        if(recipient.val() == '') {
            recipient.addClass('is-invalid').removeClass('is-valid');
            return false;
        } else {
            recipient.addClass('is-valid').removeClass('is-invalid');
            $('#deposit_confirmation').find('#recipient').html(recipient.val());
        }
        if(currency.val() == '') {
            currency.addClass('is-invalid').removeClass('is-valid');
            return false;
        } else {
            currency.addClass('is-valid').removeClass('is-invalid');
            $('#deposit_confirmation').find('#currency').html(currency.val());
        }
        if(amount.val() == '') {
            amount.addClass('is-invalid').removeClass('is-valid');
            return false;
        } else {
            amount.addClass('is-valid').removeClass('is-invalid');
            $('#deposit_confirmation').find('#amount').html(amount.val());
        }
        $('#deposit_confirmation').modal('show');
        return false;
    });
    $(document).ready(function(){
        $('.btn-submit').on('click',function(){
            $(this).prop('disabled', true);
            var o = $(this);
            var deposit_form = o.closest('form');
            deposit_form.submit();
        })
    })
</script>
@endpush
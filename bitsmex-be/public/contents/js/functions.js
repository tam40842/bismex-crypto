function number_format(number, decimals, dec_point, thousands_sep){
    var n = number, prec = decimals;
    
    var toFixedFix = function (n,prec) {
        var k = Math.pow(10,prec);
        return (Math.round(n*k)/k).toString();
    };
    
    n = !isFinite(+n) ? 0 : +n;
    prec = !isFinite(+prec) ? 0 : Math.abs(prec);
    var sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep;
    var dec = (typeof dec_point === 'undefined') ? '.' : dec_point;
    
    var s = (prec > 0) ? toFixedFix(n, prec) : toFixedFix(Math.round(n), prec); //fix for IE parseFloat(0.55).toFixed(0) = 0;
    
    var abs = toFixedFix(Math.abs(n), prec);
    var _, i;
    
    if (abs >= 1000) {
        _ = abs.split(/\D/);
        i = _[0].length % 3 || 3;
    
        _[0] = s.slice(0,i + (n < 0)) +
              _[0].slice(i).replace(/(\d{3})/g, sep+'$1');
        s = _.join(dec);
    } else {
        s = s.replace('.', dec);
    }
    
    var decPos = s.indexOf(dec);
    if (prec >= 1 && decPos !== -1 && (s.length-decPos-1) < prec) {
        s += new Array(prec-(s.length-decPos-1)).join(0)+'0';
    }
    else if (prec >= 1 && decPos === -1) {
        s += dec+new Array(prec).join(0)+'0';
    }
    return s;
}

function upload_html_4(input, i) {
    var reader = new FileReader();
    reader.onload = function(){
        var dataURL = reader.result;
        $("form").find("img").attr("src", dataURL);
    };
    reader.readAsDataURL(input.files[0]);
}

function v_modal(modal_id, title, size = "") {
	str = `<div id="modal-${modal_id}" class="modal fade" role="dialog">
                <div class="modal-dialog ${size}">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title">${title}</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <div class="modal-body">
                            <i class="fa fa-spinner fa-spin"></i> Loading...
                        </div>
                    </div>
                </div>
            </div>`;

	$('body').append(str);
	$("#modal-"+modal_id).modal({backdrop: 'static', keyboard: false});
}

function v_modal_insert(modal_id, title, result) {
	if(title) {
		$("#modal-"+modal_id).find(".modal-title").html(title);
	}
    $("#modal-"+modal_id).find(".modal-body").html(result);
}

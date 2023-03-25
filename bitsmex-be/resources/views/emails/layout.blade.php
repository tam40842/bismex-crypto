@php
  use App\Http\Controllers\Vuta\Vuta;
  $settings = Vuta::get_settings(['title_website', 'site_logo']);
@endphp

<div style="float:left;border-collapse:collapse;background-color:#ebedef;width:100%">
	<div class="adM">
</div><div align="center" style="width:100%;padding:10px" class="adM">&nbsp;</div><div class="adM">

</div><div style="background-image:linear-gradient(to right,#0158bb 0%,#013a78 100%);border:1px solid #c8cace;border-radius:4px;margin:auto;width:600px"><div class="adM">
</div><div align="center" style="background-color:#ffffff;width:100%"><img src="https://core.bitsmex.net/storage/uploads/system/2021-09-19/photo-2021-09-19-125614_size_1280x255.jpeg" style="margin:20px;width:200px;height:auto" class="CToWUd a6T" tabindex="0"><div class="a6S" dir="ltr" style="opacity: 0.01; left: 561.5px; top: 123px;"><div id=":1vq" class="T-I J-J5-Ji aQv T-I-ax7 L3 a5q" role="button" tabindex="0" aria-label="Tải xuống tệp đính kèm " data-tooltip-class="a1V" data-tooltip="Tải xuống"><div class="aSK J-J5-Ji aYr"></div></div></div></div>

<div style="width:100%;float:left">
<table border="0px" cellpadding="0" cellspacing="0" width="100%">
	<tbody>
		<tr style="background:#ffffff">
			<td width="30">&nbsp;</td>
			<td>
				@yield('content')
			<p style="text-align:justify">&nbsp;</p>

			<p style="color:#303030;font:15px/20px Helvetica,Arial,sans-serif;text-align:justify"><b>Kind regards,</b><br>
			<br>
			<b>The {{ env('APP_NAME') }} team.</b><br>
			&nbsp;</p>
			</td>
			<td width="30">&nbsp;</td>
		</tr>
	</tbody>
</table>
</div>
</div>
</div>
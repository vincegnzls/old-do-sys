
{$var.html_header}

<body bgcolor="#D8D0C8">
<form {$form.attributes}>
<!--------------------- 外枠開始 ---------------------->
<table border="0" width="100%" height="90%" class="M_table">

	<tr align="center" height="60">
		<td width="100%" colspan="2" valign="top">
			<!-- 画面タイトル開始 --> {$var.page_header} <!-- 画面タイトル終了 -->
		</td>
	</tr>

	<tr align="center">
	

		<!---------------------- 画面表示開始 --------------------->
		<td valign="top">
		
			<table border="0">
				<tr>
					<td>

<!---------------------- 画面表示1開始 --------------------->
{* 登録・変更完了メッセージ出力 *}
<span style="color: #0000ff; font-weight: bold; line-height: 130%;">
{if $var.comp_msg != null}<li>{$var.comp_msg}<br>{/if}
</span>
{* エラーメッセージ出力 *} 
<span style="color: #ff0000; font-weight: bold; line-height: 130%;">
{if $var.error_fax_msg != null}<li>{$var.error_fax_msg}<br>{/if}
{if $var.error_tel_msg != null}<li>{$var.error_tel_msg}<br>{/if}</span><br>
<font size="+0.5"><b>※�，�FAX番号、�△謀渡暖峭罎鯑�力してください</b></font><br>
<font size="+0.5"><b>※��〜�┐縫灰瓮鵐箸鯑�力してください</b></font>
<table class='List_Table' border='0' width='740' height='1085' background="../../../image/order.png">
<tr>
	<td valign="bottom" height='135'>
		<table width='550' align="right">
		<tr><td>
		<font color='#ff0000'>
		�� {$form.o_memo1.html}<br>
		</font>
		</td></tr>
		</table>
	</td>
</tr>
<tr>
	<td valign="top">
		<table width='680' align="right">
		<tr><td>
		<font color='#ff0000'>
		�� {$form.o_memo2.html}<br>
		</font>
		</td></tr>
		</table>
	</td>
</tr>
<tr>
	<td valign="top" height='370'>
		<table width='450'>
		<tr><td>
		<font color='#ff0000'>
		�� {$form.o_memo3.html}<br>
		�� {$form.o_memo4.html}<br>
		�� {$form.o_memo5.html}<br>
		�� {$form.o_memo6.html}<br>
		�� {$form.o_memo7.html}<br>
		�� {$form.o_memo8.html}<br>
		</font>
		</td></tr>
		</table>
	</td>
</tr>
</table>
	</td>
</tr>

<table width='740'>
	<tr>
		<td align="right">{$form.new_button.html}　　{$form.order_button.html}</td>
	</tr>
</table>
<!--******************** 画面表示1終了 *******************-->

					<br>
					</td>
				</tr>
					</td>
				</tr>
			</table>
		</td>
<!--******************** 画面表示終了 *******************-->

	</tr>
</table>
<!--******************* 外枠終了 ********************-->

{$var.html_footer}
	


	

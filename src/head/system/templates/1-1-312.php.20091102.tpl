
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
<span style="color: #ff0000; font-weight: bold; line-height: 130%;">
{if $form.d_memo3.error != null}<li>{$form.d_memo3.error}<br>{/if}
</span>
<span style="color: #0000ff; font-weight: bold; line-height: 130%;">
{if $var.comp_msg != null}<li>{$var.comp_msg}<br>{/if}
</span>
<font size="+0.5"><b>※�，暴蚕蠅鯑�力してください<br>※�△�TEL・FAXを入力して下さい<br>※��にメモを入力してください</b></font>
<table class='List_Table' border='1' width='720' height="450">
	<tr>
	<td class='Value' valign='middle'>
    <table align="center" valign="middle" border="0" width="740" height="350" background="../../../image/delivery.png">
	<tr>
	<td valign="bottom" height="110" width="720" align="right" colspan="2"><font color='#ff0000'>
	�　�{$form.d_memo1.html}<br>
	�◆�{$form.d_memo2.html}<br>
	</font>
	</td>
	</tr>
	<tr><td><br><br><br><br><br><br><br><br><br><br><br><br><br><br></td></tr>
    <td><font color='#ff0000'>　��</font></td>
	<tr>
		<td width="365" valign="top" align="center">
		{$form.d_memo3.html}
		</td>
	</tr>
		</td>
	</tr>
</table>
</table>
<table width='740'>
	<tr>
		<td align="right">{$form.new_button.html}　　{$form.deli_button.html}</td>
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
	


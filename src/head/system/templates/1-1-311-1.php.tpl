
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
<table width='750'>
	<tr>
		<td align="left"><font size="+0.5"><b>印字パターンを登録しました。</b></font>　　　　{$form.back_button.html}　　　　{$form.preview_button.html}</td>
	</tr>
	<tr>
		<td><hr></td>
	</tr>
</table>
<font size="+0.5"><b>パターン名：パターン１<br>※�　銑Δ房�社情報を入力してください<br>※�Г縫灰瓮鵐箸鮴瀋蠅靴堂爾気ぁ�（コメントは全パターンで共通です。）</b></font>
<table class='List_Table' border='1' width='720' height="360">
	<tr>
		<td class='Value' valign='middle'>
	   		<table align="center" valign="middle" border="0" width="740" height="350" background="../../../image/saling.png">
			<tr>
				<td valign="top" width="520" height="1" align="right" colspan="3">
					<img src="../../../image/company-rogo_small.png"><br>
				</td>
			</tr>
			<tr>
				<td valign="top" width="520" align="right" colspan="2">
				</td>
				<td valign="top" align="left" height="190">
					<font color='#ff0000' size = '1'>��</font>　<font size = '1'>{$var.s_memo1}</font><br>
					<font color='#ff0000' size = '1'>��</font>　<font size = '1'>{$var.s_memo2}</font><br>
					<font color='#ff0000' size = '1'>��</font>　<font size = '1'>{$var.s_memo3}</font><br>
					<font color='#ff0000' size = '1'>��</font>　<font size = '1'>{$var.s_memo4}</font><br>
					<font color='#ff0000' size = '1'>��</font>　<font size = '1'>{$var.s_memo5}</font><br>
					<font color='#ff0000' size = '1'>��</font>　<font size = '1'>{$var.s_memo6}</font><br>
				</font>                            
				</td>
			</tr>
			<tr>
				<td rowspan="2">
					<font color='#ff0000'>　��</font>
				</td>
				<td height="10">
				</td>
			</tr>
			<tr>
				<td width="300" align="left">
					{$form.s_memo7.html}
				</td>
			</tr>
		</td>
	</tr>
</table>
<!--******************** 画面表示1終了 *******************-->

</table>
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
	


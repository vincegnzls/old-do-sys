
{$var.html_header}

<body bgcolor="#D8D0C8">
<form name="dateForm" method="post">

{*------------------- 外枠開始 --------------------*}
<table width="100%" height="90%" class="M_table">

	<tr align="center" height="60">
		<td width="100%" colspan="2" valign="top">
			{* 画面タイトル開始 *} {$var.page_header} {* 画面タイトル終了 *}
		</td>
	</tr>

	<tr align="center">
	

		{*-------------------- 画面表示開始 -------------------*}
		<td valign="top">
		
			<table>
				<tr>
					<td>

{*-------------------- 画面表示1開始 -------------------*}
<table class="Data_Table" border="1" width="730">
<col width="100" style="font-weight: bold;">
<col width="*">
<col width="100" style="font-weight: bold;">
<col width="*">
	<tr>
		<td class="Title_Gray">出力形式</td>
		<td class="Value">{$form.f_r_output2.html}</td>
		<td class="Title_Gray">出力範囲</td>
		<td class="Value">{$form.f_radio12.html}</td>
	</tr>
	<tr>
		<td class="Title_Gray">取引年月</td>
		<td class="Value">{$form.f_date_d1.html}</td>
		<td class="Title_Gray">業種</td>
		<td class="Value">{$form.form_btype_1.html}</td>
	</tr>
	<tr>
		<td class="Title_Gray">得意先コード</td>
		<td class="Value">{$form.f_code_a1.html}</td>
		<td class="Title_Gray">得意先名</td>
		<td class="Value">{$form.f_text15.html}</td>
	</tr>
	<tr>
		<td class="Title_Gray">対象拠点</td>
		<td class="Value" colspan="3">{$form.form_cshop_1.html}</td>
	</tr>
	<tr>
		<td class="Title_Gray">出力項目</td>
		<td class="Value" colspan="3">{$form.f_check1.html}</td>
	</tr>
</table>

<table width="650">
	<tr>
		<td align="right">{$form.hyouji.html}　　{$form.kuria.html}</td>
	</tr>
</table>
<br>
{********************* 画面表示1終了 ********************}

					</td>
				</tr>
				<tr>
					<td>

{*-------------------- 画面表示2開始 -------------------*}
<font size="+0.5" color="#555555"><b>【対象拠点：　拠点1】</b></font>
<table class="List_Table" border="1" width="100%">
	<tr align="center" style="font-weight: bold;">
		<td class="Title_Gray">No.</td>
		<td class="Title_Gray">業種名</b></td>
		<td class="Title_Gray">得意先名</b></td>
		<td class="Title_Gray"></td>
		<td class="Title_Gray">2005年1月</td>
		<td class="Title_Gray">2005年2月</td>
		<td class="Title_Gray">2005年3月</td>
		<td class="Title_Gray">2005年4月</td>
		<td class="Title_Gray">2005年5月</td>
		<td class="Title_Gray">2005年6月</td>
		<td class="Title_Gray">2005年7月</td>
		<td class="Title_Gray">2005年8月</td>
		<td class="Title_Gray">2005年9月</td>
		<td class="Title_Gray">2005年10月</td>
		<td class="Title_Gray">2005年11月</td>
		<td class="Title_Gray">2005年12月</td>
		<td class="Title_Gray">月合計</td>
		<td class="Title_Gray">月平均</td>
	</tr>

	<tr class="Result1">
		<td align="right" rowspan="4">1</td>
		<td align="left" rowspan="4">業種A</td>
		<td align="left">得意先1</td>
		<td align="left">売上金額</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">120,000</td>
		<td align="right">10,000</td>
	</tr>

	<tr class="Result2">
		<td align="left">得意先2</td>
		<td align="left">売上金額</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">120,000</td>
		<td align="right">10,000</td>
	</tr>

	<tr class="Result1">
		<td align="left">得意先3</td>
		<td align="left">売上金額</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">120,000</td>
		<td align="right">10,000</td>
	</tr>

	<tr class="Result3">
		<td align="center" colspan="2"><b>小計</b></td>
		<td align="right"><b>30,000</td>
		<td align="right"><b>30,000</td>
		<td align="right"><b>30,000</td>
		<td align="right"><b>30,000</td>
		<td align="right"><b>30,000</td>
		<td align="right"><b>30,000</td>
		<td align="right"><b>30,000</td>
		<td align="right"><b>30,000</td>
		<td align="right"><b>30,000</td>
		<td align="right"><b>30,000</td>
		<td align="right"><b>30,000</td>
		<td align="right"><b>30,000</td>
		<td align="right"><b>360,000</td>
		<td align="right"><b>30,000</td>
	</tr>

	<tr class="Result1">
		<td align="right" rowspan="4">2</td>
		<td align="left" rowspan="4">業種B</td>
		<td align="left">得意先4</td>
		<td align="left">売上金額</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">120,000</td>
		<td align="right">10,000</td>
	</tr>

	<tr class="Result2">
		<td align="left">得意先5</td>
		<td align="left">売上金額</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">120,000</td>
		<td align="right">10,000</td>
	</tr>

	<tr class="Result1">
		<td align="left">得意先6</td>
		<td align="left">売上金額</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">10,000</td>
		<td align="right">120,000</td>
		<td align="right">10,000</td>
	</tr>

	<tr class="Result3">
		<td align="center" colspan="2"><b>小計</b></td>
		<td align="right"><b>30,000</td>
		<td align="right"><b>30,000</td>
		<td align="right"><b>30,000</td>
		<td align="right"><b>30,000</td>
		<td align="right"><b>30,000</td>
		<td align="right"><b>30,000</td>
		<td align="right"><b>30,000</td>
		<td align="right"><b>30,000</td>
		<td align="right"><b>30,000</td>
		<td align="right"><b>30,000</td>
		<td align="right"><b>30,000</td>
		<td align="right"><b>30,000</td>
		<td align="right"><b>360,000</td>
		<td align="right"><b>30,000</td>
	</tr>

	<tr class="Result4">
		<td align="left"><b>合計</b></td>
		<td align="left"><b>2業種</b></td>
		<td align="right" colspan="2"><b>　</b></td>
		<td align="right"><b>60,000</b></td>
		<td align="right"><b>60,000</b></td>
		<td align="right"><b>60,000</b></td>
		<td align="right"><b>60,000</b></td>
		<td align="right"><b>60,000</b></td>
		<td align="right"><b>60,000</b></td>
		<td align="right"><b>60,000</b></td>
		<td align="right"><b>60,000</b></td>
		<td align="right"><b>60,000</b></td>
		<td align="right"><b>60,000</b></td>
		<td align="right"><b>60,000</b></td>
		<td align="right"><b>60,000</b></td>
		<td align="right"><b>720,000</b></td>
		<td align="right"><b>60,000</b></td>
	</tr>

</table>

{********************* 画面表示2終了 ********************}


					</td>
				</tr>
			</table>
		</td>
		{********************* 画面表示終了 ********************}

	</tr>
</table>
{******************** 外枠終了 *********************}

{$var.html_footer}
	


<?php /* Smarty version 2.6.9, created on 2006-02-01 20:53:55
         compiled from 1-6-205.php.tpl */ ?>

<?php echo $this->_tpl_vars['var']['html_header']; ?>


<body background="../../../image/back_pink.png">
<form name="dateForm" method="post">

<!--------------------- 外枠開始 ---------------------->
<table border=0 width="100%" height="90%">

	<tr align="center" height="60">
		<td width="100%" colspan="2" valign="top">
			<!-- 画面タイトル開始 --> <?php echo $this->_tpl_vars['var']['page_header']; ?>
 <!-- 画面タイトル終了 -->
		</td>
	</tr>

	<tr align="left">
		<td width="18%" valign="top" lowspan="2">
			<!-- メニュー開始 --> <?php echo $this->_tpl_vars['var']['page_menu']; ?>
 <!-- メニュー終了 -->
		</td>

		<!---------------------- 画面表示開始 --------------------->
		<td valign="top">
		
			<table border=0  width="100%">
				<tr>
					<td>

<!---------------------- 画面表示1開始 --------------------->
<table  class="Data_Table" border="1" width="650" >
	<tr>
		<td class="Title_Pink"width="110"><b>取扱年月<font color="#ff0000">※</font></b></td>
		<td class="Value" colspan="3"><?php echo $this->_tpl_vars['form']['f_date_c1']['html']; ?>
</td>
	</tr>

	<tr>
		<td class="Title_Pink" width="100"><b>担当者コード</b></td>
		<td class="Value"><?php echo $this->_tpl_vars['form']['f_text4']['html']; ?>
</td>
		<td class="Title_Pink" width="110"><b>代行者名</b></td>
		<td class="Value" width="220"><?php echo $this->_tpl_vars['form']['f_text10']['html']; ?>
</td>
	</tr>

</table>
<table width='650'>
	<tr>
		<td align="left">
			<font color="#ff0000"><b>※は必須入力です</b></font>
		</td>
		<td align='right'>
			<?php echo $this->_tpl_vars['form']['button']['hyouji']['html']; ?>
　　<?php echo $this->_tpl_vars['form']['button']['kuria']['html']; ?>

		</td>
	</tr>
</table>
<!--******************** 画面表示1終了 *******************-->

					<br>
					</td>
				</tr>


				<tr>
					<td>

<!---------------------- 画面表示2開始 --------------------->
<?php echo $this->_tpl_vars['var']['html_page']; ?>


<font size="+0.5" color="#555555"><b>【取引年月：2006-01】</b></font>
<table class="List_Table" border="1" width="100%">
	<tr align="center">
		<td class="Title_Pink" width=""><b>No</b></td>
		<td class="Title_Pink" width=""><b>代行者名</b></td>
		<td class="Title_Pink" width=""><b>得意先</b></td>
		<td class="Title_Pink" width=""><b>代行金額（原価）</b></td>
	</tr>

	<tr class="Result1">
		<td align="right" rowspan="7">1</td>
		<td align="left" rowspan="7">代行者A</td>
		<td align="left">セガ��</td>
		<td align="right">1,300</td>
	</tr>

	<tr class="Result1">
		<td align="left">セガ��</td>
		<td align="right">1,300</td>
	</tr>

	<tr class="Result1">
		<td align="left">ダイナムA</td>
		<td align="right">1,400</td>
	</tr>

	<tr class="Result1">
		<td align="left">ダイナムB</td>
		<td align="right">1,400</td>
	</tr>

	<tr class="Result1">
		<td align="left">トイザらス　あ</td>
		<td align="right">1,000</td>
	</tr>

	<tr class="Result1">
		<td align="left">トイザらス　い</td>
		<td align="right">1,000</td>
	</tr>

	<tr class="Result2">
		<td align="left">小計</td>
		<td align="right">7,400</td>
	</tr>
	
	<tr class="Result1">
		<td align="right" rowspan="6">2</td>
		<td align="left" rowspan="6">代行者B</td>
		<td align="left">得意先１</td>
		<td align="right">1,000</td>
	</tr>

	<tr class="Result1">
		<td align="left">得意先２</td>
		<td align="right">1,000</td>
	</tr>

	<tr class="Result1">
		<td align="left">得意先３</td>
		<td align="right">1,000</td>
	</tr>

	<tr class="Result1">
		<td align="left">得意先４</td>
		<td align="right">1,000</td>
	</tr>

	<tr class="Result1">
		<td align="left">得意先５</td>
		<td align="right">1,000</td>
	</tr>

	<tr class="Result2">
		<td align="left">小計</td>
		<td align="right">5,000</td>
	</tr>

	<tr class="Result3">
		<td align="left"><b>合計</b></td>
		<td align="left"><b>2名</b></td>
		<td align="right"></td>
		<td align="right"><b>12,400</b></td>
	</tr>
</table>

<?php echo $this->_tpl_vars['var']['html_page2']; ?>


<!--******************** 画面表示2終了 *******************-->


					</td>
				</tr>
			</table>
		</td>
		<!--******************** 画面表示終了 *******************-->

	</tr>
</table>
<!--******************* 外枠終了 ********************-->

<?php echo $this->_tpl_vars['var']['html_footer']; ?>

	

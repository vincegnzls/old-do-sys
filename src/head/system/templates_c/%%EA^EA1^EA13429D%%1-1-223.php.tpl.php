<?php /* Smarty version 2.6.14, created on 2010-04-05 15:58:57
         compiled from 1-1-223.php.tpl */ ?>

<?php echo $this->_tpl_vars['var']['html_header']; ?>


<body bgcolor="#D8D0C8">
<form name="referer" method="post">

<table border="0" width="100%" height="90%" class="M_table">

	<tr align="center" height="60">
		<td width="100%" colspan="2" valign="top">
</form>
<form <?php echo $this->_tpl_vars['form']['attributes']; ?>
>
			 <?php echo $this->_tpl_vars['var']['page_header']; ?>
 		</td>
	</tr>

	<tr align="center">
	

				<td valign="top">

			<table border="0">
				<tr>
					<td>

<?php if ($_POST['form_search_button'] != "検索フォームを表示" && $_POST['form_button']['show_button'] != "表　示"): ?>
    <?php echo $this->_tpl_vars['form']['form_search_button']['html']; ?>

<?php else: ?>
<table width="650">
    <tr>
        <td>

        <table class="Data_Table" border="1" width="350">
	        <tr>
		        <td class="Title_Purple" width="110"><b>商品コード</b></td>
		        <td class="Value"><?php echo $this->_tpl_vars['form']['form_make_goods_cd']['html']; ?>
</td>
	        </tr>
	        <tr>
		        <td class="Title_Purple" width="110"><b>製造品名</b></td>
		        <td class="Value"><?php echo $this->_tpl_vars['form']['form_make_goods_name']['html']; ?>
</td>
	        </tr>
        </table>

        </td>
        <td valign="bottom">

        <table class="Data_Table" border="1" width="300">
	        <tr>
		        <td class="Title_Purple" width="110"><b>出力形式</b></td>
		        <td class="Value"><?php echo $this->_tpl_vars['form']['form_output_type']['html']; ?>
</td>
	        </tr>
        </table>

        </td>
    </tr>
    <tr>
        <td colspan="2" align="right">
        <table width=''>
	        <tr>
		        <td align="right"><?php echo $this->_tpl_vars['form']['form_button']['show_button']['html']; ?>
　　<?php echo $this->_tpl_vars['form']['form_button']['clear_button']['html']; ?>
</td>
	        </tr>
        </table>
        </td>
    </tr>
</table>
<?php endif; ?>

<br>

					</td>
				</tr>

				<tr>
					<td>

<table>
	<tr>
		<td width="50%" align="left">全<b><?php echo $this->_tpl_vars['var']['search_num']; ?>
</b>件</td>
	</tr>
</table>

<table class="List_Table" border="1" width="450">
	<tr align="center">
		<td class="Title_Purple" width=""><b>No.</b></td>
		<td class="Title_Purple" width=""><b>商品コード</b></td>
		<td class="Title_Purple" width=""><b>製造品名</b></td>
	</tr>
        <?php $_from = $this->_tpl_vars['make_goods_data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['i'] => $this->_tpl_vars['items']):
?>
	<tr class="Result1">
		<td align="right">
                <?php if ($_POST['f_page1'] != null): ?>
                <?php echo $_POST['f_page1']*100+$this->_tpl_vars['i']-99; ?>

                <?php else: ?>
                <?php echo $this->_tpl_vars['i']+1; ?>
  
                <?php endif; ?>   
                </td>
                <?php $_from = $this->_tpl_vars['items']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['j'] => $this->_tpl_vars['item']):
?>
                <?php if ($this->_tpl_vars['j'] == 0): ?>
		<td align="left"><?php echo $this->_tpl_vars['item']; ?>
</a></td>
                <?php elseif ($this->_tpl_vars['j'] == 1): ?>
		<td align="left"><a href="1-1-224.php?goods_id=<?php echo $this->_tpl_vars['item']; ?>
">
                <?php elseif ($this->_tpl_vars['j'] == 2): ?>
                <?php echo $this->_tpl_vars['item']; ?>
</a></td>
                <?php endif; ?>
                <?php endforeach; endif; unset($_from); ?>
	</tr>
        <?php endforeach; endif; unset($_from); ?>

</table>
<?php echo $this->_tpl_vars['var']['html_page2']; ?>



					</td>
				</tr>
			</table>
		</td>
		
	</tr>
</table>

<?php echo $this->_tpl_vars['var']['html_footer']; ?>

	

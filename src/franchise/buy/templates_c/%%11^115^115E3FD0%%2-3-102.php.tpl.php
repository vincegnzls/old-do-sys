<?php /* Smarty version 2.6.14, created on 2009-12-06 09:47:46
         compiled from 2-3-102.php.tpl */ ?>
<?php echo $this->_tpl_vars['var']['html_header']; ?>


<script language="javascript">
<?php echo $this->_tpl_vars['var']['js']; ?>

</script>

<body bgcolor="#D8D0C8">
<form <?php echo $this->_tpl_vars['form']['attributes']; ?>
>

<table width="100%" height="90%" class="M_table">

        <tr align="center" height="60">
        <td width="100%" colspan="2" valign="top"><?php echo $this->_tpl_vars['var']['page_header']; ?>
</td>
    </tr>
    
        <tr align="center" valign="top">
        <td>
            <table>
                <tr>
                    <td>

 
<?php if ($this->_tpl_vars['var']['auth_r_msg'] != null): ?>
    <span style="color: #ff0000; font-weight: bold; line-height: 130%;">
    <li><?php echo $this->_tpl_vars['var']['auth_r_msg']; ?>
</li>
    </span><br>
<?php endif; ?>

    <span style="color: #ff0000; font-weight: bold; line-height: 130%;">
    <ul style="margin-left: 16px;">
    <?php if ($this->_tpl_vars['var']['error'] != null): ?>
        <li><?php echo $this->_tpl_vars['var']['error']; ?>
<br>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['form']['form_client']['error'] != null): ?>
        <li><?php echo $this->_tpl_vars['form']['form_client']['error']; ?>
<br>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['form']['form_designated_date']['error'] != null): ?>
        <li><?php echo $this->_tpl_vars['form']['form_designated_date']['error']; ?>
<br>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['form']['form_order_day']['error'] != null): ?>
        <li><?php echo $this->_tpl_vars['form']['form_order_day']['error']; ?>
<br>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['form']['form_hope_day']['error'] != null): ?>
        <li><?php echo $this->_tpl_vars['form']['form_hope_day']['error']; ?>
<br>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['form']['form_ware']['error'] != null): ?>
        <li><?php echo $this->_tpl_vars['form']['form_ware']['error']; ?>
<br>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['form']['form_trade']['error'] != null): ?>
        <li><?php echo $this->_tpl_vars['form']['form_trade']['error']; ?>
<br>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['form']['form_staff']['error'] != null): ?>
        <li><?php echo $this->_tpl_vars['form']['form_staff']['error']; ?>
<br>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['form']['form_note_your']['error'] != null): ?>
        <li><?php echo $this->_tpl_vars['form']['form_note_your']['error']; ?>
<br>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['form']['form_direct']['error'] != null): ?>
        <li><?php echo $this->_tpl_vars['form']['form_direct']['error']; ?>
<br>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['form']['form_order_no']['error'] != null): ?>
        <li><?php echo $this->_tpl_vars['form']['form_order_no']['error']; ?>
<br>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['form']['form_buy_money']['error'] != null): ?>
        <li><?php echo $this->_tpl_vars['form']['form_buy_money']['error']; ?>
<br>
    <?php endif; ?>
    <?php $_from = $this->_tpl_vars['goods_err']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['i'] => $this->_tpl_vars['item']):
?>
        <?php if ($this->_tpl_vars['goods_err'][$this->_tpl_vars['i']] != null): ?>
        <li><?php echo $this->_tpl_vars['goods_err'][$this->_tpl_vars['i']]; ?>
<br>
        <?php endif; ?>
    <?php endforeach; endif; unset($_from); ?>
    <?php $_from = $this->_tpl_vars['price_num_err']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['i'] => $this->_tpl_vars['item']):
?>
        <?php if ($this->_tpl_vars['price_num_err'][$this->_tpl_vars['i']] != null): ?>
        <li><?php echo $this->_tpl_vars['price_num_err'][$this->_tpl_vars['i']]; ?>
<br>
        <?php endif; ?>
    <?php endforeach; endif; unset($_from); ?>
    <?php $_from = $this->_tpl_vars['num_err']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['i'] => $this->_tpl_vars['item']):
?>
        <?php if ($this->_tpl_vars['num_err'][$this->_tpl_vars['i']] != null): ?>
        <li><?php echo $this->_tpl_vars['num_err'][$this->_tpl_vars['i']]; ?>
<br>
        <?php endif; ?>
    <?php endforeach; endif; unset($_from); ?>
    <?php $_from = $this->_tpl_vars['price_err']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['i'] => $this->_tpl_vars['item']):
?>
        <?php if ($this->_tpl_vars['price_err'][$this->_tpl_vars['i']] != null): ?>
        <li><?php echo $this->_tpl_vars['price_err'][$this->_tpl_vars['i']]; ?>
<br>
        <?php endif; ?>
    <?php endforeach; endif; unset($_from); ?>
    <?php $_from = $this->_tpl_vars['duplicate_goods_err']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['i'] => $this->_tpl_vars['item']):
?>
        <li><?php echo $this->_tpl_vars['duplicate_goods_err'][$this->_tpl_vars['i']]; ?>
<br>
    <?php endforeach; endif; unset($_from); ?>
    </ul>
    </span>

<?php if ($this->_tpl_vars['var']['freeze_flg'] != null): ?>
    <?php if ($this->_tpl_vars['var']['goods_twice'] != null): ?>
        <font color="red"><b><?php echo $this->_tpl_vars['var']['goods_twice']; ?>
</b></font><br>
    <?php endif; ?> 
      <span style="font: bold;"><font size="+1">以下の内容で発注しますか？</font></span><br>
<?php endif; ?>


<table>
    <tr>
        <td>




<table class="Data_Table" border="1">
<col width="100" style="font-weight: bold;">
<col>
<col width="90" style="font-weight: bold;">
<col>
<col width="80" style="font-weight: bold;">
<col>
    <tr>
        <td class="Title_Blue">発注番号</td>
        <td class="Value"><?php echo $this->_tpl_vars['form']['form_order_no']['html']; ?>
</td>
        <?php if ($this->_tpl_vars['var']['freeze_flg'] == null): ?>
            <td class="Title_Blue"><?php echo $this->_tpl_vars['form']['form_client_link']['html']; ?>
<font color="#ff0000">※</font></td>
        <?php else: ?>
            <td class="Title_Blue">発注先<font color="#ff0000">※</font></td>
        <?php endif; ?>

        <?php if ($this->_tpl_vars['var']['head_flg'] == 't'): ?>
            <td class="Value" colspan="3"><?php echo $this->_tpl_vars['form']['form_client']['html']; ?>
</td>
        <?php else: ?>
            <td class="Value"><?php echo $this->_tpl_vars['form']['form_client']['html']; ?>
</td>
            <td class="Title_Blue">発注日<font color="#ff0000">※</font></td>
            <td class="Value"><?php echo $this->_tpl_vars['form']['form_order_day']['html']; ?>
</td>
        <?php endif; ?>

    </tr>
    <tr>
        <td class="Title_Blue">取引区分</a><font color="#ff0000">※</font></td>
        <td class="Value"><?php echo $this->_tpl_vars['form']['form_trade']['html']; ?>
</td>
        <td class="Title_Blue">出荷可能数</td>
        <td class="Value"><?php echo $this->_tpl_vars['form']['form_designated_date']['html']; ?>
 日後までの発注済数と引当数を考慮する</td>
        <td class="Title_Blue">希望納期</td>
        <td class="Value"><?php echo $this->_tpl_vars['form']['form_hope_day']['html']; ?>
</td>    </tr>
    </tr>
    <tr>
        <td class="Title_Blue">仕入倉庫<font color="#ff0000">※</font></td>
        <td class="Value"><?php echo $this->_tpl_vars['form']['form_ware']['html']; ?>
</td>
        <td class="Title_Blue">直送先</td>
        <td class="Value" colspan="3"><?php echo $this->_tpl_vars['form']['form_direct']['html']; ?>
</td>
    </tr>
    <tr>
        <td class="Title_Blue">発注担当者<font color="#ff0000">※</font></td>
        <td class="Value"><?php echo $this->_tpl_vars['form']['form_staff']['html']; ?>
</td>
        <td class="Title_Blue">運送業者</td>
        <td class="Value" colspan="3"><?php echo $this->_tpl_vars['form']['form_trans']['html']; ?>
</td>
    </tr>
    <tr>
        <td class="Title_Blue">通信欄<br>（仕入先宛）</td>
        <td class="Value" colspan="5"><?php echo $this->_tpl_vars['form']['form_note_your']['html']; ?>
</td>
    </tr>
</table>

        </td>
    </tr>
    <tr>
        <td>

<span style="font: bold; color: #ff0000;"><?php echo $this->_tpl_vars['var']['warning']; ?>
</span>

        </td>
    </tr>
</table>
<br>

                    </td>
                </tr>
                <tr>
                    <td>

<table width="100%">
    <tr>
        <td>

<?php echo $this->_tpl_vars['form']['hidden']; ?>

<span style="font: bold; color: #ff0000;"><?php echo $this->_tpl_vars['var']['message']; ?>
</span>

<table class="List_Table" border="1" width="100%">
    <tr align="center" style="font-weight: bold;">
        <td class="Title_Blue">No.</td>
        <td class="Title_Blue">商品コード<font color="#ff0000">※</font><br>商品名</td>
        <td class="Title_Blue">実棚数<br>(A)</td>
        <td class="Title_Blue">発注済数<br>(B)</td>
        <td class="Title_Blue">引当数<br>(C)</td>
        <td class="Title_Blue">出荷可能数<br>(A+B-C)</td>
        <td class="Title_Blue">ロット仕入</td>
        <td class="Title_Blue">ロット数</td>
        <td class="Title_Blue">発注数<font color="#ff0000">※</font></td>
        <td class="Title_Blue">仕入単価<font color="#ff0000">※</font></td>
        <td class="Title_Blue">仕入金額</td>
        <?php if ($this->_tpl_vars['var']['warning'] == null && $this->_tpl_vars['var']['freeze_flg'] == null): ?>
            <td class="Title_Add" width="50">行削除</td>
        <?php endif; ?>
    </tr>
    <?php echo $this->_tpl_vars['var']['html']; ?>

</table>

        </td>
    </tr>
    <tr>
        <td>

<?php if ($this->_tpl_vars['var']['warning'] == null): ?>
<table width="100%">
    <tr>
        	<td><?php echo $this->_tpl_vars['form']['form_add_row']['html']; ?>
</td>
        <td>
            <table class="List_Table" border="1" align="right" style="font-weight: bold;">
                <tr>
                    <td class="Title_Blue" width="80" align="center">税抜金額</td>
                    <td class="Value" width="100" align="right"><?php echo $this->_tpl_vars['form']['form_buy_money']['html']; ?>
</td>
                    <td class="Title_Blue" width="80" align="center">消費税</td>
                    <td class="Value" width="100" align="right"><?php echo $this->_tpl_vars['form']['form_tax_money']['html']; ?>
</td>
                    <td class="Title_Blue" width="80" align="center">税込合計</td>
                    <td class="Value" width="100" align="right"><?php echo $this->_tpl_vars['form']['form_total_money']['html']; ?>
</td>
                </tr>
            </table>
        </td>
        <td><?php echo $this->_tpl_vars['form']['form_sum_button']['html']; ?>
</td>
    </tr>
</table>

        </td>
    </tr>
    <tr>
        <td>

<A NAME="foot"></A>
<table width="100%">
    <tr>
		<td align="left"><font color="#ff0000"><b>※は必須入力です</b></font></td>
		 
		<?php if ($this->_tpl_vars['var']['freeze_flg'] == null): ?>
			 
        	<td align="right"><?php echo $this->_tpl_vars['form']['form_order_button']['html']; ?>
</td>
		<?php else: ?>
			 
			<td align="right"><?php echo $this->_tpl_vars['form']['comp_button']['html']; ?>
　　<?php echo $this->_tpl_vars['form']['order_button']['html']; ?>
　　<?php echo $this->_tpl_vars['form']['return_button']['html']; ?>
</td>
		<?php endif; ?>
    </tr>
</table>
<?php endif; ?>
        </td>
    </tr>
</table>

                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
</table>

<?php echo $this->_tpl_vars['var']['html_footer']; ?>

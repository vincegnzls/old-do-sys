<?php /* Smarty version 2.6.14, created on 2009-12-08 19:17:03
         compiled from 2-2-301.php.tpl */ ?>
<?php echo $this->_tpl_vars['var']['html_header']; ?>

<script language="javascript">
<?php echo $this->_tpl_vars['var']['code_value']; ?>

<?php echo $this->_tpl_vars['var']['contract']; ?>

<?php echo $this->_tpl_vars['var']['js']; ?>

</script>


<body bgcolor="#D8D0C8" onLoad="Text_Disabled('<?php echo $_POST['form_slipout_type'][0]; ?>
')">
<form <?php echo $this->_tpl_vars['form']['attributes']; ?>
>
<?php echo $this->_tpl_vars['form']['hidden']; ?>

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

 
<span style="color: #ff0000; font-weight: bold; line-height: 130%;">
    <?php if ($this->_tpl_vars['form']['form_branch_id']['error'] != null): ?>
        <li><?php echo $this->_tpl_vars['form']['form_branch_id']['error']; ?>
<br>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['form']['form_claim_day1']['error'] != null): ?>
        <li><?php echo $this->_tpl_vars['form']['form_claim_day1']['error']; ?>
<br>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['form']['form_claim_day2']['error'] != null): ?>
        <li><?php echo $this->_tpl_vars['form']['form_claim_day2']['error']; ?>
<br>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['form']['form_claim']['error'] != null): ?>
        <li><?php echo $this->_tpl_vars['form']['form_claim']['error']; ?>
<br>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['form']['form_year_month']['error'] != null): ?>
        <li><?php echo $this->_tpl_vars['form']['form_year_month']['error']; ?>
<br>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['var']['error'] != null): ?>
        <li><?php echo $this->_tpl_vars['var']['error']; ?>
<br>
    <?php endif; ?>
    <ul>
    <?php $_from = $this->_tpl_vars['sale_err']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['i'] => $this->_tpl_vars['item']):
?>
        <?php if ($this->_tpl_vars['i'] == 0): ?>
        <li>以下の売上伝票は日次更新されていないため請求データの作成に失敗しました。<br>
        <?php endif; ?>
        <?php echo $this->_tpl_vars['item']; ?>
<br>
    <?php endforeach; endif; unset($_from); ?>
    <?php $_from = $this->_tpl_vars['pay_err']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['i'] => $this->_tpl_vars['item']):
?>
        <?php if ($this->_tpl_vars['i'] == 0): ?>
        <li>以下の入金伝票は日次更新されていないため請求データの作成に失敗しました。<br>
        <?php endif; ?>
        <?php echo $this->_tpl_vars['item']; ?>
<br>
    <?php endforeach; endif; unset($_from); ?>
    <?php $_from = $this->_tpl_vars['advance_err']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['i'] => $this->_tpl_vars['item']):
?>
        <?php if ($this->_tpl_vars['i'] == 0): ?>
        <li>以下の前受金伝票は確定されていないため請求データの作成に失敗しました。<br>
        <?php endif; ?>
        <?php echo $this->_tpl_vars['item']; ?>
<br>
    <?php endforeach; endif; unset($_from); ?>
    </ul>
</span>   

<table width="100%">
    <tr>
        <td>
        <div class="note">
        請求データの作成について<br>
        　�＼禅畚颪鮃洪係紊郎萄鄒�されません。<br>
        　�∪禅當�日更新前の請求データは変更可能です。但し、請求作成済のデータを削除して作成可能です。<br>
        <div><p>
<table width="500" >
    <tr>
        <td align="left" colspan="2"><?php echo $this->_tpl_vars['form']['form_slipout_type'][0]['html']; ?>
</td>
    </tr>
    <tr>
        <td width="100"></td>
        <td>
        指定した締日の得意先に対して、請求書を作成します
        <table class="Data_Table" border="1" width="300">
        <col width="100" style="font-weight:bold;">
        <col>
            <tr>
                <td class="Title_Pink">本支店<font color="#ff0000">※</font></td>
                <td class="Value"><?php echo $this->_tpl_vars['form']['form_branch_id']['html']; ?>
</td>
            </tr>
            <tr>
                <td class="Title_Pink">請求締日<font color="#ff0000">※</font></td>
                <td class="Value"><?php echo $this->_tpl_vars['form']['form_claim_day1']['html']; ?>
</td>
            </tr>
        </table>
        </td>
    </tr>
</table>
        </td>
    </tr>
    <tr>
        <td>

<table width="660" >
    <tr>
        <td align="left" colspan="2"><?php echo $this->_tpl_vars['form']['form_slipout_type'][1]['html']; ?>
</td>
    </tr>
    <tr>
        <td width="100"></td>
        <td>
        指定した得意先に対して、指定した請求締日までの請求書を作成します
        <table class="Data_Table" border="1" width="450">
        <col width="100" style="font-weight:bold;">
        <col>
            <tr>
                <td class="Title_Pink"><?php echo $this->_tpl_vars['form']['form_claim_link']['html']; ?>
<font color="#ff0000">※</font></td>
                <td class="Value"><?php echo $this->_tpl_vars['form']['form_claim']['html']; ?>
</td>
            </tr>
            <tr>
                <td class="Title_Pink">請求締日<font color="#ff0000">※</font></td>
                                <td class="Value"><?php echo $this->_tpl_vars['form']['form_claim_day2']['html']; ?>
</td>
            </tr>
        </table>
        </td>
    </tr>
</table>

<table width="100%">
    <tr>
        <td><font color="#ff0000"><b>※は必須入力です</b></font></td>
        <td align="right"><?php echo $this->_tpl_vars['form']['form_create_button']['html']; ?>
</td>
    </tr>
</table>

        </td>
    </tr>
</table>
<br>

                    </td>
                </tr>
                <tr>
                    <td>

<table width="450" align="center">
    <tr>
        <td>

<table class="List_Table" border="1" width="100%">
    <tr align="center" style="font-weight: bold;">
        <td class="Title_Pink" rowspan="2" width="30">締日</td>
        <td class="Title_Pink" colspan="<?php echo $this->_tpl_vars['page_data']['colspan']; ?>
"><?php echo $this->_tpl_vars['var']['last_date']; ?>
</td>
        <td class="Title_Pink" colspan="<?php echo $this->_tpl_vars['page_data']['colspan']; ?>
"><?php echo $this->_tpl_vars['var']['now_date']; ?>
</td>
    </tr>
    <tr align="center" style="font-weight: bold;">
        <?php $_from = $this->_tpl_vars['page_data']['cd']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['i'] => $this->_tpl_vars['item']):
?>
            <td class="Title_Pink"><?php echo $this->_tpl_vars['page_data'][$this->_tpl_vars['item']]['name']; ?>
</td>
        <?php endforeach; endif; unset($_from); ?>
        <?php $_from = $this->_tpl_vars['page_data']['cd']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['i'] => $this->_tpl_vars['item']):
?>
            <td class="Title_Pink"><?php echo $this->_tpl_vars['page_data'][$this->_tpl_vars['item']]['name']; ?>
</td>
        <?php endforeach; endif; unset($_from); ?>
    </tr>

    <?php $_from = $this->_tpl_vars['page_data']['close_day']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['i'] => $this->_tpl_vars['item']):
?>
    <tr class="Result1">
        <td align="right"><?php echo $this->_tpl_vars['item']; ?>
</td>
        <?php $_from = $this->_tpl_vars['page_data']['cd']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['j'] => $this->_tpl_vars['items']):
?>
            <?php if ($this->_tpl_vars['page_data'][$this->_tpl_vars['items']]['data'][$this->_tpl_vars['item']]['last'] != null): ?>
            <td align="center"><?php echo $this->_tpl_vars['page_data'][$this->_tpl_vars['items']]['data'][$this->_tpl_vars['item']]['last']; ?>
</td>
            <?php else: ?>
            <td align="center">-</td>
            <?php endif; ?>
        <?php endforeach; endif; unset($_from); ?>
        <?php $_from = $this->_tpl_vars['page_data']['cd']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['j'] => $this->_tpl_vars['items']):
?>
            <?php if ($this->_tpl_vars['page_data'][$this->_tpl_vars['items']]['data'][$this->_tpl_vars['item']]['last'] != null): ?>
            <td align="center"><?php echo $this->_tpl_vars['page_data'][$this->_tpl_vars['items']]['data'][$this->_tpl_vars['item']]['now']; ?>
</td>
            <?php else: ?>
            <td align="center">-</td>
            <?php endif; ?>
        <?php endforeach; endif; unset($_from); ?>
    </tr> 
    <?php endforeach; endif; unset($_from); ?>    
</table>
<br>
○：一括作成済　　×：一括作成未　　−：対象締日の請求先無し
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

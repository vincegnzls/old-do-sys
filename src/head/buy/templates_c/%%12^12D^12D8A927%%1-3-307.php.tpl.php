<?php /* Smarty version 2.6.14, created on 2009-11-05 09:36:11
         compiled from 1-3-307.php.tpl */ ?>
<?php echo $this->_tpl_vars['var']['html_header']; ?>

<script language="javascript">
<?php echo $this->_tpl_vars['var']['code_value']; ?>

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

 
<span style="color: #ff0000; font-weight: bold; line-height: 130%;">
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
    <?php if ($this->_tpl_vars['var']['error'] != null): ?>
        <li><?php echo $this->_tpl_vars['var']['error']; ?>
<br>
    <?php endif; ?>

    <?php $_from = $this->_tpl_vars['non_update_err']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['i'] => $this->_tpl_vars['item']):
?>
        <?php if ($this->_tpl_vars['i'] == 0): ?>
            <li>以下の取引先は未更新の仕入締データがあるため作成されませんでした。<br>
        <?php endif; ?>
        　　<?php echo $this->_tpl_vars['non_update_err'][$this->_tpl_vars['i']]['client_cd']; ?>
　<?php echo $this->_tpl_vars['non_update_err'][$this->_tpl_vars['i']]['client_name']; ?>

        <br>
    <?php endforeach; endif; unset($_from); ?>
</span>   

<table width="100%">
    <tr>
        <td>
<table width="500" >
    <tr>
        <td width="100"></td>
        <td>
        指定した締日の仕入先に対して、仕入締処理を行います
        <table class="Data_Table" border="1" width="300">
        <col width="100" style="font-weight:bold;">
        <col>
            <tr>
                <td class="Title_Pink">仕入締日<font color="#ff0000">※</font></td>
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
<table width="100%">
    <tr>
        <td><font color="#ff0000"><b>※は必須入力です</b></font></td>
        <td align="right"><?php echo $this->_tpl_vars['form']['submit']['html']; ?>
</td>
    </tr>
</table>

        </td>
    </tr>
</table>
<br><br>


                    </td>
                </tr>
                <tr>
                    <td>

<table width="350" align="center">
    <tr>
        <td>

<table class="List_Table" border="1" width="100%">
    <tr align="center" style="font-weight: bold;">
        <td class="Title_Pink">No.</td>
        <td class="Title_Pink">締日</td>
        <td class="Title_Pink"><?php echo $this->_tpl_vars['var']['last_date']; ?>
</td>
        <td class="Title_Pink"><?php echo $this->_tpl_vars['var']['now_date']; ?>
</td>
    </tr>
    <?php $_from = $this->_tpl_vars['page_data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['i'] => $this->_tpl_vars['item']):
?>
    <tr class="Result1">        <td align="right"><?php echo $this->_tpl_vars['i']+1; ?>
</td>
        <td><?php echo $this->_tpl_vars['page_data'][$this->_tpl_vars['i']][0]; ?>
</td>
        <?php if ($this->_tpl_vars['page_data'][$this->_tpl_vars['i']][1] == null): ?>
        <td align="center">×</td>
        <?php else: ?>
        <td align="center">○</td>
        <?php endif; ?>
        <?php if ($this->_tpl_vars['page_data'][$this->_tpl_vars['i']][2] == null): ?>
        <td align="center">×</td>
        <?php else: ?>
        <td align="center">○</td>
        <?php endif; ?>
    </tr>   
    <?php endforeach; endif; unset($_from); ?>
</table>

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

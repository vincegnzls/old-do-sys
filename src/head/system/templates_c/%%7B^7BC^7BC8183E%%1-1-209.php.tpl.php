<?php /* Smarty version 2.6.14, created on 2010-04-05 15:59:13
         compiled from 1-1-209.php.tpl */ ?>
<?php echo $this->_tpl_vars['var']['html_header']; ?>


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

    <span style="color: #0000ff; font-weight: bold; line-height: 130%;">
    <?php if ($this->_tpl_vars['var']['message'] != null && $this->_tpl_vars['form']['form_product_cd']['error'] == null && $this->_tpl_vars['form']['form_product_name']['error'] == null): ?>
        <li><?php echo $this->_tpl_vars['var']['message']; ?>
<br>
    <?php endif; ?>
    </span>
          <span style="color: #ff0000; font-weight: bold; line-height: 130%;">
    <?php if ($this->_tpl_vars['form']['form_product_cd']['error'] != null): ?>
        <li><?php echo $this->_tpl_vars['form']['form_product_cd']['error']; ?>
<br>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['form']['form_product_name']['error'] != null): ?>
        <li><?php echo $this->_tpl_vars['form']['form_product_name']['error']; ?>
<br>
    <?php endif; ?>
    </span>

<table width="450">
    <tr>
        <td>

<table class="Data_Table" border="1" width="100%">
<col width="140" style="font-weight: bold;">
<col>
    <tr>
        <td class="Title_Purple">������ʬ������<font color="#ff0000">��</font></td>
        <td class="Value">
        <table>
            <tr>
                <td rowspan="2"><?php echo $this->_tpl_vars['form']['form_product_cd']['html']; ?>
</td>
                <td><font color="#555555">��0000��4999��9000��9999��������</font></td>
            </tr>
            <tr>
                <td><font color="#555555">��5000��8999�ϥ���å�����</font></td>
            </tr>
        </table>
        </td>
    </tr>
    <tr>
        <td class="Title_Purple">������ʬ̾<font color="#ff0000">��</font></td>
        <td class="Value"><?php echo $this->_tpl_vars['form']['form_product_name']['html']; ?>
</td>
    </tr>
    <tr>
        <td class="Title_Purple">����</td>
        <td class="Value"><?php echo $this->_tpl_vars['form']['form_product_note']['html']; ?>
</td>
    </tr>
    <tr>
        <td class="Title_Purple">��ǧ</td>
        <td class="Value"><?php echo $this->_tpl_vars['form']['form_accept']['html']; ?>
</td>
    </tr>
</table>

<table width="100%">
    <tr>
        <td><b><font color="#ff0000">����ɬ�����ϤǤ�</font></b></td>
        <td align="right"><?php echo $this->_tpl_vars['form']['form_entry_button']['html']; ?>
����<?php echo $this->_tpl_vars['form']['form_clear_button']['html']; ?>
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

<?php echo $this->_tpl_vars['form']['hidden']; ?>


<table width="100%">
    <tr>
        <td>

��<b><?php echo $this->_tpl_vars['var']['total_count']; ?>
</b>�<?php echo $this->_tpl_vars['form']['form_csv_button']['html']; ?>

<table class="List_Table" border="1" width="100%">
    <tr align="center" style="font-weight: bold;">
        <td class="Title_Purple">No.</td>
        <td class="Title_Purple">������ʬ������</td>
        <td class="Title_Purple">������ʬ̾</td>
        <td class="Title_Purple">����</td>
        <td class="Title_Purple">��ǧ</td>
    </tr>
        <?php $_from = $this->_tpl_vars['page_data']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['i'] => $this->_tpl_vars['item']):
?>
    <tr class="Result1">
        <td align="right"><?php echo $this->_tpl_vars['i']+1; ?>
</td>
        <td><?php echo $this->_tpl_vars['item'][0]; ?>
</a></td>
        <td><a href="?product_id=<?php echo $this->_tpl_vars['item'][1]; ?>
"><?php echo $this->_tpl_vars['item'][2]; ?>
</a></td>
        <td><?php echo $this->_tpl_vars['item'][3]; ?>
</a></td>
        <?php if ($this->_tpl_vars['item'][4] == '1'): ?><td align="center">��</td>
        <?php else: ?><td align="center" style="color: #ff0000;">��</td>
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

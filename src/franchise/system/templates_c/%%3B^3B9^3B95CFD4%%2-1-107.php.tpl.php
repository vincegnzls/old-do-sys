<?php /* Smarty version 2.6.14, created on 2010-05-13 14:48:27
         compiled from 2-1-107.php.tpl */ ?>
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


<table>
    <tr>
        <td>

<table class="Data_Table" border="1" width="700">
<col width="140" style="font-weight: bold;">
<col width="140">
<col width="140"style="font-weight: bold;">
<col>
    <tr>
        <td class="Title_Purple">����åץ�����</td>
        <td class="Value"><?php echo $this->_tpl_vars['form']['form_client_cd']['html']; ?>
</td>
        <td class="Title_Purple">����å�̾</td>
        <td class="Value"><?php echo $this->_tpl_vars['form']['form_client_name']['html']; ?>
</td>
    </tr>
    <tr>
        <td class="Title_Purple">�ͥåȥ����ID</td>
        <td class="Value"><?php echo $this->_tpl_vars['form']['form_staff_cd']['html']; ?>
</td>
        <td class="Title_Purple">�����å�̾</td>
        <td class="Value"><?php echo $this->_tpl_vars['form']['form_staff_name']['html']; ?>
</td>
    </tr>
    <tr>
        <td class="Title_Purple">ô���ԥ�����</td>
        <td class="Value"><?php echo $this->_tpl_vars['form']['form_charge_cd']['html']; ?>
</td>
        <td class="Title_Purple">��Ź����°����</td>
        <td class="Value"><?php echo $this->_tpl_vars['form']['form_part']['html']; ?>
</td>
    </tr>
    <tr>
        <td class="Title_Purple">��</td>
        <td class="Value"><?php echo $this->_tpl_vars['form']['form_position']['html']; ?>
</td>
        <td class="Title_Purple">����</td>
        <td class="Value"><?php echo $this->_tpl_vars['form']['form_job_type']['html']; ?>
</td>
    </tr>
    <tr>
        <td class="Title_Purple">�߿�����</td>
        <td class="Value" colspan="3"><?php echo $this->_tpl_vars['form']['form_state']['html']; ?>
</td>
    </tr>
    <tr>
        <td class="Title_Purple">�ȥ�����ǻλ��</td>
        <td class="Value" colspan="3"><?php echo $this->_tpl_vars['form']['form_toilet_license']['html']; ?>
</td>
    </tr>
    <tr>
        <td class="Title_Purple">�������</td>
        <td class="Value" colspan="3"><?php echo $this->_tpl_vars['form']['form_license']['html']; ?>
</td>
    </tr>
</table>

        </td>
        <td valign="bottom">

<table class="Data_Table" border="1" width="200">
    <tr>
        <td class="Title_Purple" width="80"><b>���Ϸ���</b></td>
        <td class="Value" colspan="3"><?php echo $this->_tpl_vars['form']['form_output_type']['html']; ?>
</td>
    </tr>
</table>

        </td>
    </tr>
    <tr>
        <td colspan="2">

<table align="right">
    <tr>
        <td><?php echo $this->_tpl_vars['form']['show_button']['html']; ?>
����<?php echo $this->_tpl_vars['form']['clear_button']['html']; ?>
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

<?php if ($this->_tpl_vars['var']['display_flg'] == true): ?>

<table width="100%">
    <tr>
        <td>

��<b><?php echo $this->_tpl_vars['var']['total_count']; ?>
</b>��
<table class="List_Table" border="1" width="100%">
    <tr align="center" style="font-weight: bold;">
        <td class="Title_Purple">No.</td>
        <td class="Title_Purple">����åץ�����<br>����å�̾</td>
        <td class="Title_Purple">ô����<br>������</td>
        <td class="Title_Purple">�ͥåȥ����ID<br>�����å�̾</td>
        <td class="Title_Purple">��</td>
        <td class="Title_Purple">��Ź</td>
        <td class="Title_Purple">��°����</td>
        <td class="Title_Purple">����</td>
        <td class="Title_Purple">�߿�����</td>
        <td class="Title_Purple">�ȥ�����ǻλ��</td>
        <td class="Title_Purple">�������</td>
    </tr>
    <?php $_from = $this->_tpl_vars['row']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['j'] => $this->_tpl_vars['items']):
?>
    <tr class="<?php echo $this->_tpl_vars['tr'][$this->_tpl_vars['j']]; ?>
">
        <td align="right"><?php echo $this->_tpl_vars['j']+1; ?>
</td>
        <?php if ($this->_tpl_vars['row'][$this->_tpl_vars['j']][1] != null): ?>
        <td><?php echo $this->_tpl_vars['row'][$this->_tpl_vars['j']][0]; ?>
-<?php echo $this->_tpl_vars['row'][$this->_tpl_vars['j']][1]; ?>
</a>
        <?php else: ?>
        <td>
        <?php endif; ?>
        <br><?php echo $this->_tpl_vars['row'][$this->_tpl_vars['j']][2]; ?>
</td>
        <td><?php echo $this->_tpl_vars['row'][$this->_tpl_vars['j']][3]; ?>
</td>
        <td>
        <?php if ($this->_tpl_vars['row'][$this->_tpl_vars['j']][5] != null): ?>
            <?php echo $this->_tpl_vars['row'][$this->_tpl_vars['j']][5]; ?>
-<?php echo $this->_tpl_vars['row'][$this->_tpl_vars['j']][6]; ?>

        <?php endif; ?>
        <br><a href="2-1-108.php?staff_id=<?php echo $this->_tpl_vars['row'][$this->_tpl_vars['j']][4]; ?>
"><?php echo $this->_tpl_vars['row'][$this->_tpl_vars['j']][7]; ?>
</a></td>
        <td><?php echo $this->_tpl_vars['row'][$this->_tpl_vars['j']][9]; ?>
</td>
        <td><?php echo $this->_tpl_vars['row'][$this->_tpl_vars['j']][14]; ?>
</td>
        <td><?php echo $this->_tpl_vars['row'][$this->_tpl_vars['j']][8]; ?>
</td>
        <td><?php echo $this->_tpl_vars['row'][$this->_tpl_vars['j']][10]; ?>
</td>
        <td align="center"><?php echo $this->_tpl_vars['row'][$this->_tpl_vars['j']][11]; ?>
</td>
        <td align="center"><?php echo $this->_tpl_vars['row'][$this->_tpl_vars['j']][12]; ?>
</td>
        <td><?php echo $this->_tpl_vars['row'][$this->_tpl_vars['j']][13]; ?>
</td>
    </tr>
    <?php endforeach; endif; unset($_from); ?>
</table>
<?php echo $this->_tpl_vars['form']['hidden']; ?>

        </td>
    </tr>
</table>

<?php endif; ?>

                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
</table>

<?php echo $this->_tpl_vars['var']['html_footer']; ?>

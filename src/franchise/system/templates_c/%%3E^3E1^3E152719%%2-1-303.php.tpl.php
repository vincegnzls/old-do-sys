<?php /* Smarty version 2.6.14, created on 2010-04-05 16:39:37
         compiled from 2-1-303.php.tpl */ ?>
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
<?php if ($this->_tpl_vars['var']['comp_msg'] != null): ?><li><?php echo $this->_tpl_vars['var']['comp_msg']; ?>
<br><?php endif; ?>
</span>
 
<span style="color: #ff0000; font-weight: bold; line-height: 130%;">
<?php if ($this->_tpl_vars['form']['form_post']['error'] != null): ?><li><?php echo $this->_tpl_vars['form']['form_post']['error']; ?>
<br><?php endif; ?>
</span><br>

<table>
    <tr>
        <td>

<span style="font: bold 16px;">※�　銑Δ房�社情報を入力してください</span><br>
<span style="font: bold 16px;">※�А銑�にコメントを入力してください</span><br>

<table class="List_Table" width="903" height="1280" cellpadding="0"><tr><td class="Value">
<table width="100%" height="100%" style="background: url(../../../image/hacchusho_f_20070618.png) no-repeat fixed;">
    <tr>
        <td valign="top">
                        <table style="position: relative; top: 142px; left: 580px;" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="color: #ff0000; font-size: 14px;">
                        <b>��</b><?php echo $this->_tpl_vars['form']['form_post']['html']; ?>

                    </td>
                </tr>
            </table>
            <table style="position: relative; top: 142px; left: 580px;" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="color: #ff0000; font-size: 14px;">
                        <b>��</b><?php echo $this->_tpl_vars['form']['o_memo2']['html']; ?>
<br>
                        <b>��</b><?php echo $this->_tpl_vars['form']['o_memo3']['html']; ?>
<br>
                        <b>��</b><?php echo $this->_tpl_vars['form']['o_memo4']['html']; ?>
<br>
                        <b>��</b><?php echo $this->_tpl_vars['form']['o_memo5']['html']; ?>
<br>
                        <b>��</b><?php echo $this->_tpl_vars['form']['o_memo6']['html']; ?>

                    </td>
                </tr>
            </table>
                        <table style="position: relative; top: 210px; left: 40px;" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="color: #ff0000; font-size: 14px;">
                        <b>��</b><?php echo $this->_tpl_vars['form']['o_memo7']['html']; ?>
<br>
                        <b>��</b><?php echo $this->_tpl_vars['form']['o_memo8']['html']; ?>

                    </td>
                </tr>
            </table>
                        <table style="position: relative; top: 880px; left: 40px;" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="color: #ff0000; font-size: 14px;">
                        <b>��</b><?php echo $this->_tpl_vars['form']['o_memo9']['html']; ?>
<br>
                        <b>��</b><?php echo $this->_tpl_vars['form']['o_memo10']['html']; ?>
<br>
                        <b>��</b><?php echo $this->_tpl_vars['form']['o_memo11']['html']; ?>
<br>
                        <b>��</b><?php echo $this->_tpl_vars['form']['o_memo12']['html']; ?>
<br>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</td></tr></table>

<table align="right">
    <tr>
        <td><?php echo $this->_tpl_vars['form']['new_button']['html']; ?>
　　<?php echo $this->_tpl_vars['form']['order_button']['html']; ?>
</td>
    </tr>
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

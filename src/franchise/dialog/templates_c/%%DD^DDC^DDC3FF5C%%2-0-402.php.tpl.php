<?php /* Smarty version 2.6.14, created on 2010-01-08 15:35:58
         compiled from 2-0-402.php.tpl */ ?>
<?php echo $this->_tpl_vars['var']['html_header']; ?>


<head>
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Expires" content="0">
</head>

<body bgcolor="#D8D0C8" onLoad="document.dateForm.form_client_name.focus();">
<form <?php echo $this->_tpl_vars['form']['attributes']; ?>
>

<table width="100%" height="90%">

        <tr valign="top">
        <td>
            <table>
                <tr>
                    <td>



<table width="500">
    <tr>
        <td>

<table class="Data_Table" border="1" width="100%">
<col width="250" style="font-weight: bold;">
<col>
    <tr>
        <td class="Title_Purple">得意先コード</td>
        <td class="Value"><?php echo $this->_tpl_vars['form']['form_client']['html']; ?>
</td>
    </tr>
    <tr>
        <td class="Title_Purple">得意先名／フリガナ</td>
        <td class="Value"><?php echo $this->_tpl_vars['form']['form_client_name']['html']; ?>
</td>
    </tr>
    <tr>
        <td class="Title_Purple">請求先名</td>
        <td class="Value"><?php echo $this->_tpl_vars['form']['form_claim_name']['html']; ?>
</td>
    </tr>
    <tr>
        <td class="Title_Purple">銀行カナ</td>
        <td class="Value"><?php echo $this->_tpl_vars['form']['form_bank_kana']['html']; ?>
</td>
    </tr>
    <tr>
        <td class="Title_Purple">振込名義</td>
        <td class="Value"><?php echo $this->_tpl_vars['form']['form_account_name']['html']; ?>
</td>
    </tr>
    <tr>
        <td class="Title_Purple">地区</td>
        <td class="Value"><?php echo $this->_tpl_vars['form']['form_area_id']['html']; ?>
</td>
    </tr>
    <tr>
        <td class="Title_Purple">状態</td>
        <td class="Value"><?php echo $this->_tpl_vars['form']['form_state']['html']; ?>
</td>
    </tr>
    <tr>
        <td class="Title_Purple">表示順</td>
        <td class="Value"><?php echo $this->_tpl_vars['form']['form_turn']['html']; ?>
</td>
    </tr>
</table>

<table align="right">
    <tr>
        <td><?php echo $this->_tpl_vars['form']['form_button']['show_button']['html']; ?>
　　<?php echo $this->_tpl_vars['form']['form_button']['close_button']['html']; ?>
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

<?php echo $this->_tpl_vars['var']['html_page']; ?>

<table class="List_Table" border="1" width="500">
    <tr align="center" style="font-weight: bold;">
        <td class="Title_Purple">No.</td>
        <td class="Title_Purple">得意先コード<br>得意先名</td>
        <td class="Title_Purple">略称</td>
        <td class="Title_Purple">地区</td>
        <td class="Title_Purple">状態</td>
    </tr>
    <?php $_from = $this->_tpl_vars['row_html']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }if (count($_from)):
    foreach ($_from as $this->_tpl_vars['j'] => $this->_tpl_vars['items']):
?>
    <tr class="Result1"> 
        <td align="right" width="30">
            <?php if ($_POST['form_show_page'] != 2 && $_POST['f_page1'] != ""): ?>
                <?php echo $_POST['f_page1']*100+$this->_tpl_vars['j']-99; ?>

            <?php else: ?>
            　  <?php echo $this->_tpl_vars['j']+1; ?>
  
            <?php endif; ?>   
        </td>               
        <td width="200">
            <?php echo $this->_tpl_vars['row_html'][$this->_tpl_vars['j']][0]; ?>
-<?php echo $this->_tpl_vars['row_html'][$this->_tpl_vars['j']][1]; ?>
<br>
            <?php if ($this->_tpl_vars['row_html'][$this->_tpl_vars['j']][5] == 'true'): ?>
				<!-- 親判定 -->
				<?php if ($this->_tpl_vars['var']['display'] == 5): ?>
					<!-- 契約マスタ -->
					<a href="#" onClick="returnValue=Array('<?php echo $this->_tpl_vars['row_js'][$this->_tpl_vars['j']][0]; ?>
','<?php echo $this->_tpl_vars['row_js'][$this->_tpl_vars['j']][1]; ?>
','<?php echo $this->_tpl_vars['row_js'][$this->_tpl_vars['j']][3]; ?>
',true);window.close();"><?php echo $this->_tpl_vars['row_html'][$this->_tpl_vars['j']][2]; ?>
</a>
				<?php elseif ($this->_tpl_vars['var']['display'] == 'true'): ?>
					<!-- 入金入力 -->
					<a href="#" onClick="returnValue=Array('<?php echo $this->_tpl_vars['row_js'][$this->_tpl_vars['j']][0]; ?>
','<?php echo $this->_tpl_vars['row_js'][$this->_tpl_vars['j']][1]; ?>
','<?php echo $this->_tpl_vars['row_js'][$this->_tpl_vars['j']][3]; ?>
',true,<?php echo $this->_tpl_vars['row_js'][$this->_tpl_vars['j']][6]; ?>
);window.close();"><?php echo $this->_tpl_vars['row_html'][$this->_tpl_vars['j']][2]; ?>
</a>
				<?php else: ?>
            		<a href="#" onClick="returnValue=Array('<?php echo $this->_tpl_vars['row_js'][$this->_tpl_vars['j']][0]; ?>
','<?php echo $this->_tpl_vars['row_js'][$this->_tpl_vars['j']][1]; ?>
','<?php echo $this->_tpl_vars['row_js'][$this->_tpl_vars['j']][3]; ?>
');window.close();"><?php echo $this->_tpl_vars['row_html'][$this->_tpl_vars['j']][2]; ?>
</a>
				<?php endif; ?>
            <?php else: ?>
            <?php echo $this->_tpl_vars['row_html'][$this->_tpl_vars['j']][2]; ?>

            <?php endif; ?>
        </td>
        </td>
        <td><?php echo $this->_tpl_vars['row_html'][$this->_tpl_vars['j']][3]; ?>
</td>
        <td align="center"><?php echo $this->_tpl_vars['row_html'][$this->_tpl_vars['j']][4]; ?>
</td>
        <td align="center"><?php echo $this->_tpl_vars['row_html'][$this->_tpl_vars['j']][7]; ?>
</td>
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
        </td>
    </tr>
    
</table>

<?php echo $this->_tpl_vars['var']['html_footer']; ?>

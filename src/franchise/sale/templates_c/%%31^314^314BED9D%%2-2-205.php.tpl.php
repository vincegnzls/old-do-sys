<?php /* Smarty version 2.6.14, created on 2010-02-25 13:53:17
         compiled from 2-2-205.php.tpl */ ?>

<?php echo $this->_tpl_vars['var']['html_header']; ?>


<body bgcolor="#D8D0C8">
<form <?php echo $this->_tpl_vars['form']['attributes']; ?>
>
<?php echo $this->_tpl_vars['form']['hidden']; ?>

<table border="0" width="100%" height="90%" class="M_Table">

	<tr align="center" height="60">
		<td width="100%" colspan="2" valign="top">
			 <?php echo $this->_tpl_vars['var']['page_header']; ?>
 		</td>
	</tr>

	<tr align="center">
		<td valign="top">
		
			<table>
				<tr>
					<td>

<table width='550'>
	<tr>
        <td align="center">
<span style="color: #ff0000; font-weight: bold; line-height: 130%;">
        <?php if ($this->_tpl_vars['var']['message'] != null): ?><li><?php echo $this->_tpl_vars['var']['message']; ?>
<br><?php endif; ?>
</span>
        </td>
    </tr>
    <tr>
		<td align='center'>
			<?php echo $this->_tpl_vars['form']['form_close_button']['html']; ?>

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

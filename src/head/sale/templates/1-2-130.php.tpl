{$var.html_header}

<!-- styleseet -->
<style type="text/css">

	/** ����� **/
	td.total {ldelim}
		height: 24px;
		background-color:  #FFBBC3;
		border-color: #B9B9B8;
	{rdelim}

	/** ����åפ��Ȥι�� **/
	td.sub {ldelim}
		height: 24px;
		border-color: #B9B9B8;
		background-color: #99CCFF;
	{rdelim}

</style>

<body class="bgimg_purple">
<form {$form.attributes}>
{$form.hidden}

{*+++++++++++++++ ���� begin +++++++++++++++*}
<table width="100%" height="90%" class="M_table">

    {*+++++++++++++++ �إå��� begin +++++++++++++++*}
    <tr align="center" height="60">
        <td width="100%" colspan="2" valign="top">{$var.page_header}</td>
    </tr>
    {*--------------- �إå��� e n d ---------------*}

    {*+++++++++++++++ ����ƥ���� begin +++++++++++++++*}
    <tr align="center" valign="top">
        <td>
            <table>
                <tr>
                    <td>

{*+++++++++++++++ ��å������� begin +++++++++++++++*}
{* ɽ�����¤Τ߻��Υ�å����� *} 
{if $var.auth_r_msg != null}
    <span style="color: #ff0000; font-weight: bold; line-height: 130%;">
    <li>{$var.auth_r_msg}</li>
    </span><br>
{/if}
{*--------------- ��å������� e n d ---------------*}

{*+++++++++++++++ ����ɽ���� begin +++++++++++++++*}
<table  class="Data_Table" border="1" width="750">
    <tr>
        <td class="Title_Pink" width="100"><b>����</b></td>
        <td class="Value" align="left" colspan="3">{$form.form_stat_check.html}</td>
    </tr>
    <tr>
        <td class="Title_Pink" width="100"><b>��󥿥��ֹ�</b></td>
        <td class="Value" align="left">{$form.form_rental_no.html}</td>
        <td class="Title_Pink" width="100"><b>����ʬ��</b></td>
        <td class="Value" align="left">{$form.form_g_product_id.html}</td>
    </tr>
	<tr>
      	<td class="Title_Pink" width="110"><b>����åץ�����</b></td>
       	<td class="Value" align="left">{$form.form_client.html}</td>
		<td class="Title_Pink" width="110"><b>����å�̾</b></td>
       	<td class="Value" align="left">{$form.form_fc.html}</td>
    </tr>
	<tr>
      	<td class="Title_Pink" width="100"><b>���ʥ�����</b></td>
       	<td class="Value" align="left">{$form.form_goods_cd.html}</td>
		<td class="Title_Pink" width="100"><b>����̾</b></td>
       	<td class="Value" align="left">{$form.form_goods_cname.html}</td>
    </tr>
</table>
<table width="750">
    <tr>
        <td align="right">{$form.form_show_button.html}����{$form.form_clear_button.html}</td>
    </tr>
</table>
{if $var.display_flg == true}
<br>
<table class="List_Table" border="1" width="650">
    <tr align="center" style="font-weight: bold;">
        <td class="total" width="162">����å׿�</td>
        <td class="total" width="162">��󥿥�����</td>
        <td class="total" width="162">������������</td>
        <td class="total" width="162">����å�������</td>
    </tr>
    <tr>
        <td class="Value" align="right">{$var.total_shop}</td>
        <td class="Value" align="right">{$var.total_num}</td>
        <td class="Value" align="right">{$var.total_rental}</td>
        <td class="Value" align="right">{$var.total_user}</td>
    </tr>
</table>
<br><hr><br>
{/if}

{foreach from=$disp_data2 key=i item=item}
	<table class="List_Table" border="1" width="650">
	    <tr align="center" style="font-weight: bold;">
	        <td class="sub" width="162">����å�̾</td>
	        <td class="sub" width="162">��󥿥��׿�</td>
	        <td class="sub" width="162">���������</td>
	        <td class="sub" width="162">����å��󶡳�</td>
	    </tr>
	    <tr>
	        <td class="Value">{$disp_data2[$i][0]}<br>{$disp_data2[$i][1]}</td>
	        <td class="Value" align="right">{$disp_data2[$i][2]}</td>
	        <td class="Value" align="right">{$disp_data2[$i][3]}</td>
	        <td class="Value" align="right">{$disp_data2[$i][4]}</td>
	    </tr>
	</table>
	<br>
	<table class="List_Table" border="1" width="100%">
	    <tr align="center" style="font-weight: bold;">
	        <td class="Title_Purple"><b>No.</b></td>
	        <td class="Title_Purple"><b>������</b></td>
	        <td class="Title_Purple"><b>��󥿥��ֹ�</b></td>
	        <td class="Title_Purple"><b>�в���</b></td>
	        <td class="Title_Purple"><b>������</b></td>
	        <td class="Title_Purple"><b>����</b></td>
	        <td class="Title_Purple"><b>����̾</b></td>
	        <td class="Title_Purple"><b>����</b></td>
	        <td class="Title_Purple"><b>���ꥢ��</b></td>
			<td class="Title_Purple"><b>������������ñ��<br>����å���ñ��</b></td>
			<td class="Title_Purple"><b>��������������<br>����å��󶡶��</b></td>
			<td class="Title_Purple"><b>����</b></td>
	    </tr>
	   
		{$html[$i]}

	</table>
<br><hr><br>
{/foreach}


{*--------------- ����ɽ���� e n d ---------------*}

                    </td>
                </tr>
            </table>
        </td>
    </tr>
    {*--------------- ����ƥ���� e n d ---------------*}

</table>
{*--------------- ���� e n d ---------------*}

{$var.html_footer}
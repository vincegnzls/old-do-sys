{$var.html_header}

<body bgcolor="#D8D0C8">
<form name="dateForm" method="post">
{$form.hidden}

{*+++++++++++++++ ���� begin +++++++++++++++*}
<table width="100%" height="90%" class="M_table">

    {*+++++++++++++++ �إå��� begin +++++++++++++++*}
    <tr align="center" height="60">
        <td width="100%" colspan="2" valign="top">{$var.page_header}</td>
    </tr>
    {*--------------- �إå��� e n d ---------------*}

    {*+++++++++++++++ ����ƥ���� begin +++++++++++++++*}
    {if $var.freeze_flg == true}
    	<tr align="center" valign="top" height="160">
	{else}
		<tr align="center" valign="top">
	{/if}
        <td>
            <table>
                <tr>
                    <td>

<!-- ��Ͽ��ǧ��å�����ɽ�� -->
{if $var.freeze_flg == true}
    {if $smarty.get.del_buy_flg == true}
    <span style="color: #ff0000; font-weight: bold; line-height: 130%;">
        <li>��ɼ���������Ƥ��뤿�ᡢ�ѹ��Ǥ��ޤ���Ǥ�����<br>
    </span>
	<table width="100%">
	    <tr>
	        <td align="center">{$form.ok_button.html}</td>
	    </tr>
	</table>
    {elseif $smarty.get.del_ord_flg == true}
    <span style="color: #ff0000; font-weight: bold; line-height: 130%;">
    <li>ȯ�����������Ƥ��뤿�ᡢ��Ͽ�Ǥ��ޤ���Ǥ�����<br>
    <span>  
	<table width="100%">
	    <tr>
	        <td align="center">{$form.ok_button.html}</td>
	    </tr>
	</table>
    {elseif $smarty.get.change_ord_flg == true}
    <span style="color: #ff0000; font-weight: bold; line-height: 130%;">
    <li>ȯ�����ѹ����줿���ᡢ�������ϤǤ��ޤ���Ǥ�����<br>
    <span>  
	<table width="100%">
	    <tr>
	        <td align="center">{$form.ok_button.html}</td>
	    </tr>
	</table>
    {elseif $smarty.get.inst_err == true}
    <span style="color: #ff0000; font-weight: bold; line-height: 130%;">
    <li>�������ѹ����줿���ᡢ�������꤬�Ԥ��ޤ���Ǥ�����<br>
    <span>  
	<table width="100%">
	    <tr>
	        <td align="center">{$form.ok_button.html}</td>
	    </tr>
	</table>
    {elseif $smarty.get.ps_stat == true}
    <span style="color: #ff0000; font-weight: bold; line-height: 130%;">
    <li>���������˴�λ���Ƥ��뤿�ᡢ��Ͽ�Ǥ��ޤ���<br>
    <span>  
	<table width="100%">
	    <tr>
	        <td align="center">{$form.ok_button.html}</td>
	    </tr>
	</table>
    {else}
	<span style="font: bold;"><font size="+1">������λ���ޤ�����<br><br>
	</font></span>
	<table width="100%">
	    <tr>
	        <td align="center">{$form.ok_button.html}��{$form.form_split_button.html}��{$form.return_button.html}</td>
	    </tr>
	</table>
    {/if}
{else}

	{*+++++++++++++++ ����ɽ���� begin +++++++++++++++*}
	<table>
	    <tr>
	        <td>

	<table class="Data_Table" border="1">
	<col width="80" style="font-weight: bold;">
	<col>
	<col width="60" style="font-weight: bold;">
	<col>
	<col width="80" style="font-weight: bold;">
	<col>
	    <tr>
	        <td class="Title_Blue">��ɼ�ֹ�</td>
	        <td class="Value">{$form.form_buy_no.html}</td>
	        <td class="Title_Blue">������</td>
	        <td class="Value">{$form.form_client.html}��{$var.client_state_print}</td>
	        <td class="Title_Blue">ȯ���ֹ�</td>
	        <td class="Value">{$form.form_ord_no.html}</td>
	    </tr>
	    <tr>
	        <td class="Title_Blue">������</td>
	        <td class="Value">{$form.form_arrival_day.html}</td>
	        <td class="Title_Blue">�����ʬ</td>
	        <td class="Value">{$form.form_trade_buy.html}</td>
	        <td class="Title_Blue">������</td>
	        <td class="Value">{$form.form_buy_day.html}</td>
	    </tr>
	    <tr>
	        <td class="Title_Blue">�����Ҹ�</td>
	        <td class="Value">{$form.form_ware_name.html}</td>
	        <td class="Title_Blue">ľ����</td>
	        <td class="Value">{$form.form_direct_name.html}</td>
	        <td class="Title_Blue">ȯ��ô����</td>
	        <td class="Value">{$form.form_oc_staff_name.html}</td>
	    </tr>
	    <tr>
	        <td class="Title_Blue">����ô����</td>
	        <td class="Value">{$form.form_c_staff_name.html}</td>
	        <td class="Title_Blue">����</td>
	        <td class="Value" colspan="3">{$form.form_note.html}</td>
	    </tr>
	</table>

	        </td>
	    </tr>
	</table>
	<br>
	{*--------------- ����ɽ���� e n d ---------------*}

	                    </td>
	                </tr>
	                <tr>
	                    <td>

	{*+++++++++++++++ ����ɽ���� begin +++++++++++++++*}
	<table width="100%">
	    <tr>
	        <td>

	<table class="List_Table" border="1" width="100%">
	    <tr align="center" style="font-weight: bold;">
	        <td class="Title_Blue">No.</td>
	        <td class="Title_Blue">���ʥ�����<br>����̾</td>
	        <td class="Title_Blue">������</td>
	        <td class="Title_Blue">����ñ��</td>
	        <td class="Title_Blue">�������</td>    
	    </tr>
	    {foreach key=j from=$row item=items}
        {* aoyama-n 2009-09-18 *}
        {if $row[$j][5] === 't' || 
            $form.form_trade_buy.html == '������' || $form.form_trade_buy.html == '���Ͱ�' || 
            $form.form_trade_buy.html == '��������' || $form.form_trade_buy.html == '�����Ͱ�'}
	    <tr class="Result1" style="color: red">
        {else}
	    <tr class="Result1">
        {/if}
	        <td align="right">{$j+1}</td>
	        <td>{$row[$j][0]}<br>{$row[$j][1]}</td>
	        <td align="right">{$row[$j][2]}</td>
	        <td align="right">{$row[$j][3]}</td>
	        <td align="right">{$row[$j][4]}</td>
	    </tr>                                                                           
	    {/foreach}                                   
	</table>

	        </td>
	    </tr>
	    <tr>
	        <td>

	<table class="List_Table" border="1" align="right">
	    <tr>
	        <td class="Title_Blue" width="80" align="center"><b>��ȴ���</b></td>
	        <td class="Value" align="right">{$form.form_buy_total.html}</td>
	        <td class="Title_Blue" width="80" align="center"><b>������</b></td>
	        <td class="Value" align="right">{$form.form_buy_tax.html}</td>
	        <td class="Title_Blue" width="80" align="center"><b>�ǹ����</b></td>
	        <td class="Value" align="right">{$form.form_buy_money.html}</td>
	    </tr>
	</table>

	        </td>
	    </tr>
	    <tr>
	        <td>

    {if $var.act_sale_flg == true}
	<table class="List_Table" border="1" align="right">
	    <tr>
	        <td width="80" align="center" bgcolor="#FDFD88"><b>����ֹ�</b></td>
	        <td class="Value" align="right">{$form.form_act_sale_no.html}</td>
	        <td width="80" align="center" bgcolor="#FDFD88"><b>�������</b></td>
	        <td class="Value" align="right">{$form.form_act_sale_amount.html}</td>
	    </tr>
	</table>

	        </td>
	    </tr>
	    <tr>
	        <td>
    {/if}

	<table width="100%">
	    <tr>
	        <td align="right">
	        {if $var.input_flg != null}{$form.ok_button.html}��{/if}��{$form.form_split_button.html}��{$form.return_button.html}</td>
	    </tr>
	</table>

	        </td>
	    </tr>
	</table>
{/if}
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
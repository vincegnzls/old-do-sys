{$var.html_header}

<script language="javascript">
{$var.code_value}
 </script>

<body bgcolor="#D8D0C8">
<form {$form.attributes}>

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
{* ���顼��å��������� *} 
    <span style="color: #ff0000; font-weight: bold; line-height: 130%;">
    {if $form.form_goods_cd.error != null}
        <li>{$form.form_goods_cd.error}<br>
    {/if}
    {if $form.form_goods_name.error != null}
        <li>{$form.form_goods_name.error}<br>
    {/if}
    {if $form.form_goods_cname.error != null}
        <li>{$form.form_goods_cname.error}<br>
    {/if}
    {if $form.form_url.error != null}
        <li>{$form.form_url.error}<br>
    {/if}
    {if $form.form_g_goods_id.error != null}
        <li>{$form.form_g_goods_id.error}<br>
    {/if}
    {if $form.form_product_id.error != null}
        <li>{$form.form_product_id.error}<br>
    {/if}
	{if $form.form_g_product_id.error != null}
        <li>{$form.form_g_product_id.error}<br>
    {/if}
    {if $form.form_in_num.error != null}
        <li>{$form.form_in_num.error}<br>
    {/if}
    {if $form.form_supplier.error != null}
        <li>{$form.form_supplier.error}<br>
    {/if}
    {if $form.form_order_point.error != null}
        <li>{$form.form_order_point.error}<br>
    {/if}
    {if $form.form_order_unit.error != null}
        <li>{$form.form_order_unit.error}<br>
    {/if}
    {if $form.form_lead.error != null}
        <li>{$form.form_lead.error}<br>
    {/if}
    </span>
{*--------------- ��å������� e n d ---------------*}

{*+++++++++++++++ ����ɽ���� begin +++++++++++++++*}
<table width="520">
    <tr>
        <td>

<table width="100%">
    <tr>
        <td>{$form.form_show_dialog_button.html}</td>
        <td align="right">{if $smarty.get.goods_id != null}{$form.back_button.html}��{$form.next_button.html}{/if}
        </td>
    </tr>
</table>
<br>

<table>
    <tr>
        <td>
        <table class="Data_Table" border="1" width="280">
            <tr>
                <td class="Type" width="40" align="center" width="100"><b>����</b></td>
                <td class="Value">{$form.form_state.html}</td>
            </tr>
        </table>
        </td>
	</tr>
	<!-- �������ʤΤ�ɽ�� -->
	{if $var.head_flg == true}
		<tr>
	        <td>
	        <table class="Data_Table" border="1" width="280">
	            <tr>
	                <td class="Type" width="40" align="center" width="100"><b>RtoR</b></td>
	                <td class="Value">{$form.form_rental.html}</td>
	            </tr>
	        </table>
	        </td>
			<td>
	        <table class="Data_Table" border="1" width="280">
	            <tr>    
	                <td class="Type" align="center" width="100"><b>���ꥢ�����</b></td>
	                <td class="Value">{$form.form_serial.html}</td>
	            </tr>   
	        </table>
	        </td>   
	    </tr>
	{/if}
</table>

        </td>
    </tr>
    <tr>
        <td>

<table class="Data_Table" border="1" width="100%">
<col width="130" style="font-weight: bold;">
    <tr>
        <td class="Title_Purple">���ʥ�����<font color="#ff0000">��</font></td>
        <td class="Value">
        <table>
            <tr>
                <td rowspan="2">{$form.form_goods_cd.html}</td>
                <td><font color=#555555>�壱�夬����������</font></td>
            </tr>
            <tr>
                <td><font color=#555555>�壱�夬���ʳ��ϥ���å���</font></td>
            </tr>
        </table>
        </td>
    </tr>
	<tr>
        <td class="Title_Purple">�Ͷ�ʬ</a><font color="#ff0000">��</font></td>
        <td class="Value">
            {if $var.head_flg == true}
                {$form.form_g_goods_name.html}
            {else}
                {$form.form_g_goods_id.html}
            {/if}
        </td>
    </tr>
    <tr>
        <td class="Title_Purple">������ʬ</a><font color="#ff0000">��</font></td>
        <td class="Value">
            {if $var.head_flg == true}
                {$form.form_product_name.html}
            {else}
                {$form.form_product_id.html}
            {/if}
        </td>
    </tr>
	<tr>
        <td class="Title_Purple">����ʬ��</a><font color="#ff0000">��</font></td>
        <td class="Value">
            {if $var.head_flg == true}
                {$form.form_g_product_name.html}
            {else}
                {$form.form_g_product_id.html}
            {/if}
        </td>
    </tr>
    <tr>
        <td class="Title_Purple">����̾<font color="#ff0000">��</font></td>
        <td class="Value">{$form.form_goods_name.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">ά��<font color="#ff0000">��</font></td>
        <td class="Value">{$form.form_goods_cname.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">°����ʬ<font color="#ff0000">��</font></td>
        <td class="Value">
            {if $var.head_flg == true}
                {$form.form_attri_name.html}
            {else}
                {$form.form_attri_div.html}
            {/if}
        </td>
    </tr>
	<tr>
        <td class="Title_Purple">{$form.form_album_link.html}</td>
        <td class="Value"></td>
    </tr>
    <tr>
        <td class="Title_Purple">�ޡ���<font color="#ff0000">��</font></td>
        <td class="Value">
            {if $var.head_flg == true}
                {$form.form_mark_name.html}
            {else}
                {$form.form_mark_div.html}
            {/if}
        </td>
    </tr>
    <tr>
        <td class="Title_Purple">ñ��</td>
        <td class="Value">{$form.form_unit.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">����</td>
        <td class="Value">{$form.form_in_num.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">{$form.form_client_link.html}</td>
        <td class="Value">{$form.form_supplier.html}</td>
    </tr>
</table>

        </td>
    </tr>
    <tr>
        <td>

<table class="Data_Table" border="1" width="100%">
<col width="130" style="font-weight: bold;">
    <tr>
        <td class="Title_Purple">�������<font color="#ff0000">��</font></td>
        <td class="Value">
            {if $var.head_flg == true}
                {$form.form_sale_manage_name.html}
            {else}
                {$form.form_sale_manage.html}
            {/if}
        </td>
    </tr>
    <tr>
        <td class="Title_Purple">�߸˴���<font color="#ff0000">��</font></td>
        <td class="Value">
            {* 2009-10-08 hashimoto-y *}
            {$form.form_stock_manage.html}
        </td>
    </tr>
    <tr>
        <td class="Title_Purple">�߸˸¤���</td>
        <td class="Value">
            {if $var.head_flg == true}
                {$form.form_stock_only_name.html}
            {else}
                {$form.form_stock_only.html}
            {/if}
        </td>
    </tr>
</table>

        </td>
    </tr>
    <tr>
        <td>

<table class="Data_Table" border="1" width="100%">
<col width="130" style="font-weight: bold;">
    <tr>
        <td class="Title_Purple">ȯ����</td>
        <td class="Value">{$form.form_order_point.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">ȯ��ñ�̿�</td>
        <td class="Value">{$form.form_order_unit.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">�꡼�ɥ�����</td>
        <td class="Value">{$form.form_lead.html}��</td>
    </tr>
</table>

        </td>
    </tr>
    <tr>
        <td>

<table class="Data_Table" border="1" width="100%">
<col width="130" style="font-weight: bold;">
    <tr>
        <td class="Title_Purple">��̾�ѹ�<font color="#ff0000">��</font></td>
        <td class="Value">
            {if $var.head_flg == true}
                {$form.form_name_change_name.html}
            {else}
                {$form.form_name_change.html}
            {/if}
        </td>
    </tr>
    <tr>
        <td class="Title_Purple">���Ƕ�ʬ<font color="#ff0000">��</font></td>
        <td class="Value">
            {if $var.head_flg == true}
                {$form.form_tax_div_name.html}
            {else}
                {$form.form_tax_div.html}
            {/if}
        </td>
    </tr>
</table>

        </td>
    </tr>
    <tr>
        <td>

<table class="Data_Table" border="1" width="100%">
<col width="130" style="font-weight: bold;">
    <tr>
        <td class="Title_Purple">�ǿ������</td>
        <td class="Value">{$var.sale_day}</td>
    </tr>
    <tr>
        <td class="Title_Purple">�ǿ�������</td>
        <td class="Value">{$var.buy_day}</td>
    </tr>
</table>

        </td>
    </tr>
    <tr>
        <td>

<table class="Data_Table" border="1" width="100%">
<col width="130" style="font-weight: bold;">
    <tr>
        <td class="Title_Purple">����</td>
        <td class="Value">{$form.form_note.html}</td>
    </tr>
</table>

<table width="100%">
    <tr>
        <td><b><font color="#ff0000">����ɬ�����ϤǤ�</font></b></td>
        <td align="right">{$form.form_entry_button.html}��{$form.form_back_button.html}</td>
    </tr>
</table>

        </td>
    </tr>
</table>
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
{$var.html_header}

<script language="javascript">
{$var.order_sheet}
 </script>

<body bgcolor="#D8D0C8" {$var.load}>
<form name="dateForm" method="post">

{*+++++++++++++++ ���� begin +++++++++++++++*}
<table width="100%" height="90%" class="M_table">

    {*+++++++++++++++ ����ƥ���� begin +++++++++++++++*}
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
{if $var.warning != null}
    <span style="color: #ff0000; font-weight: bold; line-height: 130%;">{$var.warning}</span>
{/if}
{*--------------- ��å������� e n d ---------------*}

{*+++++++++++++++ ����ɽ���� begin +++++++++++++++*}
<table>
    <tr>
        <td>

{*
<table class="Data_Table" border="1" width="650">
<col width="110" style="font-weight: bold;">
<col>
<col width="110" style="font-weight: bold;">
<col>
    <tr>
        <td class="Title_Blue">ȯ���ֹ�</td>
        <td class="Value" colspan="3">{$form.form_ord_no.html}</td>
    </tr>
    <tr>
        <td class="Title_Blue">ȯ����</td>
        <td class="Value" colspan="3">{$form.form_client.html}</td>
    </tr>
    <tr>
        <td class="Title_Blue">ȯ����</td>
        <td class="Value">{$form.form_ord_time.html}</td>
        <td class="Title_Blue">��˾Ǽ��</td>
        <td class="Value">{$form.form_hope_day.html}</td>
    </tr>
    <tr>
        <td class="Title_Blue">�����ȼ�</td>
        <td class="Value" colspan="3">{$form.form_trans.html}</td>
    </tr>
    <tr>
        <td class="Title_Blue">ľ����</td>
        <td class="Value">{$form.form_direct_name.html}</td>
        <td class="Title_Blue">�����Ҹ�</td>
        <td class="Value">{$form.form_ware_name.html}</td>
    </tr>
    <tr>
        <td class="Title_Blue">�����ʬ</a></td>
        <td class="Value">{$form.form_trade_ord.html}</td>
        <td class="Title_Blue">ô����</td>
        <td class="Value">{$form.form_c_staff_name.html}</td>
    </tr>
    <tr>
        <td class="Title_Blue" >�̿���<br>�ʻ����谸��</td>
        <td class="Value" colspan="3">{$form.form_note_my.html}</td>
    </tr>
    <tr>
        <td class="Title_Blue">�̿���<br>����������</td>
        <td class="Value" colspan="3">{$form.form_note_your.html}</td>
    </tr>
</table>
<br>
*}

<table class="Data_Table" border="1">
<col width="110" style="font-weight: bold;">
<col>
<col width="60" style="font-weight: bold;">
<col>
<col width="80" style="font-weight: bold;">
<col>
    <tr>
        <td class="Title_Blue">ȯ���ֹ�</td>
        <td class="Value">{$form.form_ord_no.html}</td>
        <td class="Title_Blue">ȯ����</td>
        <td class="Value">{$form.form_client.html}</td>
        <td class="Title_Blue">ȯ����</td>
        <td class="Value">{$form.form_ord_time.html}</td>
    </tr>
    <tr>
        <td class="Title_Blue">�����ȼ�</td>
        <td class="Value">{$form.form_trans.html}</td>
        <td class="Title_Blue">ľ����</td>
        <td class="Value">{$form.form_direct_name.html}</td>
        <td class="Title_Blue">��˾Ǽ��</td>
        <td class="Value">{$form.form_hope_day.html}</td>
    </tr>
    <tr>
        <td class="Title_Blue">�����Ҹ�</td>
        <td class="Value">{$form.form_ware_name.html}</td>
        <td class="Title_Blue">ô����</td>
        <td class="Value">{$form.form_c_staff_name.html}</td>
        <td class="Title_Blue">�����ʬ</a></td>
        <td class="Value">{$form.form_trade_ord.html}</td>
    </tr>
    {if $var.note_my != ''}
    <tr>
        <td class="Title_Blue" >�̿���<br>�ʻ����谸��</td>
        <td class="Value" colspan="5">{$form.form_note_my.html}</td>
    </tr>
    {/if}
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

{$form.hidden}
<table class="List_Table" border="1" width="100%">

    {* ���ܽ��� *} 
    {foreach key=j from=$item item=items}
        <tr align="center">
        {foreach key=i from=$items item=item}
            <td class="Title_Blue">{$item}</td>
        {/foreach}
        </tr>
    {/foreach}                          

    {* �ǡ������� *} 
    {foreach key=j from=$row item=items}
        {$row[$j][0]}
            {if $row[$j][1] == "���"}
                <td>{$row[$j][1]}</td>
                <td>{$row[$j][2]}</td>
            {else}
                <td align="right">{$j+1}</td>
                <td>{$row[$j][1]}<br>{$row[$j][2]}</td>
            {/if}
            <td align="right">{$row[$j][3]}</td>
            <td align="right">{$row[$j][4]}</td>
            <td align="right">{$row[$j][5]}</td>
            {if $row[$j][6] != null}
                {if $row[$j][6]|regex_replace:"/.*-.*/":"-"}
                    <td align="center">{$row[$j][6]}</td>
                {else}
                    <td align="right">{$row[$j][6]}</td>
                {/if}
            {/if}
            {if $row[$j][7] != null}
                <td align="right">{$row[$j][7]}</td>
            {/if}
            {if $row[$j][8] != null}
                {if $row[$j][9] != null}
                    <td align="right">{$row[$j][8]}</td>
                {else}
                    <td>{$row[$j][8]}</td>
                {/if}
            {/if}
            {if $row[$j][9] != null}
                <td>{$row[$j][9]}</td>
            {/if}
        </tr>                                                                                             
    {/foreach}                            
</table>

        </td>
    </tr>
    <tr>
        <td>

<table width="100%">
    <tr>
        <td align="right">
        <table class="List_Table" border="1">
            <tr>
            <td class="Title_Pink" width="70" align="center">��ȴ���</td>
            <td class="Value" align="right">{$form.form_buy_total.html}</td>
            <td class="Title_Pink" width="70" align="center">������</td>
            <td class="Value" align="right">{$form.form_buy_tax.html}</td>
            <td class="Title_Pink" width="70" align="center">�ǹ����</td>
            <td class="Value" align="right">{$form.form_buy_money.html}</td>
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
        <td align="right">{$form.order_button.html}��
        {if $var.warning != null || $var.offline_flg != null}
            {$form.ok_button.html}��
        {/if}
        {if $var.warning == null}
            {$form.return_button.html}
        {/if}
        </td>
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
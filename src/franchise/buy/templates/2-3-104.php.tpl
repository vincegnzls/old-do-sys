{$var.html_header}

<script language="javascript">
{$html.js}
 </script>

<body bgcolor="#D8D0C8">
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
{* ���顼��å����� *} 
<span style="color: #ff0000; font-weight: bold; line-height: 130%;">
{if $form.form_c_staff.error != null}
    <li>{$form.form_c_staff.error}
{/if}
{if $form.form_ord_day.error != null}
    <li>{$form.form_ord_day.error}
{/if}
{if $form.form_multi_staff.error != null}
    <li>{$form.form_multi_staff.error}
{/if}
{if $form.form_hope_day.error != null}
    <li>{$form.form_hope_day.error}
{/if}
{if $form.err_bought_slip.error != null}
    <li>{$form.err_bought_slip.error}
{/if}
</span>
{*--------------- ��å������� e n d ---------------*}

{*+++++++++++++++ ����ɽ���� begin +++++++++++++++*}
<table>
    <tr>
        <td>{$html.html_s}</td>
    </tr>
</table>
{*--------------- ����ɽ���� e n d ---------------*}

                    </td>
                </tr>
                <tr>
                    <td>

{*+++++++++++++++ ����ɽ���� begin +++++++++++++++*}
{if $var.post_flg == true && $var.err_flg != true}

<table>
    <tr>
        <td>
    <span style="color: #0000ff; font-weight: bold; line-height: 130%;">
         ��ȯ��������ˤĤ��Ƥϥ���饤���ɬ�פ���ޤ���
    </span>
{$html.html_page}
<table class="List_Table" border="1" width="100%">
    <tr align="center" style="font-weight: bold;">
        <td class="Title_Blue">No.</td>
        <td class="Title_Blue">{Make_Sort_Link_Tpl form=$form f_name="sl_ord_day"}</td>
        <td class="Title_Blue">{Make_Sort_Link_Tpl form=$form f_name="sl_slip"}</td>
        <td class="Title_Blue">
            {Make_Sort_Link_Tpl form=$form f_name="sl_client_cd"}<br>
            <br style="font-size: 4px;">
            {Make_Sort_Link_Tpl form=$form f_name="sl_client_name"}<br>
        </td>
        <td class="Title_Blue">������ǧ</td>
        <td class="Title_Blue">ȯ�����</td>
        {* <td class="Title_Blue">��˾Ǽ��</td> *}
        <td class="Title_Blue">{Make_Sort_Link_Tpl form=$form f_name="sl_hope_day"}</td>
        <td class="Title_Blue">�̿���<br>�ʣƣð���</td>
        <td class="Title_Blue">������λ</td>
        <td class="Title_Blue">���</td>
        <td class="Title_Blue">ȯ�������</td>
    </tr>
    {foreach key=j from=$row item=items}
    {if $j is even}
    <tr class="Result1"> 
    {else}
    <tr class="Result2">
    {/if}
        <td align="right">
            {$var.no+$j+1}
        </td>
        {*�����褬FC�ΤФ����ϻ��֤�ɽ�����ʤ�*}
        <td align="center">{$row[$j][1]}<br>{if $row[$j][5] != null}{$row[$j][2]}{/if}</td>
            {* ��ä�NULL����̤�����λ����ѹ��� ����󥿥������ѹ��Բ�*} 
            {if $row[$j][5] == '3' || ($row[$j][5] == null && $row[$j][8] == '1') && $row[$j][14] == 'f'}
                <td align="center"><a href="2-3-102.php?ord_id={$row[$j][0]}">{$row[$j][3]}</a></td>
            {else}
                <td align="center"><a href="2-3-103.php?ord_id={$row[$j][0]}&ord_flg=true">{$row[$j][3]}</a></td>
            {/if}
        <td>{$row[$j][11]}<br>{$row[$j][4]}</td>
            {if $row[$j][5] == '1'}
        <td align="center">
                <font color="#ff0000">̤��ǧ</font>
        </td>
            {elseif $row[$j][5] == '2'}
        <td align="center">
                ��
        </td>
            {elseif $row[$j][5] == '3'}
        <td align="center">
                <font color="#008000">���</font>
        </td>
            {else}
        <td align="center">
                ----
        </td>
            {/if}
        <td align="right">
            {$row[$j][6]|number_format}
        </td>
        <td align="center">{$row[$j][15]}</td>
        <td align="left">{$row[$j][16]}</td>
        <td align="center">
            {if $row[$j][8] == 3 || $row[$j][8] == 4}
                ��
            {else}
                <font color="#ff0000">̤</font>
            {/if}
        </td>
        <td align="center">
            {if $row[$j][5] == '3' || $row[$j][5] == null && $row[$j][8] == '1'}
                {if $var.auth == "w"}<a href="#" onClick="Order_Delete('data_delete_flg','ord_id_flg',{$row[$j][0]},'hdn_del_enter_date','{$row[$j][13]}');">���</a>{/if}
            {/if}
        </td>
        <td align="center">
            {*����饤��ȯ�� AND ���ä����֤Ǥʤ� AND ȯ����ȯ�ԺѤ�*}
            {if $row[$j][7] == 't' && $row[$j][5] != '3' && $row[$j][10] == 't'}
                {if $var.auth == "w"}<a href="#" onClick="window.open('../../franchise/buy/2-3-105.php?ord_id={$row[$j][0]}','_blank','');">����</a>{/if}

            {*���ե饤��ȯ�� AND ȯ����ȯ�ԺѤ� *}
            {elseif $row[$j][7] == 't' &&  $row[$j][10] == 'f'}
                {if $var.auth == "w"} <a href="#" onClick="window.open('../../franchise/buy/2-3-107.php?ord_id={$row[$j][0]}','_blank','');">����</a>{/if}

            {*ȯ����̤ȯ�� AND ���ä����֤Ǥʤ� AND ����饤��ȯ��*}
            {elseif $row[$j][7] == 'f' && $row[$j][5] != '3' && $row[$j][10] == 't'}
                <a href="#" onClick="Order_Sheet('order_sheet_flg','ord_id_flg',2,{$row[$j][0]},{$row[$j][8]});">����</a>

            {*ȯ����̤ȯ�� AND ���ե饤��ȯ��*}
            {elseif $row[$j][7] == 'f' && $row[$j][10] == 'f'}
                <a href="#" onClick="Order_Sheet('order_sheet_flg','ord_id_flg',1,{$row[$j][0]},{$row[$j][8]});">����</a>

            {*������ȯ������ä������ϥ�󥯤���ɽ��*}
            {else}
            {/if}
        </td>
    </tr>
    {/foreach}
</table>
{$html.html_page2}

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
{$var.html_header}

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

{*+++++++++++++++ ����ɽ���� begin +++++++++++++++*}
<table width="800">
    <tr>
        <td>

<table class="Data_Table" border="1" width="100%">
<col width="110" style="font-weight: bold;">
<col>
<col width="120" style="font-weight: bold;">
    <tr>
        <td class="Title_Purple">���Ϸ���</td>
        <td class="Value">{$form.form_output_type.html}</td>
        <td class="Title_Purple">ɽ�����</td>
        <td class="Value">{$form.form_show_page.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">����åץ�����</td>
        <td class="Value">{$form.form_shop.html}</td>
        <td class="Title_Purple">����å�̾</td>
        <td class="Value">{$form.form_shop_name.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">FC��������ʬ</td>
        <td class="Value">{$form.form_rank.html}</td>
        <td class="Title_Purple">����å� ����</td>
        <td class="Value" >{$form.form_state_type_s.html}</td>
    </tr>

    <tr>
        <td class="Title_Purple">�����襳����</td>
        <td class="Value">{$form.form_client.html}</td>
        <td class="Title_Purple" width="115">������̾��ά��</td>
        <td class="Value">{$form.form_client_name.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">�϶�</td>
        <td class="Value">{$form.form_area_id.html}</td>
        <td class="Title_Purple">TEL</td>
        <td class="Value">{$form.form_tel.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">�ȼ�</td>
        <td class="Value">{$form.form_btype.html}</td>
        <td class="Title_Purple">������ ����</td>
        <td class="Value">{$form.form_state_type.html}</td>

    </tr>

</table>

<table align="right">
    <tr>
        <td>{$form.form_button.show_button.html}����{$form.form_button.clear_button.html}</td>
    </tr>
</table>

        </td>
    </tr>
</table>
{*{/if}*}
<br>
{*--------------- ����ɽ���� e n d ---------------*}

                    </td>
                </tr>

                <tr>
                    <td>

{*+++++++++++++++ ����ɽ���� begin +++++++++++++++*}
{$form.hidden}

{if $var.display_flg == true}

<table width="100%">
    <tr>
        <td>
{*
{if $smarty.post.form_show_page == 2 && $smarty.post.form_button.show_button == "ɽ����" }

       <b style="font-size: 15px; color: #555555;"> �������� ���֡� {$var.state_type}��</b><br>��<b>{$var.match_count}</b>��
{else}*}
    <b style="font-size: 15px; color: #555555;">�������� ���֡� {$var.state_type}��</b>
    {$var.html_page}
{*{/if}*}

<table class="List_Table" border="1" width="100%">
    <tr align="center" style="font-weight: bold;" nowrap>
        <td class="Title_Purple">No.</td>
        <td class="Title_Purple">{Make_Sort_Link_Tpl form=$form f_name="sl_rank"}</td>
        <td class="Title_Purple">
            {Make_Sort_Link_Tpl form=$form f_name="sl_shop_cd"}<br>
            <br style="font-size: 4px;">
            {Make_Sort_Link_Tpl form=$form f_name="sl_shop_name"}<br>
        </td>
        <td class="Title_Purple">
            {Make_Sort_Link_Tpl form=$form f_name="sl_client_cd"}<br>
            <br style="font-size: 4px;">
            {Make_Sort_Link_Tpl form=$form f_name="sl_client_name"}<br>
        </td>
        <td class="Title_Purple">ά��</td>
        <td class="Title_Purple">�϶�</td>
        <td class="Title_Purple">TEL</td>
        {if $smarty.post.form_state_type == '4'}
        <td class="Title_Purple">����</td>
        {/if}
        <td class="Title_Purple">�����襳����<br>������</td>
{*
        <td class="Title_Purple">�����ͥ�����</td>
        <td class="Title_Purple">��������</td>
*}
    </tr>
    {foreach key=j from=$row item=items}
    <tr class={$tr[$j]}> 
        <td align="right">
			{if $smarty.post.form_button.show_button == "ɽ����"}
            ��  {$j+1}
            {elseif $smarty.post.form_show_page != 2 && $smarty.post.f_page1 != ""}
                {$smarty.post.f_page1*100+$j-99}
            {else if}
            ��  {$j+1}
            {/if}
        </td>
        <td align="center">{$row[$j][20]}</td>
        <td align="left">{$row[$j][12]}-{$row[$j][13]}<br>{$row[$j][9]}</td>
        <td align="left">
            {$row[$j][1]}-{$row[$j][2]}<br>
            <a href = "1-1-115.php?client_id={$row[$j][0]}">{$row[$j][3]}</a></td>
        </td>
        <td align="left">{$row[$j][4]}</td>
        <td align="center">{$row[$j][5]}</td>
        <td align="left">{$row[$j][6]}</td>
        {if $smarty.post.form_state_type == '4'}
        <td align="center">
        {if $row[$j][7] == 1}
            �����
        {else if}
            �ٻ���
        {/if}
        </td>
        {/if}
        <td>{$row[$j][14]}-{$row[$j][15]}<br>{$row[$j][10]}<br>
        {if $row[$j][16] != ''}
            {$row[$j][16]}-{$row[$j][17]}<br>{$row[$j][18]}
        {/if}
        </td>
{*
        <td align="center"><a href="javascript:WindowOpen('1-1-114.php?id={$row[$j][0]}',1024,768,'output');">����</a></td>
        <td align="center"><a href="1-1-123.php?id={$row[$j][0]}">�ѹ�</a></td>
*}
    </tr>
    {/foreach}

</table>
{if $smarty.post.form_show_page != 2 }
{$var.html_page2}
{/if}

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
{$var.html_header}

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
<span style="color: #ff0000; font-weight: bold; line-height: 130%;">
{if $form.form_c_staff.error != null}
    <li>{$form.form_c_staff.error}<br>
{/if}
{if $form.form_multi_staff.error != null}
    <li>{$form.form_multi_staff.error}<br>
{/if}
{if $form.form_ord_day.error != null}
    <li>{$form.form_ord_day.error}<br>
{/if}
{if $form.form_arrival_day.error != null}
    <li>{$form.form_arrival_day.error}<br>
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

{$var.html_page}
<table class="List_Table" border="1" width="100%">
    <tr align="center" style="font-weight: bold;">
        <td class="Title_Pink">No.</td>
        <td class="Title_Pink">
            {Make_Sort_Link_Tpl form=$form f_name="sl_client_cd"}<br>
            <br style="font-size: 4px;">
            {Make_Sort_Link_Tpl form=$form f_name="sl_client_name"}<br>
        </td>
        <td class="Title_Pink">{Make_Sort_Link_Tpl form=$form f_name="sl_h_aord_no"}</td>
        <td class="Title_Act">{Make_Sort_Link_Tpl form=$form f_name="sl_fc_ord_no"}</td>
        <td class="Title_Pink">{Make_Sort_Link_Tpl form=$form f_name="sl_direct"}</td>
        <td class="Title_Pink">{Make_Sort_Link_Tpl form=$form f_name="sl_aord_day"}</td>
        <td class="Title_Pink">{Make_Sort_Link_Tpl form=$form f_name="sl_arrival_day"}</td>
        <td class="Title_Pink">����</td>
        <td class="Title_Pink">������</td>
        <td class="Title_Pink">�вٿ�</td>
        <td class="Title_Pink">������</td>
        <td class="Title_Pink">�в��Ҹ�</td>
        <td class="Title_Pink">�������</td>
    </tr>
    {$html.html_l}
</table>
{$var.html_page2}

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
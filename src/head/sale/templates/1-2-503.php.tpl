{$var.html_header}

<body bgcolor="#D8D0C8">
<form name="dateForm" method="post">
{$form.hidden}

{*+++++++++++++++ ���� begin +++++++++++++++*}
<table width="100%" height="90%" class="M_Table">

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
<ul style="margin-left: 16px;">
{if $form.form_client.error != null}
    <li>{$form.form_client.error}<br>
{/if}
{if $form.form_count_day.error != null}
    <li>{$form.form_count_day.error}<br>
{/if}
</ul>
</span>
{*--------------- ��å������� e n d ---------------*}

{*+++++++++++++++ ����ɽ���� begin +++++++++++++++*}
<table>
    <tr>    
        <td>    

<table class="Data_Table" border="1" width="400">
<col width="100" style="font-weight: bold;">
<col>
    <tr>
        <td class="Title_Pink">{$form.form_client_link.html}<font color="#ff0000">��</font></td>
        <td class="Value">{$form.form_client.html}</td>
    </tr>
    <tr>
        <td class="Title_Pink">���״���<font color="#ff0000">��</font></td>
        <td class="Value">{$form.form_count_day.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">���Ϸ���</td>
        <td class="Value">{$form.form_output.html}</td>
    </tr>
</table>

<table width="400">
    <tr align="right">
        <td>{$form.form_show_button.html}��{$form.form_clear_button.html}</td>
    </tr>   
</table>

        </td>
        <td valign="top" style="padding-left: 20px;">

<table>
<col span="8" width="100" style="color: #525552;">
    <tr>
        <td>11�������</td>
        <td>13��������</td>
        <td>14�����Ͱ�</td>
        <td>15���������</td>
    </tr>
    <tr>
        <td>61���������</td>
        <td>63����������</td>
        <td>64�������Ͱ�</td>
        <td></td>
    </tr>
    <tr style="height: 0px;"><td colspan="4"></td></tr>
    <tr>
        <td>31������</td>
        <td>32����������</td>
        <td>33���������</td>
        <td>34���껦</td>
    </tr>
    <tr>
        <td>35�������</td>
        <td>36������¾����</td>
        <td>37�������å��껦</td>
        <td>38������Ĵ��</td>
    </tr>
    <tr>
        <td>39���������</td>
        <td></td>
        <td></td>
        <td></td>
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
<table>
    <tr>
        <td>

<table>
    <tr> 
        <td colspan="2">{$html_1}</td>
    </tr>
    <tr>
        <td valign="top">{$html_2}</td>
    </tr>
    <tr>    
        <td colspan="2">{$var.html_page}{$html_3}{$var.html_page2}</td>
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
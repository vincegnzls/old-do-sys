{$var.html_header}

<body bgcolor="#D8D0C8">
<form {$form.attributes}>

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

{if $smarty.session.group_kind == "2"}
<table>
    <tr>
        <td>{$form.form_client_button_2.html}��{$form.form_client_button_3.html}</td>
    </tr>
</table>
<br>
{/if}

<table class="Data_Table" border="1" width="400">
<col width="90" style="font-weight: bold;">
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

{if $smarty.session.group_kind == "2"}
<br><br><br>
{/if}
<table>
<col span="8" width="100" style="color: #525552;">
    <tr>    
        <td>21���ݻ���</td> 
        <td>23��������</td> 
        <td>24�����Ͱ�</td>
        <td>25���������</td>
    </tr>
    <tr>
        <td>71���������</td>
        <td>73����������</td>
        <td>74�������Ͱ�</td>
        <td></td>
    </tr>
    <tr style="height: 0px;"><td colspan="4"></td></tr>
    <tr>
        <td>41�������ʧ</td>
        <td>43��������ʧ</td>
        <td>44�������ʧ</td>
        <td>45���껦</td>
    </tr>
    <tr>
        <td>46����ʧĴ��</td>
        <td>47������¾��ʧ</td>
        <td>48�������</td>
        <td>49���������</td>
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
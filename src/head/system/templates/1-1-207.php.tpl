{$var.html_header}

<body bgcolor="#D8D0C8">
<form {$form.attributes}>

{*+++++++++++++++ ���� begin +++++++++++++++*}
<table width="100%" height="90%" class="M_table">

    {*+++++++++++++++ �إå��� begin +++++++++++++++*}
    <tr align="center" height="60">
        <td width="100%" colspan="2" valign="top">{$var.page_header}</td>
    </tr>

    <tr align="center" valign="top">
        <td>
            <table>
                <tr>
                    <td>

{*+++++++++++++++ ��å������� begin +++++++++++++++*}
{* ��Ͽ���ѹ���λ��å��������� *}
<span style="color: #0000ff; font-weight: bold; line-height: 130%;">
{if $var.comp_msg != null}<li>{$var.comp_msg}<br>{/if}
</span>
{* ���顼��å��������� *} 
<span style="color: #ff0000; font-weight: bold; line-height: 130%;">
{if $form.form_bank_cd.error != null}<li>{$form.form_bank_cd.error}<br>{/if}
{if $form.form_bank_name.error != null}<li>{$form.form_bank_name.error}<br>{/if}
{if $form.form_bank_kana.error != null}<li>{$form.form_bank_kana.error}<br>{/if}
{if $form.form_bank_cname.error != null}<li>{$form.form_bank_cname.error}<br>{/if}
</span>
{*--------------- ��å������� e n d ---------------*}

{*+++++++++++++++ ����ɽ���� begin +++++++++++++++*}
<table width="450">
    <tr>
        <td>

<table class="Data_Table" border="1" width="100%">
<col width="120" style="font-weight: bold;">
<col>
    <tr>
        <td class="Title_Purple">��ԥ�����<font color="#ff0000">��</font></td>
        <td class="Value">{$form.form_bank_cd.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">���̾<font color="#ff0000">��</font></td>
        <td class="Value">{$form.form_bank_name.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">���̾<br>�ʥեꥬ�ʡ�<font color="#ff0000">��</font></td>
        <td class="Value">{$form.form_bank_kana.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">ά��<font color="#ff0000">��</font></td>
        <td class="Value">{$form.form_bank_cname.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">��ɽ��</td>
        <td class="Value">{$form.form_nondisp_flg.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">����</td>
        <td class="Value">{$form.form_note.html}</td>
    </tr>
</table>

<table width="100%">
    <tr>
        <td>
            <b><font color="#ff0000">����ɬ�����ϤǤ�</font></b>
        </td>
        <td align="right">{$form.entry_button.html}����{$form.clear_button.html}</td>
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
{$form.hidden}
<table width="100%">
    <tr>
        <td>

��<b>{$var.total_count}</b>�{$form.csv_button.html}
<table class="List_Table" border="1" width="100%">
    <tr align="center" style="font-weight: bold;">
        <td class="Title_Purple">No.</td>
        <td class="Title_Purple">��ԥ�����</td>
        <td class="Title_Purple">���̾</td>
        <td class="Title_Purple">���̾<br>�ʥեꥬ�ʡ�</td>
        <td class="Title_Purple">ά��</td>
        <td class="Title_Purple">��ɽ��</td>
        <td class="Title_Purple">����</td>
    </tr>
    {foreach key=j from=$row item=items}
    <tr class="Result1"> 
        <td align="right">{$j+1}</td>
        <td>{$row[$j][1]}</td>
        <td><a href="1-1-207.php?bank_id={$row[$j][0]}">{$row[$j][2]}</a></td>
        <td>{$row[$j][3]}</td>
        <td>{$row[$j][4]}</td>
        <td align="center">{$row[$j][5]}</td>
        <td>{$row[$j][6]}</td>
    </tr>   
    {/foreach}
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
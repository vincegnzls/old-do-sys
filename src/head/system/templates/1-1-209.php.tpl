{$var.html_header}

<body bgcolor="#D8D0C8">
<form {$form.attributes}>

{*+++++++++++++++ 外枠 begin +++++++++++++++*}
<table width="100%" height="90%" class="M_table">

    {*+++++++++++++++ ヘッダ類 begin +++++++++++++++*}
    <tr align="center" height="60">
        <td width="100%" colspan="2" valign="top">{$var.page_header}</td>
    </tr>
    {*--------------- ヘッダ類 e n d ---------------*}

    {*+++++++++++++++ コンテンツ部 begin +++++++++++++++*}
    <tr align="center" valign="top">
        <td>
            <table>
                <tr>
                    <td>

{*+++++++++++++++ メッセージ類 begin +++++++++++++++*}
    <span style="color: #0000ff; font-weight: bold; line-height: 130%;">
    {if $var.message != null && $form.form_product_cd.error == null && $form.form_product_name.error == null}
        <li>{$var.message}<br>
    {/if}
    </span>
     {* エラーメッセージ出力 *}
     <span style="color: #ff0000; font-weight: bold; line-height: 130%;">
    {if $form.form_product_cd.error != null}
        <li>{$form.form_product_cd.error}<br>
    {/if}
    {if $form.form_product_name.error != null}
        <li>{$form.form_product_name.error}<br>
    {/if}
    </span>
{*--------------- メッセージ類 e n d ---------------*}

{*+++++++++++++++ 画面表示１ begin +++++++++++++++*}
<table width="450">
    <tr>
        <td>

<table class="Data_Table" border="1" width="100%">
<col width="140" style="font-weight: bold;">
<col>
    <tr>
        <td class="Title_Purple">管理区分コード<font color="#ff0000">※</font></td>
        <td class="Value">
        <table>
            <tr>
                <td rowspan="2">{$form.form_product_cd.html}</td>
                <td><font color="#555555">　0000〜4999と9000〜9999は本部用</font></td>
            </tr>
            <tr>
                <td><font color="#555555">　5000〜8999はショップ専用</font></td>
            </tr>
        </table>
        </td>
    </tr>
    <tr>
        <td class="Title_Purple">管理区分名<font color="#ff0000">※</font></td>
        <td class="Value">{$form.form_product_name.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">備考</td>
        <td class="Value">{$form.form_product_note.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">承認</td>
        <td class="Value">{$form.form_accept.html}</td>
    </tr>
</table>

<table width="100%">
    <tr>
        <td><b><font color="#ff0000">※は必須入力です</font></b></td>
        <td align="right">{$form.form_entry_button.html}　　{$form.form_clear_button.html}</td>
    </tr>
</table>

        </td>
    </tr>
</table>
<br>
{*--------------- 画面表示１ e n d ---------------*}

                    </td>
                </tr>
                <tr>
                    <td>

{*+++++++++++++++ 画面表示２ begin +++++++++++++++*}
{$form.hidden}

<table width="100%">
    <tr>
        <td>

全<b>{$var.total_count}</b>件　{$form.form_csv_button.html}
<table class="List_Table" border="1" width="100%">
    <tr align="center" style="font-weight: bold;">
        <td class="Title_Purple">No.</td>
        <td class="Title_Purple">管理区分コード</td>
        <td class="Title_Purple">管理区分名</td>
        <td class="Title_Purple">備考</td>
        <td class="Title_Purple">承認</td>
    </tr>
    {*1行目*}
    {foreach from=$page_data key=i item=item}
    <tr class="Result1">
        <td align="right">{$i+1}</td>
        <td>{$item[0]}</a></td>
        <td><a href="?product_id={$item[1]}">{$item[2]}</a></td>
        <td>{$item[3]}</a></td>
        {if $item[4] == "1"}<td align="center">○</td>
        {else}<td align="center" style="color: #ff0000;">×</td>
        {/if}
    </tr>
    {/foreach}      
</table>

        </td>
    </tr>
</table>
{*--------------- 画面表示２ e n d ---------------*}

                    </td>
                </tr>
            </table>
        </td>
    </tr>
    {*--------------- コンテンツ部 e n d ---------------*}

</table>
{*--------------- 外枠 e n d ---------------*}

{$var.html_footer}

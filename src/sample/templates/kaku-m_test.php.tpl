{$var.html_header}
<body bgcolor="#D8D0C8">
<form {$form.attributes}>

{*+++++++++++++++ 外枠 begin +++++++++++++++*}
<table width="100%" height="90%" class="M_Table">

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
{* エラーメッセージ出力 *}
<span style="color: #ff0000; font-weight: bold; line-height: 130%;">
</span>
{*--------------- メッセージ類 e n d ---------------*}

{*+++++++++++++++ 画面表示１ begin +++++++++++++++*}
<table><tr><td>

<table class="Data_Table" border="1" width="650">
<col width="140" style="font-weight: bold;">
<col>
<col width="140" style="font-weight: bold;">
<col>
    <tr>
        <td class="Title_Purple">ショップコード</td>
        <td class="Value">{$form.form_client_cd.html}</td>
        <td class="Title_Purple">ショップ名</td>
        <td class="Value">{$form.form_client_name.html}</td>
    </tr>
        <td class="Title_Purple">担当者コード</td>
        <td class="Value">{$form.form_charge_cd.html}</td>
        <td class="Title_Purple">スタッフ名</td>
        <td class="Value">{$form.form_staff_name.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">削除権限</td>
        <td class="Value">{$form.form_del_compe.html}</td>
        <td class="Title_Purple">承認権限</td>
        <td class="Value">{$form.form_accept_compe.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">権限付与</td>
        <td class="Value">{$form.form_compe_invest.html}</td>
        <td class="Title_Purple">
<a href="#" onClick="return window.open('kaku-m_sub.php','sub','width=600, height=800,scrollbars=yes,location=no, menubar=no,left=10,top=30');">画面</a>（未対応）</td>
        <td class="Value"></td>
    </tr>
    <tr>
        <td class="Title_Purple" rowspan=>在職識別</td>
        <td class="Value" colspan=3>{$form.form_staff_state.html}</td>
    </tr>


</table>
<tr><td align=right>{$form.show_btn.html}　　{$form.clear_btn.html}<br></td></tr>
<tr><td align=right><br>{$form.csv_btn.html}</td></tr>
</td></tr></table>
<br>
{*--------------- 画面表示１ e n d ---------------*}

                                        </td>
                                </tr>
                                <tr>
                                        <td>

{*+++++++++++++++ 画面表示２ begin +++++++++++++++*}
<table width="100%">
    <tr>
        <td>
    {$form.hidden}
<table width="100%">
        <tr>
    {$var.num}<br>
    {$var.html}
        </tr>
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


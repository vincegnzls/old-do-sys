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
{* 登録・変更完了メッセージ出力 *}
<span style="color: #0000ff; font-weight: bold; line-height: 130%;">
{if $var.comp_msg != null}<li>{$var.comp_msg}<br>{/if}
</span>
{* エラーメッセージ出力 *} 
<span style="color: #ff0000; font-weight: bold; line-height: 130%;">
{if $form.form_post.error != null}<li>{$form.form_post.error}<br>{/if}
</span><br>
{*--------------- メッセージ類 e n d ---------------*}

{*+++++++++++++++ 画面表示１ begin +++++++++++++++*}
<table>
    <tr>
        <td>

<span style="font: bold 16px;">※�　銑Δ房�社情報を入力してください</span><br>
<span style="font: bold 16px;">※�А銑�にコメントを入力してください</span><br>

<table class="List_Table" width="903" height="1280" cellpadding="0"><tr><td class="Value">
<table width="100%" height="100%" style="background: url(../../../image/hacchusho_f_20070618.png) no-repeat fixed;">
    <tr>
        <td valign="top">
            {* 発注元（自社）情報 *}
            <table style="position: relative; top: 142px; left: 580px;" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="color: #ff0000; font-size: 14px;">
                        <b>��</b>{$form.form_post.html}
                    </td>
                </tr>
            </table>
            <table style="position: relative; top: 142px; left: 580px;" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="color: #ff0000; font-size: 14px;">
                        <b>��</b>{$form.o_memo2.html}<br>
                        <b>��</b>{$form.o_memo3.html}<br>
                        <b>��</b>{$form.o_memo4.html}<br>
                        <b>��</b>{$form.o_memo5.html}<br>
                        <b>��</b>{$form.o_memo6.html}
                    </td>
                </tr>
            </table>
            {* 発注書コメント１ *}
            <table style="position: relative; top: 210px; left: 40px;" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="color: #ff0000; font-size: 14px;">
                        <b>��</b>{$form.o_memo7.html}<br>
                        <b>��</b>{$form.o_memo8.html}
                    </td>
                </tr>
            </table>
            {* 発注書コメント２ *}
            <table style="position: relative; top: 880px; left: 40px;" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="color: #ff0000; font-size: 14px;">
                        <b>��</b>{$form.o_memo9.html}<br>
                        <b>��</b>{$form.o_memo10.html}<br>
                        <b>��</b>{$form.o_memo11.html}<br>
                        <b>��</b>{$form.o_memo12.html}<br>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</td></tr></table>

<table align="right">
    <tr>
        <td>{$form.new_button.html}　　{$form.order_button.html}</td>
    </tr>
</table>

        </td>
    </tr>
</table>
{*--------------- 画面表示１ e n d ---------------*}

                    </td>
                </tr>
            </table>
        </td>
    </tr>
    {*--------------- コンテンツ部 e n d ---------------*}

</table>
{*--------------- 外枠 e n d ---------------*}

{$var.html_footer}

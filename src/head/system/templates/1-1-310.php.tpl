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

{*+++++++++++++++ メッセージ類１ begin +++++++++++++++*}
{* 登録・変更完了メッセージ出力 *}
{if $var.comp_msg != null}
    <span style="color: #0000ff; font-weight: bold; line-height: 130%;">
    <li>{$var.comp_msg}</li><br>
    </span><br>
{/if}

{if $var.commit_flg != true}
    <span style="font-weight: bold; color: #555555;">※現在、以下の発行元が登録済みです。</span>
{else}
    <span style="font-weight: bold; color: #0000ff;">印字パターンを登録しました。</span>
{/if}
{*--------------- メッセージ類１ e n d ---------------*}

{*+++++++++++++++ 画面表示１ begin +++++++++++++++*}
{$form.hidden}

<table width="950">
    <tr>
        <td>

{if $var.commit_flg != true}
    <table width="100%">
        <tr valign="bottom">
            <td>{$form.pattern_select.html}　{$form.preview_button.html}　　{$form.change_button.html}　　{$form.clear_button.html}</td>
        </tr>
    </table>
{else}
<br>　　　　　　　　　　　{$form.ok_button.html}
{/if}

        </td>
    </tr>
</table>

<hr>
{*--------------- 画面表示１ e n d ---------------*}

                    </td>
                </tr>
                <tr>
                    <td>

{*+++++++++++++++ メッセージ類２ begin +++++++++++++++*}
{* 登録・変更完了メッセージ出力 *}
{if $var.qf_err_flg == true}
    <span style="color: #ff0000; font-weight: bold; line-height: 130%;">
    {if $form.pattern_name.error != null}
        <li>{$form.pattern_name.error}</li><br>
    {/if}
    </span>
{/if}
{*--------------- メッセージ類２ e n d ---------------*}
{if $smarty.post.form_new_flg != true && $smarty.post.form_update_flg != true || $var.pattern_err != null}

    {$form.form_new_button.html}

{else}

<table width="100%">
    <tr>
        <td>

<table class="Data_Table" border="1" width="350">
<col width="110" style="font-weight: bold;">
<col>
    <tr>
        <td class="Title_Purple">パターン名<font color="#ff0000">※</font></td>
        <td class="Value">{$form.pattern_name.html}</td>
    </tr>
</table>

{if $var.commit_flg != true}
<br>
<span style="font-weight: bold; color: #ff0000;">以下の項目を設定して下さい。</span><br>
<table style="font-weight: bold; color: #555555;">
<col span="3" width="130">
    <tr>
        <td>�� 会社名</td><td>�� 代表者名</td><td>�� 住所</td>
    </tr>
    <tr>
        <td>�� TEL・FAX</td><td>�ァ銑� 取引銀行</td><td>�� コメント</td>
    </tr>
</table>
{/if}

        </td>
    </tr>
</table>
<br>

{/if}
{*--------------- 画面表示２ a n d ---------------*}

                    </td>
                </tr>
                <tr>
                    <td>

{*+++++++++++++++ 画面表示３ begin +++++++++++++++*}
{if $smarty.post.form_new_flg != true && $smarty.post.form_update_flg != true || $var.pattern_err != null}

{else}

<table>
    <tr>
        <td>

<table class="List_Table" width="930" height="680" cellpadding="0"><tr><td class="Value">
{*<table width="100%" height="100%" style="background: url(../../../image/request.PNG) no-repeat fixed 30% 50%;">*}
<table width="100%" height="100%" style="background: url(../../../image/seikyusyo.png) no-repeat fixed 30% 50%;">
    <tr>
        <td valign="top">
        {* 社名等 *}
        {if $var.commit_flg != true}
            <table style="position: relative; top: 75px; left: 550px;" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="color: #ff0000;">
                        ��{$form.c_memo1.html} {$form.font_size_select_0.html}<br>
                        ��{$form.c_memo2.html} {$form.font_size_select_1.html}<br>
                        ��{$form.c_memo3.html} {$form.font_size_select_2.html}<br>
                        ��{$form.c_memo4.html} {$form.font_size_select_3.html}
                    </td>
                </tr>
            </table>
        {else}
            <table style="position: relative; top: 125px; left: 600px;" cellspacing="0" cellpadding="0">
                <tr>
                    <td>
                        <span style="font: bold 15px;">
                        {$form.c_memo1.html}<br>
                        </span>
                        <span style="font-size: 11px;">
                        {$form.c_memo2.html}<br>
                        {$form.c_memo3.html}<br>
                        {$form.c_memo4.html}
                        </span>
                    </td>
                </tr>
            </table>
        {/if}
        {* 取引銀行 *}
        {if $var.commit_flg != true}
            <table style="position: relative; top: 480px; left: 15px;">
                <tr style="color: #ff0000;">
                    <td>
                        ��{$form.c_memo5.html}　��{$form.c_memo6.html}<br>
                        ��{$form.c_memo7.html}　��{$form.c_memo8.html}<br>
                        ��{$form.c_memo9.html}　��{$form.c_memo10.html}<br>
                        ��{$form.c_memo11.html}　��{$form.c_memo12.html}<br>
                        ��{$form.c_memo13.html}
                    </td>
                </tr>
            </table>
        {else}
            <table style="position: relative; top: 530px; left: 50px;">
                <tr>
                    <td span style="font-size: 11px;">
                        {$form.c_memo5.html}　　　{$form.c_memo6.html}<br>
                        {$form.c_memo7.html}　　　{$form.c_memo8.html}<br>
                        {$form.c_memo9.html}　　　{$form.c_memo10.html}<br>
                        {$form.c_memo11.html}　　　{$form.c_memo12.html}<br>
                        {$form.c_memo13.html}
                    </td>
                </tr>
        {/if}
        </td>
    </tr>
</table>
</tr></td></table>
<br>

{if $var.commit_flg != true}
<table align="right">
    <tr>
        <td>{$form.new_button.html}</td>
    </tr>
</table>
{/if}

{/if}

        </td>
    </tr>
<table>
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

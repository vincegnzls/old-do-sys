{$form.javascript}
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
<span style="color: #ff0000; font-weight: bold; line-height: 130%;">
{if $form.form_staff_cd.error != null}<li>{$form.form_staff_cd.error}<br>{/if}
{if $form.form_charge_cd.error != null}<li>{$form.form_charge_cd.error}<br>{/if}
{if $form.form_staff_name.error != null}<li>{$form.form_staff_name.error}<br>{/if}
{if $form.form_staff_ascii.error != null}<li>{$form.form_staff_ascii.error}<br>{/if}
{if $form.form_login_id.error != null}<li>{$form.form_login_id.error}<br>{/if}
{if $form.form_password1.error != null}<li>{$form.form_password1.error}<br>{/if}
{if $form.form_birth_day.error != null}<li>{$form.form_birth_day.error}<br>{/if}
{if $form.form_join_day.error != null}<li>{$form.form_join_day.error}<br>{/if}
{if $form.form_retire_day.error != null}<li>{$form.form_retire_day.error}<br>{/if}
{if $form.form_photo_ref.error != null}<li>{$form.form_photo_ref.error}<br>{/if}
{if $form.form_license.error != null}<li>{$form.form_license.error}<br>{/if}
{if $form.form_note.error != null}<li>{$form.form_note.error}<br>{/if}
{if $form.permit_error.error != null}<li>{$form.permit_error.error}<br>{/if}
{if $var.staff_del_restrict_msg != null}<li>{$var.staff_del_restrict_msg}<br>{/if}
{if $form.form_part.error != null}<li>{$form.form_part.error}<br>{/if}
</span>
{*--------------- メッセージ類 e n d ---------------*}

{*+++++++++++++++ 画面表示１ begin +++++++++++++++*}
{$form.hidden}

<table width="750">
    <tr>
        <td>

<table width="100%">
    <tr>
        <td align="right">
            {if $smarty.get.staff_id != null}
                {$form.back_button.html}　{$form.next_button.html}
            {/if}
        </td>
    </tr>
</table>
<br>

<table class="Data_Table" border="1" width="50%">
<col width="150" style="font-weight: bold;">
<col>
    <tr>
        <td class="Type">在職識別<font color="#ff0000">※</font></td>
        <td class="Value">{$form.form_state.html}</td>
    </tr>
</table>
<br>

<span style="font-size: 15px; color: #555555; font-weight: bold;">{$form.form_h_change_flg.html}</span>
<table class="Data_Table" border="1" width="100%">
<col width="150" style="font-weight: bold;">
<col>
<col width="200">
    <tr>
        <td class="Title_Purple">ネットワーク証ID</td>
        <td class="Value">{$form.form_staff_cd.html}</td>
        <td class="Title_Purple" align="center"><b>証明写真</b></td>
    </tr>
    <tr>
        <td class="Title_Purple">スタッフ名<font color="#ff0000">※</font></td>
        <td class="Value">{$form.form_staff_name.html}</td>
        <td class="Value" rowspan="8">
            <table width="120" height="140" align="center" style="background-image: url(2-1-110.php?staff_id={$var.staff_id});background-repeat:no-repeat; margin-bottom: -3px; margin-top: -1px;" cellspacing="0" cellpadding="0">
                <tr>
                    <td><img src="../../../image/frame.PNG" width="120" height="140" border="0"></td>
                </tr>
                <tr height="5"><td></td></tr>
            </table>
            <table align="center">
                <tr>
                    <td style="color: #555555;">
                        {if $var.freeze_flg != true}{$form.form_photo_ref.html}<br>{/if}
                        &nbsp;{$form.form_photo_del.html}
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="Title_Purple">スタッフ名<br>(フリガナ)</td>
        <td class="Value">{$form.form_staff_read.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">スタッフ名<br>(ローマ字)<font color="#ff0000">※</font></td>
        <td class="Value">{$form.form_staff_ascii.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">性別<font color="#ff0000">※</font></td>
        <td class="Value">{$form.form_sex.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">生年月日</td>
        <td class="Value">{$form.form_birth_day.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">退職日</td>
        <td class="Value">{$form.form_retire_day.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">研修履歴</td>
        <td class="Value">{$form.form_study.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">トイレ診断士資格<font color="#ff0000">※</font></td>
        <td class="Value">{$form.form_toilet_license.html}</td>
    </tr>
</table>

        </td>
    </tr>
    <tr>
        <td>

<table class="Data_Table" border="1" width="100%">
<col width="150" style="font-weight: bold;">
<col>
    <tr>
        <td class="Title_Purple">担当者コード<font color="#ff0000">※</font></td>
        <td class="Value">{$form.form_charge_cd.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">入社年月日</td>
        <td class="Value">{$form.form_join_day.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">雇用形態<font color="#ff0000">※</font></td>
        <td class="Value">{$form.form_employ_type.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">所属部署<font color="#ff0000">※</font></td>
        <td class="Value">{$form.form_part.html}　　{$form.form_section.html}課</td>
    </tr>
    <tr>
        <td class="Title_Purple">役職</td>
        <td class="Value">{$form.form_position.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">職種<font color="#ff0000">※</font></td>
        <td class="Value">{$form.form_job_type.html}</td>
    </tr>
{*担当倉庫は表示しない
    <tr>
        <td class="Title_Purple">担当倉庫</td>
        <td class="Value">{$form.form_ware.html}</td>
    </tr>
*}
    <tr>
        <td class="Title_Purple">巡回担当者</td>
        <td class="Value">{$form.form_round_staff.html}</td>
    </tr>
</table>

        </td>
    </tr>
    <tr>
        <td>

<table class="Data_Table" border="1" width="100%">
<col width="150" style="font-weight: bold;">
<col>
    <tr>
        <td class="Title_Purple">取得資格</td>
        <td class="Value">{$form.form_license.html}</td>
    </tr>
</table>

        </td>
    </tr>
    <tr>
        <td>

<table class="Data_Table" border="1" width="100%">
<col width="150" style="font-weight: bold;">
<col>
    <tr>
        <td class="Title_Purple">備考</td>
        <td class="Value">{$form.form_note.html}</td>
    </tr>
</table>

<table width="100%">
    <tr>
        <td><b><font color="#ff0000">※は必須入力です</font></b></td>
    </tr>
</table>
<br>

        </td>
    </tr>
    <tr>
        <td>

<span style="font: bold 15px; color: #555555;">ログイン情報を　{$form.form_login_info.html}</span>
<span style="color: #ff0000; font-weight: bold;">　{$var.login_info_msg}</font><br>
<table class="Data_Table" border="1" width="100%">
<col width="150" style="font-weight: bold;">
<col>
    <tr>
        <td class="Title_Purple">ログインID</td>
        <td class="Value">{$form.form_login_id.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">パスワード</td>
        <td class="Value">{$form.form_password1.html}
            <span style="color: #ff0000; font-weight: bold;">
            {if $var.password_msg != null}{$var.password_msg}{/if}
            </span>
        </td>
    </tr>
    <tr>
        <td class="Title_Purple">パスワード(確認用)</td>
        <td class="Value">{$form.form_password2.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">{$form.form_permit_link.html}</td>
        <td class="Value">{if $var.permit_set_msg != null}{$var.permit_set_msg}{/if}</td>
    </tr>
</table>

<table width="100%">
    <tr>
        <td>{if $var.change_flg == 'f'}{$form.del_button.html}{/if}<td>
        <td align="right">{$form.comp_button.html}　{$form.return_button.html}　{$form.entry_button.html}</td>
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

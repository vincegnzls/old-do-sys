{$form.javascript}
{$var.html_header}
<script language="javascript">
{$var.js_data}
</script>

<style TYPE="text/css">
<!--
td.top              {ldelim}border-top: 1px solid #999999;{rdelim}
td.bottom           {ldelim}border-bottom: 1px solid #999999;{rdelim}
td.left             {ldelim}border-left: 1px solid #999999;{rdelim}
td.top_left         {ldelim}border-top: 1px solid #999999; border-left: 1px solid #999999;{rdelim}
td.left_bottom      {ldelim}border-left: 1px solid #999999; border-bottom: 1px solid #999999;{rdelim}
td.top_left_bottom  {ldelim}border-top: 1px solid #999999; border-left: 1px solid #999999; border-bottom: 1px solid #999999;{rdelim}
-->
</style>

<body bgcolor="#D8D0C8" style="overflow-x:hidden">
<form name="dateForm" method="post">

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
{* 表示権限のみ時のメッセージ *} 
{if $var.auth_r_msg != null}
    <span style="color: #ff0000; font-weight: bold; line-height: 130%;">
    <li>{$var.auth_r_msg}</li>
    </span><br>
{/if}
{*--------------- メッセージ類 e n d ---------------*}

{*+++++++++++++++ 画面表示１ begin +++++++++++++++*}
{$form.hidden}

<table>
    <tr>
        <td colspan="3">

<table class="Data_Table" border="1" width="450">
<col width="150" style="font-weight: bold;">
<col>
    <tr>
        <td class="Title_Purple">担当者コード</td>
        <td class="Value">{$var.charge_cd}</td>
    </tr>
    <tr>
        <td class="Title_Purple">スタッフ名</td>
        <td class="Value">{$var.staff_name}</td>
    </tr>
</table>

        </td>
    </tr>
    <tr>
        <td colspan="3">

<table class="Data_Table" border="1" width="450">
<col width="150" style="font-weight: bold;">
<col>
    <tr>
        <td class="Title_Purple">削除権限を付与する</td>
        <td class="Value">{$form.permit_delete.html}</td>
    </tr>
</table>

        </td>
    </tr>
    <tr>
        <td colspan="3">

<table class="Data_Table" border="1" width="450">
<col width="150" style="font-weight: bold;">
<col>
    <tr>
        <td class="Title_Purple">承認権限を付与する</td>
        <td class="Value">{$form.permit_accept.html}</td>
    </tr>
</table>
<br>
{*--------------- 画面表示１ e n d ---------------*}

        </td>
    </tr>
    <tr>
        <td valign="top">

{*+++++++++++++++ 画面表示２ begin +++++++++++++++*}
<table class="Data_Table" bgcolor="#ffffff">
<col width="17" style="font: bold 15px;">
<col width="17" style="font: bold;">
<col width="17" style="font: bold;">
<col width="180">
<col width="35" align="center">
<col width="35" align="center">
    <tr bgcolor="#555555" style="color: #ffffff; font-weight: bold;">
        <td class="bottom" colspan="4"></td>
        <td class="bottom">表示</td>
        <td class="bottom">入力</td>
    </tr>
    <tr bgcolor="#b0b0f0">
        <td class="top" colspan="4">本部</td>
        <td class="bottom">{$form.permit.h.0.0.0.r.html}</td>
        <td class="bottom">{$form.permit.h.0.0.0.w.html}</td>
    </tr>
    <tr>
        <td bgcolor="#b0b0f0" rowspan="38"></td>
        <td bgcolor="#c7c7f0" class="top_left" colspan="3">マスタ・設定</td>
        <td bgcolor="#c7c7f0" class="bottom">{$form.permit.h.1.0.0.r.html}</td>
        <td bgcolor="#c7c7f0" class="bottom">{$form.permit.h.1.0.0.w.html}</td>
    </tr>
    <tr>
        <td bgcolor="#c0c0f0" class="left_bottom" rowspan="37"></td>
        <td bgcolor="#e0e0f0" class="top_left" colspan="2">本部管理マスタ</td>
        <td bgcolor="#e0e0f0" class="bottom">{$form.permit.h.1.1.0.r.html}</td>
        <td bgcolor="#e0e0f0" class="bottom">{$form.permit.h.1.1.0.w.html}</td>
    </tr>
    <tr>
        <td bgcolor="#e0e0f0" class="left_bottom" rowspan="4"></td>
        <td class="top_left">業種</td>
        <td>{$form.permit.h.1.1.1.r.html}</td>
        <td>{$form.permit.h.1.1.1.w.html}</td>
    </tr>
    <tr>
        <td class="left">業態</td>
        <td>{$form.permit.h.1.1.2.r.html}</td>
        <td>{$form.permit.h.1.1.2.w.html}</td>
    </tr>
    <tr>
        <td class="left">施設</td>
        <td>{$form.permit.h.1.1.3.r.html}</td>
        <td>{$form.permit.h.1.1.3.w.html}</td>
    </tr>
    <tr>
        <td class="left_bottom">サービス</td>
        <td class="bottom">{$form.permit.h.1.1.4.r.html}</td>
        <td class="bottom">{$form.permit.h.1.1.4.w.html}</td>
    </tr>
    <tr>
        <td bgcolor="#e0e0f0" class="top_left" colspan="2">一部共有マスタ</td>
        <td bgcolor="#e0e0f0" class="bottom">{$form.permit.h.1.2.0.r.html}</td>
        <td bgcolor="#e0e0f0" class="bottom">{$form.permit.h.1.2.0.w.html}</td>
    </tr>
    <tr>
        <td bgcolor="#e0e0f0" class="left_bottom" rowspan="4"></td>
        <td class="top_left">スタッフ</td>
        <td>{$form.permit.h.1.2.1.r.html}</td>
        <td>{$form.permit.h.1.2.1.w.html}</td>
    </tr>
    <tr>
        <td class="left">商品</td>
        <td>{$form.permit.h.1.2.2.r.html}</td>
        <td>{$form.permit.h.1.2.2.w.html}</td>
    </tr>
    <tr>
        <td class="left">Ｍ区分</td>
        <td>{$form.permit.h.1.2.3.r.html}</td>
        <td>{$form.permit.h.1.2.3.w.html}</td>
    </tr>
    <tr>
        <td class="left_bottom">製品区分</td>
        <td class="bottom">{$form.permit.h.1.2.4.r.html}</td>
        <td class="bottom">{$form.permit.h.1.2.4.w.html}</td>
    </tr>
    <tr>
        <td bgcolor="#e0e0f0" class="top_left" colspan="2">個別マスタ</td>
        <td bgcolor="#e0e0f0" class="bottom">{$form.permit.h.1.3.0.r.html}</td>
        <td bgcolor="#e0e0f0" class="bottom">{$form.permit.h.1.3.0.w.html}</td>
    </tr>
    <tr>
        <td bgcolor="#e0e0f0" class="left_bottom" rowspan="14"></td>
        <td class="top_left">部署</td>
        <td>{$form.permit.h.1.3.1.r.html}</td>
        <td>{$form.permit.h.1.3.1.w.html}</td>
    </tr>
    <tr>
        <td class="left">倉庫</td>
        <td>{$form.permit.h.1.3.2.r.html}</td>
        <td>{$form.permit.h.1.3.2.w.html}</td>
    </tr>
    <tr>
        <td class="left">地区</td>
        <td>{$form.permit.h.1.3.3.r.html}</td>
        <td>{$form.permit.h.1.3.3.w.html}</td>
    </tr>
    <tr>
        <td class="left">銀行</td>
        <td>{$form.permit.h.1.3.4.r.html}</td>
        <td>{$form.permit.h.1.3.4.w.html}</td>
    </tr>
    <tr>
        <td class="left">製造品</td>
        <td>{$form.permit.h.1.3.5.r.html}</td>
        <td>{$form.permit.h.1.3.5.w.html}</td>
    </tr>
    <tr>
        <td class="left">構成品</td>
        <td>{$form.permit.h.1.3.6.r.html}</td>
        <td>{$form.permit.h.1.3.6.w.html}</td>
    </tr>
    <tr>
        <td class="left">FC区分</td>
        <td>{$form.permit.h.1.3.7.r.html}</td>
        <td>{$form.permit.h.1.3.7.w.html}</td>
    </tr>
    <tr>
        <td class="left">FCグループ</td>
        <td>{$form.permit.h.1.3.8.r.html}</td>
        <td>{$form.permit.h.1.3.8.w.html}</td>
    </tr>
    <tr>
        <td class="left">FC</td>
        <td>{$form.permit.h.1.3.9.r.html}</td>
        <td>{$form.permit.h.1.3.9.w.html}</td>
    </tr>
    <tr>
        <td class="left">得意先</td>
        <td>{$form.permit.h.1.3.10.r.html}</td>
        <td>{$form.permit.h.1.3.10.w.html}</td>
    </tr>
    <tr>
        <td class="left">契約</td>
        <td>{$form.permit.h.1.3.11.r.html}</td>
        <td>{$form.permit.h.1.3.11.w.html}</td>
    </tr>
    <tr>
        <td class="left">仕入先</td>
        <td>{$form.permit.h.1.3.12.r.html}</td>
        <td>{$form.permit.h.1.3.12.w.html}</td>
    </tr>
    <tr>
        <td class="left">直送先</td>
        <td>{$form.permit.h.1.3.13.r.html}</td>
        <td>{$form.permit.h.1.3.13.w.html}</td>
    </tr>
    <tr>
        <td class="left_bottom">運送業者</td>
        <td class="bottom">{$form.permit.h.1.3.14.r.html}</td>
        <td class="bottom">{$form.permit.h.1.3.14.w.html}</td>
    </tr>
    <tr>
        <td bgcolor="#e0e0f0" class="top_left" colspan="2">帳票設定</td>
        <td bgcolor="#e0e0f0" class="bottom">{$form.permit.h.1.4.0.r.html}</td>
        <td bgcolor="#e0e0f0" class="bottom">{$form.permit.h.1.4.0.w.html}</td>
    </tr>
    <tr>
        <td bgcolor="#e0e0f0" class="left_bottom" rowspan="5"></td>
        <td class="top_left">発注書コメント</td>
        <td>{$form.permit.h.1.4.1.r.html}</td>
        <td>{$form.permit.h.1.4.1.w.html}</td>
    </tr>
    <tr>
        <td class="left">注文書フォーマット</td>
        <td>{$form.permit.h.1.4.2.r.html}</td>
        <td>{$form.permit.h.1.4.2.w.html}</td>
    </tr>
    <tr>
        <td class="left">請求書</td>
        <td>{$form.permit.h.1.4.3.r.html}</td>
        <td>{$form.permit.h.1.4.3.w.html}</td>
    </tr>
    <tr>
        <td class="left">売上伝票</td>
        <td>{$form.permit.h.1.4.4.r.html}</td>
        <td>{$form.permit.h.1.4.4.w.html}</td>
    </tr>
    <tr>
        <td class="left_bottom">納品書</td>
        <td class="bottom">{$form.permit.h.1.4.5.r.html}</td>
        <td class="bottom">{$form.permit.h.1.4.5.w.html}</td>
    </tr>
    <tr>
        <td bgcolor="#e0e0f0" class="top_left" colspan="2">システム設定</td>
        <td bgcolor="#e0e0f0" class="bottom">{$form.permit.h.1.5.0.r.html}</td>
        <td bgcolor="#e0e0f0" class="bottom">{$form.permit.h.1.5.0.w.html}</td>
    </tr>
    <tr>
        <td bgcolor="#e0e0f0" class="left_bottom" rowspan="5"></td>
        <td class="top_left">本部プロフィール</td>
        <td>{$form.permit.h.1.5.1.r.html}</td>
        <td>{$form.permit.h.1.5.1.w.html}</td>
    </tr>
    <tr>
        <td class="left">買掛残高初期設定</td>
        <td>{$form.permit.h.1.5.2.r.html}</td>
        <td>{$form.permit.h.1.5.2.w.html}</td>
    </tr>
    <tr>
        <td class="left">売掛残高初期設定</td>
        <td>{$form.permit.h.1.5.3.r.html}</td>
        <td>{$form.permit.h.1.5.3.w.html}</td>
    </tr>
    <tr>
        <td class="left">請求残高初期設定</td>
        <td>{$form.permit.h.1.5.4.r.html}</td>
        <td>{$form.permit.h.1.5.4.w.html}</td>
    </tr>
    <tr>
        <td class="left">パスワード変更</td>
        <td class="bottom">{$form.permit.h.1.5.5.r.html}</td>
        <td class="bottom">{$form.permit.h.1.5.5.w.html}</td>
    </tr>
</table>

        </td>
        <td width="10"></td>
        <td valign="top">

<table class="Data_Table" bgcolor="#ffffff">
<col width="17" style="font: bold 15px;">
<col width="17" style="font: bold;">
<col width="17" style="font: bold;">
<col width="180">
<col width="35" align="center">
<col width="35" align="center">
    <tr bgcolor="#555555" style="color: #ffffff; font-weight: bold;">
        <td class="bottom" colspan="4"></td>
        <td class="bottom">表示</td>
        <td class="bottom">入力</td>
    </tr>
    <tr bgcolor="#e5b0f0">
        <td class="top" colspan="4">ＦＣ</td>
        <td class="bottom">{$form.permit.f.0.0.0.r.html}</td>
        <td class="bottom">{$form.permit.f.0.0.0.w.html}</td>
    </tr>
    <tr>
        <td bgcolor="#e5b0f0" rowspan="33"></td>
        <td bgcolor="#f0c7f0" class="top_left" colspan="3">マスタ・設定</td>
        <td bgcolor="#f0c7f0" class="bottom">{$form.permit.f.1.0.0.r.html}</td>
        <td bgcolor="#f0c7f0" class="bottom">{$form.permit.f.1.0.0.w.html}</td>
    </tr>
    <tr>
        <td bgcolor="#f0c7f0" class="left_bottom" rowspan="32"></td>
        <td bgcolor="#ffdfff" class="top_left" colspan="2">個別マスタ</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.1.1.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.1.1.0.w.html}</td>
    </tr>
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" rowspan="11"></td>
        <td class="top_left">部署</td>
        <td>{$form.permit.f.1.1.1.r.html}</td>
        <td>{$form.permit.f.1.1.1.w.html}</td>
    </tr>
    <tr>
        <td class="left">倉庫</td>
        <td>{$form.permit.f.1.1.2.r.html}</td>
        <td>{$form.permit.f.1.1.2.w.html}</td>
    </tr>
    <tr>
        <td class="left">地区</td>
        <td>{$form.permit.f.1.1.3.r.html}</td>
        <td>{$form.permit.f.1.1.3.w.html}</td>
    </tr>
    <tr>
        <td class="left">銀行</td>
        <td>{$form.permit.f.1.1.4.r.html}</td>
        <td>{$form.permit.f.1.1.4.w.html}</td>
    </tr>
    <tr>
        <td class="left">コース</td>
        <td>{$form.permit.f.1.1.5.r.html}</td>
        <td>{$form.permit.f.1.1.5.w.html}</td>
    </tr>
    <tr>
        <td class="left">構成品</td>
        <td>{$form.permit.f.1.1.6.r.html}</td>
        <td>{$form.permit.f.1.1.6.w.html}</td>
    </tr>
    <tr>
        <td class="left">得意先</td>
        <td>{$form.permit.f.1.1.7.r.html}</td>
        <td>{$form.permit.f.1.1.7.w.html}</td>
    </tr>
    <tr>
        <td class="left">契約</td>
        <td>{$form.permit.f.1.1.8.r.html}</td>
        <td>{$form.permit.f.1.1.8.w.html}</td>
    </tr>
    <tr>
        <td class="left">仕入先</td>
        <td>{$form.permit.f.1.1.9.r.html}</td>
        <td>{$form.permit.f.1.1.9.w.html}</td>
    </tr>
    <tr>
        <td class="left">直送先</td>
        <td>{$form.permit.f.1.1.10.r.html}</td>
        <td>{$form.permit.f.1.1.10.w.html}</td>
    </tr>
    <tr>
        <td class="left_bottom">運送業者</td>
        <td class="bottom">{$form.permit.f.1.1.11.r.html}</td>
        <td class="bottom">{$form.permit.f.1.1.11.w.html}</td>
    </tr>
    <tr>
        <td bgcolor="#ffdfff" class="top_left" colspan="2">一部共有マスタ</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.1.2.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.1.2.0.w.html}</td>
    </tr>
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" rowspan="4"></td>
        <td class="top_left">スタッフ</td>
        <td>{$form.permit.f.1.2.1.r.html}</td>
        <td>{$form.permit.f.1.2.1.w.html}</td>
    </tr>
    <tr>
        <td class="left">商品</td>
        <td>{$form.permit.f.1.2.2.r.html}</td>
        <td>{$form.permit.f.1.2.2.w.html}</td>
    </tr>
    <tr>
        <td class="left">Ｍ区分</td>
        <td>{$form.permit.f.1.2.3.r.html}</td>
        <td>{$form.permit.f.1.2.3.w.html}</td>
    </tr>
    <tr>
        <td class="left_bottom">製品区分</td>
        <td class="bottom">{$form.permit.f.1.2.4.r.html}</td>
        <td class="bottom">{$form.permit.f.1.2.4.w.html}</td>
    </tr>
    <tr>
        <td bgcolor="#ffdfff" class="top_left" colspan="2">帳票設定</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.1.3.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.1.3.0.w.html}</td>
    </tr>
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" rowspan="3"></td>
        <td class="top_left">発注書コメント</td>
        <td>{$form.permit.f.1.3.1.r.html}</td>
        <td>{$form.permit.f.1.3.1.w.html}</td>
    </tr>
    <tr>
        <td class="left">請求書</td>
        <td>{$form.permit.f.1.3.2.r.html}</td>
        <td>{$form.permit.f.1.3.2.w.html}</td>
    </tr>
    <tr>
        <td class="left_bottom">売上伝票</td>
        <td class="bottom">{$form.permit.f.1.3.3.r.html}</td>
        <td class="bottom">{$form.permit.f.1.3.3.w.html}</td>
    </tr>
    <tr>
        <td bgcolor="#ffdfff" class="top_left" colspan="2">システム設定</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.1.4.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.1.4.0.w.html}</td>
    </tr>
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" rowspan="5"></td>
        <td class="top_left">自社プロフィール</td>
        <td>{$form.permit.f.1.4.1.r.html}</td>
        <td>{$form.permit.f.1.4.1.w.html}</td>
    </tr>
    <tr>
        <td class="left">買掛残高初期設定</td>
        <td>{$form.permit.f.1.4.2.r.html}</td>
        <td>{$form.permit.f.1.4.2.w.html}</td>
    </tr>
    <tr>
        <td class="left">売掛残高初期設定</td>
        <td>{$form.permit.f.1.4.3.r.html}</td>
        <td>{$form.permit.f.1.4.3.w.html}</td>
    </tr>
    <tr>
        <td class="left">請求残高初期設定</td>
        <td>{$form.permit.f.1.4.4.r.html}</td>
        <td>{$form.permit.f.1.4.4.w.html}</td>
    </tr>
    <tr>
        <td class="left_bottom">パスワード変更</td>
        <td class="bottom">{$form.permit.f.1.4.5.r.html}</td>
        <td class="bottom">{$form.permit.f.1.4.5.w.html}</td>
    </tr>
    <tr>
        <td bgcolor="#ffdfff" class="top_left" colspan="2">本部管理マスタ</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.1.5.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.1.5.0.w.html}</td>
    </tr>
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" rowspan="4"></td>
        <td class="top_left">業種</td>
        <td>{$form.permit.f.1.5.1.r.html}</td>
        <td>{$form.permit.f.1.5.1.w.html}</td>
    </tr>
    <tr>
        <td class="left">業態</td>
        <td>{$form.permit.f.1.5.2.r.html}</td>
        <td>{$form.permit.f.1.5.2.w.html}</td>
    </tr>
    <tr>
        <td class="left">施設</td>
        <td>{$form.permit.f.1.5.3.r.html}</td>
        <td>{$form.permit.f.1.5.3.w.html}</td>
    </tr>
    <tr>
        <td class="left_bottom">売上・更新済一覧</td>
        <td class="bottom">{$form.permit.f.1.5.4.r.html}</td>
        <td class="bottom">{$form.permit.f.1.5.4.w.html}</td>
    </tr>
</table>

    <tr>
        <td colspan="3" align="right">{$form.form_set_button.html}　　{$form.form_print_button.html}　　{$form.form_return_button.html}</td>
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

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

{$form.hidden}

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
{*--------------- メッセージ類 e n d ---------------*}

{*+++++++++++++++ 画面表示１ begin +++++++++++++++*}
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
<table width="100%" class="Data_Table" bgcolor="#ffffff">
<col width="17" style="font: bold 15px;">
<col width="17" style="font: bold;">
<col width="17" style="font: bold;">
<col>
<col width="35" align="center">
<col width="35" align="center">
    <tr bgcolor="#555555" style="color: #ffffff; font-weight: bold;">
        <td class="bottom" colspan="4"></td>
        <td class="bottom">表示</td>
        <td class="bottom">入力</td>
    </tr>
    {* FC *}
    <tr bgcolor="#e5b0f0">
        <td class="top" colspan="4">ＦＣ</td>
        <td class="bottom">{$form.permit.f.0.0.0.r.html}</td>
        <td class="bottom">{$form.permit.f.0.0.0.w.html}</td>
    </tr>
    {* 売上管理 *}
    <tr>
        <td bgcolor="#e5b0f0" class="left" rowspan="{$var.f_rowspan}"></td>
        <td bgcolor="#f0c7f0" class="top_left" colspan="3">{$ary_f_mod_data[0][0]}</td>
        <td bgcolor="#f0c7f0" class="bottom">{$form.permit.f.1.0.0.r.html}</td>
        <td bgcolor="#f0c7f0" class="bottom">{$form.permit.f.1.0.0.w.html}</td>
    </tr>
    {* 売上管理 - 予定取引 *}
    <tr>
        <td bgcolor="#f0c7f0" class="left_bottom" rowspan="{$var.f_menu_rowspan[0]}"></td>
        <td bgcolor="#ffdfff" class="top_left_bottom" colspan="2">{$ary_f_mod_data[0][1][0][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.1.1.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.1.1.0.w.html}</td>
    </tr>
    {* 売上管理 - 売上取引 *}
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" colspan="2">{$ary_f_mod_data[0][1][1][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.1.2.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.1.2.0.w.html}</td>
    </tr>
    {* 売上管理 - 請求管理 *}
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" colspan="2">{$ary_f_mod_data[0][1][2][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.1.3.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.1.3.0.w.html}</td>
    </tr>
    {* 売上管理 - 入金管理 *}
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" colspan="2">{$ary_f_mod_data[0][1][3][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.1.4.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.1.4.0.w.html}</td>
    </tr>
    {* 売上管理 - 実績管理 *}
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" colspan="2">{$ary_f_mod_data[0][1][4][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.1.5.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.1.5.0.w.html}</td>
    </tr>
    {* 仕入管理 *}
    <tr>
        <td bgcolor="#f0c7f0" class="top_left" colspan="3">{$ary_f_mod_data[1][0]}</td>
        <td bgcolor="#f0c7f0" class="bottom">{$form.permit.f.2.0.0.r.html}</td>
        <td bgcolor="#f0c7f0" class="bottom">{$form.permit.f.2.0.0.w.html}</td>
    </tr>
    {* 仕入管理 - 発注取引 *}
    <tr>
        <td bgcolor="#f0c7f0" class="left_bottom" rowspan="{$var.f_menu_rowspan[1]}"></td>
        <td bgcolor="#ffdfff" class="top_left_bottom" colspan="2">{$ary_f_mod_data[1][1][0][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.2.1.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.2.1.0.w.html}</td>
    </tr>
    {* 仕入管理 - 仕入取引 *}
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" colspan="2">{$ary_f_mod_data[1][1][1][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.2.2.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.2.2.0.w.html}</td>
    </tr>
    {* 仕入管理 - 支払管理 *}
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" colspan="2">{$ary_f_mod_data[1][1][2][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.2.3.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.2.3.0.w.html}</td>
    </tr>
    {* 仕入管理 - 実績管理 *}
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" colspan="2">{$ary_f_mod_data[1][1][3][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.2.4.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.2.4.0.w.html}</td>
    </tr>
    {* 在庫管理 *}
    <tr>
        <td bgcolor="#f0c7f0" class="top_left" colspan="3">{$ary_f_mod_data[2][0]}</td>
        <td bgcolor="#f0c7f0" class="bottom">{$form.permit.f.3.0.0.r.html}</td>
        <td bgcolor="#f0c7f0" class="bottom">{$form.permit.f.3.0.0.w.html}</td>
    </tr>
    {* 在庫管理 - 在庫取引 *}
    <tr>
        <td bgcolor="#f0c7f0" class="left_bottom" rowspan="{$var.f_menu_rowspan[2]}"></td>
        <td bgcolor="#ffdfff" class="top_left_bottom" colspan="2">{$ary_f_mod_data[2][1][0][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.3.1.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.3.1.0.w.html}</td>
    </tr>
    {* 在庫管理 - 棚卸管理 *}
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" colspan="2">{$ary_f_mod_data[2][1][1][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.3.2.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.3.2.0.w.html}</td>
    </tr>
    {* 更新 *}
    <tr>
        <td bgcolor="#f0c7f0" class="top_left" colspan="3">{$ary_f_mod_data[3][0]}</td>
        <td bgcolor="#f0c7f0" class="bottom">{$form.permit.f.4.0.0.r.html}</td>
        <td bgcolor="#f0c7f0" class="bottom">{$form.permit.f.4.0.0.w.html}</td>
    </tr>
    {* 更新 - 更新管理 *}
    <tr>
        <td bgcolor="#f0c7f0" class="left_bottom" rowspan="{$var.f_menu_rowspan[3]}"></td>
        <td bgcolor="#ffdfff" class="top_left" colspan="2">{$ary_f_mod_data[3][1][0][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.4.1.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.4.1.0.w.html}</td>
    </tr>
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" rowspan="{$var.f_submenu_rowspan[3][0]}"></td>
        <td class="top_left">{$ary_f_mod_data[3][1][0][1][0]}</td>
        <td>{$form.permit.f.4.1.1.r.html}</td>
        <td>{$form.permit.f.4.1.1.w.html}</td>
    </tr>
    <tr>
        <td class="left">{$ary_f_mod_data[3][1][0][1][1]}</td>
        <td>{$form.permit.f.4.1.2.r.html}</td>
        <td>{$form.permit.f.4.1.2.w.html}</td>
    </tr>
    <tr>
        <td class="left">{$ary_f_mod_data[3][1][0][1][2]}</td>
        <td>{$form.permit.f.4.1.3.r.html}</td>
        <td>{$form.permit.f.4.1.3.w.html}</td>
    </tr>
    <tr>
        <td class="left">{$ary_f_mod_data[3][1][0][1][3]}</td>
        <td>{$form.permit.f.4.1.4.r.html}</td>
        <td>{$form.permit.f.4.1.4.w.html}</td>
    </tr>
    <tr>
        <td class="left_bottom">{$ary_f_mod_data[3][1][0][1][4]}</td>
        <td class="bottom">{$form.permit.f.4.1.5.r.html}</td>
        <td class="bottom">{$form.permit.f.4.1.5.w.html}</td>
    </tr>
    {* データ出力 *}
    <tr>
        <td bgcolor="#f0c7f0" class="top_left" colspan="3">{$ary_f_mod_data[4][0]}</td>
        <td bgcolor="#f0c7f0" class="bottom">{$form.permit.f.5.0.0.r.html}</td>
        <td bgcolor="#f0c7f0" class="bottom">{$form.permit.f.5.0.0.w.html}</td>
    </tr>
    {* データ出力 - 統計情報 *}
    <tr>
        <td bgcolor="#f0c7f0" class="left_bottom" rowspan="{$var.f_menu_rowspan[4]}"></td>
        <td bgcolor="#ffdfff" class="top_left_bottom" colspan="2">{$ary_f_mod_data[4][1][0][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.5.1.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.5.1.0.w.html}</td>
    </tr>
    {* データ出力 - 売上推移 *}
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" colspan="2">{$ary_f_mod_data[4][1][1][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.5.2.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.5.2.0.w.html}</td>
    </tr>
    {* データ出力 - ABC分析 *}
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" colspan="2">{$ary_f_mod_data[4][1][2][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.5.3.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.5.3.0.w.html}</td>
    </tr>
    {* データ出力 - 仕入推移 *}
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" colspan="2">{$ary_f_mod_data[4][1][3][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.5.4.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.5.4.0.w.html}</td>
    </tr>
    {* データ出力 - CSV出力 *}
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" colspan="2">{$ary_f_mod_data[4][1][4][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.5.5.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.5.5.0.w.html}</td>
    </tr>
    {* マスタ・設定 *}
    <tr>
        <td bgcolor="#f0c7f0" class="top_left" colspan="3">{$ary_f_mod_data[5][0]}</td>
        <td bgcolor="#f0c7f0" class="bottom">{$form.permit.f.6.0.0.r.html}</td>
        <td bgcolor="#f0c7f0" class="bottom">{$form.permit.f.6.0.0.w.html}</td>
    </tr>
    {* マスタ・設定 - 個別マスタ *}
    <tr>
        <td bgcolor="#f0c7f0" class="left_bottom" rowspan="{$var.f_menu_rowspan[5]}"></td>
        <td bgcolor="#ffdfff" class="top_left" colspan="2">{$ary_f_mod_data[5][1][0][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.6.1.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.6.1.0.w.html}</td>
    </tr>
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" rowspan="{$var.f_submenu_rowspan[5][0]}"></td>
        <td class="top_left">{$ary_f_mod_data[5][1][0][1][0]}</td>
        <td>{$form.permit.f.6.1.1.r.html}</td>
        <td>{$form.permit.f.6.1.1.w.html}</td>
    </tr>
    <tr>
        <td class="left">{$ary_f_mod_data[5][1][0][1][1]}</td>
        <td>{$form.permit.f.6.1.2.r.html}</td>
        <td>{$form.permit.f.6.1.2.w.html}</td>
    </tr>
    <tr>
        <td class="left">{$ary_f_mod_data[5][1][0][1][2]}</td>
        <td>{$form.permit.f.6.1.3.r.html}</td>
        <td>{$form.permit.f.6.1.3.w.html}</td>
    </tr>
    <tr>
        <td class="left">{$ary_f_mod_data[5][1][0][1][3]}</td>
        <td>{$form.permit.f.6.1.4.r.html}</td>
        <td>{$form.permit.f.6.1.4.w.html}</td>
    </tr>
    <tr>
        <td class="left">{$ary_f_mod_data[5][1][0][1][4]}</td>
        <td>{$form.permit.f.6.1.5.r.html}</td>
        <td>{$form.permit.f.6.1.5.w.html}</td>
    </tr>
    <tr>
        <td class="left">{$ary_f_mod_data[5][1][0][1][5]}</td>
        <td>{$form.permit.f.6.1.6.r.html}</td>
        <td>{$form.permit.f.6.1.6.w.html}</td>
    </tr>
    <tr>
        <td class="left">{$ary_f_mod_data[5][1][0][1][6]}</td>
        <td>{$form.permit.f.6.1.7.r.html}</td>
        <td>{$form.permit.f.6.1.7.w.html}</td>
    </tr>
    <tr>
        <td class="left">{$ary_f_mod_data[5][1][0][1][7]}</td>
        <td>{$form.permit.f.6.1.8.r.html}</td>
        <td>{$form.permit.f.6.1.8.w.html}</td>
    </tr>
    <tr>
        <td class="left">{$ary_f_mod_data[5][1][0][1][8]}</td>
        <td>{$form.permit.f.6.1.9.r.html}</td>
        <td>{$form.permit.f.6.1.9.w.html}</td>
    </tr>
    <tr>
        <td class="left">{$ary_f_mod_data[5][1][0][1][9]}</td>
        <td>{$form.permit.f.6.1.10.r.html}</td>
        <td>{$form.permit.f.6.1.10.w.html}</td>
    </tr>
    <tr>
        <td class="left">{$ary_f_mod_data[5][1][0][1][10]}</td>
        <td>{$form.permit.f.6.1.11.r.html}</td>
        <td>{$form.permit.f.6.1.11.w.html}</td>
    </tr>
    <tr>
        <td class="left">{$ary_f_mod_data[5][1][0][1][11]}</td>
        <td>{$form.permit.f.6.1.12.r.html}</td>
        <td>{$form.permit.f.6.1.12.w.html}</td>
    </tr>
    <tr>
        <td class="left">{$ary_f_mod_data[5][1][0][1][12]}</td>
        <td>{$form.permit.f.6.1.13.r.html}</td>
        <td>{$form.permit.f.6.1.13.w.html}</td>
    </tr>
    <tr>
        <td class="left_bottom">{$ary_f_mod_data[5][1][0][1][13]}</td>
        <td class="bottom">{$form.permit.f.6.1.14.r.html}</td>
        <td class="bottom">{$form.permit.f.6.1.14.w.html}</td>
    </tr>
    {* マスタ・設定 - 一部共有マスタ *}
    <tr>
        <td bgcolor="#ffdfff" class="top_left" colspan="2">{$ary_f_mod_data[5][1][1][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.6.2.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.6.2.0.w.html}</td>
    </tr>
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" rowspan="{$var.f_submenu_rowspan[5][1]}"></td>
        <td class="top_left">{$ary_f_mod_data[5][1][1][1][0]}</td>
        <td>{$form.permit.f.6.2.1.r.html}</td>
        <td>{$form.permit.f.6.2.1.w.html}</td>
    </tr>
    <tr>
        <td class="left">{$ary_f_mod_data[5][1][1][1][1]}</td>
        <td>{$form.permit.f.6.2.2.r.html}</td>
        <td>{$form.permit.f.6.2.2.w.html}</td>
    </tr>
    <tr>
        <td class="left">{$ary_f_mod_data[5][1][1][1][2]}</td>
        <td>{$form.permit.f.6.2.3.r.html}</td>
        <td>{$form.permit.f.6.2.3.w.html}</td>
    </tr>
    <tr>
        <td class="left">{$ary_f_mod_data[5][1][1][1][3]}</td>
        <td>{$form.permit.f.6.2.4.r.html}</td>
        <td>{$form.permit.f.6.2.4.w.html}</td>
    </tr>
    <tr>
        <td class="left_bottom">{$ary_f_mod_data[5][1][1][1][4]}</td>
        <td class="bottom">{$form.permit.f.6.2.5.r.html}</td>
        <td class="bottom">{$form.permit.f.6.2.5.w.html}</td>
    </tr>
    {* マスタ・設定 - 帳票設定 *}
    <tr>
        <td bgcolor="#ffdfff" class="top_left" colspan="2">{$ary_f_mod_data[5][1][2][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.6.3.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.6.3.0.w.html}</td>
    </tr>
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" rowspan="{$var.f_submenu_rowspan[5][2]}"></td>
        <td class="top_left">{$ary_f_mod_data[5][1][2][1][0]}</td>
        <td>{$form.permit.f.6.3.1.r.html}</td>
        <td>{$form.permit.f.6.3.1.w.html}</td>
    </tr>
    <tr>
        <td class="left">{$ary_f_mod_data[5][1][2][1][1]}</td>
        <td>{$form.permit.f.6.3.2.r.html}</td>
        <td>{$form.permit.f.6.3.2.w.html}</td>
    </tr>
    <tr>
        <td class="left_bottom">{$ary_f_mod_data[5][1][2][1][2]}</td>
        <td class="bottom">{$form.permit.f.6.3.3.r.html}</td>
        <td class="bottom">{$form.permit.f.6.3.3.w.html}</td>
    </tr>
    {* マスタ・設定 - 残高初期設定 *}
    <tr>
        <td bgcolor="#ffdfff" class="top_left" colspan="2">{$ary_f_mod_data[5][1][3][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.6.4.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.6.4.0.w.html}</td>
    </tr>
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" rowspan="{$var.f_submenu_rowspan[5][3]}"></td>
        <td class="top_left">{$ary_f_mod_data[5][1][3][1][0]}</td>
        <td>{$form.permit.f.6.4.1.r.html}</td>
        <td>{$form.permit.f.6.4.1.w.html}</td>
    </tr>
    <tr>
        <td class="left">{$ary_f_mod_data[5][1][3][1][1]}</td>
        <td>{$form.permit.f.6.4.2.r.html}</td>
        <td>{$form.permit.f.6.4.2.w.html}</td>
    </tr>
    <tr>
        <td class="left_bottom">{$ary_f_mod_data[5][1][3][1][2]}</td>
        <td class="bottom">{$form.permit.f.6.4.3.r.html}</td>
        <td class="bottom">{$form.permit.f.6.4.3.w.html}</td>
    </tr>
    {* マスタ・設定 - 本部管理マスタ *}
    <tr>
        <td bgcolor="#ffdfff" class="top_left" colspan="2">{$ary_f_mod_data[5][1][4][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.6.5.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.6.5.0.w.html}</td>
    </tr>
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" rowspan="{$var.f_submenu_rowspan[5][4]}"></td>
        <td class="top_left">{$ary_f_mod_data[5][1][4][1][0]}</td>
        <td>{$form.permit.f.6.5.1.r.html}</td>
        <td>{$form.permit.f.6.5.1.w.html}</td>
    </tr>
    <tr>
        <td class="left">{$ary_f_mod_data[5][1][4][1][1]}</td>
        <td>{$form.permit.f.6.5.2.r.html}</td>
        <td>{$form.permit.f.6.5.2.w.html}</td>
    </tr>
    <tr>
        <td class="left">{$ary_f_mod_data[5][1][4][1][2]}</td>
        <td>{$form.permit.f.6.5.3.r.html}</td>
        <td>{$form.permit.f.6.5.3.w.html}</td>
    </tr>
    <tr>
        <td class="left">{$ary_f_mod_data[5][1][4][1][3]}</td>
        <td>{$form.permit.f.6.5.4.r.html}</td>
        <td>{$form.permit.f.6.5.4.w.html}</td>
    </tr>
    <tr>
        <td class="left_bottom">{$ary_f_mod_data[5][1][4][1][4]}</td>
        <td class="bottom">{$form.permit.f.6.5.5.r.html}</td>
        <td class="bottom">{$form.permit.f.6.5.5.w.html}</td>
    </tr>
</table>

    <tr>
        <td colspan="3" align="right">{$form.form_set_button.html}　{*　{$form.form_print_button.html}　*}　{$form.form_return_button.html}</td>
    </tr>
</table>
{*--------------- 画面表示１ e n d ---------------*}

        </td>
    </tr>
    {*--------------- コンテンツ部 e n d ---------------*}

</table>
{*--------------- 外枠 e n d ---------------*}

{$var.html_footer}

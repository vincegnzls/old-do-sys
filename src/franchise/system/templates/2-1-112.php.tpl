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

{*+++++++++++++++ ���� begin +++++++++++++++*}
<table width="100%" height="90%" class="M_table">

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
{*--------------- ��å������� e n d ---------------*}

{*+++++++++++++++ ����ɽ���� begin +++++++++++++++*}
<table>
    <tr>
        <td colspan="3">

<table class="Data_Table" border="1" width="450">
<col width="150" style="font-weight: bold;">
<col>
    <tr>
        <td class="Title_Purple">ô���ԥ�����</td>
        <td class="Value">{$var.charge_cd}</td>
    </tr>
    <tr>
        <td class="Title_Purple">�����å�̾</td>
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
        <td class="Title_Purple">������¤���Ϳ����</td>
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
        <td class="Title_Purple">��ǧ���¤���Ϳ����</td>
        <td class="Value">{$form.permit_accept.html}</td>
    </tr>
</table>
<br>
{*--------------- ����ɽ���� e n d ---------------*}

        </td>
    </tr>
    <tr>
        <td valign="top">

{*+++++++++++++++ ����ɽ���� begin +++++++++++++++*}
<table width="100%" class="Data_Table" bgcolor="#ffffff">
<col width="17" style="font: bold 15px;">
<col width="17" style="font: bold;">
<col width="17" style="font: bold;">
<col>
<col width="35" align="center">
<col width="35" align="center">
    <tr bgcolor="#555555" style="color: #ffffff; font-weight: bold;">
        <td class="bottom" colspan="4"></td>
        <td class="bottom">ɽ��</td>
        <td class="bottom">����</td>
    </tr>
    {* FC *}
    <tr bgcolor="#e5b0f0">
        <td class="top" colspan="4">�ƣ�</td>
        <td class="bottom">{$form.permit.f.0.0.0.r.html}</td>
        <td class="bottom">{$form.permit.f.0.0.0.w.html}</td>
    </tr>
    {* ������ *}
    <tr>
        <td bgcolor="#e5b0f0" class="left" rowspan="{$var.f_rowspan}"></td>
        <td bgcolor="#f0c7f0" class="top_left" colspan="3">{$ary_f_mod_data[0][0]}</td>
        <td bgcolor="#f0c7f0" class="bottom">{$form.permit.f.1.0.0.r.html}</td>
        <td bgcolor="#f0c7f0" class="bottom">{$form.permit.f.1.0.0.w.html}</td>
    </tr>
    {* ������ - ͽ���� *}
    <tr>
        <td bgcolor="#f0c7f0" class="left_bottom" rowspan="{$var.f_menu_rowspan[0]}"></td>
        <td bgcolor="#ffdfff" class="top_left_bottom" colspan="2">{$ary_f_mod_data[0][1][0][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.1.1.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.1.1.0.w.html}</td>
    </tr>
    {* ������ - ����� *}
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" colspan="2">{$ary_f_mod_data[0][1][1][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.1.2.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.1.2.0.w.html}</td>
    </tr>
    {* ������ - ������� *}
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" colspan="2">{$ary_f_mod_data[0][1][2][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.1.3.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.1.3.0.w.html}</td>
    </tr>
    {* ������ - ������� *}
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" colspan="2">{$ary_f_mod_data[0][1][3][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.1.4.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.1.4.0.w.html}</td>
    </tr>
    {* ������ - ���Ӵ��� *}
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" colspan="2">{$ary_f_mod_data[0][1][4][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.1.5.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.1.5.0.w.html}</td>
    </tr>
    {* �������� *}
    <tr>
        <td bgcolor="#f0c7f0" class="top_left" colspan="3">{$ary_f_mod_data[1][0]}</td>
        <td bgcolor="#f0c7f0" class="bottom">{$form.permit.f.2.0.0.r.html}</td>
        <td bgcolor="#f0c7f0" class="bottom">{$form.permit.f.2.0.0.w.html}</td>
    </tr>
    {* �������� - ȯ����� *}
    <tr>
        <td bgcolor="#f0c7f0" class="left_bottom" rowspan="{$var.f_menu_rowspan[1]}"></td>
        <td bgcolor="#ffdfff" class="top_left_bottom" colspan="2">{$ary_f_mod_data[1][1][0][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.2.1.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.2.1.0.w.html}</td>
    </tr>
    {* �������� - ������� *}
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" colspan="2">{$ary_f_mod_data[1][1][1][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.2.2.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.2.2.0.w.html}</td>
    </tr>
    {* �������� - ��ʧ���� *}
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" colspan="2">{$ary_f_mod_data[1][1][2][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.2.3.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.2.3.0.w.html}</td>
    </tr>
    {* �������� - ���Ӵ��� *}
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" colspan="2">{$ary_f_mod_data[1][1][3][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.2.4.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.2.4.0.w.html}</td>
    </tr>
    {* �߸˴��� *}
    <tr>
        <td bgcolor="#f0c7f0" class="top_left" colspan="3">{$ary_f_mod_data[2][0]}</td>
        <td bgcolor="#f0c7f0" class="bottom">{$form.permit.f.3.0.0.r.html}</td>
        <td bgcolor="#f0c7f0" class="bottom">{$form.permit.f.3.0.0.w.html}</td>
    </tr>
    {* �߸˴��� - �߸˼�� *}
    <tr>
        <td bgcolor="#f0c7f0" class="left_bottom" rowspan="{$var.f_menu_rowspan[2]}"></td>
        <td bgcolor="#ffdfff" class="top_left_bottom" colspan="2">{$ary_f_mod_data[2][1][0][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.3.1.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.3.1.0.w.html}</td>
    </tr>
    {* �߸˴��� - ê������ *}
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" colspan="2">{$ary_f_mod_data[2][1][1][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.3.2.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.3.2.0.w.html}</td>
    </tr>
    {* ���� *}
    <tr>
        <td bgcolor="#f0c7f0" class="top_left" colspan="3">{$ary_f_mod_data[3][0]}</td>
        <td bgcolor="#f0c7f0" class="bottom">{$form.permit.f.4.0.0.r.html}</td>
        <td bgcolor="#f0c7f0" class="bottom">{$form.permit.f.4.0.0.w.html}</td>
    </tr>
    {* ���� - �������� *}
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
    {* �ǡ������� *}
    <tr>
        <td bgcolor="#f0c7f0" class="top_left" colspan="3">{$ary_f_mod_data[4][0]}</td>
        <td bgcolor="#f0c7f0" class="bottom">{$form.permit.f.5.0.0.r.html}</td>
        <td bgcolor="#f0c7f0" class="bottom">{$form.permit.f.5.0.0.w.html}</td>
    </tr>
    {* �ǡ������� - ���׾��� *}
    <tr>
        <td bgcolor="#f0c7f0" class="left_bottom" rowspan="{$var.f_menu_rowspan[4]}"></td>
        <td bgcolor="#ffdfff" class="top_left_bottom" colspan="2">{$ary_f_mod_data[4][1][0][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.5.1.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.5.1.0.w.html}</td>
    </tr>
    {* �ǡ������� - ����� *}
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" colspan="2">{$ary_f_mod_data[4][1][1][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.5.2.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.5.2.0.w.html}</td>
    </tr>
    {* �ǡ������� - ABCʬ�� *}
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" colspan="2">{$ary_f_mod_data[4][1][2][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.5.3.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.5.3.0.w.html}</td>
    </tr>
    {* �ǡ������� - ������� *}
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" colspan="2">{$ary_f_mod_data[4][1][3][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.5.4.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.5.4.0.w.html}</td>
    </tr>
    {* �ǡ������� - CSV���� *}
    <tr>
        <td bgcolor="#ffdfff" class="left_bottom" colspan="2">{$ary_f_mod_data[4][1][4][0]}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.5.5.0.r.html}</td>
        <td bgcolor="#ffdfff" class="bottom">{$form.permit.f.5.5.0.w.html}</td>
    </tr>
    {* �ޥ��������� *}
    <tr>
        <td bgcolor="#f0c7f0" class="top_left" colspan="3">{$ary_f_mod_data[5][0]}</td>
        <td bgcolor="#f0c7f0" class="bottom">{$form.permit.f.6.0.0.r.html}</td>
        <td bgcolor="#f0c7f0" class="bottom">{$form.permit.f.6.0.0.w.html}</td>
    </tr>
    {* �ޥ��������� - ���̥ޥ��� *}
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
    {* �ޥ��������� - ������ͭ�ޥ��� *}
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
    {* �ޥ��������� - Ģɼ���� *}
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
    {* �ޥ��������� - �Ĺ������� *}
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
    {* �ޥ��������� - ���������ޥ��� *}
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
        <td colspan="3" align="right">{$form.form_set_button.html}��{*��{$form.form_print_button.html}��*}��{$form.form_return_button.html}</td>
    </tr>
</table>
{*--------------- ����ɽ���� e n d ---------------*}

        </td>
    </tr>
    {*--------------- ����ƥ���� e n d ---------------*}

</table>
{*--------------- ���� e n d ---------------*}

{$var.html_footer}
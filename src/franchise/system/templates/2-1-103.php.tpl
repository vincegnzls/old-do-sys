{$var.html_header}

<script language="javascript">
{$var.code_value}
{$var.contract}
{$var.js}
</script>

<body bgcolor="#D8D0C8" {if $var.complete_flg != true} onLoad="{$var.onload}{if $smarty.session.group_kind == '2'}onload_code_disable() {/if}"{/if}>
<form {$form.attributes}>

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

{if $var.update_con_mesg != null}
<div class="note">
	<li>�ݤ��ʬ���ѹ����줿���ᡢ�ʲ��η���϶�ۤδݤ������Ƽ»ܤ��ޤ�����</li><br>
	{$var.update_con_mesg}
</div>
{/if}

<span style="color: #ff0000; font-weight: bold; line-height: 130%;">
    {if $form.form_area_id.error != null}
        <li>{$form.form_area_id.error}<br>
    {/if}
    {if $form.form_btype.error != null}
        <li>{$form.form_btype.error}<br>
    {/if}
    {if $form.form_shop_gr_1.error != null}
        <li>{$form.form_shop_gr_1.error}<br>
    {/if}
    {if $var.client_cd_err != null}
        <li>{$var.client_cd_err}<br>
    {/if}
    {if $form.form_client.error != null}
        <li>{$form.form_client.error}<br>
    {/if}
    {if $form.form_client_name.error != null}
        <li>{$form.form_client_name.error}<br>
    {/if}
    {if $form.form_client_cname.error != null}
        <li>{$form.form_client_cname.error}<br>
    {/if}
    {if $form.form_client_gr.error != null}
        <li>{$form.form_client_gr.error}<br>
    {/if}
    {if $form.form_post.error != null}
        <li>{$form.form_post.error}<br>
    {/if}
    {if $form.form_address1.error != null}
        <li>{$form.form_address1.error}<br>
    {/if}
    {if $form.form_capital.error != null}
        <li>{$form.form_capital.error}<br>
    {/if}
    {if $form.form_tel.error != null}
        <li>{$form.form_tel.error != null}<br>
    {elseif $var.tel_err != null}
        <li>{$var.tel_err}<br>
    {/if}
    {if $form.form_fax.error != null}
        <li>{$form.form_fax.error}<br>
    {/if}
    {if $var.email_err != null}
        <li>{$var.email_err}<br>
    {/if}
    {if $form.form_parent_establish_day.error != null}
        <li>{$form.form_parent_establish_day.error}<br>
    {elseif $var.parent_esday_err != null}
        <li>{$var.parent_esday_err}<br>
    {/if}
    {if $form.form_company_tel.error != null}
        <li>{$form.form_company_tel.error}<br>
    {/if}
    {if $var.url_err != null}
        <li>{$var.url_err}<br>
    {/if}
{*    {if $form.form_rep_name.error != null}
        <li>{$form.form_rep_name.error}<br>
    {/if}*}
    {if $var.rep_cell_err != null}
        <li>{$var.rep_cell_err}<br>
    {/if}
    {if $form.form_direct_tel.error != null}
        <li>{$form.form_direct_tel.error}<br>
    {elseif $var.d_tel_err != null}
        <li>{$var.d_tel_err}<br>
    {/if}
    {if $form.form_trade_stime1.error != null || 
        $form.form_trade_etime1.error != null || 
        $form.form_trade_stime2.error != null || 
        $form.form_trade_etime2.error != null}
        <li>�ĶȻ��֤�Ⱦ�ѿ����ΤߤǤ���<br>
    {/if}
    {if $var.claim_err != null}
        <li>{$var.claim_err}<br>
    {/if}
    {if $form.form_cledit_limit.error != null}
        <li>{$form.form_cledit_limit.error}<br>
    {/if}
    {if $var.close_err != null}
        <li>{$var.close_err}<br>
    {/if}
    {if $form.form_pay_m.error != null}
        <li>{$form.form_pay_m.error}<br>
    {/if}
    {if $form.form_pay_d.error != null}
        <li>{$form.form_pay_d.error}<bt>
    {/if}
    {if $form.form_cont_s_day.error != null}
        <li>{$form.form_cont_s_day.error}<br>
    {elseif $var.sday_err != null}
        <li>{$var.sday_err}<br>
    {/if}
    {if $form.form_cont_peri.error != null}
        <li>{$form.form_cont_peri.error}<br>
    {/if}
    {if $form.form_cont_r_day.error != null}
        <li>{$form.form_cont_r_day.error}<br>
    {elseif $var.rday_err != null}
        <li>{$var.rday_err}<br>
    {elseif $var.sday_rday_err != null}
        <li>{$var.sday_rday_err}<br>
    {/if}
    {if $form.form_establish_day.error != null}
        <li>{$form.form_establish_day.error}<br>
    {elseif $var.esday_err != null}
        <li>{$var.esday_err}<br>
    {/if}
    {if $var.intro_act_err != null}
        <li>{$var.intro_act_err}<br>
    {/if}
	{if $form.form_intro_act.error != null}
        <li>{$form.form_intro_act.error}<br>
    {/if}
    {if $form.form_account.error != null}
        <li>{$form.form_account.error}<br>
    {/if}
    {if $form.form_cshop.error != null}
        <li>{$form.form_cshop.error}<br>
    {/if}
    {if $form.form_round_start.error != null}
        <li>{$form.form_round_start.error}<br>
    {elseif $var.rsday_err != null}
        <li>{$var.rsday_err}<br>
    {/if}
    {if $form.trade_aord_1.error != null}
        <li>{$form.trade_aord_1.error}<br>
    {/if}
    {if $var.claim_coax_err != null}
        <li>{$var.claim_coax_err}<br>
    {/if}
    {if $var.claim_tax_div_err != null}
        <li>{$var.claim_tax_div_err}<br>
    {/if}
    {if $var.claim_tax_franct_err != null}
        <li>{$var.claim_tax_franct_err}<br>
    {/if}
    {if $var.claim_month_err != null}
        <li>{$var.claim_month_err}<br>
    {/if}
    {if $form.claim1_monthly_check[0].error != null}
        <li>{$form.claim1_monthly_check[0].error}<br>
    {/if}
    {if $form.claim2_monthly_check[0].error != null}
        <li>{$form.claim2_monthly_check[0].error}<br>
    {/if}

    {if $form.form_s_pattern_select.error != null}
        <li>{$form.form_s_pattern_select.error}<br>
    {/if}
    {if $form.form_c_pattern_select.error != null}
        <li>{$form.form_c_pattern_select.error}<br>
    {/if}
    {if $var.claim_c_tax_div_err != null}
        <li>{$var.claim_c_tax_div_err}<br>
    {/if}
    {if $var.claim2_err != null}
        <li>{$var.claim2_err}<br>
    {/if}
    {if $var.claim2_coax_err != null}
        <li>{$var.claim2_coax_err}<br>
    {/if}
    {if $var.claim2_tax_div_err != null}
        <li>{$var.claim2_tax_div_err}<br>
    {/if}
    {if $var.claim2_tax_franct_err != null}
        <li>{$var.claim2_tax_franct_err}<br>
    {/if}
    {if $var.claim2_c_tax_div_err != null}
        <li>{$var.claim2_c_tax_div_err}<br>
    {/if}
    {if $form.form_deliver_note.error != null}
        <li>{$form.form_deliver_note.error}<br>
    {/if}
    {if $form.form_charge_branch_id.error != null}
        <li>{$form.form_charge_branch_id.error}<br>
    {/if}
    {if $form.del_err_mess != null}
        <li>{$var.del_err_mess}<br>
    {/if}
    {if $var.claim_del_err != null}
        <li>{$var.claim_del_err}<br>
    {/if}
    {if $var.client_name1_err != null}
        <li>{$var.client_name1_err}<br>
    {/if}
    {if $var.client_name2_err != null}
        <li>{$var.client_name2_err}<br>
    {/if}
    {if $var.address1_err != null}
        <li>{$var.address1_err}<br>
    {/if}
    {if $var.address2_err != null}
        <li>{$var.address2_err}<br>
    {/if}
    {if $var.address3_err != null}
        <li>{$var.address3_err}<br>
    {/if}
</span>
<br>
{*--------------- ��å������� e n d ---------------*}

{*+++++++++++++++ ����ɽ���� begin +++++++++++++++*}
<table width="810" >
    <tr>
        <td colspan="2">

<table width="100%">
    <tr>
{*        <td>{$form.button.list_confirm_button.html}</td>*}
        <td align="right">{if $smarty.get.client_id != null}{$form.back_button.html}��{$form.next_button.html}{/if}</td>
    </tr>
</table>

        </td>
    </tr>
    <tr>
        <td>

<table class="Data_Table" border="1" width="402">
    <tr>
        <td class="Type" width="60" align="center"><b>����</b></td>
        <td class="Value">{$form.form_state.html}</td>
    </tr>
</table>

        </td>
        <td>

<table class="Data_Table" border="1" width="502">
    <tr>
        <td class="Title_Purple" width="60"><b>���롼��</b></td>
        <td class="Value">{$form.form_client_gr.html}{$form.form_parents_div.html}</td>
{*
        <td class="Type" width="60" align="center"><b>����</b></td>
        <td class="Value">{$form.form_type.html}</td>
*}
    </tr>
</table>

        </td>
    </tr> 
    <tr>
        <td>

<table class="Data_Table" border="1" width="402">
    <tr>
        <td class="Title_Purple" width="60"><b>�϶�<font color="#ff0000">��</font></b></td>
        <td class="Value">{$form.form_area_id.html}</td>
    </tr>
</table>

        </td>
        <td>

<table class="Data_Table" border="1" width="402">
    <tr>
        <td class="Title_Purple" width="60"><b>�ȼ�<font color="#ff0000">��</font></b></td>
        <td class="Value">{$form.form_btype.html}</td>
    </tr>
</table>

        </td>
    </tr>
    <tr>
        <td>

<table class="Data_Table" border="1" width="402">
    <tr>
        <td class="Title_Purple" width="60"><b>����</b></td>
        <td class="Value">{$form.form_inst.html}</td>
    </tr>
</table>

        </td>
        <td>

<table class="Data_Table" border="1" width="402">
    <tr>
        <td class="Title_Purple" width="60"><b>����</b></td>
        <td class="Value">{$form.form_bstruct.html}</td>
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

<div style="text-align: right; font: bold; color: #3300ff;">
    ��������̾����ɼ�������ʤ����ϡ�������̾�α����Υ����å����դ��Ʋ�������
</div>
<div style="text-align: right; font: bold; color: #3300ff;">
    �������ΰ���ȥ�٥�Υե���ȥ��������礭�����������硢������̾1��2�ϣ���ʸ�����⡢����1��3�ϣ���ʸ���������Ͽ���Ʋ�������
</div>

<table class="Data_Table" border="1" width="100%" style="border: 2px solid #3300ff;">
<col width="160" style="font-weight: bold;">
<col width="250">
<col width="160" style="font-weight: bold;">
<col width="400">
<col>
    <tr>
        <td class="Title_Purple">�����襳����<font color="#ff0000">��</font></td>
        <td class="Value" colspan="3">
        {$form.form_client.html}{if $var.complete_flg != true}����{$form.form_cd_search.html}��{/if}
        </td>
    </tr>
    <tr>
        <td class="Title_Purple">������̾1<font color="#ff0000">��</font>��{$form.form_client_slip1.html}</td>
        <td class="Value">{$form.form_client_name.html}</td>
        <td class="Title_Purple">������̾1<br>(�եꥬ��)</td>
        <td class="Value">{$form.form_client_read.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">������̾2����{$form.form_client_slip2.html}</td>
        <td class="Value">{$form.form_client_name2.html}
        <td class="Title_Purple">������̾2<br>(�եꥬ��)</td>
        <td class="Value">{$form.form_client_read2.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">ά��<font color="#ff0000">��</font></td>
        <td class="Value">{$form.form_client_cname.html}</td>
        <td class="Title_Purple">ά��<br>(�եꥬ��)</td>
        <td class="Value">{$form.form_cname_read.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">�ɾ�</td>
        <td class="Value" colspan="3">{$form.form_prefix.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">��ɽ�Ի�̾</td>
        <td class="Value">{$form.form_rep_name.html}</td>
        <td class="Title_Purple">��ɽ����</td>
        <td class="Value">{$form.form_rep_position.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">͹���ֹ�<font color="#ff0000">��</font></td>
        <td class="Value" colspan="3">{$form.form_post.html}����{$form.button.input_button.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">����1<font color="#ff0000">��</font></td>
        <td class="Value">{$form.form_address1.html}</td>
        <td class="Title_Purple">����2</td>
        <td class="Value">{$form.form_address2.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">����3<br>(�ӥ�̾��¾)</td>
        <td class="Value" colspan=>{$form.form_address3.html}</td>
        <td class="Title_Purple">����2<br>(�եꥬ��)</td>
        <td class="Value">{$form.form_address_read.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">TEL<font color="#ff0000">��</font></td>
        <td class="Value">{$form.form_tel.html}</td>
        <td class="Title_Purple">FAX</td>
        <td class="Value">{$form.form_fax.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">�϶���</td>
        <td class="Value">{$form.form_establish_day.html}</td>
        <td class="Title_Purple">ô����Email</td>
        <td class="Value">{$form.form_email.html}</td>
    </tr>
</table>

        </td>
    </tr>
    <tr>
        <td>

<table class="Data_Table" border="1" width="100%" style="border: 2px solid #3300ff;">
<col width="160" style="font-weight: bold;">
<col width="250">
<col width="160" style="font-weight: bold;">
<col width="400">
    <tr>
        <td class="Title_Parent">�Ʋ��̾</td>
        <td class="Value">{$form.form_company_name.html}</td>
        <td class="Title_Parent">�Ʋ��TEL</td>
        <td class="Value">{$form.form_company_tel.html}</td>
    </tr>
    <tr>       
        <td class="Title_Parent">�Ʋ�ҽ���</td>
        <td class="Value" colspan="3">{$form.form_company_address.html}</td>
    </tr>  
    <tr>
        <td class="Title_Parent">���ܶ�</td>
        <td class="Value">{$form.form_capital.html}����</td>
        <td class="Title_Parent">�Ʋ���϶���</td>
        <td class="Value">{$form.form_parent_establish_day.html}</td>
    </tr>
    <tr>
        <td class="Title_Parent">�Ʋ����ɽ�Ի�̾</td>
        <td class="Value">{$form.form_parent_rep_name.html}</td>
        <td class="Title_Parent">URL</td>
        <td class="Value">{$form.form_url.html}</td>
    </tr>
</table>

        </td>
    </tr>
    <tr>
        <td>

<table class="Data_Table" border="1" width="100%">
<col width="85" style="font-weight: bold;">
<col width="20" style="font-weight: bold;" align="center">
<col width="50" style="font-weight: bold;">
<col width="200">
<col width="50" style="font-weight: bold;">
<col width="200">
<col width="50" style="font-weight: bold;">
<col width="300">
    <tr>
        <td class="Title_Purple" rowspan="4">ô������</td>
        <td class="Title_Purple">1</td>
        <td class="Title_Purple">����</td>
        <td class="Value">{$form.form_charger_part1.html}</td>
        <td class="Title_Purple">��</td>
        <td class="Value">{$form.form_charger_represe1.html}</td>
        <td class="Title_Purple">��̾</td>
        <td class="Value">{$form.form_charger1.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">2</td>
        <td class="Title_Purple">����</td>
        <td class="Value">{$form.form_charger_part2.html}</td>
        <td class="Title_Purple">��</td>
        <td class="Value">{$form.form_charger_represe2.html}</td>
        <td class="Title_Purple">��̾</td>
        <td class="Value">{$form.form_charger2.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">3</td>
        <td class="Title_Purple">����</td>
        <td class="Value">{$form.form_charger_part3.html}</td>
        <td class="Title_Purple">��</td>
        <td class="Value">{$form.form_charger_represe3.html}</td>
        <td class="Title_Purple">��̾</td>
        <td class="Value">{$form.form_charger3.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple" colspan="2">����</td>
        <td class="Value" colspan="5">{$form.form_charger_note.html}</td>
    </tr>
</table>

        </td>
    </tr>
    <tr>
        <td>
<table class="Data_Table" border="1" width="100%">
<col width="65" style="font-weight: bold;">
<col width="93" style="font-weight: bold;">
<col width="250">
<col width="160" style="font-weight: bold;">
<col>
    <tr>
        <td class="Title_Purple" colspan="2">�ĶȻ���</td>
        <td class="Value">{$form.form_trade_stime1.html} �� {$form.form_trade_etime1.html} <br>{$form.form_trade_stime2.html} �� {$form.form_trade_etime2.html}</td>
        <td class="Title_Purple">����</td>
        <td class="Value">{$form.form_holiday.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple" colspan="2">{$form.intro_claim_link.html}</td>
        <td class="Value" colspan="4">{$form.form_claim.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple" colspan="2">{$form.intro_claim_link2.html}</td>
        <td class="Value" colspan="3">{$form.form_claim2.html}��{$form.form_warning.html}<br>
    </tr>
    <tr>
        <td class="Title_Purple" colspan="2">�����<font color="#000000">��</font></td>
        <td class="Value" colspan="3">
        <font color="#0000ff"><b>����������������˥����å����Ʋ�������</b></font>
        <br>
        {foreach from=$form.claim1_monthly_check item=item key=i}
            {$form.claim1_monthly_check[$i].html}
        {/foreach}
        </td>
    </tr>
    <tr>
        <td class="Title_Purple" colspan="2">Ϳ������</td>
        <td class="Value">{$form.form_cledit_limit.html}����</td>
        <td class="Title_Purple">������</td>
        <td class="Value">{$form.form_col_terms.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple" colspan="2">�����ʬ<font color="#ff0000">��</font></td>
        <td class="Value" colspan="3">{$form.trade_aord_1.html}<b><font color="#ff0000">��</font><font color="#0000ff">�������ξ�������ϳ����ɻߤΤ����������������ԤʤäƲ�������</font></b></td>
    </tr>
    <tr>
        <td class="Title_Purple" colspan="2">����<font color="#ff0000">��</font><font color="#000000">��</font></td>
        <td class="Value">{$form.form_close.html}</td>
        <td class="Title_Purple">������<font color="#ff0000">��</font><font color="#000000">��</font></td>
        <td class="Value">{$form.form_pay_m.html}��{$form.form_pay_d.html}<b><font color="#ff0000">��</font><font color="#0000ff">�������ˤĤ��Ƥ������Ƚ�������Ʊ��Ǥ���</font></b></td>
    </tr>
    <tr>
        <td class="Title_Purple" colspan="2">������ˡ</td>
        <td class="Value" colspan="3">{$form.form_pay_way.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple" colspan="2">������Ը���</td>
        <td class="Value"colspan="3">{$form.form_bank.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple" colspan="2">����̾��1</td>
        <td class="Value">{$form.form_pay_name.html}</td>
        <td class="Title_Purple">����̾��2</td>
        <td class="Value">{$form.form_account_name.html}</td>
   </tr>
    <tr>
        <td class="Title_Purple" colspan="2">��Լ������ô��ʬ</td>
        <td class="Value" colspan="3">{$form.form_bank_div.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple" colspan="2">����ǯ����</td>
        <td class="Value">{$form.form_cont_s_day.html}</td>
        <td class="Title_Purple">���󹹿���</td>
        <td class="Value">{$form.form_cont_r_day.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple" colspan="2">�������</td>
        <td class="Value">{$form.form_cont_peri.html}ǯ</td>
        <td class="Title_Purple">����λ��</td>
        <td class="Value">{$form.form_cont_e_day.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple" rowspan="3">�����ɼ</td>
        <td class="Title_Purple">ȯ��<font color="#ff0000">��</font></td>
        <td class="Value" colspan="3">{$form.form_slip_out.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">ȯ�Ը�<font color="#ff0000">��</font></td>
        <td class="Value" colspan="3">{$form.form_s_pattern_select.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">������</td>
        <td class="Value" colspan="3">{$form.form_deliver_radio.html}<br>
        <font color="#0000FF"><b>�����̥����Ȥ����򤷤���硢��Ͽ���������Ȥ������ʳ���ͭ���ˤʤ�ޤ���</b></font><br>
        {$form.form_deliver_note.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple" rowspan="5">�����</td>
        <td class="Title_Purple">����</td>
        <td class="Value" colspan="3">{$form.form_bill_address_font.html}�������ΰ���ȥ�٥�Υե���ȥ��������礭������</td>
    </tr>
<!--
    <tr>
        <td class="Title_Purple">�����ϰ�<font color="#ff0000">��</font></td>
        <td class="Value" colspan="3">{$form.form_claim_scope.html}</td>
    </tr>
-->
    <tr>
        <td class="Title_Purple">ȯ��<font color="#ff0000">��</font><font color="#000000">��</font></td>
        <td class="Value" colspan="3">{$form.form_claim_out.html}<br>
        <font color="#0000FF"><b>������οƻҴط����ʤ���硢������������������������Ʊ�ͤǤ���</b></font></td>
    </tr>
    <tr>
        <td class="Title_Purple">����<font color="#ff0000">��</font></td>
        <td class="Value" colspan="3">{$form.form_claim_send.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">ȯ�Ը�<font color="#ff0000">��</font></td>
        <td class="Value" colspan="3">{$form.form_c_pattern_select.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">����</td>
        <td class="Value" colspan="4">{$form.form_claim_note.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">���<font color="#ff0000">��</font></td>
        <td class="Title_Purple">�ޤ���ʬ<font color="#000000">��</font></td>
        <td class="Value" colspan="3">{$form.form_coax.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple" rowspan="3">������<font color="#ff0000">��</font></td>
        <td class="Title_Purple">����ñ��<font color="#000000">��</font></td>
        <td class="Value" colspan="3">{$form.form_tax_div.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">ü����ʬ<font color="#000000">��</font></td>
        <td class="Value" colspan="3">{$form.form_tax_franct.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">���Ƕ�ʬ<font color="#000000">��</font></td>
        <td class="Value" colspan="3">{$form.form_c_tax_div.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple" colspan="2">����������������¾</td>
        <td class="Value" colspan="3">{$form.form_note.html}</td>
    </tr>
</table>

        </td>
    </tr>
    <tr>
        <td>

<table class="Data_Table" border="1" width="100%">
<col width="160" style="font-weight: bold;">
<col>
    <tr>
        <td class="Title_Purple">{$form.intro_act_link.html}</td>
        <td class="Value">{if $smarty.session.group_kind == '2'}{$form.form_client_div.html}��{/if}{$form.form_intro_act.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">�����������̾</td>
        <td class="Value">{$form.form_trans_account.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">���/��Ź̾</td>
        <td class="Value">{$form.form_bank_fc.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">�����ֹ�</td>
        <td class="Value">{$form.form_account_num.html}</td>
    </tr>
</table>

        </td>
    </tr>
    <tr>
        <td>

<table class="Data_Table" border="1" width="100%">
<col width="160" style="font-weight: bold;">
<col width="250">
<col width="160" style="font-weight: bold;">
<col>
    <tr>
        <td class="Title_Purple">ô����Ź<font color="#ff0000">��</font></td>
        <td class="Value" colspan="3">{$form.form_charge_branch_id.html}</td>
    </tr>
    <tr>
{*        <td class="Title_Purple">����ô��1</td>*}
        <td class="Title_Purple">������ô��</td>
        <td class="Value">{$form.form_c_staff_id1.html}</td>
{*        <td class="Title_Purple">����ô��2</td>*}
        <td class="Title_Purple">�������Ұ�</td>
        <td class="Value">{$form.form_c_staff_id2.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">��󳫻���</td>
        <td class="Value" colspan="3">{$form.form_round_start.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">�������</td>
        <td class="Value" colspan="3">{$form.form_record.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">���׻���</td>
        <td class="Value" colspan="3">{$form.form_important.html}</td>
    </tr>
</table>

<table width="100%">
    <tr>
        <td><font color="#ff0000"><b>����ɬ�����ϤǤ���</b></font>����<font color="#000000">��</font><font color="#555555"><b>�ι��ܤ˴ؤ��ơ�������������������˹�碌�ޤ���</b></td>
        <td align="right">{$form.button.entry_button.html}��{*��{$form.button.res_button.html}*}{$form.button.ok_button.html}����{$form.button.contract_button.html}����{$form.button.back_button.html}
        </td>
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
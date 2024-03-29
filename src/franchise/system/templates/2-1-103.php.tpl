{$var.html_header}

<script language="javascript">
{$var.code_value}
{$var.contract}
{$var.js}
</script>

<body bgcolor="#D8D0C8" {if $var.complete_flg != true} onLoad="{$var.onload}{if $smarty.session.group_kind == '2'}onload_code_disable() {/if}"{/if}>
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

{if $var.update_con_mesg != null}
<div class="note">
	<li>丸め区分が変更されたため、以下の契約は金額の丸め処理を再実施しました。</li><br>
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
        <li>営業時間は半角数字のみです。<br>
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
{*--------------- メッセージ類 e n d ---------------*}

{*+++++++++++++++ 画面表示１ begin +++++++++++++++*}
<table width="810" >
    <tr>
        <td colspan="2">

<table width="100%">
    <tr>
{*        <td>{$form.button.list_confirm_button.html}</td>*}
        <td align="right">{if $smarty.get.client_id != null}{$form.back_button.html}　{$form.next_button.html}{/if}</td>
    </tr>
</table>

        </td>
    </tr>
    <tr>
        <td>

<table class="Data_Table" border="1" width="402">
    <tr>
        <td class="Type" width="60" align="center"><b>状態</b></td>
        <td class="Value">{$form.form_state.html}</td>
    </tr>
</table>

        </td>
        <td>

<table class="Data_Table" border="1" width="502">
    <tr>
        <td class="Title_Purple" width="60"><b>グループ</b></td>
        <td class="Value">{$form.form_client_gr.html}{$form.form_parents_div.html}</td>
{*
        <td class="Type" width="60" align="center"><b>種別</b></td>
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
        <td class="Title_Purple" width="60"><b>地区<font color="#ff0000">※</font></b></td>
        <td class="Value">{$form.form_area_id.html}</td>
    </tr>
</table>

        </td>
        <td>

<table class="Data_Table" border="1" width="402">
    <tr>
        <td class="Title_Purple" width="60"><b>業種<font color="#ff0000">※</font></b></td>
        <td class="Value">{$form.form_btype.html}</td>
    </tr>
</table>

        </td>
    </tr>
    <tr>
        <td>

<table class="Data_Table" border="1" width="402">
    <tr>
        <td class="Title_Purple" width="60"><b>施設</b></td>
        <td class="Value">{$form.form_inst.html}</td>
    </tr>
</table>

        </td>
        <td>

<table class="Data_Table" border="1" width="402">
    <tr>
        <td class="Title_Purple" width="60"><b>業態</b></td>
        <td class="Value">{$form.form_bstruct.html}</td>
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

<div style="text-align: right; font: bold; color: #3300ff;">
    ・得意先名を伝票印字しない時は、得意先名の右隅のチェックを付けて下さい。
</div>
<div style="text-align: right; font: bold; color: #3300ff;">
    ・請求書の宛先とラベルのフォントサイズを大きく印字する場合、得意先名1と2は１４文字以内、住所1〜3は１８文字以内で登録して下さい。
</div>

<table class="Data_Table" border="1" width="100%" style="border: 2px solid #3300ff;">
<col width="160" style="font-weight: bold;">
<col width="250">
<col width="160" style="font-weight: bold;">
<col width="400">
<col>
    <tr>
        <td class="Title_Purple">得意先コード<font color="#ff0000">※</font></td>
        <td class="Value" colspan="3">
        {$form.form_client.html}{if $var.complete_flg != true}　（{$form.form_cd_search.html}）{/if}
        </td>
    </tr>
    <tr>
        <td class="Title_Purple">得意先名1<font color="#ff0000">※</font>　{$form.form_client_slip1.html}</td>
        <td class="Value">{$form.form_client_name.html}</td>
        <td class="Title_Purple">得意先名1<br>(フリガナ)</td>
        <td class="Value">{$form.form_client_read.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">得意先名2　　{$form.form_client_slip2.html}</td>
        <td class="Value">{$form.form_client_name2.html}
        <td class="Title_Purple">得意先名2<br>(フリガナ)</td>
        <td class="Value">{$form.form_client_read2.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">略称<font color="#ff0000">※</font></td>
        <td class="Value">{$form.form_client_cname.html}</td>
        <td class="Title_Purple">略称<br>(フリガナ)</td>
        <td class="Value">{$form.form_cname_read.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">敬称</td>
        <td class="Value" colspan="3">{$form.form_prefix.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">代表者氏名</td>
        <td class="Value">{$form.form_rep_name.html}</td>
        <td class="Title_Purple">代表者役職</td>
        <td class="Value">{$form.form_rep_position.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">郵便番号<font color="#ff0000">※</font></td>
        <td class="Value" colspan="3">{$form.form_post.html}　　{$form.button.input_button.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">住所1<font color="#ff0000">※</font></td>
        <td class="Value">{$form.form_address1.html}</td>
        <td class="Title_Purple">住所2</td>
        <td class="Value">{$form.form_address2.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">住所3<br>(ビル名・他)</td>
        <td class="Value" colspan=>{$form.form_address3.html}</td>
        <td class="Title_Purple">住所2<br>(フリガナ)</td>
        <td class="Value">{$form.form_address_read.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">TEL<font color="#ff0000">※</font></td>
        <td class="Value">{$form.form_tel.html}</td>
        <td class="Title_Purple">FAX</td>
        <td class="Value">{$form.form_fax.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">創業日</td>
        <td class="Value">{$form.form_establish_day.html}</td>
        <td class="Title_Purple">担当者Email</td>
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
        <td class="Title_Parent">親会社名</td>
        <td class="Value">{$form.form_company_name.html}</td>
        <td class="Title_Parent">親会社TEL</td>
        <td class="Value">{$form.form_company_tel.html}</td>
    </tr>
    <tr>       
        <td class="Title_Parent">親会社住所</td>
        <td class="Value" colspan="3">{$form.form_company_address.html}</td>
    </tr>  
    <tr>
        <td class="Title_Parent">資本金</td>
        <td class="Value">{$form.form_capital.html}万円</td>
        <td class="Title_Parent">親会社創業日</td>
        <td class="Value">{$form.form_parent_establish_day.html}</td>
    </tr>
    <tr>
        <td class="Title_Parent">親会社代表者氏名</td>
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
        <td class="Title_Purple" rowspan="4">担当情報</td>
        <td class="Title_Purple">1</td>
        <td class="Title_Purple">部署</td>
        <td class="Value">{$form.form_charger_part1.html}</td>
        <td class="Title_Purple">役職</td>
        <td class="Value">{$form.form_charger_represe1.html}</td>
        <td class="Title_Purple">氏名</td>
        <td class="Value">{$form.form_charger1.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">2</td>
        <td class="Title_Purple">部署</td>
        <td class="Value">{$form.form_charger_part2.html}</td>
        <td class="Title_Purple">役職</td>
        <td class="Value">{$form.form_charger_represe2.html}</td>
        <td class="Title_Purple">氏名</td>
        <td class="Value">{$form.form_charger2.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">3</td>
        <td class="Title_Purple">部署</td>
        <td class="Value">{$form.form_charger_part3.html}</td>
        <td class="Title_Purple">役職</td>
        <td class="Value">{$form.form_charger_represe3.html}</td>
        <td class="Title_Purple">氏名</td>
        <td class="Value">{$form.form_charger3.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple" colspan="2">備考</td>
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
        <td class="Title_Purple" colspan="2">営業時間</td>
        <td class="Value">{$form.form_trade_stime1.html} 〜 {$form.form_trade_etime1.html} <br>{$form.form_trade_stime2.html} 〜 {$form.form_trade_etime2.html}</td>
        <td class="Title_Purple">休日</td>
        <td class="Value">{$form.form_holiday.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple" colspan="2">{$form.intro_claim_link.html}</td>
        <td class="Value" colspan="4">{$form.form_claim.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple" colspan="2">{$form.intro_claim_link2.html}</td>
        <td class="Value" colspan="3">{$form.form_claim2.html}　{$form.form_warning.html}<br>
    </tr>
    <tr>
        <td class="Title_Purple" colspan="2">請求月<font color="#000000">◆</font></td>
        <td class="Value" colspan="3">
        <font color="#0000ff"><b>・請求書を作成する月にチェックして下さい。</b></font>
        <br>
        {foreach from=$form.claim1_monthly_check item=item key=i}
            {$form.claim1_monthly_check[$i].html}
        {/foreach}
        </td>
    </tr>
    <tr>
        <td class="Title_Purple" colspan="2">与信限度</td>
        <td class="Value">{$form.form_cledit_limit.html}万円</td>
        <td class="Title_Purple">回収条件</td>
        <td class="Value">{$form.form_col_terms.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple" colspan="2">取引区分<font color="#ff0000">※</font></td>
        <td class="Value" colspan="3">{$form.trade_aord_1.html}<b><font color="#ff0000">★</font><font color="#0000ff">現金売上の場合も入金漏れの防止のため毎月請求書作成を行なって下さい。</font></b></td>
    </tr>
    <tr>
        <td class="Title_Purple" colspan="2">締日<font color="#ff0000">※</font><font color="#000000">◆</font></td>
        <td class="Value">{$form.form_close.html}</td>
        <td class="Title_Purple">集金日<font color="#ff0000">※</font><font color="#000000">◆</font></td>
        <td class="Value">{$form.form_pay_m.html}の{$form.form_pay_d.html}<b><font color="#ff0000">★</font><font color="#0000ff">現金売上については締日と集金日は同一です。</font></b></td>
    </tr>
    <tr>
        <td class="Title_Purple" colspan="2">集金方法</td>
        <td class="Value" colspan="3">{$form.form_pay_way.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple" colspan="2">振込銀行口座</td>
        <td class="Value"colspan="3">{$form.form_bank.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple" colspan="2">振込名義1</td>
        <td class="Value">{$form.form_pay_name.html}</td>
        <td class="Title_Purple">振込名義2</td>
        <td class="Value">{$form.form_account_name.html}</td>
   </tr>
    <tr>
        <td class="Title_Purple" colspan="2">銀行手数料負担区分</td>
        <td class="Value" colspan="3">{$form.form_bank_div.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple" colspan="2">契約年月日</td>
        <td class="Value">{$form.form_cont_s_day.html}</td>
        <td class="Title_Purple">契約更新日</td>
        <td class="Value">{$form.form_cont_r_day.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple" colspan="2">契約期間</td>
        <td class="Value">{$form.form_cont_peri.html}年</td>
        <td class="Title_Purple">契約終了日</td>
        <td class="Value">{$form.form_cont_e_day.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple" rowspan="3">売上伝票</td>
        <td class="Title_Purple">発行<font color="#ff0000">※</font></td>
        <td class="Value" colspan="3">{$form.form_slip_out.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">発行元<font color="#ff0000">※</font></td>
        <td class="Value" colspan="3">{$form.form_s_pattern_select.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">コメント</td>
        <td class="Value" colspan="3">{$form.form_deliver_radio.html}<br>
        <font color="#0000FF"><b>・個別コメントを選択した場合、登録したコメントが請求書以外で有効になります。</b></font><br>
        {$form.form_deliver_note.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple" rowspan="5">請求書</td>
        <td class="Title_Purple">宛先</td>
        <td class="Value" colspan="3">{$form.form_bill_address_font.html}　請求書の宛先とラベルのフォントサイズを大きくする</td>
    </tr>
<!--
    <tr>
        <td class="Title_Purple">請求範囲<font color="#ff0000">※</font></td>
        <td class="Value" colspan="3">{$form.form_claim_scope.html}</td>
    </tr>
-->
    <tr>
        <td class="Title_Purple">発行<font color="#ff0000">※</font><font color="#000000">◆</font></td>
        <td class="Value" colspan="3">{$form.form_claim_out.html}<br>
        <font color="#0000FF"><b>・請求の親子関係がない場合、個別明細請求書は明細請求書と同様です。</b></font></td>
    </tr>
    <tr>
        <td class="Title_Purple">送付<font color="#ff0000">※</font></td>
        <td class="Value" colspan="3">{$form.form_claim_send.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">発行元<font color="#ff0000">※</font></td>
        <td class="Value" colspan="3">{$form.form_c_pattern_select.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">備考</td>
        <td class="Value" colspan="4">{$form.form_claim_note.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">金額<font color="#ff0000">※</font></td>
        <td class="Title_Purple">まるめ区分<font color="#000000">◆</font></td>
        <td class="Value" colspan="3">{$form.form_coax.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple" rowspan="3">消費税<font color="#ff0000">※</font></td>
        <td class="Title_Purple">課税単位<font color="#000000">◆</font></td>
        <td class="Value" colspan="3">{$form.form_tax_div.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">端数区分<font color="#000000">◆</font></td>
        <td class="Value" colspan="3">{$form.form_tax_franct.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">課税区分<font color="#000000">◆</font></td>
        <td class="Value" colspan="3">{$form.form_c_tax_div.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple" colspan="2">設備情報等・その他</td>
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
        <td class="Value">{if $smarty.session.group_kind == '2'}{$form.form_client_div.html}　{/if}{$form.form_intro_act.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">お振込先口座名</td>
        <td class="Value">{$form.form_trans_account.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">銀行/支店名</td>
        <td class="Value">{$form.form_bank_fc.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">口座番号</td>
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
        <td class="Title_Purple">担当支店<font color="#ff0000">※</font></td>
        <td class="Value" colspan="3">{$form.form_charge_branch_id.html}</td>
    </tr>
    <tr>
{*        <td class="Title_Purple">契約担当1</td>*}
        <td class="Title_Purple">現契約担当</td>
        <td class="Value">{$form.form_c_staff_id1.html}</td>
{*        <td class="Title_Purple">契約担当2</td>*}
        <td class="Title_Purple">初期契約社員</td>
        <td class="Value">{$form.form_c_staff_id2.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">巡回開始日</td>
        <td class="Value" colspan="3">{$form.form_round_start.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">取引履歴</td>
        <td class="Value" colspan="3">{$form.form_record.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">重要事項</td>
        <td class="Value" colspan="3">{$form.form_important.html}</td>
    </tr>
</table>

<table width="100%">
    <tr>
        <td><font color="#ff0000"><b>※は必須入力です。</b></font>　　<font color="#000000">◆</font><font color="#555555"><b>の項目に関して：請求時は請求先の設定に合わせます。</b></td>
        <td align="right">{$form.button.entry_button.html}　{*　{$form.button.res_button.html}*}{$form.button.ok_button.html}　　{$form.button.contract_button.html}　　{$form.button.back_button.html}
        </td>
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


{$var.html_header}

<script language="javascript">
{$var.java_sheet}
 </script>

<body bgcolor="#D8D0C8" {$var.form_load}>
<form name="dateForm" method="post">
{$form.hidden}
<!--------------------- 外枠開始 ------------------------>
<table border="0" width="100%" height="90%" class="M_Table">

	<tr align="center" height="60">
		<td width="100%" colspan="2" valign="top">
			<!-- 画面タイトル開始 --> {$var.page_header} <!-- 画面タイトル終了 -->
		</td>
	</tr>

	<tr align="center">
		<td valign="top">
		
			<table>
{*
				<tr>
					<td>

<!--------------------- 画面表示1開始 --------------------->


<!--******************** 画面表示1終了 *******************-->

					</td>
				</tr>
*}

				<tr>
					<td>

<!---------------------- 画面表示2開始 ---------------------->
{* エラー表示 *}
<span style="color: #ff0000; font-weight: bold; line-height: 130%;">
<!-- 確定チェック -->
{if $var.err_mess_confirm_flg != null}
    <li>{$var.err_mess_confirm_flg}<br>
{/if}
<!-- 削除チェック -->
{if $var.del_mess != null}
    <li>{$var.del_mess}<br>
{/if}

{if $var.concurrent_err_flg != true}
    {* 予定巡回日 *}
    {if $form.form_delivery_day.error != null}
        <li>{$form.form_delivery_day.error}<br>
    {/if}

    <!-- 請求日 -->
    {if $form.form_request_day.error != null}
        <li>{$form.form_request_day.error}<br>
    {/if}
    {if $var.error_msg16 != null}
        <li>{$var.error_msg16}<br>
    {/if}

    <!-- 備考 -->
    {if $form.form_note.error != null}
        <li>{$form.form_note.error}<br>
    {/if}

    {* 口座単価（得意先） *}
    {if $form.intro_ac_price.error != null}
        <li>{$form.intro_ac_price.error}<br>
    {/if}

    {* 口座率（得意先） *}
    {if $form.intro_ac_rate.error != null}
        <li>{$form.intro_ac_rate.error}<br>
    {/if}

    {* 代行料（固定額） *}
    {if $form.act_request_price.error != null}
        <li>{$form.act_request_price.error}<br>
    {/if}

    <!-- 取引区分 -->
    {if $form.trade_aord.error != null}
        <li>{$form.trade_aord.error}<br>
    {/if}
    <!-- 訂正理由 -->
    {if $form.form_reason.error != null}
        <li>{$form.form_reason.error}<br>
    {/if}

    <!-- 担当メイン -->
    {if $form.form_c_staff_id1.error != null}
        <li>{$form.form_c_staff_id1.error}<br>
    {/if}
    <!-- 売上率は担当メインがある場合にエラー判定 -->
    {if $form.form_sale_rate1.error != null && $form.form_c_staff_id1.error == null}
        <li>{$form.form_sale_rate1.error}<br>
    {/if}

    <!-- 担当サブ１ -->
    {if $form.form_c_staff_id2.error != null}
        <li>{$form.form_c_staff_id2.error}<br>
    {/if}
    {if $form.form_sale_rate2.error != null}
        <li>{$form.form_sale_rate2.error}<br>
    {/if}

    <!-- 担当サブ２ -->
    {if $form.form_c_staff_id3.error != null}
        <li>{$form.form_c_staff_id3.error}<br>
    {/if}
    {if $form.form_sale_rate3.error != null}
        <li>{$form.form_sale_rate3.error}<br>
    {/if}

    <!-- 担当サブ３ -->
    {if $form.form_c_staff_id4.error != null}
        <li>{$form.form_c_staff_id4.error}<br>
    {/if}
    {if $form.form_sale_rate4.error != null}
        <li>{$form.form_sale_rate4.error}<br>
    {/if}

    <!-- 巡回担当者重複判定 -->
    {if $var.error_msg2 != null}
        <li>{$var.error_msg2}<br>
    {/if}

    <!-- サービス・アイテム入力判定 -->
    {if $var.error_msg3 != null}
        <li>{$var.error_msg3}<br>
    {/if}

    <!-- 売上率 -->
    {if $var.error_msg != null}
        <li>{$var.error_msg}<br>
    {/if}
    {foreach key=i from=$error_loop_num3 item=items}
        {if $error_msg10[$i] != null}
            <li>{$error_msg10[$i]}<br>
        {/if}
    {/foreach}

    <!-- サービス印字がある場合、サービスIDがあるか判定 -->
    {if $error_msg4[1] != null}
        <li>{$error_msg4[1]}<br>
    {/if}
    {if $error_msg4[2] != null}
        <li>{$error_msg4[2]}<br>
    {/if}
    {if $error_msg4[3] != null}
        <li>{$error_msg4[3]}<br>
    {/if}
    {if $error_msg4[4] != null}
        <li>{$error_msg4[4]}<br>
    {/if}
    {if $error_msg4[5] != null}
        <li>{$error_msg4[5]}<br>
    {/if}

    <!-- アイテム印字がある場合、アイテムIDがあるか判定 -->
    {if $error_msg5[1] != null}
        <li>{$error_msg5[1]}<br>
    {/if}
    {if $error_msg5[2] != null}
        <li>{$error_msg5[2]}<br>
    {/if}
    {if $error_msg5[3] != null}
        <li>{$error_msg5[3]}<br>
    {/if}
    {if $error_msg5[4] != null}
        <li>{$error_msg5[4]}<br>
    {/if}
    {if $error_msg5[5] != null}
        <li>{$error_msg5[5]}<br>
    {/if}

    <!-- 順路 -->
    {if $form.form_route_load.error != null}
        <li>{$form.form_route_load.error}<br>
    {/if}

    <!-- 販売区分 -->
    {if $form.form_divide[1].error != null}
        <li>{$form.form_divide[1].error}<br>
    {/if}
    {if $form.form_divide[2].error != null}
        <li>{$form.form_divide[2].error}<br>
    {/if}
    {if $form.form_divide[3].error != null}
        <li>{$form.form_divide[3].error}<br>
    {/if}
    {if $form.form_divide[4].error != null}
        <li>{$form.form_divide[4].error}<br>
    {/if}
    {if $form.form_divide[5].error != null}
        <li>{$form.form_divide[5].error}<br>
    {/if}

    <!-- サービス印字・一式フラグ -->
    {if $form.form_print_flg1[1].error != null}
        <li>{$form.form_print_flg1[1].error}<br>
    {/if}
    {if $form.form_print_flg1[2].error != null}
        <li>{$form.form_print_flg1[2].error}<br>
    {/if}
    {if $form.form_print_flg1[3].error != null}
        <li>{$form.form_print_flg1[3].error}<br>
    {/if}
    {if $form.form_print_flg1[4].error != null}
        <li>{$form.form_print_flg1[4].error}<br>
    {/if}
    {if $form.form_print_flg1[5].error != null}
        <li>{$form.form_print_flg1[5].error}<br>
    {/if}

    <!-- サービス印字・アイテム印字 -->
    {if $form.form_print_flg2[1].error != null}
        <li>{$form.form_print_flg2[1].error}<br>
    {/if}
    {if $form.form_print_flg2[2].error != null}
        <li>{$form.form_print_flg2[2].error}<br>
    {/if}
    {if $form.form_print_flg2[3].error != null}
        <li>{$form.form_print_flg2[3].error}<br>
    {/if}
    {if $form.form_print_flg2[4].error != null}
        <li>{$form.form_print_flg2[4].error}<br>
    {/if}
    {if $form.form_print_flg2[5].error != null}
        <li>{$form.form_print_flg2[5].error}<br>
    {/if}

    <!-- 本体印字・本体ID -->
    {if $form.form_print_flg3[1].error != null}
        <li>{$form.form_print_flg3[1].error}<br>
    {/if}
    {if $form.form_print_flg3[2].error != null}
        <li>{$form.form_print_flg3[2].error}<br>
    {/if}
    {if $form.form_print_flg3[3].error != null}
        <li>{$form.form_print_flg3[3].error}<br>
    {/if}
    {if $form.form_print_flg3[4].error != null}
        <li>{$form.form_print_flg3[4].error}<br>
    {/if}
    {if $form.form_print_flg3[5].error != null}
        <li>{$form.form_print_flg3[5].error}<br>
    {/if}

    <!-- アイテムコード-->
    {foreach key=i from=$error_loop_num1 item=items}
        {if $form.form_goods_cd1[$i].error != null}
            <li>{$form.form_goods_cd1[$i].error}<br>
        {/if}
        {if $form.form_goods_cd3[$i].error != null}
            <li>{$form.form_goods_cd3[$i].error}<br>
        {/if}
        {if $form.form_goods_cd2[$i].error != null}
            <li>{$form.form_goods_cd2[$i].error}<br>
        {/if}
    {/foreach}

    <!-- 数量・一式-->
    {if $form.form_goods_num1[1].error != null}
        <li>{$form.form_goods_num1[1].error}<br>
    {/if}
    {if $form.form_goods_num1[2].error != null}
        <li>{$form.form_goods_num1[2].error}<br>
    {/if}
    {if $form.form_goods_num1[3].error != null}
        <li>{$form.form_goods_num1[3].error}<br>
    {/if}
    {if $form.form_goods_num1[4].error != null}
        <li>{$form.form_goods_num1[4].error}<br>
    {/if}
    {if $form.form_goods_num1[5].error != null}
        <li>{$form.form_goods_num1[5].error}<br>
    {/if}

    <!-- 本体数量 -->
    {if $form.form_goods_num2[1].error != null}
        <li>{$form.form_goods_num2[1].error}<br>
    {/if}
    {if $form.form_goods_num2[2].error != null}
        <li>{$form.form_goods_num2[2].error}<br>
    {/if}
    {if $form.form_goods_num2[3].error != null}
        <li>{$form.form_goods_num2[3].error}<br>
    {/if}
    {if $form.form_goods_num2[4].error != null}
        <li>{$form.form_goods_num2[4].error}<br>
    {/if}
    {if $form.form_goods_num2[5].error != null}
        <li>{$form.form_goods_num2[5].error}<br>
    {/if}

    <!-- 消耗品数量 -->
    {if $form.form_goods_num3[1].error != null}
        <li>{$form.form_goods_num3[1].error}<br>
    {/if}
    {if $form.form_goods_num3[2].error != null}
        <li>{$form.form_goods_num3[2].error}<br>
    {/if}
    {if $form.form_goods_num3[3].error != null}
        <li>{$form.form_goods_num3[3].error}<br>
    {/if}
    {if $form.form_goods_num3[4].error != null}
        <li>{$form.form_goods_num3[4].error}<br>
    {/if}
    {if $form.form_goods_num3[5].error != null}
        <li>{$form.form_goods_num3[5].error}<br>
    {/if}

    <!-- 営業原価 -->
    {if $form.form_trade_price[1].error != null}
        <li>{$form.form_trade_price[1].error}<br>
    {/if}
    {if $form.form_trade_price[2].error != null}
        <li>{$form.form_trade_price[2].error}<br>
    {/if}
    {if $form.form_trade_price[3].error != null}
        <li>{$form.form_trade_price[3].error}<br>
    {/if}
    {if $form.form_trade_price[4].error != null}
        <li>{$form.form_trade_price[4].error}<br>
    {/if}
    {if $form.form_trade_price[5].error != null}
        <li>{$form.form_trade_price[5].error}<br>
    {/if}

    <!-- 売上単価 -->
    {if $form.form_sale_price[1].error != null}
        <li>{$form.form_sale_price[1].error}<br>
    {/if}
    {if $form.form_sale_price[2].error != null}
        <li>{$form.form_sale_price[2].error}<br>
    {/if}
    {if $form.form_sale_price[3].error != null}
        <li>{$form.form_sale_price[3].error}<br>
    {/if}
    {if $form.form_sale_price[4].error != null}
        <li>{$form.form_sale_price[4].error}<br>
    {/if}
    {if $form.form_sale_price[5].error != null}
        <li>{$form.form_sale_price[5].error}<br>
    {/if}

    {foreach key=i from=$error_loop_num1 item=items}
        {foreach key=j from=$error_loop_num2 item=items}
            <!-- 内訳の営業原価 -->
            {if $error_msg6[$i][$j] != null}
                <li>{$error_msg6[$i][$j]}<br>
            {/if}
            <!-- 内訳の売上単価 -->
            {if $error_msg7[$i][$j] != null}
                <li>{$error_msg7[$i][$j]}<br>
            {/if}
            <!-- 内訳の数量-->
            {if $error_msg8[$i][$j] != null}
                <li>{$error_msg8[$i][$j]}<br>
            {/if}
            <!-- 内訳のアイテム-->
            {if $error_msg11[$i][$j] != null}
                <li>{$error_msg11[$i][$j]}<br>
            {/if}
            <!-- 内訳の不正判定-->
            {if $error_msg13[$i][$j] != null}
                <li>{$error_msg13[$i][$j]}<br>
            {/if}
        {/foreach}
    {/foreach}

    <!-- サービス・商品 -->
    {if $form.form_serv[1].error != null}
        <li>{$form.form_serv[1].error}<br>
    {/if}
    {if $form.form_serv[2].error != null}
        <li>{$form.form_serv[2].error}<br>
    {/if}
    {if $form.form_serv[3].error != null}
        <li>{$form.form_serv[3].error}<br>
    {/if}
    {if $form.form_serv[4].error != null}
        <li>{$form.form_serv[4].error}<br>
    {/if}
    {if $form.form_serv[5].error != null}
        <li>{$form.form_serv[5].error}<br>
    {/if}

    <!-- 本体・数量 -->
    {if $form.error_goods_num2[1].error != null}
        <li>{$form.error_goods_num2[1].error}<br>
    {/if}
    {if $form.error_goods_num2[2].error != null}
        <li>{$form.error_goods_num2[2].error}<br>
    {/if}
    {if $form.error_goods_num2[3].error != null}
        <li>{$form.error_goods_num2[3].error}<br>
    {/if}
    {if $form.error_goods_num2[4].error != null}
        <li>{$form.error_goods_num2[4].error}<br>
    {/if}
    {if $form.error_goods_num2[5].error != null}
        <li>{$form.error_goods_num2[5].error}<br>
    {/if}

    <!-- 消耗品・数量 -->
    {if $form.error_goods_num3[1].error != null}
        <li>{$form.error_goods_num3[1].error}<br>
    {/if}
    {if $form.error_goods_num3[2].error != null}
        <li>{$form.error_goods_num3[2].error}<br>
    {/if}
    {if $form.error_goods_num3[3].error != null}
        <li>{$form.error_goods_num3[3].error}<br>
    {/if}
    {if $form.error_goods_num3[4].error != null}
        <li>{$form.error_goods_num3[4].error}<br>
    {/if}
    {if $form.error_goods_num3[5].error != null}
        <li>{$form.error_goods_num3[5].error}<br>
    {/if}

    {* 商品名必須 *}
    {foreach key=i from=$error_loop_num1 item=items}
        {* アイテム正式 *}
        {if $form.official_goods_name[$i].error != null}
            <li>{$form.official_goods_name[$i].error}<br>
        {/if}
        {* アイテム略称 *}
        {if $form.form_goods_name1[$i].error != null}
            <li>{$form.form_goods_name1[$i].error}<br>
        {/if}
        {* 消耗品 *}
        {if $form.form_goods_name3[$i].error != null}
            <li>{$form.form_goods_name3[$i].error}<br>
        {/if}
        {* 本体商品 *}
        {if $form.form_goods_name2[$i].error != null}
            <li>{$form.form_goods_name2[$i].error}<br>
        {/if}
    {/foreach}

    <!-- 口座料(固定金額) -->
    {if $form.form_account_price[1].error != null}
        <li>{$form.form_account_price[1].error}<br>
    {/if}
    {if $form.form_account_price[2].error != null}
        <li>{$form.form_account_price[2].error}<br>
    {/if}
    {if $form.form_account_price[3].error != null}
        <li>{$form.form_account_price[3].error}<br>
    {/if}
    {if $form.form_account_price[4].error != null}
        <li>{$form.form_account_price[4].error}<br>
    {/if}
    {if $form.form_account_price[5].error != null}
        <li>{$form.form_account_price[5].error}<br>
    {/if}

    <!-- 口座料(率) -->
    {if $form.form_account_rate[1].error != null}
        <li>{$form.form_account_rate[1].error}<br>
    {/if}
    {if $form.form_account_rate[2].error != null}
        <li>{$form.form_account_rate[2].error}<br>
    {/if}
    {if $form.form_account_rate[3].error != null}
        <li>{$form.form_account_rate[3].error}<br>
    {/if}
    {if $form.form_account_rate[4].error != null}
        <li>{$form.form_account_rate[4].error}<br>
    {/if}
    {if $form.form_account_rate[5].error != null}
        <li>{$form.form_account_rate[5].error}<br>
    {/if}

    {* 前受金 *}
    {foreach key=i from=$error_loop_num1 item=items}
        {if $form.form_ad_offset_amount[$i].error != null}
            <li>{$form.form_ad_offset_amount[$i].error}<br>
        {/if}
    {/foreach}

{/if}
</span>

{* エラーがなくて、伝票合計（税込）より前受相殺額合計が大きい場合、警告 *}
{* rev.1.3 予定巡回日2ヶ月以上離れている警告追加 *}
{* {if $var.error_flg != true && $var.ad_total_warn_mess != null} *}
{if $var.error_flg != true && ($var.ad_total_warn_mess != null || $var.warn_lump_change != null)}
<br>
<table border="0" width="650px" cellpadding="0" cellspacing="0" style="font-weight: bold; width: 650px;">
    <tr width="650px" style="width: 650px;"><td width="650px" style="width: 650px;">
    <font color="#ff00ff">[警告]<br>
	{if $var.ad_total_warn_mess != null}
	{$var.ad_total_warn_mess}<br>
	{/if}
	{if $var.warn_lump_change != null}
	{$var.warn_lump_change}<br>
	{/if}
    {$form.form_ad_warn.html}<br><br>
	</font>
    </td></tr>
</table>
{/if}

<fieldset>
<legend><span style="font: bold 15px; color: #555555;">【伝票番号】： {$var.ord_no} </span>{$form.form_con_link.html}</legend>
<BR>
<table border="0">
    <tr>
        <td>
            <table class="List_Table" border="1" width="400">
                <tr class="Result1">
                    <td class="Title_Pink" width="78" align="center"><b>代行区分</b></td>
                    <td class="Value">{if $var.contract_div == "1"}自社巡回{elseif $var.contract_div == "2"}オンライン代行{else}オフライン代行{/if}</td>
                </tr>
                <tr class="Result1">
                    <td class="Title_Pink" width="78" align="center"><b>巡回日</b></td>
                    <td class="Value">{$var.round_form}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

<BR>
<table class="List_Table" border="1">
	<tr align="center">
		<td class="Title_Pink" width=""><b>予定巡回日<font color="red">※</font></b></td>
		<!-- 契約区分orＦＣ判定 -->
		{if $var.contract_div == 1 || $smarty.session.group_kind == '3'}
			<!-- 通常orFC -->
			<td class="Title_Pink" width=""><b>順路<font color="red">※</font></b></td>
		{else}
			<!-- 直営側の代行は順路なし -->
		{/if}
		<td class="Title_Pink" width=""><b>得意先</b></td>
		<td class="Title_Pink" width=""><b>取引区分<font color="red">※</font></b></td>
		<!-- 契約区分or直営判定 -->
		{if $var.contract_div == 1 || $smarty.session.group_kind == '2'}
			<!-- 通常or直営 -->
			<td class="Title_Pink" width=""><b>請求日<font color="red">※</font></b></td>
			<td class="Title_Pink" width=""><b>請求先</b></td>
		{else}
			<!-- FC側の代行は請求日・請求先なし -->
		{/if}
		<!-- 契約区分orＦＣ判定 -->
		{if $var.contract_div == 1 || $smarty.session.group_kind == '3'}
			<!-- 通常orFC -->
			<td class="Title_Pink" width=""><b>巡回担当チーム</b></td>
		{else}
			<!-- 直営側の代行は非表示 -->
		{/if}
	</tr>

	<!--1行目-->
	<tr class="Result1">
		<td align="center" width="150">{$form.form_delivery_day.html}</td>
		<!-- 契約区分orＦＣ判定 -->
		{if $var.contract_div == 1 || $smarty.session.group_kind == '3'}
			<!-- 通常orFC -->
			<td align="left">{$form.form_route_load.html}</td>
		{else}
			<!-- 直営側の代行は順路なし -->
		{/if}
		<td align="left" width="180">{$var.client_cd}<br>{$var.client_name}</td>
		<td align="center">{$form.trade_aord.html}</td>
		<!-- 契約区分or直営判定 -->
		{if $var.contract_div == 1 || $smarty.session.group_kind == '2'}
			<!-- 通常or直営 -->
			<td align="center" width="150">{$form.form_request_day.html}</td>
			<td align="left" width="180">{$form.form_claim.html}</td>
		{else}
			<!-- FC側の代行は請求日・請求先なし -->
		{/if}
		
		<!-- 契約区分orＦＣ判定 -->
		{if $var.contract_div == 1 || $smarty.session.group_kind == '3'}
			<!-- 通常orFC -->
			<td align="left">
				<table >
					<tr>
						<td><font color="#555555">
							メイン1<b><font color="#ff0000">※</font></b> {$form.form_c_staff_id1.html}　売上{$form.form_sale_rate1.html}％<br>
							サブ2　 <b>　</b>{$form.form_c_staff_id2.html}　売上{$form.form_sale_rate2.html}％<br>
							サブ3　 <b>　</b>{$form.form_c_staff_id3.html}　売上{$form.form_sale_rate3.html}％<br>
							サブ4　 <b>　</b>{$form.form_c_staff_id4.html}　売上{$form.form_sale_rate4.html}％<br>
							</font>
						</td>
					</tr>
				</table>
			</td>
		{/if}
	</tr>

	{* 自社巡回、または直営側の代行の場合、紹介口座を表示 *}
	{if $var.contract_div == 1 || $smarty.session.group_kind == '2'}
		<tr class="Result1">
			<td class="Title_Purple" width="110"><b>紹介口座先</b></td>
	    {if $var.contract_div == "1"}
			<td class="Value" width="185" colspan="3"><nobr>{$var.ac_name}</nobr></td>
            <td class="Title_Purple" width="110"><b>{$form.intro_ac_div[1].label}</b></td>
            <td class="Value" width="185" colspan="3">
        {else}
			<td class="Value" width="185" colspan="2"><nobr>{$var.ac_name}</nobr></td>
            <td class="Title_Purple" width="110"><b>{$form.intro_ac_div[1].label}</b></td>
            <td class="Value" width="185" colspan="">
        {/if}
                <table cellpadding="0" cellspacing="0" style="color: #555555;">
                    {if $var.ac_name == "無し"}
                    <tr><td>{$form.intro_ac_div[3].html}</td></tr>
                    {else}
                    <tr><td>{$form.intro_ac_div[0].html}{$form.intro_ac_price.html}円</td><td>　{$form.intro_ac_div[3].html}</td></tr>
                    <tr><td>{$form.intro_ac_div[1].html}{$form.intro_ac_rate.html}％</td><td></td></tr>
                    <tr><td>{$form.intro_ac_div[2].html}</td><td></td></tr>
                    {/if}
                </table>
            </td>
		</tr>
	{/if}

	<!-- 契約区分orＦＣ判定 -->
	{if $var.contract_div == 1}
		{* 通常 *}
		<tr class="Result1">
			<td class="Title_Pink"><b>備考</b></td>
			<td class="Value" colspan="3">{$form.form_note.html}</td>
			<td class="Title_Pink" ><b>訂正理由<font color="red">※</font></b></td>
			<td class="Value" colspan="2">{$form.form_reason.html}</td>
		</tr>
		<tr class="Result1">
			<td class="Title_Pink"><b>税抜合計<br>消費税</b></td>
			<td class="Value" colspan="3" align="right">{$form.form_sale_total.html}<br>{$form.form_sale_tax.html}</td>
			<td class="Title_Pink" ><b>伝票合計</b></td>
			<td class="Value" colspan="2" align="right">{$form.form_sale_money.html}</td>
		</tr>
	{else}
		{* 代行 *}
        {if $smarty.session.group_kind == "2"}
		<tr class="Result1">
			<td class="Title_Purple"><b>代行先</b></td>
			<td class="Value" colspan="2"><nobr>{$var.act_name}</nobr></td>
            <td class="Title_Purple"><b>代行委託料</b></td>
            {if $var.act_div == "1"}
            <td class="Value" colspan="3" align="left">{$var.act_amount}</td>
            {elseif $var.act_div == "2"}
            <td class="Value" colspan="3" align="left">固定額{$form.act_request_price.html}円</td>

            {elseif $var.act_div == "3"}
            <td class="Value" colspan="3" align="right">{$var.act_amount}</td>
            {/if}
		</tr>
        {/if}
		<tr class="Result1">
			<td class="Title_Pink"><b>備考</b></td>
			<td class="Value" colspan="2">{$form.form_note.html}</td>
			<td class="Title_Pink" ><b>訂正理由<font color="red">※</font></b></td>
			<td class="Value" colspan="1">{$form.form_reason.html}</td>
		</tr>
		<tr class="Result1">
			<td class="Title_Pink"><b>税抜合計<br>消費税</b></td>
			<td class="Value" align="right" colspan="2">{$form.form_sale_total.html}<br>{$form.form_sale_tax.html}</td>
			<td class="Title_Pink" ><b>伝票合計</b></td>
			<td class="Value" align="right" colspan="1">{$form.form_sale_money.html}</td>
		</tr>
	{/if}

	{* 自社巡回、または直営側の代行の場合、前受金を表示 *}
	{if $var.contract_div == 1 || $smarty.session.group_kind == '2'}
		<tr class="Result1">
			<td class="Title_Pink">
            <table width="100%" cellspacing="0" cellpadding="0"><tr>
                <td class="Title_Pink"><b>前受金残高</b></td>
                {if $var.warning == null && $var.comp_flg == null && $var.renew_flg != "true"}
                <td align="right">{$form.form_ad_sum_btn.html}</td>
                {/if}
            </tr></table>
            </td>
            {if $var.contract_div == "1"}
			<td class="Value" align="right" colspan="3">{$form.form_ad_rest_price.html}</td>
			<td class="Title_Pink" ><b>前受相殺額合計</b></td>
			<td class="Value" align="right" colspan="2">{$form.form_ad_offset_total.html}</td>
            {else}
			<td class="Value" align="right" colspan="2">{$form.form_ad_rest_price.html}</td>
			<td class="Title_Pink" ><b>前受相殺額合計</b></td>
			<td class="Value" align="right" colspan="1">{$form.form_ad_offset_total.html}</td>
            {/if}
		</tr>
	{/if}

</table>
<BR>
<A NAME="hand">
<table border="0" width="985">
	<tr>
	<td align="left"><font size="+0.5" color="#555555"><b>【商品出荷倉庫：{$var.ware_name}</b></font></td>
	<td align="left" width=922><b><font color="blue">
        <li>前受相殺額以外は税抜金額を登録してください。
        <li>「サービス名」「アイテム」にチェックを付けると伝票に印字されます
    </b></td>
	</tr>
</table>

<table class="Data_Table" border="1" width="950">
	<tr>
		<td class="Title_Purple" rowspan="2"><b>販売区分<font color="#ff0000">※</font></b></td>
		<td class="Title_Purple" rowspan="2"><b>サービス名</b></td>
		<td class="Title_Purple" rowspan="2"><b>アイテム</b></td>
		<td class="Title_Purple" rowspan="2"><b>数量</b></td>
		<td class="Title_Purple" colspan="2"><b>金額</b></td>
		<td class="Title_Purple" rowspan="2"><b>消耗品</b></td>
		<td class="Title_Purple" rowspan="2"><b>数量</b></td>
		<td class="Title_Purple" rowspan="2"><b>本体商品</b></td>
		<td class="Title_Purple" rowspan="2"><b>数量</b></td>
		{if $var.contract_div == 1 || $smarty.session.group_kind == '2'}
			<td class="Title_Purple" rowspan="2"><b>口座料<br>(商品単位)</b></td>
			<td class="Title_Purple" rowspan="2"><b>前受相殺額</b></td>
		{/if}
    	{* <td class="Title_Purple" rowspan="2"><b>内訳</b></td> *}
		{if $var.contract_div == 1}
	    	<td class="Title_Purple" rowspan="2"><b>クリア</b></td>
		{/if}
	</tr>

	<tr>
		<td class="Title_Purple"><b>営業原価<font color="#ff0000">※</font><br>売上単価<font color="#ff0000">※</font></b></td>
		<td class="Title_Purple" ><b>原価合計額<br>売上合計額</b></td>
	</tr>

	{foreach key=i from=$loop_num item=items}
        {* 2009-09-21 hashimoto-y *}
        {if $toSmarty_discount_flg[$i] === 't'}
            <tr class="Value" style="color: red">
        {else}
		    <tr>
        {/if}

			<td class="Value" align="center">{$form.form_divide[$i].html}</td>
			<td class="Value">{$form.form_print_flg1[$i].html}<br>{$form.form_serv[$i].html}</td>
			<!-- フリーズ判定 -->
			{if $var.contract_div == '1'}
				<!-- 通常の場合 -->
                <td class="Value">
                    {$form.form_goods_cd1[$i].html}({$form.form_search1[$i].html}){$form.form_print_flg2[$i].html}<br>
                    {$form.official_goods_name[$i].html}<br>
                    {$form.form_goods_name1[$i].html}
                </td>
				<td class="Value" align="right"><font color=#555555>一式</font>{$form.form_issiki[$i].html}<br>{$form.form_goods_num1[$i].html}</td>
			{else}
				<!-- 代行伝票の場合検索リンクの（）無し -->
                <td class="Value">
                    {$form.form_goods_cd1[$i].html}　{$form.form_print_flg2[$i].html}<br>
                    {$form.official_goods_name[$i].html}<br>
                    {$form.form_goods_name1[$i].html}
                </td>
                {* 2009-09-21 hashimoto-y *}
                {if $toSmarty_discount_flg[$i] === 't'}
				    <td class="Value" align="right">一式{$form.form_issiki[$i].html}<br>{$form.form_goods_num1[$i].html}</td>
                {else}
				    <td class="Value" align="right"><font color=#555555>一式</font>{$form.form_issiki[$i].html}<br>{$form.form_goods_num1[$i].html}</td>
                {/if}
			{/if}
			<td class="Value" align="right">{$form.form_trade_price[$i].html}<br>{$form.form_sale_price[$i].html}</td>
			<td class="Value" align="right">{$form.form_trade_amount[$i].html}<br>{$form.form_sale_amount[$i].html}</td>
			<!-- フリーズ判定 -->
			{if $var.contract_div == '1'}
				<!-- 通常の場合 -->
				<td class="Value">{$form.form_goods_cd3[$i].html}({$form.form_search3[$i].html})<br>{$form.form_goods_name3[$i].html}</td>
			{else}
				<!-- 代行伝票の場合検索リンクの（）無し -->
				<td class="Value">{$form.form_goods_cd3[$i].html}<br>{$form.form_goods_name3[$i].html}</td>
			{/if}
			<td class="Value" align="right">{$form.form_goods_num3[$i].html}</td>
			<!-- フリーズ判定 -->
			{if $var.contract_div == '1'}
				<!-- 通常の場合 -->
				<td class="Value">{$form.form_goods_cd2[$i].html}({$form.form_search2[$i].html})<br>{$form.form_goods_name2[$i].html}</td>
			{else}
				<!-- 代行伝票の場合検索リンクの（）無し -->
				<td class="Value">{$form.form_goods_cd2[$i].html}<br>{$form.form_goods_name2[$i].html}</td>
			{/if}
			<td class="Value" align="right">{$form.form_goods_num2[$i].html}</td>
			<!-- ＦＣ側の代行判定 -->
			{if $var.contract_div == 1 || $smarty.session.group_kind == '2'}
				<!-- 通常or直営 -->
				<td class="Value">
					<table height="20">
                    {* 2009-09-21 hashimoto-y *}
                    {if $toSmarty_discount_flg[$i] === 't'}
                        <tr style="color: red">
							<td>{$form.form_aprice_div[$i].html}</td>
							<td>
								{$form.form_br.html}<br>
		      					{$form.form_account_price[$i].html}円<br>
		      					{$form.form_account_rate[$i].html}%
							</td>
                    {else}
						<tr>
							<td><font color="#555555">{$form.form_aprice_div[$i].html}</font></td>
							<td><font color="#555555">
								{$form.form_br.html}<br>
		      					{$form.form_account_price[$i].html}円<br>
		      					{$form.form_account_rate[$i].html}%
							</font></td>
                    {/if}
						</tr>
					</table>
				</td>
                {* 前受相殺額 *}
                <td class="Value">
                {* 2009-09-21 hashimoto-y *}
                {if $toSmarty_discount_flg[$i] === 't'}
                    <table cellspacing="0" cellpadding="0">
                        <tr style="color: red"><td>{$form.form_ad_offset_radio[$i][1].html}</td><td></td></tr>
                        <tr style="color: red">
                            <td>{$form.form_ad_offset_radio[$i][2].html}</td>
                            <td>{$form.form_ad_offset_amount[$i].html}</td>
                        </tr>
                    </table>
                {else}
                    <table cellspacing="0" cellpadding="0">
                        <tr><td><font color="#555555">{$form.form_ad_offset_radio[$i][1].html}</font></td><td></td></tr>
                        <tr>
                            <td><font color="#555555">{$form.form_ad_offset_radio[$i][2].html}</font></td>
                            <td><font color="#555555">{$form.form_ad_offset_amount[$i].html}</font></td>
                        </tr>
                    </table>
                {/if}
                </td>
			{/if}
    		{* <td class="Value" align="center">{$form.form_breakdown[$i].html}</td> *}
            {* 自社巡回の場合のみ行クリア *}
			{if $var.contract_div == 1}
	    		<td class="Value" align="center">{$form.clear_line[$i].html}</td>
			{/if}
		</tr>
	{/foreach}
</table>

<!-- ＦＣor通常伝票で、同時実行エラーなしのみクリアボタン表示 -->
{* {if ($var.group_kind == 3 || $var.contract_div == '1') && $var.concurrent_err_flg != "true"} *}
{if $var.concurrent_err_flg != "true"}
	<table width="960">
		<tr>
			<td align='right'>{$form.clear_button.html}</td>
		</tr>
	</table>
{/if}

</A>
</fieldset>

<table border="0" width="970">
	<tr>
		<td align="left"><b><font color="red">※は必須入力です</font></b></td>
	</tr>
	<tr>
		<td align='right'>
			{if $var.concurrent_err_flg == "true"}
				{$form.slip_del_ok.html}
			{else}
				{* {$form.correction_button.html}　　{$form.form_back.html} *}
				{* rev.1.3 警告の場合、訂正ボタンは表示しない *}
				{if $var.ad_total_warn_mess == null && $var.warn_lump_change == null}{$form.correction_button.html}　　{/if}
				{$form.form_back.html}
			{/if}
		</td>
	</tr>
</table>

<!--******************** 画面表示2終了 *******************-->

					</td>
				</tr>
			</table>
		<!--******************** 画面表示終了 *******************-->

		</td>
	</tr>
</table>
<!--******************* 外枠終了 ********************-->

{$var.html_footer}
	


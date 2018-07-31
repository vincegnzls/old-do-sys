{$var.html_header}

<script language="javascript">
{$var.code_value}
 </script>

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
{* エラーメッセージ出力 *} 
    <span style="color: #ff0000; font-weight: bold; line-height: 130%;">
    {if $form.form_goods_cd.error != null}
        <li>{$form.form_goods_cd.error}<br>
    {/if}
    {if $form.form_goods_name.error != null}
        <li>{$form.form_goods_name.error}<br>
    {/if}
    {if $form.form_goods_cname.error != null}
        <li>{$form.form_goods_cname.error}<br>
    {/if}
    {if $form.form_url.error != null}
        <li>{$form.form_url.error}<br>
    {/if}
    {if $form.form_g_goods_id.error != null}
        <li>{$form.form_g_goods_id.error}<br>
    {/if}
    {if $form.form_product_id.error != null}
        <li>{$form.form_product_id.error}<br>
    {/if}
	{if $form.form_g_product_id.error != null}
        <li>{$form.form_g_product_id.error}<br>
    {/if}
    {if $form.form_in_num.error != null}
        <li>{$form.form_in_num.error}<br>
    {/if}
    {if $form.form_supplier.error != null}
        <li>{$form.form_supplier.error}<br>
    {/if}
    {if $form.form_order_point.error != null}
        <li>{$form.form_order_point.error}<br>
    {/if}
    {if $form.form_order_unit.error != null}
        <li>{$form.form_order_unit.error}<br>
    {/if}
    {if $form.form_lead.error != null}
        <li>{$form.form_lead.error}<br>
    {/if}
    </span>
{*--------------- メッセージ類 e n d ---------------*}

{*+++++++++++++++ 画面表示１ begin +++++++++++++++*}
<table width="520">
    <tr>
        <td>

<table width="100%">
    <tr>
        <td>{$form.form_show_dialog_button.html}</td>
        <td align="right">{if $smarty.get.goods_id != null}{$form.back_button.html}　{$form.next_button.html}{/if}
        </td>
    </tr>
</table>
<br>

<table>
    <tr>
        <td>
        <table class="Data_Table" border="1" width="280">
            <tr>
                <td class="Type" width="40" align="center" width="100"><b>状態</b></td>
                <td class="Value">{$form.form_state.html}</td>
            </tr>
        </table>
        </td>
	</tr>
	<!-- 本部商品のみ表示 -->
	{if $var.head_flg == true}
		<tr>
	        <td>
	        <table class="Data_Table" border="1" width="280">
	            <tr>
	                <td class="Type" width="40" align="center" width="100"><b>RtoR</b></td>
	                <td class="Value">{$form.form_rental.html}</td>
	            </tr>
	        </table>
	        </td>
			<td>
	        <table class="Data_Table" border="1" width="280">
	            <tr>    
	                <td class="Type" align="center" width="100"><b>シリアル管理</b></td>
	                <td class="Value">{$form.form_serial.html}</td>
	            </tr>   
	        </table>
	        </td>   
	    </tr>
	{/if}
</table>

        </td>
    </tr>
    <tr>
        <td>

<table class="Data_Table" border="1" width="100%">
<col width="130" style="font-weight: bold;">
    <tr>
        <td class="Title_Purple">商品コード<font color="#ff0000">※</font></td>
        <td class="Value">
        <table>
            <tr>
                <td rowspan="2">{$form.form_goods_cd.html}</td>
                <td><font color=#555555>上１桁が０は本部用</font></td>
            </tr>
            <tr>
                <td><font color=#555555>上１桁が０以外はショップ用</font></td>
            </tr>
        </table>
        </td>
    </tr>
	<tr>
        <td class="Title_Purple">Ｍ区分</a><font color="#ff0000">※</font></td>
        <td class="Value">
            {if $var.head_flg == true}
                {$form.form_g_goods_name.html}
            {else}
                {$form.form_g_goods_id.html}
            {/if}
        </td>
    </tr>
    <tr>
        <td class="Title_Purple">管理区分</a><font color="#ff0000">※</font></td>
        <td class="Value">
            {if $var.head_flg == true}
                {$form.form_product_name.html}
            {else}
                {$form.form_product_id.html}
            {/if}
        </td>
    </tr>
	<tr>
        <td class="Title_Purple">商品分類</a><font color="#ff0000">※</font></td>
        <td class="Value">
            {if $var.head_flg == true}
                {$form.form_g_product_name.html}
            {else}
                {$form.form_g_product_id.html}
            {/if}
        </td>
    </tr>
    <tr>
        <td class="Title_Purple">商品名<font color="#ff0000">※</font></td>
        <td class="Value">{$form.form_goods_name.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">略記<font color="#ff0000">※</font></td>
        <td class="Value">{$form.form_goods_cname.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">属性区分<font color="#ff0000">※</font></td>
        <td class="Value">
            {if $var.head_flg == true}
                {$form.form_attri_name.html}
            {else}
                {$form.form_attri_div.html}
            {/if}
        </td>
    </tr>
	<tr>
        <td class="Title_Purple">{$form.form_album_link.html}</td>
        <td class="Value"></td>
    </tr>
    <tr>
        <td class="Title_Purple">マーク<font color="#ff0000">※</font></td>
        <td class="Value">
            {if $var.head_flg == true}
                {$form.form_mark_name.html}
            {else}
                {$form.form_mark_div.html}
            {/if}
        </td>
    </tr>
    <tr>
        <td class="Title_Purple">単位</td>
        <td class="Value">{$form.form_unit.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">入数</td>
        <td class="Value">{$form.form_in_num.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">{$form.form_client_link.html}</td>
        <td class="Value">{$form.form_supplier.html}</td>
    </tr>
</table>

        </td>
    </tr>
    <tr>
        <td>

<table class="Data_Table" border="1" width="100%">
<col width="130" style="font-weight: bold;">
    <tr>
        <td class="Title_Purple">販売管理<font color="#ff0000">※</font></td>
        <td class="Value">
            {if $var.head_flg == true}
                {$form.form_sale_manage_name.html}
            {else}
                {$form.form_sale_manage.html}
            {/if}
        </td>
    </tr>
    <tr>
        <td class="Title_Purple">在庫管理<font color="#ff0000">※</font></td>
        <td class="Value">
            {* 2009-10-08 hashimoto-y *}
            {$form.form_stock_manage.html}
        </td>
    </tr>
    <tr>
        <td class="Title_Purple">在庫限り品</td>
        <td class="Value">
            {if $var.head_flg == true}
                {$form.form_stock_only_name.html}
            {else}
                {$form.form_stock_only.html}
            {/if}
        </td>
    </tr>
</table>

        </td>
    </tr>
    <tr>
        <td>

<table class="Data_Table" border="1" width="100%">
<col width="130" style="font-weight: bold;">
    <tr>
        <td class="Title_Purple">発注点</td>
        <td class="Value">{$form.form_order_point.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">発注単位数</td>
        <td class="Value">{$form.form_order_unit.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">リードタイム</td>
        <td class="Value">{$form.form_lead.html}日</td>
    </tr>
</table>

        </td>
    </tr>
    <tr>
        <td>

<table class="Data_Table" border="1" width="100%">
<col width="130" style="font-weight: bold;">
    <tr>
        <td class="Title_Purple">品名変更<font color="#ff0000">※</font></td>
        <td class="Value">
            {if $var.head_flg == true}
                {$form.form_name_change_name.html}
            {else}
                {$form.form_name_change.html}
            {/if}
        </td>
    </tr>
    <tr>
        <td class="Title_Purple">課税区分<font color="#ff0000">※</font></td>
        <td class="Value">
            {if $var.head_flg == true}
                {$form.form_tax_div_name.html}
            {else}
                {$form.form_tax_div.html}
            {/if}
        </td>
    </tr>
</table>

        </td>
    </tr>
    <tr>
        <td>

<table class="Data_Table" border="1" width="100%">
<col width="130" style="font-weight: bold;">
    <tr>
        <td class="Title_Purple">最新売上日</td>
        <td class="Value">{$var.sale_day}</td>
    </tr>
    <tr>
        <td class="Title_Purple">最新仕入日</td>
        <td class="Value">{$var.buy_day}</td>
    </tr>
</table>

        </td>
    </tr>
    <tr>
        <td>

<table class="Data_Table" border="1" width="100%">
<col width="130" style="font-weight: bold;">
    <tr>
        <td class="Title_Purple">備考</td>
        <td class="Value">{$form.form_note.html}</td>
    </tr>
</table>

<table width="100%">
    <tr>
        <td><b><font color="#ff0000">※は必須入力です</font></b></td>
        <td align="right">{$form.form_entry_button.html}　{$form.form_back_button.html}</td>
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

{$var.html_header}

<!-- styleseet -->
<style type="text/css">

	/** 総合計 **/
	td.total {ldelim}
		height: 24px;
		background-color:  #FFBBC3;
		border-color: #B9B9B8;
	{rdelim}

	/** ショップごとの合計 **/
	td.sub {ldelim}
		height: 24px;
		border-color: #B9B9B8;
		background-color: #99CCFF;
	{rdelim}

</style>

<body class="bgimg_purple">
<form {$form.attributes}>
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
{* 表示権限のみ時のメッセージ *} 
{if $var.auth_r_msg != null}
    <span style="color: #ff0000; font-weight: bold; line-height: 130%;">
    <li>{$var.auth_r_msg}</li>
    </span><br>
{/if}
{*--------------- メッセージ類 e n d ---------------*}

{*+++++++++++++++ 画面表示１ begin +++++++++++++++*}
<table  class="Data_Table" border="1" width="750">
    <tr>
        <td class="Title_Pink" width="100"><b>状況</b></td>
        <td class="Value" align="left" colspan="3">{$form.form_stat_check.html}</td>
    </tr>
    <tr>
        <td class="Title_Pink" width="100"><b>レンタル番号</b></td>
        <td class="Value" align="left">{$form.form_rental_no.html}</td>
        <td class="Title_Pink" width="100"><b>商品分類</b></td>
        <td class="Value" align="left">{$form.form_g_product_id.html}</td>
    </tr>
	<tr>
      	<td class="Title_Pink" width="110"><b>ショップコード</b></td>
       	<td class="Value" align="left">{$form.form_client.html}</td>
		<td class="Title_Pink" width="110"><b>ショップ名</b></td>
       	<td class="Value" align="left">{$form.form_fc.html}</td>
    </tr>
	<tr>
      	<td class="Title_Pink" width="100"><b>商品コード</b></td>
       	<td class="Value" align="left">{$form.form_goods_cd.html}</td>
		<td class="Title_Pink" width="100"><b>商品名</b></td>
       	<td class="Value" align="left">{$form.form_goods_cname.html}</td>
    </tr>
</table>
<table width="750">
    <tr>
        <td align="right">{$form.form_show_button.html}　　{$form.form_clear_button.html}</td>
    </tr>
</table>
{if $var.display_flg == true}
<br>
<table class="List_Table" border="1" width="650">
    <tr align="center" style="font-weight: bold;">
        <td class="total" width="162">ショップ数</td>
        <td class="total" width="162">レンタル総数</td>
        <td class="total" width="162">本部経費総額</td>
        <td class="total" width="162">ショップ提供総額</td>
    </tr>
    <tr>
        <td class="Value" align="right">{$var.total_shop}</td>
        <td class="Value" align="right">{$var.total_num}</td>
        <td class="Value" align="right">{$var.total_rental}</td>
        <td class="Value" align="right">{$var.total_user}</td>
    </tr>
</table>
<br><hr><br>
{/if}

{foreach from=$disp_data2 key=i item=item}
	<table class="List_Table" border="1" width="650">
	    <tr align="center" style="font-weight: bold;">
	        <td class="sub" width="162">ショップ名</td>
	        <td class="sub" width="162">レンタル合計数</td>
	        <td class="sub" width="162">本部経費額</td>
	        <td class="sub" width="162">ショップ提供額</td>
	    </tr>
	    <tr>
	        <td class="Value">{$disp_data2[$i][0]}<br>{$disp_data2[$i][1]}</td>
	        <td class="Value" align="right">{$disp_data2[$i][2]}</td>
	        <td class="Value" align="right">{$disp_data2[$i][3]}</td>
	        <td class="Value" align="right">{$disp_data2[$i][4]}</td>
	    </tr>
	</table>
	<br>
	<table class="List_Table" border="1" width="100%">
	    <tr align="center" style="font-weight: bold;">
	        <td class="Title_Purple"><b>No.</b></td>
	        <td class="Title_Purple"><b>設置先</b></td>
	        <td class="Title_Purple"><b>レンタル番号</b></td>
	        <td class="Title_Purple"><b>出荷日</b></td>
	        <td class="Title_Purple"><b>解約日</b></td>
	        <td class="Title_Purple"><b>状況</b></td>
	        <td class="Title_Purple"><b>商品名</b></td>
	        <td class="Title_Purple"><b>数量</b></td>
	        <td class="Title_Purple"><b>シリアル</b></td>
			<td class="Title_Purple"><b>　　本部経費単価<br>ショップ提供単価</b></td>
			<td class="Title_Purple"><b>　　本部経費金額<br>ショップ提供金額</b></td>
			<td class="Title_Purple"><b>備考</b></td>
	    </tr>
	   
		{$html[$i]}

	</table>
<br><hr><br>
{/foreach}


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

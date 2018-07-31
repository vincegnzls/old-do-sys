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
{if $var.error != null}<li>{$var.error}<br>{/if}
</span>
{*--------------- メッセージ類 e n d ---------------*}

{*+++++++++++++++ 画面表示１ begin +++++++++++++++*}
<table width="550">
    <tr>
        <td>

<table class="Data_Table" border="1" width="600">
<col width="110" style="font-weight: bold;">
<col width="190">
<col width="110" style="font-weight: bold;">
<col>
    {* 商品・倉庫が指定されているか *}
    {if $var.get_goods_id == NULL && $var.get_ware_id == NULL}
{*
        <tr>
            <td class="Title_Yellow">出力形式</td>
            <td class="Value" colspan="3">{$form.form_output.html}</td>
        </tr>
*}
        <tr>
            <td class="Title_Yellow">取扱期間</td>
            <td class="Value" colspan="3">{$form.form_hand_day.html}</td>
        </tr>
        <tr>
            <td class="Title_Yellow">倉庫</td>
            <td class="Value" colspan="3">{$form.form_ware.html}</td>
        </tr>
        <tr>
            <td class="Title_Yellow">商品コード</td>
            <td class="Value">{$form.form_goods_cd.html}</td>
            <td class="Title_Yellow">商品名</td>
            <td class="Value" width="250">{$form.form_goods_cname.html}</td>
        </tr>
        <tr>
            <td class="Title_Yellow">商品分類</td>
            <td class="Value">{$form.form_g_product.html}</td>
            {* 2009-10-12 aoyama-n *}
            <td class="Title_Yellow">出力形式</td>
            <td class="Value">{$form.form_output_type.html}</td>
        </tr>
    {else}
        {* 指定されていた場合は、取扱期間だけ表示 *}
        <tr>
            <td class="Title_Yellow">取扱期間</td>
            <td class="Value" colspan="3">{$form.form_hand_day.html}</td>
        </tr>
    {/if}
</table>

<table align="right">
    <tr>
        <td>{$form.show_button.html}　　{$form.clear_button.html}</td>
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

{if $var.display_flg == true}

<table width="100%">
    <tr>
        <td>

<span style="font: bold 15px; color: #555555;">
【取扱期間：
{if $var.error == null && ($var.hand_start != NULL || $var.hand_end != NULL)}{$var.hand_start} 〜 {$var.hand_end}
{else}指定無し
{/if}
】
</span>
<br>

{$var.html_page}
<table class="List_Table" border="1" width="100%">
    <tr align="center" style="font-weight: bold;">
        <td class="Title_Yellow">No.</td>
        <td class="Title_Yellow">倉庫</td>
        <td class="Title_Yellow">商品分類</td>
        <td class="Title_Yellow">商品コード<br>商品名</td>
        <td class="Title_Yellow">前残在庫</td>
        <td class="Title_Yellow">入庫数</td>
        <td class="Title_Yellow">出庫数</td>
        <td class="Title_Yellow">現在庫数</td>
    </tr>
    {foreach key=j from=$row item=items}
    {* 偶数なら色付けない *}
    {if $j==0 || $j%2==0}
        <tr class="Result1">
    {else}
        {* 奇数なら色付ける *}
        <tr class="Result2">
    {/if}
        <td align="right">
        {if $smarty.post.show_button == "表　示"}
            {$j+1}
        {elseif $smarty.post.f_page1 != null}
            {$smarty.post.f_page1*100+$j-99}
        {else}
            {$j+1}
        {/if}
        </td>
        <td>{$row[$j][0]}</td>
        <td>{$row[$j][9]}</td>
        {* 発注点警告・出荷予定一覧から遷移してきたか判定 *}
        {if $var.get_goods_id == NULL && $var.get_ware_id == NULL}
            {* 受払照会 *}
            <td>{$row[$j][1]}<br><a href="1-4-114.php?ware_id={$row[$j][3]}&goods_id={$row[$j][4]}&start={$var.hand_start}&end={$var.hand_end}">{$row[$j][2]}</a></td>
        {elseif $var.get_goods_id != NULL && $var.get_ware_id == NULL}
            {* 発注点警告 *}
            <td>{$row[$j][1]}<br><a href="1-4-114.php?ware_id={$row[$j][3]}&goods_id={$row[$j][4]}&start={$var.hand_start}&end={$var.hand_end}&trans_flg=1">{$row[$j][2]}</a></td>
        {elseif $var.get_goods_id == NULL && $var.get_ware_id != NULL}
            {* 出荷予定一覧 *}
            <td>{$row[$j][1]}<br><a href="1-4-114.php?ware_id={$row[$j][3]}&goods_id={$row[$j][4]}&start={$var.hand_start}&end={$var.hand_end}&trans_flg=2">{$row[$j][2]}</a></td>
        {elseif $var.get_goods_id != NULL && $var.get_ware_id != NULL}
            {* 在庫照会 *}
            <td>{$row[$j][1]}<br><a href="1-4-114.php?ware_id={$row[$j][3]}&goods_id={$row[$j][4]}&start={$var.hand_start}&end={$var.hand_end}&trans_flg=3">{$row[$j][2]}</a></td>
        {/if}
        <td align="right">{$row[$j][5]}</td>
        <td align="right">{$row[$j][6]}</td>
        <td align="right">{$row[$j][7]}</td>
        <td align="right">{$row[$j][8]}</td>
    </tr>
    {/foreach}
</table>
{$var.html_page2}

        </td>
    </tr>
</table>

{/if}
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

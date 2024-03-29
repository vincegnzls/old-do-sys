{$var.html_header}

<body bgcolor="#D8D0C8">
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
{*--------------- メッセージ類 e n d ---------------*}

{*+++++++++++++++ 画面表示１ begin +++++++++++++++*}
<table width="700">
    <tr>
        <td>
            <table class="Data_Table" border="1" width="430">
            <col width="90" style="font-weight: bold;">
            <col>
                <tr>
                    <td class="Title_Purple">商品コード</td>
                    <td class="Value">{$form.form_goods_cd.html}</td>
                </tr>
                <tr>
                    <td class="Title_Purple">Ｍ区分</td>
                    <td class="Value">{$form.form_g_goods.html}</td>
                </tr>
                <tr>
                    <td class="Title_Purple">管理区分</td>
                    <td class="Value">{$form.form_product.html}</td>
                </tr>
                <tr>
                    <td class="Title_Purple">商品分類</td>
                    <td class="Value">{$form.form_g_product.html}</td>
                </tr>
                <tr>
                    <td class="Title_Purple">商品名</td>
                    <td class="Value">{$form.form_goods_name.html}</td>
                </tr>
                <tr>
                    <td class="Title_Purple">略記</td>
                    <td class="Value">{$form.form_goods_cname.html}</td>
                </tr>
            </table>
        </td>
        <td valign="bottom">
            <table class="Data_Table" border="1" width="480">
            <col width="90" style="font-weight: bold;">
                <tr>
                    <td class="Title_Purple">在庫限り品</td>
                    <td class="Value">{$form.form_stock_only.html}</td>
                </tr>
                <tr>
                    <td class="Title_Purple">状態</td>
                    <td class="Value">{$form.form_state.html}</td>
                </tr>
                <tr>
                    <td class="Title_Purple">承認</td>
                    <td class="Value">{$form.form_accept.html}</td>
                </tr>
                <tr>
                    <td class="Title_Purple">出力形式</td>
                    <td class="Value">{$form.form_output_type.html}</td>
                </tr>
                <tr>
                    <td class="Title_Purple">表示件数</td>
                    <td class="Value">{$form.form_display_num.html}</td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan="2" align="right">{$form.form_show_button.html}　　{$form.form_clear_button.html}</td>
    </tr>
</table>
<br>
{*--------------- 画面表示１ e n d ---------------*}

                    </td>
                </tr>
                <tr>
                    <td>

{*+++++++++++++++ 画面表示２ begin +++++++++++++++*}
{if $var.display_flg == true}

<table width="100%">
    <tr>
        <td>

<table class="Data_Table" border="1">
    <tr align="center" style="font-weight: bold;">
        <td class="Title_Purple" width="100">{Make_Sort_Link_Tpl form=$form f_name="nl_attri_div1" hdn_form="hdn_attri_div"}</td>
        <td class="Title_Purple" width="100">{Make_Sort_Link_Tpl form=$form f_name="nl_attri_div2" hdn_form="hdn_attri_div"}</td>
        <td class="Title_Purple" width="100">{Make_Sort_Link_Tpl form=$form f_name="nl_attri_div3" hdn_form="hdn_attri_div"}</td>
        <td class="Title_Purple" width="100">{Make_Sort_Link_Tpl form=$form f_name="nl_attri_div4" hdn_form="hdn_attri_div"}</td>
        <td class="Title_Purple" width="100">{Make_Sort_Link_Tpl form=$form f_name="nl_attri_div5" hdn_form="hdn_attri_div"}</td>
        <td class="Title_Purple" width="100">{Make_Sort_Link_Tpl form=$form f_name="nl_attri_div6" hdn_form="hdn_attri_div"}</td>
    </tr>
    <tr>
        <td class="Value" align="right">{$attri_div[0]}件</td>
        <td class="Value" align="right">{$attri_div[1]}件</td>
        <td class="Value" align="right">{$attri_div[2]}件</td>
        <td class="Value" align="right">{$attri_div[3]}件</td>
        <td class="Value" align="right">{$attri_div[4]}件</td>
        <td class="Value" align="right">{$attri_div[0]+$attri_div[1]+$attri_div[2]+$attri_div[3]+$attri_div[4]}件</td>
    </tr>
</table>

<br>

{if $var.display_num == 2}
全<b>{$var.match_count}</b>件
{else}
{$var.html_page}
{/if}

<table class="List_Table" border="1" width="100%">
    <tr align="center" style="font-weight: bold;">
        <td class="Title_Purple">No.</td>
        <td class="Title_Purple">{Make_Sort_Link_Tpl form=$form f_name="sl_goods_cd"}</td>
        <td class="Title_Purple">{Make_Sort_Link_Tpl form=$form f_name="sl_g_goods"}</td>
        <td class="Title_Purple">{Make_Sort_Link_Tpl form=$form f_name="sl_product"}</td>
        <td class="Title_Purple">{Make_Sort_Link_Tpl form=$form f_name="sl_g_product"}</td>
        <td class="Title_Purple">{Make_Sort_Link_Tpl form=$form f_name="sl_goods_name"}</td>
        <td class="Title_Purple">{Make_Sort_Link_Tpl form=$form f_name="sl_goods_cname"}</td>
        <td class="Title_Purple">{Make_Sort_Link_Tpl form=$form f_name="sl_attribute"}</td>
        <td class="Title_Purple">アルバム</td>
        <td class="Title_Purple">在庫品</td>
        <td class="Title_Purple">状態</td>
        <td class="Title_Purple">承認</td>
    </tr>
        {foreach from=$page_data key=j item=item}
        {if $page_data[$j][12] == 't'}
        <tr class="Result5">
        {elseif $j%2 == 1}
        <tr class="Result2">
        {else}
        <tr class="Result1">
        {/if}
        <td align="right">
            {if $smarty.post.form_show_button == "表　示"}
                {*表示押下*}
                {$j+1}
            {elseif $smarty.post.form_display_num != 2 && $smarty.post.f_page1 != ""}
                {$smarty.post.f_page1*100+$j-99}
            {else if}
            　  {$j+1}
            {/if}
        </td>
        <td><a href="1-1-221.php?goods_id={$page_data[$j][1]}">{$page_data[$j][0]}</a></td>
        <td align="left">{$page_data[$j][5]}</td>
        <td align="left">{$page_data[$j][4]}</td>
        <td align="left">{$page_data[$j][9]}</td>
        <td align="left">{$page_data[$j][2]}</td>
        <td align="left">{$page_data[$j][3]}</td>
        <td align="center">{$page_data[$j][6]}</td>
        {if $page_data[$j][8] != Null}
        <td align="center"><a href="#" onClick="window.open('{$var.url}{$page_data[$j][8]}');">表示</a></td>
        {else}
        <td></td>
        {/if}
        <td align="center">{if $page_data[$j][10] == "1"}○{/if}</td>
        <td align="center">{$page_data[$j][7]}</td>
        {if $page_data[$j][11] == '1'}
        <td align="center">○</td>
        {else}
        <td align="center"><font color="red">×</font></td>
        {/if}
    </tr>
        {/foreach}
</table>
{if $var.display_num != 2}{$var.html_page2}{/if}

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

{$var.html_header}

<script language="javascript">
{$html.js}
 </script>

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
{* エラーメッセージ *} 
<span style="color: #ff0000; font-weight: bold; line-height: 130%;">
{if $form.form_c_staff.error != null}
    <li>{$form.form_c_staff.error}
{/if}
{if $form.form_ord_day.error != null}
    <li>{$form.form_ord_day.error}
{/if}
{if $form.form_multi_staff.error != null}
    <li>{$form.form_multi_staff.error}
{/if}
{if $form.form_hope_day.error != null}
    <li>{$form.form_hope_day.error}
{/if}
{if $form.err_bought_slip.error != null}
    <li>{$form.err_bought_slip.error}
{/if}
</span>
{*--------------- メッセージ類 e n d ---------------*}

{*+++++++++++++++ 画面表示１ begin +++++++++++++++*}
<table>
    <tr>
        <td>{$html.html_s}</td>
    </tr>
</table>
{*--------------- 画面表示１ e n d ---------------*}

                    </td>
                </tr>
                <tr>
                    <td>

{*+++++++++++++++ 画面表示２ begin +++++++++++++++*}
{if $var.post_flg == true && $var.err_flg != true}

<table>
    <tr>
        <td>
    <span style="color: #0000ff; font-weight: bold; line-height: 130%;">
         ※発注書印刷についてはオンラインは必要ありません。
    </span>
{$html.html_page}
<table class="List_Table" border="1" width="100%">
    <tr align="center" style="font-weight: bold;">
        <td class="Title_Blue">No.</td>
        <td class="Title_Blue">{Make_Sort_Link_Tpl form=$form f_name="sl_ord_day"}</td>
        <td class="Title_Blue">{Make_Sort_Link_Tpl form=$form f_name="sl_slip"}</td>
        <td class="Title_Blue">
            {Make_Sort_Link_Tpl form=$form f_name="sl_client_cd"}<br>
            <br style="font-size: 4px;">
            {Make_Sort_Link_Tpl form=$form f_name="sl_client_name"}<br>
        </td>
        <td class="Title_Blue">本部確認</td>
        <td class="Title_Blue">発注金額</td>
        {* <td class="Title_Blue">希望納期</td> *}
        <td class="Title_Blue">{Make_Sort_Link_Tpl form=$form f_name="sl_hope_day"}</td>
        <td class="Title_Blue">通信欄<br>（ＦＣ宛）</td>
        <td class="Title_Blue">仕入完了</td>
        <td class="Title_Blue">削除</td>
        <td class="Title_Blue">発注書印刷</td>
    </tr>
    {foreach key=j from=$row item=items}
    {if $j is even}
    <tr class="Result1"> 
    {else}
    <tr class="Result2">
    {/if}
        <td align="right">
            {$var.no+$j+1}
        </td>
        {*仕入先がFCのばあいは時間は表示しない*}
        <td align="center">{$row[$j][1]}<br>{if $row[$j][5] != null}{$row[$j][2]}{/if}</td>
            {* 取消かNULLかつ未処理の時、変更可 ＊レンタル料は変更不可*} 
            {if $row[$j][5] == '3' || ($row[$j][5] == null && $row[$j][8] == '1') && $row[$j][14] == 'f'}
                <td align="center"><a href="2-3-102.php?ord_id={$row[$j][0]}">{$row[$j][3]}</a></td>
            {else}
                <td align="center"><a href="2-3-103.php?ord_id={$row[$j][0]}&ord_flg=true">{$row[$j][3]}</a></td>
            {/if}
        <td>{$row[$j][11]}<br>{$row[$j][4]}</td>
            {if $row[$j][5] == '1'}
        <td align="center">
                <font color="#ff0000">未確認</font>
        </td>
            {elseif $row[$j][5] == '2'}
        <td align="center">
                済
        </td>
            {elseif $row[$j][5] == '3'}
        <td align="center">
                <font color="#008000">取消</font>
        </td>
            {else}
        <td align="center">
                ----
        </td>
            {/if}
        <td align="right">
            {$row[$j][6]|number_format}
        </td>
        <td align="center">{$row[$j][15]}</td>
        <td align="left">{$row[$j][16]}</td>
        <td align="center">
            {if $row[$j][8] == 3 || $row[$j][8] == 4}
                済
            {else}
                <font color="#ff0000">未</font>
            {/if}
        </td>
        <td align="center">
            {if $row[$j][5] == '3' || $row[$j][5] == null && $row[$j][8] == '1'}
                {if $var.auth == "w"}<a href="#" onClick="Order_Delete('data_delete_flg','ord_id_flg',{$row[$j][0]},'hdn_del_enter_date','{$row[$j][13]}');">削除</a>{/if}
            {/if}
        </td>
        <td align="center">
            {*オンライン発注 AND 取り消し状態でない AND 発注書発行済み*}
            {if $row[$j][7] == 't' && $row[$j][5] != '3' && $row[$j][10] == 't'}
                {if $var.auth == "w"}<a href="#" onClick="window.open('../../franchise/buy/2-3-105.php?ord_id={$row[$j][0]}','_blank','');">印刷</a>{/if}

            {*オフライン発注 AND 発注書発行済み *}
            {elseif $row[$j][7] == 't' &&  $row[$j][10] == 'f'}
                {if $var.auth == "w"} <a href="#" onClick="window.open('../../franchise/buy/2-3-107.php?ord_id={$row[$j][0]}','_blank','');">印刷</a>{/if}

            {*発注書未発行 AND 取り消し状態でない AND オンライン発注*}
            {elseif $row[$j][7] == 'f' && $row[$j][5] != '3' && $row[$j][10] == 't'}
                <a href="#" onClick="Order_Sheet('order_sheet_flg','ord_id_flg',2,{$row[$j][0]},{$row[$j][8]});">印刷</a>

            {*発注書未発行 AND オフライン発注*}
            {elseif $row[$j][7] == 'f' && $row[$j][10] == 'f'}
                <a href="#" onClick="Order_Sheet('order_sheet_flg','ord_id_flg',1,{$row[$j][0]},{$row[$j][8]});">印刷</a>

            {*本部が発注を取り消した場合はリンクを非表示*}
            {else}
            {/if}
        </td>
    </tr>
    {/foreach}
</table>
{$html.html_page2}

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

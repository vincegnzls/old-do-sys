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

{*--------------- メッセージ類 e n d ---------------*}

{*+++++++++++++++ 画面表示１ begin +++++++++++++++*}
<table>
    <tr>
        <td>

<table class="Data_Table" border="1" width="350">
<col width="110" style="font-weight: bold;">
<col>
    <tr>
        <td class="Title_Purple">仕入先コード</td>
        <td class="Value">{$form.form_client_cd.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">仕入先名</td>
        <td class="Value">{$form.form_client_name.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">地区</td>
        <td class="Value">{$form.form_area_id.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">TEL</td>
        <td class="Value">{$form.form_tel.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">状態</td>
        <td class="Value">{$form.form_state_type.html}</td>
    </tr>
</table>

        </td>
        <td valign="bottom">

<table class="Data_Table" border="1" width="300">
    <tr>
        <td class="Title_Purple" width="80"><b>出力形式</b></td>
        <td class="Value">{$form.form_output_type.html}</td>
    </tr>
</table>

        </td>
    </tr>
    <tr>
        <td colspan="2">

<table align="right">
    <tr>
        <td>{$form.form_button.show_button.html}　　{$form.form_button.clear_button.html}</td>
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

全<b>{$var.match_count}</b>件
<table class="List_Table" border="1" width="100%">
    <tr align="center" style="font-weight: bold;">
        <td class="Title_Purple">No.</td>
        <td class="Title_Purple">仕入先コード<br>仕入先名</td>
        <td class="Title_Purple">地区</td>
        <td class="Title_Purple">TEL</td>
        <td class="Title_Purple">状態</td>
    {foreach key=j from=$row item=items}
    <tr class="Result1"> 
        <td align="right">
            {if $smarty.post.f_page1 != null}
                {$smarty.post.f_page1*10+$j-9}
            {else if}
            　  {$j+1}
            {/if}
        </td>               
        <td>
            {$row[$j][1]}<br>
            <a href="2-1-216.php?client_id={$row[$j][0]}">{$row[$j][2]}</a></td>
        </td>
        <td align="center">{$row[$j][3]}</td>
        <td>{$row[$j][4]}</td>
        <td align="center">
        {if $row[$j][5] == 1}
            取引中
        {elseif $row[$j][5] == 2}
            解約・休止中
        {elseif $row[$j][5] == 3}
            解約
        {/if}
        </td>
    </tr>
    {/foreach}
</table>

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

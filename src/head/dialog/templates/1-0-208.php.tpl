{$var.html_header}

<head>
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Cache-Control" content="no-cache">
<meta http-equiv="Expires" content="0">
</head>

<body bgcolor="#D8D0C8" onLoad="document.dateForm.form_supplier_name.focus()">
<form name="dateForm" method="post">
{$form.hidden}
{*+++++++++++++++ 外枠 begin +++++++++++++++*}
<table width="100%" height="90%">

    {*+++++++++++++++ コンテンツ部 begin +++++++++++++++*}
    <tr valign="top">
        <td>
            <table>
                <tr>
                    <td>

<form {$form.attributes}>

{*+++++++++++++++ メッセージ類 begin +++++++++++++++*}

{*--------------- メッセージ類 e n d ---------------*}

{*+++++++++++++++ 画面表示１ begin +++++++++++++++*}
<table width="450">
    <tr>
        <td>

<table class="Data_Table" border="1" width="100%">
<col width="110" style="font-weight: bold;">
<col>
    <tr>
        <td class="Title_Purple">仕入先コード</td>
        <td class="Value">{$form.form_supplier_cd.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">仕入先名</td>
        <td class="Value">{$form.form_supplier_name.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">略称</td>
        <td class="Value">{$form.form_supplier_cname.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">業種</td>
        <td class="Value">{$form.form_btype_name.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">状態</td>
        <td class="Value">{$form.form_state.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">表示順</td>
        <td class="Value">{$form.form_sort_type.html}</td>
    </tr>
</table>

<table align="right">
    <tr>
        <td>{$form.form_show_button.html}　　{$form.form_clear_button.html}</td>
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
<table width="100%">
    <tr>
        <td>

全<b>{$var.match_count}</b>件
<table class="List_Table" border="1" width="100%">
<col align="right">
<col span="4">
    <tr align="center" style="font-weight: bold;">
        <td class="Title_Purple" align="center">No.</td>
        <td class="Title_Purple">仕入先コード</td>
        <td class="Title_Purple">仕入先名</td>
        <td class="Title_Purple">略称</td>
        <td class="Title_Purple">業種</td> 
        <td class="Title_Purple">状態</td> 
   </tr>
    {foreach from=$page_data key=i item=items}
    <tr class="Result1">
        <td>{$i+1}</td>
    {foreach from=$items key=j item=item}
    {if $j == 0}
        <td><a href="#" onClick="returnValue=Array({$return_data[$i]});window.close();">{$item}</a></td>
    {elseif $j == 1}
        <td>{$item}</td>
    {elseif $j == 2}
        <td>{$item}</td>
    {elseif $j == 3}
        <td>{$item}</td>
    {elseif $j == 4}
        <td>{$item}</td>
    {/if}
    {/foreach}
    </tr>
    {/foreach}
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

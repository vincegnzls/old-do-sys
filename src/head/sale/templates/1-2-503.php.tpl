{$var.html_header}

<body bgcolor="#D8D0C8">
<form name="dateForm" method="post">
{$form.hidden}

{*+++++++++++++++ 外枠 begin +++++++++++++++*}
<table width="100%" height="90%" class="M_Table">

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
<ul style="margin-left: 16px;">
{if $form.form_client.error != null}
    <li>{$form.form_client.error}<br>
{/if}
{if $form.form_count_day.error != null}
    <li>{$form.form_count_day.error}<br>
{/if}
</ul>
</span>
{*--------------- メッセージ類 e n d ---------------*}

{*+++++++++++++++ 画面表示１ begin +++++++++++++++*}
<table>
    <tr>    
        <td>    

<table class="Data_Table" border="1" width="400">
<col width="100" style="font-weight: bold;">
<col>
    <tr>
        <td class="Title_Pink">{$form.form_client_link.html}<font color="#ff0000">※</font></td>
        <td class="Value">{$form.form_client.html}</td>
    </tr>
    <tr>
        <td class="Title_Pink">集計期間<font color="#ff0000">※</font></td>
        <td class="Value">{$form.form_count_day.html}</td>
    </tr>
    <tr>
        <td class="Title_Purple">出力形式</td>
        <td class="Value">{$form.form_output.html}</td>
    </tr>
</table>

<table width="400">
    <tr align="right">
        <td>{$form.form_show_button.html}　{$form.form_clear_button.html}</td>
    </tr>   
</table>

        </td>
        <td valign="top" style="padding-left: 20px;">

<table>
<col span="8" width="100" style="color: #525552;">
    <tr>
        <td>11：掛売上</td>
        <td>13：掛返品</td>
        <td>14：掛値引</td>
        <td>15：割賦売上</td>
    </tr>
    <tr>
        <td>61：現金売上</td>
        <td>63：現金返品</td>
        <td>64：現金値引</td>
        <td></td>
    </tr>
    <tr style="height: 0px;"><td colspan="4"></td></tr>
    <tr>
        <td>31：集金</td>
        <td>32：振込入金</td>
        <td>33：手形入金</td>
        <td>34：相殺</td>
    </tr>
    <tr>
        <td>35：手数料</td>
        <td>36：その他入金</td>
        <td>37：スイット相殺</td>
        <td>38：入金調整</td>
    </tr>
    <tr>
        <td>39：現金売上</td>
        <td></td>
        <td></td>
        <td></td>
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
<table>
    <tr>
        <td>

<table>
    <tr> 
        <td colspan="2">{$html_1}</td>
    </tr>
    <tr>
        <td valign="top">{$html_2}</td>
    </tr>
    <tr>    
        <td colspan="2">{$var.html_page}{$html_3}{$var.html_page2}</td>
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

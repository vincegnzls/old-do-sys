{$var.html_header}
<script language="javascript">
{$var.code_value}
{$var.contract}
{$var.js}
</script>


<body bgcolor="#D8D0C8" onLoad="Text_Disabled('{$smarty.post.form_slipout_type[0]}')">
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
{* エラーメッセージ出力 *} 
<span style="color: #ff0000; font-weight: bold; line-height: 130%;">
    {if $form.form_branch_id.error != null}
        <li>{$form.form_branch_id.error}<br>
    {/if}
    {if $form.form_claim_day1.error != null}
        <li>{$form.form_claim_day1.error}<br>
    {/if}
    {if $form.form_claim_day2.error != null}
        <li>{$form.form_claim_day2.error}<br>
    {/if}
    {if $form.form_claim.error != null}
        <li>{$form.form_claim.error}<br>
    {/if}
    {if $form.form_year_month.error != null}
        <li>{$form.form_year_month.error}<br>
    {/if}
    {if $var.error != null}
        <li>{$var.error}<br>
    {/if}
    <ul>
    {foreach from=$sale_err key=i item=item}
        {if $i == 0}
        <li>以下の売上伝票は日次更新されていないため請求データの作成に失敗しました。<br>
        {/if}
        {$item}<br>
    {/foreach}
    {foreach from=$pay_err key=i item=item}
        {if $i == 0}
        <li>以下の入金伝票は日次更新されていないため請求データの作成に失敗しました。<br>
        {/if}
        {$item}<br>
    {/foreach}
    {foreach from=$advance_err key=i item=item}
        {if $i == 0}
        <li>以下の前受金伝票は確定されていないため請求データの作成に失敗しました。<br>
        {/if}
        {$item}<br>
    {/foreach}
    </ul>
</span>   
{*--------------- メッセージ類 e n d ---------------*}

{*+++++++++++++++ 画面表示１ begin +++++++++++++++*}
<table width="100%">
    <tr>
        <td>
        <div class="note">
        請求データの作成について<br>
        　�＼禅畚颪鮃洪係紊郎萄鄒�されません。<br>
        　�∪禅當�日更新前の請求データは変更可能です。但し、請求作成済のデータを削除して作成可能です。<br>
        <div><p>
<table width="500" >
    <tr>
        <td align="left" colspan="2">{$form.form_slipout_type[0].html}</td>
    </tr>
    <tr>
        <td width="100"></td>
        <td>
        指定した締日の得意先に対して、請求書を作成します
        <table class="Data_Table" border="1" width="300">
        <col width="100" style="font-weight:bold;">
        <col>
            <tr>
                <td class="Title_Pink">本支店<font color="#ff0000">※</font></td>
                <td class="Value">{$form.form_branch_id.html}</td>
            </tr>
            <tr>
                <td class="Title_Pink">請求締日<font color="#ff0000">※</font></td>
                <td class="Value">{$form.form_claim_day1.html}</td>
            </tr>
        </table>
        </td>
    </tr>
</table>
        </td>
    </tr>
    <tr>
        <td>

<table width="660" >
    <tr>
        <td align="left" colspan="2">{$form.form_slipout_type[1].html}</td>
    </tr>
    <tr>
        <td width="100"></td>
        <td>
        指定した得意先に対して、指定した請求締日までの請求書を作成します
        <table class="Data_Table" border="1" width="450">
        <col width="100" style="font-weight:bold;">
        <col>
            <tr>
                <td class="Title_Pink">{$form.form_claim_link.html}<font color="#ff0000">※</font></td>
                <td class="Value">{$form.form_claim.html}</td>
            </tr>
            <tr>
                <td class="Title_Pink">請求締日<font color="#ff0000">※</font></td>
                {*<td class="Value">{$form.form_claim_day2.html}　（{$form.form_year_month.html}）</td>*}
                <td class="Value">{$form.form_claim_day2.html}</td>
            </tr>
        </table>
        </td>
    </tr>
</table>

<table width="100%">
    <tr>
        <td><font color="#ff0000"><b>※は必須入力です</b></font></td>
        <td align="right">{$form.form_create_button.html}</td>
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
<table width="450" align="center">
    <tr>
        <td>

<table class="List_Table" border="1" width="100%">
    <tr align="center" style="font-weight: bold;">
        <td class="Title_Pink" rowspan="2" width="30">締日</td>
        <td class="Title_Pink" colspan="{$page_data.colspan}">{$var.last_date}</td>
        <td class="Title_Pink" colspan="{$page_data.colspan}">{$var.now_date}</td>
    </tr>
    <tr align="center" style="font-weight: bold;">
        {foreach from=$page_data.cd item=item key=i}
            <td class="Title_Pink">{$page_data[$item].name}</td>
        {/foreach}
        {foreach from=$page_data.cd item=item key=i}
            <td class="Title_Pink">{$page_data[$item].name}</td>
        {/foreach}
    </tr>

    {foreach from=$page_data.close_day item=item key =i}
    <tr class="Result1">
        <td align="right">{$item}</td>
        {foreach from=$page_data.cd item=items key=j}
            {if $page_data[$items].data.$item.last != null}
            <td align="center">{$page_data[$items].data.$item.last}</td>
            {else}
            <td align="center">-</td>
            {/if}
        {/foreach}
        {foreach from=$page_data.cd item=items key=j}
            {if $page_data[$items].data.$item.last != null}
            <td align="center">{$page_data[$items].data.$item.now}</td>
            {else}
            <td align="center">-</td>
            {/if}
        {/foreach}
    </tr> 
    {/foreach}    
</table>
<br>
○：一括作成済　　×：一括作成未　　−：対象締日の請求先無し
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

{$var.html_header}
<script>
{$var.js}
</script>
<body bgcolor="#D8D0C8">
<form {$form.attributes}>

{*+++++++++++++++ ���� begin +++++++++++++++*}
<table width="100%" height="90%" class="M_Table">

    {*+++++++++++++++ �إå��� begin +++++++++++++++*}
    <tr align="center" height="60">
        <td width="100%" colspan="2" valign="top">{$var.page_header}</td>
    </tr>
    {*--------------- �إå��� e n d ---------------*}

    {*+++++++++++++++ ����ƥ���� begin +++++++++++++++*}
    <tr align="center" valign="top">
        <td>
            <table>
                <tr>
                    <td>


{*+++++++++++++++ ��å������� begin +++++++++++++++*}
<span style="color: #ff0000; font-weight: bold; line-height: 130%;">
<ul style="margin-left: 16px;">
    {if $var.error != null}
        <li>{$var.error}<br>
    {/if}
    {if $form.form_client.error != null}
        <li>{$form.form_client.error}<br>
    {/if}
    {if $form.form_designated_date.error != null}
        <li>{$form.form_designated_date.error}<br>
    {/if}
    {if $form.form_order_day.error != null}
        <li>{$form.form_order_day.error}<br>
    {/if}
    {if $form.form_hope_day.error != null}
        <li>{$form.form_hope_day.error}<br>
    {/if}
    {if $form.form_arrival_day.error != null}
        <li>{$form.form_arrival_day.error}<br>
    {/if}
    {if $form.form_ware.error != null}
        <li>{$form.form_ware.error}<br>
    {/if}
    {if $form.form_trade.error != null}
        <li>{$form.form_trade.error}<br>
    {/if}
    {if $form.form_staff.error != null}
        <li>{$form.form_staff.error}<br>
    {/if}
    {if $form.form_note_your.error != null}
        <li>{$form.form_note_your.error}<br>
    {/if}
    {if $form.form_note_your2.error != null}
        <li>{$form.form_note_your2.error}<br>
    {/if}
    {if $form.form_direct.error != null}
        <li>{$form.form_direct.error}<br>
    {/if}
    {if $form.form_order_no.error != null}
        <li>{$form.form_order_no.error}<br>
    {/if}   
    {if $form.form_buy_money.error != null}
        <li>{$form.form_buy_money.error}<br>
    {/if}   
    {foreach from=$goods_err item=item key=i}
        {if $goods_err[$i] != null}
        <li>{$goods_err[$i]}<br>
        {/if}
    {/foreach}
    {foreach from=$price_num_err item=item key=i}
        {if $price_num_err[$i] != null}
        <li>{$price_num_err[$i]}<br>
        {/if}
    {/foreach}
    {foreach from=$num_err item=item key=i}
        {if $num_err[$i] != null}
        <li>{$num_err[$i]}<br>
        {/if}
    {/foreach}
    {foreach from=$price_err item=item key=i}
        {if $price_err[$i] != null}
        <li>{$price_err[$i]}<br>
        {/if}
    {/foreach}
    {foreach from=$duplicate_goods_err item=item key=i}
        <li>{$duplicate_goods_err[$i]}<br>
    {/foreach}
</ul>
</span> 
{if $var.freeze_flg != null}
    {if $var.goods_twice != null}
        <font color="red"><b>{$var.goods_twice}</b></font><br>
    {/if}
    <span style="font: bold;"><font size="+1">�ʲ������Ƥ�ȯ�����ޤ�����</font></span><br>
{/if}
{*--------------- ��å������� e n d ---------------*}

{*+++++++++++++++ ����ɽ���� begin +++++++++++++++*}

{*
{if $var.update_flg == true}
<table class="Data_Table" border="1">
<col width="120" style="font-weight: bold;">
<col>
    <tr>    
        <td class="Title_Blue">ȯ����</td> 
        <td class="VALUE">{$form.form_send_date.html}</td>
    </tr>   
</table>
<br>
{/if}
*}
<table width="100%">
    <tr>
        <td>

<table class="Data_Table" border="1" width="100%">
<col width="120" style="font-weight: bold;">
<col>
<col width="90" style="font-weight: bold;">
<col>
<col width="90" style="font-weight: bold;">
<col>
    <tr>
        <td class="Title_Blue">ȯ���ֹ�</td>
        <td class="Value">{$form.form_order_no.html}</td>
        <td class="Title_Blue">{$form.form_client_link.html}<font color="#ff0000">��</font></td>
        <td class="Value">{$form.form_client.html}</td>
        <td class="Title_Blue">ȯ����<font color="#ff0000">��</font></td>
        <td class="Value">{$form.form_order_day.html}</td>
    </tr>
    <tr>
        <td class="Title_Blue">�����ʬ</a><font color="#ff0000">��</font></td>
        <td class="Value">{$form.form_trade.html}</td>
        <td class="Title_Blue">�вٲ�ǽ��</td>
        <td class="Value">{$form.form_designated_date.html} ����ޤǤ�ȯ���ѿ��Ȱ��������θ����</td>
        <td class="Title_Blue">��˾Ǽ��</td>
        <td class="Value" >{$form.form_hope_day.html}</td>
    </tr>
    <tr>
        <td class="Title_Blue">�����Ҹ�<font color="#ff0000">��</font></td>
        <td class="Value" >{$form.form_ware.html}</td>
        <td class="Title_Blue">ô����<font color="#ff0000">��</font></td>
        <td class="Value">{$form.form_staff.html}</td>
        <td class="Title_Blue">����ͽ����</td>
        <td class="Value" >{$form.form_arrival_day.html}</td>
    </tr>
    <tr>
        <td class="Title_Blue">�����ȼ�</td>
        <td class="Value">{$form.form_trans.html}</td>
		{* rev.1.3 ľ����ƥ��������� *}
        {* <td class="Title_Blue">ľ����</td> *}
        <td class="Title_Blue">{$form.form_direct_link.html}</td>
        {* <td class="Value" colspan="3">{$form.form_direct.html}</td> *}
        <td class="Value" colspan="3">{$form.form_direct_text.cd.html}&nbsp;{$form.form_direct_text.name.html}&nbsp;&nbsp;�����衧{$form.form_direct_text.claim.html}</td>
    </tr>
    <tr>
        <td class="Title_Blue">�̿���<br>�ʻ����谸��</td>
        <td class="Value" colspan="5">{$form.form_note_your.html}</td>
    </tr>
    <tr>
        <td class="Title_Blue">�����̿���<br>�ʻ����谸��</td>
        <td class="Value" colspan="5">{$form.form_note_your2.html}</td>
    </tr>
</table>
        </td>
    </tr>
    <tr>
        <td>
         <span style="font: bold; color: #ff0000;">{$var.warning}</span>
        </td>
    </tr>
</table>
<br>
{*--------------- ����ɽ���� e n d ---------------*}

                    </td>
                </tr>
                <tr>
                    <td>

{*+++++++++++++++ ����ɽ���� begin +++++++++++++++*}
<table width="100%">
    <tr>
        <td>

<table class="List_Table" border="1" width="100%">
    <tr align="center" style="font-weight: bold;">
        <td class="Title_Blue">No.</td>
        <td class="Title_Blue">���ʥ�����<font color="#ff0000">��</font><br>����̾</td>
        <td class="Title_Blue">��ê��<br>(A)</td>
        <td class="Title_Blue">ȯ���ѿ�<br>(B)</td>
        <td class="Title_Blue">������<br>(C)</td>
        <td class="Title_Blue">�вٲ�ǽ��<br>(A+B-C)</td>
        <td class="Title_Blue">���åȻ���</td>
        <td class="Title_Blue">���åȿ�</td>
        <td class="Title_Blue">ȯ����<font color="#ff0000">��</font></td>
        <td class="Title_Blue">����ñ��<font color="#ff0000">��</font></td>
        <td class="Title_Blue">�������</td>
        {if $var.client_search_flg == true && $var.freeze_flg != true}
            <td class="Title_Blue">�Ժ��</b></td>
        {/if}
    </tr>
{$var.html}
</table>
        </td>
    </tr>
    <tr>
        <td>
{$form.hidden}
{if $var.client_search_flg == true}
<table width="100%">
    <tr>
            <td>{$form.form_add_row_button.html}</td>
        <td>
            <table class="List_Table" border="1" align="right" >
                <tr>
                    <td class="Title_Blue" width="80" align="center"><b>��ȴ���</b></td>
                    <td class="Value" width="100" align="right">{$form.form_buy_money.html}</td>
                    <td class="Title_Blue" width="80" align="center"><b>������</b></td>
                    <td class="Value" width="100" align="right">{$form.form_tax_money.html}</td>
                    <td class="Title_Blue" width="80" align="center"><b>�ǹ����</b></td>
                    <td class="Value" width="100" align="right">{$form.form_total_money.html}</td>
                </tr>
            </table>
        </td>
        <td>{$form.form_sum_button.html}</td>
    </tr>
</table>

<A NAME="foot"></A>
<table width="100%">
    <tr>
        <td><b><font color="#ff0000">����ɬ�����ϤǤ�</font></b></td>
        <td align="right">{$form.form_order_button.html}��{$form.form_comp_button.html}��{$form.form_slip_comp_button.html}��{$form.form_back_button.html}</td>
    </tr>
</table>

        </td>
    </tr>
</table>
{/if}
{*--------------- ����ɽ���� e n d ---------------*}

                    </td>
                </tr>
            </table>
        </td>
    </tr>
    {*--------------- ����ƥ���� e n d ---------------*}

</table>
{*--------------- ���� e n d ---------------*}

{$var.html_footer}
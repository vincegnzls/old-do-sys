{* -------------------------------------------------------------------
 * @Program         2-2-209.php.tpl
 * @fnc.Overview    �����ɼ����
 * @author          fukuda
 * @Cng.Tracking    #1: 2007-03-21
 * ---------------------------------------------------------------- *}

{$var.html_header}

<body bgcolor="#D8D0C8">
<form {$form.attributes}>
{$form.hidden}

{*+++++++++++++++ ���� begin +++++++++++++++*}
<table height="90%" class="M_Table">

    {*+++++++++++++++ �إå��� begin +++++++++++++++*}
    <tr align="center" height="60">
        <td colspan="2" valign="top">{$var.page_header}</td>
    </tr>
    {*--------------- �إå��� e n d ---------------*}

    {*+++++++++++++++ ����ƥ���� begin +++++++++++++++*}
    <tr align="center" valign="top">
        <td>
            <table>
                <tr>
                    <td>

{*+++++++++++++++ ��å������� begin +++++++++++++++*}
{* ���顼��å����� *}
<span style="color: #ff0000; font-weight: bold; line-height: 130%;">
{if $form.form_round_staff.error != null}
    <li>{$form.form_round_staff.error}
{/if}
{if $form.form_multi_staff.error != null}
    <li>{$form.form_multi_staff.error}
{/if}
{if $form.form_round_day.error != null}
    <li>{$form.form_round_day.error}
{/if}
{if $form.form_claim_day.error != null}
    <li>{$form.form_claim_day.error}
{/if}
</span>
{*--------------- ��å������� e n d ---------------*}

{*+++++++++++++++ ����ɽ���� begin +++++++++++++++*}
<table>
    <tr>
        <td>{$html.html_s}</td>
    </tr>
</table>
<br>
{*--------------- ����ɽ���� e n d ---------------*}

                    </td>
                </tr>
                <tr style="page-break-before: always;">
                    <td>

{*+++++++++++++++ ����ɽ���� begin +++++++++++++++*}
<table>
    <tr>
        <td>{$html.html_page}</td>
    </tr>
    <tr>
        <td>{$html.html_1}</td>
    </tr>
    <tr>
        <td>{$html.html_2}</td>
    </tr>
    <tr>
        <td>{$html.html_page2}</td>
    </tr>
</table>
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
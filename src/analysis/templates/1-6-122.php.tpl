{$var.html_header}

<body bgcolor="#D8D0C8">
<form name="dateForm" method="post">

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

{*--------------- メッセージ類 e n d ---------------*}

{*+++++++++++++++ 画面表示１ begin +++++++++++++++*}
<table width="600">
    <tr>
        <td>

<table class="Data_Table" border="2" width="100%">
<col width="110" style="font-weight: bold;">
<col>
<col width="110" style="font-weight: bold;">
<col>
    <tr>
        <td class="Title_Gray">出力形式</td>
        <td class="Value">{$form.f_r_output2.html}</td>
        <td class="Title_Gray">取引年月</td>
        <td class="Value">{$form.f_date_d1.html}</td>
    </tr>
    <tr>
        <td class="Title_Gray">仕入先コード</td>
        <td class="Value">{$form.f_text6.html}</td>
        <td class="Title_Gray">仕入先名</td>
        <td class="Value">{$form.f_text15.html}</td>
    </tr>
    <tr>
        <td class="Title_Gray">出力項目</td>
        <td class="Value" colspan="3">{$form.f_check2.html}</td>
    </tr>
</table>

<table align="right">
    <tr>
        <td>{$form.hyouji.html}　　{$form.kuria.html}</td>
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

<table class="List_Table" border="1" width="100%">
<col align="right">
<col span="2">
<col span="14" align="right">
    <tr style="font-weight: bold;">
        <td class="Title_Gray" align="center">No.</td>
        <td class="Title_Gray" align="center">仕入先名</td>
        <td class="Title_Gray" align="center"></td>
        <td class="Title_Gray" align="center">2005年1月</td>
        <td class="Title_Gray" align="center">2005年2月</td>
        <td class="Title_Gray" align="center">2005年3月</td>
        <td class="Title_Gray" align="center">2005年4月</td>
        <td class="Title_Gray" align="center">2005年5月</td>
        <td class="Title_Gray" align="center">2005年6月</td>
        <td class="Title_Gray" align="center">2005年7月</td>
        <td class="Title_Gray" align="center">2005年8月</td>
        <td class="Title_Gray" align="center">2005年9月</td>
        <td class="Title_Gray" align="center">2005年10月</td>
        <td class="Title_Gray" align="center">2005年11月</td>
        <td class="Title_Gray" align="center">2005年12月</td>
        <td class="Title_Gray" align="center">月合計</td>
        <td class="Title_Gray" align="center">月平均</td>
    </tr>
    <tr class="Result1">
        <td>1</td>
        <td>仕入先1</td>
        <td>仕入金額<br>支払額</td>
        <td>10,000<br>8,000</td>
        <td>10,000<br>8,000</td>
        <td>10,000<br>8,000</td>
        <td>10,000<br>8,000</td>
        <td>10,000<br>8,000</td>
        <td>10,000<br>8,000</td>
        <td>10,000<br>8,000</td>
        <td>10,000<br>8,000</td>
        <td>10,000<br>8,000</td>
        <td>10,000<br>8,000</td>
        <td>10,000<br>8,000</td>
        <td>10,000<br>8,000</td>
        <td>120,000<br>96,000</td>
        <td>10,000<br>8,000</td>
    </tr>
    <tr class="Result2">
        <td>2</td>
        <td>仕入先2</td>
        <td>仕入金額<br>支払額</td>
        <td>10,000<br>8,000</td>
        <td>10,000<br>8,000</td>
        <td>10,000<br>8,000</td>
        <td>10,000<br>8,000</td>
        <td>10,000<br>8,000</td>
        <td>10,000<br>8,000</td>
        <td>10,000<br>8,000</td>
        <td>10,000<br>8,000</td>
        <td>10,000<br>8,000</td>
        <td>10,000<br>8,000</td>
        <td>10,000<br>8,000</td>
        <td>10,000<br>8,000</td>
        <td>120,000<br>96,000</td>
        <td>10,000<br>8,000</td>
    </tr>
    <tr class="Result1">
        <td>3</td>
        <td>仕入先3</td>
        <td>仕入金額<br>支払額</td>
        <td>10,000<br>8,000</td>
        <td>10,000<br>8,000</td>
        <td>10,000<br>8,000</td>
        <td>10,000<br>8,000</td>
        <td>10,000<br>8,000</td>
        <td>10,000<br>8,000</td>
        <td>10,000<br>8,000</td>
        <td>10,000<br>8,000</td>
        <td>10,000<br>8,000</td>
        <td>10,000<br>8,000</td>
        <td>10,000<br>8,000</td>
        <td>10,000<br>8,000</td>
        <td>120,000<br>96,000</td>
        <td>10,000<br>8,000</td>
    </tr>
    <tr class="Result3" style="font-weight: bold;">
        <td>合計</td>
        <td>3店舗</td>
        <td>仕入金額<br>支払額</td>
        <td>30,000<br>24,000</td>
        <td>30,000<br>24,000</td>
        <td>30,000<br>24,000</td>
        <td>30,000<br>24,000</td>
        <td>30,000<br>24,000</td>
        <td>30,000<br>24,000</td>
        <td>30,000<br>24,000</td>
        <td>30,000<br>24,000</td>
        <td>30,000<br>24,000</td>
        <td>30,000<br>24,000</td>
        <td>30,000<br>24,000</td>
        <td>30,000<br>24,000</td>
        <td>360,000<br>288,000</td>
        <td>30,000<br>24,000</td>
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

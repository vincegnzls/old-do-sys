<?php /* Smarty version 2.6.9, created on 2006-06-14 10:26:34
         compiled from 1-6-113.php.tpl */ ?>
<?php echo $this->_tpl_vars['var']['html_header']; ?>


<body bgcolor="#D8D0C8">
<form name="dateForm" method="post">

<table width="100%" height="90%" class="M_Table">

        <tr align="center" height="60">
        <td width="100%" colspan="2" valign="top"><?php echo $this->_tpl_vars['var']['page_header']; ?>
</td>
    </tr>
    
        <tr align="center" valign="top">
        <td>
            <table>
                <tr>
                    <td>



<table width="500">
    <tr>
        <td>

<table class="Data_Table" border="1" width="100%">
<col width="80" style="font-weight: bold;">
<col>
<col width="80" style="font-weight: bold;">
<col>
    <tr>
        <td class="Title_Gray">出力形式</td>
        <td class="Value"><?php echo $this->_tpl_vars['form']['f_r_output2']['html']; ?>
</td>
        <td class="Title_Gray">出力金額</td>
        <td class="Value"><?php echo $this->_tpl_vars['form']['f_radio13']['html']; ?>
</td>
    </tr>
    <tr>
        <td class="Title_Gray">取引年月</td>
        <td class="Value" colspan="3"><?php echo $this->_tpl_vars['form']['f_date_d1']['html']; ?>
</td>
    </tr>
    <tr>
        <td class="Title_Gray">業種</td>
        <td class="Value" colspan="3"><?php echo $this->_tpl_vars['form']['form_btype_1']['html']; ?>
</td>
    </tr>
</table>

<table align="right">
    <tr>
        <td><?php echo $this->_tpl_vars['form']['hyouji']['html']; ?>
　　<?php echo $this->_tpl_vars['form']['kuria']['html']; ?>
</td>
    </tr>
</table>

        </td>
    </tr>
</table>
<br>

                    </td>
                </tr>
                <tr>
                    <td>

<table width="100%">
    <tr>
        <td>

<span style="font: bold 15px; color: #555555;">【取引年月：2005-01 〜 2005-12】</span><br>

<table class="List_Table" border="1" width="100%">
<col align="right">
<col>
<col span="4" align="right">
<col align="center">
    <tr style="font-weight: bold;">
        <td class="Title_Gray" align="center">No.</td>
        <td class="Title_Gray" align="center">業種名</td>
        <td class="Title_Gray" align="center">売上金額</td>
        <td class="Title_Gray" align="center">構成比</td>
        <td class="Title_Gray" align="center">累積金額</td>
        <td class="Title_Gray" align="center">累積構成比</td>
        <td class="Title_Gray" align="center">区分</td>
    </tr>
    <tr class="Result1">
        <td>1</td>
        <td>業種5</td>
        <td>3,500</td>
        <td>34.70%</td>
        <td>3,500</td>
        <td>34.70%</td>
        <td rowspan="2">A<br>(0%〜70%)</td>
    </tr>
    <tr class="Result2">
        <td>2</td>
        <td>業種2</td>
        <td>3,210</td>
        <td>31.80%</td>
        <td>6,710</td>
        <td>66.50%</td>
    </tr>
    <tr class="Result1">
        <td>3</td>
        <td>業種1</td>
        <td>1,520</td>
        <td>15.10%</td>
        <td>8,230</td>
        <td>81.60%</td>
        <td rowspan="2">B<br>(70%〜90%)</td>
    </tr>
    <tr class="Result2">
        <td>4</td>
        <td>業種4</td>
        <td>720</td>
        <td>7.10%</td>
        <td>8,950</td>
        <td>88.70%</td>
    </tr>
    <tr class="Result1">
        <td>5</td>
        <td>業種7</td>
        <td>320</td>
        <td>3.20%</td>
        <td>9,270</td>
        <td>91.90%</td>
        <td rowspan="4">C<br>(90%〜100%)</td>
    </tr>
    <tr class="Result2">
        <td>6</td>
        <td>業種3</td>
        <td>260</td>
        <td>2.60%</td>
        <td>9,530</td>
        <td>94.40%</td>
    </tr>
    <tr class="Result1">
        <td>7</td>
        <td>業種6</td>
        <td>210</td>
        <td>2.10%</td>
        <td>9,740</td>
        <td>96.50%</td>
    </tr>
    <tr class="Result2">
        <td>8</td>
        <td>その他</td>
        <td>350</td>
        <td>3.50%</td>
        <td>10,090</td>
        <td>100%</td>
    </tr>
    <tr class="Result3" style="font-weight: bold;">
        <td>合計</td>
        <td>10業種</td>
        <td>10,090</td>
        <td align="center">-</td>
        <td align="center">-</td>
        <td align="center">-</td>
        <td></td>
    </tr>
</table>

        </td>
    </tr>
</table>

                    </td>
                </tr>
            </table>
        </td>
    </tr>
    
</table>

<?php echo $this->_tpl_vars['var']['html_footer']; ?>

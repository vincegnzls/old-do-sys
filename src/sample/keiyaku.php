<?php

$head = "https://210.196.79.250/amenity/src/head";
$fc   = "https://210.196.79.250/amenity/src/franchise";

$link_arr = array(
    system => "�ݸ���"
);

echo <<<HTML_SRC
**************************************************<br>
����<br>
**************************************************<br>
������<br>
<a href="$head/system/1-1-123.php">�������</a><p>
<a href="$head/system/1-1-104.php">������Ͽ</a><p>
<a href="$head/sale/1-2-105.html">�����Ȳ�</a><p>


**************************************************<br>
�ݸ���<br>
**************************************************<br>
������<br>
<a href="$head/system/1-1-131.php">�ݸ�����Ͽ</a><p>
<a href="$head/system/1-1-132.php">�ݸ�������</a><p>
<!--
<a href="$head/system/1-1-133.php">�ݸ����ܺ�</a><p>

<a href="$head/sale/1-2-105.php">�����Ȳ�</a><p>

��FC<br>
<a href="$fc/buy/2-3-201.php">��������</a><p>
-->

**************************************************<br>
��󥿥�TO��󥿥�<br>
**************************************************<br>
������<br>
<a href="$head/system/1-1-141.php">��󥿥�TO��󥿥���Ͽ</a><p>
<a href="$head/system/1-1-142.php">��󥿥�TO��󥿥����</a><p>

��FC<br>
<a href="$fc/system/2-1-141.php">��󥿥�TO��󥿥���Ͽ</a><p>
<a href="$fc/system/2-1-142.php">��󥿥�TO��󥿥����</a><p>


**************************************************<br>
����ޥ����������<br>
**************************************************<br>
��FC<br>
<a href="$fc/system/2-1-114.php">����ޥ����������</a><p>

HTML_SRC;

?>

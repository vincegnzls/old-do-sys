<?php

//環境設定ファイル
require_once("ENV_local.php");
require(FPDF_DIR);

//ラベル出力関数ファイル
require_once(INCLUDE_DIR."function_label.inc");


// DB接続設定
$db_con = Db_Connect();

// 権限チェック
$auth = Auth_Check($db_con);

//ショップIDを抽出
$shop_id = $_POST["form_label_check"];

//ショップごとのラベルに表示するデータを抽出
$label_data = Get_Label_Data($db_con, $shop_id);

if($label_data === false){
    print "<b>";
    print " <font color=#ff0000><li>ラベルを作成する得意先を選択して下さい。</font>";
    print "</b>";

    exit;
}


$pdf=new MBFPDF('P','pt','a4');

//PDF出力
Make_Label_Pdf ($pdf, $label_data);

?>

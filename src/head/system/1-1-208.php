<?php
/*
 * 変更履歴
 * 1.0.0 (2006/04/12) 住所３の追加(suzuki-t)
 *
 * @author		suzuki-t <suzuki-t@bhsk.co.jp>
 *
 * @version		1.0.0 (2006/04/21)
*/

/*
 * 履歴：
 *  日付            B票No.      担当者      内容
 *  -----------------------------------------------------------
 *  2006-12-08      ban_0074    suzuki      登録・変更のログを残すように修正
 *  2007-01-23      仕様変更    watanabe-k  ボタンの色を変更
 *  2007-05-07                  kaku-m      csv出力項目を追加
 *  2010-04-28      Rev.1.5　　 hashimoto-y 非表示機能の追加
 *   2016/01/20                amano  Dialogue, Button_Submit 関数でボタン名が送られない IE11 バグ対応
 *
*/

$page_title = "銀行マスタ";

//環境設定ファイル
require_once("ENV_local.php");

//HTML_QuickFormを作成
$form =& new HTML_QuickForm("dateForm", "POST", "$_SERVER[PHP_SELF]");

//DB接続
$db_con = Db_Connect();

// 権限チェック
$auth       = Auth_Check($db_con);
// 入力・変更権限無しメッセージ
$auth_r_msg = ($auth[0] == "r") ? $auth[3] : null;
// ボタンDisabled
$disabled   = ($auth[0] == "r") ? "disabled" : null;

/****************************/
//外部変数取得
/****************************/
$shop_id  = $_SESSION[client_id];

/* GETしたIDの正当性チェック */
$where = " bank_id IN (SELECT bank_id FROM t_bank WHERE shop_id = 1) ";
if ($_GET["b_bank_id"] != null && Get_Id_Check_Db($db_con, $_GET["b_bank_id"], "b_bank_id", "t_b_bank", "num", $where) != true){
    header("Location: ../top.php");
}

/****************************/
//部品定義
/****************************/
//銀行ID
$select_value = Select_Get($db_con, 'bank');
$form->addElement("select","form_bank_select", "",$select_value, $g_form_option_select);

//支店コード
$form->addElement("text","form_b_bank_cd","テキストフォーム","size=\"3\" maxLength=\"3\" style=\"$g_form_style\"".$g_form_option."\"");

//支店名
$form->addElement("text","form_b_bank_name","テキストフォーム","size=\"34\" maxLength=\"15\"".$g_form_option."\"");
//支店名（フリガナ）
$form->addElement("text","form_b_bank_kana","テキストフォーム","size=\"34\" maxLength=\"15\"".$g_form_option."\"");
//郵便番号
$text[] =& $form->createElement("text","form_post_no1","テキストフォーム","size=\"3\" maxLength=\"3\" style=\"$g_form_style\" onkeyup=\"changeText(this.form,'form_post[form_post_no1]','form_post[form_post_no2]',3)\"".$g_form_option."\"");
$text[] =& $form->createElement("static","","","-");
$text[] =& $form->createElement("text","form_post_no2","テキストフォーム","size=\"4\" maxLength=\"4\" style=\"$g_form_style\"".$g_form_option."\"");
$form->addGroup( $text, "form_post", "form_post");
//住所１
$form->addElement("text","form_address1","テキストフォーム","size=\"34\" maxLength=\"15\"".$g_form_option."\"");
//住所２
$form->addElement("text","form_address2","テキストフォーム","size=\"34\" maxLength=\"15\"".$g_form_option."\"");
//住所３
$form->addElement("text","form_address3","テキストフォーム","size=\"34\" maxLength=\"15\"".$g_form_option."\"");
//TEL
$form->addElement("text","form_tel","テキストフォーム","size=\"15\" maxLength=\"13\" style=\"$g_form_style\"".$g_form_option."\"");
//FAX
$form->addElement("text","form_fax","テキストフォーム","size=\"15\" maxLength=\"13\" style=\"$g_form_style\" ".$g_form_option."\"");
//口座種別
//$form->addElement("text","form_account_kind","テキストフォーム","size=\"5\" maxLength=\"5\"".$g_form_option."\"");
//口座番号
//$form->addElement("text","form_account_no","テキストフォーム","size=\"8\" maxLength=\"7\" style=\"$g_form_style\"".$g_form_option."\"");

#2010-04-28 hashimoto-y
$form->addElement('checkbox', 'form_nondisp_flg', '', '');

//備考
$form->addElement("text","form_note","テキストフォーム","size=\"34\" maxLength=\"30\"".$g_form_option."\"");
//自動入力
$form->addElement("button","input_button","自動入力","onClick=\"javascript:Button_Submit('input_button_flg','#','true', this)\"");
//クリア
$form->addElement("button","clear_button","クリア","onClick=\"javascript:location.href('$_SERVER[PHP_SELF]');\"");
//登録
$form->addElement("submit","entry_button","登　録","onClick=\"javascript:return Dialogue('登録します。','#', this)\" $disabled");
//CSV出力
$form->addElement("button","csv_button","CSV出力","onClick=\"javascript:Button_Submit('csv_button_flg','#','true', this)\"");

$form->addElement("button","bank_button","銀行登録画面","onClick=\"javascript:Referer('1-1-207.php')\"");
$form->addElement("button","bank_mine_button","支店登録画面",$g_button_color." onClick=\"location.href='$_SERVER[PHP_SELF]'\"");
$form->addElement("button","bank_account_button","口座登録画面","onClick=\"javascript:Referer('1-1-210.php')\"");


//更新リンク押下判定フラグ
$form->addElement("hidden", "update_flg");
//自動入力ボタン押下判定フラグ
$form->addElement("hidden", "input_button_flg");
//CSV出力ボタン押下判定フラグ
$form->addElement("hidden", "csv_button_flg");

/******************************/
//自動入力ボタン押下処理
/*****************************/
if($_POST["input_button_flg"]==true){
    $post1     = $_POST["form_post"]["form_post_no1"];             //郵便番号１
    $post2     = $_POST["form_post"]["form_post_no2"];             //郵便番号２
    //郵便番号から値取得
    $post_value = Post_Get($post1,$post2,$db_con);
    $cons_data["form_post"]["form_post_no1"]     = $post1;
    $cons_data["form_post"]["form_post_no2"]     = $post2;
    $cons_data["form_address1"]                  = $post_value[1];  //住所1
    $cons_data["form_address2"]                  = $post_value[2];  //住所2
    //郵便番号フラグをクリア
    $cons_data["input_button_flg"] = "";
    $form->setConstants($cons_data);
}

/******************************/
//CSV出力ボタン押下処理
/*****************************/
if($_POST["csv_button_flg"]==true && $_POST["entry_button"] != "登　録" && $_POST["input_button_flg"]!=true){
    /** CSV作成SQL **/
    $sql = "SELECT ";
    $sql .= "   t_bank.bank_cd,";               //銀行コード
    $sql .= "   t_bank.bank_name,";             //銀行名
    $sql .= "   t_bank.bank_cname,";            //銀行名（略称）
    $sql .= "   t_b_bank.b_bank_cd,";           //支店コード
    $sql .= "   t_b_bank.b_bank_name,";         //支店名
    $sql .= "   t_b_bank.b_bank_kana,";         //支店名（フリガナ）
    $sql .= "   t_b_bank.post_no1,";            //郵便番号1
    $sql .= "   t_b_bank.post_no2,";            //郵便番号2
    $sql .= "   t_b_bank.address1,";            //住所1
    $sql .= "   t_b_bank.address2,";            //住所2
    $sql .= "   t_b_bank.address3,";            //住所3
    $sql .= "   t_b_bank.tel,";                 //TEL
    $sql .= "   t_b_bank.fax,";                 //FAX
    #2010-04-28 hashimoto-y
    $sql .= "   CASE t_b_bank.nondisp_flg";     //非表示
    $sql .= "   WHEN true  THEN '○'";
    $sql .= "   WHEN false THEN ''";
    $sql .= "   END,";

    $sql .= "   t_b_bank.note ";                //備考
    $sql .= "FROM ";
    $sql .= "   t_bank";
    $sql .= "   LEFT JOIN";
    $sql .= "   t_b_bank ";
    $sql .= "   ON t_bank.bank_id = t_b_bank.bank_id";
    $sql .= " WHERE ";
    $sql .= "   t_bank.shop_id = $shop_id ";
    $sql .= "ORDER BY t_bank.bank_cd, t_b_bank.b_bank_cd;";

    $result = Db_Query($db_con,$sql);

    //CSVデータ取得
    $i=0;
    while($data_list = pg_fetch_array($result)){
        //銀行コード
        $bank_data[$i][0] = $data_list[0];
        //銀行名
        $bank_data[$i][1] = $data_list[1];
        //銀行名（フリガナ）
        $bank_data[$i][2] = $data_list[2];
        //略称
        $bank_data[$i][3] = $data_list[3];
        //支店コード
        $bank_data[$i][4] = $data_list[4];
        //支店名
        $bank_data[$i][5] = $data_list[5];
        //郵便番号1-郵便番号2(両方or片方が未入力の場合はnull表示)
        if($data_list[6] != null && $data_list[6] != null){
            $bank_data[$i][6] = $data_list[6]."-".$data_list[7];
        }else{
            $bank_data[$i][6] = "";
        }
        //住所1
        $bank_data[$i][7] = $data_list[8];
        //住所２
        $bank_data[$i][8] = $data_list[9];
        //住所３
        $bank_data[$i][9] = $data_list[10];
            //TEL
        $bank_data[$i][10] = $data_list[11];
        //FAX
        $bank_data[$i][11] = $data_list[12];

        #2010-04-28 hashimoto-y
        #//備考
        #$bank_data[$i][12] = $data_list[13];
        //非表示
        $bank_data[$i][12] = $data_list[13];
        //備考
        $bank_data[$i][13] = $data_list[14];
        $i++;
    }

    //CSVファイル名
    $csv_file_name = "支店マスタ".date("Ymd").".csv";
    //CSVヘッダ作成
    $csv_header = array(
        "銀行コード", 
        "銀行名",
        "略称",
        "支店コード",
        "支店名", 
        "支店名（フリガナ）", 
        "郵便番号",
        "住所１",
        "住所２",
        "住所３",
        "TEL",
        "FAX",
        #2010-04-28 hashimoto-y
        "非表示",
        "備考"
    );
    $csv_file_name = mb_convert_encoding($csv_file_name, "SJIS", "EUC");
    $csv_data = Make_Csv($bank_data, $csv_header);
    Header("Content-disposition: attachment; filename=$csv_file_name");
    Header("Content-type: application/octet-stream; name=$csv_file_name");
    print $csv_data;
    exit;
}

/****************************/
//変更処理（リンク）
/****************************/

//変更リンク押下判定
if($_GET["b_bank_id"] != ""){

    //変更するレンタルIDを取得
    $update_num = $_GET["b_bank_id"];

    /** 銀行マスタ取得SQL作成 **/
    $sql = "SELECT ";
    $sql .= "   bank_id,";
    $sql .= "   b_bank_cd,";           //支店コード
    $sql .= "   b_bank_name,";         //支店名
    $sql .= "   b_bank_kana,";         //支店名（フリガナ）
    $sql .= "   post_no1,";            //郵便番号1
    $sql .= "   post_no2,";            //郵便番号2
    $sql .= "   address1,";            //住所1
    $sql .= "   address2,";            //住所2
    $sql .= "   address3,";            //住所3
    $sql .= "   tel,";                 //TEL
    $sql .= "   fax,";                 //FAX
//    $sql .= "   account_kind,";        //口座種別
//    $sql .= "   account_no,";          //口座番号

    #2010-04-28 hashimoto-y
    $sql .= "nondisp_flg,";         //非表示

    $sql .= "   note ";                //備考
    $sql .= " FROM ";
    $sql .= "   t_b_bank ";
    $sql .= " WHERE ";
    $sql .= "   b_bank_id = ".$update_num.";";
    $result = Db_Query($db_con,$sql);
    //GETデータ判定
    Get_Id_Check($result);
    $data_list = pg_fetch_array($result,0);

    //フォームに値を復元
    $def_fdata["form_bank_select"]                  =    $data_list[0];  
    $def_fdata["form_b_bank_cd"]                    =    $data_list[1];  
    $def_fdata["form_b_bank_name"]                  =    $data_list[2];  
    $def_fdata["form_b_bank_kana"]                  =    $data_list[3];  
    $def_fdata["form_post"]["form_post_no1"]        =    $data_list[4];  
    $def_fdata["form_post"]["form_post_no2"]        =    $data_list[5];  
    $def_fdata["form_address1"]                     =    $data_list[6];  
    $def_fdata["form_address2"]                     =    $data_list[7];  
    $def_fdata["form_address3"]                     =    $data_list[8];  
    $def_fdata["form_tel"]                          =    $data_list[9];  
    $def_fdata["form_fax"]                          =    $data_list[10];  
//    $def_fdata["form_account_kind"]                 =    $data_list[10]; 
//    $def_fdata["form_account_no"]                   =    $data_list[11]; 
    #2010-04-28 hashimoto-y
    #$def_fdata["form_note"]                         =    $data_list[11]; 
    $def_fdata["form_nondisp_flg"]                  =    ($data_list[11] == 't')? 1 : 0;
    $def_fdata["form_note"]                         =    $data_list[12]; 
    $def_fdata["update_flg"]                        =    true; 
    
    $form->setDefaults($def_fdata);
}

/****************************/
//エラーチェック(AddRule)
/****************************/
//銀行名
$form->addRule('form_bank_select','銀行は必須項目です。','required');

//◇支店コード
//・必須チェック
$form->addRule('form_b_bank_cd','支店コード は半角数字のみです。','required');
//・文字種チェック
$form->addRule('form_b_bank_cd','支店コード は半角数字のみです。',"regex", "/^[0-9]+$/");

//◇支店名
//・必須チェック
$form->addRule('form_b_bank_name','支店名 は1文字以上15文字以下です。','required');
// 全角/半角スペースのみチェック
$form->registerRule("no_sp_name", "function", "No_Sp_Name");
$form->addRule("form_b_bank_name", "支店名 にスペースのみの登録はできません。", "no_sp_name");

//◇支店名（）
//・必須チェック
$form->addRule('form_b_bank_kana','支店名（フリガナ） は1文字以上15文字以下です。','required');
// 全角/半角スペースのみチェック
$form->registerRule("no_sp_name", "function", "No_Sp_Name");
$form->addRule("form_b_bank_kana", "支店名（フリガナ） にスペースのみの登録はできません。", "no_sp_name");

//◇郵便番号
//・文字数チェック
//・文字種チェック
$form->addGroupRule('form_post', array(
    'form_post_no1' => array(
        array('郵便番号は半角数字の7桁です。','rangelength',array(3,3)),
        array('郵便番号は半角数字の7桁です。',"regex", "/^[0-9]+$/")
    ),
    'form_post_no2' => array(
        array('郵便番号は半角数字の7桁です。','rangelength',array(4,4)),
        array('郵便番号は半角数字の7桁です。',"regex", "/^[0-9]+$/"),
    )
));

//◇口座番号
//・文字種チェック
//$form->addRule('form_account_no','口座番号 は半角数字のみです。',"regex", "/^[0-9]+$/");

//◇TEL
//・文字種チェック
$form->addRule("form_tel", "TELは半角数字と「-」のみ13桁です。", "regex", "/^[0-9-]+$/");

//◇FAX
//・文字種チェック
$form->addRule("form_fax", "FAXは半角数字と「-」のみ13桁です。", "regex", "/^[0-9-]+$/");

/****************************/
//登録処理
/****************************/
if($_POST["entry_button"] == "登　録"){
    //入力フォーム値取得
    $bank_id      = $_POST["form_bank_select"];             //銀行ID
    $b_bank_cd    = $_POST["form_b_bank_cd"];               //支店コード
    $b_bank_name  = $_POST["form_b_bank_name"];             //支店名
    $b_bank_kana  = $_POST["form_b_bank_kana"];             //支店名（フリガナ）
    $post1        = $_POST["form_post"]["form_post_no1"];   //郵便番号1
    $post2        = $_POST["form_post"]["form_post_no2"];   //郵便番号2
    $address1     = $_POST["form_address1"];                //住所1
    $address2     = $_POST["form_address2"];                //住所2
    $address3     = $_POST["form_address3"];                //住所3
    $tel          = $_POST["form_tel"];                     //TEL
    $fax          = $_POST["form_fax"];                     //FAX
//    $account_kind = $_POST["form_account_kind"];            //口座種別 
//    $account_no   = $_POST["form_account_no"];              //口座番号 

    #2010-04-28 hashimoto-y
    $nondisp_flg  = ($_POST["form_nondisp_flg"] == '1')? 't' : 'f';             //非表示

    $note         = $_POST["form_note"];                    //備考 
    $update_flg   = $_POST["update_flg"];                   //登録・更新判定
    $update_num   = $_GET["b_bank_id"];                       //銀行ID


    /****************************/
    //エラーチェック(PHP)
    /****************************/
    //◇呼出コード
    //・重複チェック
    if($b_bank_cd != null && $bank_id != null){
        //呼出コードに０を埋める
        $b_bank_cd = str_pad($b_bank_cd, 3, 0, STR_POS_LEFT);
        //入力したコードがマスタに存在するかチェック
        $sql  = "SELECT ";
        $sql .= "   b_bank_cd "; 
        $sql .= " FROM ";
        $sql .= "   t_b_bank ";
        $sql .= " WHERE ";
        $sql .= "   bank_id = $bank_id";
        $sql .= "   AND";
        $sql .= "   b_bank_cd = '$b_bank_cd' ";

        //変更の場合は、自分のデータ以外を参照する
        if($update_num != null && $update_flg == true){
            $sql .= " AND NOT ";
            $sql .= "b_bank_id = '$update_num'";
        }
        $sql .= ";";
        $result = Db_Query($db_con, $sql);
        $row_count = pg_num_rows($result);
        if($row_count != 0){
            $form->setElementError("form_b_bank_cd","既に使用されている 支店コード です。");
        }
    }

    //■支店名（フリガナ）
    //・特定の文字だけ使用可能
    if (!mb_ereg("^[0-9A-Zｱ-ﾝﾞﾟｰ･ ]+$", $b_bank_kana)){
        $form->setElementError("form_b_bank_kana", "支店名（フリガナ） は、半角ｶﾅ（大文字）と半角英数字（大文字）のみ使用可能です。");
    }


    //エラーの際には、登録処理を行わない
    if($form->validate()){
        
        Db_Query($db_con, "BEGIN;");

        //更新か登録か判定
        if($update_flg == true){
            //作業区分は更新
            $work_div = '2';
            //変更完了メッセージ
            $comp_msg = "変更しました。";

            $sql  = "UPDATE \n";
            $sql .= "t_b_bank \n";
            $sql .= "SET \n";
            $sql .= "bank_id = '$bank_id',\n";
            $sql .= "b_bank_cd = '$b_bank_cd',\n";
            $sql .= "b_bank_name = '$b_bank_name',\n";
            $sql .= "b_bank_kana = '$b_bank_kana',\n";
            $sql .= "post_no1 = '$post1',\n";
            $sql .= "post_no2 = '$post2',\n";
            $sql .= "address1 = '$address1',\n";
            $sql .= "address2 = '$address2',\n";
            $sql .= "address3 = '$address3',\n";
            $sql .= "tel = '$tel',\n";
            $sql .= "fax = '$fax',\n";
//            $sql .= "account_kind = '$account_kind',";
//            $sql .= "account_no = '$account_no',";
            #2010-04-28 hashimoto-y
            $sql .= "nondisp_flg = '$nondisp_flg',";
            $sql .= "note = '$note' \n";
            $sql .= "WHERE \n";
            $sql .= "b_bank_id = $update_num;\n";
        }else{
            //作業区分は登録
            $work_div = '1';
            //登録完了メッセージ
            $comp_msg = "登録しました。";

            $sql  = "INSERT INTO t_b_bank (\n";
            $sql .= "   b_bank_id,\n";
            $sql .= "   bank_id,\n";
            $sql .= "   b_bank_cd,\n";
            $sql .= "   b_bank_name,\n";
            $sql .= "   b_bank_kana,\n";
//            $sql .= "   account_kind,";
//            $sql .= "   account_no,";
            $sql .= "   post_no1,\n";
            $sql .= "   post_no2,\n";
            $sql .= "   address1,\n";
            $sql .= "   address2,\n";
            $sql .= "   address3,\n";
            $sql .= "   tel,\n";
            $sql .= "   fax,\n";
            #2010-04-28 hashimoto-y
            $sql .= "   nondisp_flg,";
            $sql .= "   note\n";
            $sql .= ")VALUES(\n";
            $sql .= "(SELECT COALESCE(MAX(b_bank_id), 0)+1 FROM t_b_bank),\n";
            $sql .= "   $bank_id,\n";
            $sql .= "   '$b_bank_cd',\n";
            $sql .= "   '$b_bank_name',\n";
            $sql .= "   '$b_bank_kana',\n";
//            $sql .= "   '$account_kind',";
//            $sql .= "   '$account_no',";
            $sql .= "   '$post1',\n";
            $sql .= "   '$post2',\n";
            $sql .= "   '$address1',\n";
            $sql .= "   '$address2',\n";
            $sql .= "   '$address3',\n";
            $sql .= "   '$tel',\n";
            $sql .= "   '$fax',\n";
            #2010-04-28 hashimoto-y
            $sql .= "   '$nondisp_flg',";
            $sql .= "   '$note'\n";
            $sql .= ");";
        }

        $result = Db_Query($db_con,$sql);
        if($result == false){
            Db_Query($db_con,"ROLLBACK;");
            exit;
        }
        //銀行マスタの値をログに書き込む
        $result = Log_Save($db_con,'b_bank',$work_div,$b_bank_cd,$b_bank_name);
        //ログ登録時にエラーになった場合
        if($result == false){
            Db_Query($db_con,"ROLLBACK;");
            exit;
        }
        Db_Query($db_con, "COMMIT;");

        //フォームの値を初期化
        $def_fdata["form_bank_select"]                        =    "";
        $def_fdata["form_b_bank_cd"]                       =    "";
        $def_fdata["form_b_bank_name"]                      =    "";
        $def_fdata["form_b_bank_kana"]                      =    "";
        $def_fdata["form_bank_cname"]                     =    "";
        $def_fdata["form_post"]["form_post_no1"]          =    "";
        $def_fdata["form_post"]["form_post_no2"]          =    "";
        $def_fdata["form_address1"]                       =    "";
        $def_fdata["form_address2"]                       =    "";
        $def_fdata["form_address3"]                       =    "";
        $def_fdata["form_tel"]                            =    "";
        $def_fdata["form_fax"]                            =    "";
//        $def_fdata["form_account_kind"]                   =    "";
//        $def_fdata["form_account_no"]                     =    "";
        #2010-04-28 hashimoto-y
        $def_fdata["form_nondisp_flg"]                    =    "";
        $def_fdata["form_note"]                           =    "";
        $def_fdata["update_flg"]                          =    "";

        $form->setConstants($def_fdata);
    }
}

/******************************/
//ヘッダーに表示させる全件数
/*****************************/
/** 銀行マスタ取得SQL作成 **/
$sql  = "SELECT ";
$sql .= "   t_bank.bank_cd,";           //銀行コード
$sql .= "   t_bank.bank_name,";         //銀行名
$sql .= "   t_bank.bank_cname,";        //略称
$sql .= "   t_b_bank.b_bank_id,";       //支店ID
$sql .= "   t_b_bank.b_bank_cd,";       //支店コード
$sql .= "   t_b_bank.b_bank_name, ";    //支店名
$sql .= "   t_b_bank.b_bank_kana, ";    //支店名
//$sql .= "   t_b_bank.account_kind,";
//$sql .= "   t_b_bank.account_no,";
#2010-04-28 hashimoto-y
$sql .= "   CASE t_b_bank.nondisp_flg"; //非表示
$sql .= "   WHEN true  THEN '○'";
$sql .= "   WHEN false THEN ''";
$sql .= "   END,";

$sql .= "   t_b_bank.note";
$sql .= " FROM ";
$sql .= "   t_bank";
$sql .= "   LEFT JOIN";
$sql .= "   t_b_bank ";
$sql .= "   ON t_bank.bank_id = t_b_bank.bank_id";
$sql .= " WHERE ";
$sql .= "   t_bank.shop_id = $shop_id ";
$sql .= "   ORDER BY t_bank.bank_cd, t_b_bank.b_bank_cd;";

$result = Db_Query($db_con,$sql);
//全件数取得(ヘッダー)
$total_count = pg_num_rows($result);

//行データ部品を作成
$row = Get_Data($result);

for($i = 0; $i < $total_count; $i++){
    for($j = 0; $j < $total_count; $j++){
        if($i != $j && $row[$i][0] == $row[$j][0]){
            $row[$j][0] = null;
            $row[$j][1] = null;
            $row[$j][2] = null;
        }
    }
}

for($i = 0; $i < $total_count; $i++){
    if($row[$i][0] == null){
        $tr[$i] = $tr[$i-1];
    }else{  
        if($tr[$i-1] == "Result1"){
            $tr[$i] = "Result2";
        }else{  
            $tr[$i] = "Result1";
        }       
    }
}


/****************************/
//HTMLヘッダ
/****************************/
$html_header = Html_Header($page_title);

/****************************/
//HTMLフッタ
/****************************/
$html_footer = Html_Footer();

/****************************/
//メニュー作成
/****************************/
$page_menu = Create_Menu_h('system','1');

/****************************/
//画面ヘッダー作成
/****************************/
$page_title .= "(全".$total_count."件)";
$page_title .= "　".$form->_elements[$form->_elementIndex[bank_button]]->toHtml();
$page_title .= "　".$form->_elements[$form->_elementIndex[bank_mine_button]]->toHtml();
$page_title .= "　".$form->_elements[$form->_elementIndex[bank_account_button]]->toHtml();

$page_header = Create_Header($page_title);


// Render関連の設定
$renderer =& new HTML_QuickForm_Renderer_ArraySmarty($smarty);
$form->accept($renderer);

//form関連の変数をassign
$smarty->assign('form',$renderer->toArray());

//その他の変数をassign
$smarty->assign('var',array(
    'html_header'   => "$html_header",
    'page_menu'     => "$page_menu",
    'page_header'   => "$page_header",
    'html_footer'   => "$html_footer",
    'total_count'   => "$total_count",
    'comp_msg'      => "$comp_msg",
    'auth_r_msg'    => "$auth_r_msg",
));
$smarty->assign('row',$row);
$smarty->assign('tr',$tr);

//テンプレートへ値を渡す
$smarty->display(basename($_SERVER[PHP_SELF] .".tpl"));

?>

<?php
/*在庫テーブルのデータ更新*/

/******************************
 *  変更履歴
 *      ・（2009-08-18）新規作成 <hashimoto-y>
 *      ・（2010-01-21）発注残・引当以外のデータが無いと重複したレコードが出来る不具合修正 <hashimoto-y>
 *
*******************************/

//環境設定ファイル
require_once("ENV_local.php");

//DBに接続
$conn = Db_Connect();

//開始
$today = date("Y-m-d H:i:s");
error_log("$today 在庫テーブル更新処理開始 \n",3,LOG_FILE);

//エラー変数初期化
$error_con  = NULL;
$error_time = NULL;
$error_msg  = NULL;
$e_contents = NULL;
$error_flg  = NULL;

/**************************/
//更新するデータを抽出
/**************************/
$today = date("Y-m-d");

Db_Query($conn,"BEGIN");

Db_Query($conn,"LOCK TABLE t_stock IN ACCESS EXCLUSIVE MODE");

Db_Query($conn,"TRUNCATE t_stock");

$t_stock_renew_sql = <<<_EOD_

INSERT INTO t_stock
   SELECT
        CASE
            WHEN t_stock.goods_id IS NOT NULL THEN t_stock.goods_id
            WHEN t_stock.goods_id IS NULL AND t_allowance1.goods_id IS NOT NULL THEN t_allowance1.goods_id
            ELSE NULL::integer
        END AS goods_id,
        CASE
            WHEN t_stock.ware_id IS NOT NULL THEN t_stock.ware_id
            WHEN t_stock.ware_id IS NULL AND t_allowance1.ware_id IS NOT NULL THEN t_allowance1.ware_id
            ELSE NULL::integer
        END AS ware_id,
        COALESCE(t_stock.stock_num, 0::bigint) AS stock_num,
        COALESCE(t_stock.rstock_num, 0::bigint) AS rstock_num,
        COALESCE(t_allowance1.allowance_num, 0::bigint) AS rorder_num,
        CASE
            WHEN t_stock.shop_id IS NOT NULL THEN t_stock.shop_id
            WHEN t_stock.shop_id IS NULL AND t_allowance1.shop_id IS NOT NULL THEN t_allowance1.shop_id
            ELSE NULL::integer
        END AS shop_id


   FROM

   (
      SELECT
           CASE
               WHEN t_stock.goods_id IS NOT NULL THEN t_stock.goods_id
               WHEN t_stock.goods_id IS NULL AND t_allowance.goods_id IS NOT NULL THEN t_allowance.goods_id
               ELSE NULL::integer
           END AS goods_id,
           CASE
               WHEN t_stock.ware_id IS NOT NULL THEN t_stock.ware_id
               WHEN t_stock.ware_id IS NULL AND t_allowance.ware_id IS NOT NULL THEN t_allowance.ware_id
               ELSE NULL::integer
           END AS ware_id,
           COALESCE(t_stock.stock_num, 0::bigint) AS stock_num,
           COALESCE(t_allowance.allowance_num, 0::bigint) AS rstock_num,
           CASE
               WHEN t_stock.shop_id IS NOT NULL THEN t_stock.shop_id
               WHEN t_stock.shop_id IS NULL AND t_allowance.shop_id IS NOT NULL THEN t_allowance.shop_id
               ELSE NULL::integer
           END AS shop_id,

           ( CASE
               WHEN t_stock.goods_id IS NOT NULL THEN t_stock.goods_id
               WHEN t_stock.goods_id IS NULL AND t_allowance.goods_id IS NOT NULL THEN t_allowance.goods_id
               ELSE NULL::integer
           END ) || '-' ||
           ( CASE
               WHEN t_stock.ware_id IS NOT NULL THEN t_stock.ware_id
               WHEN t_stock.ware_id IS NULL AND t_allowance.ware_id IS NOT NULL THEN t_allowance.ware_id
               ELSE NULL::integer
           END ) || '-' ||
           ( CASE
               WHEN t_stock.shop_id IS NOT NULL THEN t_stock.shop_id
               WHEN t_stock.shop_id IS NULL AND t_allowance.shop_id IS NOT NULL THEN t_allowance.shop_id
               ELSE NULL::integer
           END ) AS stock_cd

      FROM ( SELECT t_stock_hand.goods_id, t_stock_hand.ware_id, sum(t_stock_hand.num *
                   CASE t_stock_hand.io_div
                       WHEN 1 THEN 1
                       WHEN 2 THEN -1
                       ELSE NULL::integer
                   END) AS stock_num, t_stock_hand.shop_id, (((t_stock_hand.goods_id::text || '-'::text) || t_stock_hand.ware_id::text) || '-'::text) || t_stock_hand.shop_id::text AS stock_cd
              FROM t_stock_hand
             WHERE t_stock_hand.work_div::text <> 1::text AND t_stock_hand.work_div::text <> 3::text AND t_stock_hand.work_day <= now()
             GROUP BY t_stock_hand.goods_id, t_stock_hand.ware_id, t_stock_hand.shop_id) t_stock
      FULL JOIN ( SELECT t_stock_hand.goods_id, t_stock_hand.ware_id, sum(t_stock_hand.num *
                   CASE t_stock_hand.io_div
                       WHEN 1 THEN -1
                       WHEN 2 THEN 1
                       ELSE NULL::integer
                   END) AS allowance_num, t_stock_hand.shop_id, (((t_stock_hand.goods_id::text || '-'::text) || t_stock_hand.ware_id::text) || '-'::text) || t_stock_hand.shop_id::text AS stock_cd
              FROM t_stock_hand
             WHERE t_stock_hand.work_div::text = 1::text AND t_stock_hand.work_day <= now()
             GROUP BY t_stock_hand.goods_id, t_stock_hand.ware_id, t_stock_hand.shop_id) t_allowance ON t_stock.stock_cd = t_allowance.stock_cd
   ) AS t_stock


   FULL JOIN ( SELECT t_stock_hand.goods_id, t_stock_hand.ware_id, sum(t_stock_hand.num *
           CASE t_stock_hand.io_div
               WHEN 1 THEN -1
               WHEN 2 THEN 1
               ELSE NULL::integer
           END) AS allowance_num, t_stock_hand.shop_id, (((t_stock_hand.goods_id::text || '-'::text) || t_stock_hand.ware_id::text) || '-'::text) || t_stock_hand.shop_id::text AS stock_cd
      FROM t_stock_hand
     WHERE t_stock_hand.work_div::text = 3::text AND t_stock_hand.work_day <= now()
     GROUP BY t_stock_hand.goods_id, t_stock_hand.ware_id, t_stock_hand.shop_id) t_allowance1 ON t_stock.stock_cd = t_allowance1.stock_cd
;


_EOD_;


$result = Db_Query($conn, $t_stock_renew_sql);
if($result === false){
	$error_con = pg_last_error();
	$error_time = date("Y-m-d H:i");
	$error_msg[0] = "$error_time ".__FILE__." \n".__LINE__."行目 在庫テーブルの更新失敗 \n ";
	$error_msg[1] = "$error_con \n\n";
}


//正常判定
if($error_msg == NULL){
	//正常

	Db_Query($conn,"COMMIT");
	$today = date("Y-m-d H:i:s");
	//FILEに出力
	error_log("$today 在庫テーブル更新完了 \n\n",3,LOG_FILE);
}else{
	//異常
	Db_Query($conn, "ROLLBACK");

	//FILEにエラー出力
	$estr = NULL;
	for($i=0;$i<count($error_msg);$i++){
		error_log($error_msg[$i],3,LOG_FILE);
		$e_contents .= $error_msg[$i];
	}
	//メールでエラー通知
	Error_send_mail($g_error_mail,$g_error_add,"在庫テーブル更新処理で異常発生",$e_contents);
}


?>


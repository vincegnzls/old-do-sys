<?php
//�����ȥ�
$page_title = "DB��Ͽ";


if($_POST["submit"] == "�¹�"){


    //�Ķ�����ե�����
    require_once("ENV_local.php");
    require_once("file.fnc");

    //DB��³
    $db_con = Db_Connect();

    $file_name = "/usr/local/apache2/htdocs/amenity/src/sample/updata/amenity_product_history.csv";

    $file = fopen($file_name, "r");

    Db_Query($db_con, "BEGIN;");

    //�ԥ롼��
    $j = 1;
    while($file_data = fgets($file, "1000") ){

        $insert_data = explode(',',$file_data);

        $g_goods_id       = addslashes(trim($insert_data[0]));
        $his_g_goods_cd   = addslashes(trim($insert_data[1]));
        $his_g_goods_name = addslashes(trim($insert_data[2]));

        $sql  = "UPDATE\n";
        $sql .= "   t_g_goods \n";
        $sql .= "SET\n";
        $sql .= "   his_g_goods_cd   = '$his_g_goods_cd ', \n";
        $sql .= "   his_g_goods_name = '$his_g_goods_name' \n";
        $sql .= "WHERE\n";
        $sql .= "   g_goods_id = $g_goods_id\n";
        $sql .= ";\n";

        $result = Db_Query($db_con, $sql);
        if($result === false){
            Db_Query($db_con, "ROLLBACK;");
            exit;
        }else{
            print "OK <br>";
        }
    }
    Db_Query($db_con, "COMMIT;");
}

?>

<html>
<head>
<title></title>
</head>
<body>
    <form action="./his_product.php" method="post">
    <input type="submit" name="submit" value="�¹�">
    </form>
</body>
</html>
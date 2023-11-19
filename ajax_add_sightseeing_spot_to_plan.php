<?php
session_start();
require_once("connect_database.php");

$post_data_1 = $_POST['post_data_1'];
$post_data_2 = json_decode($_POST['post_data_2'], true);
$post_data_3 = $_POST['post_data_3'];

$database_name = "minatomirai_sightseeing_spots";

try{

    $stmt1 = $pdo->prepare("SELECT * FROM $database_name where id = :id");
    $stmt1 -> bindParam(":id", $post_data_1, PDO::PARAM_INT);
    $stmt1 -> execute();
    $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);

}catch(PDOException $e){
    //デバッグ用
    echo $e->getMessage();
}

switch ($post_data_2) {
    case "1":
        //セッション変数を配列にする
        //スポットは同じ名前、3個以上登録できないようにする設定
        if (isset($_SESSION["s_l_spots"])) {
            if (!(in_array($post_data_1, array_column( $_SESSION["s_l_spots"], 0)))) {
                if (count($_SESSION["s_l_spots"]) < 3) {
                    $_SESSION["s_l_spots"][] = [$post_data_1, $post_data_3, $result1["name"]];
                    $spotname = $result1["name"];
                } else {
                    $spotname = "3";
                }
            } else {
                $spotname = "";
            }
        } else {
            $_SESSION["s_l_spots"][] = [$post_data_1, $post_data_3, $result1["name"]];
            $spotname = $result1["name"];
        }
        break;
    case "2":
        if (isset($_SESSION["l_d_spots"])) {
            if (!(in_array($post_data_1, array_column( $_SESSION["l_d_spots"], 0)))) {
                if (count($_SESSION["l_d_spots"]) < 3) {
                    $_SESSION["l_d_spots"][] = [$post_data_1, $post_data_3, $result1["name"]];
                    $spotname = $result1["name"];
                } else {
                    $spotname = "3";
                }
            } else {
                $spotname = "";
            }
        } else {
            $_SESSION["l_d_spots"][] = [$post_data_1, $post_data_3, $result1["name"]];
            $spotname = $result1["name"];
        }
        break;
    case "3":
        if (isset($_SESSION["d_g_spots"])) {
            if (!(in_array($post_data_1, array_column( $_SESSION["d_g_spots"], 0)))) {
                if (count($_SESSION["d_g_spots"]) < 3) {
                    $_SESSION["d_g_spots"][] = [$post_data_1, $post_data_3, $result1["name"]];
                    $spotname = $result1["name"];
                } else {
                    $spotname = "3";
                }
            } else {
                $spotname = "";
            }
        } else {
            $_SESSION["d_g_spots"][] = [$post_data_1, $post_data_3, $result1["name"]];
            $spotname = $result1["name"];
        }
        break;
}

try {
    switch ($post_data_2) {
        case "1":
            if (!isset($_SESSION["s_l_spots"])) {
                $spots_name_and_id = [["設定されていません", 0]];
            } else {
                foreach ($_SESSION["s_l_spots"] as $s_l) {
                    $stmt = $pdo->prepare("SELECT * FROM $database_name WHERE id = :id");
                    $stmt->bindParam(":id", $s_l[0]);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    $spots_name_and_id[] = [$result["name"], $s_l[0]];
                }
            }
            break;
        case "2":
            if (!isset($_SESSION["l_d_spots"])) {
                $spots_name_and_id = [["設定されていません", 0]];
            } else {
                foreach ($_SESSION["l_d_spots"] as $l_d) {
                    $stmt = $pdo->prepare("SELECT * FROM $database_name WHERE id = :id");
                    $stmt->bindParam(":id", $l_d[0]);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    $spots_name_and_id[] = [$result["name"], $l_d[0]];
                }
            }
            break;
        case "3":
            if (!isset($_SESSION["d_g_spots"])) {
                $spots_name_and_id = [["設定されていません", 0]];
            } else {
                foreach ($_SESSION["d_g_spots"] as $d_g) {
                    $stmt = $pdo->prepare("SELECT * FROM $database_name WHERE id = :id");
                    $stmt->bindParam(":id", $d_g[0]);
                    $stmt->execute();
                    $result = $stmt->fetch(PDO::FETCH_ASSOC);
                    $spots_name_and_id[] = [$result["name"], $d_g[0]];
                }
            }
            break;
    }
} catch (PDOException $e) {
    echo $e->getMessage();
}

$return_array = array($spotname, $spots_name_and_id);

echo json_encode($return_array);
?>
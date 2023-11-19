<?php
session_start();
require_once("connect_database.php");

$post_data_1 = $_POST['post_data_1'];

try {
    $id = $post_data_1;

    //名前重複チェック準備
    $stmt1 = $pdo->prepare("SELECT * FROM userplan WHERE id = :id");
    $stmt1->bindParam(":id", $id);
    $stmt1->execute();
    $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);

    $_SESSION["start_station_id"] = $result1["plan_start"];
    $_SESSION["goal_station_id"] = $result1["plan_goal"];
    $_SESSION["lunch_id"] = $result1["lunch"];
    $_SESSION["lunch_time"] = $result1["lunch_time"];
    $_SESSION["dinner_id"] = $result1["dinner"];
    $_SESSION["dinner_time"] = $result1["dinner_time"];;

    if($result1["s_l"] == -1){
        unset($_SESSION["s_l_spots"]);
    } else {
        $array1 = explode(",", $result1["s_l"]);
        $array3 = [];
        foreach($array1 as $array_id){
            //不本意
            $stmt = $pdo->prepare("SELECT * FROM minatomirai_sightseeing_spots WHERE id = :id");
            $stmt->bindParam(":id", $array_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $array3[] = $result["name"];
        }
        $array2 = explode(",", $result1["s_l_times"]);
        $_SESSION["s_l_spots"] =  array_map(NULL, $array1, $array2, $array3);
    }

    if($result1["l_d"] == -1){
        unset($_SESSION["l_d_spots"]);
    } else {
        $array1 = explode(",", $result1["l_d"]);
        $array3 = [];
        foreach($array1 as $array_id){
            //不本意
            $stmt = $pdo->prepare("SELECT * FROM minatomirai_sightseeing_spots WHERE id = :id");
            $stmt->bindParam(":id", $array_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $array3[] = $result["name"];
        }
        $array2 = explode(",", $result1["l_d_times"]);
        $_SESSION["l_d_spots"] =  array_map(NULL, $array1, $array2, $array3);
    }

    if($result1["d_g"] == -1){
        unset($_SESSION["d_g_spots"]);
    } else {
        $array1 = explode(",", $result1["d_g"]);
        $array3 = [];
        foreach($array1 as $array_id){
            //不本意
            $stmt = $pdo->prepare("SELECT * FROM minatomirai_sightseeing_spots WHERE id = :id");
            $stmt->bindParam(":id", $array_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $array3[] = $result["name"];
        }
        $array2 = explode(",", $result1["d_g_times"]);
        $_SESSION["d_g_spots"] =  array_map(NULL, $array1, $array2, $array3);
    }

    $errormessage = "観光計画をコピーしました";
} catch (PDOException $e) {
    $errormessage = "データベースエラー";
}

$return_array = array($errormessage);

echo json_encode($return_array);
?>
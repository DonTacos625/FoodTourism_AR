<?php
session_start();
require_once("connect_database.php");

$post_data_1 = $_POST['post_data_1'];
$post_data_2 = $_POST['post_data_2'];
$post_data_3 = $_POST['post_data_3'];
$post_data_4 = $_POST['post_data_4'];
$post_data_5 = $_POST['post_data_5'];

$_SESSION["input_plan_name"] = $post_data_2;
$_SESSION["input_plan_memo"] = $post_data_3;
$_SESSION["plan_show"] = $post_data_1;

try {
    $save_plan_id = $post_data_5;
    $save_maker_id = $_SESSION["user_id"];
    $save_show = $post_data_1;
    $save_plan_name = $post_data_2;
    $save_memo = $post_data_3;
    $save_area = $post_data_4;

    //名前重複チェック準備
    $stmt1 = $pdo->prepare("SELECT * FROM userplan WHERE plan_name = :plan_name AND id != :id");
    $stmt1->bindParam(":plan_name", $save_plan_name);
    $stmt1->bindParam(":id", $save_plan_id);
    $stmt1->execute();
    $result1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);

    if (isset($_SESSION["start_station_id"])) {
        $save_plan_start = $_SESSION["start_station_id"];
    } else {
        $save_plan_start = -1;
    }
    if (isset($_SESSION["goal_station_id"])) {
        $save_plan_goal = $_SESSION["goal_station_id"];
    } else {
        $save_plan_goal = -1;
    }
    /*
    if (isset($_SESSION["start_time"])) {
        $save_start_time = $_SESSION["start_time"];
    } else {
        $save_start_time = "10:00";
    }
    if (isset($_SESSION["goal_time"])) {
        $save_goal_time = $_SESSION["goal_time"];
    } else {
        $save_goal_time = "17:00";
    }
    */
    
    if (isset($_SESSION["lunch_id"])) {
        $save_lunch = $_SESSION["lunch_id"];
    } else {
        $save_lunch = -1;
    }
    if (isset($_SESSION["dinner_id"])) {
        $save_dinner = $_SESSION["dinner_id"];
    } else {
        $save_dinner = -1;
    }
    if (isset($_SESSION["lunch_time"])) {
        $save_lunch_time = $_SESSION["lunch_time"];
    } else {
        $save_lunch_time = 30;
    }
    if (isset($_SESSION["dinner_time"])) {
        $save_dinner_time = $_SESSION["dinner_time"];
    } else {
        $save_dinner_time = 30;
    }
    
    if (isset($_SESSION["s_l_spots"])) {
        $plan_s_l = $_SESSION["s_l_spots"];
    } else {
        $plan_s_l = [[-1,0,"設定されていません"]];
    }
    foreach($plan_s_l as $s_l_spot){
        $s_l_spots_id[] = $s_l_spot[0];
        $s_l_spots_time[] = $s_l_spot[1];
    }
    $save_s_l = implode(',', $s_l_spots_id);
    $save_s_l_times = implode(',', $s_l_spots_time);

    if (isset($_SESSION["l_d_spots"])) {
        $plan_l_d = $_SESSION["l_d_spots"];
    } else {
        $plan_l_d = [[-1,0,"設定されていません"]];
    }
    foreach($plan_l_d as $l_d_spot){
        $l_d_spots_id[] = $l_d_spot[0];
        $l_d_spots_time[] = $l_d_spot[1];
    }
    $save_l_d = implode(',', $l_d_spots_id);
    $save_l_d_times = implode(',', $l_d_spots_time);

    if (isset($_SESSION["d_g_spots"])) {
        $plan_d_g = $_SESSION["d_g_spots"];
    } else {
        $plan_d_g = [[-1,0,"設定されていません"]];
    }
    foreach($plan_d_g as $d_g_spot){
        $d_g_spots_id[] = $d_g_spot[0];
        $d_g_spots_time[] = $d_g_spot[1];
    }
    $save_d_g = implode(',', $d_g_spots_id);
    $save_d_g_times = implode(',', $d_g_spots_time);

    //データベースに観光計画情報を保存
    //名前重複チェック
    if (empty($result1)) {
        if($save_plan_start != -1 && $save_plan_goal != -1){
            if($save_lunch != -1 || $save_dinner != -1){
                //ID,Pass書き込み
                //ユーザ情報書き込み
                $stmt3 = $pdo->prepare("UPDATE userplan SET
                    maker_id = :maker_id, 
                    show = :show, 
                    plan_name = :plan_name, 
                    memo = :memo, 
                    area = :area, 
                    plan_start = :plan_start, 
                    s_l = :s_l, 
                    s_l_times = :s_l_times, 
                    lunch = :lunch, 
                    lunch_time = :lunch_time, 
                    l_d = :l_d, 
                    l_d_times = :l_d_times, 
                    dinner = :dinner, 
                    dinner_time = :dinner_time,
                    d_g = :d_g, 
                    d_g_times = :d_g_times, 
                    plan_goal = :plan_goal 
                    WHERE id = :id");

                $stmt3->bindParam(":id", $save_plan_id);
                $stmt3->bindParam(":maker_id", $save_maker_id, PDO::PARAM_INT);
                $stmt3->bindParam(":show", $save_show, PDO::PARAM_INT);
                $stmt3->bindParam(":plan_name", $save_plan_name, PDO::PARAM_STR);
                $stmt3->bindParam(":memo", $save_memo, PDO::PARAM_STR);
                $stmt3->bindParam(":area", $save_area, PDO::PARAM_INT);
                $stmt3->bindParam(":plan_start", $save_plan_start, PDO::PARAM_INT);
                $stmt3->bindParam(":s_l", $save_s_l, PDO::PARAM_STR);
                $stmt3->bindParam(":s_l_times", $save_s_l_times, PDO::PARAM_STR);
                $stmt3->bindParam(":lunch", $save_lunch, PDO::PARAM_INT);
                $stmt3->bindParam(":lunch_time", $save_lunch_time, PDO::PARAM_INT);
                $stmt3->bindParam(":l_d", $save_l_d, PDO::PARAM_STR);
                $stmt3->bindParam(":l_d_times", $save_l_d_times, PDO::PARAM_STR);
                $stmt3->bindParam(":dinner", $save_dinner, PDO::PARAM_INT);
                $stmt3->bindParam(":dinner_time", $save_dinner_time, PDO::PARAM_INT);
                $stmt3->bindParam(":d_g", $save_d_g, PDO::PARAM_STR);
                $stmt3->bindParam(":d_g_times", $save_d_g_times, PDO::PARAM_STR);
                $stmt3->bindParam(":plan_goal", $save_plan_goal, PDO::PARAM_INT);
                $stmt3->execute();
                $errormessage = "更新完了";
            } else {
                $errormessage = "昼食と夕食どちらかを登録してください";
            }
        } else {
            $errormessage = "開始駅と終了駅両方を登録してください";
        }
    } else {
        $errormessage = "プラン名が既に使用されています";
    }
} catch (PDOException $e) {
    $errormessage = "データベースエラー";
}

$return_array = array($errormessage);

echo json_encode($return_array);
?>
<?php
session_start();
//require_once("connect_database.php");

$post_start = $_POST['post_data_1'];
$post_goal = $_POST['post_data_2'];

$post_lunch_box = $_POST['post_data_3'];
$post_dinner_box = $_POST['post_data_4'];

$post_s_l = $_POST['post_data_5'];
$post_l_d = $_POST['post_data_6'];
$post_d_g= $_POST['post_data_7'];


$_SESSION["start_station_id"] = $post_start;
$_SESSION["goal_station_id"] = $post_goal;

if($post_lunch_box[0] == -1){
    unset($_SESSION["lunch_id"]);
    unset($_SESSION["lunch_time"]);
} else {
    $_SESSION["lunch_id"] = $post_lunch_box[0];
    $_SESSION["lunch_time"] = $post_lunch_box[1];
}
if($post_dinner_box[0] == -1){
    unset($_SESSION["dinner_id"]);
    unset($_SESSION["dinner_time"]);
} else {
    $_SESSION["dinner_id"] = $post_dinner_box[0];
    $_SESSION["dinner_time"] = $post_dinner_box[1];
}

if($post_s_l[0][0] == -1){
    unset($_SESSION["s_l_spots"]);
} else {
    $_SESSION["s_l_spots"] = $post_s_l;
}
if($post_l_d[0][0] == -1){
    unset($_SESSION["l_d_spots"]);
} else {
    $_SESSION["l_d_spots"] = $post_l_d;
}
if($post_d_g[0][0] == -1){
    unset($_SESSION["d_g_spots"]);
} else {
    $_SESSION["d_g_spots"] = $post_d_g;
}

$return_array = "更新";

echo json_encode($return_array);
?>
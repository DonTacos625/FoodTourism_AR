<?php
session_start();

$post_data_1 = $_POST['post_data_1'];
$_SESSION["area_id"] = $post_data_1;

unset($_SESSION["start_station_id"]);
unset($_SESSION["goal_station_id"]);
unset($_SESSION["lunch_id"]);
unset($_SESSION["dinner_id"]);
unset($_SESSION["lunch_time"]);
unset($_SESSION["dinner_time"]);
unset($_SESSION["s_l_spots"]);
unset($_SESSION["l_d_spots"]);
unset($_SESSION["d_g_spots"]);

$errormessage = "対象エリアを変更しました";
$return_array = array($errormessage);
echo json_encode($return_array);
?>
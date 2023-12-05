<?php
session_start();

//代入する値
$post_data_1 = $_POST['post_data_1'];
//どのセッション変数に代入するか
$post_data_2 = json_decode($_POST['post_data_2'], true);

switch ($post_data_2) {
    case "1":
        $_SESSION["search_spots_distance"] = $post_data_1;
        break;
    case "2":
        $_SESSION["search_spots_category"] = $post_data_1;
        break;
}

//echo json_encode($return);
//echo json_encode($spots_name_and_id);
?>

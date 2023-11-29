<?php
session_start();
require_once("connect_database.php");

$post_data_1 = $_POST['post_data_1'];
$post_data_2 = json_decode($_POST['post_data_2'], true);
$post_data_3 = $_POST['post_data_3'];

$database_name = "hasune_restaurants";
try{

    $stmt1 = $pdo->prepare("SELECT * FROM $database_name where id = :id");
    $stmt1 -> bindParam(":id", $post_data_1, PDO::PARAM_INT);
    $stmt1 -> execute();
    $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);

}catch(PDOException $e){
    //デバッグ用
    echo $e->getMessage();
}

function unset_spots()
{
    if (isset($_SESSION["s_l_spots"])) {
        unset($_SESSION["s_l_spots"]);
    }
    if (isset($_SESSION["l_d_spots"])) {
        unset($_SESSION["l_d_spots"]);
    }
    if (isset($_SESSION["d_g_spots"])) {
        unset($_SESSION["d_g_spots"]);
    }
}

switch ($post_data_2) {
    case "1":
        $_SESSION["lunch_id"] = $post_data_1;
        $_SESSION["lunch_time"] = $post_data_3;
        $frame_id = "lunch_name";
        unset_spots();
        break;
    case "2":
        $_SESSION["dinner_id"] = $post_data_1;
        $_SESSION["dinner_time"] = $post_data_3;
        $frame_id = "dinner_name";
        unset_spots();
        break;
}

$return_array = array($result1["name"], $frame_id, $post_data_3);

echo json_encode($return_array);
?>
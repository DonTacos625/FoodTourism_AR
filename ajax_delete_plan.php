<?php
session_start();
require_once("connect_database.php");

$post_data_1 = $_POST['post_data_1'];

try {
    $id = $post_data_1;

    //名前重複チェック準備
    $stmt1 = $pdo->prepare("DELETE FROM userplan WHERE id = :id");
    $stmt1->bindParam(":id", $id);
    $stmt1->execute();
    $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);

    $errormessage = "観光計画を削除しました";
} catch (PDOException $e) {
    $errormessage = "データベースエラー";
}

$return_array = array($errormessage);

echo json_encode($return_array);
?>
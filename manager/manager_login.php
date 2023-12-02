<?php
//require_once(__DIR__ . "/../config/cfg.php");

session_start();

//エラーメッセージ初期化
$errormessage = "";


if (!empty($_GET["register"])) {
    $errormessage = "会員登録完了";
}

//ログイン処理
if (!empty($_POST["login"])) {
    //IDのチェック
    if (empty($_POST["user_name"])) {
        $errormessage = "ユーザーネームかメールアドレスが入力されていません";
    } else if (empty($_POST["pass"])) {
        $errormessage = "パスワードが入力されていません";
    }

    //ID・Passのチェック
    if (!empty($_POST["user_name"]) && !empty($_POST["pass"])) {
        $user_name = $_POST["user_name"];

        //DB接続
        require "connect_database.php";

        try {

            $pass = $_POST["pass"];

            $stmt1 = $pdo->prepare("SELECT * FROM userinfo WHERE user_name = :user_name OR email = :user_name");
            $stmt1->bindParam(":user_name", $user_name);
            $stmt1->execute();

            if ($result1 = $stmt1->fetchAll(PDO::FETCH_ASSOC)) {
                if (password_verify($pass, $result1[0]["pass"])) {
                    session_regenerate_id(true);

                    $_SESSION["user_name"] = $result1[0]["user_name"];
                    $_SESSION["id"] = $result1["id"];
                    $_SESSION["age"] = $result1["age"];
                    $_SESSION["gender"] = $result1["gender"];

                    //ログイン成功でホームに移動
                    header("Location: home.php");
                    exit();
                } else if (!password_verify($pass, $result1[0]["pass"])) {
                    $errormessage = "パスワードが間違っています";
                }
            } else if (empty($result1)) {
                $errormessage = "ユーザーネームかメールアドレスが間違っています";
            }
        } catch (PDOException $e) {
            $errormessage = "データベースエラー";

            echo $e->getMessage();
        }
    }
}

?>

<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0">
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-214561408-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-214561408-1');
    </script>

    <title>ログイン</title>

    <link rel="stylesheet" type="text/css" href="css/copyright.css">
    <style>

        #loginbox {
            width: 768px;
            height: 500px;
            margin: auto;
            border: 1px solid #aaa;
            text-align: center;
        }

        #loginbox table {
            margin: auto;
        }

        #loginbox table th {
            background: #0099FF;
            color: #fff;
            white-space: nowrap;
            border-left: 5px solid #000080;
        }
        #loginbox table td {
            text-align: right;
        }

        @media screen and (max-width: 768px) {
            h2 {
                font-size: 19px;
            }

            h3 {
                font-size: 17px;
            }

            #loginbox {
                width: 90%;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <main  class="row">
            <div id="loginbox"><br><br><br>
                <h2>横浜みなとみらいフードツーリズム計画作成支援システム</h2>
                <p>
                    こちらは横浜みなとみらい近隣でのフードツーリズム計画の作成を支援するシステムです。<br>
                    本システムは、PC・スマートフォン・タブレット端末でご利用可能です。<br>
                    利用には<a href="signup.php">利用者登録</a>が必要となります。<br>
                    利用方法につきましては、ログイン後に使い方をご覧になるか、<a href="https://drive.google.com/file/d/1mlQpnjmVcnnn_Qqt2BwjwZzFUHGAplDD/view?usp=sharing" target="blank">こちら</a>のマニュアルからご覧ください<br>
                </p>

                <h3>ログイン</h3>
                <form id="loginform" name="loginform" action="" method="POST" autocomplete="off">
                    <table>
                        <tr>
                            <th><label for="user_name">ネームかemail</label></th>
                            <td>
                                <input type="text" id="user_name" name="user_name" placeholder="ネームかemailを入力" value="" required>
                            </td>
                        </tr>

                        <tr>
                            <th><label for="pass">パスワード</label></th>
                            <td>
                                <input type="password" id="pass" name="pass" placeholder="パスワードを入力" value="" required>
                            </td>
                        </tr>

                        <tr>
                            <td></td>
                            <td><input type="submit" id="login" name="login" value="ログイン"></td>
                        </tr>
                    </table>
                </form>

                <div>
                    <font color="#ff0000"><?php echo htmlspecialchars($errormessage, ENT_QUOTES); ?></font>
                </div>
                <div>
                    <font color="#ff0000"><?php //echo htmlspecialchars($signupmessage, ENT_QUOTES); ?></font>
                </div>
            </div>
        </main>
        <footer>
            <p>Copyright(c) 2021 山本佳世子研究室 All Rights Reserved.</p>
        </footer>
    </div>
</body>

</html>
<?php
require "frame_define_bl.php";

session_start();

if (!empty($_SESSION["user_name"])) {
    $errormessage = "ログアウトしました";
} else {
    $errormessage = "再度ログインして下さい";
}

$_SESSION = array();

@session_destroy();
?>

<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0">

    <title>ログアウト</title>

    <link rel="stylesheet" type="text/css" href="css/copyright.css">
    <style>

        #logoutbox {
            width: 768px;
            height: 500px;
            margin: auto;
            border: 1px solid #aaa;
            text-align: center;
        }

        @media screen and (max-width: 768px) {
            h2 {
                font-size: 19px;
            }

            h3 {
                font-size: 17px;
            }

            #logoutbox {
                width: 90%;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <main>
            <div id="logoutbox"><br><br><br>
                <h2 class="m-3">横浜みなとみらいフードツーリズム計画作成支援システム</h2>
                <h3 class="m-3">ログアウト</h3>
                <a href="login.php">ログイン画面</a>
                <div><?php echo htmlspecialchars($errormessage, ENT_QUOTES); ?></div>
            </div>
        </main>
        <footer>
            <p>Copyright(c) 2023 山本佳世子研究室 All Rights Reserved.</p>
        </footer>
    </div>
</body>

</html>
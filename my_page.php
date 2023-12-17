<?php
require "frame_define.php";
require "frame_header.php";
require "frame_menu.php";
require "frame_rightmenu.php";

//frame.phpからの変数
$now_weight = $frameresult["user_weight"];
$now_gender = $frameresult["gender"];
$now_age = $frameresult["age"];

function set_checked($session_name, $value)
{
    if ($value == $session_name) {
        //値がセッション変数と等しいとチェックされてる判定として返す
        print "checked=\"checked\"";
    } else {
        print "";
    }
}

$errormessage = "";
$editmessage = "";

if (!empty($_POST["editpass"])) {
    //Pass文字列チェック
    if (!preg_match('/\A(?=.*?[a-z])(?=.*?\d)[a-z\d]{6,15}+\z/i', $_POST["pass1"])) {
        $errormessage = 'パスワードが不適切です';
    }

    //Pass空判定
    if (empty($_POST["pass"])) {
        $errormessage = '現在のパスワードが未入力です';
    } else if (empty($_POST["pass1"])) {
        $errormessage = '新しいパスワードが未入力です';
    } else if (empty($_POST["pass2"])) {
        $errormessage = '新しいパスワード(確認)が未入力です';
    }

    if (!empty($_POST["pass"]) && !empty($_POST["pass1"]) && !empty($_POST["pass2"]) && $_POST["pass1"] === $_POST["pass2"] && preg_match('/\A(?=.*?[a-z])(?=.*?\d)[a-z\d]{6,15}+\z/i', $_POST["pass1"])) {
        $pass = $_POST["pass"];
        $pass1 = $_POST["pass1"];

        //DB接続
        try {

            //現在のパスワードを確認
            $stmt1 = $pdo->prepare("SELECT * FROM userinfo WHERE id = :id");
            $stmt1->bindParam(":id", $_SESSION["user_id"]);
            $stmt1->execute();
            $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);

            if (password_verify($pass, $result1["pass"])) {
                $passhash = password_hash($pass1, PASSWORD_DEFAULT);
                $stmt2 = $pdo->prepare("UPDATE userinfo SET pass = :pass WHERE id = :id");
                $stmt2->bindParam(":pass", $passhash, PDO::PARAM_STR);
                $stmt2->bindParam(":id", $_SESSION["user_id"]);
                $stmt2->execute();

                $editmessage = "変更完了";
            } else {
                $errormessage = "パスワードが間違っています";
            }
        } catch (PDOException $e) {
            $errormessage = "データベースエラー";
            //デバッグ用
            echo $e->getMessage();
        }
    } else if ($_POST["pass1"] != $_POST["pass2"]) {
        $errormessage = "パスワードが一致しません";
    }
}


if (!empty($_POST["editopt"])) {

    //登録情報を整理
    $age = $_POST["age"];
    $gender = $_POST["gender"];
    if (isset($_POST["user_weight"])) {
        $user_weight = $_POST["user_weight"];
    } else {
        $user_weight = 0;
    }
    settype($age, "int");
    settype($user_weight, "int");

    //DB接続
    try {

        $stmt3 = $pdo->prepare("UPDATE userinfo SET gender = :gender, age = :age, user_weight = :user_weight WHERE id = :id");
        $stmt3->bindParam(":id", $_SESSION["user_id"]);
        $stmt3->bindParam(":gender", $gender, PDO::PARAM_STR);
        $stmt3->bindParam(":age", $age, PDO::PARAM_INT);
        $stmt3->bindParam(":user_weight", $user_weight, PDO::PARAM_INT);
        $stmt3->execute();

        $editmessage = "変更完了";

    } catch (PDOException $e) {
        $errormessage = "データベースエラー";
        //デバッグ用
        echo $e->getMessage();
    }
}
?>

<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-WJ8NH8EYSR"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-WJ8NH8EYSR');
    </script>

    <title>登録情報変更</title>

    <style>
        h2 {
            border-left: 5px solid #000080;
            margin: 0px;
        }

        .editbox {
            float: left;
            width: 500px;
            margin-left: 5px;
        }

        .editbox h2 {
            margin: 0px;
        }

        .editbox th {
            width: 100px;
            background: #0099FF;
            color: #fff;
            white-space: nowrap;
            margin: 3px;
            padding: 2px;
            border-left: 5px solid #000080;
        }

        .editbox td {
            text-align: left;
        }

        @media screen and (min-width:769px) and (max-width:1366px) {
            h2 {
                font-size: 20px;
            }

        }

        @media screen and (max-width:768px) {
            h2 {
                font-size: 19px;
            }

            .editbox {
                width: auto;
                margin: 0px;
            }

            .editbox th {
                font-size: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <main class="row">
            <div class="editbox">
                <h2>パスワード変更</h2>
                <form class="editform" name="editform" action="" method="POST">
                    <table>
                        <tr>
                            <th rowspan="2" scope="rowgroup"><label for="pass">旧パスワード</label></th>
                            <td scope="row"><small>現在のパスワードを入力して下さい</small></td>
                        </tr>

                        </tr>
                        <td><input type="password" id="pass" name="pass" placeholder="現在のパスワードを入力" value="" required></td>
                        </tr>

                        <tr>
                            <th rowspan="2" scope="rowgroup"><label for="pass1">新パスワード</label></th>
                            <td scope="row"><small>半角英数字をそれぞれ1種類以上含む6~15文字</small></td>
                        </tr>

                        </tr>
                        <td><input type="password" id="pass1" name="pass1" placeholder="新しいパスワードを入力" value="" required></td>
                        </tr>

                        <tr>
                            <th rowspan="2" scope="rowgroup"><label for="pass2">新パスワード(確認)</label></th>
                            <td scope="row"><small>パスワードを再入力して下さい</small></td>
                        </tr>

                        </tr>
                        <td><input type="password" id="pass2" name="pass2" placeholder="新しいパスワードを再入力" value="" required></td>
                        </tr>

                        <tr>
                            <td></td>
                            <td><input type="submit" id="editpass" name="editpass" value="変更"></td>
                        </tr>
                    </table>
                </form>

                <div>
                    <font color="#ff0000"><?php echo htmlspecialchars($errormessage, ENT_QUOTES); ?></font>
                </div>
                <div>
                    <font color="#0000ff"><?php echo htmlspecialchars($editmessage, ENT_QUOTES); ?></font>
                </div>
            </div>

            <div class="editbox">
                <h2>登録情報変更</h2>
                <form class="editform" name="editform" action="" method="POST">
                <table>
                        <tr>
                            <th rowspan="2" scope="rowgroup"><label for="user_weight">体重</label></th>
                            <td scope="row"><small>体重を入力してください<br>(消費カロリーの計算に使用します)</small></td>
                        </tr>
                        <tr>
                            <td scope="row"><input type="number" id="user_weight" name="user_weight" placeholder="体重を入力" value="<?php echo $now_weight; ?>">kg</td>
                        </tr>

                        <tr>
                            <th>性別</th>
                            <td>
                                <input type="radio" id="gender" name="gender" value="未回答" <?php set_checked($now_gender, "未回答"); ?>>回答しない
                                <input type="radio" id="gender" name="gender" value="男性" <?php set_checked($now_gender, "男性"); ?>>男性
                                <input type="radio" id="gender" name="gender" value="女性" <?php set_checked($now_gender, "女性"); ?>>女性
                            </td>
                        </tr>
                        
                        <tr>
                            <th>年代</th>
                            <td>
                                <input type="radio" id="age" name="age" value="0" <?php set_checked($now_age, "0"); ?> >回答しない<br>
                                <input type="radio" id="age" name="age" value="10" <?php set_checked($now_age, "10"); ?>>10代
                                <input type="radio" id="age" name="age" value="20" <?php set_checked($now_age, "20"); ?>>20代
                                <input type="radio" id="age" name="age" value="30" <?php set_checked($now_age, "30"); ?>>30代<br>
                                <input type="radio" id="age" name="age" value="40" <?php set_checked($now_age, "40"); ?>>40代
                                <input type="radio" id="age" name="age" value="50" <?php set_checked($now_age, "50"); ?>>50代
                                <input type="radio" id="age" name="age" value="60" <?php set_checked($now_age, "60"); ?>>60代以上
                            </td>
                        </tr>
                        <tr>
                            <td></td>
                            <td><input type="submit" id="editopt" name="editopt" value="変更"></td>
                        </tr>
                    </table>
                </form>

                <div>
                    <font color="#ff0000"><?php echo htmlspecialchars($errormessage, ENT_QUOTES); ?></font>
                </div>
                <div>
                    <font color="#0000ff"><?php echo htmlspecialchars($editmessage, ENT_QUOTES); ?></font>
                </div>
            </div>
        </main>
        <footer>
            <p>Copyright(c) 2023 山本佳世子研究室 All Rights Reserved.</p>
        </footer>
    </div>
</body>

</html>
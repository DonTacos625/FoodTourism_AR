<?php

/*
DB userinfo
id,pass,age,gender

DB userdata
*/

//session_start();

$errormessage = "";
$signupmessage = "";

//入力値保存
if (!isset($_SESSION["user_name"])) {
    $_SESSION["user_name"] = "";
    $user_name_f = "";
} else {
    $user_name_f = $_SESSION["user_name"];
}
if (isset($_POST["user_name"])) {
    $user_name_f = $_POST["user_name"];
    $_SESSION["user_name"] = $_POST["user_name"];
} else {
    $user_name_f = $_SESSION["user_name"];
}

if (!isset($_SESSION["pass"])) {
    $_SESSION["pass"] = "";
    $pass_f = "";
} else {
    $pass_f = $_SESSION["pass"];
}
if (isset($_POST["pass"])) {
    $pass_f = $_POST["pass"];
    $_SESSION["pass"] = $_POST["pass"];
} else {
    $pass_f = $_SESSION["pass"];
}
if (!isset($_SESSION["pass2"])) {
    $_SESSION["pass2"] = "";
    $pass2_f = "";
} else {
    $pass2_f = $_SESSION["pass2"];
}
if (isset($_POST["pass2"])) {
    $pass2_f = $_POST["pass2"];
    $_SESSION["pass2"] = $_POST["pass2"];
} else {
    $pass2_f = $_SESSION["pass2"];
}

if (!isset($_SESSION["user_weight"])) {
    $_SESSION["user_weight"] = "";
    $user_weight_f = "";
} else {
    $user_weight_f = $_SESSION["user_weight"];
}
if (isset($_POST["user_weight"])) {
    $user_weight_f = $_POST["user_weight"];
    $_SESSION["user_weight"] = $_POST["user_weight"];
} else {
    $user_weight_f = $_SESSION["user_weight"];
}


//登録処理
if (!empty($_POST["signup"])) {
    //ID・Pass文字列チェック
    if (!preg_match('/\A[a-z\d]{4,10}+\z/i', $_POST["user_name"])) {
        $errormessage = 'ユーザーネームが不適切です';
    }
    if (!preg_match('/\A(?=.*?[a-z])(?=.*?\d)[a-z\d]{6,15}+\z/i', $_POST["pass"])) {
        $errormessage = 'パスワードが不適切です';
    }
    //ID・Pass空チェック
    if (empty($_POST["user_name"])) {
        $errormessage = "ユーザーIDが未入力です";
    } else if (empty($_POST["pass"])) {
        $errormessage = 'パスワードが未入力です';
    } else if (empty($_POST["pass2"])) {
        $errormessage = 'パスワードを再入力してください';
    }

    if (!empty($_POST["user_name"]) && !empty($_POST["pass"]) && !empty($_POST["pass2"]) && $_POST["pass"] === $_POST["pass2"] && preg_match('/\A[a-z\d]{4,10}+\z/i', $_POST["user_name"]) && preg_match('/\A(?=.*?[a-z])(?=.*?\d)[a-z\d]{6,15}+\z/i', $_POST["pass"]) ) {
        $user_name = $_POST["user_name"];
        $user_name = strtr($user_name, [
            '\\' => '\\\\',
            '%' => '\%',
            '_' => '\_',
        ]);

        $pass = $_POST["pass"];

        $age = $_POST["age"];
        $gender = $_POST["gender"];

        if (isset($_POST["user_weight"])) {
            $user_weight = $_POST["user_weight"];
        } else {
            $user_weight = 0;
        }

        $survey = 1;
        settype($age, "int");
        settype($user_weight, "int");

        //DB接続
        require "connect_database.php";

        try {

            //名前重複チェック準備
            $stmt1 = $pdo->prepare("SELECT * FROM userinfo WHERE user_name = :user_name");
            $stmt1->bindParam(":user_name", $user_name);
            $stmt1->execute();
            $result1 = $stmt1->fetchAll(PDO::FETCH_ASSOC);

            //データベースに観光計画情報を保存

            //名前重複チェック
            if (empty($result1)) {
                //パスワードのハッシュ化
                $passhash = password_hash($pass, PASSWORD_DEFAULT);

                //ID,Pass書き込み
                //ユーザー情報書き込み
                $stmt3 = $pdo->prepare("INSERT INTO userinfo(user_name, pass, gender, age, user_weight, survey) VALUES(:user_name, :pass, :gender, :age, :user_weight, :survey)");
                $stmt3->bindParam(":user_name", $user_name, PDO::PARAM_STR);
                $stmt3->bindParam(":pass", $passhash, PDO::PARAM_STR);
                $stmt3->bindParam(":age", $age, PDO::PARAM_INT);
                $stmt3->bindParam(":gender", $gender, PDO::PARAM_STR);
                $stmt3->bindParam(":user_weight", $user_weight, PDO::PARAM_INT);
                $stmt3->bindParam(":survey", $survey, PDO::PARAM_INT);
                $stmt3->execute();

                //ログイン画面へ移動
                header("Location: login.php?register=1");
                //@session_destroy();

                $signupmessage = "登録完了";
            } elseif (!empty($result1)) {
                $errormessage = "IDが既に使用されています";
            } elseif (!empty($result2)) {
                $errormessage = "メールアドレスが既に使用されています";
            }
        } catch (PDOException $e) {
            $errormessage = "データベースエラー";
        }
    } else if ($_POST["pass"] != $_POST["pass2"]) {
        $errormessage = "パスワードに誤りがあります";
    }
}
//var_dump($result1);
//var_dump($user_name);
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

    <title>サインアップ</title>

    <link rel="stylesheet" type="text/css" href="css/copyright.css">
    <style>

        #signupbox {
            width: 768px;
            height: 700px;
            margin: auto;
            border: 1px solid #aaa;
            text-align: center;
        }

        #signupbox table {
            margin: auto;
        }

        #signupbox table th {
            background: #0099FF;
            color: #fff;
            white-space: nowrap;
            border-left: 5px solid #000080;
            width: 12vw;
        }

        #signupbox table td {
            text-align: left;
            width: 12vw;
        }

        @media screen and (max-width: 768px) {
            h2 {
                font-size: 19px;
            }

            h3 {
                font-size: 17px;
            }

            #signupbox {
                width: auto;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <main>
            <div id="signupbox">
                <h2>横浜みなとみらいフードツーリズム計画作成支援システム</h2>

                <h3>利用者登録</h3>
                <form id="signupform" name="signupForm" action="" method="POST" autocomplete="off">
                    <table>
                        <tr>
                            <th rowspan="2" scope="rowgroup"><label for="user_name">ユーザーネーム</label></th>
                            <td scope="row"><small>半角英数字4~10文字</small></td>
                        </tr>
                        <tr>
                            <td scope="row"><input type="text" id="user_name" name="user_name" placeholder="ユーザーネームを入力" value="<?php echo $user_name_f; ?>" required></td>
                        </tr>

                        <tr>
                            <th rowspan="2" scope="rowgroup"><label for="pass">パスワード</label></th>
                            <td scope="row"><small>半角英数字をそれぞれ1種類以上含む6~15文字</small></td>
                        </tr>
                        <tr>
                            <td scope="row"><input type="password" id="pass" name="pass" placeholder="パスワードを入力" value="<?php echo $pass_f; ?>" required></td>
                        </tr>
                        <tr>
                            <th rowspan="2" scope="rowgroup"><label for="pass2">パスワード再入力</label></th>
                            <td scope="row"><small>パスワードを再入力して下さい</small></td>
                        </tr>
                        <tr>
                            <td scope="row"><input type="password" id="pass2" name="pass2" placeholder="パスワードを再入力" value="<?php echo $pass2_f; ?>" required></td>
                        </tr>

                    </table>

                <h3>任意登録</h3>
                    <table>
                        <tr>
                            <th rowspan="2" scope="rowgroup"><label for="user_weight">体重</label></th>
                            <td scope="row"><small>体重を入力してください</small></td>
                        </tr>
                        <tr>
                            <td scope="row"><input type="number" id="user_weight" name="user_weight" placeholder="体重を入力" value="<?php echo $user_weight_f; ?>">kg</td>
                        </tr>

                        <tr>
                            <th>性別</th>
                            <td>
                                <input type="radio" id="gender" name="gender" value="未回答" checked="checked">回答しない
                                <input type="radio" id="gender" name="gender" value="男性">男性
                                <input type="radio" id="gender" name="gender" value="女性">女性
                            </td>
                        </tr>
                        
                        <tr>
                            <th>年代</th>
                            <td>
                                <input type="radio" id="age" name="age" value="0" checked="checked">回答しない<br>
                                <input type="radio" id="age" name="age" value="10">10代
                                <input type="radio" id="age" name="age" value="20">20代
                                <input type="radio" id="age" name="age" value="30">30代<br>
                                <input type="radio" id="age" name="age" value="40">40代
                                <input type="radio" id="age" name="age" value="50">50代
                                <input type="radio" id="age" name="age" value="60">60代以上
                            </td>
                        </tr>

                        <tr>
                            <td></td>
                            <td><input type="submit" id="signup" name="signup" value="登録"><br><br>
                            <a href="login.php">ログイン画面</a></td>
                        </tr>
                    </table>
                </form>
                <div>
                    <font color="#ff0000"><?php echo htmlspecialchars($errormessage, ENT_QUOTES); ?></font>
                </div>
                <div>
                    <font color="#0000ff"><?php echo htmlspecialchars($signupmessage, ENT_QUOTES); ?></font>
                </div>
            </div>
        </main>
        <footer>
            <p>Copyright(c) 2021 山本佳世子研究室 All Rights Reserved.</p>
        </footer>
    </div>
</body>

</html>
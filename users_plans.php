<?php

require "frame_define.php";
require "frame_header.php";
require "frame_menu.php";
require "frame_rightmenu.php";

try {

    //sql文にする
    $sql = "SELECT userplan.*, userinfo.user_name FROM userplan INNER JOIN userinfo ON userplan.maker_id = userinfo.id WHERE show = 1 AND userplan.area = $area;";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
} catch (PDOException $e) {
    echo "失敗:" . $e->getMessage() . "\n";
    exit();
}
$count = 0
?>

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
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <title>他のユーザが作成した観光計画</title>
    <style>
        @media screen and (min-width:769px) and (max-width:1366px) {}

        @media screen and (max-width:768px) {

            .search_form {
                font-size: 12px;
            }

        }

        .flex_test-box {
            width: 97%;
            background-color: #eee;
            /* 背景色指定 */
            padding: 10px;
            /* 余白指定 */
            display: flex;
            /* フレックスボックスにする */
            align-items: stretch;
            /* 縦の位置指定 */
            flex-wrap: wrap;
        }

        .plan_text {
            overflow: hidden;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
            /* 任意の行数を指定*/
        }

        .card {
            margin: 10px;
            /* 外側の余白 */
        }

        .card-header h4 {
            border-left: 5px solid #000080;
            margin: 0px;
        }
    </style>
</head>

<script type="text/javascript">
    //セレクトボックスから選ばれたワードを検索ワードボックスに入れる　もっといい方法あるかも
    function input_search_name(word) {
        const update = document.getElementById("search_name");
        update.value = word;
    };
    //予算範囲が不適切な場合
    function right_range(word) {
        if (word.match(/lunch/)) {
            const lunch_min = document.getElementById("lunch_min");
            const lunch_max = document.getElementById("lunch_max");
            if (lunch_min.value - lunch_max.value > 0) {
                alert("最小予算が最大予算を超えています！");
            }
        } else if (word.match(/dinner/)) {
            const dinner_min = document.getElementById("dinner_min");
            const dinner_max = document.getElementById("dinner_max");
            if (dinner_min.value - dinner_max.value > 0) {
                alert("最小予算が最大予算を超えています！");
            }
        }
    };
</script>

<body>
    <div class="container-fluid">
        <main class="row">
            <h3 class="px-0" id="search_start">他のユーザが作成した観光計画</h3>

            <div class="flex_test-box">
                <?php foreach ($stmt as $row) : ?>
                    <?php $count += 1; ?>
                    <div class="card" style="width: 18rem;" value=<?php echo $row["id"]; ?>>
                        <h4 class="card-header"><?php echo htmlspecialchars($row["plan_name"], ENT_QUOTES); ?></h4><br>
                        <img class="card-img-top" src=<?php if ($row["lunch"] != -1) {
                                                            echo "images/$area_name/restaurants/" . $row["lunch"] . ".jpg";
                                                        } else {
                                                            echo "images/$area_name/restaurants/" . $row["dinner"] . ".jpg";
                                                        } ?> onError="this.onerror=null;this.src='images/no_image.jpg';" alt="">
                        <div class="card-text">
                            <div class="plan_text">作成したユーザ：<br><?php echo htmlspecialchars($row["user_name"], ENT_QUOTES); ?></div><br>
                            <div class="plan_text">メモ：<br><?php echo htmlspecialchars($row["memo"], ENT_QUOTES); ?></div>
                        </div>
                        <br><a href="users_plan_detail.php?plan_id=<?php echo $row["id"]; ?>">詳細ページに移動する</a>
                    </div>
                <?php endforeach; ?>
                <?php
                if (!$count) {
                    echo "検索条件に該当する飲食店はありませんでした";
                }
                ?>
            </div>
            <br>
        </main>
        <footer>
            <p>Copyright(c) 2023 山本佳世子研究室 All Rights Reserved.</p>
        </footer>
    </div>
</body>

</html>
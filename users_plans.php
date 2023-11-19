<?php

require "frame.php";

try {

    //sql文にする
    $sql = 'SELECT userplan.*, userinfo.user_name FROM userplan INNER JOIN userinfo ON userplan.maker_id = userinfo.id WHERE show = 1;';

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
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-214561408-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-214561408-1');
    </script>
    <!-- <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> -->
    <title>飲食店の検索・決定（一覧表示）</title>
    <style>
        h3 {
            border-left: 5px solid #000080;
            margin: 0px;
        }

        #detailbox {
            position: relative;
            float: left;
            margin-left: 0px;
        }

        @media screen and (min-width:769px) and (max-width:1366px) {
            h3 {
                margin: 0px;
                font-size: 18px;
            }
        }

        @media screen and (max-width:768px) {
            h3 {
                margin: 0px;
                font-size: 17px;
            }

            .search_form {
                font-size: 12px;
            }

            #detailbox {
                width: auto;
                margin: 0px;
                float: none;
            }

        }

        .flex_test-box {
            width: 90%;
            background-color: #eee;     /* 背景色指定 */
            padding:  10px;             /* 余白指定 */
            display: flex;              /* フレックスボックスにする */
            align-items:stretch;        /* 縦の位置指定 */
            flex-wrap: wrap;
        }

        .flex_test-item {
            padding: 10px;
            color:  #0a0000;               /* 文字色 */
            margin:  10px;              /* 外側の余白 */
            border-radius:  5px;        /* 角丸指定 */
            width: 25%;                 /* 幅指定 */
            background-color:  #fff; /* 背景色指定 */
        }

        .flex_test-item #imgbox{
            float: left;
            display: flex;
            width: 15vw;
            height: 15vw;
            margin-bottom: 15px;
            justify-content: center;
            align-items: center;
        }

        .flex_test-item #imgbox img{
            width:auto;
            height:auto;
            max-width:100%;
            max-height:100%;
        }
        h4 {
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
        if(word.match(/lunch/)){
            const lunch_min = document.getElementById("lunch_min");
            const lunch_max = document.getElementById("lunch_max");
            if(lunch_min.value - lunch_max.value > 0){
                alert("最小予算が最大予算を超えています！");
            }
        } else if(word.match(/dinner/)){
            const dinner_min = document.getElementById("dinner_min");
            const dinner_max = document.getElementById("dinner_max");
            if(dinner_min.value - dinner_max.value > 0){
                alert("最小予算が最大予算を超えています！");
            }
        }
    };
</script>

<body>
    <div class="container">
        <main>
            <div id="detailbox">
                <h3 id="search_start">飲食店の検索・決定</h3>
                <div class="move_box">
                    <a class="prev_page" name="prev_station" href="set_station.php">開始・終了駅選択に戻る</a>
                    <a class="next_page" name="next_keiro" href="sightseeing_spots_selection.php">観光スポット選択へ</a><br>
                </div>

                <div class="flex_test-box">
                    <?php foreach ($stmt as $row) : ?>
                        <?php $count += 1; ?>
                            <div class="flex_test-item" value=<?php echo $row["id"]; ?>>
                                <h4>プラン名：<?php echo $row["plan_name"]; ?></h4><br>
                                <div id="imgbox">
                                    <img src=<?php if($row["lunch"] != -1) {echo "images/minatomirai/restaurants/". $row["lunch"] .".jpg" ;} else {echo "images/minatomirai/restaurants/". $row["dinner"] .".jpg" ;}?> alt=""><br>
                                </div>
                                <div>作成したユーザー：<?php echo $row["user_name"]; ?></div><br>
                                <div>メモ：<?php echo $row["memo"]; ?></div><br>
                                <a href="users_plan_detail.php?plan_id=<?php echo $row["id"]; ?>">詳細ページに移動する</a>
                            </div>
                    <?php endforeach; ?>
                    <?php
                    if (!$count) {
                        echo "検索条件に該当する飲食店はありませんでした";
                    }
                    ?>
                </div>
            </div>
            <br>
        </main>
        <footer>
            <p>Copyright(c) 2023 山本佳世子研究室 All Rights Reserved.</p>
        </footer>
    </div>
</body>

</html>
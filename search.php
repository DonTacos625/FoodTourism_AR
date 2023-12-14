<?php

require "frame_define.php";
require "frame_header.php";
require "frame_menu.php";
require "frame_rightmenu.php";

$message = "";
if (!isset($_SESSION["start_station_id"]) || !isset($_SESSION["goal_station_id"])) {
    $message = "開始・終了駅が設定されていません";
} else if (!isset($_SESSION["lunch_id"]) && !isset($_SESSION["dinner_id"])) {
    $message = "昼食または夕食予定地が設定されていません";
}

try {

    //SESSION変数初期値設定
    if (!isset($_SESSION["wifi"])) {
        $_SESSION["wifi"] = "0";
    }
    if (!isset($_SESSION["private_room"])) {
        $_SESSION["private_room"] = "0";
    }
    if (!isset($_SESSION["credit_card"])) {
        $_SESSION["credit_card"] = "0";
    }
    if (!isset($_SESSION["non_smoking"])) {
        $_SESSION["non_smoking"] = "0";
    }
    if (!isset($_SESSION["lunch"])) {
        $_SESSION["lunch"] = "0";
    }

    if (!isset($_SESSION["capacity"])) {
        $_SESSION["capacity"] = "0";
    }

    if (!isset($_SESSION["lunch_min"])) {
        $_SESSION["lunch_min"] = "0";
    }
    if (!isset($_SESSION["lunch_max"])) {
        $_SESSION["lunch_max"] = "999999";
    }
    if (!isset($_SESSION["dinner_min"])) {
        $_SESSION["dinner_min"] = "0";
    }
    if (!isset($_SESSION["dinner_max"])) {
        $_SESSION["dinner_max"] = "999999";
    }

    if (!isset($_SESSION["search_genre"])) {
        $_SESSION["search_genre"] = "0";
    }
    if (!isset($_SESSION["search_name"])) {
        $_SESSION["search_name"] = "";
    }
    //提出されたデータ
    if (isset($_POST["wifi"])) {
        $wifi = $_POST["wifi"];
        $_SESSION["wifi"] = $wifi;
    } else {
        $wifi = $_SESSION["wifi"];
    }
    if (isset($_POST["private_room"])) {
        $private_room = $_POST["private_room"];
        $_SESSION["private_room"] = $private_room;
    } else {
        $private_room = $_SESSION["private_room"];
    }
    if (isset($_POST["credit_card"])) {
        $credit_card = $_POST["credit_card"];
        $_SESSION["credit_card"] = $credit_card;
    } else {
        $credit_card = $_SESSION["credit_card"];
    }
    if (isset($_POST["non_smoking"])) {
        $non_smoking = $_POST["non_smoking"];
        $_SESSION["non_smoking"] = $non_smoking;
    } else {
        $non_smoking = $_SESSION["non_smoking"];
    }
    if (isset($_POST["lunch"])) {
        $lunch = $_POST["lunch"];
        $_SESSION["lunch"] = $lunch;
    } else {
        $lunch = $_SESSION["lunch"];
    }

    if (isset($_POST["capacity"])) {
        $capacity = $_POST["capacity"];
        $_SESSION["capacity"] = $capacity;
        settype($capacity, "int");
    } else {
        $capacity = $_SESSION["capacity"];
        settype($capacity, "int");
    }

    if (isset($_POST["lunch_min"])) {
        $lunch_min = $_POST["lunch_min"];
        $_SESSION["lunch_min"] = $lunch_min;
    } else {
        $lunch_min = $_SESSION["lunch_min"];
    }
    if (isset($_POST["lunch_max"])) {
        $lunch_max = $_POST["lunch_max"];
        $_SESSION["lunch_max"] = $lunch_max;
    } else {
        $lunch_max = $_SESSION["lunch_max"];
    }
    if (isset($_POST["dinner_min"])) {
        $dinner_min = $_POST["dinner_min"];
        $_SESSION["dinner_min"] = $dinner_min;
    } else {
        $dinner_min = $_SESSION["dinner_min"];
    }
    if (isset($_POST["dinner_max"])) {
        $dinner_max = $_POST["dinner_max"];
        $_SESSION["dinner_max"] = $dinner_max;
    } else {
        $dinner_max = $_SESSION["dinner_max"];
    }

    if (isset($_POST["search_genre"])) {
        $search_genre = $_POST["search_genre"];
        $_SESSION["search_genre"] = $search_genre;
    } else {
        $search_genre = $_SESSION["search_genre"];
    }
    if (isset($_POST["search_name"])) {
        $search_name = $_POST["search_name"];
        $_SESSION["search_name"] = $search_name;
    } else {
        $search_name = $_SESSION["search_name"];
    }

    $keywordCondition = [];
    //posts = [["データベースのカラム名", "検索条件"]]
    $posts = [["wifi", $wifi], ["private_room", $private_room], ["credit_card", $credit_card], ["non_smoking", $non_smoking], ["lunch", $lunch], ["capacity", $capacity]];

    $search_word = strtr($search_name, [
        '\\' => '\\\\',
        '%' => '\%',
        '_' => '\_',
    ]);

    //値が0じゃないデータを　keywordCondition　に格納
    foreach ($posts as $post) {
        if (!($post[1] == "0")) {
            $column = $post[0];
            if ($post[0] == "capacity") {
                $keywordCondition[] =  " $column >= $post[1] ";
            } else {
                $keyword = $post[1];
                $keywordCondition[] =  " $column LIKE '%" . $keyword . "%' ";
            }
        }
    }
    //予算範囲
    if ($lunch_min != 0) {
        $keywordCondition[] =  " lunch_min >= $lunch_min";
        $keywordCondition[] =  " lunch_min <> -1";
    }
    if ($lunch_max != 999999) {
        $keywordCondition[] =  " lunch_max <= $lunch_max";
        $keywordCondition[] =  " lunch_max <> -1";
    }
    if ($dinner_min != 0) {
        $keywordCondition[] =  " dinner_min >= $dinner_min";
        $keywordCondition[] =  " dinner_min <> -1";
    }
    if ($dinner_max != 999999) {
        $keywordCondition[] =  " dinner_max <= $dinner_max";
        $keywordCondition[] =  " dinner_max <> -1";
    }
    //$keywordCondition[] =  " lunch_min >= $lunch_min AND lunch_max <= $lunch_max ";
    //$keywordCondition[] =  " dinner_min >= $dinner_min AND dinner_max <= $dinner_max ";
    //名前検索かジャンル検索か判定
    if ($search_genre == "0") {
        $column1 = "genre";
        $column2 = "genre_sub";
        $keywordCondition[] = "( $column1 LIKE '%" . $search_word . "%' OR $column2 LIKE '%" . $search_word . "%' )";
    } else {
        $column1 = "name";
        $keywordCondition[] = " $column1 LIKE '%" . $search_word . "%' ";
    }
    $keywordCondition[] =  " show >= 1 ";

    // ここで、 
    // [ 'product_name LIKE "%hoge%"', 
    //   'product_name LIKE "%fuga%"', 
    //   'product_name LIKE "%piyo%"' ]
    // という配列ができあがっている。

    // これをANDでつなげて、文字列にする
    $keywordCondition = implode(' AND ', $keywordCondition);
    //var_dump($keywordCondition);

    //sql文にする
    $sql = 'SELECT * FROM ' . $database_restaurants . ' WHERE ' . $keywordCondition . ' ';

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
} catch (PDOException $e) {
    echo "失敗:" . $e->getMessage() . "\n";
    exit();
}


//検索条件の保存のため
function set_checked($session_name, $value)
{
    if ($value == $_SESSION[$session_name]) {
        //値がセッション変数と等しいとチェックされてる判定として返す
        print "checked=\"checked\"";
    } else {
        print "";
    }
}
function set_selected($session_name, $value)
{
    if ($value == $_SESSION[$session_name]) {
        //値がセッション変数と等しいとチェックされてる判定として返す
        print "selected=\"selected\"";
    } else {
        print "";
    }
}

$count = 0;

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
    <title>飲食店の検索・決定（一覧表示）</title>
    <style>
        .search_result .table th {
            text-align: left;
            white-space: nowrap;
            background: #EEEEEE;
            width: 5vw;
        }

        .search_result .table td {
            background: #EEEEEE;
            padding: 3px;
        }

        @media screen and (min-width:769px) and (max-width:1366px) {}

        @media screen and (max-width:768px) {

            .search_form {
                font-size: 14px;
            }

            .search_result {
                font-size: 12px;
            }

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
            <div>
                <font color="#ff0000"><?php echo htmlspecialchars($message, ENT_QUOTES); ?></font>
            </div>
            <h3 class="px-0" id="search_start">飲食店の検索・決定</h3>
            <div>
                <ol class="stepBar">
                    <li class="visited" onclick="location.href='set_station.php'"><span>1</span><br>開始・終了駅</li>
                    <li class="visited" onclick="location.href='search_map.php'"><span>2</span><br>飲食店</li>
                    <li onclick="location.href='sightseeing_spots_selection_map.php'"><span>3</span><br>観光スポット</li>
                    <li onclick="location.href='plan_edit.php'"><span>4</span><br>観光計画を保存</li>
                </ol>
            </div>
            <a id="view_result" name="view_result" href="search_map.php">地図上で結果を表示</a><br>
            <div class="search_form">
                <form action="search.php" method="post">
                    Wi-Fi：
                    <input type="radio" id="wifi" name="wifi" value="0" <?php set_checked("wifi", "0"); ?>>指定なし
                    <input type="radio" id="wifi" name="wifi" value="あり" <?php set_checked("wifi", "あり"); ?>>あり
                    <input type="radio" id="wifi" name="wifi" value="なし" <?php set_checked("wifi", "なし"); ?>>なし<br>

                    個室：
                    <input type="radio" id="private_room" name="private_room" value="0" <?php set_checked("private_room", "0"); ?>>指定なし
                    <input type="radio" id="private_room" name="private_room" value="あり ：" <?php set_checked("private_room", "あり ："); ?>>あり
                    <input type="radio" id="private_room" name="private_room" value="なし ：" <?php set_checked("private_room", "なし ："); ?>>なし<br>

                    カード決済：
                    <input type="radio" id="credit_card" name="credit_card" value="0" <?php set_checked("credit_card", "0"); ?>>指定なし
                    <input type="radio" id="credit_card" name="credit_card" value="利用可" <?php set_checked("credit_card", "利用可"); ?>>利用可
                    <input type="radio" id="credit_card" name="credit_card" value="利用不可" <?php set_checked("credit_card", "利用不可"); ?>>利用不可<br>

                    禁煙席：
                    <input type="radio" id="non_smoking" name="non_smoking" value="0" <?php set_checked("non_smoking", "0"); ?>>指定なし
                    <input type="radio" id="non_smoking" name="non_smoking" value="全面禁煙" <?php set_checked("non_smoking", "全面禁煙"); ?>>全面禁煙
                    <input type="radio" id="non_smoking" name="non_smoking" value="一部禁煙" <?php set_checked("non_smoking", "一部禁煙"); ?>>一部禁煙
                    <input type="radio" id="non_smoking" name="non_smoking" value="禁煙席なし" <?php set_checked("non_smoking", "禁煙席なし"); ?>>禁煙席なし<br>

                    ランチメニュー：
                    <input type="radio" id="lunch" name="lunch" value="0" <?php set_checked("lunch", "0"); ?>>指定なし
                    <input type="radio" id="lunch" name="lunch" value="あり" <?php set_checked("lunch", "あり"); ?>>あり
                    <input type="radio" id="lunch" name="lunch" value="なし" <?php set_checked("lunch", "なし"); ?>>なし<br>

                    総席数：
                    <input type="number" value="<?php echo $capacity; ?>" id="capacity" name="capacity">～<br>

                    昼食の予算：
                    <select size="1" id="lunch_min" name="lunch_min" onchange="right_range(name)">
                        <option value="0" <?php set_selected("lunch_min", "0"); ?>> 指定なし </option>
                        <option value="501" <?php set_selected("lunch_min", "501"); ?>> 501円 </option>
                        <option value="1001" <?php set_selected("lunch_min", "1001"); ?>> 1001円 </option>
                        <option value="1501" <?php set_selected("lunch_min", "1501"); ?>> 1501円 </option>
                        <option value="2001" <?php set_selected("lunch_min", "2001"); ?>> 2001円 </option>
                        <option value="3001" <?php set_selected("lunch_min", "3001"); ?>> 3001円 </option>
                        <option value="4001" <?php set_selected("lunch_min", "4001"); ?>> 4001円 </option>
                        <option value="5001" <?php set_selected("lunch_min", "5001"); ?>> 5001円 </option>
                        <option value="7001" <?php set_selected("lunch_min", "7001"); ?>> 7001円 </option>
                        <option value="10001" <?php set_selected("lunch_min", "10001"); ?>> 10001円 </option>
                        <option value="15001" <?php set_selected("lunch_min", "15001"); ?>> 15001円 </option>
                        <option value="20001" <?php set_selected("lunch_min", "20001"); ?>> 20001円 </option>
                        <option value="30001" <?php set_selected("lunch_min", "30001"); ?>> 30001円 </option>
                    </select>
                    ～
                    <select size="1" id="lunch_max" name="lunch_max" onchange="right_range(name)">
                        <option value="999999" <?php set_selected("lunch_max", "999999"); ?>> 指定なし </option>
                        <option value="501" <?php set_selected("lunch_max", "501"); ?>> 501円 </option>
                        <option value="1001" <?php set_selected("lunch_max", "1001"); ?>> 1001円 </option>
                        <option value="1501" <?php set_selected("lunch_max", "1501"); ?>> 1501円 </option>
                        <option value="2001" <?php set_selected("lunch_max", "2001"); ?>> 2001円 </option>
                        <option value="3001" <?php set_selected("lunch_max", "3001"); ?>> 3001円 </option>
                        <option value="4001" <?php set_selected("lunch_max", "4001"); ?>> 4001円 </option>
                        <option value="5001" <?php set_selected("lunch_max", "5001"); ?>> 5001円 </option>
                        <option value="7001" <?php set_selected("lunch_max", "7001"); ?>> 7001円 </option>
                        <option value="10001" <?php set_selected("lunch_max", "10001"); ?>> 10001円 </option>
                        <option value="15001" <?php set_selected("lunch_max", "15001"); ?>> 15001円 </option>
                        <option value="20001" <?php set_selected("lunch_max", "20001"); ?>> 20001円 </option>
                        <option value="30001" <?php set_selected("lunch_max", "30001"); ?>> 30001円 </option>
                    </select><br>

                    夕食の予算：
                    <select size="1" id="dinner_min" name="dinner_min" onchange="right_range(name)">
                        <option value="0" <?php set_selected("dinner_min", "0"); ?>> 指定なし </option>
                        <option value="501" <?php set_selected("dinner_min", "501"); ?>> 501円 </option>
                        <option value="1001" <?php set_selected("dinner_min", "1001"); ?>> 1001円 </option>
                        <option value="1501" <?php set_selected("dinner_min", "1501"); ?>> 1501円 </option>
                        <option value="2001" <?php set_selected("dinner_min", "2001"); ?>> 2001円 </option>
                        <option value="3001" <?php set_selected("dinner_min", "3001"); ?>> 3001円 </option>
                        <option value="4001" <?php set_selected("dinner_min", "4001"); ?>> 4001円 </option>
                        <option value="5001" <?php set_selected("dinner_min", "5001"); ?>> 5001円 </option>
                        <option value="7001" <?php set_selected("dinner_min", "7001"); ?>> 7001円 </option>
                        <option value="10001" <?php set_selected("dinner_min", "10001"); ?>> 10001円 </option>
                        <option value="15001" <?php set_selected("dinner_min", "15001"); ?>> 15001円 </option>
                        <option value="20001" <?php set_selected("dinner_min", "20001"); ?>> 20001円 </option>
                        <option value="30001" <?php set_selected("dinner_min", "30001"); ?>> 30001円 </option>
                    </select>
                    ～
                    <select size="1" id="dinner_max" name="dinner_max" onchange="right_range(name)">
                        <option value="999999" <?php set_selected("dinner_max", "999999"); ?>> 指定なし </option>
                        <option value="501" <?php set_selected("dinner_max", "501"); ?>> 501円 </option>
                        <option value="1001" <?php set_selected("dinner_max", "1001"); ?>> 1001円 </option>
                        <option value="1501" <?php set_selected("dinner_max", "1501"); ?>> 1501円 </option>
                        <option value="2001" <?php set_selected("dinner_max", "2001"); ?>> 2001円 </option>
                        <option value="3001" <?php set_selected("dinner_max", "3001"); ?>> 3001円 </option>
                        <option value="4001" <?php set_selected("dinner_max", "4001"); ?>> 4001円 </option>
                        <option value="5001" <?php set_selected("dinner_max", "5001"); ?>> 5001円 </option>
                        <option value="7001" <?php set_selected("dinner_max", "7001"); ?>> 7001円 </option>
                        <option value="10001" <?php set_selected("dinner_max", "10001"); ?>> 10001円 </option>
                        <option value="15001" <?php set_selected("dinner_max", "15001"); ?>> 15001円 </option>
                        <option value="20001" <?php set_selected("dinner_max", "20001"); ?>> 20001円 </option>
                        <option value="30001" <?php set_selected("dinner_max", "30001"); ?>> 30001円 </option>
                    </select><br>

                    検索の設定：
                    <input type="radio" id="search_genre" name="search_genre" value="0" <?php set_checked("search_genre", "0"); ?>>ジャンルで検索
                    <input type="radio" id="search_genre" name="search_genre" value="1" <?php set_checked("search_genre", "1"); ?>>店名で検索<br>

                    検索ワード：
                    <input type="text" value="<?php echo $search_word; ?>" id="search_name" name="search_name">
                    <select name="genre_example" size="1" onchange="input_search_name(value)">
                        <option value=""> ワードを入力するか以下から選択してください </option>
                        <option value="中華"> 中華 </option>
                        <option value="和食"> 和食 </option>
                        <option value="洋食"> 洋食 </option>
                        <option value="イタリアン"> イタリアン </option>
                        <option value="フレンチ"> フレンチ </option>
                        <option value="居酒屋"> 居酒屋 </option>
                        <option value="バイキング"> バイキング </option>
                        <option value="カフェ"> カフェ </option>
                    </select>
                    <br>
                    <input type="submit" name="submit" value="検索する">
                </form>
            </div><br>
            <!--
            <div class="move_box">
                <a class="prev_page" name="prev_station" href="set_station.php">開始・終了駅選択に戻る</a>
                <a class="next_page" name="next_keiro" href="sightseeing_spots_selection_map.php">観光スポット選択へ</a><br>
            </div><br>
            -->
            <?php foreach ($stmt as $row) : ?>
                <?php $count += 1; ?>
                <div class="card bg-light mb-3" style="width: 80rem;" id="infobox" value=<?php echo $row["id"]; ?>>
                    <div class="row g-0">
                        <div class="col-md-5">
                            <img class="img-fluid rounded-start" src=<?php echo "images/$area_name/restaurants/" . $row["id"] . ".jpg" ?> alt="">
                        </div>
                        <div class="search_result col-md-12">
                            <table class="table card-body">
                                <tr>
                                    <th>店舗名</th>
                                    <td><?php echo $row["name"]; ?></td>
                                </tr>
                                <tr>
                                    <th>ジャンル</th>
                                    <td><?php echo $row["genre"]; ?>、<?php echo $row["genre_sub"]; ?></td>
                                </tr>
                                <tr>
                                    <th>営業時間</th>
                                    <td><?php echo nl2br($row["open_time"]); ?></td>
                                </tr>
                                <tr>
                                    <th>定休日</th>
                                    <td><?php echo nl2br($row["close_time"]); ?></td>
                                </tr>
                                <tr>
                                    <th>予算</th>
                                    <td>昼：<?php if ($row["lunch_budget"]) {
                                                echo $row["lunch_budget"];
                                            } else {
                                                echo "不明";
                                            } ?>　　夜：<?php echo $row["dinner_budget"]; ?></td>
                                </tr>
                                <tr>
                                    <th>総席数</th>
                                    <td><?php echo nl2br($row["capacity"]); ?>席</td>
                                </tr>
                                <tr>
                                    <th>禁煙席</th>
                                    <td><?php echo nl2br($row["non_smoking"]); ?>席</td>
                                </tr>
                                <tr>
                                    <th>ランチメニュー</th>
                                    <td><?php echo $row["lunch"]; ?></td>
                                </tr>
                                <tr>
                                    <th>ホームページURL</th>
                                    <td>
                                        <?php
                                        if (!empty($row["urls"])) {
                                            print "<a href = " . $row["urls"] . " target=_blank>ホームページにアクセスする</a>";
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>地図付き詳細ページへ</th>
                                    <td>
                                        <a href="restaurant_detail.php?restaurant_id=<?php echo $row["id"]; ?>">詳細ページに移動する</a>
                                    </td>
                                </tr>
                            </table>
                            <a href="#search_start">▲ページ上部に戻る</a>
                        </div>
                    </div>
                </div><br>
            <?php endforeach; ?>
            <?php
            if (!$count) {
                echo "検索条件に該当する飲食店はありませんでした";
            }
            ?>
            <br>
        </main>
        <footer>
            <p>Copyright(c) 2023 山本佳世子研究室 All Rights Reserved.</p>
        </footer>
    </div>
</body>

</html>
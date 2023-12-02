<?php

//ページを跨いで共有する変数
/*
$_SESSION["user_id"]
$_SESSION["user_name"]
$_SESSION["age"]
$_SESSION["gender"]

$_SESSION["start_station_id"]
$_SESSION["goal_station_id"]

$_SESSION["lunch_id"]
$_SESSION["dinner_id"]
$_SESSION["lunch_time"]
$_SESSION["dinner_time"]

$_SESSION["s_l_spots"]
$_SESSION["l_d_spots"]
$_SESSION["d_g_spots"]
array[[spot_id,time],[spot_id,time],[spot_id,time]]

$_SESSION["start_time"]
$_SESSION["goal_time"]
*/

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION["user_name"])) {
    header("Location: logout.php");
    exit;
}

if (isset($_SESSION["start_station_id"])) {
    $plan_start_station_id = $_SESSION["start_station_id"];
} else {
    $plan_start_station_id = -1;
}
if (isset($_SESSION["goal_station_id"])) {
    $plan_goal_station_id = $_SESSION["goal_station_id"];
} else {
    $plan_goal_station_id = -1;
}
if (isset($_SESSION["start_time"])) {
    $plan_start_time = $_SESSION["start_time"];
} else {
    $plan_start_time = "10:00";
}
if (isset($_SESSION["goal_time"])) {
    $plan_goal_time = $_SESSION["goal_time"];
} else {
    $plan_goal_time = "17:00";
}

if (isset($_SESSION["lunch_id"])) {
    $plan_lunch_id = $_SESSION["lunch_id"];
} else {
    $plan_lunch_id = -1;
}
if (isset($_SESSION["dinner_id"])) {
    $plan_dinner_id = $_SESSION["dinner_id"];
} else {
    $plan_dinner_id = -1;
}
if (isset($_SESSION["lunch_time"])) {
    $plan_lunch_time = $_SESSION["lunch_time"];
} else {
    $plan_lunch_time = 30;
}
if (isset($_SESSION["dinner_time"])) {
    $plan_dinner_time = $_SESSION["dinner_time"];
} else {
    $plan_dinner_time = 30;
}

if (isset($_SESSION["s_l_spots"])) {
    $plan_s_l_spots = $_SESSION["s_l_spots"];
} else {
    $plan_s_l_spots = [[-1, 0, "設定されていません"]];
}
if (isset($_SESSION["l_d_spots"])) {
    $plan_l_d_spots = $_SESSION["l_d_spots"];
} else {
    $plan_l_d_spots = [[-1, 0, "設定されていません"]];
}
if (isset($_SESSION["d_g_spots"])) {
    $plan_d_g_spots = $_SESSION["d_g_spots"];
} else {
    $plan_d_g_spots = [[-1, 0, "設定されていません"]];
}

$making_plan = [
    ["start", $plan_start_station_id, $plan_start_time],
    ["s_l", $plan_s_l_spots],
    ["lunch", $plan_lunch_id, $plan_lunch_time],
    ["l_d", $plan_l_d_spots],
    ["dinner", $plan_dinner_id, $plan_dinner_time],
    ["d_g", $plan_d_g_spots],
    ["goal", $plan_goal_station_id, $plan_goal_time]
];
//var_dump($plan_s_l_spots);

$area = 1;
//DB接続
require "connect_database.php";

//利用するデータベースを選択
if ($area == 1) {
    //$area = 1;
    $area_name = "minatomirai";
    $center = [139.635, 35.453];
    $database_stations = "minatomirai_stations";
    $database_restaurants = "minatomirai_restaurants";
    $database_sightseeing_spots = "minatomirai_sightseeing_spots";
    $map_stations = "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/gis_minatomirai_stations/FeatureServer";
    $map_restaurants = "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/gis_minatomirai_restaurants/FeatureServer";
    $map_sightseeing_spots = "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/gis_minatomirai_sightseeing_spots/FeatureServer";
} else if ($area == 2) {
    //$area = 2;
    $area_name = "hasune";
    $center = [139.6790835, 35.78443256];
    $database_stations = "hasune_stations";
    $database_restaurants = "hasune_restaurants";
    $database_sightseeing_spots = "hasune_sightseeing_spots";
    $map_stations = "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/gis_hasune_stations/FeatureServer";
    $map_restaurants = "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/gis_hasune_restaurants/FeatureServer";
    $map_sightseeing_spots = "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/gis_hasune_sightseeing_spots/FeatureServer";
} else if ($area == 3) {
    //$area = 3;
    $area_name = "chofu";
    $center = [139.5436966, 35.65780459];
    $database_stations = "chofu_stations";
    $database_restaurants = "chofu_restaurants";
    $database_sightseeing_spots = "chofu_sightseeing_spots";
    $map_stations = "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/gis_chofu_stations/FeatureServer";
    $map_restaurants = "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/gis_chofu_restaurants/FeatureServer";
    $map_sightseeing_spots = "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/gis_hasune_sightseeing_spots/FeatureServer";
}

/*
$area = 2;
$center = [139.635, 35.453];
$database_stations = "hasune_stations";
$database_restaurants = "hasune_restaurants";
$database_sightseeing_spots = "hasune_sightseeing_spots";
$map_stations = "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/gis_hasune_stations/FeatureServer";
$map_restaurants = "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/gis_hasune_restaurants/FeatureServer";
$map_sightseeing_spots = "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/gis_minatomirai_sightseeing_spots/FeatureServer";
*/
//$database_restaurants ="hasune_restaurants";
//$map_stations = "";
//$map_restaurants = "";
//$map_sightseeing_spots = "";

try {

    $framestmt = $pdo->prepare("SELECT * FROM userinfo WHERE user_name = :user_name");
    $framestmt->bindParam(":user_name", $_SESSION["user_name"]);
    $framestmt->execute();
    $frameresult = $framestmt->fetch(PDO::FETCH_ASSOC);

    //
    $framestmt1 = $pdo->prepare("SELECT * FROM $database_restaurants WHERE id = :id");
    $framestmt1->bindParam(":id", $_SESSION["lunch_id"]);
    $framestmt1->execute();
    $frameresult1 = $framestmt1->fetch(PDO::FETCH_ASSOC);

    $framestmt2 = $pdo->prepare("SELECT * FROM $database_restaurants WHERE id = :id");
    $framestmt2->bindParam(":id", $_SESSION["dinner_id"]);
    $framestmt2->execute();
    $frameresult2 = $framestmt2->fetch(PDO::FETCH_ASSOC);

    $framestmt3 = $pdo->prepare("SELECT * FROM $database_stations WHERE id = :id");
    $framestmt3->bindParam(":id", $_SESSION["start_station_id"]);
    $framestmt3->execute();
    $frameresult3 = $framestmt3->fetch(PDO::FETCH_ASSOC);

    $framestmt4 = $pdo->prepare("SELECT * FROM $database_stations WHERE id = :id");
    $framestmt4->bindParam(":id", $_SESSION["goal_station_id"]);
    $framestmt4->execute();
    $frameresult4 = $framestmt4->fetch(PDO::FETCH_ASSOC);


    //謎 データベース接続するとセッション変数の配列の値が「Array」に変わってしまう不具合があった
    if (!isset($_SESSION["s_l_sightseeing_spots_id"])) {
        $s_l_spots_name = [["設定されていません", 0]];
    } else {
        foreach ($_SESSION["s_l_sightseeing_spots_id"] as $s_l) {
            $framestmt5 = $pdo->prepare("SELECT * FROM $database_sightseeing_spots WHERE id = :id");
            $framestmt5->bindParam(":id", $s_l);
            $framestmt5->execute();
            $frameresult5 = $framestmt5->fetch(PDO::FETCH_ASSOC);
            //$spot_count +=1;
            $s_l_spots_name[] = [$frameresult5["name"], $s_l];
        }
    }

    if (!isset($_SESSION["l_d_sightseeing_spots_id"])) {
        $l_d_spots_name = [["設定されていません", 0]];
    } else {
        foreach ($_SESSION["l_d_sightseeing_spots_id"] as $l_d) {
            $framestmt6 = $pdo->prepare("SELECT * FROM $database_sightseeing_spots WHERE id = :id");
            $framestmt6->bindParam(":id", $l_d);
            $framestmt6->execute();
            $frameresult6 = $framestmt6->fetch(PDO::FETCH_ASSOC);
            //$spot_count +=1;
            $l_d_spots_name[] = [$frameresult6["name"], $l_d];
        }
    }

    if (!isset($_SESSION["d_g_sightseeing_spots_id"])) {
        $d_g_spots_name = [["設定されていません", 0]];
    } else {
        foreach ($_SESSION["d_g_sightseeing_spots_id"] as $d_g) {
            $framestmt7 = $pdo->prepare("SELECT * FROM $database_sightseeing_spots WHERE id = :id");
            $framestmt7->bindParam(":id", $d_g);
            $framestmt7->execute();
            $frameresult7 = $framestmt7->fetch(PDO::FETCH_ASSOC);
            //$spot_count +=1;
            $d_g_spots_name[] = [$frameresult7["name"], $d_g];
        }
    }
    //
} catch (PDOException $e) {
}

//セッション変数を定義
$_SESSION["user_id"] = $frameresult["id"];


//表示の初期値を設定
if (!isset($_SESSION["lunch_id"])) {
    $lunch_name = "昼食地点を設定してください";
} else {
    $lunch_name = $frameresult1["name"];
}
if (!isset($_SESSION["dinner_id"])) {
    $dinner_name = "夕食地点を設定してください";
} else {
    $dinner_name = $frameresult2["name"];
}

if (!isset($_SESSION["start_station_id"])) {
    $start_station_name = "開始駅を設定してください";
} else {
    $start_station_name = $frameresult3["name"];
}
if (!isset($_SESSION["goal_station_id"])) {
    $goal_station_name = "終了駅を設定してください";
} else {
    $goal_station_name = $frameresult4["name"];
}


function display_frame($name_row, $time)
{
    $count = 0;
    foreach ($name_row as $spot_name) {
        $count += 1;
        $frame_spot_name = " " . $count . ":" . $spot_name[0] . " ";
        print "
    <div id=\"frame_spot_name\">$frame_spot_name</div>
    <button class=\"btn2\" type=\"button\" id=\"removebtn\" value=$spot_name[1] onclick=\"remove_spot($time, value)\" title=\"このスポットを削除します\">×</button>
    <button type=\"button\" id=\"swapupbtn\" value=$spot_name[1] onclick=\"swap_spots($time, value, 'up')\" title=\"このスポットを一つ上に移動します\">↑</button>
    <button type=\"button\" id=\"swapdownbtn\" value=$spot_name[1] onclick=\"swap_spots($time, value, 'down')\" title=\"このスポットを一つ下に移動します\">↓</button><br>
    ";
    };
};


?>

<!doctype html>
<html>

<code class="code-multiline">
    <!-- jQuery読み込み -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <!-- Propper.js読み込み -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <!-- BootstrapのJavascript読み込み -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
</code>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.jp/docs/5.3/assets/css/docs.css" rel="stylesheet">
    <title>Bootstrap Example</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <link rel="stylesheet" type="text/css" href="css/copyright.css">
    <link rel="stylesheet" type="text/css" href="css/viewbox.css?<?php echo date('YmdHis'); ?>">
    <style>
    h1 {
        margin: 0px;
    }

    .search_form {
        line-height: 200%;
    }

    #dropmenu {
        list-style-type: none;
        position: relative;
        width: 77vw;
        height: 35px;
        padding: 0;
        background: #0099ff;
        border-bottom: 5px solid #00ffff;
        border-radius: 3px 3px 0 0;
        z-index: 3;
    }

    #dropmenu li {
        position: relative;
        width: 16.665%;
        float: left;
        margin: 0;
        padding: 0;
        text-align: center;
        border-right: 1px solid #99ffff;
        box-sizing: border-box;
    }

    #dropmenu li a {
        display: block;
        margin: 0;
        padding: 13px 0 11px;
        color: #FFFFFF;
        font-size: 17px;
        font-weight: bold;
        line-height: 1;
        text-decoration: none;
    }

    #dropmenu li ul {
        list-style: none;
        position: absolute;
        top: 100%;
        left: 0;
        margin: 0;
        padding: 0;
        border-radius: 0 0 3px 3px;
    }

    #dropmenu li ul li {
        overflow: hidden;
        width: 100%;
        height: 0;
        color: #fff;
        -moz-transition: .2s;
        -webkit-transition: .2s;
        -o-transition: .2s;
        -ms-transition: .2s;
        transition: .2s;
    }

    #dropmenu li ul li a {
        padding: 6px 8px;
        background: #0099FF;
        text-align: left;
        font-size: 15px;
        font-weight: normal;
    }

    #dropmenu li:hover>a {
        background: #0066ff;
    }

    #dropmenu>li:hover>a {
        border-radius: 3px 3px 0 0;
    }

    #dropmenu li:hover ul li {
        overflow: visible;
        height: 30px;
        border-bottom: 3px solid #0066ff;
        border-right: 0px;
    }

    #dropmenu li:hover ul li:last-child a {
        border-radius: 0 0 3px 3px;
    }

    #leftbox {
        position: relative;
        top: -70px;
        float: right;
        width: 20vw;
        border-right: 3px solid #0099FF;
        z-index: 2;
    }

    #leftbox h2 {
        background: #0099FF;
        color: #FFFFFF;
        margin-right: 5px;
        border-left: 5px solid #000080;
    }

    #leftbox p {
        margin-left: 10px;
    }

    #leftbox #sightseeing_plan {
        width: 15vw;
    }

    @media screen and (min-width:769px) {
        #toggle_menu {
            display: none;
        }
    }

    @media screen and (min-width:769px) and (max-width:1366px) {
        h1 {
            font-size: 25px;
        }

        #dropmenu {
            width: 77vw;
            height: 30px;
            border-bottom: 4px solid #000080;
        }

        #dropmenu li a {
            padding: 7px 0 9px;
            font-size: 16px;
        }

        #dropmenu li ul li a {
            padding: 4px 6px;
            font-size: 13px;
        }

        #dropmenu li:hover ul li {
            height: 23px;
            border-bottom: 2px solid #000080;
        }

        #leftbox h2 {
            background: #0099FF;
            color: #FFFFFF;
            margin-right: 4px;
            border-left: 4px solid #000080;
            font-size: 17px;
        }
    }

    @media screen and (max-width:768px) {
        h1 {
            font-size: 22px;
        }

        h2 {
            margin: 0px;
            font-size: 19px;
        }

        #dropmenu {
            display: none;
        }

        #leftbox {
            display: none;
        }

        #survey_box {
            display: none;
        }

        #making_planbox {
            font-size: 2vw;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            border: 1px solid black;
            padding: 20px;
            z-index: 1000;
        }

        #toggle_menu {
            padding: 0px;
            margin-bottom: 5px;
            border-bottom: 1px solid #000000;
        }

        #toggle_menu label {
            font-weight: bold;
            border: solid 2px black;
            cursor: pointer;
        }

        #toggle_menu>input {
            display: none;
        }

        #toggle_menu #menu {
            height: 0;
            padding: 0;
            overflow: hidden;
            opacity: 0;
            transition: 0.2s;
        }

        #toggle_menu input:checked~#menu {
            height: auto;
            opacity: 1;
        }

    }

    .sortable ul {
        list-style: none;
        padding: 0;
    }

    .sortable li {
        cursor: pointer;
        border: 1px solid;
    }

    .hidden {
        background: #808080;
    }
    </style>
</head>

<body>
    <!-- <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> -->
    <script>
    //leftの情報を上書きする
    function update_frame(data, id) {
        const update = document.getElementById(id);
        update.innerHTML = data;
        //console.log(update.innerHTML);
    }

    //観光計画からスポットを削除
    function hidden_spot(name) {
        var name_tag = document.getElementById(name);
        if (name_tag.className != "hidden") {
            name_tag.className = "hidden";
            name_tag.querySelector(".btn").textContent = "戻す";
        } else {
            name_tag.className = "";
            name_tag.querySelector(".btn").textContent = "削除";
        }

    };
    </script>

    <h1>横浜みなとみらいフードツーリズム計画作成支援システム</h1>

    <ul id="dropmenu">
        <li><a href="home.php">ホーム</a></li>

        <li><a href="explain.php">使い方</a></li>

        <li><a>観光計画作成</a>
            <ul>
                <li><a href="set_station.php">開始・終了駅の設定</a></li>
                <li><a href="search.php">飲食店の検索・決定</a></li>
                <li><a id="keiro" name="keiro" href="sightseeing_spots_selection_map.php">観光スポット選択</a></li>
            </ul>
        </li>

        <li><a>観光支援</a>
            <ul>
                <li><a href="search_nearby_restaurants_map.php">周辺スポットの検索</a></li>
                <li><a href="navigation_map.php">ナビゲーション</a></li>
            </ul>
        </li>

        <li><a>一覧</a>
            <ul>
                <li><a href="view.php">スポット一覧</a></li>
                <li><a href="users_plans.php">他のユーザーが作成した観光計画</a></li>
            </ul>
        </li>

        <li><a>マイページ</a>
            <ul>
                <li><a id="see_myroute" name="see_myroute" href="plan_edit.php">作成中の観光計画を見る</a></li>
                <li><a id="see_myroute" name="see_myroute" href="user_plans.php">保存した観光計画を見る</a></li>
                <li><a id="see_myroute" name="see_myroute" href="user_plans.php">観光記録</a></li>
                <li><a href="my_page.php">登録情報変更</a></li>
                <li><a href="logout.php">ログアウト</a></li>
            </ul>
        </li>

    </ul>

    <div id="leftbox">
        <div id="userdata_box">
            <h2>会員情報</h2>

            <b>名前:</b> <?php echo htmlspecialchars($_SESSION["user_name"], ENT_QUOTES); ?><br>

            <b>年代:</b> <?php if (!$frameresult["age"]) { ?>
            未回答
            <?php } else {
                            echo htmlspecialchars($frameresult["age"], ENT_QUOTES); ?>代 <?php } ?><br>

            <b>性別:</b> <?php echo htmlspecialchars($frameresult["gender"], ENT_QUOTES); ?><br>
        </div>

        <h2>現在の観光計画</h2>
        <button onclick="remade_plan()">更新する</button><br>
        <div id="making_plan_box">
            <div class="sortable">
                開始駅<br>
                <ul>
                    <li id="plan_start_box" value="<?php echo $making_plan[0][1]; ?>">
                        <img id="pin" width="20" height="20" src="./icons/pop_start.png" alt="開始駅のアイコン" title="開始駅">
                        <?php echo $start_station_name ?><br>
                    </li>
                </ul>
            </div>
            <div class="sortable">
                昼食前に訪れる観光スポット<br>
                <ul id="sort">
                    <?php $count_s_l = 0; ?>
                    <?php foreach ($plan_s_l_spots as $date) { ?>
                    <?php $count_s_l += 1; ?>
                    <li value=<?php echo $date[0] ?> id=<?php echo "plan_s_l_" . $count_s_l . "_box"; ?>
                        draggable="true">
                        <img class="pin_s_l" width="20" height="20"
                            src=<?php echo "./icons/pop_icon_s_l" . $count_s_l . ".png"; ?> alt="昼食前に訪れる観光スポットのアイコン"
                            title="昼食前に訪れる観光スポット">
                        <div class="s_l_name"><?php echo $date[2] ?></div>
                        <input class="s_l_time" type="number" value="<?php echo $date[1]; ?>">分
                        <button type="button" class="btn btn-light btn-outline-dark"
                            value=<?php echo "plan_s_l_" . $count_s_l . "_box"; ?>
                            onclick="hidden_spot(value)">削除</button>
                    </li>
                    <?php } ?>
                </ul>
                <input type="hidden" id="list-ids" name="list-ids" />
            </div>
            <div class="sortable">
                昼食を食べる飲食店<br>
                <ul>
                    <li id="plan_lunch_box" value="<?php echo $making_plan[2][1]; ?>">
                        <img id="pin" width="20" height="20" src="./icons/pop_lunch.png" alt="昼食予定地のアイコン" title="昼食予定地">
                        <?php echo $lunch_name ?><br>
                        <input class="time" type="number" value="<?php echo $making_plan[2][2]; ?>">分
                        <button type="button" class="btn btn-light btn-outline-dark" value=""
                            onclick="hidden_spot('plan_lunch_box')">削除</button>
                    </li>
                </ul>
            </div>
            <div class="sortable">
                昼食後に訪れる観光スポット<br>
                <ul id="sort2">
                    <?php $count_l_d = 0; ?>
                    <?php foreach ($plan_l_d_spots as $date) { ?>
                    <?php $count_l_d += 1; ?>
                    <li value=<?php echo $date[0] ?> id=<?php echo "plan_l_d_" . $count_l_d . "_box"; ?>
                        draggable="true">
                        <img class="pin_l_d" width="20" height="20"
                            src=<?php echo "./icons/pop_icon_l_d" . $count_l_d . ".png"; ?> alt="昼食後に訪れる観光スポットのアイコン"
                            title="昼食後に訪れる観光スポット">
                        <div class="l_d_name"><?php echo $date[2] ?></div>
                        <input class="l_d_time" type="number" value="<?php echo $date[1]; ?>">分
                        <button type="button" class="btn btn-light btn-outline-dark"
                            value=<?php echo "plan_l_d_" . $count_l_d . "_box"; ?>
                            onclick="hidden_spot(value)">削除</button>
                    </li>
                    <?php } ?>
                </ul>
                <input type="hidden" id="list-ids" name="list-ids" />
            </div>
            <div class="sortable">
                夕食を食べる飲食店<br>
                <ul>
                    <li id="plan_dinner_box" value="<?php echo $making_plan[4][1]; ?>">
                        <img id="pin" width="20" height="20" src="./icons/pop_dinner.png" alt="夕食予定地のアイコン"
                            title="夕食予定地">
                        <?php echo $dinner_name ?><br>
                        <input class="time" type="number" value="<?php echo $making_plan[4][2]; ?>">分
                        <button type="button" class="btn btn-light btn-outline-dark" value=""
                            onclick="hidden_spot('plan_dinner_box')">削除</button>
                    </li>
                </ul>
            </div>
            <div class="sortable">
                夕食後に訪れる観光スポット<br>
                <ul id="sort3">
                    <?php $count_d_g = 0; ?>
                    <?php foreach ($plan_d_g_spots as $date) { ?>
                    <?php $count_d_g += 1; ?>
                    <li value=<?php echo $date[0] ?> id=<?php echo "plan_d_g_" . $count_d_g . "_box"; ?>
                        draggable="true">
                        <img class="pin_d_g" width="20" height="20"
                            src=<?php echo "./icons/pop_icon_d_g" . $count_d_g . ".png"; ?> alt="夕食後に訪れる観光スポットのアイコン"
                            title="夕食後に訪れる観光スポット">
                        <div class="d_g_name"><?php echo $date[2] ?></div>
                        <input class="d_g_time" type="number" value="<?php echo $date[1]; ?>">分
                        <button type="button" class="btn btn-light btn-outline-dark"
                            value=<?php echo "plan_d_g_" . $count_d_g . "_box"; ?>
                            onclick="hidden_spot(value)">削除</button>
                    </li>
                    <?php } ?>
                </ul>
                <input type="hidden" id="list-ids" name="list-ids" />
            </div>
            <div class="sortable">
                終了駅<br>
                <ul>
                    <li id="plan_goal_box" value="<?php echo $making_plan[6][1] ?>">
                        <img id="pin" width="20" height="20" src="./icons/pop_goal.png" alt="終了駅のアイコン" title="終了駅">
                        <div class="plan_goal_name"><?php echo $goal_station_name ?></div>
                    </li>
                </ul>
            </div>
        </div>

        <div id="survey_box">
            <h2>アンケート</h2>
            <p>
                <?php
                print "アンケートの回答を締め切りました。ご回答くださった方々、誠にありがとうございました。";
                /*
                if ($frameresult["survey"]) {
                    print "<form action=\"\" method=\"POST\">";
                    print "<input type=\"submit\" id=\"survey\" name=\"survey\" value=\"回答する\" onClick=\"window.open('https://forms.gle/amw8j1wJDPcAn29h7?openExternalBrowser=1','_blank')\"><br>";
                    print "</form>";
                    print "回答は<font color=\"red\">1回</font>のみです<br>";
                    print "<b>システムを1度以上利用してからご回答ください</b>";
                } else {
                    print "ご回答ありがとうございました";
                }
                */
                ?>
            </p>
        </div>

    </div>

    <div id="toggle_menu">
        <div class="navbar navbar-expand-lg bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">メニューバー</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvas"
                    aria-controls="offcanvas">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvas" aria-labelledby="offcanvasLabel">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title">Menu</h5>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        <ul class="navbar-nav">
                            <li class="nav-item"><a href="home.php">ホーム</a></li>
                            <li class="nav-item"><a href="explain.php">使い方</a></li>
                            <li class="nav-item"><a href="view.php">スポット一覧</a></li>

                            <li class="nav-item"><a>観光計画作成</a>
                                <ul>
                                    <li class="nav-item"><a href="set_station.php">開始・終了駅の設定</a></li>
                                    <li class="nav-item"><a href="search.php">飲食店の検索・決定</a></li>
                                    <li class="nav-item"><a id="toggle_keiro" name="toggle_keiro"
                                            href="sightseeing_spots_selection_map.php">観光スポット選択</a></li>
                                </ul>
                            </li>

                            <li class="nav-item"><a>観光支援</a>
                                <ul>
                                    <li class="nav-item"><a href="search_nearby_restaurants_map.php">周辺スポットの検索</a></li>
                                    <li class="nav-item"><a href="navigation_map.php">ナビゲーション</a></li>
                                </ul>
                            </li>

                            <li class="nav-item"><a>マイページ</a>
                                <ul>
                                    <li class="nav-item"><a id="toggle_see_myroute" name="toggle_see_myroute"
                                            href="see_myroute.php">作成した観光計画を見る</a></li>
                                    <li class="nav-item"><a href="my_page.php">登録情報変更</a></li>
                                    <li class="nav-item"><a href="logout.php">ログアウト</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <label for="menu_label">≡メニュー</label>
        <input type="checkbox" id="menu_label" />

        <div id="menu">
            <ul>
                <li><a href="home.php">ホーム</a></li>

                <li><a href="explain.php">使い方</a></li>

                <li><a>観光計画作成</a>
                    <ul>
                        <li><a href="set_station.php">開始・終了駅の設定</a></li>
                        <li><a href="search.php">飲食店の検索・決定</a></li>
                        <li><a id="toggle_keiro" name="toggle_keiro"
                                href="sightseeing_spots_selection_map.php">観光スポット選択</a></li>
                    </ul>
                </li>

                <li><a>観光支援</a>
                    <ul>
                        <li><a href="search_nearby_restaurants_map.php">周辺スポットの検索</a></li>
                        <li><a href="navigation_map.php">ナビゲーション</a></li>
                    </ul>
                </li>

                <li><a href="view.php">スポット一覧</a></li>

                <li><a>マイページ</a>
                    <ul>
                        <li><a id="toggle_see_myroute" name="toggle_see_myroute" href="see_myroute.php">作成した観光計画を見る</a>
                        </li>
                        <li><a href="my_page.php">登録情報変更</a></li>
                        <li><a href="logout.php">ログアウト</a></li>
                    </ul>
                </li>

            </ul>
        </div>
    </div>

    <div id="toggle_menu">
        <label for="menu_label2">≡設定情報</label>
        <input type="checkbox" id="menu_label2" />

        <div id="menu">
            <ul>
                <li>
                    <h2>会員情報</h2>
                    <ul>
                        <li><b>名前:</b> <?php echo htmlspecialchars($_SESSION["user_name"], ENT_QUOTES); ?></li>

                        <li><b>年代:</b> <?php if (!$frameresult["age"]) { ?>未回答
                            <?php } else {
                                            echo htmlspecialchars($frameresult["age"], ENT_QUOTES); ?>代 <?php } ?></li>

                        <li><b>性別:</b> <?php echo htmlspecialchars($frameresult["gender"], ENT_QUOTES); ?></li>
                    </ul>
                </li>

                <li>
                    <h2>現在の観光計画</h2>
                    <ul>
                        <li><b>開始駅:</b>
                            <img id="pin" width="20" height="20" src="./icons/pop_start.png" alt="開始駅のアイコン" title="開始駅">
                            <div id="start_name"><?php echo htmlspecialchars($start_station_name, ENT_QUOTES); ?></div>
                        </li><br>

                        <li>
                            <b>昼食前に訪れる観光スポット:</b>
                            <img id="pin" width="20" height="20" src="./markers/pop_icon1_f.png"
                                alt="昼食前に訪れる観光スポットのアイコン" title="昼食前に訪れる観光スポット">
                            <div id="toggle_s_l_spots_line">
                                <?php display_frame($s_l_spots_name, 1) ?>
                            </div>
                        </li><br>

                        <li><b>昼食予定地:</b>
                            <img id="pin" width="20" height="20" src="./icons/pop_lunch.png" alt="昼食予定地のアイコン"
                                title="昼食予定地">
                            <div id="lunch_name"><?php echo htmlspecialchars($lunch_name, ENT_QUOTES); ?></div>
                        </li><br>

                        <li>
                            <b>昼食後に訪れる観光スポット:</b>
                            <img id="pin" width="20" height="20" src="./markers/pop_icon2_f.png"
                                alt="昼食後に訪れる観光スポットのアイコン" title="昼食後に訪れる観光スポット">
                            <div id="toggle_l_d_spots_line">
                                <?php display_frame($l_d_spots_name, 2) ?>
                            </div>
                        </li><br>

                        <li><b>夕食予定地:</b>
                            <img id="pin" width="20" height="20" src="./icons/pop_dinner.png" alt="夕食予定地のアイコン"
                                title="夕食予定地">
                            <div id="dinner_name"><?php echo htmlspecialchars($dinner_name, ENT_QUOTES); ?></div>
                        </li><br>

                        <li>
                            <b>夕食前に訪れる観光スポット:</b>
                            <img id="pin" width="20" height="20" src="./markers/pop_icon3_f.png"
                                alt="夕食後に訪れる観光スポットのアイコン" title="夕食後に訪れる観光スポット">
                            <div id="toggle_d_g_spots_line">
                                <?php display_frame($d_g_spots_name, 3) ?>
                            </div>
                        </li><br>

                        <li><b>終了駅:</b>
                            <img id="pin" width="20" height="20" src="./icons/pop_goal.png" alt="終了駅のアイコン" title="終了駅">
                            <div id="goal_name"><?php echo htmlspecialchars($goal_station_name, ENT_QUOTES); ?></div>
                        </li>
                    </ul>
                </li>

                <li>
                    <h2>アンケート</h2>
                    <?php
                    print "アンケートの回答を締め切りました。ご回答くださった方々、誠にありがとうございました。";
                    /*
                    if ($frameresult["survey"]) {
                        print "<form action=\"\" method=\"POST\">";
                        print "<input type=\"submit\" id=\"survey\" name=\"survey\" value=\"回答する\" onClick=\"window.open('https://forms.gle/amw8j1wJDPcAn29h7?openExternalBrowser=1','_blank')\"><br>";
                        print "</form>";
                        print "回答は<font color=\"red\">1回</font>のみです<br>";
                        print "<b>システムを1度以上利用してからご回答ください</b>";
                    } else {
                        print "ご回答ありがとうございました";
                    }
                    */
                    ?>
                </li>

            </ul>
        </div>
    </div>
</body>

<!-- ドラッグアンドドロップを実装する用 -->
<script src="//cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script src="Sortable.min.js"></script>
<script>
var el = document.getElementById('sort');
var sortable = Sortable.create(el, {
    onSort: function(evt) {
        var items = el.querySelectorAll('li');
        for (var i = 0; i < items.length; i++) {
            //items[i].querySelector('.no_s_l').value = i + 1;
            //items[i].querySelector('.no_s_l').id = `s_l_${i + 1}`
            var src = `./icons/pop_icon_s_l${i + 1}.png`;
            items[i].querySelector('.pin_s_l').src = src;
        }
    }
});
var el2 = document.getElementById('sort2');
var sortable2 = Sortable.create(el2, {
    onSort: function(evt) {
        var items = el2.querySelectorAll('li');
        for (var i = 0; i < items.length; i++) {
            //items[i].querySelector('.no_l_d').value = i + 1;
            var src = `./icons/pop_icon_l_d${i + 1}.png`;
            items[i].querySelector('.pin_l_d').src = src;
        }
    }
});
var el3 = document.getElementById('sort3');
var sortable3 = Sortable.create(el3, {
    onSort: function(evt) {
        var items = el3.querySelectorAll('li');
        for (var i = 0; i < items.length; i++) {
            //items[i].querySelector('.no_d_g').value = i + 1;
            var src = `./icons/pop_icon_d_g${i + 1}.png`;
            items[i].querySelector('.pin_d_g').src = src;
        }
    }
});

function remade_plan() {
    var start, lunch, lunch_time, dinner_time, dinner, goal;
    var s_l_post_box = [];
    var l_d_post_box = [];
    var d_g_post_box = [];
    start = document.getElementById('plan_start_box').value;
    goal = document.getElementById('plan_goal_box').value;

    var lunch_box = document.getElementById('plan_lunch_box');
    if (lunch_box.className != "hidden") {
        lunch = lunch_box.value;
        lunch_time = lunch_box.querySelector('.time').value;
    } else {
        lunch = -1;
        lunch_time = 0;
    }
    var dinner_box = document.getElementById('plan_dinner_box');
    if (dinner_box.className != "hidden") {
        dinner = dinner_box.value;
        dinner_time = dinner_box.querySelector('.time').value;
    } else {
        dinner = -1;
        dinner_time = 0;
    }
    var lunch_post_box = [lunch, lunch_time];
    var dinner_post_box = [dinner, dinner_time];

    //alert(dinner_time);
    var s_l_spots = document.getElementById('sort').querySelectorAll('li');
    s_l_spots.forEach(function(element) {
        if (element.className != "hidden") {
            s_l_post_box.push([element.value, element.querySelector('.s_l_time').value, element.querySelector(
                '.s_l_name').textContent]);
        }
    });
    var l_d_spots = document.getElementById('sort2').querySelectorAll('li');
    l_d_spots.forEach(function(element) {
        if (element.className != "hidden") {
            l_d_post_box.push([element.value, element.querySelector('.l_d_time').value, element.querySelector(
                '.l_d_name').textContent]);
        }
    });
    var d_g_spots = document.getElementById('sort3').querySelectorAll('li');
    d_g_spots.forEach(function(element) {
        if (element.className != "hidden") {
            d_g_post_box.push([element.value, element.querySelector('.d_g_time').value, element.querySelector(
                '.d_g_name').textContent]);
        }
    });

    jQuery(function($) {
        $.ajax({
            url: "ajax_reload_making_plan.php",
            type: "POST",
            dataType: "json",
            data: {
                post_data_1: start,
                post_data_2: goal,
                post_data_3: lunch_post_box,
                post_data_4: dinner_post_box,
                post_data_5: s_l_post_box,
                post_data_6: l_d_post_box,
                post_data_7: d_g_post_box
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert("ajax通信に失敗しました");
            },
            success: function(response) {
                //alert(response);
                location.reload();
            }
        });
    });

};
</script>

</html>
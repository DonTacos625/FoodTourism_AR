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
    $plan_s_l_spots = [[-1,0,"設定されていません"]];
}
if (isset($_SESSION["l_d_spots"])) {
    $plan_l_d_spots = $_SESSION["l_d_spots"];
} else {
    $plan_l_d_spots = [[-1,0,"設定されていません"]];
}
if (isset($_SESSION["d_g_spots"])) {
    $plan_d_g_spots = $_SESSION["d_g_spots"];
} else {
    $plan_d_g_spots = [[-1,0,"設定されていません"]];
}

$making_plan = [["start", $plan_start_station_id, $plan_start_time],
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
if($area == 1){
    //$area = 1;
    $area_name = "minatomirai";
    $center = [139.635, 35.453];
    $database_stations = "minatomirai_stations";
    $database_restaurants = "minatomirai_restaurants";
    $database_sightseeing_spots = "minatomirai_sightseeing_spots";
    $map_stations = "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/gis_minatomirai_stations/FeatureServer";
    $map_restaurants = "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/gis_minatomirai_restaurants/FeatureServer";
    $map_sightseeing_spots = "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/gis_minatomirai_sightseeing_spots/FeatureServer";
} else if($area == 2){
    //$area = 2;
    $area_name = "hasune";
    $center = [139.6790835, 35.78443256];
    $database_stations = "hasune_stations";
    $database_restaurants = "hasune_restaurants";
    $database_sightseeing_spots = "hasune_sightseeing_spots";
    $map_stations = "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/gis_hasune_stations/FeatureServer";
    $map_restaurants = "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/gis_hasune_restaurants/FeatureServer";
    $map_sightseeing_spots = "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/gis_hasune_sightseeing_spots/FeatureServer";
} else if($area == 3){
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


function display_frame($name_row, $time){
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
    <!--
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.jp/docs/5.3/assets/css/docs.css" rel="stylesheet">
    <title>Bootstrap Example</title>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    -->
    <link rel="stylesheet" type="text/css" href="css/copyright.css">

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
            width: 19vw;
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

        .sortable ul{
          list-style: none;
          padding: 0;
        }
        .sortable li{
          cursor:pointer;
          border:1px solid;
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

        //time(1,2,3)の時間帯のidが一致するスポットを削除する
        function remove_spot(time, id) {
            jQuery(function($) {
                $.ajax({
                    url: './ajax_remove_spot.php',
                    type: "POST",
                    dataType: 'json',
                    data: {
                        post_data_1: time,
                        post_data_2: id,
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert("ajax通信に失敗しました");
                    },
                    success: function(response) {
                        //alert("削除されたのは" + response[0][0]);
                        /*
                        if (response.length < 1) {
                            alert("「" + response[0][0] + "」が削除されました");
                        }
                        */
                        overwrite(time, response, 0);
                        overwrite(time, response, 1);

                        //keiroの関数
                        kousin();
                    }
                });
            });
        };

        //time(1,2,3)の時間帯のidが一致するスポットをswapmode(up,down)によって上か下に入れ替える
        function swap_spots(time, id, swapmode) {
            jQuery(function($) {
                $.ajax({
                    url: './ajax_swap_spot.php',
                    type: "POST",
                    dataType: 'json',
                    data: {
                        post_data_1: time,
                        post_data_2: id,
                        post_data_3: swapmode
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert("ajax通信に失敗しました");
                    },
                    success: function(response) {
                        //alert("返り値は" + response);
                        /*
                        if (!(response == "")) {
                            update_frame("設定されていません", response);
                        }
                        */
                        overwrite(time, response, 0);
                        overwrite(time, response, 1);

                        //keiroの関数
                        kousin();
                    }
                });
            });
        };

        //name_array = [["スポット名", "スポットID"]]
        //time(1,2,3)の時間帯のleftboxの内容を上書きする
        function overwrite(time, name_array, toggle) {
            //alert(time);
            if (toggle == 0) {
                if (time == 1) {
                    $div1 = document.getElementById("s_l_spots_line");
                } else if (time == 2) {
                    $div1 = document.getElementById("l_d_spots_line");
                } else if (time == 3) {
                    $div1 = document.getElementById("d_g_spots_line");
                }
            } else {
                if (time == 1) {
                    $div1 = document.getElementById("toggle_s_l_spots_line");
                } else if (time == 2) {
                    $div1 = document.getElementById("toggle_l_d_spots_line");
                } else if (time == 3) {
                    $div1 = document.getElementById("toggle_d_g_spots_line");
                }
            }
            $div1.innerHTML = "";
            for (var i = 0; i < name_array.length; i++) {
                //alert(name_array[i][1]);
                const newDiv = document.createElement("div");
                var j = i + 1;
                newDiv.innerHTML = j + ":" + name_array[i][0];
                const removeBtn = document.createElement("button");
                removeBtn.innerHTML = "×";
                removeBtn.title = "このスポットを削除します";
                removeBtn.className = 'btn2';
                const swapupBtn = document.createElement("button");
                swapupBtn.innerHTML = "↑";
                swapupBtn.title = "このスポットを一つ上に移動します";
                const swapdownBtn = document.createElement("button");
                swapdownBtn.innerHTML = "↓";
                swapdownBtn.title = "このスポットを一つ下に移動します";

                const spot_id = name_array[i][1];
                removeBtn.onclick = () => {
                    remove_spot(time, spot_id, toggle);
                }
                swapupBtn.onclick = () => {
                    swap_spots(time, spot_id, 'up', toggle);
                }
                swapdownBtn.onclick = () => {
                    swap_spots(time, spot_id, 'down', toggle);
                }

                //ボタン間の隙間
                const newa1 = document.createElement("a");
                const newa2 = document.createElement("a");
                newa1.innerHTML = " ";
                newa2.innerHTML = " ";

                newDiv.appendChild(document.createElement("br"));
                newDiv.appendChild(removeBtn);
                newDiv.appendChild(newa1);
                newDiv.appendChild(swapupBtn);
                newDiv.appendChild(newa2);
                newDiv.appendChild(swapdownBtn);

                $div1.appendChild(newDiv);
            }

        }

        //観光計画からスポットを削除
        function hidden_spot(name) {
            var name_tag = document.getElementById(name);
            if(name_tag.className != "hidden"){
                name_tag.className = "hidden";
                name_tag.querySelector(".btn").textContent = "戻す";
            } else {
                name_tag.className = "";
                name_tag.querySelector(".btn").textContent = "削除";
            }
            
        };

        //観光計画の情報を更新
        function update_making_plan() {
            var radios = document.getElementsByName("lunch_or_dinner");
            for(var i=0; i<radios.length; i++){
                if (radios[i].checked) {
                //選択されたラジオボタンのvalue値を取得する
                mode = radios[i].value;
                break;
                }
            }
            if(mode == "0"){
                alert("昼食か夕食を選択してください");
            } else {
                var plan_start_time = document.getElementById("plan_start_time").value;
                var plan_goal_time = document.getElementById("plan_goal_time").value;
                jQuery(function($) {
                    $.ajax({
                        url: "ajax_update_making_plan.php",
                        type: "POST",
                        dataType: "json",
                        data: {
                            post_data_1: restaurant_id,
                            post_data_2: mode,
                            post_data_3: time
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            alert("ajax通信に失敗しました");
                        },
                        success: function(response) {
                            //frameの関数
                            update_frame(response[0], response[1]);
                            if (mode == "1") {
                                alert("「" + response[0] + "」を昼食に設定しました");
                            } else {
                                alert("「" + response[0] + "」を夕食に設定しました");
                            }
                        }
                    });
                });
            };
        };
    </script>


</body>

<!-- ドラッグアンドドロップを実装する用 -->
<script src="//cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>

<script src="Sortable.min.js"></script>
<!-- -->
<script>
/*
var el = document.getElementById('sort');
var sortable = Sortable.create(el);

var el2 = document.getElementById('sort2');
var sortable2 = Sortable.create(el2);

var el3 = document.getElementById('sort3');
var sortable3 = Sortable.create(el3);
*/

var el = document.getElementById('sort');
var sortable = Sortable.create(el, {
    onSort: function(evt) {
        var items = el.querySelectorAll('li');
        for (var i = 0; i < items.length; i++) {
            items[i].querySelector('.no_s_l').value = i + 1;
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
            items[i].querySelector('.no_l_d').value = i + 1;
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
            items[i].querySelector('.no_d_g').value = i + 1;
            var src = `./icons/pop_icon_d_g${i + 1}.png`;
            items[i].querySelector('.pin_d_g').src = src;
        }
    }
});




</script>

</html>
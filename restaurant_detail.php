<?php

require "frame.php";

$restaurant_id = $_GET["restaurant_id"];

//stations_id設定
if (isset($_SESSION["start_station_id"])) {
    $start_station_id = $_SESSION["start_station_id"];
} else {
    $start_station_id = 0;
}
if (isset($_SESSION["goal_station_id"])) {
    $goal_station_id = $_SESSION["goal_station_id"];
} else {
    $goal_station_id = 0;
}
$station_id = [$start_station_id, $goal_station_id];

try {

    if ($start_station_id != 0) {
        $stmt2 = $pdo->prepare("SELECT * FROM $database_stations WHERE id = :id");
        $stmt2->bindParam(":id", $start_station_id);
        $stmt2->execute();
        $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
        $station_plan[] = [$result2["x"], $result2["y"], "start"];
    } else {
        $station_plan[] = [0, 0, "start"];
    }

    if ($goal_station_id != 0) {
        $stmt4 = $pdo->prepare("SELECT * FROM $database_stations WHERE id = :id");
        $stmt4->bindParam(":id", $goal_station_id);
        $stmt4->execute();
        $result4 = $stmt4->fetch(PDO::FETCH_ASSOC);
        $station_plan[] = [$result4["x"], $result4["y"], "goal"];
    } else {
        $station_plan[] = [0, 0, "goal"];
    }

    $stmt1 = $pdo->prepare("SELECT * FROM $database_restaurants where id = :id");
    $stmt1->bindParam(":id", $restaurant_id, PDO::PARAM_INT);
    $stmt1->execute();
    $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    //デバッグ用
    echo $e->getMessage();
}
?>

<html>

<head>
    <meta charset="utf-8" />
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
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no" />
    <title>飲食店詳細</title>
    <style>
        #detailbox {
            position: relative;
            float: left;
            margin-left: 5px;
        }

        #detailbox h3 {
            border-left: 5px solid #000080;
            margin: 0px;
        }

        #detailbox #imgbox #viewbox {
            position: relative;
            float: left;
            width: 75vw;
            height: 40vw;
            margin-bottom: 15px;
            justify-content: center;
            align-items: center;
        }

        #detailbox #imgbox #viewbox #viewDiv {
            position: relative;
            padding: 0;
            margin: 0;
            height: 100%;
            width: 100%;
        }

        #detailbox #infobox {
            float: left;
            width: 75vw;
            margin-left: 5px;
        }

        .clearfix:after {
            display: block;
            clear: both;
            height: 0px;
            visibility: hidden;
            content: ".";
        }

        #detailbox #infobox table {
            width: 100%;
            border: solid 3px #FFFFFF;
        }

        #detailbox #infobox table th {
            text-align: left;
            white-space: nowrap;
            background: #EEEEEE;
        }

        #detailbox #infobox table td {
            background: #EEEEEE;
            padding: 3px;
        }

        #detailbox #infobox table td ul {
            margin: 0px;
        }

        #detailbox #infobox table td ul li {
            display: inline-block;
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

            #detailbox {
                width: auto;
                margin: 0px;
                float: none;
            }

            #detailbox #imgbox #viewbox {
                position: relative;
                float: left;
                width: 95%;
                height: 50vh;
                margin-bottom: 15px;
                justify-content: center;
                align-items: center;
            }

            #detailbox #infobox {
                width: 95%;
                float: none;
            }

            #detailbox #infobox table {
                font-size: 13px;
            }

        }
    </style>

    <link rel="stylesheet" href="https://js.arcgis.com/4.21/esri/themes/light/main.css" />
    <script src="https://js.arcgis.com/4.21/"></script>
    <!-- <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> -->

    <script>
        require([
            "esri/Map",
            "esri/views/MapView",
            "esri/layers/WebTileLayer",
            "esri/layers/FeatureLayer",
            "esri/Graphic",
            "esri/layers/GraphicsLayer",
            "esri/rest/support/Query",
            "esri/rest/support/RouteParameters",
            "esri/rest/support/FeatureSet",
            "esri/symbols/PictureMarkerSymbol"
        ], function(
            Map,
            MapView,
            WebTileLayer,
            FeatureLayer,
            Graphic,
            GraphicsLayer,
            Query,
            RouteParameters,
            FeatureSet,
            PictureMarkerSymbol
        ) {

            // Point the URL to a valid routing service
            const routeUrl = "https://utility.arcgis.com/usrsvcs/servers/4550df58672c4bc6b17607b947177b56/rest/services/World/Route/NAServer/Route_World";

            const food_template = {
                title: "{Name}",
                content: [{
                    type: "fields",
                    fieldInfos: [{
                        fieldName: "ID",
                        label: "ID",
                        visible: true
                    }, {
                        fieldName: "genre",
                        label: "ジャンル",
                        visible: true
                    }, {
                        fieldName: "genre_sub",
                        label: "サブジャンル",
                        visible: true
                    }, {
                        fieldName: "open_time",
                        label: "営業時間",
                        visible: true
                    }, {
                        fieldName: "close_time",
                        label: "定休日",
                        visible: true
                    }, {
                        fieldName: "lunch_budget",
                        label: "昼予算",
                        visible: true
                    }, {
                        fieldName: "dinner_budget",
                        label: "夜予算",
                        visible: true
                    }, {
                        fieldName: "capacity",
                        label: "席数",
                        visible: true
                    }, {
                        fieldName: "non_smoking",
                        label: "禁煙席",
                        visible: true
                    }, {
                        fieldName: "lunch",
                        label: "ランチメニュー",
                        visible: true
                    }, {
                        fieldName: "X",
                        label: "経度",
                        visible: true
                    }, {
                        fieldName: "Y",
                        label: "緯度",
                        visible: true
                    }]
                }]
            };

            const station_template = {
                title: "{Name}",
                content: [{
                    type: "fields",
                    fieldInfos: [{
                        fieldName: "ID",
                        label: "ID",
                        visible: true
                    }, {
                        fieldName: "X",
                        label: "経度",
                        visible: true
                    }, {
                        fieldName: "Y",
                        label: "緯度",
                        visible: true
                    }]
                }]
            };

            //スタートとゴールの駅を決める
            var station_id = <?php echo json_encode($station_id); ?>;
            var station_feature_sql = "";

            for (var i = 0; i < station_id.length; i++) {
                if (i != station_id.length - 1) {
                    station_feature_sql += "ID = "
                    station_feature_sql += station_id[i];
                    station_feature_sql += " OR "
                } else if (i == station_id.length - 1) {
                    station_feature_sql += "ID = "
                    station_feature_sql += station_id[i];
                }
            }

            //飲食店のIDから表示するスポットを決める
            var result1 = <?php echo json_encode($result1) ?>;
            var food_feature_sql = "ID = " + result1["id"];

            //spotLayer
            //$map_stations,$map_restaurants,$map_sightseeing_spotsはframe.phpに
            var foodLayer = new FeatureLayer({
                url: <?php echo json_encode($map_restaurants); ?>,
                id: "foodLayer",
                popupTemplate: food_template,
                definitionExpression: food_feature_sql
            });

            var stationLayer = new FeatureLayer({
                url: <?php echo json_encode($map_stations); ?>,
                id: "stationLayer",
                popupTemplate: station_template,
                definitionExpression: station_feature_sql
            });

            //選択したスポットの表示レイヤー
            const station_pointLayer = new GraphicsLayer();
            const food_pointLayer = new GraphicsLayer();
            
            const center_pointLayer = new GraphicsLayer();

            const map = new Map({
                basemap: "streets",
                layers: [foodLayer, stationLayer, station_pointLayer, food_pointLayer, center_pointLayer]
            });

            const view = new MapView({
                container: "viewDiv", // Reference to the scene div created in step 5
                map: map, // Reference to the map object created before the scene
                center: [result1["x"], result1["y"]],
                zoom: 15,
                popup: {
                    dockEnabled: true,
                    dockOptions: {
                        breakpoint: false
                    }
                }
            });

            //中心地点にマーカーを
            function make_center_maker(pic,Layer,X,Y) {
                const point = {
                    type: "point",
                    x: X,
                    y: Y
                };
                var stopSymbol = new PictureMarkerSymbol({
                    url: pic,
                    width: "30px",
                    height: "46.5px"
                });
                var stop = new Graphic({
                    geometry: point,
                    symbol: stopSymbol
                });
                Layer.add(stop);
            }
            make_center_maker("./markers/red_pin.png",center_pointLayer,result1["x"], result1["y"])

            //phpの経路情報をjavascript用に変換           
            var station_plan = <?php echo json_encode($station_plan); ?>;
            //開始駅と終了駅が同じの場合のフラグを設定
            var start_point = station_plan[0];
            var goal_point = station_plan.slice(-1)[0];
            var mode_change = 0;
            if (start_point[0] == goal_point[0] && start_point[1] == goal_point[1]) {
                mode_change = 1;
            }
            //最初に経路表示する処理
            function start_map(plan, Layer) {
                for (var j = 0; j < plan.length; j++) {
                    if (!(plan[j][0] == 0)) {
                        var point = {
                            type: "point",
                            x: plan[j][0],
                            y: plan[j][1]
                        };
                        if (plan[j].length > 2) {
                            if (plan[j][2] == "start") {
                                if (mode_change == 1) {
                                    pointpic = "./markers/start_and_goal.png";
                                } else {
                                    pointpic = "./markers/start.png";
                                }
                            } else if (plan[j][2] == "lunch") {
                                pointpic = "./markers/lunch.png";
                            } else if (plan[j][2] == "dinner") {
                                pointpic = "./markers/dinner.png";
                            } else if (plan[j][2] == "goal") {
                                if (mode_change == 1) {
                                    pointpic = "./markers/start_and_goal.png";
                                } else {
                                    pointpic = "./markers/goal.png";
                                }
                            } else {
                                pointpic = "./markers/ltblue.png";
                            }
                        }
                        var stopSymbol = new PictureMarkerSymbol({
                            url: pointpic,
                            width: "30px",
                            height: "46.5px"
                        });
                        var stop = new Graphic({
                            geometry: point,
                            symbol: stopSymbol
                        });
                        Layer.add(stop);
                    }
                }
            }

            start_map(station_plan, station_pointLayer);

            function add_point(pic, Layer) {
                const point = {
                    type: "point",
                    x: view.popup.selectedFeature.attributes.X,
                    y: view.popup.selectedFeature.attributes.Y
                };
                var stopSymbol = new PictureMarkerSymbol({
                    url: pic,
                    width: "20px",
                    height: "31px"
                });
                var stop = new Graphic({
                    geometry: point,
                    symbol: stopSymbol
                });
                Layer.removeAll();
                Layer.add(stop);
            }

        });

        //昼食・夕食を決定
        function post_restaurant(restaurant_id) {
            var mode = 0;
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
                var time = document.getElementById("food_time").value;
                jQuery(function($) {
                    $.ajax({
                        url: "ajax_add_restaurant_to_plan.php",
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
                            window.location.href = "search.php";
                        }
                    });
                });
            };
        };

        //表示を更新する
        function toframe(data, id) {
            //frameの関数
            update_frame(data, id);
        }
    </script>

</head>

<body>
    <div class="container">
        <main>
            <div id="detailbox">
                <h3>観光スポットの詳細情報</h3>

                <div id="box" class="clearfix">

                    <div id="imgbox">
                        <div id="viewbox">
                            <div id="viewDiv"></div>
                        </div>
                    </div>

                    <div id="infobox">
                        <table>
                            <tr>
                                <th><div id="imgbox"><img src=<?php if($area == "minatomirai") {echo "images/minatomirai/restaurants/". $result1["id"] .".jpg" ;} else if($area == "hasune"){echo "images/hasune/restaurants/". $result1["id"] .".jpg" ;} else if($area == "chofu"){echo "images/chofu/restaurants/". $result1["id"] .".jpg" ;}?> alt=""></div></th>
                                <td></td>
                            </tr>
                            <tr>
                                <th>店舗名</th>
                                <td><?php echo $result1["name"]; ?></td>
                            </tr>

                            <tr>
                                <th>ジャンル</th>
                                <td><?php echo $result1["genre"]; ?>、<?php echo $result1["genre_sub"]; ?></td>
                            </tr>

                            <tr>
                                <th>住所</th>
                                <td><?php echo $result1["address"]; ?></td>
                            </tr>

                            <tr>
                                <th>アクセス</th>
                                <td><?php echo $result1["access"]; ?></td>
                            </tr>

                            <tr>
                                <th>営業時間</th>
                                <td><?php echo nl2br($result1["open_time"]); ?></td>
                            </tr>

                            <tr>
                                <th>定休日</th>
                                <td><?php echo nl2br($result1["close_time"]); ?></td>
                            </tr>

                            <tr>
                                <th>予算</th>
                                <td>昼：<?php if($result1["lunch_budget"]) {echo $result1["lunch_budget"];} else {echo "不明";} ?>　　夜：<?php echo $result1["dinner_budget"]; ?></td>
                            </tr>

                            <tr>
                                <th>総席数</th>
                                <td><?php echo nl2br($result1["capacity"]); ?>席</td>
                            </tr>

                            <tr>
                                <th>禁煙席</th>
                                <td><?php echo nl2br($result1["non_smoking"]); ?>席</td>
                            </tr>

                            <tr>
                                <th>ランチメニュー</th>
                                <td><?php echo $result1["lunch"]; ?></td>
                            </tr>

                            <tr>
                                <th>説明</th>
                                <td><?php echo nl2br($result1["catch_comment"]); ?></td>
                            </tr>

                            <tr>
                                <th>ホームページURL</th>
                                <td>
                                    <?php
                                    if (!empty($result1["urls"])) {
                                        print "<a href = " . $result1["urls"] . " target=_blank>ホームページにアクセスする</a>";
                                    }
                                    ?>
                                </td>
                            </tr>
                            <tr>
                                <th>観光計画に組み込みますか？</th>
                                <td>
                                    <input type="radio" id="lunch_or_dinner" name="lunch_or_dinner" value="1">昼食
                                    <input type="radio" id="lunch_or_dinner" name="lunch_or_dinner" value="2">夕食<br>
                                    <input type="number" value="30" id="food_time" name="food_time">分
                                    <button type="button" value=<?php echo $result1["id"]; ?> onclick="post_restaurant(value)">設定する</button>
                                </td>
                            </tr>
                        </table>
                        <li><a href="search.php">飲食店検索に戻る</a></li>
                    </div><br>
                </div>
            </div>
        </main>
        <footer>
            <p>Copyright(c) 2021 山本佳世子研究室 All Rights Reserved.</p>
        </footer>
    </div>
</body>

</html>
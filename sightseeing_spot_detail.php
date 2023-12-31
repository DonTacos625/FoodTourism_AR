<?php

require "frame_define.php";
require "frame_header.php";
require "frame_menu.php";
require "frame_rightmenu.php";

$spot_id = $_GET["spot_id"];

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

//food_shops_id設定
if (!isset($_SESSION["lunch_id"])) {
    $lunch_shop_id = -1;
} else {
    $lunch_shop_id = $_SESSION["lunch_id"];
}
if (!isset($_SESSION["dinner_id"])) {
    $dinner_shop_id = -1;
} else {
    $dinner_shop_id = $_SESSION["dinner_id"];
}
$food_shop_id = [$lunch_shop_id, $dinner_shop_id];

try {

    if (!isset($_SESSION["start_station_id"])) {
        $start_station_info = [0, 0, "start"];
    } else {
        $stmt1 = $pdo->prepare("SELECT * FROM $database_stations WHERE id = :id");
        $stmt1->bindParam(":id", $start_station_id);
        $stmt1->execute();
        $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);
        $start_station_info = [$result1["x"], $result1["y"], "start"];
    }

    if (!isset($_SESSION["lunch_id"])) {
        $lunch_info = [0, 0, "lunch"];
    } else {
        $stmt2 = $pdo->prepare("SELECT * FROM $database_restaurants WHERE id = :id");
        $stmt2->bindParam(":id", $lunch_shop_id);
        $stmt2->execute();
        $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
        $lunch_info = [$result2["x"], $result2["y"], "lunch"];
    }

    if (!isset($_SESSION["dinner_id"])) {
        $dinner_info = [0, 0, "dinner"];
    } else {
        $stmt3 = $pdo->prepare("SELECT * FROM $database_restaurants WHERE id = :id");
        $stmt3->bindParam(":id", $dinner_shop_id);
        $stmt3->execute();
        $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);
        $dinner_info = [$result3["x"], $result3["y"], "dinner"];
    }

    if (!isset($_SESSION["goal_station_id"])) {
        $goal_station_info = [0, 0, "goal"];
    } else {
        $stmt4 = $pdo->prepare("SELECT * FROM $database_stations WHERE id = :id");
        $stmt4->bindParam(":id", $goal_station_id);
        $stmt4->execute();
        $result4 = $stmt4->fetch(PDO::FETCH_ASSOC);
        $goal_station_info = [$result4["x"], $result4["y"], "goal"];
    }

    $stmt = $pdo->prepare("SELECT * FROM $database_sightseeing_spots where id = :id");
    $stmt->bindParam(":id", $spot_id, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    //デバッグ用
    echo $e->getMessage();
}

//keikakuの配列作成
$keikaku[] = $start_station_info;

$keikaku[] = $lunch_info;

$keikaku[] = $dinner_info;

$keikaku[] = $goal_station_info;

?>

<html>

<head>
    <meta charset="utf-8" />
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
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no" />
    <link rel="stylesheet" type="text/css" href="css/detailbox.css?<?php echo date('YmdHis'); ?>">
    <title>観光スポット詳細</title>

    <link rel="stylesheet" href="https://js.arcgis.com/4.21/esri/themes/light/main.css" />
    <script src="https://js.arcgis.com/4.21/"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

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
            //popup
            var detailAction_station = {
                title: "詳細",
                id: "station_detail",
                className: "esri-icon-documentation"
            };
            var detailAction_restaurant = {
                title: "詳細",
                id: "restaurant_detail",
                className: "esri-icon-documentation"
            };
            var detailAction_spot = {
                title: "詳細",
                id: "spot_detail",
                className: "esri-icon-documentation"
            };

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
                }],
                actions: [detailAction_restaurant]
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
                }],
                actions: [detailAction_station]
            };

            const spots_template = {
                title: "{Name}",
                content: [{
                    type: "fields",
                    fieldInfos: [{
                        fieldName: "ID",
                        label: "ID",
                        visible: true
                    }, {
                        fieldName: "category",
                        label: "カテゴリー",
                        visible: true
                    }, {
                        fieldName: "homepage",
                        label: "ホームページ",
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
                }],
                actions: [detailAction_spot]
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
            var food_shop_id = <?php echo json_encode($food_shop_id); ?>;
            var food_feature_sql = "";

            for (var i = 0; i < food_shop_id.length; i++) {
                if (i != food_shop_id.length - 1) {
                    food_feature_sql += "ID = "
                    food_feature_sql += food_shop_id[i];
                    food_feature_sql += " OR "
                } else if (i == food_shop_id.length - 1) {
                    food_feature_sql += "ID = "
                    food_feature_sql += food_shop_id[i];
                }
            }

            //飲食店のIDから表示するスポットを決める
            var result = <?php echo json_encode($result) ?>;
            var spots_feature_sql = "ID = " + result["id"];

            //spotLayer
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

            var spotLayer = new FeatureLayer({
                url: <?php echo json_encode($map_sightseeing_spots); ?>,
                id: "spotLayer",
                popupTemplate: spots_template,
                definitionExpression: spots_feature_sql
            });

            //選択したスポットの表示レイヤー
            const routeLayer = new GraphicsLayer();

            //選択したスポットの表示レイヤー
            const s_l_pointLayer = new GraphicsLayer();
            const l_d_pointLayer = new GraphicsLayer();
            const d_g_pointLayer = new GraphicsLayer();

            const center_pointLayer = new GraphicsLayer();

            const map = new Map({
                basemap: "streets",
                layers: [foodLayer, stationLayer, spotLayer, routeLayer, s_l_pointLayer, l_d_pointLayer, d_g_pointLayer, center_pointLayer]
            });

            const view = new MapView({
                container: "viewDiv", // Reference to the scene div created in step 5
                map: map, // Reference to the map object created before the scene
                center: [result["x"], result["y"]],
                zoom: 14,
                popup: {
                    dockEnabled: true,
                    dockOptions: {
                        breakpoint: false
                    }
                }
            });

            //中心地点にマーカーを
            function make_center_maker(pic, Layer, X, Y) {
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
            make_center_maker("./markers/red_pin.png", center_pointLayer, result["x"], result["y"])

            //phpの経路情報をjavascript用に変換           
            var keikaku = <?php echo json_encode($keikaku); ?>;

            function display_route(plan) {
                //最初に経路表示する処理
                //開始駅と終了駅が同じの場合のフラグを設定
                var start_point = plan[0];
                var goal_point = plan.slice(-1)[0];
                var mode_change = 0;
                if (start_point[0] == goal_point[0] && start_point[1] == goal_point[1]) {
                    mode_change = 1;
                }
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
                            } else if (plan[j][2] == 11) {
                                pointpic = "./markers/s_l_spot1.png";
                            } else if (plan[j][2] == 12) {
                                pointpic = "./markers/s_l_spot2.png";
                            } else if (plan[j][2] == 13) {
                                pointpic = "./markers/s_l_spot3.png";
                            } else if (plan[j][2] == 21) {
                                pointpic = "./markers/l_d_spot1.png";
                            } else if (plan[j][2] == 22) {
                                pointpic = "./markers/l_d_spot2.png";
                            } else if (plan[j][2] == 23) {
                                pointpic = "./markers/l_d_spot3.png";
                            } else if (plan[j][2] == 31) {
                                pointpic = "./markers/d_g_spot1.png";
                            } else if (plan[j][2] == 32) {
                                pointpic = "./markers/d_g_spot2.png";
                            } else if (plan[j][2] == 33) {
                                pointpic = "./markers/d_g_spot3.png";
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
                        routeLayer.add(stop);
                        /*
                        routeParams.stops.features.push(stop);
                        if (routeParams.stops.features.length >= 2) {
                            route.solve(routeUrl, routeParams).then(showRoute);
                        }
                        */
                    }
                }
            }
            display_route(keikaku);

            //ポップアップの処理
            view.popup.on("trigger-action", function(event) {
                if (event.action.id === "station_detail") {
                    var id = view.popup.selectedFeature.attributes.id;
                    srs_detail(id, "station");
                }
                if (event.action.id === "restaurant_detail") {
                    var id = view.popup.selectedFeature.attributes.id;
                    srs_detail(id, "restaurant");
                }
                if (event.action.id === "spot_detail") {
                    var id = view.popup.selectedFeature.attributes.id;
                    srs_detail(id, "spot");
                }
            });

        });

        //昼食・夕食を決定
        function post_sightseeing_spot(spot_id) {
            var mode = 0;
            var radios = document.getElementsByName("s_l_d_g");
            for (var i = 0; i < radios.length; i++) {
                if (radios[i].checked) {
                    //選択されたラジオボタンのvalue値を取得する
                    mode = radios[i].value;
                    break;
                }
            }
            if (mode == "0") {
                alert("時間帯を選択してください");
            } else {
                var time = document.getElementById("sightseeing_time").value;
                jQuery(function($) {
                    $.ajax({
                        url: "ajax_add_sightseeing_spot_to_plan.php",
                        type: "POST",
                        dataType: "json",
                        data: {
                            post_data_1: spot_id,
                            post_data_2: mode,
                            post_data_3: time
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            alert("ajax通信に失敗しました");
                        },
                        success: function(response) {
                            //frameの関数
                            //toframe(mode, response[1]);
                            //alert(response[1]);
                            if (response[0] == "") {
                                alert("同じスポットは登録できません");
                            } else if (response[0] == "3") {
                                alert("各時間帯に登録できるスポットは3つまでです");
                            } else {
                                if (mode == "1") {
                                    alert("「" + response[0] + "」を昼食前に訪問する観光スポットに設定しました");
                                } else if (mode == "2") {
                                    alert("「" + response[0] + "」を昼食後に訪問する観光スポットに設定しました");
                                } else {
                                    alert("「" + response[0] + "」を夕食前に訪問する観光スポットに設定しました");
                                }
                            }
                            window.location.href = "sightseeing_spots_selection_map.php";
                            //window.history.back();
                        }
                    });
                });
            };
        };
    </script>

</head>

<body>
    <div class="container-fluid">
        <main class="row">
            <div id="detailbox">
                <h3 class="px-0">観光スポットの詳細情報</h3>

                <div id="box" class="clearfix">
                    <div id="viewbox">
                        <div id="viewDiv"></div>
                    </div>

                    <div id="infobox">
                        <table>
                            <table>
                                <tr>
                                    <th>
                                        <div id="imgbox"><img src=<?php echo "images/$area_name/sightseeing_spots/" . $result["id"] . ".jpg" ?> onError="this.onerror=null;this.src='images/no_image.jpg';" alt=""></div>
                                    </th>
                                    <td></td>
                                </tr>
                                <tr>
                                    <th>名称</th>
                                    <td><?php echo $result["name"]; ?></td>
                                </tr>
                                <tr>
                                    <th>カテゴリー</th>
                                    <td><?php echo $result["category"]; ?></td>
                                </tr>
                                <tr>
                                    <th>ホームページURL</th>
                                    <td>
                                        <?php
                                        if (!empty($result["homepage"])) {
                                            print "<a href = " . $result["homepage"] . " target=_blank>ホームページにアクセスする</a>";
                                        }
                                        ?>
                                    </td>
                                </tr>
                                <tr>
                                    <th>観光計画に組み込みますか？</th>
                                    <td>
                                        <img class="pin_s_l" width="18" height="18" src="./icons/pop_icon_s_l1.png" alt="昼食前に訪問する観光スポットのアイコン" title="昼食前に訪問する観光スポット">
                                        <input type="radio" id="s_l_d_g" name="s_l_d_g" value="1">昼食の前
                                        <img class="pin_l_d" width="18" height="18" src="./icons/pop_icon_l_d1.png" alt="昼食後に訪問する観光スポットのアイコン" title="昼食後に訪問する観光スポット">
                                        <input type="radio" id="s_l_d_g" name="s_l_d_g" value="2">昼食の後
                                        <img class="pin_d_g" width="18" height="18" src="./icons/pop_icon_d_g1.png" alt="夕食後に訪問する観光スポットのアイコン" title="夕食後に訪問する観光スポット">
                                        <input type="radio" id="s_l_d_g" name="s_l_d_g" value="3">夕食の後<br>
                                        滞在時間：<input type="number" value="30" id="sightseeing_time" name="sightseeing_time">分
                                        <button type="button" class="btn btn-secondary" value=<?php echo $spot_id; ?> onclick="post_sightseeing_spot(value)">設定する</button>
                                    </td>
                                </tr>
                            </table>
                            <!-- <li><a href="#" onclick="window.history.back(); return false;">戻る</a></li> -->
                            <li><a href="sightseeing_spots_selection_map.php">戻る</a></li>
                    </div>
                </div>
            </div>
        </main>
        <footer>
            <p>Copyright(c) 2023 山本佳世子研究室 All Rights Reserved.</p>
        </footer>
    </div>
</body>

</html>
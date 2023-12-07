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

//stations_id設定
if (!isset($_SESSION["start_station_id"])) {
    $start_station_id = 0;
} else {
    $start_station_id = $_SESSION["start_station_id"];
}
if (!isset($_SESSION["goal_station_id"])) {
    $goal_station_id = 0;
} else {
    $goal_station_id = $_SESSION["goal_station_id"];
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

//spots設定
if (!isset($_SESSION["s_l_spots"])) {
    $s_l_ids = [0];
} else {
    foreach ($_SESSION["s_l_spots"] as $s_l) {
        $s_l_ids[] = $s_l[0];
    }
}
if (!isset($_SESSION["l_d_spots"])) {
    $l_d_ids = [0];
} else {
    foreach ($_SESSION["l_d_spots"] as $l_d) {
        $l_d_ids[] = $l_d[0];
    }
}
if (!isset($_SESSION["d_g_spots"])) {
    $d_g_ids = [0];
} else {
    foreach ($_SESSION["d_g_spots"] as $d_g) {
        $d_g_ids[] = $d_g[0];
    }
}
$spots_id = array_merge($s_l_ids, $l_d_ids, $d_g_ids);

//デバッグ用
//$_SESSION["s_l_spots"] = [[1,30,"name1"],[1,30,"name2"]];
//$_SESSION["l_d_spots"] = [[1,30,"name1"],[1,30,"name2"]];
//$_SESSION["d_g_spots"] = [[1,30,"name1"],[1,30,"name2"]];

//DB接続
try {
    if (!isset($_SESSION["start_station_id"])) {
        $start_info = [0, 0, "start"];
    } else {
        $stmt1 = $pdo->prepare("SELECT * FROM $database_stations WHERE id = :id");
        $stmt1->bindParam(":id", $start_station_id);
        $stmt1->execute();
        $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);
        $start_info = [$result1["x"], $result1["y"], "start"];
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
        $goal_info = [0, 0, "goal"];
    } else {
        $stmt4 = $pdo->prepare("SELECT * FROM $database_stations WHERE id = :id");
        $stmt4->bindParam(":id", $goal_station_id);
        $stmt4->execute();
        $result4 = $stmt4->fetch(PDO::FETCH_ASSOC);
        $goal_info = [$result4["x"], $result4["y"], "goal"];
    }

    $spot_count = 10;
    if (!isset($_SESSION["s_l_spots"])) {
        $s_l_spots = [[0, 0, 0]];
    } else {
        foreach ($_SESSION["s_l_spots"] as $s_l) {
            $stmt5 = $pdo->prepare("SELECT * FROM $database_sightseeing_spots WHERE id = :id");
            $stmt5->bindParam(":id", $s_l[0]);
            $stmt5->execute();
            $result5 = $stmt5->fetch(PDO::FETCH_ASSOC);
            $spot_count += 1;
            $s_l_spots[] = [$result5["x"], $result5["y"], $spot_count];
        }
    }
    $spot_count = 20;
    if (!isset($_SESSION["l_d_spots"])) {
        $l_d_spots = [[0, 0, 0]];
    } else {
        foreach ($_SESSION["l_d_spots"] as $l_d) {
            $stmt6 = $pdo->prepare("SELECT * FROM $database_sightseeing_spots WHERE id = :id");
            $stmt6->bindParam(":id", $l_d[0]);
            $stmt6->execute();
            $result6 = $stmt6->fetch(PDO::FETCH_ASSOC);
            $spot_count += 1;
            $l_d_spots[] = [$result6["x"], $result6["y"], $spot_count];
        }
    }
    $spot_count = 30;
    if (!isset($_SESSION["d_g_spots"])) {
        $d_g_spots = [[0, 0, 0]];
    } else {
        foreach ($_SESSION["d_g_spots"] as $d_g) {
            $stmt7 = $pdo->prepare("SELECT * FROM $database_sightseeing_spots WHERE id = :id");
            $stmt7->bindParam(":id", $d_g[0]);
            $stmt7->execute();
            $result7 = $stmt7->fetch(PDO::FETCH_ASSOC);
            $spot_count += 1;
            $d_g_spots[] = [$result7["x"], $result7["y"], $spot_count];
        }
    }
} catch (PDOException $e) {
}

//keikakuは目的地の配列
//keikakuの配列作成
$keikaku[] = $start_info;
foreach ($s_l_spots as $s_l_add) {
    $keikaku[] = $s_l_add;
}
$keikaku[] = $lunch_info;
foreach ($l_d_spots as $l_d_add) {
    $keikaku[] = $l_d_add;
}
$keikaku[] = $dinner_info;
foreach ($d_g_spots as $d_g_add) {
    $keikaku[] = $d_g_add;
}
$keikaku[] = $goal_info;

//var_dump($start_info);
//var_dump($_SESSION["s_l_spots"]);
//var_dump($s_l_ids);
//var_dump($s_l_spots);
//var_dump($l_d_spots);


//検索条件の復元
if (!isset($_SESSION["search_spots_distance"])) {
    $_SESSION["search_spots_distance"] = "100";
}
$search_distance = $_SESSION["search_spots_distance"];

if (!isset($_SESSION["search_spots_category"])) {
    $_SESSION["search_spots_category"] = "0";
}
$categoryName = $_SESSION["search_spots_category"];

//提出されたデータ
if (isset($_POST["search_spots_distance"])) {
    $search_distance = $_POST["search_spots_distance"];
    $_SESSION["search_spots_distance"] = $search_distance;
} else {
    $search_distance = $_SESSION["search_spots_distance"];
}
if (isset($_POST["search_spots_category"])) {
    $categoryName = $_POST["search_spots_category"];
    $_SESSION["search_spots_category"] = $categoryName;
} else {
    $categoryName = $_SESSION["search_spots_category"];
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
    <title>観光スポット選択（一覧表示）</title>
    <style>
        .move_box {
            position: relative;
            width: 76vw;
            float: left;
        }

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

        @media screen and (max-width:768px) {

            .search_form {
                font-size: 12px;
            }

            .search_result {
                font-size: 12px;
            }

            .move_box {
                width: 100%;
            }

        }
    </style>

    <link rel="stylesheet" href="https://js.arcgis.com/4.21/esri/themes/light/main.css" />
    <script src="https://js.arcgis.com/4.21/"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

    <script>
        var pointpic = "";
        require([
            "esri/Map",
            "esri/views/MapView",
            "esri/layers/WebTileLayer",
            "esri/layers/FeatureLayer",
            "esri/Graphic",
            "esri/layers/GraphicsLayer",
            "esri/rest/support/Query",
            "esri/rest/route",
            "esri/rest/support/RouteParameters",
            "esri/rest/support/FeatureSet",
            "esri/symbols/PictureMarkerSymbol",
            "esri/symbols/CIMSymbol"
        ], function(
            Map,
            MapView,
            WebTileLayer,
            FeatureLayer,
            Graphic,
            GraphicsLayer,
            Query,
            route,
            RouteParameters,
            FeatureSet,
            PictureMarkerSymbol,
            CIMSymbol
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

            //観光スポットのIDから表示するスポットを決める
            var spots_id = <?php echo json_encode($spots_id); ?>;
            var spots_feature_sql = "";

            for (var i = 0; i < spots_id.length; i++) {
                if (i != spots_id.length - 1) {
                    spots_feature_sql += "ID = "
                    spots_feature_sql += spots_id[i];
                    spots_feature_sql += " OR "
                } else if (i == spots_id.length - 1) {
                    spots_feature_sql += "ID = "
                    spots_feature_sql += spots_id[i];
                }
            }

            // スポット名を表示するラベルを定義
            var labelClass = {
                symbol: {
                    type: "text",
                    color: "white",
                    haloColor: "black",
                    haloSize: 1
                },
                font: {
                    size: 15,
                    widget: "bold"
                },
                labelPlacement: "above-center",
                labelExpressionInfo: {
                    expression: "$feature.name"
                }
            };

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

            var route_spotsLayer = new FeatureLayer({
                url: <?php echo json_encode($map_sightseeing_spots); ?>,
                id: "roue_spotsLayer",
                popupTemplate: spots_template,
                definitionExpression: spots_feature_sql
            });

            var spotLayer = new FeatureLayer({
                url: <?php echo json_encode($map_sightseeing_spots); ?>,
                id: "spotLayer"
            });

            //ルート表示のレイヤー
            const routeLayer = new GraphicsLayer();
            //周辺スポットのレイヤー
            const resultsLayer = new GraphicsLayer();

            //選択したスポットの表示レイヤー
            const s_l_pointLayer = new GraphicsLayer();
            const l_d_pointLayer = new GraphicsLayer();
            const d_g_pointLayer = new GraphicsLayer();

            // Setup the route parameters
            const routeParams = new RouteParameters({
                // An authorization string used to access the routing service
                apiKey: MY_API_KEY,
                attributeParameterValues: [{
                    parameterName: "Restriction Usage",
                    attributeName: "Walking",
                    value: "PROHIBITED"
                }, {
                    parameterName: "Restriction Usage",
                    attributeName: "Preferred for Pedestrians",
                    value: "PREFER_LOW"
                }],
                restrictionAttributes: ["Walking", "Preferred for Pedestrians"],

                stops: new FeatureSet(),
                outSpatialReference: {
                    // autocasts as new SpatialReference()
                    wkid: 3857
                },
                directionsLengthUnits: "kilometers"
            });
            routeParams.returnDirections = true;

            // Define the symbology used to display the stops
            const CheckSymbol = {
                type: "simple-marker", // autocasts as new SimpleMarkerSymbol()
                style: "cross",
                size: 15,
                outline: {
                    // autocasts as new SimpleLineSymbol()
                    width: 4
                }
            };

            // Define the symbology used to display the route
            const routeSymbol = {
                type: "simple-line", // autocasts as SimpleLineSymbol()
                color: [0, 0, 255, 0.5],
                width: 3
            };

            const routeArrowSymbol = new CIMSymbol({
                data: {
                    type: "CIMSymbolReference",
                    symbol: {
                        type: "CIMLineSymbol",
                        symbolLayers: [{
                                // black 1px line symbol
                                type: "CIMSolidStroke",
                                enable: true,
                                width: 2,
                                color: [
                                    0,
                                    0,
                                    0,
                                    255
                                ]
                            },
                            {
                                // arrow symbol
                                type: "CIMVectorMarker",
                                enable: true,
                                size: 5,
                                markerPlacement: {
                                    type: "CIMMarkerPlacementAlongLineSameSize", // places same size markers along the line
                                    endings: "WithMarkers",
                                    placementTemplate: [69.5], // determines space between each arrow
                                    angleToLine: true // symbol will maintain its angle to the line when map is rotated
                                },
                                frame: {
                                    xmin: -5,
                                    ymin: -5,
                                    xmax: 5,
                                    ymax: 5
                                },
                                markerGraphics: [{
                                    type: "CIMMarkerGraphic",
                                    geometry: {
                                        rings: [
                                            [
                                                [
                                                    -8,
                                                    -5.47
                                                ],
                                                [
                                                    -8,
                                                    5.6
                                                ],
                                                [
                                                    1.96,
                                                    -0.03
                                                ],
                                                [
                                                    -8,
                                                    -5.47
                                                ]
                                            ]
                                        ]
                                    },
                                    symbol: {
                                        // black fill for the arrow symbol
                                        type: "CIMPolygonSymbol",
                                        symbolLayers: [{
                                            type: "CIMSolidFill",
                                            enable: true,
                                            color: [
                                                0,
                                                0,
                                                0,
                                                255
                                            ]
                                        }]
                                    }
                                }]
                            }
                        ]
                    }
                }
            });

            const map = new Map({
                basemap: "streets",
                layers: [resultsLayer, stationLayer, foodLayer, route_spotsLayer, routeLayer, s_l_pointLayer, l_d_pointLayer, d_g_pointLayer]
            });

            //frameの変数
            var center = <?php echo json_encode($center); ?>;
            const view = new MapView({
                container: "viewDiv", // Reference to the scene div created in step 5
                map: map, // Reference to the map object created before the scene
                center: center,
                zoom: 14,
                popup: {
                    dockEnabled: true,
                    dockOptions: {
                        breakpoint: false
                    }
                }
            });

            //phpの経路情報をjavascript用に変換           
            var keikaku = <?php echo json_encode($keikaku); ?>;

            function display_route(plan) {
                //前回の経路を、グラフィックスレイヤーから削除
                routeLayer.removeAll();
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
                        routeParams.stops.features.push(stop);
                    }
                }
                if (routeParams.stops.features.length >= 2) {
                    route.solve(routeUrl, routeParams).then(showRoute);
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

            $results_form = document.getElementById("result_table");
            //初期検索範囲と初期カテゴリー
            $search_distance = <?php echo json_encode($search_distance); ?>;
            $categoryName = <?php echo json_encode($categoryName); ?>;
            //ルート形状沿いの観光地検索
            queryAroundSpot = (geom) => {
                spot_array = [];
                let query = spotLayer.createQuery();
                query.geometry = geom;
                query.outFields = ["*"];
                //カテゴリーでの検索
                if ($categoryName != "0") {
                    query.where = "category = '" + $categoryName + "'";
                }
                query.distance = $search_distance;
                query.units = "meters";
                //観光スポットに対する検索の実行
                spotLayer.queryFeatures(query).then(function(featureSet) {
                    var result_fs = featureSet.features;

                    $results_form.innerHTML = "";
                    if (result_fs.length === 0) {
                        $results_form.innerHTML = "検索条件に該当する観光スポットはありませんでした";
                    }

                    //前回の検索結果を、グラフィックスレイヤーから削除
                    resultsLayer.removeAll();
                    //検索結果に対する設定
                    var features = result_fs.map(function(graphic) {
                        graphic.symbol = {
                            type: "simple-marker",
                            style: "diamond",
                            size: 12.0,
                            color: "darkorange"
                        };
                        graphic.popupTemplate = spots_template;
                        $say = [graphic.attributes.id, graphic.attributes.name, graphic.attributes.category, graphic.attributes.urls];
                        spot_array.push($say);
                        return graphic;
                    });
                    
                    //今回のクリックによる検索結果を、グラフィックスレイヤーに登録（マップに表示）
                    resultsLayer.addMany(features);

                    var test_row = spot_array;
                    var table_column = ["ID", "スポット名", "カテゴリー", "ホームページ"];
                    make_spots_table(test_row, table_column);
                })
            };

            // ルート表示用のレイヤーにデータを追加　＋　周辺スポット表示関数呼び出し
            function showRoute(data) {
                const routeResult = data.routeResults[0].route;
                routeResult.symbol = routeArrowSymbol;
                routeLayer.add(routeResult);
                queryAroundSpot(routeResult.geometry);
                $route_result_data = routeResult.geometry;

                $totalLength = data.routeResults[0].directions.totalLength;
            }

        });

        //表示する観光スポットのカテゴリーを変える
        function change_distance(distance) {
            $search_distance = distance;
            //queryAroundSpot($route_result_data);
        }

        //表示する観光スポットのカテゴリーを変える
        function change_category(category_name) {
            $categoryName = category_name;
            //queryAroundSpot($route_result_data);
        }

        function decimalPart(num, decDigits) {
            var decPart = num - ((num >= 0) ? Math.floor(num) : Math.ceil(num));
            return decPart.toFixed(decDigits);
        }

        function doc() {
            var km = $totalLength.toPrecision(3);
            alert("総歩行距離" + km + " km");
            var time = ($totalLength / 4.8);
            var hour = Math.trunc(time);
            var mini = 60 * decimalPart(time, 1);
            alert("総歩行時間" + hour + "時間" + mini + "分");
        }

        function display_results() {
            queryAroundSpot($route_result_data);
        }

        var area_name = <?php echo json_encode($area_name); ?>;

        function make_spots_table(array, columns) {
            $result_spots_form = document.getElementById("result_spots_table");
            $result_spots_form.innerHTML = "";
            $result_spots_form.className = 'tables';

            for (var i = 0; i < array.length; i++) {
                const a_id = array[i][0];
                const a_name = array[i][1];
                const a_category = array[i][2];
                const a_urls = array[i][3];

                if (a_urls == null) {
                    $a_page = "<a>なし</a>";
                } else {
                    $a_page = `<a href="${a_urls}" target=_blank>ホームページにアクセスする</a>`;
                }
                //表示するhtmlの作成
                const newDiv = document.createElement("div");
                newDiv.className = 'search_result';
                newDiv.innerHTML = `
                <div class="card bg-light mb-3" style="width: 80rem;" id="infobox" value=${a_id}>
                    <div class="row g-0">
                        <div class="col-md-5">
                            <img class="img-fluid rounded-start" src="images/${area_name}/sightseeing_spots/${a_id}.jpg" alt="">
                        </div>
                        <div class="search_results col-md-12">
                            <table class="table card-body">
                                <tr>
                                    <th>スポット名</th>
                                    <td>${a_name}</td>
                                </tr>
                                <tr>
                                    <th>カテゴリー</th>
                                    <td>${a_category}</td>
                                </tr>
                                <tr>
                                    <th>ホームページURL</th>
                                    <td>${$a_page}</td>
                                </tr>
                                <tr>
                                    <th>地図付き詳細ページへ</th>
                                    <td>
                                        <a href="sightseeing_spot_detail.php?spot_id=${a_id}">詳細ページに移動する</a>
                                    </td>
                                </tr>
                            </table>
                            <a href="#search_start">▲ページ上部に戻る</a>
                        </div>
                    </div>
                </div><br>
                `;
                $result_spots_form.appendChild(newDiv);
            }
        }
    </script>

</head>

<body>
    <div class="container-fluid">
        <main class="row">
            <div>
                <font color="#ff0000"><?php echo htmlspecialchars($message, ENT_QUOTES); ?></font>
            </div>
            <h3 class="px-0" id="search_start">観光スポット選択</h3>
            <div>
                <ol class="stepBar">
                    <li class="visited" onclick="location.href='set_station.php'"><span>1</span><br>開始・終了駅</li>
                    <li class="visited" onclick="location.href='search_map.php'"><span>2</span><br>飲食店</li>
                    <li class="visited" onclick="location.href='sightseeing_spots_selection_map.php'"><span>3</span><br>観光スポット</li>
                    <li onclick="location.href='plan_edit.php'"><span>4</span><br>観光計画を保存</li>
                </ol>
            </div>
            <a id="list_result" name="list_result" href="sightseeing_spots_selection_map.php">地図上で結果を表示</a><br>
            <div class="search_form">
                <form action="sightseeing_spots_selection.php" method="post">
                    経路からの観光スポット表示範囲：<br>
                    <input type="radio" id="search_spots_distance" name="search_spots_distance" value="100000" onclick="change_distance(value) ; keep_radio(value, '1')" <?php set_checked("search_spots_distance", "100000"); ?>>指定なし
                    <input type="radio" id="search_spots_distance" name="search_spots_distance" value="100" onclick="change_distance(value) ; keep_radio(value, '1')" <?php set_checked("search_spots_distance", "100"); ?>>周囲100m
                    <input type="radio" id="search_spots_distance" name="search_spots_distance" value="200" onclick="change_distance(value) ; keep_radio(value, '1')" <?php set_checked("search_spots_distance", "200"); ?>>周囲200m
                    <input type="radio" id="search_spots_distance" name="search_spots_distance" value="300" onclick="change_distance(value) ; keep_radio(value, '1')" <?php set_checked("search_spots_distance", "300"); ?>>周囲300m
                    <input type="radio" id="search_spots_distance" name="search_spots_distance" value="400" onclick="change_distance(value) ; keep_radio(value, '1')" <?php set_checked("search_spots_distance", "400"); ?>>周囲400m
                    <input type="radio" id="search_spots_distance" name="search_spots_distance" value="500" onclick="change_distance(value) ; keep_radio(value, '1')" <?php set_checked("search_spots_distance", "500"); ?>>周囲500m
                    <input type="radio" id="search_spots_distance" name="search_spots_distance" value="600" onclick="change_distance(value) ; keep_radio(value, '1')" <?php set_checked("search_spots_distance", "600"); ?>>周囲600m<br>

                    観光スポットのカテゴリー：<br>
                    <input type="radio" id="search_spots_category" name="search_spots_category" value="0" onclick="change_category(value) ; keep_radio(value, '2')" <?php set_checked("search_spots_category", "0"); ?>>指定なし
                    <input type="radio" id="search_spots_category" name="search_spots_category" value="名所・史跡" onclick="change_category(value) ; keep_radio(value, '2')" <?php set_checked("search_spots_category", "名所・史跡"); ?>>名所・史跡
                    <input type="radio" id="search_spots_category" name="search_spots_category" value="ショッピング" onclick="change_category(value) ; keep_radio(value, '2')" <?php set_checked("search_spots_category", "ショッピング"); ?>>ショッピング
                    <input type="radio" id="search_spots_category" name="search_spots_category" value="芸術・博物館" onclick="change_category(value) ; keep_radio(value, '2')" <?php set_checked("search_spots_category", "芸術・博物館"); ?>>芸術・博物館
                    <input type="radio" id="search_spots_category" name="search_spots_category" value="テーマパーク・公園" onclick="change_category(value) ; keep_radio(value, '2')" <?php set_checked("search_spots_category", "テーマパーク・公園"); ?>>テーマパーク・公園
                    <input type="radio" id="search_spots_category" name="search_spots_category" value="その他" onclick="change_category(value) ; keep_radio(value, '2')" <?php set_checked("search_spots_category", "その他"); ?>>その他<br>

                    <input type="submit" name="submit" value="検索する">
                </form>
                <!--<button type="button" onclick="display_results()">観光スポットを絞り込む</button>-->
            </div><br>
            <!--
            <div class="move_box">
                <a class="prev_page" name="prev_search" href="search.php">飲食店検索・決定に戻る</a>
                <a class="next_page" name="see_myroute" href="plan_edit.php">作成した観光経路を見るへ</a>
            </div><br>
            -->
            <div id="result_table"></div><br>
            <div id="result_spots_table"></div>
        </main>
        <footer>
            <p>Copyright(c) 2023 山本佳世子研究室 All Rights Reserved.</p>
        </footer>
    </div>
</body>

</html>
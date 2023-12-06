<?php

require "frame_define.php";
require "frame_header.php";
require "frame_menu.php";
require "frame_rightmenu.php";
$plan_id = $_GET["plan_id"];

function set_checked($session_name, $value)
{
    if ($value == $_SESSION[$session_name]) {
        //値がセッション変数と等しいとチェックされてる判定として返す
        print "checked=\"checked\"";
    } else {
        print "";
    }
}

$message = "";

//DB接続
try {
    $stmt = $pdo->prepare("SELECT * FROM userplan WHERE id = :id");
    $stmt->bindParam(":id", $plan_id);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $s_l_ids = explode(",", $result["s_l"]);
    $s_l_times = explode(",", $result["s_l_times"]);
    $l_d_ids = explode(",", $result["l_d"]);
    $l_d_times = explode(",", $result["l_d_times"]);
    $d_g_ids = explode(",", $result["d_g"]);
    $d_g_times = explode(",", $result["d_g_times"]);

    //stations_id設定
    if ($result["plan_start"] == -1) {
        $start_station_id = 0;
        $start_info = [0, 0, "start"];
    } else {
        $start_station_id = $result["plan_start"];
        $stmt1 = $pdo->prepare("SELECT * FROM $database_stations WHERE id = :id");
        $stmt1->bindParam(":id", $start_station_id);
        $stmt1->execute();
        $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);
        $start_info = [$result1["x"], $result1["y"], "start"];
        $side_start_station_name = $result1["name"]; //
    }
    if ($result["plan_goal"] == -1) {
        $goal_station_id = 0;
        $goal_info = [0, 0, "goal"];
    } else {
        $goal_station_id = $result["plan_goal"];
        $stmt4 = $pdo->prepare("SELECT * FROM $database_stations WHERE id = :id");
        $stmt4->bindParam(":id", $goal_station_id);
        $stmt4->execute();
        $result4 = $stmt4->fetch(PDO::FETCH_ASSOC);
        $goal_info = [$result4["x"], $result4["y"], "goal"];
        $side_goal_station_name = $result4["name"]; //
    }
    $station_id = [$start_station_id, $goal_station_id];

    //food_shops_id設定
    if ($result["lunch"] == -1) {
        $lunch_shop_id = -1;
        $lunch_info = [0, 0, "lunch"];
        $side_lunch_name = "設定されていません"; //
        $side_lunch_time = 0; //
    } else {
        $lunch_shop_id = $result["lunch"];
        $stmt2 = $pdo->prepare("SELECT * FROM $database_restaurants WHERE id = :id");
        $stmt2->bindParam(":id", $lunch_shop_id);
        $stmt2->execute();
        $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
        $lunch_info = [$result2["x"], $result2["y"], "lunch"];
        $side_lunch_name = $result2["name"]; //
        $side_lunch_time = $result["lunch_time"]; //
    }
    if ($result["dinner"] == -1) {
        $dinner_shop_id = -1;
        $dinner_info = [0, 0, "dinner"];
        $side_dinner_name = "設定されていません"; //
        $side_dinner_time = 0; //
    } else {
        $dinner_shop_id = $result["dinner"];
        $stmt3 = $pdo->prepare("SELECT * FROM $database_restaurants WHERE id = :id");
        $stmt3->bindParam(":id", $dinner_shop_id);
        $stmt3->execute();
        $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);
        $dinner_info = [$result3["x"], $result3["y"], "dinner"];
        $side_dinner_name = $result3["name"]; //
        $side_dinner_time = $result["dinner_time"]; //
    }
    $food_shop_id = [$lunch_shop_id, $dinner_shop_id];

    //spots設定
    $spot_count = 10;
    if ($result["s_l"] == -1) {
        $s_l_ids = [0]; //
        $s_l_times = [0]; //
        $s_l_names = ["設定されていません"]; //
        $s_l_spots = [[0, 0, 0]];
    } else {
        $s_l_ids = explode(",", $result["s_l"]); //
        $s_l_times = explode(",", $result["s_l_times"]); //
        foreach ($s_l_ids as $s_l) {
            $stmt5 = $pdo->prepare("SELECT * FROM $database_sightseeing_spots WHERE id = :id");
            $stmt5->bindParam(":id", $s_l);
            $stmt5->execute();
            $result5 = $stmt5->fetch(PDO::FETCH_ASSOC);
            $spot_count += 1;
            $s_l_spots[] = [$result5["x"], $result5["y"], $spot_count];
            $s_l_names[] = $result5["name"]; //
        }
    }
    $spot_count = 20;
    if ($result["l_d"] == -1) {
        $l_d_ids = [0]; //
        $l_d_times = [0]; //
        $l_d_names = ["設定されていません"]; //
        $l_d_spots = [[0, 0, 0]];
    } else {
        $l_d_ids = explode(",", $result["l_d"]); //
        $l_d_times = explode(",", $result["l_d_times"]); //
        foreach ($l_d_ids as $l_d) {
            $stmt6 = $pdo->prepare("SELECT * FROM $database_sightseeing_spots WHERE id = :id");
            $stmt6->bindParam(":id", $l_d);
            $stmt6->execute();
            $result6 = $stmt6->fetch(PDO::FETCH_ASSOC);
            $spot_count += 1;
            $l_d_spots[] = [$result6["x"], $result6["y"], $spot_count];
            $l_d_names[] = $result6["name"]; //
        }
    }
    $spot_count = 30;
    if ($result["d_g"] == -1) {
        $d_g_ids = [0]; //
        $d_g_times = [0]; //
        $d_g_names = ["設定されていません"]; //
        $d_g_spots = [[0, 0, 0]];
    } else {
        $d_g_ids = explode(",", $result["d_g"]); //
        $d_g_times = explode(",", $result["d_g_times"]); //
        foreach ($d_g_ids as $d_g) {
            $stmt7 = $pdo->prepare("SELECT * FROM $database_sightseeing_spots WHERE id = :id");
            $stmt7->bindParam(":id", $d_g);
            $stmt7->execute();
            $result7 = $stmt7->fetch(PDO::FETCH_ASSOC);
            $spot_count += 1;
            $d_g_spots[] = [$result7["x"], $result7["y"], $spot_count];
            $d_g_names[] = $result7["name"]; //
        }
    }

    $spots_id = array_merge($s_l_ids, $l_d_ids, $d_g_ids);
} catch (PDOException $e) {
}
$side_s_l_spots = array_map(NULL, $s_l_ids, $s_l_times, $s_l_names);
$side_l_d_spots = array_map(NULL, $l_d_ids, $l_d_times, $l_d_names);
$side_d_g_spots = array_map(NULL, $d_g_ids, $d_g_times, $d_g_names);
$side_display_plan = [
    ["start", $side_start_station_name],
    ["s_l", $side_s_l_spots],
    ["lunch", $side_lunch_name, $side_lunch_time],
    ["l_d", $side_l_d_spots],
    ["dinner", $side_dinner_name, $side_dinner_time],
    ["d_g", $side_d_g_spots],
    ["goal", $side_goal_station_name]
];

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
    <title>作成した観光計画を見る</title>
    <style>
        #viewbox {
            position: relative;
            float: left;
            width: 77vw;
            height: 60vh;
            margin-left: 5px;
        }

        #viewbox #viewDiv {
            position: relative;
            padding: 0;
            margin: 0;
            height: 100%;
            width: 77vw;
        }

        @media screen and (min-width:769px) and (max-width:1366px) {
            #viewbox {
                width: 77vw;
                height: 70vh;
            }
        }

        @media screen and (max-width:768px) {
            #viewbox {
                position: relative;
                float: left;
                width: 100vw;
                height: 60vh;
                margin: 0px;
            }

            #viewbox #viewDiv {
                width: 100%;
                height: 90%;
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
            const MY_API_KEY = "AAPKfe5fdd5be2744698a188fcc0c7b7b1d742vtC5TsStg94fpwkldrfNo3SJn2jl_VuCOEEdcBiwR7dKOKxejIP_3EDj9IPSPg";

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
                definitionExpression: food_feature_sql,
                labelingInfo: [labelClass]
            });

            var stationLayer = new FeatureLayer({
                url: <?php echo json_encode($map_stations); ?>,
                id: "stationLayer",
                popupTemplate: station_template,
                definitionExpression: station_feature_sql,
                labelingInfo: [labelClass]
            });

            var spotsLayer = new FeatureLayer({
                url: <?php echo json_encode($map_sightseeing_spots); ?>,
                id: "spotsLayer",
                popupTemplate: spots_template,
                definitionExpression: spots_feature_sql,
                labelingInfo: [labelClass]
            });

            //ルート表示のレイヤー
            const routeLayer = new GraphicsLayer();

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
                layers: [stationLayer, foodLayer, spotsLayer, routeLayer]
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
                //routeLayer.removeAll();
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

            // ルート表示用のレイヤーにデータを追加
            function showRoute(data) {
                const routeResult = data.routeResults[0].route;
                routeResult.symbol = routeArrowSymbol;
                routeLayer.add(routeResult);
                //$route_result_data = routeResult.geometry;
                //総距離
                $totalLength = data.routeResults[0].directions.totalLength;
                doc();
            }

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

        //観光経路表示を更新する
        function kousin() {
            location.reload();
        }

        function decimalPart(num, decDigits) {
            var decPart = num - ((num >= 0) ? Math.floor(num) : Math.ceil(num));
            return decPart.toFixed(decDigits);
        }

        function doc() {
            var km = $totalLength.toPrecision(3);
            $length = "総歩行距離：" + km + " km";
            //alert($length);
            var time = ($totalLength / 4.8);
            var hour = Math.trunc(time);
            var mini = 60 * decimalPart(time, 1);
            $time = "総歩行時間：" + hour + "時間" + mini + "分";

            var user_weight = <?php echo json_encode($frameresult["user_weight"]); ?>;
            if (user_weight > 0) {
                var cal = 3.5 * time * user_weight * 1.05;
                $kcal = "消費カロリー：" + cal.toPrecision(4) + "kcal";
            } else {
                $kcal = "消費カロリー：計算できませんでした";
            }
            //alert($time);
            //frameの関数
            update_frame($length, "length_km");
            update_frame($time, "time_h_m");
            update_frame($kcal, "cal_k");
        }

        var plan_id = <?php echo json_encode($plan_id); ?>;

        function copy_plan() {
            var mode = 1;
            if (window.confirm('現在作成している観光計画を上書きしますがよろしいですか？')) {
                jQuery(function($) {
                    $.ajax({
                        url: "ajax_copy_plan.php",
                        type: "POST",
                        dataType: "json",
                        data: {
                            post_data_1: plan_id,
                            post_data_2: mode
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            alert("ajax通信に失敗しました");
                        },
                        success: function(response) {
                            alert(response);
                            window.location.href = "plan_edit.php";
                        }
                    });
                });
            } else {

            }
        };

        function delete_plan() {
            if (window.confirm('この観光計画を削除しますがよろしいですか？')) {
                jQuery(function($) {
                    $.ajax({
                        url: "ajax_delete_plan.php",
                        type: "POST",
                        dataType: "json",
                        data: {
                            post_data_1: plan_id
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            alert("ajax通信に失敗しました");
                        },
                        success: function(response) {
                            alert(response);
                            window.location.href = "user_plans.php";
                        }
                    });
                });
            } else {

            }
        };
    </script>



</head>

<body>
    <div id="leftbox">

        <h2>観光計画</h2>

        <div id="other_plan_box">
            <div class="sortable">
                開始駅<br>
                <ul>
                    <li class="card" id="other_plan_start_box" value="">
                        <div class="card-body p-2">
                            <img id="pin" width="20" height="20" src="./icons/pop_start.png" alt="開始駅のアイコン" title="開始駅">
                            <?php echo $side_start_station_name ?><br>
                        </div>
                    </li>
                </ul>
            </div>

            <?php if ($side_s_l_spots[0][2] != "設定されていません") { ?>
                <div class="sortable">
                    昼食前に訪れる観光スポット<br>
                    <ul>
                        <?php $side_count = 0; ?>
                        <?php foreach ($side_s_l_spots as $date) { ?>
                            <?php $side_count += 1; ?>
                            <li class="card">
                                <div class="card-body p-2">
                                    <img width="20" height="20" src=<?php echo "./icons/pop_icon_s_l" . $side_count . ".png"; ?> alt="昼食前に訪れる観光スポットのアイコン" title="昼食前に訪れる観光スポット">
                                    <div><?php echo $date[2] ?></div>
                                    <input disabled class="time" type="number" value="<?php echo $date[1]; ?>">分
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                    <input type="hidden" id="list-ids" name="list-ids" />
                </div>
            <?php } ?>

            <?php if ($side_lunch_name != "設定されていません") { ?>
                <div class="sortable">
                    昼食を食べる飲食店<br>
                    <ul>
                        <li class="card" id="" value="">
                            <div class="card-body p-2">
                                <img id="pin" width="20" height="20" src="./icons/pop_lunch.png" alt="昼食予定地のアイコン" title="昼食予定地">
                                <?php echo $side_lunch_name ?><br>
                                <input disabled class="time" type="number" value="<?php echo $side_lunch_time; ?>">分
                            </div>
                        </li>
                    </ul>
                </div>
            <?php } ?>

            <?php if ($side_l_d_spots[0][2] != "設定されていません") { ?>
                <div class="sortable">
                    昼食後に訪れる観光スポット<br>
                    <ul>
                        <?php $side_count = 0; ?>
                        <?php foreach ($side_l_d_spots as $date) { ?>
                            <?php $side_count += 1; ?>
                            <li class="card">
                                <div class="card-body p-2">
                                    <img width="20" height="20" src=<?php echo "./icons/pop_icon_l_d" . $side_count . ".png"; ?> alt="昼食後に訪れる観光スポットのアイコン" title="昼食後に訪れる観光スポット">
                                    <div><?php echo $date[2] ?></div>
                                    <input disabled class="time" type="number" value="<?php echo $date[1]; ?>">分
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                    <input type="hidden" id="list-ids" name="list-ids" />
                </div>
            <?php } ?>

            <?php if ($side_dinner_name != "設定されていません") { ?>
                <div class="sortable">
                    夕食を食べる飲食店<br>
                    <ul>
                        <li class="card" id="" value="">
                            <div class="card-body p-2">
                                <img id="pin" width="20" height="20" src="./icons/pop_dinner.png" alt="夕食予定地のアイコン" title="夕食予定地">
                                <?php echo $side_dinner_name ?><br>
                                <input disabled class="time" type="number" value="<?php echo $side_dinner_time; ?>">分
                            </div>
                        </li>
                    </ul>
                </div>
            <?php } ?>

            <?php if ($side_d_g_spots[0][2] != "設定されていません") { ?>
                <div class="sortable">
                    夕食後に訪れる観光スポット<br>
                    <ul>
                        <?php $side_count = 0; ?>
                        <?php foreach ($side_d_g_spots as $date) { ?>
                            <?php $side_count += 1; ?>
                            <li class="card">
                                <div class="card-body p-2">
                                    <img width="20" height="20" src=<?php echo "./icons/pop_icon_d_g" . $side_count . ".png"; ?> alt="夕食後に訪れる観光スポットのアイコン" title="夕食後に訪れる観光スポット">
                                    <div><?php echo $date[2] ?></div>
                                    <input disabled class="time" type="number" value="<?php echo $date[1]; ?>">分
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                    <input type="hidden" id="list-ids" name="list-ids" />
                </div>
            <?php } ?>

            <div class="sortable">
                終了駅<br>
                <ul>
                    <li class="card" id="plan_goal_box" value="">
                        <div class="card-body p-2">
                            <img id="pin" width="20" height="20" src="./icons/pop_goal.png" alt="終了駅のアイコン" title="終了駅">
                            <div class="plan_goal_name"><?php echo $side_goal_station_name ?></div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>

    </div>

    <div class="container-fluid">
        <main class="row">
            <div>
                <font color="#ff0000"><?php echo htmlspecialchars($message, ENT_QUOTES); ?></font>
            </div>

            <h3 class="px-0" >プラン詳細</h3>
            <div class="plan_explain">
                プラン名：<?php echo $result["plan_name"]; ?><br>
                <b>
                    <div id="cal_k">消費カロリー：0.00 kcal</div>
                </b>
                <b>
                    <div id="length_km">総歩行距離：0.00 km</div>
                </b>
                <b>
                    <div id="time_h_m">総歩行時間：0時間0分</div>
                </b><br>
                <b>
                    <div>
                        説明：<br>
                        <?php echo $result["memo"]; ?>
                    </div>
                </b>
            </div>

            <div class="icon_explain">
                <img class="pin_list1" src="./markers/icon_explain_s_f.png" alt="昼食予定地のアイコン" title="アイコン説明１">
                <img class="pin_list2" src="./markers/icon_explain_spots.png" alt="昼食予定地のアイコン" title="アイコン説明２">
            </div>
            <div id="viewbox">
                <div id="viewDiv"></div>
                <button type="button" class="btn btn-secondary btn-lg" onclick="copy_plan()" title="観光計画を編集"><b>観光計画を編集します</b></button>
                <button type="button" class="btn btn-secondary btn-lg" onclick="delete_plan()" title="観光計画を編集"><b>観光計画を削除します</b></button>
            </div>
        </main>
        <footer>
            <p>Copyright(c) 2021 山本佳世子研究室 All Rights Reserved.</p>
        </footer>
    </div>
</body>

</html>
<?php

require "frame.php";

//入力情報保存用のSESSION変数初期値設定
if (!isset($_SESSION["input_plan_name"])) {
    $_SESSION["input_plan_name"] = "";
}
$input_plan_name = $_SESSION["input_plan_name"];
if (!isset($_SESSION["input_plan_memo"])) {
    $_SESSION["input_plan_memo"] = "";
} else {}
$input_plan_memo = $_SESSION["input_plan_memo"];

if (!isset($_SESSION["plan_show"])) {
    $_SESSION["plan_show"] = 1;
}

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
$set_stations = 0;
$set_foods = 0;
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
//$_SESSION["s_l_spots"] = [1,2];
//$_SESSION["l_d_spots"] = [3,4];
//$_SESSION["d_g_spots"] = [5,6];

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
//var_dump($keikaku);
//var_dump($_SESSION["plan_show"]);
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
        .icon_explain {
            position: relative;
            float: left;
            width: 100%;
            height: 15%;
        }

        .pin_list1 {
            width: 315px;
            height: 75px;
        }

        .pin_list2 {
            width: 390px;
            height: 75px;
        }

        .pin_list3 {
            width: 192px;
            height: 75px;
        }

        #viewbox #btn {
            width: 80%;
            height: 40px;
            color: #fff;
            background-color: #3399ff;
            border-bottom: 5px solid #33ccff;
            -webkit-box-shadow: 0 3px 5px rgba(0, 0, 0, .3);
            box-shadow: 0 3px 5px rgba(0, 0, 0, .3);
        }

        #viewbox #btn:hover {
            margin-top: 3px;
            color: #fff;
            background: #0099ff;
            border-bottom: 2px solid #00ccff;
        }

        @media screen and (min-width:769px) and (max-width:1366px) {}

        @media screen and (max-width:768px) {

            h3 {
                margin: 0px;
                font-size: 17px;
            }

            .icon_explain {
                width: 95vw;
            }

            .pin_list1 {
                width: 100%;
                height: 100%;
            }

            .pin_list2 {
                width: 100%;
                height: 100%;
            }

            .pin_list3 {
                width: 100%;
                height: 100%;
            }

            .container {
                display: flex;
                flex-direction: column;
                min-height: 160vh;
            }
        }

        .flex_test-box {
                background-color: #eee;     /* 背景色指定 */
                padding:  10px;             /* 余白指定 */
                display: flex;              /* フレックスボックスにする */
                align-items:stretch;        /* 縦の位置指定 */
            }

            .flex_test-item {
                padding: 10px;
                color:  #0a0000;               /* 文字色 */
                margin:  10px;              /* 外側の余白 */
                border-radius:  5px;        /* 角丸指定 */
                width: 15%;                 /* 幅指定 */
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

            .flex_test-item:nth-child(1) {
                background-color:  #fff; /* 背景色指定 */
            }

            .flex_test-item:nth-child(2) {
                background-color:  #fff; /* 背景色指定 */
            }

            .flex_test-item:nth-child(3) {
                background-color: #fff; /* 背景色指定 */
            }

            .flex_test-item:nth-child(4) {
                background-color:  #fff; /* 背景色指定 */
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

            /*
            //読み込みせずに経路を更新したかった
            function remake_route() {
                jQuery(function($) {
                    const dummy = 1;
                    $.ajax({
                        url: "./ajax_remake_route.php",
                        type: "POST",
                        dataType: "json",
                        data: {
                            post_data_1: dummy
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            alert("ajax通信に失敗しました");
                        },
                        success: function(response) {
                            display_route(response);
                        }
                    });
                });
            };
            */

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
            //alert($time);
            //frameの関数
            update_frame($length, "length_km");
            update_frame($time, "time_h_m");
        }

        //データベースに観光計画を保存する
        //phpの情報をjavascript用に変換           
        var set_stations = <?php echo json_encode($set_stations); ?>;
        var set_foods = <?php echo json_encode($set_foods); ?>;

        function upload_plan() {
            var radios = document.getElementsByName("plan_show");
            for(var i=0; i<radios.length; i++){
                if (radios[i].checked) {
                //選択されたラジオボタンのvalue値を取得する
                mode = radios[i].value;
                break;
                }
            }
            var plan_name = document.getElementById("plan_name").value;
            var plan_memo = document.getElementById("plan_comment").value;
            var area = <?php echo json_encode($area); ?>;
            if (plan_name != "") {
                jQuery(function($) {
                    $.ajax({
                        url: "./ajax_saving_plan.php",
                        type: "POST",
                        dataType: "json",
                        data: {
                            post_data_1: mode,
                            post_data_2: plan_name,
                            post_data_3: plan_memo,
                            post_data_4: area
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            alert("ajax通信に失敗しました");
                        },
                        success: function(response) {
                            alert(response);
                        }
                    });
                });
            } else {
                alert("プラン名を登録してください");
            }
        }

    </script>

</head>

<body>
    <div class="container">
        <main>
            <div>
                <font color="#ff0000"><?php echo htmlspecialchars($message, ENT_QUOTES); ?></font>
            </div>
            <h3>作成した観光計画</h3>
            <div class="icon_explain">
                <b>
                    <div id="calo_km">正味カロリー：1312.00 kcal<br>
                                      （摂取カロリー 1400 kcal - 消費カロリー 88 kcal）</div>
                </b>
                <b>
                    <div id="length_km">総歩行距離：0.00 km</div>
                </b>
                <b>
                    <div id="time_h_m">総歩行時間：0時間0分</div>
                </b><br>
            </div>

            <div class="move_box">
                <a class="prev_page" name="prev_keiro" href="sightseeing_spots_selection_map.php">観光スポット選択に戻る</a>
            </div><br>
            <div class="icon_explain">
                <img class="pin_list1" src="./markers/icon_explain_s_f.png" alt="昼食予定地のアイコン" title="アイコン説明１">
                <img class="pin_list2" src="./markers/icon_explain_spots.png" alt="昼食予定地のアイコン" title="アイコン説明２">
            </div>
            <div id="viewbox">
                <div id="viewDiv"></div>
                <p>プラン名：<br>
	            <input type="text" id="plan_name" size="15" value="<?php echo $input_plan_name; ?>"></p>
                <p>メモ：<br>
	            <textarea id="plan_comment"><?php echo $input_plan_memo; ?></textarea><br>
                <p>観光計画を公開しますか？：<br>
                <input type="radio" id="plan_show" name="plan_show" value="1" <?php set_checked("plan_show", "1"); ?>>公開する
                <input type="radio" id="plan_show" name="plan_show" value="0" <?php set_checked("plan_show", "0"); ?>>公開しない<br>
                <button type="button" id="btn" onclick="upload_plan()" title="観光経路を保存します"><b>観光計画を保存する</b></button>
            </div>
        </main>
        <footer>
            <p>Copyright(c) 2021 山本佳世子研究室 All Rights Reserved.</p>
        </footer>
    </div>
</body>

</html>
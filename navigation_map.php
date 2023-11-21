<?php

require "frame.php";

$message = "";
if (!isset($_SESSION["start_station_id"]) || !isset($_SESSION["goal_station_id"])) {
    $message = "開始・終了駅が設定されていません";
} else if (!isset($_SESSION["lanch_id"]) || !isset($_SESSION["dinner_id"])) {
    $message = "昼食・夕食予定地が設定されていません";
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
if (!isset($_SESSION["lanch_id"])) {
    $lanch_shop_id = 0;
} else {
    $lanch_shop_id = $_SESSION["lanch_id"];
}
if (!isset($_SESSION["dinner_id"])) {
    $dinner_shop_id = 0;
} else {
    $dinner_shop_id = $_SESSION["dinner_id"];
}
$food_shop_id = [$lanch_shop_id, $dinner_shop_id];

//sightseeing_spots_id設定
if (!isset($_SESSION["s_l_sightseeing_spots_id"])) {
    $s_l_ids = [0];
} else {
    $s_l_ids = $_SESSION["s_l_sightseeing_spots_id"];
}
if (!isset($_SESSION["l_d_sightseeing_spots_id"])) {
    $l_d_ids = [0];
} else {
    $l_d_ids = $_SESSION["l_d_sightseeing_spots_id"];
}
if (!isset($_SESSION["d_g_sightseeing_spots_id"])) {
    $d_g_ids = [0];
} else {
    $d_g_ids = $_SESSION["d_g_sightseeing_spots_id"];
}
$spots_id = array_merge($s_l_ids, $l_d_ids, $d_g_ids);

//デバッグ用
//$_SESSION["s_l_sightseeing_spots_id"] = [1,2];
//$_SESSION["l_d_sightseeing_spots_id"] = [3,4];
//$_SESSION["d_g_sightseeing_spots_id"] = [5,6];

//DB接続
try {
    if (!isset($_SESSION["start_station_id"])) {
        $start_station_info = [0, 0, "start"];
    } else {
        $stmt1 = $pdo->prepare("SELECT * FROM minatomirai_station_data WHERE id = :id");
        $stmt1->bindParam(":id", $start_station_id);
        $stmt1->execute();
        $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);
        $start_station_info = [$result1["x"], $result1["y"], "start"];
    }

    if (!isset($_SESSION["lanch_id"])) {
        $lanch_info = [0, 0, "lanch"];
    } else {
        $stmt2 = $pdo->prepare("SELECT * FROM minatomirai_shop_data WHERE id = :id");
        $stmt2->bindParam(":id", $lanch_shop_id);
        $stmt2->execute();
        $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
        $lanch_info = [$result2["x"], $result2["y"], "lanch"];
    }

    if (!isset($_SESSION["dinner_id"])) {
        $dinner_info = [0, 0, "dinner"];
    } else {
        $stmt3 = $pdo->prepare("SELECT * FROM minatomirai_shop_data WHERE id = :id");
        $stmt3->bindParam(":id", $dinner_shop_id);
        $stmt3->execute();
        $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);
        $dinner_info = [$result3["x"], $result3["y"], "dinner"];
    }

    if (!isset($_SESSION["goal_station_id"])) {
        $goal_station_info = [0, 0, "goal"];
    } else {
        $stmt4 = $pdo->prepare("SELECT * FROM minatomirai_station_data WHERE id = :id");
        $stmt4->bindParam(":id", $goal_station_id);
        $stmt4->execute();
        $result4 = $stmt4->fetch(PDO::FETCH_ASSOC);
        $goal_station_info = [$result4["x"], $result4["y"], "goal"];
    }

    $spot_count = 10;
    if (!isset($_SESSION["s_l_sightseeing_spots_id"])) {
        $s_l_sightseeing_spots_id = [[0, 0, 0]];
    } else {
        foreach ($_SESSION["s_l_sightseeing_spots_id"] as $s_l) {
            $stmt5 = $pdo->prepare("SELECT * FROM minatomirai_sightseeing_data WHERE id = :id");
            $stmt5->bindParam(":id", $s_l);
            $stmt5->execute();
            $result5 = $stmt5->fetch(PDO::FETCH_ASSOC);
            $spot_count += 1;
            $s_l_sightseeing_spots_id[] = [$result5["x"], $result5["y"], $spot_count];
        }
    }
    $spot_count = 20;
    if (!isset($_SESSION["l_d_sightseeing_spots_id"])) {
        $l_d_sightseeing_spots_id = [[0, 0, 0]];
    } else {
        foreach ($_SESSION["l_d_sightseeing_spots_id"] as $l_d) {
            $stmt6 = $pdo->prepare("SELECT * FROM minatomirai_sightseeing_data WHERE id = :id");
            $stmt6->bindParam(":id", $l_d);
            $stmt6->execute();
            $result6 = $stmt6->fetch(PDO::FETCH_ASSOC);
            $spot_count += 1;
            $l_d_sightseeing_spots_id[] = [$result6["x"], $result6["y"], $spot_count];
        }
    }
    $spot_count = 30;
    if (!isset($_SESSION["d_g_sightseeing_spots_id"])) {
        $d_g_sightseeing_spots_id = [[0, 0, 0]];
    } else {
        foreach ($_SESSION["d_g_sightseeing_spots_id"] as $d_g) {
            $stmt7 = $pdo->prepare("SELECT * FROM minatomirai_sightseeing_data WHERE id = :id");
            $stmt7->bindParam(":id", $d_g);
            $stmt7->execute();
            $result7 = $stmt7->fetch(PDO::FETCH_ASSOC);
            $spot_count += 1;
            $d_g_sightseeing_spots_id[] = [$result7["x"], $result7["y"], $spot_count];
        }
    }
} catch (PDOException $e) {
}

//planは目的地の配列
//planの配列作成
$plan[] = $start_station_info;
foreach ($s_l_sightseeing_spots_id as $s_l_add) {
    $plan[] = $s_l_add;
}
$plan[] = $lanch_info;
foreach ($l_d_sightseeing_spots_id as $l_d_add) {
    $plan[] = $l_d_add;
}
$plan[] = $dinner_info;
foreach ($d_g_sightseeing_spots_id as $d_g_add) {
    $plan[] = $d_g_add;
}
$plan[] = $goal_station_info;


//検索条件の復元
if (!isset($_SESSION["search_spots_distance"])) {
    $_SESSION["search_spots_distance"] = "100";
}
$search_distance = $_SESSION["search_spots_distance"];

if (!isset($_SESSION["search_spots_category"])) {
    $_SESSION["search_spots_category"] = "0";
}
$categoryName = $_SESSION["search_spots_category"];

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
    <title>観光スポット選択（地図上表示）</title>
    <style>
        h3 {
            border-left: 5px solid #000080;
            margin: 0px;
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

        .move_box {
            position: relative;
            width: 76vw;
            float: left;
        }

        @media screen and (max-width:768px) {
            h3 {
                margin: 0px;
                font-size: 17px;
            }

            .search_form {
                font-size: 12px;
            }

            .move_box {
                width: 100%;
            }

            .container {
                display: flex;
                flex-direction: column;
                min-height: 160vh;
            }
        }
    </style>

    <link rel="stylesheet" href="https://js.arcgis.com/4.21/esri/themes/light/main.css" />
    <script src="https://js.arcgis.com/4.21/"></script>
    <!-- <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> -->

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
            //popup
            var routeAction = {
                title: "ルートに追加する",
                id: "route",
                className: "esri-icon-navigation"
            };

            var s_l_Action = {
                title: "昼食前に訪れる",
                id: "s_l",
                image: "pop_icon1.png"
            };

            var l_d_Action = {
                title: "昼食後に訪れる",
                id: "l_d",
                image: "pop_icon2.png"
            };

            var d_g_Action = {
                title: "夕食後に訪れる",
                id: "d_g",
                image: "pop_icon3.png"
            };

            var detailAction = {
                title: "詳細",
                id: "detail",
                className: "esri-icon-documentation"
            };

            var food_template = {
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
                        fieldName: "time",
                        label: "営業時間",
                        visible: true
                    }, {
                        fieldName: "money",
                        label: "予算",
                        visible: true
                    }, {
                        fieldName: "yoyaku",
                        label: "予約可否",
                        visible: true
                    }, {
                        fieldName: "tel",
                        label: "電話番号",
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
                }],
                actions: [s_l_Action, l_d_Action, d_g_Action, detailAction]
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
                url: "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/minatomirai_sightseeing_UTF_8/FeatureServer",
                id: "roue_spotsLayer",
                popupTemplate: spots_template,
                definitionExpression: spots_feature_sql
            });

            var spotLayer = new FeatureLayer({
                url: "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/minatomirai_sightseeing_UTF_8/FeatureServer",
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
                                width: 6,
                                color: [
                                    255,
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
                                                255,
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

            const view = new MapView({
                container: "viewDiv", // Reference to the scene div created in step 5
                map: map, // Reference to the map object created before the scene
                center: [139.635, 35.453],
                zoom: 14,
                popup: {
                    dockEnabled: true,
                    dockOptions: {
                        breakpoint: false
                    }
                }
            });

            //phpの経路情報をjavascript用に変換           
            var plan = <?php echo json_encode($plan); ?>;

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
                                    pointpic = "./marker/start_and_goal.png";
                                } else {
                                    pointpic = "./marker/start.png";
                                }
                            } else if (plan[j][2] == "lanch") {
                                pointpic = "./marker/lanch.png";
                            } else if (plan[j][2] == "dinner") {
                                pointpic = "./marker/dinner.png";
                            } else if (plan[j][2] == "goal") {
                                if (mode_change == 1) {
                                    pointpic = "./marker/start_and_goal.png";
                                } else {
                                    pointpic = "./marker/goal.png";
                                }
                            } else if (plan[j][2] == 11) {
                                pointpic = "./marker/s_l_spot1.png";
                            } else if (plan[j][2] == 12) {
                                pointpic = "./marker/s_l_spot2.png";
                            } else if (plan[j][2] == 13) {
                                pointpic = "./marker/s_l_spot3.png";
                            } else if (plan[j][2] == 21) {
                                pointpic = "./marker/l_d_spot1.png";
                            } else if (plan[j][2] == 22) {
                                pointpic = "./marker/l_d_spot2.png";
                            } else if (plan[j][2] == 23) {
                                pointpic = "./marker/l_d_spot3.png";
                            } else if (plan[j][2] == 31) {
                                pointpic = "./marker/d_g_spot1.png";
                            } else if (plan[j][2] == 32) {
                                pointpic = "./marker/d_g_spot2.png";
                            } else if (plan[j][2] == 33) {
                                pointpic = "./marker/d_g_spot3.png";
                            } else {
                                pointpic = "./marker/ltblue.png";
                            }
                        }
                        var stopSymbol = new PictureMarkerSymbol({
                            url: pointpic,
                            width: "80px",
                            height: "124px"
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
            display_route(plan);

            //押したボタンによって
            view.popup.on("trigger-action", function(event) {
                if (event.action.id === "s_l") {
                    hozon("1", s_l_pointLayer);
                    //remake_route();
                }
                if (event.action.id === "l_d") {
                    hozon("2", l_d_pointLayer);
                    //remake_route();
                }
                if (event.action.id === "d_g") {
                    hozon("3", d_g_pointLayer);
                    //remake_route();
                }
                if (event.action.id === "detail") {
                    var spot_id = view.popup.selectedFeature.attributes.id;
                    spot_detail(spot_id);
                }
            });

            //スポットの詳細ページに飛ぶときに送信するデータ
            function spot_detail(id) {
                var form = document.createElement('form');
                form.method = 'GET';
                form.action = './spotdetail2.php';
                var reqElm = document.createElement('input');
                reqElm.name = 'spot_id';
                reqElm.value = id;
                form.appendChild(reqElm);
                document.body.appendChild(form);
                form.submit();
            };

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

            function hozon(time, Layer) {
                //スポット取得
                const spot_id = view.popup.selectedFeature.attributes.id;
                jQuery(function($) {
                    $.ajax({
                        url: "./ajax_addspot.php",
                        type: "POST",
                        dataType: "json",
                        data: {
                            post_data_1: spot_id,
                            post_data_2: time
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            alert("ajax通信に失敗しました");
                        },
                        success: function(response) {
                            //alert(response[0]);
                            //esriの関数の外へ
                            toframe(time, response[1]);

                            if (response[0] == "") {
                                alert("同じスポットは登録できません");
                            } else if (response[0] == "3") {
                                alert("各時間帯に登録できるスポットは3つまでです");
                            } else {
                                alert("「" + response[0] + "」を訪問する観光スポットに追加しました");

                                //選択したスポットの座標に印を
                                const point = {
                                    type: "point",
                                    x: view.popup.selectedFeature.attributes.X,
                                    y: view.popup.selectedFeature.attributes.Y
                                };
                                const stop = new Graphic({
                                    geometry: point,
                                    symbol: CheckSymbol
                                });
                                //Layer.removeAll();
                                Layer.add(stop);

                            }
                        }
                    });
                });
            };

            $results_form = document.getElementById("result_table");
            //初期検索範囲と初期カテゴリー
            $search_distance = <?php echo json_encode($search_distance); ?>;
            $categoryName = "中華";
            //$categoryName = <?php //echo json_encode($categoryName); ?>;
            //ルート形状沿いの観光地検索
            queryAroundSpot = (geom) => {
                let query = foodLayer.createQuery();
                query.geometry = geom;
                query.outFields = ["*"];
                //カテゴリーでの検索
                if ($categoryName != "0") {
                    query.where = "category = '" + $categoryName + "'";
                }
                query.distance = $search_distance;
                query.units = "meters";
                //観光スポットに対する検索の実行
                foodLayer.queryFeatures(query).then(function(featureSet) {
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
                            size: 24.0,
                            color: "darkorange"
                        };
                        graphic.popupTemplate = spots_template;
                        //graphic.labelingInfo = [labelClass];
                        return graphic;
                    });
                    //今回のクリックによる検索結果を、グラフィックスレイヤーに登録（マップに表示）
                    resultsLayer.addMany(features);
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

        //検索結果を保存する関数
        function keep_radio(value, mode) {
            jQuery(function($) {
                $.ajax({
                    url: "./ajax_keiro_keepradio.php",
                    type: "POST",
                    dataType: "json",
                    data: {
                        post_data_1: value,
                        post_data_2: mode
                    }
                });
            });
        };

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

        //frame関数内のoverwriteを実行する
        function toframe(time, response) {
            //frameの関数
            overwrite(time, response, 0);
            overwrite(time, response, 1);
        }

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
            alert("総歩行距離" + km + " km");
            var time = ($totalLength / 4.8);
            var hour = Math.trunc(time);
            var mini = 60 * decimalPart(time, 1);
            alert("総歩行時間" + hour + "時間" + mini + "分");
        }

        function display_results() {
            queryAroundSpot($route_result_data);
        }
    </script>

</head>

<body>
    <div class="container">
        <main>
            <div>
                <font color="#ff0000"><?php echo htmlspecialchars($message, ENT_QUOTES); ?></font>
            </div>
            <h3>観光スポット選択</h3>
            <a id="list_result" name="list_result" href="keiro_list.php">一覧で結果を表示</a><br>
            <div class="search_form">
                <form action="">
                    経路からの観光スポット表示範囲：<br>
                    <input type="radio" id="distance" name="distance" value="100000" onclick="change_distance(value) ; keep_radio(value, '1')" <?php set_checked("search_spots_distance", "100000"); ?>>指定なし
                    <input type="radio" id="distance" name="distance" value="100" onclick="change_distance(value) ; keep_radio(value, '1')" <?php set_checked("search_spots_distance", "100"); ?>>周囲100m
                    <input type="radio" id="distance" name="distance" value="200" onclick="change_distance(value) ; keep_radio(value, '1')" <?php set_checked("search_spots_distance", "200"); ?>>周囲200m
                    <input type="radio" id="distance" name="distance" value="300" onclick="change_distance(value) ; keep_radio(value, '1')" <?php set_checked("search_spots_distance", "300"); ?>>周囲300m
                    <input type="radio" id="distance" name="distance" value="400" onclick="change_distance(value) ; keep_radio(value, '1')" <?php set_checked("search_spots_distance", "400"); ?>>周囲400m
                    <input type="radio" id="distance" name="distance" value="500" onclick="change_distance(value) ; keep_radio(value, '1')" <?php set_checked("search_spots_distance", "500"); ?>>周囲500m
                    <input type="radio" id="distance" name="distance" value="600" onclick="change_distance(value) ; keep_radio(value, '1')" <?php set_checked("search_spots_distance", "600"); ?>>周囲600m<br>
                </form>

                <form action="">
                    観光スポットのカテゴリー：<br>
                    <input type="radio" id="category" name="category" value="0" onclick="change_category(value) ; keep_radio(value, '2')" <?php set_checked("search_spots_category", "0"); ?>>指定なし
                    <input type="radio" id="category" name="category" value="名所・史跡" onclick="change_category(value) ; keep_radio(value, '2')" <?php set_checked("search_spots_category", "名所・史跡"); ?>>名所・史跡
                    <input type="radio" id="category" name="category" value="ショッピング" onclick="change_category(value) ; keep_radio(value, '2')" <?php set_checked("search_spots_category", "ショッピング"); ?>>ショッピング
                    <input type="radio" id="category" name="category" value="芸術・博物館" onclick="change_category(value) ; keep_radio(value, '2')" <?php set_checked("search_spots_category", "芸術・博物館"); ?>>芸術・博物館
                    <input type="radio" id="category" name="category" value="テーマパーク・公園" onclick="change_category(value) ; keep_radio(value, '2')" <?php set_checked("search_spots_category", "テーマパーク・公園"); ?>>テーマパーク・公園
                    <input type="radio" id="category" name="category" value="その他" onclick="change_category(value) ; keep_radio(value, '2')" <?php set_checked("search_spots_category", "その他"); ?>>その他<br>
                </form>
                <button type="button" onclick="display_results()">観光スポットを絞り込む</button>
            </div><br>
            <div class="move_box">
                <a class="prev_page" name="prev_search" href="search.php">飲食店検索・決定に戻る</a>
                <a class="next_page" name="see_myroute" href="see_myroute.php">作成した観光経路を見るへ</a>
            </div><br>
            <div id="result_table"></div>
            <div id="viewbox">
                <div id="viewDiv"></div>
                <button type="button" id="btn" onclick="kousin()" title="経路を更新し表示します"><b>経路再表示</b></button>
            </div>
        </main>
        <footer>
            <p>Copyright(c) 2021 山本佳世子研究室 All Rights Reserved.</p>
        </footer>
    </div>
</body>

</html>
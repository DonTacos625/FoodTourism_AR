<?php

require "frame_define.php";

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
            $s_l_categorys[] = $result5["category"]; //
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
            $l_d_categorys[] = $result6["category"]; //
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
            $d_g_categorys[] = $result7["category"]; //
        }
    }

    $spots_id = array_merge($s_l_ids, $l_d_ids, $d_g_ids);
} catch (PDOException $e) {
}
$side_s_l_spots = array_map(NULL, $s_l_ids, $s_l_names, $s_l_times, $s_l_categorys);
$side_l_d_spots = array_map(NULL, $l_d_ids, $l_d_names, $l_d_times, $l_d_categorys);
$side_d_g_spots = array_map(NULL, $d_g_ids, $d_g_names, $d_g_times, $d_g_categorys);
$side_display_plan = [
    ["start", $side_start_station_name],
    ["s_l", $side_s_l_spots],
    ["lunch", $side_lunch_name, $side_lunch_time],
    ["l_d", $side_l_d_spots],
    ["dinner", $side_dinner_name, $side_dinner_time],
    ["d_g", $side_d_g_spots],
    ["goal", $side_goal_station_name]
];
/*
$ar_plan = [ ["type", lat, lng, [id, name, time]],
             ["start", lat, lng, [id, name]], 
             ["lunch", lat, lng, [id, name, time, genre, genre_sub, open_time, close_time, lunch_budget, dinner_budget]],
             ["l_d_1", lat, lng, [id, name, time, category]]
           ]
*/
//総滞在時間
$total_minute = $side_lunch_time + $side_dinner_time + array_sum($s_l_times) + array_sum($l_d_times) + array_sum($d_g_times);
//var_dump($total_minute);

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
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-WJ8NH8EYSR"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'G-WJ8NH8EYSR');
    </script>

    <script src="https://aframe.io/releases/1.2.0/aframe.min.js"></script>
    <script src="https://unpkg.com/aframe-look-at-component@0.8.0/dist/aframe-look-at-component.min.js"></script>
    <script src='https://raw.githack.com/AR-js-org/AR.js/master/three.js/build/ar-threex-location-only.js'></script>
    <script src='https://raw.githack.com/AR-js-org/AR.js/master/aframe/build/aframe-ar.js'></script>

    <!-- <script src="https://unpkg.com/aframe-html-shader@0.2.0/dist/aframe-html-shader.min.js"></script> 
         <script src="http://html2canvas.hertzen.com/dist/html2canvas.min.js"></script> -->

    <script src="script/aframe-html-shader.min.js"></script>
    <script src="script/html2canvas.min.js"></script>
    <link rel="stylesheet" href="css/ar_style.css?<?php echo date('YmdHis'); ?>">

    <meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no" />
    <title>ナビゲーション(AR)</title>
    <style>
        #viewbox {
            position: fixed;
            overflow-x: hidden;
            width: 1vw;
            height: 1vh;
            margin-left: 5px;
        }

        #viewbox h3 {
            border-left: 5px solid #000080;
            margin: 0px;
        }

        #viewbox #viewDiv {
            height: 90%;
            width: 95%;
        }

        .modal-body table th {
            text-align: left;
            white-space: nowrap;
            background: #EEEEEE;
            width: 5vw;
        }

        .modal-body table td {
            background: #EEEEEE;
            padding: 3px;
        }

        @media screen and (min-width:769px) and (max-width:1366px) {}

        @media screen and (max-width:768px) {}
    </style>

    <link rel="stylesheet" href="https://js.arcgis.com/4.25/esri/themes/light/main.css" />
    <script src="https://js.arcgis.com/4.25/"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

    <script>
        /*
        var pointpic = "";
        var current_latitude = 0;
        var current_longitude = 0;
        $first_load = 0;

        function test() {
            var options = {
                timeout: 10000 // 10秒でタイムアウトするように設定する
            };
            navigator.geolocation.getCurrentPosition(test2, errorCallback, options);
        };

        // 取得失敗した場合
        function errorCallback(error) {
            switch (error.code) {
                case 1: //PERMISSION_DENIED
                    alert("位置情報の利用が許可されていません");
                    break;
                case 2: //POSITION_UNAVAILABLE
                    alert("現在位置が取得できませんでした");
                    break;
                case 3: //TIMEOUT
                    alert("申し訳ございませんが、タイムアウトになりました。再読み込みするか、少し間を置いてご利用ください。");
                    break;
                default:
                    alert("その他のエラー(エラーコード:" + error.code + ")");
                    break;
            }
        };

        function test2(position) {
            current_latitude = position.coords.latitude;
            current_longitude = position.coords.longitude;
            //alert(current_longitude);
        }
        test();

        require([
            "esri/Map",
            "esri/views/MapView",
            "esri/layers/WebTileLayer",
            "esri/layers/FeatureLayer",
            "esri/widgets/Track",
            "esri/Graphic",
            "esri/layers/GraphicsLayer",
            "esri/rest/support/Query",
            "esri/rest/route",
            "esri/rest/support/RouteParameters",
            "esri/rest/support/FeatureSet",
            "esri/symbols/PictureMarkerSymbol",
            "esri/symbols/CIMSymbol",
            "esri/geometry/SpatialReference",
            "esri/geometry/Polyline",
            "esri/geometry/Point"
        ], function(
            Map,
            MapView,
            WebTileLayer,
            FeatureLayer,
            Track,
            Graphic,
            GraphicsLayer,
            Query,
            route,
            RouteParameters,
            FeatureSet,
            PictureMarkerSymbol,
            CIMSymbol,
            SpatialReference,
            Polyline,
            Point
        ) {

            // Point the URL to a valid routing service
            const routeUrl = "https://utility.arcgis.com/usrsvcs/servers/4550df58672c4bc6b17607b947177b56/rest/services/World/Route/NAServer/Route_World";

            var spot_id = <?php echo json_encode($navi_spot_id); ?>;
            //観光スポットのIDから表示するスポットを決める
            var feature_sql = "";
            feature_sql += "ID = "
            feature_sql += spot_id;

            //spotLayer
            var foodLayer = new FeatureLayer({
                url: <?php echo json_encode($map_restaurants); ?>,
                id: "foodLayer",
                definitionExpression: feature_sql
            });

            var stationLayer = new FeatureLayer({
                url: <?php echo json_encode($map_stations); ?>,
                id: "stationLayer",
                definitionExpression: feature_sql
            });

            var spotsLayer = new FeatureLayer({
                url: <?php echo json_encode($map_sightseeing_spots); ?>,
                id: "spotsLayer",
                definitionExpression: feature_sql
            });

            //ルート表示のレイヤー
            const routeLayer = new GraphicsLayer();
            //途中地点のレイヤー
            const getLayer = new GraphicsLayer();

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

            var spot_type = <?php echo json_encode($navi_spot_type); ?>;
            if (spot_type == 1) {
                $goalLayer = stationLayer;
            } else if (spot_type == 2) {
                $goalLayer = foodLayer;
            } else if (spot_type == 3) {
                $goalLayer = spotsLayer;
            }
            const map = new Map({
                basemap: "streets",
                layers: [$goalLayer, routeLayer, getLayer]
            });

            //frameの変数
            var center = [current_longitude, current_latitude];
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

            function display_route(plan) {
                //前回の経路を、グラフィックスレイヤーから削除
                routeLayer.removeAll();
                getLayer.removeAll();
                routeParams.stops.features.splice(0);
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
                //alert(routeParams.stops.features.length);
                if (routeParams.stops.features.length >= 2) {
                    route.solve(routeUrl, routeParams).then(showRoute);
                }

            }

            // ルート表示用のレイヤーにデータを追加
            function showRoute(data) {
                const routeResult = data.routeResults[0].route;
                routeResult.symbol = routeArrowSymbol;
                routeLayer.add(routeResult);
                //$route_result_data = routeResult.geometry;
                //総距離
                $totalLength = data.routeResults[0].directions.totalLength;
                doc();
                if ($totalLength >= 1) {
                    alert("目的地は1km以上離れています！");
                }
                if ($totalLength <= 0.1) {
                    alert("目的地周辺に到着しました！");
                }
                if ($first_load == 0) {
                    $middle_points = [];
                    $metersLength = $totalLength.toPrecision(3) * 1000;
                    $distance = 200;
                    while ($metersLength > $distance) {
                        //alert();
                        const point_get = getPointAlongLine(routeResult.geometry, $distance, 0);
                        const point_get_Symbol = {
                            type: "simple-marker", // autocasts as new SimpleMarkerSymbol()
                            style: "square",
                            size: 15,
                            outline: {
                                // autocasts as new SimpleLineSymbol()
                                width: 4
                            }
                        };
                        const get_stop = new Graphic({
                            geometry: point_get,
                            symbol: point_get_Symbol
                        });
                        $middle_points.push([get_stop.geometry.latitude, get_stop.geometry.longitude]);
                        $distance += 200;
                        getLayer.add(get_stop);
                    }
                    var middle_array = $middle_points;
                    make_middle_ar_object(middle_array);
                    $first_load = 1;
                }
            }

            const track = new Track({
                view: view
            });
            track.on("track", ({
                position
            }) => {
                const {
                    longitude,
                    latitude
                } = position.coords;
                const goal_point = <?php echo json_encode($navi_goal_info); ?>;
                //alert(`${longitude.toFixed(4)}, ${latitude.toFixed(4)}`);
                var new_keikaku = [
                    [longitude.toFixed(4), latitude.toFixed(4), "start"],
                    [goal_point[0], goal_point[1], "goal"]
                ]
                display_route(new_keikaku);
            });
            view.ui.add(track, "top-left");
            view.when(() => {
                track.start();
            });

            //二点間の距離を導出
            function distanceBetweenPoints(x1, y1, x2, y2) {
                return Math.sqrt(Math.pow(x2 - x1, 2) + (Math.pow(y2 - y1, 2)));
            }
            //ルート上の等間隔の点を定義
            function getPointAlongLine(polyline, distance, pathIndex) {
                if (!pathIndex)
                    pathIndex = 0;
                if (!distance)
                    distance = 0;
                if ((pathIndex >= 0) && (pathIndex < polyline.paths.length)) {
                    var path = polyline.paths[pathIndex];
                    var x1, x2, x3, y1, y2, y3;
                    var travelledDistance = 0;
                    var pathDistance;
                    var distanceDiff;
                    var angle;
                    if (distance === 0)
                        return polyline.getPoint(pathIndex, 0);
                    else if (distance > 0) {
                        for (var i = 1; i < path.length; i++) {
                            x1 = path[i - 1][0];
                            y1 = path[i - 1][1];
                            x2 = path[i][0];
                            y2 = path[i][1];
                            pathDistance = distanceBetweenPoints(x1, y1, x2, y2);
                            travelledDistance += pathDistance;
                            if (travelledDistance === distance)
                                return polyline.getPoint(pathIndex, i);
                            else if (travelledDistance > distance) {
                                distanceDiff = pathDistance - (travelledDistance - distance);
                                angle = Math.atan2(y2 - y1, x2 - x1);
                                x3 = distanceDiff * Math.cos(angle);
                                y3 = distanceDiff * Math.sin(angle);
                                return new Point(x1 + x3, y1 + y3, polyline.spatialReference);
                            }
                        }
                    }
                }
                return null;
            }

        });
        */


        function make_ar_object(array) {
            $AR_form = document.getElementById("ar_scene");
            for (var i = 0; i < array.length; i++) {
                //const a_id = array[i][0];
                const a_latitude = array[i][1];
                const a_longitude = array[i][0];
                const a_name = array[i][2];

                //entityの作成
                const newEntity = document.createElement("a-entity");
                newEntity.className = 'ar_object';
                newEntity.setAttribute('look-at', "[gps-new-camera]");
                newEntity.setAttribute('gps-new-entity-place', {
                    latitude: a_latitude,
                    longitude: a_longitude
                });
                newEntity.setAttribute('data-text', a_name);
                newEntity.setAttribute('scale', "15 15 15");

                newEntity.onclick = () => {
                    alert($totalLength);
                }

                const newSphere = document.createElement("a-cone");
                newSphere.setAttribute('height', '-2');
                const animation = `
                            property:rotation;
                            dur:10000;
                            from:0 0 0;
                            to:0 360 0;
                            loop:-1
                            easing:linear;`
                newSphere.setAttribute('animation', animation);
                newSphere.setAttribute('src', `./skins/navigation_pin_skin.png`);
                //newSphere.setAttribute('color', 'color="#EF2D5E"');
                newEntity.appendChild(newSphere);

                $AR_form.appendChild(newEntity);
            }
        }

        var plan_array = [<?php echo json_encode($side_display_plan); ?>];
        function make_plan_ar_object(array) {
            $AR_form = document.getElementById("ar_scene");
            //開始駅と終了駅が同じかどうか
            var goal_point = array.slice(-1)[0];
            var mode_change = 0;
            if (start_point[0] == goal_point[0] && start_point[1] == goal_point[1]) {
                mode_change = 1;
            }
            for (var i = 0; i < array.length; i++) {
                const a_type = array[i][0];
                const a_latitude = array[i][1];
                const a_longitude = array[i][2];

                const a_id = array[i][3][0];
                const a_name = array[i][3][1];

                /*
                var a_time;
                if(a_type != "start" && a_type != "goal"){
                    a_time = array[i][3][2];
                }
                */

                //entityの作成
                const newEntity = document.createElement("a-entity");
                newEntity.className = 'ar_object';
                newEntity.setAttribute('look-at', "[gps-new-camera]");
                newEntity.setAttribute('gps-new-entity-place', {
                    latitude: a_latitude,
                    longitude: a_longitude
                });
                newEntity.setAttribute('scale', "5 5 5");

                //球を追加
                const newSphere = document.createElement("a-sphere");
                newSphere.setAttribute('radius', '1');
                /*
                const animation = `
                            property:rotation;
                            dur:10000;
                            from:0 0 0;
                            to:0 360 0;
                            loop:-1
                            easing:linear;`
                newSphere.setAttribute('animation', animation);
                */
                if (a_type == "start") {
                    if (mode_change == 1) {
                        src = `./skins/ar_plan_start_and_goal.png`;
                    } else {
                        src = `./skins/ar_plan_start.png`;
                    }
                } else if (a_type == "lunch") {
                    src = `./skins/ar_plan_lunch.png`;
                } else if (a_type == "dinner") {
                    src = `./skins/ar_plan_dinner.png`;
                } else if (a_type == "goal") {
                    if (mode_change == 1) {
                        src = `./skins/ar_plan_start_and_goal.png`;
                    } else {
                        src = `./skins/ar_plan_goal.png`;
                    }
                } else if (a_type == 11) {
                    src = `./skins/ar_plan_s_l_spot1.png`;
                } else if (a_type == 12) {
                    src = `./skins/ar_plan_s_l_spot2.png`;
                } else if (a_type == 13) {
                    src = `./skins/ar_plan_s_l_spot3.png`;
                } else if (a_type == 21) {
                    src = `./skins/ar_plan_l_d_spot1.png`;
                } else if (a_type == 22) {
                    src = `./skins/ar_plan_l_d_spot2.png`;
                } else if (a_type == 23) {
                    src = `./skins/ar_plan_l_d_spot3.png`;
                } else if (a_type == 31) {
                    src = `./skins/ar_plan_d_g_spot1.png`;
                } else if (a_type == 32) {
                    src = `./skins/ar_plan_d_g_spot2.png`;
                } else if (a_type == 33) {
                    src = `./skins/ar_plan_d_g_spot3.png`;
                } else {
                    src = `./skins/ar_plan_ltblue.png`;
                }
                newSphere.setAttribute('src', src);

                newEntity.appendChild(newSphere);

                $AR_form.appendChild(newEntity);
            }
        }

        function make_ar_distance(array) {
            $AR_form = document.getElementById("ar_scene");
            for (var i = 0; i < array.length; i++) {
                //const a_id = array[i][0];
                const a_latitude = array[i][1];
                const a_longitude = array[i][0];
                const a_name = array[i][2];

                //entityの作成
                const newEntity = document.createElement("a-entity");
                newEntity.className = 'ar_object';
                newEntity.setAttribute('look-at', "[gps-new-camera]");
                newEntity.setAttribute('gps-new-entity-place', {
                    latitude: a_latitude,
                    longitude: a_longitude
                });
                //newEntity.setAttribute('data-text', a_name);
                newEntity.setAttribute('scale', "20 20 20");
                newEntity.setAttribute('position', "0 30 0");

                const newText = document.createElement("a-text");
                newText.id = 'ar_text';
                newText.setAttribute('value', "100 M");
                newEntity.appendChild(newText);

                $AR_form.appendChild(newEntity);
            }
        }

        var area_name = <?php echo json_encode($area_name); ?>;
        var modal_array = [<?php echo json_encode($navi_goal_detail); ?>];
        var modal_type = <?php echo json_encode($navi_spot_type); ?>;
        //モーダルウィンドウを作成する
        function make_modal_table(array, type) {
            $result_modal_form = document.getElementById("result_modal_table");
            $result_modal_form.innerHTML = "";
            $result_modal_form.className = 'tables';

            if (type == 2) {
                for (var i = 0; i < array.length; i++) {
                    const a_id = array[i][0];
                    const a_lattitude = array[i][1];
                    const a_longitude = array[i][2];
                    const a_name = array[i][3];
                    const a_genre = array[i][4][0];
                    const a_genre_sub = array[i][4][1];
                    const a_open_time = array[i][5];
                    const a_close_time = array[i][6];
                    const a_lunch_budget = array[i][7];
                    const a_dinner_budget = array[i][8];

                    //表示するhtmlの作成
                    const newDiv = document.createElement("div");
                    newDiv.id = `modal_box${i+1}`;
                    newDiv.className = 'modal fade';
                    newDiv.setAttribute('tabindex', "-1");
                    newDiv.setAttribute('aria-labelledby', `modal_box_label${i+1}`);
                    newDiv.setAttribute('aria-hidden', "true");
                    newDiv.innerHTML = `
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="modal_box_Label${i+1}">${a_name}</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <img class="modal_img" src="images/${area_name}/restaurants/${a_id}.jpg" onError="this.onerror=null;this.src='images/no_image.jpg';" alt="">
                                <table class="table text-wrap">
                                    <tr>
                                        <th>ジャンル</th>
                                        <td class="modal_change">${a_genre},${a_genre_sub}</td>
                                    </tr>
                                    <tr>
                                        <th>営業時間</th>
                                        <td class="modal_change">${a_open_time}</td>
                                    </tr>
                                    <tr>
                                        <th>定休日</th>
                                        <td class="modal_change">${a_close_time}</td>
                                    </tr>
                                    <tr>
                                        <th>予算</th>
                                        <td>昼：${a_lunch_budget}　　夜：${a_dinner_budget}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                                <a class="btn btn-primary" href="restaurant_detail.php?restaurant_id=${a_id}">詳細ページへ</a>
                            </div>
                        </div>
                    </div>`;
                    $result_modal_form.appendChild(newDiv);
                }
            } else if (type == 3) {
                for (var i = 0; i < array.length; i++) {
                    const a_id = array[i][0];
                    const a_lattitude = array[i][1];
                    const a_longitude = array[i][2];
                    const a_name = array[i][3];
                    const a_category = array[i][4];
                    const a_urls = array[i][5];

                    if (a_urls == null) {
                        $a_page = "<a>なし</a>";
                    } else {
                        $a_page = `<a href="${a_urls}" target=_blank>ホームページにアクセスする</a>`;
                    }
                    //表示するhtmlの作成
                    const newDiv = document.createElement("div");
                    newDiv.id = `modal_box${i+1}`;
                    newDiv.className = 'modal fade';
                    newDiv.setAttribute('tabindex', "-1");
                    newDiv.setAttribute('aria-labelledby', `modal_box_label${i+1}`);
                    newDiv.setAttribute('aria-hidden', "true");
                    newDiv.innerHTML = `
                    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="modal_box_Label${i+1}">${a_name}</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <img class="modal_img" src="images/${area_name}/sightseeing_spots/${a_id}.jpg" onError="this.onerror=null;this.src='images/no_image.jpg';" alt="">
                                <table class="table text-wrap">
                                    <tr>
                                        <th>カテゴリー</th>
                                        <td class="modal_change">${a_category}</td>
                                    </tr>
                                    <tr>
                                        <th>ホームページ</th>
                                        <td>${$a_page}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                                <a class="btn btn-primary" href="sightseeing_spot_detail.php?spot_id=${a_id}">詳細ページへ</a>
                            </div>
                        </div>
                    </div>`;
                    $result_modal_form.appendChild(newDiv);
                }
            }

        }

        var array = [<?php echo json_encode($navi_goal_info); ?>];

        function decimalPart(num, decDigits) {
            var decPart = num - ((num >= 0) ? Math.floor(num) : Math.ceil(num));
            return decPart.toFixed(decDigits);
        }

        function doc() {
            var m = $totalLength.toPrecision(3) * 1000;
            $length = m + " M";
            //alert($length);
            var time = ($totalLength / 4.8);
            var hour = Math.floor((time * 60) / 60);
            var mini = Math.floor((time * 60) % 60);
            $time = `総歩行時間：${hour}時間${mini}分`

            change_distance($length);
        }
        //店のナビゲーションページに飛ぶときに送信するデータ
        function navigation_map() {
            var spot_id = <?php echo json_encode($navi_spot_id); ?>;
            var spot_type = <?php echo json_encode($navi_spot_type); ?>;
            var form = document.createElement('form');
            form.method = 'GET';
            form.action = './navigation_map.php';
            var reqElm = document.createElement('input');
            var reqElm2 = document.createElement('input');
            reqElm.name = 'navi_spot_id';
            reqElm.value = spot_id;
            reqElm2.name = 'navi_spot_type';
            reqElm2.value = spot_type;
            form.appendChild(reqElm);
            form.appendChild(reqElm2);
            document.body.appendChild(form);
            form.submit();
        };
    </script>
</head>

<body>
    <div id="viewbox">
        <div id="viewDiv"></div>
    </div>

    <a-scene id="ar_scene" device-orientation-permission-ui="enabled: false" vr-mode-ui='enabled: false' arjs='sourceType: webcam; videoTexture: true; debugUIEnabled: false' renderer='antialias: true; alpha: true' cursor='rayOrigin: mouse'>
        <a-camera gps-new-camera='gpsMinDistance: 5'></a-camera>
    </a-scene>

    <div id="header_bar" class="justify-content-center">
        目的地まで<h1 class="fw-bold" id="ar_distance">0M</h1>
    </div>

    <div id="bottom_bar">
        <button class="btn btn-primary w-15" onclick="location.reload()" type=button><i class="bi bi-arrow-clockwise"></i><!--再読み込み--></button>
        <button id="result_list_btn" data-bs-toggle="modal" data-bs-target="#modal_box1" type=button>目的地の情報</button>
        <button data-bs-toggle="modal" data-bs-target="#explain_modal" type=button>使用方法</button>
        <button id="change" class="btn btn-primary" type=button onclick="navigation_map()"><i class="bi bi-backspace-fill"></i><!--戻る--></button>
    </div>

    <div class="container-fluid">
        <main class="row">
            <div class="modal fade" id="explain_modal" tabindex="-1" aria-labelledby="explain_modal_Label" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="explain_modal_Label">ナビゲーション機能の説明</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <img class="modal_img" style="width: 100%;" src="images/navigation_ar_explain.png">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="result_modal_table"></div>
        </main>
    </div>
</body>
<script>
    function change_distance(value) {
        //alert(value);
        var ar_text = document.getElementById("ar_text");
        ar_text.setAttribute('value', value);
        var ar_distance = document.getElementById("ar_distance");
        ar_distance.textContent = value;
    }
    make_ar_object(array);
    make_ar_distance(array);
    make_modal_table(modal_array, modal_type);
</script>

</html>
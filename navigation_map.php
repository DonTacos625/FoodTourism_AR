<?php

require "frame_define.php";
require "frame_header.php";
require "frame_menu.php";
require "frame_rightmenu.php";

$navi_spot_id = $_GET["navi_spot_id"];
$navi_spot_type = $_GET["navi_spot_type"];
//$navi_spot_id = 37;
//$navi_spot_type = 2;

$message = "";
//DB接続
try {
    if ($navi_spot_type == 1) {
        $database = $database_stations;
    } else if ($navi_spot_type == 2) {
        $database = $database_restaurants;
        //$database = "hasune_restaurants";
    } else {
        $database = $database_sightseeing_spots;
    }
    $stmt1 = $pdo->prepare("SELECT * FROM $database WHERE id = :id");
    $stmt1->bindParam(":id", $navi_spot_id);
    $stmt1->execute();
    $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);
    $navi_goal_info = [$result1["x"], $result1["y"], "goal"];
} catch (PDOException $e) {
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
            background-color: #eee;
            /* 背景色指定 */
            padding: 10px;
            /* 余白指定 */
            display: flex;
            /* フレックスボックスにする */
            align-items: stretch;
            /* 縦の位置指定 */
        }

        .flex_test-item {
            padding: 10px;
            color: #0a0000;
            /* 文字色 */
            margin: 10px;
            /* 外側の余白 */
            border-radius: 5px;
            /* 角丸指定 */
            width: 15%;
            /* 幅指定 */
        }

        .flex_test-item #imgbox {
            float: left;
            display: flex;
            width: 15vw;
            height: 15vw;
            margin-bottom: 15px;
            justify-content: center;
            align-items: center;
        }

        .flex_test-item #imgbox img {
            width: auto;
            height: auto;
            max-width: 100%;
            max-height: 100%;
        }

        .flex_test-item:nth-child(1) {
            background-color: #fff;
            /* 背景色指定 */
        }

        .flex_test-item:nth-child(2) {
            background-color: #fff;
            /* 背景色指定 */
        }

        .flex_test-item:nth-child(3) {
            background-color: #fff;
            /* 背景色指定 */
        }

        .flex_test-item:nth-child(4) {
            background-color: #fff;
            /* 背景色指定 */
        }
    </style>

    <link rel="stylesheet" href="https://js.arcgis.com/4.25/esri/themes/light/main.css" />
    <script src="https://js.arcgis.com/4.25/"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

    <script>
        var pointpic = "";
        var current_latitude = 0;
        var current_longitude = 0;

        function test() {
            navigator.geolocation.getCurrentPosition(test2);
        }

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
            "esri/widgets/Locate",
            "esri/widgets/Track",
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
            Locate,
            Track,
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
            var detailAction = {
                title: "詳細",
                id: "detail",
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
                actions: [detailAction]
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
                actions: [detailAction]
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
                actions: [detailAction]
            };

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

            var spot_id = <?php echo json_encode($navi_spot_id); ?>;
            //観光スポットのIDから表示するスポットを決める
            var feature_sql = "";
            feature_sql += "ID = "
            feature_sql += spot_id;

            //spotLayer
            var foodLayer = new FeatureLayer({
                url: <?php echo json_encode($map_restaurants); ?>,
                id: "foodLayer",
                popupTemplate: food_template,
                definitionExpression: feature_sql,
                labelingInfo: [labelClass]
            });

            var stationLayer = new FeatureLayer({
                url: <?php echo json_encode($map_stations); ?>,
                id: "stationLayer",
                popupTemplate: station_template,
                definitionExpression: feature_sql,
                labelingInfo: [labelClass]
            });

            var spotsLayer = new FeatureLayer({
                url: <?php echo json_encode($map_sightseeing_spots); ?>,
                id: "spotsLayer",
                popupTemplate: spots_template,
                definitionExpression: feature_sql,
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
                layers: [$goalLayer, routeLayer]
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

            //ポップアップの処理
            view.popup.on("trigger-action", function(event) {
                if (event.action.id === "detail") {
                    if (spot_type == 2) {
                        restaurant_detail();
                    } else if (spot_type == 3) {
                        spot_detail();
                    }
                }
            });
            //店の詳細ページに飛ぶときに送信するデータ
            function restaurant_detail() {
                var restaurant_id = view.popup.selectedFeature.attributes.id;
                var form = document.createElement('form');
                form.method = 'GET';
                form.action = './restaurant_detail.php';
                var reqElm = document.createElement('input');
                reqElm.name = 'restaurant_id';
                reqElm.value = restaurant_id;
                form.appendChild(reqElm);
                document.body.appendChild(form);
                form.submit();
            };
            //スポットの詳細ページに飛ぶときに送信するデータ
            function spot_detail() {
                var spot_id = view.popup.selectedFeature.attributes.id;
                var form = document.createElement('form');
                form.method = 'GET';
                form.action = './sightseeing_spot_detail.php';
                var reqElm = document.createElement('input');
                reqElm.name = 'spot_id';
                reqElm.value = spot_id;
                form.appendChild(reqElm);
                document.body.appendChild(form);
                form.submit();
            };

            function display_route(plan) {
                //前回の経路を、グラフィックスレイヤーから削除
                routeLayer.removeAll();
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
                                    pointpic = "./markers/current_location_pin.png";
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
            //display_route(keikaku);

            // ルート表示用のレイヤーにデータを追加
            function showRoute(data) {
                const routeResult = data.routeResults[0].route;
                routeResult.symbol = routeArrowSymbol;
                routeLayer.add(routeResult);
                //$route_result_data = routeResult.geometry;
                //総距離
                $totalLength = data.routeResults[0].directions.totalLength;
                doc();
                //alert($totalLength);
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

            /*
            function distanceBetweenPoints(x1, y1, x2, y2) {
                return Math.sqrt(Math.pow(x2 - x1, 2) + (Math.pow(y2 - y1, 2)));
            }

            function getPointAlongLine(polyline, distance, pathIndex) {
                if (!pathIndex)
                    pathIndex = 0;
                if (!distance)
                    distance = 0;
                alert("d");
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
                            x2 = path[0];
                            y2 = path[1];
                            pathDistance = this._distanceBetweenPoints(x1, y1, x2, y2);
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
            getPointAlongLine(resultLayer, 10, 1);
            */

        });

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
        //店のナビゲーションページに飛ぶときに送信するデータ
        function navigation_ar() {
            var spot_id = <?php echo json_encode($navi_spot_id); ?>;
            var spot_type = <?php echo json_encode($navi_spot_type); ?>;
            var form = document.createElement('form');
            form.method = 'GET';
            form.action = './navigation_ar.php';
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
    <div class="container-fluid">
        <main class="row">
            <div>
                <font color="#ff0000"><?php echo htmlspecialchars($message, ENT_QUOTES); ?></font>
            </div>
            <h3>目的地までの経路</h3>
            <a id="view_result" name="view_result" onclick="navigation_ar()">ARで結果を表示</a><br>

            <div class="icon_explain">
                <b>
                    <div id="length_km">総歩行距離：0.00 km</div>
                </b>
                <b>
                    <div id="time_h_m">総歩行時間：0時間0分</div>
                </b><br>
            </div>

            <div class="icon_explain">
                <img class="pin_list1" src="./markers/icon_explain_s_f.png" alt="昼食予定地のアイコン" title="アイコン説明１">
                <img class="pin_list2" src="./markers/icon_explain_spots.png" alt="昼食予定地のアイコン" title="アイコン説明２">
            </div>
            <div id="viewbox">
                <div id="viewDiv"></div>
            </div>
        </main>
        <footer>
            <p>Copyright(c) 2023 山本佳世子研究室 All Rights Reserved.</p>
        </footer>
    </div>
</body>

</html>
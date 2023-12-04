<?php

require "frame_define.php";

$navi_spot_id = $_GET["navi_spot_id"];
$navi_spot_type = $_GET["navi_spot_type"];

$message = "";
//DB接続
try {
    if ($navi_spot_type == 1) {
        $database = $database_stations;
    } else if ($navi_spot_type == 2) {
        //$database = $database_restaurants;
        $database = "hasune_restaurants";
    } else {
        $database = $database_sightseeing_spots;
    }
    $stmt1 = $pdo->prepare("SELECT * FROM $database WHERE id = :id");
    $stmt1->bindParam(":id", $navi_spot_id);
    $stmt1->execute();
    $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);
    $navi_goal_info = [$result1["x"], $result1["y"], "goal"];
    //var_dump($navi_goal_info);
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
    <title>作成した観光計画を見る</title>
    <style>
        h3 {
            border-left: 5px solid #000080;
            margin: 0px;
        }

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

        @media screen and (min-width:769px) and (max-width:1366px) {}

        @media screen and (max-width:768px) {}
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
                if ($totalLength >= 1) {
                    alert("目的地は1km以上離れています！");
                }
                if ($totalLength <= 0.1) {
                    alert("目的地周辺に到着しました！");
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

        var to_id = <?php echo json_encode($navi_spot_id); ?>;
        //area_name = 
        function make_ar_object(array) {
            $AR_form = document.getElementById("ar_scene");
            for (var i = 0; i < array.length; i++) {
                //const a_id = array[i][0];
                const a_latitude = array[i][1];
                const a_longitude = array[i][0];
                const a_name = array[i][2];
                /*
                const a_genre = array[i][4][0];
                const a_genre_sub = array[i][4][1];
                const a_open_time = array[i][5];
                const a_close_time = array[i][6];
                const a_lunch_budget = array[i][7];
                const a_dinner_budget = array[i][8];
                */

                //entityの作成
                const newEntity = document.createElement("a-entity");
                newEntity.className = 'ar_object';
                newEntity.setAttribute('look-at', "[gps-new-camera]");
                newEntity.setAttribute('gps-new-entity-place', {
                    latitude: a_latitude,
                    longitude: a_longitude
                });
                newEntity.setAttribute('data-text', a_name);
                newEntity.setAttribute('scale', "20 20 20");
                newEntity.setAttribute('popovertarget', `modalbox${i+1}`);

                newEntity.onclick = () => {
                    alert($totalLength);
                }

                /*
                newEntity.setAttribute('material', 'color: blue');
                newEntity.setAttribute('geometry', 'primitive: box');
                */

                //球を追加
                const newSphere = document.createElement("a-sphere");
                newSphere.setAttribute('radius', '1.25');
                const animation = `
                            property:rotation;
                            dur:10000;
                            from:0 0 0;
                            to:0 360 0;
                            loop:-1
                            easing:linear;`
                newSphere.setAttribute('animation', animation);
                //newSphere.setAttribute('src', `images/${area_name}/restaurants/${to_id}.jpg`);
                newSphere.setAttribute('src', `https://cdn.glitch.com/6668328a-fbb0-4645-8a4e-7a21aac2ab17%2Fearth.jpg?v=1606211796642`);
                newSphere.setAttribute('color', 'color="#EF2D5E"');
                newEntity.appendChild(newSphere);

                /*
                //planeの作成
                const newPlane = document.createElement("a-plane");
                newPlane.id = `planebox${i+1}`;
                newPlane.setAttribute('look-at', "[gps-new-camera]");
                if (i % 2 == 0) {
                    newPlane.setAttribute('position', "0 -5 0");
                } else if (i % 3 == 0) {
                    newPlane.setAttribute('position', "0 5 0");
                } else {
                    newPlane.setAttribute('position', "0 0 0");
                }
                //newPlane.setAttribute('position', "0 0 0");
                newPlane.setAttribute('width', "16");
                newPlane.setAttribute('height', "10");
                const material = `shader:html;target: #infobox${i+1};`
                newPlane.setAttribute('material', material);

                newEntity.appendChild(newPlane);
                */
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

                //newEntity.setAttribute('popovertarget', `modalbox${i+1}`);
                /*
                newEntity.onclick = () => {
                    alert($totalLength);
                }
                */
                //newEntity.setAttribute('material', 'color: blue');
                //newEntity.setAttribute('geometry', 'primitive: box');

                const newText = document.createElement("a-text");
                newText.id = 'ar_text';
                newText.setAttribute('value', "100 M");
                newEntity.appendChild(newText);

                $AR_form.appendChild(newEntity);
            }
        }

        var array = [<?php echo json_encode($navi_goal_info); ?>];
        //alert(array);
        //var array = [[139.6787332, 35.78268538, "goal"]];

        function decimalPart(num, decDigits) {
            var decPart = num - ((num >= 0) ? Math.floor(num) : Math.ceil(num));
            return decPart.toFixed(decDigits);
        }

        function doc() {
            var km = $totalLength.toPrecision(3) * 1000;
            $length = km + " M";
            //alert($length);
            var time = ($totalLength / 4.8);
            var hour = Math.trunc(time);
            var mini = 60 * decimalPart(time, 1);
            $time = "総歩行時間：" + hour + "時間" + mini + "分";
            //alert($time);
            /*
            //frameの関数
            update_frame($length, "length_km");
            update_frame($time, "time_h_m");
            */
            //alert($length);
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
    <div class="container-fluid">
        <main>
            <a-scene id="ar_scene" vr-mode-ui='enabled: false' arjs='sourceType: webcam; videoTexture: true; debugUIEnabled: false' renderer='antialias: true; alpha: true' cursor='rayOrigin: mouse'>
                <a-camera gps-new-camera='gpsMinDistance: 5'></a-camera>
            </a-scene>
            <div id="viewbox">
                <div id="viewDiv"></div>
            </div>
            <div id="bottom_bar">
                <button id="result_list_btn" popovertarget="mypopover" type=button>ボタン</button>
                <button id="searchform_btn" type=button onclick="change_distance()">検索フォームを開く</button>
                <select id="change_display_btn" size="1" onchange="change_display(value)">
                    <option value="default"> 通常表示 </option>
                    <option value="small"> 店名だけ表示 </option>
                    <option value="image"> 写真だけ表示 </option>
                </select>
                <button id="change" type=button onclick="navigation_map()">戻る</button>
            </div>
        </main>
        <footer>
            <p>Copyright(c) 2021 山本佳世子研究室 All Rights Reserved.</p>
        </footer>
    </div>
</body>
<script>
    function change_distance(value) {
        //alert(value);
        var ar_text = document.getElementById("ar_text");
        ar_text.setAttribute('value', value);
    }
    make_ar_object(array);
    make_ar_distance(array);
</script>

</html>
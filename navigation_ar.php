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
        $database = $database_restaurants;
    } else if ($navi_spot_type == 3) {
        $database = $database_sightseeing_spots;
    }
    $stmt1 = $pdo->prepare("SELECT * FROM $database WHERE id = :id");
    $stmt1->bindParam(":id", $navi_spot_id);
    $stmt1->execute();
    $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);
    $navi_goal_info = [$result1["x"], $result1["y"], "goal"];

    if ($navi_spot_type == 1) {
        $navi_goal_detail = [$result1["id"], $result1["y"], $result1["x"]];
    } else if ($navi_spot_type == 2) {
        $navi_goal_detail = [$result1["id"], $result1["y"], $result1["x"], $result1["name"], [$result1["genre"], $result1["genre_sub"]], $result1["open_time"], $result1["close_time"], $result1["lunch_budget"], $result1["dinner_budget"]];
    } else if ($navi_spot_type == 3) {
        $navi_goal_detail = [$result1["id"], $result1["y"], $result1["x"], $result1["name"], $result1["category"], $result1["urls"]];
    }

    //var_dump($navi_goal_info);
} catch (PDOException $e) {
}

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
            "esri/widgets/Locate",
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
            Locate,
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
                newEntity.setAttribute('popovertarget', `modalbox${i+1}`);

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

        function make_middle_ar_object(array) {
            $AR_form = document.getElementById("ar_scene");
            for (var i = 0; i < array.length; i++) {
                const a_latitude = array[i][0];
                const a_longitude = array[i][1];

                //entityの作成
                const newEntity = document.createElement("a-entity");
                newEntity.className = 'ar_object';
                newEntity.setAttribute('look-at', "[gps-new-camera]");
                newEntity.setAttribute('gps-new-entity-place', {
                    latitude: a_latitude,
                    longitude: a_longitude
                });
                newEntity.setAttribute('scale', "5 5 5");

                newEntity.onclick = () => {
                    alert($totalLength);
                }

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
                newSphere.setAttribute('src', `./skins/ar_middle${i+1}.png`);
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
                                <img class="modal_img" src="images/${area_name}/restaurants/${a_id}.jpg" alt="">
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
                                <img class="modal_img" src="images/${area_name}/sightseeing_spots/${a_id}.jpg" alt="">
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
        目的地まで<h1 id="ar_distance">0M</h1>
    </div>
    <div id="bottom_bar">
        <button class="btn btn-primary w-15" onclick="location.reload()" type=button>再読み込み</button>
        <button id="result_list_btn" data-bs-toggle="modal" data-bs-target="#modal_box1" type=button>目的地の情報</button>
        <button data-bs-toggle="modal" data-bs-target="#explain_modal" type=button>使用方法</button>
        <button id="change" type=button onclick="navigation_map()">戻る</button>
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
                            あああ
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
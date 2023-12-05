<?php

require "frame_define.php";
require "frame_header.php";
require "frame_menu.php";
require "frame_rightmenu.php";

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
        $start_info = [0, 0, "start"];

        $start_keep_name = [0, "開始駅を選択してください"];
    } else {
        $stmt1 = $pdo->prepare("SELECT * FROM $database_stations WHERE id = :id");
        $stmt1->bindParam(":id", $start_station_id);
        $stmt1->execute();
        $result1 = $stmt1->fetch(PDO::FETCH_ASSOC);
        $start_info = [$result1["x"], $result1["y"], "start"];

        $start_keep_name = [$start_station_id, $result1["name"]];
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

        $goal_keep_name = [0, "終了駅を選択してください"];
    } else {
        $stmt4 = $pdo->prepare("SELECT * FROM $database_stations WHERE id = :id");
        $stmt4->bindParam(":id", $goal_station_id);
        $stmt4->execute();
        $result4 = $stmt4->fetch(PDO::FETCH_ASSOC);
        $goal_info = [$result4["x"], $result4["y"], "goal"];

        $goal_keep_name = [$goal_station_id, $result4["name"]];
    }

    //SQL文を実行して、結果を$stmtに代入する。
    $stmt = $pdo->prepare(" SELECT * FROM $database_stations ");
    $stmt->execute();

    $stmt2 = $pdo->prepare(" SELECT * FROM $database_stations ");
    $stmt2->execute();
} catch (PDOException $e) {
    echo "失敗:" . $e->getMessage() . "\n";
    exit();
}

//keikakuは目的地の配列
//keikakuの配列作成
$keikaku[] = $start_info;

$keikaku[] = $lunch_info;

$keikaku[] = $dinner_info;

$keikaku[] = $goal_info;
//var_dump($keikaku);
?>

<html>

<head>
    <meta charset="UTF-8">
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
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <title>開始・終了駅の設定</title>

    <style>

        #editbox {
            position: relative;
            float: left;
            display: flex;
            width: 40vw;
            margin-left: 5px;
        }

        #editbox #start_box {
            float: left;
        }

        #editbox #goal_box {
            float: right;
        }

        .move_box {
            position: relative;
            width: 76vw;
            float: left;
        }

        @media screen and (min-width:769px) and (max-width:1366px) {
            h2 {
                font-size: 20px;
            }
        }

        @media screen and (max-width:768px) {
            h2 {
                font-size: 19px;
            }

            .move_box {
                width: 100%;
            }

            #editbox {
                width: 100%;
            }
        }
    </style>


    <link rel="stylesheet" href="https://js.arcgis.com/4.21/esri/themes/light/main.css" />
    <script src="https://js.arcgis.com/4.21/"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

    <script type="text/javascript">
        var pointpic = "";
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
            "esri/symbols/PictureMarkerSymbol",
            "esri/symbols/CIMSymbol",
            "esri/widgets/LayerList"
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
            PictureMarkerSymbol,
            CIMSymbol,
            LayerList
        ) {
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
                }]
                ,actions: [detailAction_restaurant]
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
                ,actions: [detailAction_station]
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

            //spotLayer
            var foodLayer = new FeatureLayer({
                url: <?php echo json_encode($map_restaurants); ?>,
                id: "foodLayer",
                popupTemplate: food_template,
                definitionExpression: food_feature_sql
            });

            var stationsLayer = new FeatureLayer({
                url: <?php echo json_encode($map_stations); ?>,
                id: "stationsLayer",
                popupTemplate: station_template,
                labelingInfo: [labelClass]
            });

            //選択したスポットの表示レイヤー
            const planLayer = new GraphicsLayer();

            const map = new Map({
                basemap: "streets",
                layers: [foodLayer, stationsLayer, planLayer]
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
            //最初に経路表示する処理
            function start_map(plan, Layer) {
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

            start_map(keikaku, planLayer);

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

            //マップ上にポイントを追加
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

        //駅の初期値
        $start_station_id = "";
        $goal_station_id = "";

        function set_station(id, mode) {
            if (mode == "start") {
                $start_station_id = id;
            } else if (mode == "goal") {
                $goal_station_id = id;
            }
        };

        //セレクトボックスから駅を設定
        function add_spots_select(id, num) {
            if (id != "0") {
                jQuery(function($) {
                    $.ajax({
                        url: "./ajax_view_addspots.php",
                        type: "POST",
                        dataType: "json",
                        data: {
                            post_data_1: id,
                            post_data_2: num
                        },
                        error: function(XMLHttpRequest, textStatus, errorThrown) {
                            alert("ajax通信に失敗しました");
                        },
                        success: function(response) {
                            if (num == 1) {
                                alert("「" + response[0] + "」を開始駅に設定しました");
                            } else if (num == 4) {
                                alert("「" + response[0] + "」を終了駅に設定しました");
                            }
                            //update_frame(response[0], response[1]);
                            location.reload();
                        }
                    });
                });
            }
        };

        function jump(value) {
            window.location.href = "station_detail.php?station_id=" + value;
        }
    </script>

</head>

<body>
    <div class="container-fluid">
        <main class="row">
            <h3 class="px-0">開始・終了駅の設定</h3>
            <div>
                <ol class="stepBar">
                    <li class="visited" onclick="location.href='set_station.php'"><span>1</span><br>開始・終了駅</li>
                    <li onclick="location.href='search_map.php'"><span>2</span><br>飲食店</li>
                    <li onclick="location.href='sightseeing_spots_selection_map.php'"><span>3</span><br>観光スポット</li>
                    <li onclick="location.href='plan_edit.php'"><span>4</span><br>観光計画を保存</li>
                </ol>
            </div>
            <div id="editbox">
                <div id="start_box">
                    開始駅を選択する：<br>
                    <select name="start_station_id" size="1" onchange="jump(value)">
                        <option value=<?php echo $start_keep_name[0]; ?>> <?php echo $start_keep_name[1]; ?> </option>
                        <?php foreach ($stmt as $row1) : ?>
                            <option value=<?php echo $row1["id"]; ?>> <?php echo $row1["name"]; ?> </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div id="goal_box">
                    終了駅を選択する：<br>
                    <select name="goal_station_id" size="1" onchange="jump(value)">
                        <option value=<?php echo $goal_keep_name[0]; ?>> <?php echo $goal_keep_name[1]; ?> </option>
                        <?php foreach ($stmt2 as $row2) : ?>
                            <option value=<?php echo $row2["id"]; ?>> <?php echo $row2["name"]; ?> </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="move_box">
                <a class="prev_page" name="prev_explain" href="explain.php#set_station">使い方に戻る</a>
                <a class="next_page" name="next_search" href="search.php">飲食店の検索・決定へ</a>
            </div><br>
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
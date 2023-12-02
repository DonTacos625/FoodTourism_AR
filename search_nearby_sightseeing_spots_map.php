<?php

require "frame_header.php";
require "frame_menu.php";
require "frame_rightmenu.php";

try {

    //SESSION変数初期値設定
    if (!isset($_SESSION["search_spots_category"])) {
        $_SESSION["search_spots_category"] = "0";
    }
    if (!isset($_SESSION["spots_around_distance"])) {
        $_SESSION["spots_around_distance"] = "500";
    }
    if (!isset($_SESSION["spots_around_count"])) {
        $_SESSION["spots_around_count"] = "5";
    }
    if (!isset($_SESSION["spots_sort_conditions"])) {
        $_SESSION["spots_sort_conditions"] = "distance_nearest";
    }

    //提出されたデータ
    if (isset($_POST["category"])) {
        $categoryName = $_POST["category"];
        $_SESSION["search_spots_category"] = $categoryName;
    } else {
        $categoryName = $_SESSION["search_spots_category"];
    }
    if (isset($_POST["spots_around_distance"])) {
        $spots_around_distance = $_POST["spots_around_distance"];
        $_SESSION["spots_around_distance"] = $spots_around_distance;
    } else {
        $spots_around_distance = $_SESSION["spots_around_distance"];
    }
    if (isset($_POST["spots_around_count"])) {
        $spots_around_count = $_POST["spots_around_count"];
        $_SESSION["spots_around_count"] = $spots_around_count;
    } else {
        $spots_around_count = $_SESSION["spots_around_count"];
    }
    if (isset($_POST["spots_sort_conditions"])) {
        $spots_sort_conditions = $_POST["spots_sort_conditions"];
        $_SESSION["spots_sort_conditions"] = $spots_sort_conditions;
    } else {
        $spots_sort_conditions = $_SESSION["spots_sort_conditions"];
    }

    $keywordCondition = [];
    //posts = [["データベースのカラム名", "検索条件"]]
    $posts = [["category", $categoryName]];

    //値が0じゃないデータを　keywordCondition　に格納
    foreach ($posts as $post) {
        if (!($post[1] == "0")) {
            $column = $post[0];
            if ($post[0] == "capacity") {
                $keywordCondition[] =  " $column >= $post[1] ";
            } else {
                $keyword = $post[1];
                $keywordCondition[] =  " $column LIKE '%" . $keyword . "%' ";
            }
        }
    }
    $keywordCondition[] =  " id >= 0 ";

    // ここで、 
    // [ 'product_name LIKE "%hoge%"', 
    //   'product_name LIKE "%fuga%"', 
    //   'product_name LIKE "%piyo%"' ]
    // という配列ができあがっている。

    // これをANDでつなげて、文字列にする
    $keywordCondition = implode(' AND ', $keywordCondition);
    //$keywordCondition = $keywordCondition . " ORDER BY id DESC LIMIT 3";
    //$keywordCondition = $keywordCondition . " OR (id = $lunch_shop_id OR id = $dinner_shop_id) ";
    //var_dump($keywordCondition);

    //sql文にする
    $sql = 'SELECT * FROM ' . $database_sightseeing_spots .' WHERE ' . $keywordCondition . ' ';
    //var_dump($sql);
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
} catch (PDOException $e) {
    echo "失敗:" . $e->getMessage() . "\n";
    exit();
}

/*
//keikakuは目的地の配列
//keikakuの配列作成
$keikaku[] = $start_info;

$keikaku[] = $lunch_info;

$keikaku[] = $dinner_info;

$keikaku[] = $goal_info;
//var_dump($keikaku);
*/
//検索結果を配列に格納
$count = 0;
foreach ($stmt as $shop_id) {
    $food_shop_id[] = $shop_id["id"];
    $count += 1;
}
//var_dump($food_shop_id);


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
function set_selected($session_name, $value)
{
    if ($value == $_SESSION[$session_name]) {
        //値がセッション変数と等しいとチェックされてる判定として返す
        print "selected=\"selected\"";
    } else {
        print "";
    }
}

//検索条件が初期の場合
$all_foodLayer_Flag = 0;
if ($categoryName == "0") {
    $all_foodLayer_Flag = 1;
}
//var_dump($all_foodLayer_Flag);


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
    <meta name="viewport" content="initial-scale=1,maximum-scale=1,user-scalable=no" />
    <title>飲食店の検索・決定（地図上表示）</title>
    <style>
        h3 {
            border-left: 5px solid #000080;
            margin: 0px;
        }

        #detailbox {
            position: relative;
            float: left;
            margin-left: 0px;
        }

        #detailbox #infobox {
            float: left;
            width: 75vw;
            margin-left: 5px;
        }

        #detailbox #infobox table {
            width: 100%;
            border: solid 3px #ffffff;
        }

        #detailbox #infobox #imgbox {
            float: left;
            display: flex;
            width: 20vw;
            height: 15vw;
            margin-bottom: 15px;
            justify-content: center;
            align-items: center;
        }

        #detailbox #infobox #imgbox img {
            width: auto;
            height: auto;
            max-width: 100%;
            max-height: 100%;
        }

        #detailbox #infobox table th {
            text-align: left;
            white-space: nowrap;
            background: #EEEEEE;
            width: 15vw;
        }

        #detailbox #infobox table td {
            background: #EEEEEE;
            padding: 3px;
        }

        #detailbox #infobox table td ul {
            margin: 0px;
        }

        #detailbox #infobox table td ul li {
            display: inline-block;
        }

        #detailbox #infobox table td pre {
            white-space: pre-wrap;
        }

        @media screen and (min-width:769px) and (max-width:1366px) {
            h3 {
                margin: 0px;
                font-size: 18px;
            }
        }

        @media screen and (max-width:768px) {
            h3 {
                margin: 0px;
                font-size: 17px;
            }

            .search_form {
                font-size: 12px;
            }

            #detailbox {
                width: auto;
                margin: 0px;
                float: none;
            }

            #detailbox #infobox {
                width: 100%;
                float: none;
            }

            #detailbox #infobox table {
                font-size: 13px;
            }

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

            .move_box {
                width: 100%;
            }

            .search_form {
                font-size: 12px;
            }

            .container {
                display: flex;
                flex-direction: column;
                min-height: 180vh;
            }

        }
    </style>

    <link rel="stylesheet" href="https://js.arcgis.com/4.21/esri/themes/light/main.css" />
    <script src="https://js.arcgis.com/4.21/"></script>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

    <script>
        var pointpic = "";

        var current_latitude = 0;
        var current_longitude = 0;

        function test() {
            navigator.geolocation.getCurrentPosition(
                test2,
                // 取得失敗した場合
                function(error) {
                    switch (error.code) {
                        case 1: //PERMISSION_DENIED
                            alert("位置情報の利用が許可されていません");
                            break;
                        case 2: //POSITION_UNAVAILABLE
                            alert("現在位置が取得できませんでした");
                            break;
                        case 3: //TIMEOUT
                            alert("タイムアウトになりました");
                            break;
                        default:
                            alert("その他のエラー(エラーコード:" + error.code + ")");
                            break;
                    }
                }
            );
        }

        function test2(position) {
            current_latitude = position.coords.latitude;
            current_longitude = position.coords.longitude;
            //alert(current_longitude);
        }
        test();
        //alert(current_longitude);

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
            "esri/rest/support/TopFeaturesQuery",
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
            Locate,
            Track,
            Graphic,
            GraphicsLayer,
            Query,
            TopFeaturesQuery,
            RouteParameters,
            FeatureSet,
            PictureMarkerSymbol,
            CIMSymbol,
            LayerList
        ) {

            // Point the URL to a valid routing service
            const routeUrl = "https://utility.arcgis.com/usrsvcs/servers/4550df58672c4bc6b17607b947177b56/rest/services/World/Route/NAServer/Route_World";
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
                }]
                //,actions: [detailAction]
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
            
            const spot_template = {
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
                ,actions: [detailAction]
            };
            

            //飲食店のIDから表示するスポットを決める
            var spots_feature_sql = "";
            spots_feature_sql = <?php echo json_encode($keywordCondition); ?>;


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
            //$map_stations,$map_restaurants,$map_sightseeing_spotsはframe.phpに
            /*
            var foodLayer = new FeatureLayer({
                url: <?php //echo json_encode($map_restaurants); ?>,
                id: "foodLayer",
                popupTemplate: food_template,
                definitionExpression: food_feature_sql,
                labelingInfo: [labelClass]
            });

            var all_foodLayer = new FeatureLayer({
                url: <?php //echo json_encode($map_restaurants); ?>,
                id: "all_foodLayer",
                popupTemplate: food_template,
                labelingInfo: [labelClass]
            });


            var stationLayer = new FeatureLayer({
                url: <?php //echo json_encode($map_stations); ?>,
                id: "stationLayer",
                popupTemplate: station_template,
                definitionExpression: station_feature_sql
            });
            */
            var sightseeing_spotsLayer = new FeatureLayer({
                url: <?php echo json_encode($map_sightseeing_spots); ?>,
                id: "sightseeing_spotsLayer",
                popupTemplate: spot_template,
                definitionExpression: spots_feature_sql
            });

            //選択したスポットの表示レイヤー
            const resultsLayer = new GraphicsLayer();

            const map = new Map({
                basemap: "streets",
                layers: [resultsLayer]
                //layers: [$food]
            });

            var center = [current_longitude, current_latitude];
            const view = new MapView({
                container: "viewDiv", // Reference to the scene div created in step 5
                map: map, // Reference to the map object created before the scene
                center: center,
                zoom: 15,
                popup: {
                    dockEnabled: true,
                    dockOptions: {
                        breakpoint: false
                    }
                }
            });

            function add_point(pic, Layer) {
                const point = {
                    type: "point",
                    x: view.popup.selectedFeature.attributes.X,
                    y: view.popup.selectedFeature.attributes.Y
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
                Layer.removeAll();
                Layer.add(stop);
            }
            //ポップアップの処理
            view.popup.on("trigger-action", function(event) {
                if (event.action.id === "detail") {
                    spot_detail();
                }
            });

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

            //Locate関数
            const locate = new Locate({
                view: view,
                graphic: new Graphic({
                    symbol: {
                        type: "simple-marker",
                        size: "12px",
                        color: "green",
                        outline: {
                            color: "#efefef",
                            width: "1.5px"
                        }
                    }
                }),
                useHeadingEnabled: false
            });
            view.ui.add(locate, "top-left");

            var featureLayer = new FeatureLayer({
                url: "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/gis_hasune_sightseeing_spots/FeatureServer",
                id: "featureLayer",
                popupTemplate: spot_template,
                definitionExpression: spots_feature_sql
            });

            var current_Symbol = new PictureMarkerSymbol({
                url: "./markers/current_location_pin.png",
                width: "30px",
                height: "46.5px"
            });
            const current_template = {
                title: "現在地",
                content: [{
                    type: "text",
                    text: `緯度：${current_latitude}`
                }, {
                    type: "text",
                    text: `経度：${current_longitude}`
                }]
            };

            const distance = <?php echo json_encode($spots_around_distance); ?>;
            const count = <?php echo json_encode($spots_around_count); ?>;
            const sort_conditions = <?php echo json_encode($spots_sort_conditions); ?>;
            nearby_spots = (geom) => {
                test();
                let graphic = new Graphic({
                    geometry: {
                        type: "point",

                        latitude: current_latitude,
                        longitude: current_longitude,
                        spatialReference: view.spatialReference
                    },
                    symbol: current_Symbol,
                    popupTemplate: current_template
                });
                //sort_restaurants(currebt_latidute, current_longitude);
                //クリックした位置から 500m のバッファ内のスポット（避難所）を検索するための query式を作成
                let query = featureLayer.createQuery();
                query.geometry = graphic.geometry;
                query.outFields = ["*"];
                //ソートの処理
                var distance_sort = 0;
                if (sort_conditions == "distance_nearest") {
                    distance_sort = 1;
                } else if (sort_conditions == "distance_farthest") {
                    distance_sort = 2;
                }

                query.distance = distance;
                query.units = "meters";

                var query_count = count;
                var s_count = 0;

                featureLayer.queryFeatures(query).then(function(featureSet) {
                    var result_fs = featureSet.features;
                    //検索結果が0件だったら、何もしない
                    //if (result_fs.length === 0) { return }

                    //前回の検索結果を、グラフィックスレイヤーから削除
                    resultsLayer.removeAll();
                    $sort_array = [];
                    //検索結果に対する設定
                    var features = result_fs.map(function(graphic) {
                        var distance_from_here = Math.abs(current_latitude - graphic.attributes.Y) + Math.abs(current_longitude - graphic.attributes.X);
                        $sort_array.push([Math.round(distance_from_here * 100000), graphic]);
                        return graphic;
                    });
                    //ソート方法の選択
                    if (distance_sort == 1) {
                        var sorted_array = $sort_array.sort(function(a, b) {
                            return (a[0] - b[0]);
                        });
                    } else if (distance_sort == 2) {
                        var sorted_array = $sort_array.sort(function(a, b) {
                            return (b[0] - a[0]);
                        });
                    } else {
                        var sorted_array = $sort_array;
                    }
                    var sorted_features = sorted_array.map(function(graphic) {
                        s_count += 1;
                        if (s_count <= query_count) {
                            //シンボル設定
                            graphic[1].symbol = {
                                type: "simple-marker",
                                style: "diamond",
                                size: 10.5,
                                color: "darkorange"
                            };
                            graphic[1].popupTemplate = spot_template;
                            //alert(graphic[1].attributes.dinner_min);
                            return graphic[1];
                        }
                    });

                    //検索結果と現在地を、グラフィックスレイヤーに登録（マップに表示）
                    resultsLayer.add(graphic);
                    resultsLayer.addMany(sorted_features);
                });
                view.goTo(graphic);
            };

            nearby_spots();

        });

        function input_search_name(word) {
            const update = document.getElementById("search_name");
            update.value = word;
        };
    </script>

</head>

<script type="text/javascript">

    function display_results() {
        nearby_spots();
        //alert("s");
    }

</script>

<body>
    <div class="container-fluid">
        <main class="row">
            <div id="detailbox">
                <h3 id="search_start">飲食店の検索・決定</h3>
                <a id="view_result" name="view_result" href="search_nearby_sightseein_ar.php">ARで結果を表示</a><br>
                <a id="view_result2" name="view_result2" href="search_nearby_restaurants_map.php">飲食店</a><br>
                <div class="search_form">
                    <form action="search_nearby_sightseeing_spots_map.php" method="post">
                        観光スポットの検索範囲：<br>
                        <input type="radio" id="spots_around_distance" name="spots_around_distance" value="300" <?php set_checked("spots_around_distance", "300"); ?>>周囲300m
                        <input type="radio" id="spots_around_distance" name="spots_around_distance" value="400" <?php set_checked("spots_around_distance", "400"); ?>>周囲400m
                        <input type="radio" id="spots_around_distance" name="spots_around_distance" value="500" <?php set_checked("spots_around_distance", "500"); ?>>周囲500m
                        <input type="radio" id="spots_around_distance" name="spots_around_distance" value="600" <?php set_checked("spots_around_distance", "600"); ?>>周囲600m
                        <input type="radio" id="spots_around_distance" name="spots_around_distance" value="700" <?php set_checked("spots_around_distance", "700"); ?>>周囲700m
                        <input type="radio" id="spots_around_distance" name="spots_around_distance" value="800" <?php set_checked("spots_around_distance", "800"); ?>>周囲800m<br>

                        並び替え条件：
                        <select size="1" id="spots_sort_conditions" name="spots_sort_conditions" onchange="">
                            <option value="distance_nearest" <?php set_selected("spots_sort_conditions", "distance_nearest"); ?>> 距離が近い順 </option>
                            <option value="distance_farthest" <?php set_selected("spots_sort_conditions", "distance_farthest"); ?>> 距離が遠い順 </option>
                        </select><br>

                        表示数：
                        <select size="1" id="spots_around_count" name="spots_around_count" onchange="">
                            <option value="1" <?php set_selected("spots_around_count", "1"); ?>> 1 </option>
                            <option value="2" <?php set_selected("spots_around_count", "2"); ?>> 2 </option>
                            <option value="3" <?php set_selected("spots_around_count", "3"); ?>> 3 </option>
                            <option value="4" <?php set_selected("spots_around_count", "4"); ?>> 4 </option>
                            <option value="5" <?php set_selected("spots_around_count", "5"); ?>> 5 </option>
                            <option value="6" <?php set_selected("spots_around_count", "6"); ?>> 6 </option>
                            <option value="7" <?php set_selected("spots_around_count", "7"); ?>> 7 </option>
                            <option value="8" <?php set_selected("spots_around_count", "8"); ?>> 8 </option>
                            <option value="9" <?php set_selected("spots_around_count", "9"); ?>> 9 </option>
                            <option value="10" <?php set_selected("spots_around_count", "10"); ?>> 10 </option>
                        </select><br>

                        観光スポットのカテゴリー：<br>
                        <input type="radio" id="category" name="category" value="0" <?php set_checked("search_spots_category", "0"); ?>>指定なし
                        <input type="radio" id="category" name="category" value="名所・史跡" <?php set_checked("search_spots_category", "名所・史跡"); ?>>名所・史跡
                        <input type="radio" id="category" name="category" value="ショッピング" <?php set_checked("search_spots_category", "ショッピング"); ?>>ショッピング
                        <input type="radio" id="category" name="category" value="芸術・博物館" <?php set_checked("search_spots_category", "芸術・博物館"); ?>>芸術・博物館
                        <input type="radio" id="category" name="category" value="テーマパーク・公園" <?php set_checked("search_spots_category", "テーマパーク・公園"); ?>>テーマパーク・公園
                        <input type="radio" id="category" name="category" value="その他" <?php set_checked("search_spots_category", "その他"); ?>>その他<br>

                        <input type="submit" name="submit" value="検索する">
                    </form>
                </div><br>
                <button type="button" onclick="display_results()">再読み込み</button>
                <?php
                if (!$count) {
                    echo "検索条件に該当する飲食店はありませんでした";
                }
                ?>
                <div id="viewbox">
                    <div id="viewDiv"></div>
                </div>
            </div>
        </main>
        <footer>
            <p>Copyright(c) 2021 山本佳世子研究室 All Rights Reserved.</p>
        </footer>
    </div>
</body>

</html>
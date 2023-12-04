<?php

require "frame_define.php";
require "frame_header.php";
require "frame_menu.php";
//require "frame_rightmenu.php";

try {

    //SESSION変数初期値設定
    if (!isset($_SESSION["restaurants_around_distance"])) {
        $_SESSION["restaurants_around_distance"] = "500";
    }
    if (!isset($_SESSION["restaurants_around_count"])) {
        $_SESSION["restaurants_around_count"] = "5";
    }
    if (!isset($_SESSION["sort_conditions"])) {
        $_SESSION["sort_conditions"] = "distance_nearest";
    }
    if (!isset($_SESSION["wifi"])) {
        $_SESSION["wifi"] = "0";
    }
    if (!isset($_SESSION["private_room"])) {
        $_SESSION["private_room"] = "0";
    }
    if (!isset($_SESSION["credit_card"])) {
        $_SESSION["credit_card"] = "0";
    }
    if (!isset($_SESSION["non_smoking"])) {
        $_SESSION["non_smoking"] = "0";
    }
    if (!isset($_SESSION["lunch"])) {
        $_SESSION["lunch"] = "0";
    }

    if (!isset($_SESSION["capacity"])) {
        $_SESSION["capacity"] = "0";
    }

    if (!isset($_SESSION["lunch_min"])) {
        $_SESSION["lunch_min"] = "0";
    }
    if (!isset($_SESSION["lunch_max"])) {
        $_SESSION["lunch_max"] = "999999";
    }
    if (!isset($_SESSION["dinner_min"])) {
        $_SESSION["dinner_min"] = "0";
    }
    if (!isset($_SESSION["dinner_max"])) {
        $_SESSION["dinner_max"] = "999999";
    }

    if (!isset($_SESSION["search_genre"])) {
        $_SESSION["search_genre"] = "0";
    }
    if (!isset($_SESSION["search_name"])) {
        $_SESSION["search_name"] = "";
    }

    //提出されたデータ
    if (isset($_POST["restaurants_around_distance"])) {
        $restaurants_around_distance = $_POST["restaurants_around_distance"];
        $_SESSION["restaurants_around_distance"] = $restaurants_around_distance;
    } else {
        $restaurants_around_distance = $_SESSION["restaurants_around_distance"];
    }
    if (isset($_POST["restaurants_around_count"])) {
        $restaurants_around_count = $_POST["restaurants_around_count"];
        $_SESSION["restaurants_around_count"] = $restaurants_around_count;
    } else {
        $restaurants_around_count = $_SESSION["restaurants_around_count"];
    }
    if (isset($_POST["sort_conditions"])) {
        $sort_conditions = $_POST["sort_conditions"];
        $_SESSION["sort_conditions"] = $sort_conditions;
    } else {
        $sort_conditions = $_SESSION["sort_conditions"];
    }
    if (isset($_POST["wifi"])) {
        $wifi = $_POST["wifi"];
        $_SESSION["wifi"] = $wifi;
    } else {
        $wifi = $_SESSION["wifi"];
    }
    if (isset($_POST["private_room"])) {
        $private_room = $_POST["private_room"];
        $_SESSION["private_room"] = $private_room;
    } else {
        $private_room = $_SESSION["private_room"];
    }
    if (isset($_POST["credit_card"])) {
        $credit_card = $_POST["credit_card"];
        $_SESSION["credit_card"] = $credit_card;
    } else {
        $credit_card = $_SESSION["credit_card"];
    }
    if (isset($_POST["non_smoking"])) {
        $non_smoking = $_POST["non_smoking"];
        $_SESSION["non_smoking"] = $non_smoking;
    } else {
        $non_smoking = $_SESSION["non_smoking"];
    }
    if (isset($_POST["lunch"])) {
        $lunch = $_POST["lunch"];
        $_SESSION["lunch"] = $lunch;
    } else {
        $lunch = $_SESSION["lunch"];
    }

    if (isset($_POST["capacity"])) {
        $capacity = $_POST["capacity"];
        $_SESSION["capacity"] = $capacity;
        settype($capacity, "int");
    } else {
        $capacity = $_SESSION["capacity"];
        settype($capacity, "int");
    }

    if (isset($_POST["lunch_min"])) {
        $lunch_min = $_POST["lunch_min"];
        $_SESSION["lunch_min"] = $lunch_min;
    } else {
        $lunch_min = $_SESSION["lunch_min"];
    }
    if (isset($_POST["lunch_max"])) {
        $lunch_max = $_POST["lunch_max"];
        $_SESSION["lunch_max"] = $lunch_max;
    } else {
        $lunch_max = $_SESSION["lunch_max"];
    }
    if (isset($_POST["dinner_min"])) {
        $dinner_min = $_POST["dinner_min"];
        $_SESSION["dinner_min"] = $dinner_min;
    } else {
        $dinner_min = $_SESSION["dinner_min"];
    }
    if (isset($_POST["dinner_max"])) {
        $dinner_max = $_POST["dinner_max"];
        $_SESSION["dinner_max"] = $dinner_max;
    } else {
        $dinner_max = $_SESSION["dinner_max"];
    }

    if (isset($_POST["search_genre"])) {
        $search_genre = htmlspecialchars($_POST["search_genre"]);
        $_SESSION["search_genre"] = $search_genre;
    } else {
        $search_genre = $_SESSION["search_genre"];
    }
    if (isset($_POST["search_name"])) {
        $search_name = htmlspecialchars($_POST["search_name"]);
        $_SESSION["search_name"] = $search_name;
    } else {
        $search_name = $_SESSION["search_name"];
    }

    $keywordCondition = [];
    //posts = [["データベースのカラム名", "検索条件"]]
    $posts = [["wifi", $wifi], ["private_room", $private_room], ["credit_card", $credit_card], ["non_smoking", $non_smoking], ["lunch", $lunch], ["capacity", $capacity]];

    $search_word = strtr($search_name, [
        '\\' => '\\\\',
        '%' => '\%',
        '_' => '\_',
    ]);

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
    //予算範囲
    if ($lunch_min != 0) {
        $keywordCondition[] =  " lunch_min >= $lunch_min";
        $keywordCondition[] =  " lunch_min <> -1";
    }
    if ($lunch_max != 999999) {
        $keywordCondition[] =  " lunch_max <= $lunch_max";
        $keywordCondition[] =  " lunch_max <> -1";
    }
    if ($dinner_min != 0) {
        $keywordCondition[] =  " dinner_min >= $dinner_min";
        $keywordCondition[] =  " dinner_min <> -1";
    }
    if ($dinner_max != 999999) {
        $keywordCondition[] =  " dinner_max <= $dinner_max";
        $keywordCondition[] =  " dinner_max <> -1";
    }
    //$keywordCondition[] =  " lunch_min >= $lunch_min AND lunch_max <= $lunch_max ";
    //$keywordCondition[] =  " dinner_min >= $dinner_min AND dinner_max <= $dinner_max ";
    //名前検索かジャンル検索か判定
    if ($search_genre == "0") {
        $column1 = "genre";
        $column2 = "genre_sub";
        $keywordCondition[] = "( $column1 LIKE '%" . $search_word . "%' OR $column2 LIKE '%" . $search_word . "%' )";
    } else {
        $column1 = "name";
        $keywordCondition[] = " $column1 LIKE '%" . $search_word . "%' ";
    }
    $keywordCondition[] =  " show >= 1 ";;

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

    //$database_restaurants = "hasune_restaurants";
    //sql文にする
    $sql = 'SELECT * FROM ' . $database_restaurants . ' WHERE ' . $keywordCondition . ' ';
    //var_dump($sql);
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
} catch (PDOException $e) {
    echo "失敗:" . $e->getMessage() . "\n";
    exit();
}

//検索結果を配列に格納
$count = 0;
foreach ($stmt as $shop_id) {
    $food_shop_id[] = $shop_id["id"];
    $count += 1;
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
if (
    $wifi == "0" && $private_room == "0" && $credit_card == "0" && $non_smoking == "0" && $lunch == "0"
    && ($capacity == "0" || $capacity == "") && $search_word == ""
    && $dinner_min == "0" && $dinner_max == "999999" && $lunch_min == "0" && $lunch_max == "999999"
) {
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

        /*
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
                    alert("タイムアウトになりました");
                    break;
                default:
                    alert("その他のエラー(エラーコード:" + error.code + ")");
                    break;
            }
        };
        */
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
            var navigationAction = {
                title: "ナビゲーション",
                id: "navigation",
                className: "esri-icon-navigation"
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
                actions: [detailAction, navigationAction]
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
            /*
            const sightseeing_spot_template = {
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
            */

            //飲食店のIDから表示するスポットを決める
            var food_feature_sql = "";
            food_feature_sql = <?php echo json_encode($keywordCondition); ?>;


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
            var foodLayer = new FeatureLayer({
                url: <?php echo json_encode($map_restaurants); ?>,
                id: "foodLayer",
                popupTemplate: food_template,
                definitionExpression: food_feature_sql,
                labelingInfo: [labelClass]
            });

            var all_foodLayer = new FeatureLayer({
                url: <?php echo json_encode($map_restaurants); ?>,
                id: "all_foodLayer",
                popupTemplate: food_template,
                labelingInfo: [labelClass]
            });

            /*
            var stationLayer = new FeatureLayer({
                url: <?php //echo json_encode($map_stations); 
                        ?>,
                id: "stationLayer",
                popupTemplate: station_template,
                definitionExpression: station_feature_sql
            });

            var sightseeing_spotsLayer = new FeatureLayer({
                url: <?php //echo json_encode($map_sightseeing_spots); 
                        ?>,
                id: "sightseeing_spotsLayer",
                popupTemplate: sightseeing_spots_template
            });
            */

            //選択したスポットの表示レイヤー
            const resultsLayer = new GraphicsLayer();

            //飲食店全体を表示するか検索結果を表示するか
            var foodLayer_Flag = <?php echo json_encode($all_foodLayer_Flag); ?>;
            if (foodLayer_Flag == 1) {
                $food = all_foodLayer;
            } else {
                $food = foodLayer;
            }

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

            //ポップアップの処理
            view.popup.on("trigger-action", function(event) {
                if (event.action.id === "detail") {
                    restaurant_detail();
                }
                if (event.action.id === "navigation") {
                    restaurant_navigation();
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
            //店のナビゲーションページに飛ぶときに送信するデータ
            function restaurant_navigation() {
                var restaurant_id = view.popup.selectedFeature.attributes.id;
                var form = document.createElement('form');
                form.method = 'GET';
                form.action = './navigation_map.php';
                var reqElm = document.createElement('input');
                var reqElm2 = document.createElement('input');
                reqElm.name = 'navi_spot_id';
                reqElm.value = restaurant_id;
                reqElm2.name = 'navi_spot_type';
                reqElm2.value = 2;
                form.appendChild(reqElm);
                form.appendChild(reqElm2);
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
                url: <?php echo json_encode($map_restaurants); ?>, //
                id: "featureLayer",
                popupTemplate: food_template,
                definitionExpression: food_feature_sql
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
            const distance = <?php echo json_encode($restaurants_around_distance); ?>;
            const count = <?php echo json_encode($restaurants_around_count); ?>;
            const sort_conditions = <?php echo json_encode($sort_conditions); ?>;
            nearby_restaurants = (geom) => {
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

                let query = featureLayer.createQuery();
                query.geometry = graphic.geometry;
                query.outFields = ["*"];
                //ソートの条件確定
                var distance_sort = 0;
                if (sort_conditions == "distance_nearest") {
                    distance_sort = 1;
                } else if (sort_conditions == "distance_farthest") {
                    distance_sort = 2;
                } else if (sort_conditions == "lunch_minimum") {
                    query.orderByFields = ["lunch_min ASC"];
                    query.where = "lunch_min <> -1";
                } else if (sort_conditions == "lunch_maximum") {
                    query.orderByFields = ["lunch_max DESC"];
                    query.where = "lunch_max <> -1";
                } else if (sort_conditions == "dinner_minimum") {
                    query.orderByFields = ["dinner_min ASC"];
                    query.where = "dinner_min <> -1";
                } else if (sort_conditions == "dinner_maximum") {
                    query.orderByFields = ["dinner_max DESC"];
                    query.where = "dinner_max <> -1";
                }

                query.distance = distance;
                query.units = "meters";
                var query_count = count;
                var s_count = 0;

                featureLayer.queryFeatures(query).then(function(featureSet) {
                    var result_fs = featureSet.features;

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
                            graphic[1].popupTemplate = food_template;
                            //alert(graphic[1].attributes.dinner_min);
                            return graphic[1];
                        }
                    });
                    //検索結果が0件だったら、何もしない
                    if (result_fs.length === 0) {
                        alert("検索条件に該当する飲食店はありませんでした");
                    }
                    //検索結果と現在地を、グラフィックスレイヤーに登録（マップに表示）
                    resultsLayer.add(graphic);
                    resultsLayer.addMany(sorted_features);

                });
                view.goTo(graphic);
            };

            nearby_restaurants();

        });

        function input_search_name(word) {
            const update = document.getElementById("search_name");
            update.value = word;
        };
    </script>

</head>

<script type="text/javascript">
    //セレクトボックスから選ばれたワードを検索ワードボックスに入れる　もっといい方法あるかも
    function input_search_name(word) {
        const update = document.getElementById("search_name");
        update.value = word;
    };
    //予算範囲が不適切な場合
    function right_range(word) {
        if (word.match(/lunch/)) {
            const lunch_min = document.getElementById("lunch_min");
            const lunch_max = document.getElementById("lunch_max");
            if (lunch_min.value - lunch_max.value > 0) {
                alert("最小予算が最大予算を超えています！");
            }
        } else if (word.match(/dinner/)) {
            const dinner_min = document.getElementById("dinner_min");
            const dinner_max = document.getElementById("dinner_max");
            if (dinner_min.value - dinner_max.value > 0) {
                alert("最小予算が最大予算を超えています！");
            }
        }
    };

    function display_results() {
        nearby_restaurants();
        //alert("s");
    }
</script>

<body>
    <div class="container-fluid">
        <main class="row">
            <div id="detailbox">
                <h3 id="search_start">飲食店の検索・決定</h3>
                <a id="view_result" name="view_result" href="search_nearby_restaurants_ar.php">ARで結果を表示</a><br>
                <a id="view_result2" name="view_result2" href="search_nearby_sightseeing_spots_map.php">観光スポット</a><br>
                <div class="search_form">
                    <form action="search_nearby_restaurants_map.php" method="post">
                        飲食店の検索範囲：<br>
                        <input type="radio" id="restaurants_around_distance" name="restaurants_around_distance" value="300" <?php set_checked("restaurants_around_distance", "300"); ?>>周囲300m
                        <input type="radio" id="restaurants_around_distance" name="restaurants_around_distance" value="400" <?php set_checked("restaurants_around_distance", "400"); ?>>周囲400m
                        <input type="radio" id="restaurants_around_distance" name="restaurants_around_distance" value="500" <?php set_checked("restaurants_around_distance", "500"); ?>>周囲500m
                        <input type="radio" id="restaurants_around_distance" name="restaurants_around_distance" value="600" <?php set_checked("restaurants_around_distance", "600"); ?>>周囲600m
                        <input type="radio" id="restaurants_around_distance" name="restaurants_around_distance" value="700" <?php set_checked("restaurants_around_distance", "700"); ?>>周囲700m
                        <input type="radio" id="restaurants_around_distance" name="restaurants_around_distance" value="800" <?php set_checked("restaurants_around_distance", "800"); ?>>周囲800m<br>

                        並び替え条件：
                        <select size="1" id="sort_conditions" name="sort_conditions" onchange="">
                            <option value="distance_nearest" <?php set_selected("sort_conditions", "distance_nearest"); ?>> 距離が近い順 </option>
                            <option value="distance_farthest" <?php set_selected("sort_conditions", "distance_farthest"); ?>> 距離が遠い順 </option>
                            <option value="lunch_minimum" <?php set_selected("sort_conditions", "lunch_minimum"); ?>> 昼の予算が低い順 </option>
                            <option value="lunch_maximum" <?php set_selected("sort_conditions", "lunch_maximum"); ?>> 昼の予算が高い順 </option>
                            <option value="dinner_minimum" <?php set_selected("sort_conditions", "dinner_minimum"); ?>> 夜の予算が低い順 </option>
                            <option value="dinner_maximum" <?php set_selected("sort_conditions", "dinner_maximum"); ?>> 夜の予算が高い順 </option>
                        </select><br>

                        表示数：
                        <select size="1" id="restaurants_around_count" name="restaurants_around_count" onchange="">
                            <option value="1" <?php set_selected("restaurants_around_count", "1"); ?>> 1 </option>
                            <option value="2" <?php set_selected("restaurants_around_count", "2"); ?>> 2 </option>
                            <option value="3" <?php set_selected("restaurants_around_count", "3"); ?>> 3 </option>
                            <option value="4" <?php set_selected("restaurants_around_count", "4"); ?>> 4 </option>
                            <option value="5" <?php set_selected("restaurants_around_count", "5"); ?>> 5 </option>
                            <option value="6" <?php set_selected("restaurants_around_count", "6"); ?>> 6 </option>
                            <option value="7" <?php set_selected("restaurants_around_count", "7"); ?>> 7 </option>
                            <option value="8" <?php set_selected("restaurants_around_count", "8"); ?>> 8 </option>
                            <option value="9" <?php set_selected("restaurants_around_count", "9"); ?>> 9 </option>
                            <option value="10" <?php set_selected("restaurants_around_count", "10"); ?>> 10 </option>
                        </select><br>

                        WIFI：
                        <input type="radio" id="wifi" name="wifi" value="0" <?php set_checked("wifi", "0"); ?>>指定なし
                        <input type="radio" id="wifi" name="wifi" value="あり" <?php set_checked("wifi", "あり"); ?>>あり
                        <input type="radio" id="wifi" name="wifi" value="なし" <?php set_checked("wifi", "なし"); ?>>なし<br>

                        個室：
                        <input type="radio" id="private_room" name="private_room" value="0" <?php set_checked("private_room", "0"); ?>>指定なし
                        <input type="radio" id="private_room" name="private_room" value="あり ：" <?php set_checked("private_room", "あり ："); ?>>あり
                        <input type="radio" id="private_room" name="private_room" value="なし ：" <?php set_checked("private_room", "なし ："); ?>>なし<br>

                        カード決済：
                        <input type="radio" id="credit_card" name="credit_card" value="0" <?php set_checked("credit_card", "0"); ?>>指定なし
                        <input type="radio" id="credit_card" name="credit_card" value="利用可" <?php set_checked("credit_card", "利用可"); ?>>利用可
                        <input type="radio" id="credit_card" name="credit_card" value="利用不可" <?php set_checked("credit_card", "利用不可"); ?>>利用不可<br>

                        禁煙席：
                        <input type="radio" id="non_smoking" name="non_smoking" value="0" <?php set_checked("non_smoking", "0"); ?>>指定なし
                        <input type="radio" id="non_smoking" name="non_smoking" value="全面禁煙" <?php set_checked("non_smoking", "全面禁煙"); ?>>全面禁煙
                        <input type="radio" id="non_smoking" name="non_smoking" value="一部禁煙" <?php set_checked("non_smoking", "一部禁煙"); ?>>一部禁煙
                        <input type="radio" id="non_smoking" name="non_smoking" value="禁煙席なし" <?php set_checked("non_smoking", "禁煙席なし"); ?>>禁煙席なし<br>

                        ランチメニュー：
                        <input type="radio" id="lunch" name="lunch" value="0" <?php set_checked("lunch", "0"); ?>>指定なし
                        <input type="radio" id="lunch" name="lunch" value="あり" <?php set_checked("lunch", "あり"); ?>>あり
                        <input type="radio" id="lunch" name="lunch" value="なし" <?php set_checked("lunch", "なし"); ?>>なし<br>

                        総席数：
                        <input type="number" value="<?php echo $capacity; ?>" id="capacity" name="capacity">～<br>

                        昼食の予算：
                        <select size="1" id="lunch_min" name="lunch_min" onchange="right_range(name)">
                            <option value="0" <?php set_selected("lunch_min", "0"); ?>> 指定なし </option>
                            <option value="501" <?php set_selected("lunch_min", "501"); ?>> 501円 </option>
                            <option value="1001" <?php set_selected("lunch_min", "1001"); ?>> 1001円 </option>
                            <option value="1501" <?php set_selected("lunch_min", "1501"); ?>> 1501円 </option>
                            <option value="2001" <?php set_selected("lunch_min", "2001"); ?>> 2001円 </option>
                            <option value="3001" <?php set_selected("lunch_min", "3001"); ?>> 3001円 </option>
                            <option value="4001" <?php set_selected("lunch_min", "4001"); ?>> 4001円 </option>
                            <option value="5001" <?php set_selected("lunch_min", "5001"); ?>> 5001円 </option>
                            <option value="7001" <?php set_selected("lunch_min", "7001"); ?>> 7001円 </option>
                            <option value="10001" <?php set_selected("lunch_min", "10001"); ?>> 10001円 </option>
                            <option value="15001" <?php set_selected("lunch_min", "15001"); ?>> 15001円 </option>
                            <option value="20001" <?php set_selected("lunch_min", "20001"); ?>> 20001円 </option>
                            <option value="30001" <?php set_selected("lunch_min", "30001"); ?>> 30001円 </option>
                        </select>
                        ～
                        <select size="1" id="lunch_max" name="lunch_max" onchange="right_range(name)">
                            <option value="999999" <?php set_selected("lunch_max", "999999"); ?>> 指定なし </option>
                            <option value="501" <?php set_selected("lunch_max", "501"); ?>> 501円 </option>
                            <option value="1001" <?php set_selected("lunch_max", "1001"); ?>> 1001円 </option>
                            <option value="1501" <?php set_selected("lunch_max", "1501"); ?>> 1501円 </option>
                            <option value="2001" <?php set_selected("lunch_max", "2001"); ?>> 2001円 </option>
                            <option value="3001" <?php set_selected("lunch_max", "3001"); ?>> 3001円 </option>
                            <option value="4001" <?php set_selected("lunch_max", "4001"); ?>> 4001円 </option>
                            <option value="5001" <?php set_selected("lunch_max", "5001"); ?>> 5001円 </option>
                            <option value="7001" <?php set_selected("lunch_max", "7001"); ?>> 7001円 </option>
                            <option value="10001" <?php set_selected("lunch_max", "10001"); ?>> 10001円 </option>
                            <option value="15001" <?php set_selected("lunch_max", "15001"); ?>> 15001円 </option>
                            <option value="20001" <?php set_selected("lunch_max", "20001"); ?>> 20001円 </option>
                            <option value="30001" <?php set_selected("lunch_max", "30001"); ?>> 30001円 </option>
                        </select><br>

                        夕食の予算：
                        <select size="1" id="dinner_min" name="dinner_min" onchange="right_range(name)">
                            <option value="0" <?php set_selected("dinner_min", "0"); ?>> 指定なし </option>
                            <option value="501" <?php set_selected("dinner_min", "501"); ?>> 501円 </option>
                            <option value="1001" <?php set_selected("dinner_min", "1001"); ?>> 1001円 </option>
                            <option value="1501" <?php set_selected("dinner_min", "1501"); ?>> 1501円 </option>
                            <option value="2001" <?php set_selected("dinner_min", "2001"); ?>> 2001円 </option>
                            <option value="3001" <?php set_selected("dinner_min", "3001"); ?>> 3001円 </option>
                            <option value="4001" <?php set_selected("dinner_min", "4001"); ?>> 4001円 </option>
                            <option value="5001" <?php set_selected("dinner_min", "5001"); ?>> 5001円 </option>
                            <option value="7001" <?php set_selected("dinner_min", "7001"); ?>> 7001円 </option>
                            <option value="10001" <?php set_selected("dinner_min", "10001"); ?>> 10001円 </option>
                            <option value="15001" <?php set_selected("dinner_min", "15001"); ?>> 15001円 </option>
                            <option value="20001" <?php set_selected("dinner_min", "20001"); ?>> 20001円 </option>
                            <option value="30001" <?php set_selected("dinner_min", "30001"); ?>> 30001円 </option>
                        </select>
                        ～
                        <select size="1" id="dinner_max" name="dinner_max" onchange="right_range(name)">
                            <option value="999999" <?php set_selected("dinner_max", "999999"); ?>> 指定なし </option>
                            <option value="501" <?php set_selected("dinner_max", "501"); ?>> 501円 </option>
                            <option value="1001" <?php set_selected("dinner_max", "1001"); ?>> 1001円 </option>
                            <option value="1501" <?php set_selected("dinner_max", "1501"); ?>> 1501円 </option>
                            <option value="2001" <?php set_selected("dinner_max", "2001"); ?>> 2001円 </option>
                            <option value="3001" <?php set_selected("dinner_max", "3001"); ?>> 3001円 </option>
                            <option value="4001" <?php set_selected("dinner_max", "4001"); ?>> 4001円 </option>
                            <option value="5001" <?php set_selected("dinner_max", "5001"); ?>> 5001円 </option>
                            <option value="7001" <?php set_selected("dinner_max", "7001"); ?>> 7001円 </option>
                            <option value="10001" <?php set_selected("dinner_max", "10001"); ?>> 10001円 </option>
                            <option value="15001" <?php set_selected("dinner_max", "15001"); ?>> 15001円 </option>
                            <option value="20001" <?php set_selected("dinner_max", "20001"); ?>> 20001円 </option>
                            <option value="30001" <?php set_selected("dinner_max", "30001"); ?>> 30001円 </option>
                        </select><br>

                        検索の設定：
                        <input type="radio" id="search_genre" name="search_genre" value="0" <?php set_checked("search_genre", "0"); ?>>ジャンルで検索
                        <input type="radio" id="search_genre" name="search_genre" value="1" <?php set_checked("search_genre", "1"); ?>>店名で検索<br>

                        検索ワード：
                        <input type="text" value="<?php echo $search_word; ?>" id="search_name" name="search_name">
                        <select name="genre_example" size="1" onchange="input_search_name(value)">
                            <option value=""> ワードを入力するか以下から選択してください </option>
                            <option value="中華"> 中華 </option>
                            <option value="和食"> 和食 </option>
                            <option value="洋食"> 洋食 </option>
                            <option value="イタリアン"> イタリアン </option>
                            <option value="フレンチ"> フレンチ </option>
                            <option value="居酒屋"> 居酒屋 </option>
                            <option value="バイキング"> バイキング </option>
                            <option value="カフェ"> カフェ </option>
                        </select>
                        <br>
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
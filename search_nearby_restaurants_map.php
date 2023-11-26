<?php

require "frame.php";

try {

    //SESSION変数初期値設定
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
    $posts = [["wifi", $wifi], ["private_room", $private_room], ["credit_card", $credit_card], ["non_smoking", $non_smoking], ["lunch", $lunch], ["capacity", $capacity] ];

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
    $keywordCondition[] =  " lunch_min >= $lunch_min AND lunch_max <= $lunch_max ";
    $keywordCondition[] =  " dinner_min >= $dinner_min AND dinner_max <= $dinner_max ";
    //名前検索かジャンル検索か判定
    if ($search_genre == "0") {
        $column1 = "genre";
        $column2 = "genre_sub";
        $keywordCondition[] = "( $column1 LIKE '%" . $search_word . "%' OR $column2 LIKE '%" . $search_word . "%' )";
    } else {
        $column1 = "name";
        $keywordCondition[] = " $column1 LIKE '%" . $search_word . "%' ";
    }
    $keywordCondition[] =  " show >= 1 ";
;

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
    $sql = 'SELECT * FROM ' . $database_restaurants .' WHERE ' . $keywordCondition . ' ';
    var_dump($sql);
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
if ($wifi == "0" && $private_room == "0" && $credit_card == "0" && $non_smoking == "0" && $lunch == "0" 
    && ($capacity == "0" || $capacity == "") && $search_word == "" 
    && $dinner_min == "0" && $dinner_max == "999999" && $lunch_min == "0" && $lunch_max == "999999") {
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

        #detailbox #infobox #imgbox{
            float: left;
            display: flex;
            width: 20vw;
            height: 15vw;
            margin-bottom: 15px;
            justify-content: center;
            align-items: center;
        }

        #detailbox #infobox #imgbox img{
            width:auto;
            height:auto;
            max-width:100%;
            max-height:100%;
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
    <!-- <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> -->
    
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
                ,actions: [detailAction]
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
                url: <?php //echo json_encode($map_stations); ?>,
                id: "stationLayer",
                popupTemplate: station_template,
                definitionExpression: station_feature_sql
            });

            var sightseeing_spotsLayer = new FeatureLayer({
                url: <?php //echo json_encode($map_sightseeing_spots); ?>,
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
                layers: [$food, resultsLayer]
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
                    restaurant_detail();
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
                url: "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/gis_hasune_restaurants/FeatureServer",
                id: "featureLayer",
                popupTemplate: food_template,
                definitionExpression: food_feature_sql
            });

            var current_Symbol = new PictureMarkerSymbol({
                url: "./markers/d_g_spot3.png",
                width: "30px",
                height: "46.5px"
            });
            nearby_restaurants = (geom) => {
                let graphic = new Graphic({
                    geometry: {
                        type: "point",
                        /*
                        latitude: evt.mapPoint.latitude,
                        longitude: evt.mapPoint.longitude,
                        */
                        latitude: current_latitude,
                        longitude: current_longitude,
                        spatialReference: view.spatialReference
                    },
                    symbol: current_Symbol
                });

                //クリックした位置から 500m のバッファ内のスポット（避難所）を検索するための query式を作成
                let query = featureLayer.createQuery();
                query.geometry = graphic.geometry;
                query.outFields = ["*"];
                query.orderByFields = ["lunch_min DESC"];
                //query.topCount = 3,
                //query.maxRecordCount = 3;
                query.distance = 500;
                query.units = "meters";

                var query_count = 5;
                var s_count = 0;
                featureLayer.queryFeatures(query).then(function (featureSet) {
                    var result_fs = featureSet.features;
                    //検索結果が0件だったら、何もしない
                    //if (result_fs.length === 0) { return }

                    //前回の検索結果を、グラフィックスレイヤーから削除
                    resultsLayer.removeAll();

                    //検索結果に対する設定
                    var features = result_fs.map(function (graphic) {
                        s_count += 1;
                        if(s_count <= query_count){
                            //シンボル設定
                            graphic.symbol = {
                                type: "simple-marker",
                                style: "diamond",
                                size: 10.5,
                                color: "darkorange"
                            };
                            graphic.popupTemplate = food_template;
                            //alert(s_count);
                            return graphic;
                        }
                    });

                    //検索結果と現在地を、グラフィックスレイヤーに登録（マップに表示）
                    resultsLayer.add(graphic);
                    resultsLayer.addMany(features);

                });
                //alert(current_longitude);
            };

            nearby_restaurants();

        });

        function toframe(data, id) {
            //frameの関数
            update_frame(data, id);
        }

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
        if(word.match(/lunch/)){
            const lunch_min = document.getElementById("lunch_min");
            const lunch_max = document.getElementById("lunch_max");
            if(lunch_min.value - lunch_max.value > 0){
                alert("最小予算が最大予算を超えています！");
            }
        } else if(word.match(/dinner/)){
            const dinner_min = document.getElementById("dinner_min");
            const dinner_max = document.getElementById("dinner_max");
            if(dinner_min.value - dinner_max.value > 0){
                alert("最小予算が最大予算を超えています！");
            }
        }
    };
</script>

<body>
    <div class="container">
        <main>
        <div id="detailbox">
                <h3 id="search_start">飲食店の検索・決定</h3>
                <a id="view_result" name="view_result" href="search_nearby_restaurants_ar.php">ARで結果を表示</a><br>
                <a id="view_result2" name="view_result2" href="search_nearby_sightseeing_spots_map.php">観光スポット</a><br>
                <div class="search_form">
                    <form action="search_nearby_restaurants_map.php" method="post">
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
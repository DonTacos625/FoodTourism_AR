<?php

require "frame_define.php";

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
        $search_genre = $_POST["search_genre"];
        $_SESSION["search_genre"] = $search_genre;
    } else {
        $search_genre = $_SESSION["search_genre"];
    }
    if (isset($_POST["search_name"])) {
        $search_name = $_POST["search_name"];
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
    $keywordCondition[] =  " show >= 1 ";

    // ここで、 
    // [ 'product_name LIKE "%hoge%"', 
    //   'product_name LIKE "%fuga%"', 
    //   'product_name LIKE "%piyo%"' ]
    // という配列ができあがっている。

    // これをANDでつなげて、文字列にする
    $keywordCondition = implode(' AND ', $keywordCondition);
    //$keywordCondition = $keywordCondition . " OR (id = $lunch_shop_id OR id = $dinner_shop_id) ";
    //var_dump($keywordCondition);

    //sql文にする
    $sql = 'SELECT * FROM ' . $database_restaurants . ' WHERE ' . $keywordCondition . ' ';

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
} catch (PDOException $e) {
    echo "失敗:" . $e->getMessage() . "\n";
    exit();
}

//検索結果を配列に格納
$count = 0;
$count = $stmt->rowCount();


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

?>

<html>

<head>
    <meta charset="UTF-8">
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
    <title>周辺飲食店の検索（AR）</title>
    <style>
        .target #ar_tablebox table {
            float: left;
            display: block;
            width: 100%;
            height: auto;
            border: solid 3px #ffffff;
        }

        .target #ar_tablebox table th {
            text-align: left;
            white-space: nowrap;
            background: #EEEEEE;
            width: 5vw;
        }

        .target #ar_tablebox table td {
            background: #EEEEEE;
            padding: 3px;
        }

        .result_modals {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: white;
            border: 1px solid black;
            padding: 20px;
            z-index: 1000;
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

        .search_form {
            position: fixed;
            top: 50%;
            left: 50%;
            width: 70vw;
            transform: translate(-50%, -50%);
            background-color: white;
            border: 1px solid black;
            padding: 20px;
            z-index: 1000;
        }

        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }

        @media screen and (min-width:769px) and (max-width:1366px) {}

        @media screen and (max-width:768px) {
            .modal {
                font-size: 3vw;
            }

            .search_form {
                font-size: 2.5vw;
            }
        }
    </style>

    <link rel="stylesheet" href="https://js.arcgis.com/4.21/esri/themes/light/main.css" />
    <script src="https://js.arcgis.com/4.21/"></script>

    <script>
        var pointpic = "";
        var spot_array = [];

        var current_latitude = 0;
        var current_longitude = 0;

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
            //調布駅
            /*
            current_latitude = 35.65204951;
            current_longitude = 139.5448359;
            /*
            //みなとみらい駅
            current_latitude = 	35.45754618;
            current_longitude = 139.6326894;
            */
        }
        test();

        require([
            "esri/Map",
            "esri/views/MapView",
            "esri/layers/WebTileLayer",
            "esri/layers/FeatureLayer",
            "esri/widgets/Locate",
            "esri/Graphic",
            "esri/layers/GraphicsLayer",
            "esri/rest/support/Query",
            "esri/rest/support/FeatureSet",
            "esri/symbols/PictureMarkerSymbol",
            "esri/symbols/CIMSymbol"
        ], function(
            Map,
            MapView,
            WebTileLayer,
            FeatureLayer,
            Locate,
            Graphic,
            GraphicsLayer,
            Query,
            FeatureSet,
            PictureMarkerSymbol,
            CIMSymbol
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
                }],
                actions: [detailAction]
            };

            //飲食店のIDから表示するスポットを決める
            var food_feature_sql = "";
            food_feature_sql = <?php echo json_encode($keywordCondition); ?>;

            //選択したスポットの表示レイヤー
            const resultsLayer = new GraphicsLayer();

            const map = new Map({
                basemap: "streets",
                layers: [resultsLayer]
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
            //view.ui.add(locate, "top-left");

            var featureLayer = new FeatureLayer({
                url: <?php echo json_encode($map_restaurants); ?>, //
                id: "featureLayer",
                popupTemplate: food_template,
                definitionExpression: food_feature_sql
            });

            var current_Symbol = new PictureMarkerSymbol({
                url: "./markers/d_g_spot3.png",
                width: "30px",
                height: "46.5px"
            });
            $sort_array = [];
            const distance = <?php echo json_encode($restaurants_around_distance); ?>;
            const count = <?php echo json_encode($restaurants_around_count); ?>;
            const sort_conditions = <?php echo json_encode($sort_conditions); ?>;
            nearby_restaurants = (geom) => {
                test();
                //alert("s");
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
                //ソートの処理
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
                    //検索結果が0件だったら、何もしない
                    //if (result_fs.length === 0) { return }

                    //前回の検索結果を、グラフィックスレイヤーから削除
                    resultsLayer.removeAll();

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
                            $say = [graphic[1].attributes.id, graphic[1].attributes.Y, graphic[1].attributes.X, graphic[1].attributes.name, [graphic[1].attributes.genre, graphic[1].attributes.genre_sub], graphic[1].attributes.open_time, graphic[1].attributes.close_time, graphic[1].attributes.lunch_budget, graphic[1].attributes.dinner_budget];
                            spot_array.push($say);
                            return graphic[1];
                        }
                    });
                    //検索結果が0件だったら、何もしない
                    if (result_fs.length === 0) {
                        alert("検索条件に該当する飲食店はありませんでした");
                    }
                    //検索結果と現在地を、グラフィックスレイヤーに登録（マップに表示）
                    resultsLayer.add(graphic);
                    resultsLayer.addMany(features);

                    var test_row = spot_array;
                    var table_column = ["ID", "緯度", "経度", "店舗名", "ジャンル", "営業時間", "定休日", "予算"];
                    //make_table(test_row, table_column);
                    make_little_table(test_row, table_column);
                    make_modal_table(test_row, table_column);
                    //make_name_table(test_row);
                    make_image_table(test_row);
                    make_ar_object(test_row);

                    //reload()
                });
            };

            nearby_restaurants();

        });
    </script>

</head>

<script type="text/javascript">
    var area_name = <?php echo json_encode($area_name); ?>;
    var hit_count = <?php echo json_encode($count); ?>;

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

    //テーブルのセルを作成
    function make_tablecell(array, column, s_num, c_num) {
        const newtr = document.createElement("tr");
        const newth = document.createElement("th");
        newth.innerHTML = column;
        const newtd = document.createElement("td");

        var word = "";
        if (!array[s_num][c_num]) {
            word = "不明";
        } else {
            word = array[s_num][c_num];
        }

        if (column == "予算") {
            var word2 = "";
            if (!array[s_num][c_num + 1]) {
                word2 = "不明";
            } else {
                word2 = array[s_num][c_num + 1];
            }
            newtd.innerHTML = `昼：${word} 夜：${word2}`;
        } else if (column == "ジャンル") {
            var word3 = `${array[s_num][c_num][0]}`;
            var word4 = `、${array[s_num][c_num][1]}`;
            newtd.innerHTML = `${word3}${word4}`;
        } else {
            newtd.innerHTML = word;
        }

        newtr.appendChild(newth);
        newtr.appendChild(newtd);
        return newtr;
    }

    //モーダルウィンドウを作成する
    function make_modal_table(array, columns) {
        $result_modal_form = document.getElementById("result_modal_table");
        $result_modal_form.innerHTML = "";
        $result_modal_form.className = 'tables';

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
                        <img class="modal_img" style="max-width :100%; max-height :100%;" src="images/${area_name}/restaurants/${a_id}.jpg" onError="this.onerror=null;this.src='images/no_image.jpg';" alt="">
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
                        <a class="btn btn-primary" href="navigation_map.php?navi_spot_id=${a_id}&navi_spot_type=2">ナビゲーション</a>
                        <a class="btn btn-primary" href="restaurant_detail.php?restaurant_id=${a_id}">詳細ページへ</a>
                    </div>
                </div>
            </div>`;
            $result_modal_form.appendChild(newDiv);
        }
    }

    //検索結果を表示する
    function make_little_table(array, columns) {
        var count = 0;

        $results_form = document.getElementById("result_table");
        $results_form.innerHTML = "";
        $results_form.className = 'tables';

        for (var i = 0; i < array.length; i++) {
            const a_id = array[i][0];
            const a_lattitude = array[i][1];
            const a_longitude = array[i][2];
            const a_name = array[i][3];

            //表示するhtmlの作成
            const newDiv = document.createElement("div");
            newDiv.id = `infobox${i+1}`;
            newDiv.className = 'target';

            const newNameDiv = document.createElement("div");
            newNameDiv.className = 'ar_namebox';
            newNameDiv.id = `namebox${i+1}`;
            const newH2 = document.createElement("h2");
            newH2.textContent = a_name;
            newNameDiv.appendChild(newH2);
            newDiv.appendChild(newNameDiv);
            //テーブルの作成
            const newTableBox = document.createElement("div");
            newTableBox.id = 'ar_tablebox';
            const newTable = document.createElement("table");
            for (var j = 3; j < columns.length; j++) {
                const newtablecell = make_tablecell(array, columns[j], i, j);
                newTable.appendChild(newtablecell);
            }
            newTableBox.appendChild(newTable);
            newDiv.appendChild(newTableBox);
            $results_form.appendChild(newDiv);

            count += 1;
        }
        if (count == 0) {
            alert("検索条件に該当する飲食店はありませんでした");
        }
        var ar_count = document.getElementById("ar_count");
        ar_count.textContent = `検索結果は${hit_count}件中${count}件を表示しています`;
    }

    //検索結果の写真を表示する
    function make_image_table(array) {
        $results_image_form = document.getElementById("result_image_table");
        $results_image_form.innerHTML = "";

        $results_image_form.className = 'tables';
        for (var i = 0; i < array.length; i++) {
            const a_id = array[i][0];
            //表示する画像の作成
            const newImgDiv = document.createElement("div");
            const newImg = document.createElement("img");
            const src = `images/${area_name}/restaurants/${a_id}.jpg`;
            newImg.setAttribute('src', src);
            newImg.setAttribute('onError', "this.onerror=null;this.src='images/no_image.jpg';");
            newImgDiv.appendChild(newImg);

            newImgDiv.className = 'target_image';
            newImgDiv.id = `info_image_box${i+1}`;
            $results_image_form.appendChild(newImgDiv);
        }
    }

    function change_display(value) {
        var array = spot_array;
        if (value == "small") {
            for (var i = 0; i < array.length; i++) {
                const target = document.getElementById(`planebox${i+1}`);
                //const material = `shader:html;target: #namebox${i+1};`
                const material = `src: ./skins/ar_icon${i+1}.png;`
                target.setAttribute('geometry', "primitive: sphere");
                target.setAttribute('scale', "3 3 3");
                target.removeAttribute('material');
                target.setAttribute('material', material);
                target.setAttribute('width', "5");
                target.setAttribute('height', "3");
            }
        } else if (value == "default") {
            for (var i = 0; i < array.length; i++) {
                const target = document.getElementById(`planebox${i+1}`);
                const material = `shader:html;target: #infobox${i+1};`
                target.setAttribute('geometry', "primitive: plane");
                target.removeAttribute('scale');
                target.setAttribute('material', material);
                target.setAttribute('width', "16");
                target.setAttribute('height', "10");
            }
        } else if (value == "image") {
            for (var i = 0; i < array.length; i++) {
                const target = document.getElementById(`planebox${i+1}`);
                const material = `shader:html;target: #info_image_box${i+1};`
                target.setAttribute('geometry', "primitive: plane");
                target.removeAttribute('scale');
                target.setAttribute('material', material);
                target.setAttribute('width', "8");
                target.setAttribute('height', "5");
            }
        } else {

        }
    }

    //ARのオブジェクトを作成する
    function make_ar_object(array) {
        $AR_form = document.getElementById("ar_scene");

        /*
        var ar_objects = document.querySelectorAll(".ar_object");
        ar_objects.forEach(ar_object => {
            ar_object.remove();
        });
        */
        /*
        let d_nested = document.getElementById("nested");
        let $AR_form = d.removeChild(d_nested);
        */
        /*
         while ($AR_form.firstChild) {
             if($AR_form.firstChild.id != "ar_camera"){
                 $AR_form.removeChild($AR_form.firstChild);
             }
         }*/

        //$AR_form.innerHTML = "";
        //const newCamera = document.createElement("a-entity");
        //newCamera.setAttribute('gps-new-camera', "gpsMinDistance: 5");
        //$AR_form.appendChild(newCamera);

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

            //entityの作成
            const newEntity = document.createElement("a-entity");
            newEntity.className = 'ar_object';
            newEntity.id = `ar_object${i+1}`;
            newEntity.setAttribute('look-at', "[gps-new-camera]");
            newEntity.setAttribute('gps-new-entity-place', {
                latitude: a_lattitude,
                longitude: a_longitude
            });
            newEntity.setAttribute('data-text', a_name);
            newEntity.setAttribute('scale', "7 7 7");

            newEntity.setAttribute('data-bs-toggle', "modal");
            newEntity.setAttribute('data-bs-target', `#modal_box${i+1}`);

            //planeの作成
            const newPlane = document.createElement("a-plane");
            newPlane.id = `planebox${i+1}`;
            newPlane.setAttribute('look-at', "[gps-new-camera]");

            newPlane.setAttribute('geometry', "primitive: plane");

            newPlane.setAttribute('position', `0 ${-10 + i*3} 0`);
            newPlane.setAttribute('width', "16");
            newPlane.setAttribute('height', "10");
            const material = `shader:html;target: #infobox${i+1};`
            newPlane.setAttribute('material', material);

            newEntity.appendChild(newPlane);
            $AR_form.appendChild(newEntity);
        }
    }

    //再読み込み
    function display_results() {
        nearby_restaurants();
    }
</script>

<body>

    <div id="result_table"></div>

    <div id="result_name_table"></div>

    <div id="result_image_table"></div>

    <a-scene id="ar_scene" device-orientation-permission-ui="enabled: false" vr-mode-ui='enabled: false' arjs='sourceType: webcam; videoTexture: true; debugUIEnabled: false' renderer='antialias: true; alpha: true' cursor='rayOrigin: mouse'>
        <a-camera id="ar_camera" gps-new-camera='gpsMinDistance: 5'></a-camera>
    </a-scene>
    <div id="header_bar" class="justify-content-center">
        <div id="ar_count" class="fw-bold">検索結果は0件中0件を表示しています</div>
    </div>
    <div id="bottom_bar">
        <!-- <button class="btn btn-primary w-15" onclick="display_results()" type=button>再読み込み</button> -->
        <button class="btn btn-primary" onclick="location.reload()" type=button><i class="bi bi-arrow-clockwise"></i><!--再読み込み--></button>
        <select class="btn btn-primary" size="1" onchange="change_display(value)">
            <option value="default"> 通常表示 </option>
            <option value="small"> オブジェクト表示 </option>
            <option value="image"> 写真だけ表示 </option>
        </select>
        <button class="btn btn-primary" type=button data-bs-toggle="modal" data-bs-target="#search_modal"><i class="bi bi-search"></i><!--検索フォーム--></button>
        <button class="btn btn-primary" type=button onclick="location.href='search_nearby_restaurants_map.php'"><i class="bi bi-backspace-fill"></i><!--戻る--></button>
    </div>

    <div class="container-fluid">
        <main class="row">
            <div class="modal fade" id="search_modal" tabindex="-1" aria-labelledby="search_modal_Label" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="search_modal_Label">検索フォーム</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="search_nearby_restaurants_ar.php" method="post">
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

                                Wi-Fi：
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
                                    <option value="バー"> バー </option>
                                    <option value="カフェ"> カフェ </option>
                                </select>
                                <br>
                                <input type="submit" name="submit" value="検索する">
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            <div id="result_modal_table"></div>
        </main>
        <div id="viewbox">
            <div id="viewDiv"></div>
        </div>
        <footer>
            <p>Copyright(c) 2023 山本佳世子研究室 All Rights Reserved.</p>
        </footer>
    </div>

</body>

</html>
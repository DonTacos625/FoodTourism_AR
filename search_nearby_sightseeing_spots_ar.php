<?php

require "frame_define.php";

try {

    //SESSION変数初期値設定
    if (!isset($_SESSION["search_spots_category"])) {
        $_SESSION["search_spots_category"] = ["名所・史跡", "ショッピング", "芸術・博物館", "テーマパーク・公園", "その他"];
        //$_POST['categorys'] = $_SESSION["search_spots_category"];
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

    //$_POST["categorys"]がセットされないことが初期以外にもあるため別の処理
    if (isset($_POST["categorys"])) {
        $checkboxs = $_POST['categorys'];
        $_SESSION["search_spots_category"] = $checkboxs;
    } else {
        if (isset($_POST["spots_around_distance"])) {
            //categorysのチェックボックスが空の時
            $checkboxs = [];
            $_SESSION["search_spots_category"] = $checkboxs;
        } else {
            //リダイレクト時の処理
            $checkboxs = $_SESSION["search_spots_category"];
        }
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
    //チェックボックスのカテゴリーをOR文に
    if (count($checkboxs)) {
        foreach ($checkboxs as $check) {
            $checkCondition[] =  " category LIKE '%" . $check . "%' ";
        }
        $keywordCondition[] = implode(' OR ', $checkCondition);
    } else {
        $keywordCondition[] =  " id <= -1 ";
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
    $sql = 'SELECT * FROM ' . $database_sightseeing_spots . ' WHERE ' . $keywordCondition . ' ';
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
/*
//検索結果を配列に格納
$count = 0;
foreach ($stmt as $shop_id) {
    $food_shop_id[] = $shop_id["id"];
    $count += 1;
}
//var_dump($food_shop_id);
*/


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
function set_checkboxs($session_name, $value)
{
    if (in_array($value, $_SESSION[$session_name])) {
        //値がセッション変数の配列に入っていればチェックされてる判定として返す
        print "checked=\"checked\"";
    } else {
        print "";
    }
}

function set_checkAll($session_name, $length)
{
    if (count($_SESSION[$session_name]) == $length) {
        //すべてのチェックボックスがチェックされていればチェックされてる判定として返す
        print "checked=\"checked\"";
    } else {
        print "";
    }
}
//検索結果を配列に格納
$count = 0;
$count = $stmt->rowCount();

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
    <title>周辺観光スポットの検索（AR）</title>
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
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

    <script src="script/checkAll.js"></script>

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
                }],
                actions: [detailAction]
            };

            //飲食店のIDから表示するスポットを決める
            var spots_feature_sql = "";
            spots_feature_sql = <?php echo json_encode($keywordCondition); ?>;

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
                url: <?php echo json_encode($map_sightseeing_spots); ?>,
                id: "featureLayer",
                popupTemplate: spot_template,
                definitionExpression: spots_feature_sql
            });

            var current_Symbol = new PictureMarkerSymbol({
                url: "./markers/d_g_spot3.png",
                width: "30px",
                height: "46.5px"
            });
            $sort_array = [];
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
                    symbol: current_Symbol
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
                            $say = [graphic[1].attributes.id, graphic[1].attributes.Y, graphic[1].attributes.X, graphic[1].attributes.name, graphic[1].attributes.category, graphic[1].attributes.urls];
                            spot_array.push($say);
                            return graphic[1];
                        }
                    });
                    //検索結果が0件だったら、何もしない
                    if (result_fs.length === 0) {
                        alert("検索条件に該当する観光スポットはありませんでした");
                    }
                    //検索結果と現在地を、グラフィックスレイヤーに登録（マップに表示）
                    resultsLayer.add(graphic);
                    resultsLayer.addMany(sorted_features);

                    var test_row = spot_array;
                    var table_column = ["ID", "緯度", "経度", "スポット名", "カテゴリー", "ホームページ"];
                    //make_table(test_row, table_column);
                    make_little_table(test_row, table_column);
                    make_modal_table(test_row, table_column);
                    //make_name_table(test_row);
                    make_image_table(test_row);
                    make_ar_object(test_row);
                    //alert("s");
                });
            };

            nearby_spots();

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

    function open_result_list() {
        var overlay = document.querySelector(".overlay");
        overlay.style.display = "block";
        var modal = document.getElementById("result_table");
        modal.style.display = "block";
    }

    //テーブルのセルを作成
    function make_tablecell(array, column, s_num, c_num) {
        const newtr = document.createElement("tr");
        const newth = document.createElement("th");
        newth.innerHTML = column;
        const newtd = document.createElement("td");

        var word = "";
        if (!array[s_num][c_num]) {
            word = "なし";
        } else {
            word = array[s_num][c_num];
        }
        if (column == "ホームページ") {
            newtd.innerHTML = word;
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
                                <td class="modal_change">${$a_page}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">閉じる</button>
                        <a class="btn btn-primary" href="navigation_map.php?navi_spot_id=${a_id}&navi_spot_type=3">ナビゲーション</a>
                        <a class="btn btn-primary" href="sightseeing_spot_detail.php?spot_id=${a_id}">詳細ページへ</a>
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
            const a_category = array[i][4];
            const a_url = array[i][5];

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
            const src = `images/${area_name}/sightseeing_spots/${a_id}.jpg`;
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

        for (var i = 0; i < array.length; i++) {
            const a_id = array[i][0];
            const a_lattitude = array[i][1];
            const a_longitude = array[i][2];
            const a_name = array[i][3];
            const a_category = array[i][4];

            //entityの作成
            const newEntity = document.createElement("a-entity");
            newEntity.className = 'ar_object';
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
</script>

<body>

    <div id="result_table"></div>

    <div id="result_name_table"></div>

    <div id="result_image_table"></div>

    <a-scene id="ar_scene" device-orientation-permission-ui="enabled: false" vr-mode-ui='enabled: false' arjs='sourceType: webcam; videoTexture: true; debugUIEnabled: false' renderer='antialias: true; alpha: true' cursor='rayOrigin: mouse'>
        <a-camera gps-new-camera='gpsMinDistance: 5'></a-camera>
    </a-scene>

    <div id="header_bar" class="justify-content-center">
        <div id="ar_count">検索結果は0件中0件を表示しています</div>
    </div>
    <div id="bottom_bar">
        <button class="btn btn-primary w-15" onclick="location.reload()" type=button><i class="bi bi-arrow-clockwise"></i><!--再読み込み--></button>
        <select class="btn btn-primary w-15" size="1" onchange="change_display(value)">
            <option value="default"> 通常表示 </option>
            <option value="small"> オブジェクト表示 </option>
            <option value="image"> 写真だけ表示 </option>
        </select>
        <button class="btn btn-primary w-15" type=button data-bs-toggle="modal" data-bs-target="#search_modal"><i class="bi bi-search"></i><!--検索フォーム--></button>
        <button class="btn btn-primary w-15" type=button onclick="location.href='search_nearby_sightseeing_spots_map.php'"><i class="bi bi-backspace-fill"></i><!--戻る--></button>
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
                            <form action="search_nearby_sightseeing_spots_ar.php" method="post">
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
                                <div>
                                    <input type="checkbox" id="checkAll" <?php set_checkAll("search_spots_category", 5); ?>>全てチェック
                                    <input type="checkbox" id="checkbox2" name="categorys[]" value="名所・史跡" <?php set_checkboxs("search_spots_category", "名所・史跡"); ?>>名所・史跡
                                    <input type="checkbox" id="checkbox3" name="categorys[]" value="ショッピング" <?php set_checkboxs("search_spots_category", "ショッピング"); ?>>ショッピング
                                    <input type="checkbox" id="checkbox4" name="categorys[]" value="芸術・博物館" <?php set_checkboxs("search_spots_category", "芸術・博物館"); ?>>芸術・博物館
                                    <input type="checkbox" id="checkbox5" name="categorys[]" value="テーマパーク・公園" <?php set_checkboxs("search_spots_category", "テーマパーク・公園"); ?>>テーマパーク・公園
                                    <input type="checkbox" id="checkbox6" name="categorys[]" value="その他" <?php set_checkboxs("search_spots_category", "その他"); ?>>その他
                                </div>

                                <input type="submit" name="submit" value="検索する">
                            </form>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="result_modal_table"></div>
        </main>

        <footer>
            <p>Copyright(c) 2023 山本佳世子研究室 All Rights Reserved.</p>
        </footer>
    </div>

</body>

</html>
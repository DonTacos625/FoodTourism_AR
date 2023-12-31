<?php

//ページを跨いで共有する変数
/*
$_SESSION["user_id"]
$_SESSION["user_name"]
$_SESSION["age"]
$_SESSION["gender"]
$_SESSION["user_weight"]

$_SESSION["start_station_id"]
$_SESSION["goal_station_id"]

$_SESSION["lunch_id"]
$_SESSION["dinner_id"]
$_SESSION["lunch_time"]
$_SESSION["dinner_time"]

$_SESSION["s_l_spots"]
$_SESSION["l_d_spots"]
$_SESSION["d_g_spots"]
array[[spot_id,time],[spot_id,time],[spot_id,time]]

$_SESSION["start_time"]
$_SESSION["goal_time"]
*/

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION["user_name"])) {
    header("Location: logout.php");
    exit;
}

if (isset($_SESSION["start_station_id"])) {
    $plan_start_station_id = $_SESSION["start_station_id"];
} else {
    $plan_start_station_id = -1;
}
if (isset($_SESSION["goal_station_id"])) {
    $plan_goal_station_id = $_SESSION["goal_station_id"];
} else {
    $plan_goal_station_id = -1;
}
if (isset($_SESSION["start_time"])) {
    $plan_start_time = $_SESSION["start_time"];
} else {
    $plan_start_time = "10:00";
}
if (isset($_SESSION["goal_time"])) {
    $plan_goal_time = $_SESSION["goal_time"];
} else {
    $plan_goal_time = "17:00";
}

if (isset($_SESSION["lunch_id"])) {
    $plan_lunch_id = $_SESSION["lunch_id"];
} else {
    $plan_lunch_id = -1;
}
if (isset($_SESSION["dinner_id"])) {
    $plan_dinner_id = $_SESSION["dinner_id"];
} else {
    $plan_dinner_id = -1;
}
if (isset($_SESSION["lunch_time"])) {
    $plan_lunch_time = $_SESSION["lunch_time"];
} else {
    $plan_lunch_time = 30;
}
if (isset($_SESSION["dinner_time"])) {
    $plan_dinner_time = $_SESSION["dinner_time"];
} else {
    $plan_dinner_time = 30;
}

if (isset($_SESSION["s_l_spots"])) {
    $plan_s_l_spots = $_SESSION["s_l_spots"];
} else {
    $plan_s_l_spots = [[-1, 0, "設定されていません"]];
}
if (isset($_SESSION["l_d_spots"])) {
    $plan_l_d_spots = $_SESSION["l_d_spots"];
} else {
    $plan_l_d_spots = [[-1, 0, "設定されていません"]];
}
if (isset($_SESSION["d_g_spots"])) {
    $plan_d_g_spots = $_SESSION["d_g_spots"];
} else {
    $plan_d_g_spots = [[-1, 0, "設定されていません"]];
}

$making_plan = [
    ["start", $plan_start_station_id, $plan_start_time],
    ["s_l", $plan_s_l_spots],
    ["lunch", $plan_lunch_id, $plan_lunch_time],
    ["l_d", $plan_l_d_spots],
    ["dinner", $plan_dinner_id, $plan_dinner_time],
    ["d_g", $plan_d_g_spots],
    ["goal", $plan_goal_station_id, $plan_goal_time]
];
//var_dump($plan_s_l_spots);

//DB接続
require "connect_database.php";

if (isset($_SESSION["area_id"])) {
    $area = $_SESSION["area_id"];
} else {
    $area = 1;
}
//利用するデータベースを選択
if ($area == 1) {
    //$area = 1;
    $area_name = "minatomirai";
    $center = [139.635, 35.453];
    $database_stations = "minatomirai_stations";
    $database_restaurants = "minatomirai_restaurants";
    $database_sightseeing_spots = "minatomirai_sightseeing_spots";
    $map_stations = "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/gis_minatomirai_stations/FeatureServer";
    $map_restaurants = "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/gis_minatomirai_restaurants/FeatureServer";
    $map_sightseeing_spots = "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/gis_minatomirai_sightseeing_spots/FeatureServer";
} else if ($area == 2) {
    //$area = 2;
    $area_name = "hasune";
    $center = [139.6790835, 35.78443256];
    $database_stations = "hasune_stations";
    $database_restaurants = "hasune_restaurants";
    $database_sightseeing_spots = "hasune_sightseeing_spots";
    $map_stations = "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/gis_hasune_stations/FeatureServer";
    $map_restaurants = "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/gis_hasune_restaurants/FeatureServer";
    $map_sightseeing_spots = "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/gis_hasune_sightseeing_spots/FeatureServer";
} else if ($area == 3) {
    //$area = 3;
    $area_name = "chofu";
    $center = [139.5436966, 35.65780459];
    $database_stations = "chofu_stations";
    $database_restaurants = "chofu_restaurants";
    $database_sightseeing_spots = "chofu_sightseeing_spots";
    $map_stations = "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/gis_chofu_stations/FeatureServer";
    $map_restaurants = "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/gis_chofu_restaurants/FeatureServer";
    $map_sightseeing_spots = "https://services7.arcgis.com/rbNS7S9fqH4JaV7Y/arcgis/rest/services/gis_chofu_sightseeing_spots/FeatureServer";
}
$_SESSION["area_name"] = $area_name;
//var_dump($area_name);

try {

    $framestmt = $pdo->prepare("SELECT * FROM userinfo WHERE user_name = :user_name");
    $framestmt->bindParam(":user_name", $_SESSION["user_name"]);
    $framestmt->execute();
    $frameresult = $framestmt->fetch(PDO::FETCH_ASSOC);

    //
    $framestmt1 = $pdo->prepare("SELECT * FROM $database_restaurants WHERE id = :id");
    $framestmt1->bindParam(":id", $_SESSION["lunch_id"]);
    $framestmt1->execute();
    $frameresult1 = $framestmt1->fetch(PDO::FETCH_ASSOC);

    $framestmt2 = $pdo->prepare("SELECT * FROM $database_restaurants WHERE id = :id");
    $framestmt2->bindParam(":id", $_SESSION["dinner_id"]);
    $framestmt2->execute();
    $frameresult2 = $framestmt2->fetch(PDO::FETCH_ASSOC);

    $framestmt3 = $pdo->prepare("SELECT * FROM $database_stations WHERE id = :id");
    $framestmt3->bindParam(":id", $_SESSION["start_station_id"]);
    $framestmt3->execute();
    $frameresult3 = $framestmt3->fetch(PDO::FETCH_ASSOC);

    $framestmt4 = $pdo->prepare("SELECT * FROM $database_stations WHERE id = :id");
    $framestmt4->bindParam(":id", $_SESSION["goal_station_id"]);
    $framestmt4->execute();
    $frameresult4 = $framestmt4->fetch(PDO::FETCH_ASSOC);


    //謎 データベース接続するとセッション変数の配列の値が「Array」に変わってしまう不具合があった
    if (!isset($_SESSION["s_l_sightseeing_spots_id"])) {
        $s_l_spots_name = [["設定されていません", 0]];
    } else {
        foreach ($_SESSION["s_l_sightseeing_spots_id"] as $s_l) {
            $framestmt5 = $pdo->prepare("SELECT * FROM $database_sightseeing_spots WHERE id = :id");
            $framestmt5->bindParam(":id", $s_l);
            $framestmt5->execute();
            $frameresult5 = $framestmt5->fetch(PDO::FETCH_ASSOC);
            //$spot_count +=1;
            $s_l_spots_name[] = [$frameresult5["name"], $s_l];
        }
    }

    if (!isset($_SESSION["l_d_sightseeing_spots_id"])) {
        $l_d_spots_name = [["設定されていません", 0]];
    } else {
        foreach ($_SESSION["l_d_sightseeing_spots_id"] as $l_d) {
            $framestmt6 = $pdo->prepare("SELECT * FROM $database_sightseeing_spots WHERE id = :id");
            $framestmt6->bindParam(":id", $l_d);
            $framestmt6->execute();
            $frameresult6 = $framestmt6->fetch(PDO::FETCH_ASSOC);
            //$spot_count +=1;
            $l_d_spots_name[] = [$frameresult6["name"], $l_d];
        }
    }

    if (!isset($_SESSION["d_g_sightseeing_spots_id"])) {
        $d_g_spots_name = [["設定されていません", 0]];
    } else {
        foreach ($_SESSION["d_g_sightseeing_spots_id"] as $d_g) {
            $framestmt7 = $pdo->prepare("SELECT * FROM $database_sightseeing_spots WHERE id = :id");
            $framestmt7->bindParam(":id", $d_g);
            $framestmt7->execute();
            $frameresult7 = $framestmt7->fetch(PDO::FETCH_ASSOC);
            //$spot_count +=1;
            $d_g_spots_name[] = [$frameresult7["name"], $d_g];
        }
    }
    //
} catch (PDOException $e) {
}

//セッション変数を定義
$_SESSION["user_id"] = $frameresult["id"];


//表示の初期値を設定
if (!isset($_SESSION["lunch_id"])) {
    $lunch_name = "昼食地点を設定してください";
} else {
    $lunch_name = $frameresult1["name"];
}
if (!isset($_SESSION["dinner_id"])) {
    $dinner_name = "夕食地点を設定してください";
} else {
    $dinner_name = $frameresult2["name"];
}

if (!isset($_SESSION["start_station_id"])) {
    $start_station_name = "開始駅を設定してください";
} else {
    $start_station_name = $frameresult3["name"];
}
if (!isset($_SESSION["goal_station_id"])) {
    $goal_station_name = "終了駅を設定してください";
} else {
    $goal_station_name = $frameresult4["name"];
}

?>

<!doctype html>
<html>

<code class="code-multiline">
    <!-- jQuery読み込み -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <!-- Propper.js読み込み -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <!-- BootstrapのJavascript読み込み -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js" integrity="sha384-B4gt1jrGC7Jh4AgTPSdUtOBvfO8shuf57BaghqFfPlYxofvL8/KUEfYiJOMMV+rV" crossorigin="anonymous"></script>
</code>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://getbootstrap.jp/docs/5.3/assets/css/docs.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css">

    <link rel="stylesheet" type="text/css" href="css/copyright.css?<?php echo date('YmdHis'); ?>">
    <link rel="stylesheet" type="text/css" href="css/background.css?<?php echo date('YmdHis'); ?>">
    <link rel="stylesheet" type="text/css" href="css/viewbox.css?<?php echo date('YmdHis'); ?>">
    <link rel="stylesheet" type="text/css" href="css/stepbar.css?<?php echo date('YmdHis'); ?>">


</head>

<body>
    <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script>
        //leftの情報を上書きする
        function update_frame(data, id) {
            const update = document.getElementById(id);
            update.innerHTML = data;
            //console.log(update.innerHTML);
        }

        //観光計画からスポットを削除
        function hidden_spot(name) {
            var name_tag = document.getElementById(name);
            if (name_tag.className != "hidden") {
                name_tag.className = "hidden";
                name_tag.querySelector(".btn").textContent = "戻す";
            } else {
                name_tag.className = "";
                name_tag.querySelector(".btn").textContent = "削除";
            }
        };

        function srs_detail(id, type) {
            var form = document.createElement('form');
            var reqElm = document.createElement('input');
            form.method = 'GET';
            if (type == "station") {
                form.action = './station_detail.php';
                reqElm.name = 'station_id';
            } else if (type == "restaurant") {
                form.action = './restaurant_detail.php';
                reqElm.name = 'restaurant_id';
            } else if (type == "spot") {
                form.action = './sightseeing_spot_detail.php';
                reqElm.name = 'spot_id';
            }
            reqElm.value = id;
            form.appendChild(reqElm);
            document.body.appendChild(form);
            form.submit();
        };

        function change_area(area) {
            if (area != 0) {
                if (window.confirm('現在作成している観光計画をリセットしますがよろしいですか？')) {
                    jQuery(function($) {
                        $.ajax({
                            url: "ajax_change_area.php",
                            type: "POST",
                            dataType: "json",
                            data: {
                                post_data_1: area
                            },
                            error: function(XMLHttpRequest, textStatus, errorThrown) {
                                alert("ajax通信に失敗しました");
                            },
                            success: function(response) {
                                alert(response);
                                window.location.reload();
                            }
                        });
                    });
                } else {

                }
            }
        };

        /*
        function browser_indicater() {
            var userAgent = window.navigator.userAgent.toLowerCase();
            if (userAgent.indexOf('msie') != -1 ||
                userAgent.indexOf('trident') != -1) {
                alert('Internet Explorerをお使いのようですが、こちらの機能はGoogle Chromeでのみご利用できます。申し訳ございません。');
            } else if (userAgent.indexOf('edge') != -1) {
                alert('Edgeをお使いのようですが、こちらの機能はGoogle Chromeでのみご利用できます。申し訳ございません。');
            } else if (userAgent.indexOf('chrome') != -1) {
                //alert('Google Chromeをお使いのようですので、こちらの機能はご利用いただけます');
            } else if (userAgent.indexOf('safari') != -1) {
                //alert('Safariをお使いのようですが、こちらの機能はGoogle Chromeでのみご利用できます。申し訳ございません。');
            } else if (userAgent.indexOf('firefox') != -1) {
                alert('FireFoxをお使いのようですが、こちらの機能はGoogle Chromeでのみご利用できます。申し訳ございません。');
            } else if (userAgent.indexOf('opera') != -1) {
                alert('Operaをお使いのようですが、こちらの機能はGoogle Chromeでのみご利用できます。申し訳ございません。');
            } else {
                alert('ブラウザを特定できませんでした');
            }
        }
        */

        const MY_API_KEY = "AAPKfe5fdd5be2744698a188fcc0c7b7b1d742vtC5TsStg94fpwkldrfNo3SJn2jl_VuCOEEdcBiwR7dKOKxejIP_3EDj9IPSPg";
        //browser_indicater();
    </script>

</body>

</html>
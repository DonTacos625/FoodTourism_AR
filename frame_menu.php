<?php

?>

<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0">

    <style>
        .search_form {
            line-height: 200%;
        }

        @media screen and (min-width:769px) {
            #toggle_menu {
                display: none;
            }
        }

        @media screen and (min-width:769px) and (max-width:1366px) {

            #userdata_box {
                font-size: 1vw;
            }

        }

        @media screen and (max-width:768px) {
            #userdata_box {
                font-size: 70%;
            }

            #userdata_box h4 {
                font-size: 150%;
            }

            h2 {
                margin: 0px;
                font-size: 19px;
            }

        }
    </style>
</head>

<body>
    <!-- <script src="https://code.jquery.com/jquery-3.2.1.min.js"></script> -->
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
    </script>

    <nav class="navbar navbar-expand-lg navbar-light mb-3" style="background-color: #e3f2fd;">
        <div class="container-fluid p-2">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav lead">
                    <li class="nav-item">
                        <a class="nav-link" href="home.php">ホーム</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="explain.php">使い方</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            観光計画作成
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="set_station.php">開始・終了駅の設定</a></li>
                            <li><a class="dropdown-item" href="search_map.php">飲食店の検索・決定</a></li>
                            <li><a class="dropdown-item" href="sightseeing_spots_selection_map.php">観光スポット選択</a></li>
                            <li><a class="dropdown-item" href="plan_edit.php">観光計画を保存</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            現地での観光支援
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="search_nearby_restaurants_map.php">周辺スポットの検索</a></li>
                            <li><a class="dropdown-item" href="user_plans.php?from_current=1">観光をナビゲーション</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            一覧
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="all_spots_view.php">スポット一覧</a></li>
                            <li><a class="dropdown-item" href="users_plans.php">他のユーザが作成した観光計画</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            マイページ
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="user_plans.php?from_current=0">保存した観光計画を見る</a></li>
                            <!-- <li><a class="dropdown-item" href="user_plans.php">観光記録</a></li> -->
                            <li><a class="dropdown-item" href="my_page.php">登録情報変更</a></li>
                            <li><a class="dropdown-item" href="logout.php">ログアウト</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="survey.php">アンケート</a>
                    </li>
                </ul>
            </div>
            <div><button class="btn btn-light" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">現在作成中の観光計画</button></div>
            <div id="userdata_box" class="mr-3">
                <h4>会員情報</h4>
                <b>名前:</b> <?php echo htmlspecialchars($_SESSION["user_name"], ENT_QUOTES); ?><br>
                <b>年代:</b> <?php if (!$frameresult["age"]) { ?>
                    未回答
                    <?php } else {
                                echo htmlspecialchars($frameresult["age"], ENT_QUOTES); ?>代 <?php } ?><br>
                <b>性別:</b> <?php echo htmlspecialchars($frameresult["gender"], ENT_QUOTES); ?><br>
            </div>
            <!--
            <form class="d-flex  position-absolute end-0">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search">
                <button class="btn btn-outline-success" type="submit">Search</button>
            </form>
            <form class="d-flex" method="get" action="http://www.google.co.jp/search">
                <input type="text" name="q" size="31" maxlength="255" value="">
                <input type="submit" name="btng" value="検索">
                <input type="hidden" name="hl" value="ja">
                <input type="hidden" name="sitesearch" value="http://localhost/php_test/foodtourism_ar">
            </form>
            -->
        </div>
    </nav>

</body>

</html>
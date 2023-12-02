<?php

?>

<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0">

    <style>
        h1 {
            margin: 0px;
        }

        .search_form {
            line-height: 200%;
        }

        /*右のボックスが無いとき*/
        #dropmenu {
            list-style-type: none;
            position: relative;
            width: 100vw;
            height: 35px;
            padding: 0;
            background: #0099ff;
            border-bottom: 5px solid #00ffff;
            border-radius: 3px 3px 0 0;
            z-index: 3;
        }

        #dropmenu li {
            position: relative;
            width: 16.665%;
            float: left;
            margin: 0;
            padding: 0;
            text-align: center;
            border-right: 1px solid #99ffff;
            box-sizing: border-box;
        }

        #dropmenu li a {
            display: block;
            margin: 0;
            padding: 13px 0 11px;
            color: #FFFFFF;
            font-size: 17px;
            font-weight: bold;
            line-height: 1;
            text-decoration: none;
        }

        #dropmenu li ul {
            list-style: none;
            position: absolute;
            top: 100%;
            left: 0;
            margin: 0;
            padding: 0;
            border-radius: 0 0 3px 3px;
        }

        #dropmenu li ul li {
            overflow: hidden;
            width: 100%;
            height: 0;
            color: #fff;
            -moz-transition: .2s;
            -webkit-transition: .2s;
            -o-transition: .2s;
            -ms-transition: .2s;
            transition: .2s;
        }

        #dropmenu li ul li a {
            padding: 6px 8px;
            background: #0099FF;
            text-align: left;
            font-size: 15px;
            font-weight: normal;
        }

        #dropmenu li:hover>a {
            background: #0066ff;
        }

        #dropmenu>li:hover>a {
            border-radius: 3px 3px 0 0;
        }

        #dropmenu li:hover ul li {
            overflow: visible;
            height: 30px;
            border-bottom: 3px solid #0066ff;
            border-right: 0px;
        }

        #dropmenu li:hover ul li:last-child a {
            border-radius: 0 0 3px 3px;
        }

        @media screen and (min-width:769px) {
            #toggle_menu {
                display: none;
            }
        }

        @media screen and (min-width:769px) and (max-width:1366px) {
            h1 {
                font-size: 25px;
            }

            #dropmenu {
                width: 77vw;
                height: 30px;
                border-bottom: 4px solid #000080;
            }

            #dropmenu li a {
                padding: 7px 0 9px;
                font-size: 16px;
            }

            #dropmenu li ul li a {
                padding: 4px 6px;
                font-size: 13px;
            }

            #dropmenu li:hover ul li {
                height: 23px;
                border-bottom: 2px solid #000080;
            }
        }

        @media screen and (max-width:768px) {
            h1 {
                font-size: 22px;
            }

            h2 {
                margin: 0px;
                font-size: 19px;
            }

            #dropmenu {
                display: none;
            }

            #toggle_menu {
                padding: 0px;
                margin-bottom: 5px;
                border-bottom: 1px solid #000000;
            }

            #toggle_menu label {
                font-weight: bold;
                border: solid 2px black;
                cursor: pointer;
            }

            #toggle_menu>input {
                display: none;
            }

            #toggle_menu #menu {
                height: 0;
                padding: 0;
                overflow: hidden;
                opacity: 0;
                transition: 0.2s;
            }

            #toggle_menu input:checked~#menu {
                height: auto;
                opacity: 1;
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

    <ul id="dropmenu">
        <li><a href="home.php">ホーム</a></li>

        <li><a href="explain.php">使い方</a></li>

        <li><a>観光計画作成</a>
            <ul>
                <li><a href="set_station.php">開始・終了駅の設定</a></li>
                <li><a href="search.php">飲食店の検索・決定</a></li>
                <li><a id="keiro" name="keiro" href="sightseeing_spots_selection_map.php">観光スポット選択</a></li>
            </ul>
        </li>

        <li><a>観光支援</a>
            <ul>
                <li><a href="search_nearby_restaurants_map.php">周辺スポットの検索</a></li>
                <li><a href="navigation_map.php">ナビゲーション</a></li>
            </ul>
        </li>

        <li><a>一覧</a>
            <ul>
                <li><a href="view.php">スポット一覧</a></li>
                <li><a href="users_plans.php">他のユーザーが作成した観光計画</a></li>
            </ul>
        </li>

        <li><a>マイページ</a>
            <ul>
                <li><a id="see_myroute" name="see_myroute" href="plan_edit.php">作成中の観光計画を見る</a></li>
                <li><a id="see_myroute" name="see_myroute" href="user_plans.php">保存した観光計画を見る</a></li>
                <li><a id="see_myroute" name="see_myroute" href="user_plans.php">観光記録</a></li>
                <li><a href="my_page.php">登録情報変更</a></li>
                <li><a href="logout.php">ログアウト</a></li>
            </ul>
        </li>

    </ul>

    <div id="toggle_menu">
        <div class="navbar navbar-expand-lg bg-light">
            <div class="container-fluid">
                <a class="navbar-brand" href="#">メニューバー</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvas" aria-controls="offcanvas">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvas" aria-labelledby="offcanvasLabel">
                    <div class="offcanvas-header">
                        <h5 class="offcanvas-title">Menu</h5>
                        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        <ul class="navbar-nav">
                            <li class="nav-item"><a href="home.php">ホーム</a></li>
                            <li class="nav-item"><a href="explain.php">使い方</a></li>
                            <li class="nav-item"><a href="view.php">スポット一覧</a></li>

                            <li class="nav-item"><a>観光計画作成</a>
                                <ul>
                                    <li class="nav-item"><a href="set_station.php">開始・終了駅の設定</a></li>
                                    <li class="nav-item"><a href="search.php">飲食店の検索・決定</a></li>
                                    <li class="nav-item"><a id="toggle_keiro" name="toggle_keiro" href="sightseeing_spots_selection_map.php">観光スポット選択</a></li>
                                </ul>
                            </li>

                            <li class="nav-item"><a>観光支援</a>
                                <ul>
                                    <li class="nav-item"><a href="search_nearby_restaurants_map.php">周辺スポットの検索</a></li>
                                    <li class="nav-item"><a href="navigation_map.php">ナビゲーション</a></li>
                                </ul>
                            </li>

                            <li class="nav-item"><a>マイページ</a>
                                <ul>
                                    <li class="nav-item"><a id="toggle_see_myroute" name="toggle_see_myroute" href="see_myroute.php">作成した観光計画を見る</a></li>
                                    <li class="nav-item"><a href="my_page.php">登録情報変更</a></li>
                                    <li class="nav-item"><a href="logout.php">ログアウト</a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <label for="menu_label">≡メニュー</label>
        <input type="checkbox" id="menu_label" />

        <div id="menu">
            <ul>
                <li><a href="home.php">ホーム</a></li>

                <li><a href="explain.php">使い方</a></li>

                <li><a>観光計画作成</a>
                    <ul>
                        <li><a href="set_station.php">開始・終了駅の設定</a></li>
                        <li><a href="search.php">飲食店の検索・決定</a></li>
                        <li><a id="toggle_keiro" name="toggle_keiro" href="sightseeing_spots_selection_map.php">観光スポット選択</a></li>
                    </ul>
                </li>

                <li><a>観光支援</a>
                    <ul>
                        <li><a href="search_nearby_restaurants_map.php">周辺スポットの検索</a></li>
                        <li><a href="navigation_map.php">ナビゲーション</a></li>
                    </ul>
                </li>

                <li><a href="view.php">スポット一覧</a></li>

                <li><a>マイページ</a>
                    <ul>
                        <li><a id="toggle_see_myroute" name="toggle_see_myroute" href="see_myroute.php">作成した観光計画を見る</a>
                        </li>
                        <li><a href="my_page.php">登録情報変更</a></li>
                        <li><a href="logout.php">ログアウト</a></li>
                    </ul>
                </li>

            </ul>
        </div>
    </div>

</body>

</html>
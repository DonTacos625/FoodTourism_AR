<?php


?>

<!doctype html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0,minimum-scale=1.0">

    <style>
        #leftbox {
            position: relative;
            top: -5px;
            float: right;
            width: 20vw;
            border-right: 3px solid #0099FF;
            z-index: 2;
        }

        #leftbox h2 {
            background: #0099FF;
            color: #FFFFFF;
            margin-right: 5px;
            border-left: 5px solid #000080;
        }

        #leftbox p {
            margin-left: 10px;
        }

        #leftbox #sightseeing_plan {
            width: 15vw;
        }

        @media screen and (min-width:769px) {
            #toggle_menu {
                display: none;
            }
        }

        @media screen and (min-width:769px) and (max-width:1366px) {

            #leftbox h2 {
                background: #0099FF;
                color: #FFFFFF;
                margin-right: 4px;
                border-left: 4px solid #000080;
                font-size: 17px;
            }
        }

        @media screen and (max-width:768px) {

            #leftbox {
                display: none;
            }

            #making_planbox {
                font-size: 2vw;
                position: fixed;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background-color: white;
                border: 1px solid black;
                padding: 20px;
                z-index: 1000;
            }

        }

        .sortable ul {
            list-style: none;
            padding: 0;
        }

        .sortable li {
            cursor: pointer;
            border: 1px solid;
        }

        .hidden {
            background: #808080;
        }
    </style>
</head>

<body>

    <button class="btn btn-primary position-absolute end-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight">現在作成中の観光計画</button>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasRightLabel">Offcanvas right</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">

            <h2>現在の観光計画</h2>
            <button onclick="remade_plan()">更新する</button><br>
            <div id="making_plan_box">
                <div class="sortable">
                    開始駅<br>
                    <ul>
                        <li id="plan_start_box" value="<?php echo $making_plan[0][1]; ?>">
                            <img id="pin" width="20" height="20" src="./icons/pop_start.png" alt="開始駅のアイコン" title="開始駅">
                            <?php echo $start_station_name ?><br>
                        </li>
                    </ul>
                </div>
                <div class="sortable">
                    昼食前に訪れる観光スポット<br>
                    <ul id="sort">
                        <?php $count_s_l = 0; ?>
                        <?php foreach ($plan_s_l_spots as $date) { ?>
                            <?php $count_s_l += 1; ?>
                            <li value=<?php echo $date[0] ?> id=<?php echo "plan_s_l_" . $count_s_l . "_box"; ?> draggable="true">
                                <img class="pin_s_l" width="20" height="20" src=<?php echo "./icons/pop_icon_s_l" . $count_s_l . ".png"; ?> alt="昼食前に訪れる観光スポットのアイコン" title="昼食前に訪れる観光スポット">
                                <div class="s_l_name"><?php echo $date[2] ?></div>
                                <input class="s_l_time" type="number" value="<?php echo $date[1]; ?>">分
                                <button type="button" class="btn btn-light btn-outline-dark" value=<?php echo "plan_s_l_" . $count_s_l . "_box"; ?> onclick="hidden_spot(value)">削除</button>
                            </li>
                        <?php } ?>
                    </ul>
                    <input type="hidden" id="list-ids" name="list-ids" />
                </div>
                <div class="sortable">
                    昼食を食べる飲食店<br>
                    <ul>
                        <li id="plan_lunch_box" value="<?php echo $making_plan[2][1]; ?>">
                            <img id="pin" width="20" height="20" src="./icons/pop_lunch.png" alt="昼食予定地のアイコン" title="昼食予定地">
                            <?php echo $lunch_name ?><br>
                            <input class="time" type="number" value="<?php echo $making_plan[2][2]; ?>">分
                            <button type="button" class="btn btn-light btn-outline-dark" value="" onclick="hidden_spot('plan_lunch_box')">削除</button>
                        </li>
                    </ul>
                </div>
                <div class="sortable">
                    昼食後に訪れる観光スポット<br>
                    <ul id="sort2">
                        <?php $count_l_d = 0; ?>
                        <?php foreach ($plan_l_d_spots as $date) { ?>
                            <?php $count_l_d += 1; ?>
                            <li value=<?php echo $date[0] ?> id=<?php echo "plan_l_d_" . $count_l_d . "_box"; ?> draggable="true">
                                <img class="pin_l_d" width="20" height="20" src=<?php echo "./icons/pop_icon_l_d" . $count_l_d . ".png"; ?> alt="昼食後に訪れる観光スポットのアイコン" title="昼食後に訪れる観光スポット">
                                <div class="l_d_name"><?php echo $date[2] ?></div>
                                <input class="l_d_time" type="number" value="<?php echo $date[1]; ?>">分
                                <button type="button" class="btn btn-light btn-outline-dark" value=<?php echo "plan_l_d_" . $count_l_d . "_box"; ?> onclick="hidden_spot(value)">削除</button>
                            </li>
                        <?php } ?>
                    </ul>
                    <input type="hidden" id="list-ids" name="list-ids" />
                </div>
                <div class="sortable">
                    夕食を食べる飲食店<br>
                    <ul>
                        <li id="plan_dinner_box" value="<?php echo $making_plan[4][1]; ?>">
                            <img id="pin" width="20" height="20" src="./icons/pop_dinner.png" alt="夕食予定地のアイコン" title="夕食予定地">
                            <?php echo $dinner_name ?><br>
                            <input class="time" type="number" value="<?php echo $making_plan[4][2]; ?>">分
                            <button type="button" class="btn btn-light btn-outline-dark" value="" onclick="hidden_spot('plan_dinner_box')">削除</button>
                        </li>
                    </ul>
                </div>
                <div class="sortable">
                    夕食後に訪れる観光スポット<br>
                    <ul id="sort3">
                        <?php $count_d_g = 0; ?>
                        <?php foreach ($plan_d_g_spots as $date) { ?>
                            <?php $count_d_g += 1; ?>
                            <li value=<?php echo $date[0] ?> id=<?php echo "plan_d_g_" . $count_d_g . "_box"; ?> draggable="true">
                                <img class="pin_d_g" width="20" height="20" src=<?php echo "./icons/pop_icon_d_g" . $count_d_g . ".png"; ?> alt="夕食後に訪れる観光スポットのアイコン" title="夕食後に訪れる観光スポット">
                                <div class="d_g_name"><?php echo $date[2] ?></div>
                                <input class="d_g_time" type="number" value="<?php echo $date[1]; ?>">分
                                <button type="button" class="btn btn-light btn-outline-dark" value=<?php echo "plan_d_g_" . $count_d_g . "_box"; ?> onclick="hidden_spot(value)">削除</button>
                            </li>
                        <?php } ?>
                    </ul>
                    <input type="hidden" id="list-ids" name="list-ids" />
                </div>
                <div class="sortable">
                    終了駅<br>
                    <ul>
                        <li id="plan_goal_box" value="<?php echo $making_plan[6][1] ?>">
                            <img id="pin" width="20" height="20" src="./icons/pop_goal.png" alt="終了駅のアイコン" title="終了駅">
                            <div class="plan_goal_name"><?php echo $goal_station_name ?></div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div id="toggle_menu">
        <label for="menu_label2">≡設定情報</label>
        <input type="checkbox" id="menu_label2" />

        <div id="menu">
            <ul>
                <li>
                    <h2>会員情報</h2>
                    <ul>
                        <li><b>名前:</b> <?php echo htmlspecialchars($_SESSION["user_name"], ENT_QUOTES); ?></li>

                        <li><b>年代:</b> <?php if (!$frameresult["age"]) { ?>未回答
                            <?php } else {
                                            echo htmlspecialchars($frameresult["age"], ENT_QUOTES); ?>代 <?php } ?></li>

                        <li><b>性別:</b> <?php echo htmlspecialchars($frameresult["gender"], ENT_QUOTES); ?></li>
                    </ul>
                </li>

                <li>
                    <h2>現在の観光計画</h2>
                    <ul>
                        <li><b>開始駅:</b>
                            <img id="pin" width="20" height="20" src="./icons/pop_start.png" alt="開始駅のアイコン" title="開始駅">
                            <div id="start_name"><?php echo htmlspecialchars($start_station_name, ENT_QUOTES); ?></div>
                        </li><br>

                        <li>
                            <b>昼食前に訪れる観光スポット:</b>
                            <img id="pin" width="20" height="20" src="./markers/pop_icon1_f.png" alt="昼食前に訪れる観光スポットのアイコン" title="昼食前に訪れる観光スポット">
                            <div id="toggle_s_l_spots_line">
                                <?php display_frame($s_l_spots_name, 1) ?>
                            </div>
                        </li><br>

                        <li><b>昼食予定地:</b>
                            <img id="pin" width="20" height="20" src="./icons/pop_lunch.png" alt="昼食予定地のアイコン" title="昼食予定地">
                            <div id="lunch_name"><?php echo htmlspecialchars($lunch_name, ENT_QUOTES); ?></div>
                        </li><br>

                        <li>
                            <b>昼食後に訪れる観光スポット:</b>
                            <img id="pin" width="20" height="20" src="./markers/pop_icon2_f.png" alt="昼食後に訪れる観光スポットのアイコン" title="昼食後に訪れる観光スポット">
                            <div id="toggle_l_d_spots_line">
                                <?php display_frame($l_d_spots_name, 2) ?>
                            </div>
                        </li><br>

                        <li><b>夕食予定地:</b>
                            <img id="pin" width="20" height="20" src="./icons/pop_dinner.png" alt="夕食予定地のアイコン" title="夕食予定地">
                            <div id="dinner_name"><?php echo htmlspecialchars($dinner_name, ENT_QUOTES); ?></div>
                        </li><br>

                        <li>
                            <b>夕食前に訪れる観光スポット:</b>
                            <img id="pin" width="20" height="20" src="./markers/pop_icon3_f.png" alt="夕食後に訪れる観光スポットのアイコン" title="夕食後に訪れる観光スポット">
                            <div id="toggle_d_g_spots_line">
                                <?php display_frame($d_g_spots_name, 3) ?>
                            </div>
                        </li><br>

                        <li><b>終了駅:</b>
                            <img id="pin" width="20" height="20" src="./icons/pop_goal.png" alt="終了駅のアイコン" title="終了駅">
                            <div id="goal_name"><?php echo htmlspecialchars($goal_station_name, ENT_QUOTES); ?></div>
                        </li>
                    </ul>
                </li>

                <li>
                    <h2>アンケート</h2>
                    <?php
                    print "アンケートの回答を締め切りました。ご回答くださった方々、誠にありがとうございました。";
                    /*
                    if ($frameresult["survey"]) {
                        print "<form action=\"\" method=\"POST\">";
                        print "<input type=\"submit\" id=\"survey\" name=\"survey\" value=\"回答する\" onClick=\"window.open('https://forms.gle/amw8j1wJDPcAn29h7?openExternalBrowser=1','_blank')\"><br>";
                        print "</form>";
                        print "回答は<font color=\"red\">1回</font>のみです<br>";
                        print "<b>システムを1度以上利用してからご回答ください</b>";
                    } else {
                        print "ご回答ありがとうございました";
                    }
                    */
                    ?>
                </li>

            </ul>
        </div>
    </div>
</body>

<!-- ドラッグアンドドロップを実装する用 -->
<script src="//cdnjs.cloudflare.com/ajax/libs/Sortable/1.15.0/Sortable.min.js"></script>
<script src="Sortable.min.js"></script>
<script>
    var el = document.getElementById('sort');
    var sortable = Sortable.create(el, {
        onSort: function(evt) {
            var items = el.querySelectorAll('li');
            for (var i = 0; i < items.length; i++) {
                //items[i].querySelector('.no_s_l').value = i + 1;
                //items[i].querySelector('.no_s_l').id = `s_l_${i + 1}`
                var src = `./icons/pop_icon_s_l${i + 1}.png`;
                items[i].querySelector('.pin_s_l').src = src;
            }
        }
    });
    var el2 = document.getElementById('sort2');
    var sortable2 = Sortable.create(el2, {
        onSort: function(evt) {
            var items = el2.querySelectorAll('li');
            for (var i = 0; i < items.length; i++) {
                //items[i].querySelector('.no_l_d').value = i + 1;
                var src = `./icons/pop_icon_l_d${i + 1}.png`;
                items[i].querySelector('.pin_l_d').src = src;
            }
        }
    });
    var el3 = document.getElementById('sort3');
    var sortable3 = Sortable.create(el3, {
        onSort: function(evt) {
            var items = el3.querySelectorAll('li');
            for (var i = 0; i < items.length; i++) {
                //items[i].querySelector('.no_d_g').value = i + 1;
                var src = `./icons/pop_icon_d_g${i + 1}.png`;
                items[i].querySelector('.pin_d_g').src = src;
            }
        }
    });

    function remade_plan() {
        var start, lunch, lunch_time, dinner_time, dinner, goal;
        var s_l_post_box = [];
        var l_d_post_box = [];
        var d_g_post_box = [];
        start = document.getElementById('plan_start_box').value;
        goal = document.getElementById('plan_goal_box').value;

        var lunch_box = document.getElementById('plan_lunch_box');
        if (lunch_box.className != "hidden") {
            lunch = lunch_box.value;
            lunch_time = lunch_box.querySelector('.time').value;
        } else {
            lunch = -1;
            lunch_time = 0;
        }
        var dinner_box = document.getElementById('plan_dinner_box');
        if (dinner_box.className != "hidden") {
            dinner = dinner_box.value;
            dinner_time = dinner_box.querySelector('.time').value;
        } else {
            dinner = -1;
            dinner_time = 0;
        }
        var lunch_post_box = [lunch, lunch_time];
        var dinner_post_box = [dinner, dinner_time];

        //alert(dinner_time);
        var s_l_spots = document.getElementById('sort').querySelectorAll('li');
        s_l_spots.forEach(function(element) {
            if (element.className != "hidden" && element.value != -1) {
                s_l_post_box.push([element.value, element.querySelector('.s_l_time').value, element.querySelector('.s_l_name').textContent]);
            }
        });
        if(s_l_post_box.length == 0){
            s_l_post_box.push([-1,0,"設定されていません"]);
        }
        var l_d_spots = document.getElementById('sort2').querySelectorAll('li');
        l_d_spots.forEach(function(element) {
            if (element.className != "hidden" && element.value != -1) {
                l_d_post_box.push([element.value, element.querySelector('.l_d_time').value, element.querySelector('.l_d_name').textContent]);
            }
        });
        if(l_d_post_box.length == 0){
            l_d_post_box.push([-1,0,"設定されていません"]);
        }
        var d_g_spots = document.getElementById('sort3').querySelectorAll('li');
        d_g_spots.forEach(function(element) {
            if (element.className != "hidden" && element.value != -1) {
                d_g_post_box.push([element.value, element.querySelector('.d_g_time').value, element.querySelector('.d_g_name').textContent]);
            }
        });
        if(d_g_post_box.length == 0){
            d_g_post_box.push([-1,0,"設定されていません"]);
        }

        jQuery(function($) {
            $.ajax({
                url: "ajax_reload_making_plan.php",
                type: "POST",
                dataType: "json",
                data: {
                    post_data_1: start,
                    post_data_2: goal,
                    post_data_3: lunch_post_box,
                    post_data_4: dinner_post_box,
                    post_data_5: s_l_post_box,
                    post_data_6: l_d_post_box,
                    post_data_7: d_g_post_box
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert("ajax通信に失敗しました");
                },
                success: function(response) {
                    //alert(response);
                    location.reload();
                }
            });
        });

    };
</script>

</html>
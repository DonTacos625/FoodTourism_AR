<?php

?>

<!doctype html>
<html>

<head>
    <style>
        #title {
            margin: 0px;
        }

        #footer {
            font-size: 1vw;
        }

        #header {
            display: flex;
            padding-bottom: 20px;
        }

        #header #userdata_box {
            top: -40px;
            float: right;
            border: solid;
            border-color: #111101;
            z-index: 2;
        }

        .container-fluid {
            padding-bottom: 80px;
        }

        @media screen and (min-width:769px) {
            #toggle_menu {
                display: none;
            }
        }

        @media screen and (min-width:769px) and (max-width:1366px) {
            #title {
                font-size: 25px;
            }

            #header #userdata_box {
                font-size: 25px;
            }
        }

        @media screen and (max-width:768px) {
            #title {
                font-size: 3vw;
            }

            #footer {
                font-size: 1.7vw;
            }

            h2 {
                margin: 0px;
                font-size: 19px;
            }

        }
    </style>
</head>

<body>

    <div id="header">
        <div id="title">
            <h1>横浜みなとみらいフードツーリズム支援システム</h1>
        </div>
    </div>
    <div id="changer" class="mr-3">
        対象地域チェンジャー<br>
        <select name="forbidden" size="1" onchange="change_area(value)">
            <option value="0"> スポットを選択してください </option>
            <option value="1"> みなとみらい </option>
            <option value="2"> 蓮根 </option>
            <option value="3"> 調布 </option>
        </select><br>
    </div>
</body>

<footer id="footer" class="fixed-bottom d-flex justify-content-end">
    <p>Copyright(c) 2023 山本佳世子研究室 All Rights Reserved.</p>
</footer>

</html>
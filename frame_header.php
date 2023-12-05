<?php

?>

<!doctype html>
<html>

<head>
    <style>
        #title {
            margin: 0px;
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

</body>
<!-- 
<footer class="position-absolute top-100 start-50 translate-middle">
    <p>Copyright(c) 2023 山本佳世子研究室 All Rights Reserved.</p>
</footer>
-->

</html>
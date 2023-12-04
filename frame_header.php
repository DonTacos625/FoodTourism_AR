<?php

?>

<!doctype html>
<html>

<head>
    <style>
        h1 {
            margin: 0px;
        }

        #header {
            display: flex;
            padding-bottom: 80px;
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
            h1 {
                font-size: 25px;
            }

            #header #userdata_box {
                font-size: 25px;
            }
        }

        @media screen and (max-width:768px) {
            h1 {
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
            <h1>横浜みなとみらいフードツーリズム計画作成支援システム</h1>
        </div>
        <div id="userdata_box" class="position-absolute top-0 end-0">
            <h2>会員情報</h2>

            <b>名前:</b> <?php echo htmlspecialchars($_SESSION["user_name"], ENT_QUOTES); ?><br>

            <b>年代:</b> <?php if (!$frameresult["age"]) { ?>
                未回答
                <?php } else {
                            echo htmlspecialchars($frameresult["age"], ENT_QUOTES); ?>代 <?php } ?><br>

            <b>性別:</b> <?php echo htmlspecialchars($frameresult["gender"], ENT_QUOTES); ?><br>
        </div>
        <div id="survey_box">
            <h2>アンケート</h2>
            <p>
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
            </p>
        </div>
    </div>

</body>
<footer class="position-absolute top-100 start-50 translate-middle">
    <p>Copyright(c) 2023 山本佳世子研究室 All Rights Reserved.</p>
</footer>

</html>
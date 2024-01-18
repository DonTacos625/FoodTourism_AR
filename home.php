<?php
require "frame_define.php";
require "frame_header.php";
require "frame_menu.php";
require "frame_rightmenu.php";
?>

<!doctype html>
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

    <title>ホーム</title>

    <style>
        #homebox {
            width: 70vw;
            float: left;
            margin-left: 5px;
        }

        #homebox h2 {
            margin: 0px;
        }

        #homebox h3 {
            border-left: 5px solid #000080;
            margin: 0px;
        }

        @media screen and (min-width:769px) and (max-width:1366px) {
            h2 {
                font-size: 20px;
            }

            h3 {
                font-size: 18px;
            }
        }

        @media screen and (max-width:768px) {
            h2 {
                font-size: 19px;
            }

            h3 {
                font-size: 17px;
            }

            #homebox {
                width: auto;
                margin: 0px;
            }
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <main class="row">
            <div id="homebox">
                <h2>ホーム</h2>

                <h2>
                    <font color=#000080>&emsp;本運用中</font>
                </h2>

                <h3>目的</h3>
                <p>
                    飲食を主な目的とした観光を支援することを目的としたシステムです。
                    観光計画の作成と現地での観光支援を行うことができます。
                    完成した観光計画の経路は、各地点を徒歩で移動した場合の最小距離として
                    マップ上に表示されます。
                </p><br>

                <h3>フードツーリズムとは？</h3>
                <p>
                    フードツーリズムとは、地域ならではの食・食文化を楽しむことを目的とした旅（日本フードツーリズム協会より）のことです。
                    本システムは、利用者が飲食を主体とした観光計画を作成することを支援するためのシステムです。
                </p><br>

                <h3>更新履歴</h3>
                <p>
                    2024/1/20 本運用終了<br>
                    2023/12/22 本運用開始<br>
                    2023/12/14 試験運用終了<br>
                    2023/12/7 試験運用開始
                </p><br>

                <h3>使い方</h3>
                <p>
                    ページ上部の観光計画作成から情報を登録することで観光計画を作成することが可能です。<br>
                    地図上のアイコンを押すことでポップアップが表示されます。<br>
                    詳しい使い方は<a href="explain.php#set_station">こちら</a><br><br>
                    
                    <font color=#000080><big>アンケートの回答を締め切りました。ご回答くださった方々、誠にありがとうございました。</big></font>
                    <!--
                    <font color=#000080><big><a href="survey.php">利用後、アンケートへのご回答をお願いします。</a></big></font>
                    -->

                </p><br>

                <!--
                対象地域チェンジャー<br>
                <div id="changer" class="d-flex">
                    <select name="forbidden" size="1" onchange="change_area(value)">
                        <option value="0"> スポットを選択してください </option>
                        <option value="1"> みなとみらい </option>
                        <option value="2"> 蓮根 </option>
                        <option value="3"> 調布 </option>
                    </select><br>
                    <div class="ml-3">　現在の対象地域：<?php //echo htmlspecialchars($area_message, ENT_QUOTES); ?></div>
                </div><br>
                -->

                <h3>連絡先</h3>
                <p>
                    不具合等ございましたら、下記のメールアドレスまでご連絡下さい。<br>
                    作成者:平野<br>
                    h2230116@edu.cc.uec.ac.jp<br>
                </p>
                <div>
        </main>
        <footer>
            <div>Copyright(c) 2023 山本佳世子研究室 All Rights Reserved.</div>
            <div>Powered by <a href="http://webservice.recruit.co.jp/">ホットペッパーグルメ Webサービス</a></div>
        </footer>
    </div>
</body>

</html>
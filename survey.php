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
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-214561408-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-214561408-1');
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

<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script>
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
</script>

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
                    飲食を主な目的とした観光計画の作成を支援することを目的としたシステムです。
                    使い方に従うことで観光計画を作成することができます。
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
                    2022/1/28 本運用終了<br>
                    2022/1/10 LINEの内部ブラウザからだとアンケートに回答できない問題を修正<br>
                    2022/1/7 IDの頭文字を数字にした場合データベースエラーが起きる問題を修正<br>
                    2022/1/7 本運用開始<br>
                    2021/12/10 試験運用終了<br>
                    2021/12/7 試験運用開始
                </p><br>

                <h3>使い方</h3>
                <p>
                    ページ上部の観光計画作成から情報を登録することで観光計画を作成することが可能です。<br>
                    地図上のアイコンを押すことでポップアップが表示されます。<br>
                    詳しい使い方は<a href="explain.php#set_station">こちら</a><br><br>
                    <font color=#000080><big>アンケートの回答を締め切りました。ご回答くださった方々、誠にありがとうございました。</big></font>
                    <!--
                    <font color=#000080><big>利用後、アンケートへのご回答をお願いします。</big>></font>
                    <a href="https://docs.google.com/forms/d/e/1FAIpQLScQcIeHdLfLpeNjIDbjEBCPtureGZi007aUUhgwqXhQffXR_A/viewform?usp=sf_link" target="blank">回答する</a>
                    -->
                </p><br>

                対象地域チェンジャー<br>
                <select name="forbidden" size="1" onchange="change_area(value)">
                    <option value="0"> スポットを選択してください </option>
                    <option value="1"> みなとみらい </option>
                    <option value="2"> 蓮根 </option>
                    <option value="3"> 調布 </option>
                </select><br>

                <h3>連絡先</h3>
                <p>
                    不具合等ございましたら、下記のメールアドレスまでご連絡下さい。<br>
                    作成者:平野<br>
                    h2230116@edu.cc.uec.ac.jp<br>
                </p>
                <div>
        </main>
        <footer>
            <p>Copyright(c) 2021 山本佳世子研究室 All Rights Reserved.</p>
        </footer>
    </div>
</body>

</html>
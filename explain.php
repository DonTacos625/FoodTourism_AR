<?php
require "frame_define.php";
require "frame_header.php";
require "frame_menu.php";
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

    <title>使い方</title>

    <style>
        #explainbox {
            width: 70vw;
            float: left;
        }

        #explainbox h2 {
            margin: 0px;
        }

        #explainbox h3 {
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

            #explainbox {
                width: auto;
                margin: 0px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <main class="row">
            <div id="explainbox">
                <h2 id="index">当サイトの使い方</h2>
                <ul>
                    <li>ページの見方</li>
                    <ul>
                        <li><a href="#userinfo">会員情報</a></li>
                        <li><a href="#plan">現在の観光計画</a></li>
                        <li><a href="#survey">アンケート</a></li>
                    </ul>
                    <li>観光計画作成</li>
                    <ul>
                        <li><a href="#set_station">開始・終了駅の設定</a></li>
                        <li><a href="#search">飲食店の検索・決定</a></li>
                        <li><a href="#select_spots">観光スポット選択</a></li>
                    </ul>
                    <li>スポット一覧</li>
                    <ul>
                        <li><a href="#spots_view">スポット一覧</a></li>
                    </ul>
                    <li>マイページ</li>
                    <ul>
                        <li><a href="#see_route">作成した観光経路を見る</a></li>
                        <li><a href="#password">パスワード変更</a></li>
                        <li><a href="#logout">ログアウト</a></li>
                    </ul>
                </ul>

                <p>
                <h3 class="px-0" >ページの見方</h3>
                <p>
                    ページの右部には常に「会員情報」、「現在の観光計画」、「アンケート」が表示されています。<br><br>
                    <b id="userinfo">会員情報</b><br>
                    &emsp;あなたのIDと登録情報が表示されています。パスワードの変更は「マイページ」から行えます。<br><br>
                    <b id="plan">現在の観光計画</b><br>
                    &emsp;あなたが作成した、現在の観光計画が表示されています。また、設定した観光スポットを観光計画から削除または順番の変更を行うことができます。<br><br>
                    <b id="survey">アンケート</b><br>
                    &emsp;システムを１度以上ご利用いただいたのち、アンケートにご協力をお願いします。「回答する」ボタンを押すことでGoogleFormからアンケートに回答できます。<br>
                    <a href="#index">▲ページ上部に戻る</a>
                </p><br>
                </p>

                <p>
                <h3 class="px-0" >観光計画作成の手順</h3>
                <p>
                    観光経路を作成するために、以下の3つの手順を行います。<br><br>

                    <b id="set_station">(1)開始・終了駅の設定</b><br>
                    &emsp;あなたが観光を開始する駅と終了する駅をセレクトボックスか地図上から選択してください。<br><br>
                    <b id="search">(2)飲食店の検索・決定</b><br>
                    &emsp;画面上部にある検索フォームから飲食店を検索することができます。
                    「地図上で結果を表示」を押すことで地図上に結果を表示することもできます。
                    また、各種項目を設定することでより絞り込んだ検索を行えます。
                    昼食、夕食時に訪れたい飲食店が決まったら、
                    「昼食に設定する」、「夕食に設定する」を押してそれぞれに設定してください。<br><br>
                    <b id="select_spots">(3)観光スポット選択</b><br>
                    &emsp;現在の観光経路周辺の観光スポットが地図上に表示されます。
                    「一覧で結果を表示」を押すことで一覧の形で結果を表示することもできます。
                    各種項目を設定することで表示する観光スポットを絞り込むことができます。
                    その中から訪れたい観光スポットのマーカーをクリック(タップ)し、
                    「昼食前」、「昼食後」、「夕食後」の各アイコンを押すことで、
                    どの時間に訪れるか選択することができ、地図の下にある「経路再表示」を押すことで
                    観光経路を更新することができます。
                    観光スポットは各時間帯でそれぞれ3地点まで設定することができます。
                    観光スポットを計画から削除、または順番を変更したい場合はページ右部の「現在の観光計画」
                    から各ボタンを押すことで変更することができます。
                    また、観光スポットを選択した後に開始・終了駅または昼食・夕食の飲食店を変更すると、
                    <font color="red">設定した観光スポットがリセットされる</font>ためご注意ください。<br><br>

                    「作成した観光計画を見る」で最終的な観光経路が表示されます。<br>

                    <a href="set_station.php">観光計画を作成する>>></a><br><br>

                    <a href="#index">▲ページ上部に戻る</a>
                </p><br>
                </p>

                <p>
                <h3 class="px-0"  id="spots_view">スポット一覧</h3>
                <p>
                    &emsp;本システムに登録されているスポットを地図上で閲覧することができます。マップの右上部で表示するスポットを変更できます。
                    また、スポットのマーカーをクリック（タップ）し、各アイコンを押すことで、地図上からも観光計画に組み込むことができます（駅、飲食店のみ）。<br>
                    <a href="#index">▲ページ上部に戻る</a>
                </p><br>
                </p>

                <p>
                <h3 class="px-0" >マイページ</h3>
                <p>
                    <b id="see_route">作成した観光計画を見る</b><br>
                    &emsp;最終的に作成された観光計画の経路を見ることができます。
                    その他の情報として総歩行距離と歩行時間（歩行速度を時速4.8kmと想定）が表示されます。
                    また、地図の下にある「観光計画を保存」を押すことで現在の観光計画を保存することができます。<br><br>

                    <b id="password">パスワードの変更</b><br>
                    &emsp;パスワードの変更を行います
                <ul>
                    <li>旧パスワード:現在のパスワードを入力</li>
                    <li>新パスワード:半角英数字をそれぞれ1種類以上含む6~15文字で入力</li>
                    <li>新パスワード(確認):新パスワードを再入力</li>
                </ul>
                入力後、「変更」ボタンを押す<br><br>
                <b id="logout">ログアウト</b><br>
                &emsp;ログアウト状態にします。ログアウトを押した時点で、確認画面を挟まずログアウト画面に移行するためご注意ください。<br>
                <a href="#index">▲ページ上部に戻る</a>
                </p>
                </p><br>

            </div>
        </main>
        <footer>
            <p>Copyright(c) 2021 山本佳世子研究室 All Rights Reserved.</p>
        </footer>
    </div>
</body>

</html>
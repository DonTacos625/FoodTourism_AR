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
                画像付きのマニュアルは<a href="https://drive.google.com/file/d/1r7a-p5nmR4ws5O6M-80ljNtVzPAdzxWQ/view?usp=drive_link" target="blank">こちら</a><br>
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
                        <li><a href="#save_plan">観光計画の保存</a></li>
                    </ul>
                    <li>現地での観光支援</li>
                    <ul>
                        <li><a href="#nearby_spots">周辺スポットの検索</a></li>
                        <li><a href="#navigation_plan">観光をナビゲーション</a></li>
                    </ul>
                    <li>一覧</li>
                    <ul>
                        <li><a href="#view_all_spots">スポット一覧</a></li>
                        <li><a href="#view_other_plans">他のユーザが作成した観光計画</a></li>
                    </ul>
                    <li>マイページ</li>
                    <ul>
                        <li><a href="#saved_plans">保存した観光計画</a></li>
                        <li><a href="#password">パスワード変更</a></li>
                        <li><a href="#logout">ログアウト</a></li>
                    </ul>
                </ul>

                <p>
                <h3 class="px-0">ページの見方</h3>
                <p>
                    ページの右上部には常に「会員情報」、「現在の観光計画」が表示されています。<br>
                    また、上部のメニューバーから本システムの各機能を利用することができます。<br><br>
                    <b id="userinfo">会員情報</b><br>
                    &emsp;あなたのIDと登録情報が表示されています。パスワードの変更は「マイページ」から行えます。<br><br>
                    <b id="plan">現在作成中の観光計画</b><br>
                    &emsp;クリック(タップ)することであなたが現在作成している観光計画が表示されます。また、設定した飲食店、観光スポットを観光計画から削除または順番の変更を行うことができます。<br><br>
                    <b id="survey">アンケート</b><br>
                    &emsp;システムの利用後、アンケートへのご協力をお願いいたします。
                    アンケートは「観光計画作成」に関する機能と「現地での観光支援」に関する機能の二種類あり、
                    利用した機能に応じてご回答をお願いいたします。<br>
                    <a href="survey.php">アンケートに回答する>>></a><br><br>

                    <a href="#index">▲ページ上部に戻る</a>
                </p><br>
                </p>

                <p>
                <h3 class="px-0">観光計画作成</h3>
                <p>
                    観光計画を作成し保存するために、以下の4つの手順を行います。<br><br>

                    <b id="set_station">(1)開始・終了駅の設定</b><br>
                    &emsp;あなたが観光を開始する駅と終了する駅をセレクトボックスか地図上から選択してください。<br><br>
                    <b id="search">(2)飲食店の検索・決定</b><br>
                    &emsp;画面上部にある検索フォームから飲食店を検索することができます。
                    「地図上で結果を表示」を押すことで地図上に結果を表示することもできます。
                    また、各種項目を設定することでより絞り込んだ検索を行えます。
                    昼食、夕食時に訪れたい飲食店が決まったら、
                    各々の詳細ページで滞在時間を入力し、昼食・夕食を食べる飲食店として設定してください。<br><br>
                    <b id="select_spots">(3)観光スポット選択</b><br>
                    &emsp;現在の観光経路周辺の観光スポットが地図上に表示されます。
                    「一覧で結果を表示」を押すことで一覧の形で結果を表示することもできます。
                    各種項目を設定することで表示する観光スポットを絞り込むことができます。
                    その中から訪れたい観光スポットのマーカーをクリック(タップ)し、
                    詳細ページから、「昼食前」、「昼食後」、「夕食後」の
                    どの時間に訪問するか選択することができます。
                    観光スポットは各時間帯でそれぞれ3地点まで設定することができます。
                    観光スポットを計画から削除、または順番を変更したい場合はページ右部の「現在の観光計画」
                    から各ボタンを押すことで変更することができます。
                    また、観光スポットを選択した後に開始・終了駅または昼食・夕食の飲食店を変更すると、
                    <font color="red">設定した観光スポットがリセットされる</font>ためご注意ください。<br><br>
                    <b id="save_plan">(4)観光計画の保存</b><br>
                    &emsp;作成した観光計画の観光経路が地図上に表示されます。
                    その他の情報として総歩行距離と歩行時間（歩行速度を時速4.8kmと想定）が表示されます。
                    観光計画のプラン名とメモを記載し、他のユーザに公開するか選択することで観光計画を保存することができます。<br><br>
                    保存した観光計画は「マイページ＞保存した観光計画」で閲覧・編集することができます。<br>

                    <a href="set_station.php">観光計画を作成する>>></a><br><br>

                    <a href="#index">▲ページ上部に戻る</a>
                </p><br>
                </p>

                <p>
                <h3 class="px-0">現地での観光支援</h3>
                <p>
                    <b id="nearby_spots">周辺スポットの検索</b><br>
                    &emsp;現在地周辺にある飲食店及び観光スポットを検索することができます。
                    検索結果は地図上またはARカメラ上で表示することができます。
                    また、選択したスポットへの道順と移動距離を地図上またはARカメラ上で表示することができます。<br><br>

                    <b id="navigation_plan">観光をナビゲーション</b><br>
                    &emsp;保存した観光計画に沿ってナビゲーションを行うことができます。
                    保存した観光計画の中から使用する観光計画を選択し、現在地と観光経路の位置関係を表示することができます。
                    「周辺スポットの検索」と同様に、選択したスポットへの道順と移動距離を地図上またはARカメラ上で表示することができます。<br><br>

                    <b id="attention">注意事項</b><br>
                    <font color="red">
                    &emsp;※これらの機能は位置情報が取得できなければ正しく動作しないため
                    インターネット環境がある場所で利用することを推奨しています。
                    </font><br><br>

                    <a href="#index">▲ページ上部に戻る</a>
                </p><br>
                </p>

                <p>
                <h3 class="px-0">一覧</h3>
                <p>
                    <b id="view_all_spots">スポット一覧</b><br>
                    &emsp;本システムに登録されているスポットを地図上で閲覧することができます。マップの右上部で表示するスポットを変更できます。
                    また、スポットのマーカーをクリック（タップ）し、各詳細ページから観光計画に組み込むこともできます。<br><br>

                    <b id="view_other_plans">保存した観光計画</b><br>
                    &emsp;他のユーザが作成した公開されている観光計画を閲覧することができます。
                    また、個別の詳細ページから観光計画をコピーすることができます。<br><br>
                    <a href="#index">▲ページ上部に戻る</a>
                </p><br>
                </p>

                <p>
                <h3 class="px-0">マイページ</h3>
                <p>
                    <b id="saved_plans">保存した観光計画</b><br>
                    &emsp;今までに作成した観光計画を閲覧することができます。
                    個別の詳細ページから観光計画の編集を行うことができます。<br><br>

                    <b id="password">登録情報変更</b><br>
                    &emsp;ユーザの登録情報の変更を行います。以下の情報を変更することができます。
                <ul>
                    <li>パスワード</li>
                    <li>体重</li>
                    <li>性別</li>
                    <li>年代</li>
                </ul>
                <b id="logout">ログアウト</b><br>
                &emsp;ログアウト状態にします。ログアウトを押した時点で、確認画面を挟まずログアウト画面に移行するためご注意ください。<br>
                <a href="#index">▲ページ上部に戻る</a>
                </p>
                </p><br>

            </div>
        </main>
        <footer>
            <p>Copyright(c) 2023 山本佳世子研究室 All Rights Reserved.</p>
        </footer>
    </div>
</body>

</html>
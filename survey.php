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

    <title>アンケート</title>

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

        }

        @media screen and (max-width:768px) {
            h2 {
                font-size: 19px;
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
                <h2>アンケート</h2>

                <h2>
                    <font color=#000080>&emsp;本運用中</font>
                </h2>

                <h3 class="px-0">アンケートのご協力をお願いいたします。</h3>
                <p>
                    本システムの利用後、ご利用いただいた機能に応じて以下のアンケートへのご回答をよろしくお願いいたします。<br>
                    <!--
                    試験運用のアンケート<br>
                    <a href="https://docs.google.com/forms/d/e/1FAIpQLSfMJvh44Jx_t_EKmWqyb8eGyGfFAetVmiDxitJj3Gocse6EkQ/viewform?usp=sf_link" target="blank">https://docs.google.com/forms/d/e/1FAIpQLSfMJvh44Jx_t_EKmWqyb8eGyGfFAetVmiDxitJj3Gocse6EkQ/viewform?usp=sf_link</a><br>
                    -->

                    <!--
                    (1)「観光計画作成」機能に関するアンケート<br>
                    <a href="https://docs.google.com/forms/d/e/1FAIpQLScVj6saWaQCF1t1RTBdVTmtD-ursZt-nKPl_WWUHb2rJv5MXQ/viewform?usp=sf_link" target="blank">https://docs.google.com/forms/d/e/1FAIpQLScVj6saWaQCF1t1RTBdVTmtD-ursZt-nKPl_WWUHb2rJv5MXQ/viewform?usp=sf_link</a><br><br>
                    (2)「現地での観光支援」機能に関するアンケート<br>
                    <a href="https://docs.google.com/forms/d/e/1FAIpQLSde9Q0OGMhy9ooYVERz0eucCu1s-uxz5cDOrKekqNGCXOP6Kw/viewform?usp=sf_link" target="blank">https://docs.google.com/forms/d/e/1FAIpQLSde9Q0OGMhy9ooYVERz0eucCu1s-uxz5cDOrKekqNGCXOP6Kw/viewform?usp=sf_link</a>
                    -->
                    
                    <font color=#000080><big>アンケートの回答を締め切りました。ご回答くださった方々、誠にありがとうございました。</big></font>

                </p><br>

                <h3 class="px-0">連絡先</h3>
                <p>
                    不具合等ございましたら、下記のメールアドレスまでご連絡下さい。<br>
                    作成者:平野<br>
                    h2230116@edu.cc.uec.ac.jp<br>
                </p>
                <div>
        </main>
        <footer>
            <p>Copyright(c) 2023 山本佳世子研究室 All Rights Reserved.</p>
        </footer>
    </div>
</body>

</html>
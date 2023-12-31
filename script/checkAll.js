        //チェックボックスをすべて選択する処理
        $(function() {

            var checkAll = '#checkAll'; //「すべて」のチェックボックスのidを指定
            var checkBox = 'input[name="categorys[]"]'; //チェックボックスのnameを指定

            $(checkAll).on('click', function() {
                $(checkBox).prop('checked', $(this).is(':checked'));
            });

            $(checkBox).on('click', function() {
                var boxCount = $(checkBox).length; //全チェックボックスの数を取得
                var checked = $(checkBox + ':checked').length; //チェックされているチェックボックスの数を取得
                if (checked === boxCount) {
                    $(checkAll).prop('checked', true);
                } else {
                    $(checkAll).prop('checked', false);
                }
            });

        });
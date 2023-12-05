// ページネーションを生成する要素を取得
const ul = document.querySelector(".pagenations-wrapper");

// 「戻るタグ」
let prevTag = `<li class="prev" onclick="createPagenation(totalPages, ${page - 1})">
                <a href="?page=${page - 1}">Prev</a></li>`;
// 「次へタグ」
let nextTag = `<li class="next" onclick="createPagenation(totalPages, ${page + 1})">
                <a href="?page=${page + 1}">Next</a></li>`;
// 1ページ目のリストタグ
let firstPage = `<li onclick="createPagenation(totalPages, 1)">
                  <a href="?page=1">1</a></li>`;
// 最後のページのリストタグ
let lastPage = `<li onclick="createPagenation(totalPages, ${totalPages})">
                  <a href="?page=${totalPages}">${totalPages}</a></li>`;
function createPagenation(totalPages, page) {
    let liTag = ""; // 条件にあった場合にHTMLを代入していきます
    let beforePage = page - 1;

    // 2ページ以降、Prev(戻る)タグを表示
    if (page > 1) {
        liTag += prevTag;
    }

    // 1ページ目のリストタグ
    // 今開いているページが1ページ目ならアクティブ
    if (page == 1) {
        liTag += `<li onclick="createPagenation(totalPages, 1)" class="active">
                                <a href="?page=1">1</a></li>`;
    } else {
        liTag += firstPage;
    }

    // トータルのページが6ページより小さい場合
    if (totalPages < 6) {
        // 「トータルページ-1」と同じだけのリストを表示
        for (let i = 2; i <= totalPages - 1; i++) {
            if (i == page) {
                // 今開いているページをアクティブに
                liTag += `<li onclick="createPagenation(totalPages, ${i})" class="active">
                                    <a href="?page=${i}">${i}</a></li>`;
            } else {
                liTag += `<li onclick="createPagenation(totalPages, ${i})">
                                    <a href="?page=${i}">${i}</a></li>`;
            }
        }
    } else {
        // トータルページが6ページ以上の場合
        if (page > 3) {
            // 開いているページが3ページより大きい場合「...」を表示
            liTag += `<li class="dots"><span>...</span></li>`;
        }
        // 今開いているページが1ページ目か、2ページ目の場合
        if (page == 1 || page == 2) {
            for (let i = 2; i < 4; i++) {
                // 2つだけリストタグを表示
                if (i == page) {
                    // 今開いているページをアクティブに
                    liTag += `<li onclick="createPagenation(totalPages, ${i})" class="active">
                                      <a href="?page=${i}">${i}</a></li>`;
                } else {
                    liTag += `<li onclick="createPagenation(totalPages, ${i})">
                                      <a href="?page=${i}">${i}</a></li>`;
                }
            }
        }

        // 今開いているページが2ページ以上、トータルページ未満の場合
        if (page > 2 && page < totalPages - 1) {
            // 今開いているページが2ページより大きい場合

            // 今開いているページを含むリストタグを3つ表示させる
            for (let i = beforePage; i < beforePage + 3; i++) {
                if (i == page) {
                    // 今開いているページをアクティブに
                    liTag += `<li onclick="createPagenation(totalPages, ${i})" class="active">
                                      <a href="?page=${i}">${i}</a></li>`;
                } else {
                    liTag += `<li onclick="createPagenation(totalPages, ${i})">
                                      <a href="?page=${i}">${i}</a></li>`;
                }
            }
        }

        // 今開いているページがトータルページより2つより小さい場合「...」を表示
        if (page < totalPages - 2) {
            liTag += `<li class="dots">…</li>`;
        }

        // 今開いているページが最終ページ、またはそのひとつ前のページの場合
        if (page == totalPages || page == totalPages - 1) {
            // beforePageの調整
            beforePage = page == totalPages ? beforePage - 1 : beforePage;
            // リストタグを2つ表示
            for (let i = beforePage; i < beforePage + 2; i++) {
                if (i == page) {
                    // 今開いているページをアクティブに
                    liTag += `<li onclick="createPagenation(totalPages, ${i})" class="active">
                                      <a href="?page=${i}">${i}</a></li>`;
                } else {
                    liTag += `<li onclick="createPagenation(totalPages, ${i})">
                                      <a href="?page=${i}">${i}</a></li>`;
                }
            }
        }
    }

    // 今開いているページが最終ページならアクティブに
    if (page == totalPages) {
        liTag += `<li onclick="createPagenation(totalPages, ${totalPages})" class="active">
                                <a href="?page=${totalPages}">${totalPages}</a></li>`;
    } else {
        liTag += lastPage;
    }

    // 開いているページがトータルページ未満の場合、Next(次へ)タグを表示
    if (page < totalPages) {
        liTag += nextTag;
    }

    // 最後にすべてのHTMLを代入したliTagを返す
    return liTag;
}
ul.innerHTML = createPagenation(totalPages, page);
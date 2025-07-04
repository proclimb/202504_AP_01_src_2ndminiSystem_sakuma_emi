var inputName, inputKana, inputPostalCode, inputPrefecture, inputCityTown, inputBuilding, inputTel, inputEmail;

document.addEventListener('DOMContentLoaded', function () {
    inputName = document.getElementById('name');
    inputKana = document.getElementById('kana');
    inputPostalCode = document.getElementById('postal_code');
    inputPrefecture = document.getElementById('prefecture');
    inputCityTown = document.getElementById('city_town');
    inputBuilding = document.getElementById('building');
    inputTel = document.getElementById('tel');
    inputEmail = document.getElementById('email');
    inputFile1 = document.getElementById('document1');
    inputFile2 = document.getElementById('document2');

    toggleConfirmButton()

    inputName.addEventListener('input', function () {
        // 入力値を取得
        const value = inputName.value;

        removeErrorMessage(inputName);
        inputName.classList.remove("error-form");
        removeServerErrorMessage(inputName);

        // バリデーションや表示の更新
        if (value == "") {
            errorElement(inputName, "お名前が入力されていません");
        } else if (value.length > 20) {
            errorElement(inputName, "お名前は20文字以内で入力してください");
        }
        toggleConfirmButton()
    });

    inputKana.addEventListener('input', function () {
        // 入力値を取得
        const value = inputKana.value;

        removeErrorMessage(inputKana);
        inputKana.classList.remove("error-form");
        removeServerErrorMessage(inputKana);

        // バリデーションや表示の更新
        if (value == "") {
            errorElement(inputKana, "ふりがなが入力されていません");
        } else if (!validateKana(value)) {
            errorElement(inputKana, "ひらがなを入れてください");
        }
        toggleConfirmButton()
    });

    inputPostalCode.addEventListener('input', function () {
        // 入力値を取得
        const value = inputPostalCode.value;

        removeErrorMessage(inputPostalCode);
        inputPostalCode.classList.remove("error-form");
        removeServerErrorMessage(inputPostalCode);

        // バリデーションや表示の更新
        if (value == "") {
            errorElement(inputPostalCode, "郵便番号が入力されていません");
        } else if (!/^\d{3}-\d{4}$/.test(value)) {
            errorElement(inputPostalCode, "郵便番号が正しくありません");
        }
        toggleConfirmButton()
    });

    inputPrefecture.addEventListener('input', function () {
        // 入力値を取得
        const value = inputPrefecture.value;
        const valueCityTown = inputCityTown.value;

        removeErrorMessage(inputPrefecture);
        inputPrefecture.classList.remove("error-form");
        removeServerErrorMessage(inputPrefecture);

        // バリデーションや表示の更新
        if (value == "" || valueCityTown == "") {
            errorElement(document.data.building, "住所(都道府県もしくは市区町村・番地)が入力されていません");
        } else if (value.length > 10) {
            errorElement(document.data.building, "郵便番号と住所が一致しません");
        }
        toggleConfirmButton()
    });

    inputCityTown.addEventListener('input', function () {
        // 入力値を取得
        const value = inputCityTown.value;
        const valuePrefecture = inputPrefecture.value;

        removeErrorMessage(inputCityTown);
        inputCityTown.classList.remove("error-form");
        removeServerErrorMessage(inputCityTown);

        // バリデーションや表示の更新
        if (value == "" || valuePrefecture == "") {
            errorElement(document.data.building, "住所(都道府県もしくは市区町村・番地)が入力されていません");
        } else if (value.length > 50) {
            errorElement(document.data.building, "市区町村・番地もしくは建物名は50文字以内で入力してください");
        }
        toggleConfirmButton()
    });

    inputBuilding.addEventListener('input', function () {
        // 入力値を取得
        const value = inputBuilding.value;

        removeErrorMessage(inputBuilding);
        inputBuilding.classList.remove("error-form");
        removeServerErrorMessage(inputBuilding);

        // バリデーションや表示の更新
        if (value.length > 50) {
            errorElement(inputBuilding, "市区町村・番地もしくは建物名は50文字以内で入力してください物名は50文字以内で入力してください");
        }
        toggleConfirmButton()
    });

    inputTel.addEventListener('input', function () {
        // 入力値を取得
        const value = inputTel.value;

        removeErrorMessage(inputTel);
        inputTel.classList.remove("error-form");
        removeServerErrorMessage(inputTel);

        // バリデーションや表示の更新
        if (value == "") {
            errorElement(inputTel, "電話番号が入力されていません");
        } else if (!validateTel(value)) {
            errorElement(inputTel, "電話番号は12~13桁で正しく入力してください");
        }
        toggleConfirmButton()
    });

    inputEmail.addEventListener('input', function () {
        // 入力値を取得
        const value = inputEmail.value;

        removeErrorMessage(inputEmail);
        inputEmail.classList.remove("error-form");
        removeServerErrorMessage(inputEmail);

        // バリデーションや表示の更新
        if (value == "") {
            errorElement(inputEmail, "メールアドレスが入力されていません");
        } else if (!validateMail(value)) {
            errorElement(inputEmail, "有効なメールアドレスを入力してください");
        }
        toggleConfirmButton()
    });

    inputFile1.addEventListener('change', function () {

        removeErrorMessage(inputFile1);
        inputFile1.classList.remove("error-form");
        removeServerErrorMessage(inputFile1);

        // バリデーション処理
        if (inputFile1 && inputFile1.files.length > 0) {
            const file1 = inputFile1.files[0];
            const type1 = file1.type;

            // PNG もしくは JPEG 以外はエラー
            if (type1 !== "image/png" && type1 !== "image/jpeg") {
                errorElement(inputFile1, "ファイル形式は PNG または JPEG のみ許可されています");
            }
        }
        toggleConfirmButton()
    });

    inputFile2.addEventListener('change', function () {

        removeErrorMessage(inputFile2);
        inputFile2.classList.remove("error-form");
        removeServerErrorMessage(inputFile2);

        // バリデーション処理
        if (inputFile2 && inputFile2.files.length > 0) {
            const file2 = inputFile2.files[0];
            const type2 = file2.type;

            // PNG もしくは JPEG 以外はエラー
            if (type2 !== "image/png" && type2 !== "image/jpeg") {
                errorElement(inputFile2, "ファイル形式は PNG または JPEG のみ許可されています");
            }
        }
        toggleConfirmButton()
    });
});


/**
 * 各項目の入力を行う
 */


function validate() {

    // 1.エラー有無の初期化(true:エラーなし、false：エラーあり)
    var flag = true;

    if (hasErrorMessage(inputName)) {
        // すでにエラーメッセージがある場合の処理
        flag = false;
    } else if (hasErrorMessage(inputKana)) {
        flag = false;
    } else if (hasErrorMessage(inputPostalCode)) {
        flag = false;
    } else if (hasErrorMessage(inputPrefecture)) {
        flag = false;
    } else if (hasErrorMessage(inputCityTown)) {
        flag = false;
    } else if (hasErrorMessage(inputBuilding)) {
        flag = false;
    } else if (hasErrorMessage(inputTel)) {
        flag = false;
    } else if (hasErrorMessage(inputEmail)) {
        flag = false;
    } else if (inputFile1 !== null) {
        if (hasErrorMessage(inputFile1)) {
            flag = false;
        }
    } else if (inputFile2 !== null) {
        if (hasErrorMessage(inputFile2)) {
            flag = false;
        }
    }



    // 2.エラーメッセージを削除
    //removeElementsByClass("error");
    //removeClass("error-form");

    /*
    // 3.お名前の入力をチェック
    // 3-1.必須チェック
    if (document.data.name.value == "") {
        errorElement(document.data.name, "名前が入力されていません");
        flag = false;
    }

    // 4.ふりがなの入力をチェック
    // 4-1.必須チェック
    if (document.data.kana.value == "") {
        errorElement(document.data.kana, "ふりがなが入力されていません");
        flag = false;
    } else {
        // 4-2.ひらがなのチェック
        if (!validateKana(document.data.kana.value)) {
            errorElement(document.data.kana, "ひらがなを入れてください");
            flag = false;
        }
    }

    // 郵便番号
    if (document.data.postal_code.value === "") {
        errorElement(document.data.postal_code, "郵便番号が入力されていません");
        flag = false;
    } else if (!/^\d{3}-\d{4}$/.test(document.data.postal_code.value)) {
        errorElement(document.data.postal_code, "郵便番号が正しくありません");
        flag = false;
    }

    // 住所（都道府県、市区町村）
    if (document.data.prefecture.value === "" || document.data.city_town.value === "") {
        errorElement(document.data.building, "住所(都道府県もしくは市区町村・番地)が入力されていません");
        flag = false;
    }

    // 6.電話番号の入力をチェック
    // 6-1.必須チェック
    if (document.data.tel.value == "") {
        errorElement(document.data.tel, "電話番号が入力されていません");
        flag = false;
    } else {
        // 6-2.電話番号の長さをチェック
        if (!validateTel(document.data.tel.value)) {
            errorElement(document.data.tel, "電話番号は12~13桁で正しく入力してください");
            flag = false;
        }
    }

    // 5.メールアドレスの入力をチェック
    // 5-1.必須チェック
    if (document.data.email.value == "") {
        errorElement(document.data.email, "メールアドレスが入力されていません");
        flag = false;
    } //else {
    // 5-2.メールアドレスの形式をチェック
    //if (!validateMail(document.data.email.value)) {
    //    errorElement(document.data.email, "有効なメールアドレスを入力してください");
    //    flag = false;
    //}
    //}



    // document1 のチェック
    var fileInput1 = document.data.document1;
    if (fileInput1 && fileInput1.files.length > 0) {

        let next = fileInput1.nextSibling;
        while (next && next.nodeType === 3) { // テキストノードをスキップ
            next = next.nextSibling;
        }
        if (next && next.className === "error") {
            next.remove();
        }
        inputEmail.classList.remove("error-form");

        var file1 = fileInput1.files[0];
        var type1 = file1.type;
        // PNG もしくは JPEG 以外はエラー
        if (type1 !== "image/png" && type1 !== "image/jpeg") {
            errorElement(fileInput1, "ファイル形式は PNG または JPEG のみ許可されています");
            flag = false;
        }
    }
    // document2 のチェック
    var fileInput2 = document.data.document2;
    if (fileInput2 && fileInput2.files.length > 0) {

        let next = fileInput2.nextSibling;
        while (next && next.nodeType === 3) { // テキストノードをスキップ
            next = next.nextSibling;
        }
        if (next && next.className === "error") {
            next.remove();
        }
        inputEmail.classList.remove("error-form");
        var file2 = fileInput2.files[0];
        var type2 = file2.type;
        if (type2 !== "image/png" && type2 !== "image/jpeg") {
            errorElement(fileInput2, "ファイル形式は PNG または JPEG のみ許可されています");
            flag = false;
        }
    }
        */

    // 7.エラーチェック
    if (flag) {
        document.data.submit();
    }

    return false;
}


/**
 * エラーメッセージを表示する
 * @param {*} form メッセージを表示する項目
 * @param {*} msg 表示するエラーメッセージ
 */
var errorElement = function (form, msg) {

    // 1.項目タグに error-form のスタイルを適用させる
    form.className = "error-form";

    // 2.エラーメッセージの追加
    // 2-1.divタグの作成
    var newElement = document.createElement("div");

    // 2-2.error のスタイルを作成する
    newElement.className = "error";

    // 2-3.エラーメッセージのテキスト要素を作成する
    var newText = document.createTextNode(msg);

    // 2-4.2-1のdivタグに2-3のテキストを追加する
    newElement.appendChild(newText);

    // 2-5.項目タグの次の要素として、2-1のdivタグを追加する
    form.parentNode.insertBefore(newElement, form.nextSibling);
}


/**
 * エラーメッセージの削除
 *   className が、設定されている要素を全件取得し、タグごと削除する
 * @param {*} className 削除するスタイルのクラス名
 */
var removeElementsByClass = function (className) {

    // 1.html内から className の要素を全て取得する
    var elements = document.getElementsByClassName(className);
    while (elements.length > 0) {
        // 2.取得した全ての要素を削除する
        elements[0].parentNode.removeChild(elements[0]);
    }
}

/**
 * 適応スタイルの削除
 *   className を、要素から削除する
 *
 * @param {*} className
 */
var removeClass = function (className) {

    // 1.html内から className の要素を全て取得する
    var elements = document.getElementsByClassName(className);
    while (elements.length > 0) {
        // 2.取得した要素からclassName を削除する
        elements[0].className = "";
    }
}

/**
 * メールアドレスの書式チェック
 * @param {*} val チェックする文字列
 * @returns true：メールアドレス、false：メールアドレスではない
 */
var validateMail = function (val) {

    // メールアドレスの書式が以下であるか(*は、半角英数字と._-)
    // ***@***.***
    // ***.***@**.***
    // ***.***@**.**.***
    if (val.match(/@/) == null) {
        return false;
    } else {
        return true;
    }
}

/**
 * 電話番号のチェック
 * @param {*} val チェックする文字列
 * @returns true：電話番号、false：電話番号ではない
 */
var validateTel = function (val) {

    // 半角数値と-(ハイフン)のみであるか
    if (val.match(/^0[0-9]{1,4}-[0-9]{1,4}-[0-9]{3,4}$/) == null) {
        return false;
    } else {
        return true;
    }
}

/**
 * ひらがなのチェック
 * @param {*} val チェックする文字列
 * @returns true：ひらがなのみ、false：ひらがな以外の文字がある
 */
var validateKana = function (val) {

    // ひらがな(ぁ～ん)と長音のみであるか
    if (val.match(/^[ぁ-んー]+(?:[ 　]?[ぁ-んー]+)*$/) == null) {
        return false;
    } else {
        return true;
    }
}

function hasErrorMessage(input) {
    let next = input.nextSibling;
    while (next && next.nodeType === 3) { // テキストノードをスキップ
        next = next.nextSibling;
    }
    return next && next.className === "error";
}


function removeErrorMessage(input) {
    let next = input.nextSibling;
    while (next && next.nodeType === 3) { // テキストノードをスキップ
        next = next.nextSibling;
    }
    if (next && next.className === "error") {
        next.remove();
    }
}

function removeServerErrorMessage(input) {
    /*let next = input.nextSibling;
    while (next && next.nodeType === 3) { // テキストノードをスキップ
        next = next.nextSibling;
    }
    if (next && next.classList && next.classList.contains("error-msg")) {
        next.remove();
    }*/
    if (!input || !input.parentNode) return;
    var errors = input.parentNode.querySelectorAll('.error, .error-msg');
    errors.forEach(function (el) {
        el.remove();
    });
}

function hasAnyError() {
    // JSエラー
    if (document.getElementsByClassName('error').length > 0) { return true; }
    return false;
}

// ボタンの有効/無効を切り替える関数
function toggleConfirmButton() {
    const btn = document.getElementById('confirmBtn');
    btn.disabled = hasAnyError();
}
<?php

class Validator
{
    private $error_message = [];

    // 呼び出し元で使う
    public function validate($data, $master_data = [])
    {
        $this->error_message = [];

        // 名前
        if (empty($data['name'])) {
            $this->error_message['name'] = '名前が入力されていません';
        } elseif (mb_strlen($data['name']) > 20) {
            $this->error_message['name'] = '名前は20文字以内で入力してください';
        } elseif (!preg_match('/^[ぁ-んァ-ヶー一-龠]+(?:[ 　]?[ぁ-んァ-ヶー一-龠]+)*$/u', $data['name'])) {
            $this->error_message['name'] = '名前は日本語（常用漢字・ひらがな・カタカナ）で入力してください';
        }

        // ふりがな
        if (empty($data['kana'])) {
            $this->error_message['kana'] = 'ふりがなが入力されていません';
        } elseif (mb_strlen($data['kana']) > 20) {
            $this->error_message['kana'] = 'ふりがなは20文字以内で入力してください';
        } elseif (!preg_match('/[ぁ-んー]+(?:[ 　]?[ぁ-んー]+)*$/u', $data['kana'])) {
            $this->error_message['kana'] = 'ひらがなを入れてください';
        }


        // 生年月日
        if ($data['birth_date']) {
        } elseif (empty($data['birth_year']) || empty($data['birth_month']) || empty($data['birth_day'])) {
            $this->error_message['birth_date'] = '生年月日が入力されていません';
        } elseif (!$this->isValidDate($data['birth_year'] ?? '', $data['birth_month'] ?? '', $data['birth_day'] ?? '')) {
            $this->error_message['birth_date'] = '生年月日が正しくありません（存在しない日付です）';
        } elseif (!$this->isValidToday($data['birth_year'] ?? '', $data['birth_month'] ?? '', $data['birth_day'] ?? '')) {
            $this->error_message['birth_date'] = '生年月日が正しくありません（過去の日付を入力してください）';
        }

        // 郵便番号
        if (empty($data['postal_code'])) {
            $this->error_message['postal_code'] = '郵便番号が入力されていません';
        } elseif (!preg_match('/^[0-9]{3}-[0-9]{4}$/', $data['postal_code'] ?? '')) {
            $this->error_message['postal_code'] = '郵便番号が正しくありません';
        } elseif (empty($master_data)) {
            $this->error_message['postal_code'] = '郵便番号が存在しません';
        }

        // 住所
        if (empty($data['prefecture']) || empty($data['city_town'])) {
            $this->error_message['address'] = '住所(都道府県もしくは市区町村・番地)が入力されていません';
        } elseif (mb_strlen($data['prefecture']) > 5) {
            $this->error_message['address'] = '郵便番号と住所が一致しません';
        } elseif (mb_strlen($data['city_town']) > 50 || mb_strlen($data['building']) > 50) {
            $this->error_message['address'] = '市区町村・番地もしくは建物名は50文字以内で入力してください';
        } elseif ($this->isValidAddress($master_data ?? '', $data['prefecture'] ?? '', $data['city_town'] ?? '')) {
            $this->error_message['address'] = '郵便番号と住所が一致しません';
        }


        // 電話番号
        if (empty($data['tel'])) {
            $this->error_message['tel'] = '電話番号が入力されていません';
        } elseif (
            !preg_match('/^0\d{1,4}-\d{1,4}-\d{3,4}$/', $data['tel'] ?? '') ||
            mb_strlen($data['tel']) < 12 ||
            mb_strlen($data['tel']) > 13
        ) {
            $this->error_message['tel'] = '電話番号は12~13桁で正しく入力してください';
        }

        // メールアドレス
        if (empty($data['email'])) {
            $this->error_message['email'] = 'メールアドレスが入力されていません';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->error_message['email'] = '有効なメールアドレスを入力してください';
        }

        return empty($this->error_message);
    }


    // エラーメッセージ取得
    public function getErrors()
    {
        return $this->error_message;
    }

    // 生年月日の日付整合性チェック（存在）
    private function isValidDate($year, $month, $day)
    {
        return checkdate((int)$month, (int)$day, (int)$year);
    }

    // 生年月日の日付整合性チェック（未来日）
    private function isValidToday($year, $month, $day)
    {
        $today = date("Y/m/d");
        $target_day = "{$year}/{$month}/{$day}";
        return $today > $target_day;
    }

    // 郵便番号と住所の整合性チェック
    private function isValidAddress($master_data, $prefecture, $city_town)
    {
        // 郵便番号と住所の整合性チェックは、実際のデータベースを参照する必要があります。
        // ここでは簡易的なチェックを行います。

        //DBからデータを取り出す
        //UserAddressクラスの getMasterData()メソッドを実装して、郵便番号と住所のマスターデータを取得する。

        $prefecture_master = $master_data['prefecture'] ?? '';
        $city_master = $master_data['city'] ?? '';

        // $city_townは市区町村と番地を含む可能性があるため、簡易的にチェック(DBに登録されているデータの文字数)
        $city = mb_substr($city_town, 0, mb_strlen($city_master)); // 市区町村の先頭のみ抜き出す

        if ($prefecture == $prefecture_master && $city == $city_master) {
            return false; // 一致している場合はfalseを返す
        } else {
            return true; // 一致しない場合はtrueを返す
        }
    }
}

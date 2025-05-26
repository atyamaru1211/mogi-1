# coachtechフリマ

## 環境構築
**Dockerビルド**
1. `git clone git@github.com:atyamaru1211/mogi-1.git`
2. DockerDesktopアプリを立ち上げる
3. `docker-compose up -d --build`


**Laravel環境構築**
1. `docker-compose exec php bash`
2. `composer install`
3. 「.env.example」ファイルを 「.env」ファイルに命名を変更。または、新しく.envファイルを作成 `cp .env.example .env`
4. .envに以下の環境変数を追加
``` text
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass
```
5. .envに、以下のメール設定を追加
```
MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="test@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```
6. .envに、StripeのAPIキー設定を追加。pk_test_YOUR_STRIPE_PUBLISHABLE_KEYとsk_test_YOUR_STRIPE_SECRET_KEYに関しては[Stripe ダッシュボード](https://dashboard.stripe.com/developers/api_keys) から取得してください。
```
STRIPE_PUBLIC_KEY=pk_test_YOUR_STRIPE_PUBLISHABLE_KEY
STRIPE_SECRET_KEY=sk_test_YOUR_STRIPE_SECRET_KEY
```

7. アプリケーションキーの作成
``` bash
php artisan key:generate
```

8. マイグレーションの実行
``` bash
php artisan migrate
```

9. シーディングの実行
``` bash
php artisan db:seed
```

**テスト環境構築**
1. `docker-compose exec mysql bash`
2. `mysql -u root -p`　パスワードを要求されたら`root`
2. `CREATE DATABASE demo_test;`
3. mysqlコンテナを`exit`で出て、PHPコンテナに入り直す。`docker-compose exec php bash`
4. .envをコピーして.env.testingというファイルを作成。`cp .env .env.testing`
5. .env.testingに以下の環境変数を追加
``` text
APP_NAME=Laravel
APP_ENV=test
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=demo_test
DB_USERNAME=root
DB_PASSWORD=root
```

6. テスト用アプリケーションキーの作成
``` bash
php artisan key:generate --env=testing
```

7. マイグレーションの実行
``` bash
php artisan migrate --env=testing
```

8. シーディングの実行
``` bash
php artisan db:seed --env=testing
```

## 追加機能・変更点（機能要件との差異、運営からのフィードバックを含む）
本プロジェクトでは、基本の機能要件に加えて以下の変更・追加対応を行いました。

### デザイン・UI関連
* **デザイン要件のレスポンシブ対応:**
    * 全般的なレスポンシブ対応に加え、特に**商品購入画面のみUIを一部変更**しています。

### 機能・ロジック関連
* **メール認証機能の追加（応用要件）:**
    * 応用要件としてメール認証機能を実装しました。これにより、案件シートの基本設計書に**メール認証誘導画面 `auth/verify.blade.php` を追加**しています。
    * メール認証誘導画面の「認証はこちらから」ボタンは、**Mailhogへ遷移**するように設定しています。

* **バリデーションルールの変更・追加（運営からのフィードバック）:**
    * **会員登録（`RegisterRequest`）:**
        * ユーザー名（`name`）に「入力必須」および「20文字以内」のバリデーションルールを追加しました。
    * **プロフィール更新（`ProfileRequest`）:**
        * ユーザー名に「入力必須」および「20文字以内」のバリデーションルールを追加しました。
        * 郵便番号に「入力必須」および「ハイフンを含む8文字」のバリデーションルールを追加しました。
        * 住所に「入力必須」のバリデーションルールを追加しました。

* **機能要件との対応差異（運営からのフィードバック）:**
    * 機能要件の `FN006`ではなく、**テストケース一覧の `ID:1`（会員登録）の要件に合わせて実装**を進めました。
    * 各種パスは、基本設計書に合わせて作成しています。

* **Stripe決済処理の挙動（コンビニ決済におけるテスト環境の制約と合意事項）:**
    * Stripeによる商品購入処理において、**クレジットカード決済を選択した場合**は、Stripeの決済完了後に自動的に画面遷移が行われ、「Sold」と表示された商品詳細画面に移行し、商品購入処理が完了します。（**決済完了後の画面遷移については指定がなかったため、商品詳細画面に遷移されるように実装しております。**）
    * 一方、**コンビニ決済を選択した場合**は、Stripeの性質上、コンビニで支払いが完了して初めて購入処理が完了します。そのため、テスト環境での自動的な購入完了のシミュレーションが困難です。
    * この制約を踏まえ、コンビニ決済を選択した場合は、Stripeの決済完了画面まで遷移した後、**手動で `http://localhost/` のURLにアクセスすることで購入処理が完了する**よう実装しています。
    * この実装方針については、運営より「はい、ご認識の通りです。」との回答を得ており、コーチからも現在の実装で問題ない旨の確認をいただいております。

### テストケースの変更（応用要件を含む）
* **テストケース一覧 `ID:1`（会員登録）の変更:**
    * 応用要件の実装に伴い、テストケースの最下項を「**全ての項目が入力されている場合、メール認証誘導画面へ遷移、認証後ログイン画面に遷移される**」へ変更しました。

* **テストケース一覧 `ID:10`（商品購入機能）の変更:**
    * 応用要件の実装に伴い、テストケースの最上項を「**「購入する」ボタンを押下するとStripeの購入処理画面に遷移し、購入処理後、購入が完了する**」へ変更しました。


## 使用技術(実行環境)
- PHP 7.4.9
- Laravel8.83.29
- MySQL8.0.26

## ER図
![alt](erd.png)

## URL
- 開発環境：http://localhost/
- phpMyAdmin:：http://localhost:8080/

# freemarket-test

## 環境構築

### Docker ビルド
以下のコマンドをターミナルで実行してください：

```sh
git clone git@github.com:su-masayuki/freemarket-test.git
cd freemarket-test
docker-compose up -d --build
```

### Laravel 環境構築
```sh
docker-compose exec php bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan db:seed
```

## 使用技術（実行環境）

- PHP 8.2.11  
- Laravel 8.83.29  
- MySQL 8.0.26  
- Nginx 1.21.1  
- phpMyAdmin（phpmyadmin/phpmyadmin 最新）
- Mailtrap
- Stripe

## URL

- 商品一覧: http://localhost
- 会員登録: http://localhost/register
- ログイン: http://localhost/login
- phpMyAdmin: http://localhost:8080

## ER図

![ER図](er.drawio.png)

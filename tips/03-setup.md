# 設定專案

## 加入 Behat 支援

加入我開發的 [Behat Laravel Extension](https://github.com/jaceju/goez-behat-laravel-extension) ：

```bash
composer require goez/behat-laravel-extension --dev
```

測試看看 Behat 是否能正確初始化：

```bash
./vendor/bin/behat --init
```

應該會產生一個 `features` 資料夾，裡面會有一個包含 `FeatureContext.php` 檔案的 `bootstrap` 資料夾。如果確認沒問題就刪掉 `features` 資料夾，之後我們會再重建新的 context 檔：

```bash
rm -rf features
```

把 `.env` 複製成 `.env.behat` ：

```bash
cp .env .env.behat
```

編輯 `.env.behat` ，加入以下兩行：

```ini
DB_CONNECTION=sqlite
DB_SOURCE=:memory:
```

新增一個 `behat.yml` 檔，內容如下：

```yaml
default:
    extensions:
        Goez\BehatLaravelExtension:
        Behat\MinkExtension:
            default_session: laravel
            laravel: ~
    suites:
        bookshelf_features:
            paths:    [ %paths.base%/features/bookshelf ]
            contexts: [ BookshelfContext ]
        membership_features:
            paths:    [ %paths.base%/features/membership ]
            contexts: [ MembershipContext ]
```

編輯 `config/database.php` 檔，將 `sqlite` 中的 `database` 改用 `DB_SOURCE` ：

```diff
-            'database' => storage_path('database.sqlite'),
+            'database' => env('DB_SOURCE', storage_path('database.sqlite')),
```

## 新增 Context 類別

重新初始化 behat ，這時候會依照 `behat.yml` 的定義來建立 context 檔案：

```bash
./vendor/bin/behat --init
```

編輯 `features/bootstrap/BookshelfContext.php` ，讓 `BookshelfContext` 改為繼承 `LaravelContext` 類別：

```php
use Goez\BehatLaravelExtension\Context\LaravelContext;

class BookshelfContext extends LaravelContext
{
}
```

編輯 `features/bootstrap/MembershipContext.php` ，也是同樣的步驟：

```php
use Goez\BehatLaravelExtension\Context\LaravelContext;

class MembershipContext extends LaravelContext
{
}
```

下一步：[註冊功能](./04-register.md)。

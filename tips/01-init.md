# 專案初始化

先用 `laravel` 指令來建立一個新專案，專案名稱為 `bookshelf` ：

```
cd ~/Projects/tdd
laravel new bookshelf
```

因為直接執行 `./vendor/bin/phpunit` 會有問題，所以要重建 vendor ：

```bash
cd bookshelf
rm -rf vendor
composer install
```

下一步：[建立 prototype](./02-prototype.md) 。
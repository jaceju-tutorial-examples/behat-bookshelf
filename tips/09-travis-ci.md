# 整合 Travis CI

在 GitHub 上建立新專案，並將目前這個專案推到 repository 中。

到 `https//travis-ci.org` 用 GitHub 帳號註冊。

在 Travis CI 介面中連結新專案。

在專案根目錄新增 `.travis.yml` 檔，內容如下：

```yaml
language: php

php:
  - 5.5.9
  - 5.5
  - 5.6
  - 7.0
  - hhvm

sudo: false

install:
  - travis_retry composer self-update
  - travis_retry composer install --no-interaction --prefer-source

matrix:
  allow_failures:
    - php: hhvm
  fast_finish: true

script:
  - vendor/bin/phpunit
  - vendor/bin/behat

```

查看是否有正確執行 CI 建置。

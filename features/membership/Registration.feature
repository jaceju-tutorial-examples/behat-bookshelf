Feature: 使用者可以註冊帳號
    In order to 使用需要認證的系統
    As a 訪客
    I want to 註冊帳號

    Background:
        Given 帳號 "Taylor Otwell" "taylorotwell@example.com" 已註冊

    Scenario: 使用者註冊帳號成功
        When 註冊帳號 "Jace Ju" "jaceju@gmail.com"
        Then 登入系統
        And 導向首頁

    Scenario: 使用者註冊未輸入帳號及密碼
        When 註冊帳號 "" ""
        Then 頁面出現錯誤訊息 "請輸入帳號與密碼"

    Scenario: 使用者註冊已存在的帳號
        When 註冊帳號 "Taylor Otwell" "taylorotwell@example.com"
        Then 頁面出現錯誤訊息 "您所輸入的帳號已經有人申請"

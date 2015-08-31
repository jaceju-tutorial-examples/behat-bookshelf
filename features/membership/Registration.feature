Feature: 使用者可以註冊帳號
    In order to 使用需要認證的系統
    As a 訪客
    I want to 註冊帳號

    Scenario: 使用者註冊帳號成功
        When 註冊帳號 "Jace Ju" "jaceju@gmail.com"
        Then 登入系統
        And 導向首頁
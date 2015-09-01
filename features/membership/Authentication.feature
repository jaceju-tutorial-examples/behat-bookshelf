Feature: 使用者認證
    In order to 使用需要認證的系統
    As a 使用者
    I want to 輸入登錄資訊

    Scenario: 使用者登入系統，成功登入
        Given 帳號 "Jace Ju" "jaceju@example.com" 已註冊
        When 用帳號 "jaceju@example.com" 及密碼 "password" 登入系統
        Then 登入系統
        And 導向首頁

    Scenario: 使用者登入系統，輸入密碼錯誤
        Given 帳號 "Jace Ju" "jaceju@example.com" 已註冊
        When 用帳號 "jaceju@example.com" 及密碼 "PASSWORD" 登入系統
        Then 頁面出現錯誤訊息 "登入失敗，帳號或密碼錯誤"

    Scenario: 使用者登入系統，帳號不存在
        When 用帳號 "jaceju@example.com" 及密碼 "password" 登入系統
        Then 頁面出現錯誤訊息 "登入失敗，帳號或密碼錯誤"

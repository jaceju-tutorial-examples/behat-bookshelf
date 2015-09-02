Feature: 使用者可以借還書籍
    In order to 借還書籍
    As a 使用者
    I want to 查看書籍列表、借書及還書

    Background:
        Given 用帳號 "Jace Ju" "jaceju@gmail.com" 登入系統
        And 帳號 "Taylor Otwell" "taylorotwell@example.com" 已註冊
        And 帳號 "Jeffrey Way" "jeffreyway@example.com" 已註冊
        And 書架上現有書籍
            | 書籍名稱                | 出借狀況 |
            | 專案管理實務              | 可借出  |
            | HTML5 + CSS3 專用網站設計 | 可借出  |
            | JavaScript 學習手冊     | 可借出  |
            | 精通 VI               | 可借出  |
            | PHP 聖經              | 可借出  |
        And 書籍 "專案管理實務" 已被 "taylorotwell@example.com" 借出
        And 書籍 "精通 VI" 已被 "jeffreyway@example.com" 借出

    Scenario: 使用者可查看書籍列表及出借狀況
        When 進入首頁
        Then 顯示書籍清單、出借狀況
            | 書籍名稱                | 出借狀況 |
            | 專案管理實務              | 已借出  |
            | HTML5 + CSS3 專用網站設計 | 可借出  |
            | JavaScript 學習手冊     | 可借出  |
            | 精通 VI               | 已借出  |
            | PHP 聖經              | 可借出  |

    Scenario: 使用者借書
        Given 在列表的 "HTML5 + CSS3 專用網站設計"
        When 點選「借書」按鈕
        Then 出借狀況顯示 "已借出"
        And 顯示「還書」按鈕
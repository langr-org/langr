内含一个sql包，需要安装。

開發架構目錄結構
專案目錄名稱
　|_ auto
　|_ data
　|_ html
　|_ images
　|_ include
　　　|_ config
　　　|_ css
　　　|_ public
　　　|_ tools
　　　|_ function
　　　|_ javascript
　　　|_ mail
　　　|_ php
　|_ lib
　|_ php
　|_ tmpl
　|_ tmplCache


auto 
　　自動定時執行程式，PHP SHELL 腳本配合 Linux crontab 命令。 

data 
　　資料目錄，保存upload上傳的資料等。 

html 
　　靜態網頁緩存目錄。 

images 
　　圖片目錄。 

include 
　　公共程式文件目錄。
　　config：配置文件目錄
　　css：網頁樣式表文件目錄
　　public：公共類文件目錄
　　tools：工具類文件目錄
　　function：函數文件目錄
　　javascript：js文件目錄
　　mail：郵件文件目錄
　　php：公共的小程式目錄


lib 
　　核心庫目錄。 

tmpl 
　　模板目錄。 

tmplCache 
　　模板編譯緩存目錄。 

名詞解釋
專案：多個模組的組合，實現的一個完整功能的程式系統（例如：８５９１專案、台灣論壇專案）。 
模組：功能獨立但又同屬於一個專案，每個模組單入口。（例如：前台模組、後台模組、經銷商模組）。 
Module：相同屬性功能的集合，（例如：user）。 
Action：實現單一功能的事件。 
Action前綴：GET方式調用的Action前綴為Show，POST方式調用的Action前綴為Do 
子模板：模板中的二級模板，（例如：網頁中的某列表的多行迴圈體部分） 
框架結構特點
模組單入口，方便管理和控制； 
功能集成化，方便重復使用，減少開發時間； 
模板編譯，模板技術將邏輯層和表現層分離，方便美工與程式協作，自動編譯加快模板處理速度； 
開發編碼與顯示編碼無關性，開發編碼ＧＢＫ，顯示編碼ＵＴＦ－８ 

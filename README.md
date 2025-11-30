# 線上單字集

這是一個基於 Laravel 與 Nuxt 架構所打造的前後端分離的線上單字集系統，支援基本的使用者管理、單字集建立、檢視及修改，並可透過 Web Speech 與 Google TTS 實現語音播放功能。

## 功能簡介

- 使用者註冊 / 登入 / 登出 / 修改密碼
- 單字集 CRUD（私人 / 公開）
- 單字 / 單字集的列表顯示與分頁
- 單字播放功能
  - （預設）Web Speech API
  - （可選）Google Cloud Text-to-Speech
  - 支援語音語言選擇與語速調整

## 使用技術 

- 後端：Laravel 12、Laravel Sanctum
- 前端：Nuxt 4、Bootstrap 5
- TTS：Web Speech API、Google Cloud TTS

## 環境建置

在開始建置前，請先安裝以下軟體與工具：
- php（8.2 - 8.4）
- Node.js（≥ 20.x LTS）
- Composer

### 1. 下載專案

```bash
git clone https://github.com/frank900917/flash-card.git
cd flash-card
```

### 2. 後端建置（Laravel）

```bash
cd backend
cp .env.example .env
composer install
php artisan key:generate
```

編輯 `.env` 檔案，設定以下項目：

```env
# 時區設定（範例）
APP_TIMEZONE=Asia/Taipei

# 資料庫設定（範例）
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Google TTS 設定（可選）
GOOGLE_TTS=false
GOOGLE_APPLICATION_CREDENTIALS=完整憑證路徑/key.json
```

建立資料表
```bash
php artisan migrate
```

填充假資料（可選）
```bash
php artisan db:seed
```

啟動開發伺服器
```bash
php artisan serve
```

### 3. 前端建置（Nuxt）

```bash
cd frontend
npm install
```

啟動開發伺服器
```bash
npm run dev
```

### 使用 Google TTS 功能（可選）

若欲啟用 Google Text-to-Speech：
1. 前往 [Google Cloud Console](https://console.cloud.google.com/) 開啟 Text-to-Speech API。
2. 建立服務帳戶，前往金鑰頁面建立新的金鑰。
3. 將下載的服務帳戶金鑰保存在適當位置，並於 `backend\.env` 啟用 Google TTS 並設定完整憑證保存路徑。

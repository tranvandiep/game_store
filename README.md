# My Kiddy Game Store 🎮

Kho ứng dụng và phân phối các gói tài nguyên **Trò chơi Giáo dục (Web Games)** & **Thẻ học Tương tác (Flashcards)** chính thức cho dự án **My Kiddy**.

---

## 🌟 Giới Thiệu (About)

- **Nền tảng công nghệ:** Toàn bộ trò chơi và bộ thẻ học trong kho được xây dựng trên nền tảng công nghệ **ReactJS + TypeScript**, tích hợp kiến trúc Clean MVC, âm thanh Web Audio API chất lượng cao và hệ thống đa ngôn ngữ độc lập.
- **Cơ chế vận hành:** Các gói tài nguyên được đóng gói thành file ZIP chuẩn nén đơn tệp (`Singlefile HTML5 Bundle`). Ứng dụng **My Kiddy** trên thiết bị của bé sẽ tự động đồng bộ qua Manifest, tải về và chạy hoàn toàn Offline mượt mà 60 FPS trong môi trường cô lập bảo mật cao (`SafeWebEngine`).

---

## 🕹️ Chơi Thử Trực Tiếp Trên Web (Live Web Play Zone)

Người chơi và kiểm thử viên có thể trải nghiệm trực tiếp toàn bộ các Game và Flashcard trên trình duyệt thông qua GitHub Pages:

- 🌐 **Web Portal Trực Tuyến**: [https://tranvandiep.github.io/game_store/](https://tranvandiep.github.io/game_store/)
- 🐔 **Bắt Gà Tinh Nghịch (Game)**: [https://tranvandiep.github.io/game_store/online/games/catch_chicken/](https://tranvandiep.github.io/game_store/online/games/catch_chicken/)

---

## 📱 Tải Ứng Dụng My Kiddy

Ứng dụng quản lý thời gian, giáo dục và giải trí an toàn dành cho trẻ em và gia đình:

- 🤖 **Android (Google Play Store)**: [https://play.google.com/store/apps/details?id=com.gozic.mykiddy](https://play.google.com/store/apps/details?id=com.gozic.mykiddy)
- 🍏 **iOS (Apple App Store)**: [https://apps.apple.com/us/app/my-kiddy/id6801847363](https://apps.apple.com/us/app/my-kiddy/id6801847363)

---

## 📋 Danh Sách Gói Tài Nguyên (Web Content Packages)

| Tên Gói | Loại | Mã Code | Phiên Bản | Link Tải Trực Tiếp (Raw Zip) | Chơi Trực Tuyến |
| :--- | :---: | :---: | :---: | :--- | :---: |
| **Bắt Gà Tinh Nghịch** | Game | `CATCH_CHICKEN` | `v6` | [CATCH_CHICKEN.zip](https://raw.githubusercontent.com/tranvandiep/game_store/main/games/CATCH_CHICKEN.zip) | [Chơi Ngay ➔](https://tranvandiep.github.io/game_store/online/games/catch_chicken/) |

---

## 🌐 Manifest Tự Động (Manifest URL)

Ứng dụng **My Kiddy** sử dụng đường dẫn sau để tự động kiểm tra và đồng bộ danh sách gói mới nhất:
```text
https://raw.githubusercontent.com/tranvandiep/game_store/main/web_games_manifest.json
```

---

## 🏛️ Cấu Trúc Thư Mục Kho Lưu Trữ

```text
game_store/
├── index.html                   # Trang Web Portal chọn và chơi game trực tuyến (GitHub Pages)
├── README.md                    # Tài liệu giới thiệu kho ứng dụng Game & Flashcard
├── web_games_manifest.json      # File danh mục và phiên bản cập nhật tự động
├── online/                      # Kho chơi thử trực tiếp trên Web
│   ├── index.html               # Trang Web Portal subfolder
│   ├── games/                   # Singlefile HTML của từng Game
│   │   └── catch_chicken/
│   │       └── index.html
│   └── flashcards/              # Singlefile HTML của từng Flashcard
├── games/                       # Chứa các gói zip game nén cho App Flutter
│   └── CATCH_CHICKEN.zip
├── flashcards/                  # Chứa các gói zip flashcard nén cho App Flutter
└── thumbnails/                  # Chứa ảnh minh họa đại diện
    └── catch_chicken.jpg
```

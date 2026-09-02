# My Kiddy Game Store 🎮

Kho phân phối và lưu trữ các gói tài nguyên HTML5 Web Games & Flashcards chính thức cho hệ sinh thái ứng dụng **My Kiddy**.

---

## 📱 Tải Ứng Dụng My Kiddy

Ứng dụng quản lý thời gian, giáo dục và giải trí an toàn dành cho trẻ em và gia đình:

- 🤖 **Android (Google Play Store)**: [https://play.google.com/store/apps/details?id=com.gozic.mykiddy](https://play.google.com/store/apps/details?id=com.gozic.mykiddy)
- 🍏 **iOS (Apple App Store)**: [https://apps.apple.com/us/app/my-kiddy/id6801847363](https://apps.apple.com/us/app/my-kiddy/id6801847363)

---

## 📋 Danh Sách Gói Tài Nguyên (Web Content Packages)

| Tên Gói | Loại | Mã Code | Phiên Bản | Link Tải Trực Tiếp (Raw Zip) |
| :--- | :---: | :---: | :---: | :--- |
| **Bắt Gà Tinh Nghịch** | Game | `CATCH_CHICKEN` | `v5` | [CATCH_CHICKEN.zip](https://raw.githubusercontent.com/tranvandiep/game_store/main/games/CATCH_CHICKEN.zip) |
| **Phép Cộng Vui Nhộn** | Flashcard | `MATH_ADDITION` | `v6` | [MATH_ADDITION.zip](https://raw.githubusercontent.com/tranvandiep/game_store/main/flashcards/MATH_ADDITION.zip) |

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
├── README.md                    # Tài liệu danh mục kho game
├── web_games_manifest.json      # File danh mục và phiên bản cập nhật tự động
├── games/                       # Chứa các gói zip game
│   └── CATCH_CHICKEN.zip
├── flashcards/                  # Chứa các gói zip flashcard
│   └── MATH_ADDITION.zip
└── thumbnails/                  # Chứa ảnh minh họa đại diện
    ├── catch_chicken.jpg
    └── math_addition.jpg
```

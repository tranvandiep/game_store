# My Kiddy Game Store 🎮

Kho phân phối và lưu trữ các gói tài nguyên HTML5 Web Games & Flashcards chính thức cho ứng dụng **My Kiddy**.

---

## 📋 Danh Sách Gói Tài Nguyên (Web Content Packages)

| Tên Gói | Loại | Mã Code | Phiên Bản | Link Tải Trực Tiếp (Raw Zip) |
| :--- | :---: | :---: | :---: | :--- |
| **Bắt Gà Tinh Nghịch** | Game | `CATCH_CHICKEN` | `v1` | [CATCH_CHICKEN_v1.zip](https://raw.githubusercontent.com/tranvandiep/game_store/main/games/CATCH_CHICKEN_v1.zip) |
| **Phép Cộng Vui Nhộn** | Flashcard | `MATH_ADDITION` | `v1` | [MATH_ADDITION_v1.zip](https://raw.githubusercontent.com/tranvandiep/game_store/main/flashcards/MATH_ADDITION_v1.zip) |

---

## 🌐 Manifest Tự Động (Manifest URL)

Ứng dụng **My Kiddy** sử dụng đường dẫn sau để tải danh sách gói mới nhất:
```
https://raw.githubusercontent.com/tranvandiep/game_store/main/web_games_manifest.json
```

---

## 🏛️ Cấu Trúc Thư Mục Kho Lưu Trữ

```text
game_store/
├── web_games_manifest.json    # File danh mục và phiên bản cập nhật tự động
├── games/                     # Chứa các gói zip game
│   └── CATCH_CHICKEN_v1.zip
└── flashcards/                # Chứa các gói zip flashcard
    └── MATH_ADDITION_v1.zip
```

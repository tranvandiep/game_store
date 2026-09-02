# My Kiddy Game Store 🎮

Kho phân phối và lưu trữ các gói tài nguyên HTML5 Web Games & Flashcards chính thức cho ứng dụng **My Kiddy**.

---

## 📋 Danh Sách Gói Tài Nguyên (Web Content Packages)

| Tên Gói | Loại | Mã Code | Phiên Bản | Link Tải Trực Tiếp (Raw Zip) |
| :--- | :---: | :---: | :---: | :--- |
| **Bắt Gà Tinh Nghịch** | Game | `CATCH_CHICKEN` | `v3` | [CATCH_CHICKEN.zip](https://raw.githubusercontent.com/tranvandiep/game_store/main/games/CATCH_CHICKEN.zip) |
| **Phép Cộng Vui Nhộn** | Flashcard | `MATH_ADDITION` | `v3` | [MATH_ADDITION.zip](https://raw.githubusercontent.com/tranvandiep/game_store/main/flashcards/MATH_ADDITION.zip) |

---

## 🌐 Manifest Tự Động (Manifest URL)

Ứng dụng **My Kiddy** sử dụng đường dẫn sau để tải danh sách gói mới nhất:
```text
https://raw.githubusercontent.com/tranvandiep/game_store/main/web_games_manifest.json
```

---

## 🏛️ Cấu Trúc Thư Mục Kho Lưu Trữ

```text
game_store/
├── web_games_manifest.json      # File danh mục và phiên bản cập nhật tự động
├── games/                       # Chứa các gói zip game
│   └── CATCH_CHICKEN.zip
├── flashcards/                  # Chứa các gói zip flashcard
│   └── MATH_ADDITION.zip
└── thumbnails/                  # Chứa ảnh minh họa đại diện
    ├── catch_chicken.jpg
    └── math_addition.jpg
```

---

## ⚙️ Quy Trình Đóng Gói & Xuất Bản (Build & Packaging Workflow)

Hệ thống cung cấp script tự động hóa hoàn toàn quy trình đóng gói qua công cụ `scripts/build_and_deploy.js`:

### 🚀 Lệnh đóng gói tất cả (Deploy All):
```bash
npm run deploy:all
```
**Các bước script tự động thực hiện:**
1. Duyệt qua tất cả các thư mục trong `src/features/`.
2. Đọc file metadata `game_config.json` hoặc `flashcard_config.json`.
3. Sử dụng Vite Singlefile để biên dịch toàn bộ HTML, CSS, JS, Assets thành 1 file `index.html` duy nhất (Zero external file dependencies).
4. Nén file `index.html` thành file ZIP chuẩn tại `deploys/games/{CODE}.zip` hoặc `deploys/flashcards/{CODE}.zip`.
5. Tự động cập nhật file `deploys/web_games_manifest.json`.
6. Tự động đồng bộ `web_games_manifest.json` sang thư mục Flutter App: `assets/config/web_games_manifest.json`.

### 🎯 Lệnh đóng gói riêng 1 game cụ thể:
```bash
npm run deploy:game -- catch_chicken
# hoặc
npm run deploy:game -- math_addition
```

---

## 📋 Chi Tiết Định Dạng `web_games_manifest.json`

Mỗi phần tử trong mảng manifest đại diện cho 1 Game hoặc Flashcard với các trường thông tin:

```json
{
  "id": "game_catch_chicken",
  "type": "game",
  "code": "CATCH_CHICKEN",
  "version": 2,
  "name": "Bắt Gà Tinh Nghịch",
  "description": "Nhanh tay chạm vào những chú gà đang chạy trốn để ghi điểm!",
  "thumbnail": "https://raw.githubusercontent.com/tranvandiep/game_store/main/thumbnails/catch_chicken.jpg",
  "url": "https://raw.githubusercontent.com/tranvandiep/game_store/main/games/CATCH_CHICKEN.zip",
  "category": "Action & Strategy",
  "backgroundColor": "#FEF3C7",
  "ages": [3, 4, 5, 6, 7],
  "status": "active",
  "tiviStatus": "active"
}
```

### Chi tiết các trường dữ liệu:
| Trường | Kiểu dữ liệu | Ý nghĩa |
| :--- | :--- | :--- |
| `id` | `String` | Định danh duy nhất của mục (`game_{name}` hoặc `flashcard_{name}`) |
| `type` | `'game' \| 'flashcard'` | Phân loại nội dung |
| `code` | `String` | Mã nhận diện viết hoa không dấu (Ví dụ: `CATCH_CHICKEN`, `MATH_ADDITION`) |
| `version` | `Number` | Phiên bản hiện tại (App sẽ tự động tải bản mới khi version tăng) |
| `name` | `String` | Tên hiển thị của trò chơi / bộ thẻ |
| `description` | `String` | Mô tả ngắn gọn nội dung |
| `thumbnail` | `String (URL)` | Đường dẫn ảnh đại diện (CDN hoặc GitHub Raw Store) |
| `url` | `String (URL)` | Đường dẫn tải file zip gói trò chơi |
| `category` | `String` | Danh mục / Thể loại |
| `backgroundColor` | `String (Hex)` | Màu nền nhận diện của game khi mở trong SafeWebEngine |
| `ages` | `Array<Number>` | Độ tuổi phù hợp (dùng cho bộ lọc và gợi ý nội dung) |
| `status` | `'active' \| 'inactive'` | Trạng thái hiển thị trên thiết bị di động |
| `tiviStatus` | `'active' \| 'inactive'` | Trạng thái hiển thị trên Android TV Mode |

---

## 🌐 Đồng Bộ Lên Kho Game Store (GitHub / CDN)

Khi các gói zip mới được tạo ra trong thư mục này:
1. Đẩy các file zip trong `games/`, `flashcards/` và ảnh `thumbnails/` lên repository kho game (`https://github.com/tranvandiep/game_store`).
2. Ứng dụng **My Kiddy** trên thiết bị của bé sẽ tự động kiểm tra version trong manifest, tải gói ZIP về và giải nén chạy offline an toàn 100% qua `SafeWebEnginePage`.

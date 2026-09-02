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
| **Bắt Gà Tinh Nghịch** | Game | `CATCH_CHICKEN` | `v4` | [CATCH_CHICKEN.zip](https://raw.githubusercontent.com/tranvandiep/game_store/main/games/CATCH_CHICKEN.zip) |
| **Phép Cộng Vui Nhộn** | Flashcard | `MATH_ADDITION` | `v4` | [MATH_ADDITION.zip](https://raw.githubusercontent.com/tranvandiep/game_store/main/flashcards/MATH_ADDITION.zip) |

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
├── README.md                    # Tài liệu giới thiệu kho game & liên hệ hợp tác
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

## 🤝 Liên Hệ Nhận Game SDK & Hợp Tác Phát Triển

Bạn là nhà phát triển (Game Developer), nhà sáng tạo nội dung giáo dục hoặc studio muốn đưa trò chơi, thẻ học tương tác lên hệ sinh thái **My Kiddy**?

Chúng tôi cung cấp **My Kiddy Game SDK Workspace** (React + TypeScript + Vite + Singlefile Archiver) với các tính năng chuẩn hóa:
- Khung kiến trúc **MVC Clean Architecture**.
- Hệ thống đa ngôn ngữ độc lập (**i18n**).
- **Sound Manager** tích hợp Web Audio API chất lượng cao.
- **MyKiddyBridge** giao tiếp 2 chiều với Native App (điểm số, thời gian, sự kiện, theme color).
- Script đóng gói **Auto-Packaging 1 click** ra file zip và manifest chuẩn.

📩 **Liên hệ nhận Game SDK & hỗ trợ kỹ thuật:**
- **Email:** [tranvandiep.it88@gmail.com](mailto:tranvandiep.it88@gmail.com)
- **Website:** [https://mykiddy.net](https://mykiddy.net)

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
  "version": 3,
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

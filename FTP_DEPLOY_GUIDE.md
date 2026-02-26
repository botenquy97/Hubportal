# Hướng Dẫn Cấu Hình Tự Động Publish Lên cPanel (Auto-Deploy)

Tuyệt vời vì bạn đã kết nối CSDL thành công! Thay vì mỗi lần sửa code phải nén `.zip` rồi tự Upload bằng tay lên File Manager, bạn hoàn toàn có thể cấu hình **Visual Studio Code** tự động đẩy file thẳng lên cPanel mỗi khi bạn bấm Lưu (Save).

Chúng ta sẽ sử dụng giao thức **FTP** (File Transfer Protocol), đã có sẵn trên mọi gói hosting cPanel.

## Bước 1: Tạo tài khoản FTP trên cPanel
Để bảo mật, chúng ta sẽ tạo một tài khoản FTP chỉ có quyền truy cập vào thư mục web của bạn.
1. Đăng nhập vào cPanel.
2. Tìm đến phần **Files** và nhấp vào biểu tượng **"FTP Accounts"**.
3. Tại phần *Add FTP Account*, điền:
   - **Log in (Tên đăng nhập):** Nhập `deploy` (Nó sẽ tự gắn thêm `@ten-mien-cua-ban.com`).
   - **Password (Mật khẩu):** Tạo một mật khẩu mạnh và lưu lại.
   - **Directory (Thư mục):** Sửa lại đường dẫn thành đúng thư mục `public_html` của trang web bạn.
4. Nhấn nút **"Create FTP Account"**.

## Bước 2: Cài Tiện Ích (Extension) trong VS Code
1. Mở VS Code trên máy tính của bạn.
2. Ở cột bên trái, bấm vào biểu tượng các ô vuông **Extensions** (Phím tắt `Ctrl + Shift + X`).
3. Gõ tìm kiếm từ khóa `SFTP` (Của nhà phát triển *Natizyskunk*).
4. Nhấn nút **Install** để cài đặt.

## Bước 3: Cấu Hình Kết Nối FTP 
1. Quay lại file đang mở ở dự án Web Hub, nhấn tổ hợp phím `Ctrl + Shift + P` để hiện bảng lệnh.
2. Gõ chữ `SFTP: Config` và Enter.
3. VS Code sẽ tự động sinh ra một file tên là `.vscode/sftp.json`. Bạn hãy sửa nội dung file đó thành như sau:

```json
{
    "name": "Web Hub cPanel",
    "host": "ten-mien-cua-ban.com",
    "protocol": "ftp",
    "port": 21,
    "username": "deploy@ten-mien-cua-ban.com",
    "password": "mật_khẩu_ftp_bạn_vừa_tạo",
    "remotePath": "/",
    "uploadOnSave": true,
    "ignore": [
        ".vscode",
        ".git",
        ".DS_Store",
        "*.md",
        "*.sql",
        "web-hub-source.zip"
    ]
}
```

> **Lưu ý quan trọng:**
> - Nhớ thay `host`, `username`, và `password` thành đúng thông tin của bạn.
> - `remotePath` là `/` vì khi tạo tài khoản FTP ở Bước 1, bạn đã ép tài khoản đó chỉ vào được `public_html` rồi.
> - `uploadOnSave: true` chính là sức mạnh tự động: **Mọi thứ bạn "Lưu" sẽ bay thẳng lên hosting.**

## Bước 4: Thử Nghiệm Tự Động Publish!
1. Bây giờ, bạn hãy mở file `index.html` lên.
2. Sửa thử một chữ bất kỳ (ví dụ: dòng chữ *Chào buổi sáng* đổi thành *Chào bạn!*)
3. Bấm **Lưu (`Ctrl + S`)**.
4. Nhìn xuống khu vực dưới cùng góc phải của VS Code, bạn sẽ thấy nó hiện chữ `"Uploading..."` chạy rất nhanh.
5. Cập nhật lại trang Web (F5) trên trình duyệt, dòng chữ mới sẽ được cập nhật ngay lập tức!

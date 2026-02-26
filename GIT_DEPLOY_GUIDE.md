# Hướng Dẫn Deploy (Publish) Code Tự Động Lên cPanel Bằng Git

Hoàn toàn được! Sử dụng Git là phương án hiện đại, chuyên nghiệp nhất và dễ dàng quản lý phiên bản (Version Control) khi bạn code.

Hầu hết các phiên bản cPanel đời mới hiện nay đều có tích hợp sẵn công cụ **"Git Version Control"**. Dưới đây là 2 cách phổ biến để dùng Git với cPanel:

---

## Cách 1: Setup Github Actions (Được khuyên dùng nhất ⭐)
Tự động 100%. Mỗi khi bạn dùng lệnh `git push` đoạn code từ VS Code lên Github, Github sẽ tự động lấy code đó "bắn" sang cPanel của bạn vào thư mục Web qua giao thức FTP/SFTP.

**Ưu điểm:** Có ngay một bản lưu trên Github (tránh mất mã nguồn), triển khai chuyên nghiệp chẳng khác gì kỹ sư hệ thống.

### Bước 1: Chuẩn bị Github
1. Gõ `git init` trong terminal VS Code.
2. Commit toàn bộ file lên: `git add .` rồi `git commit -m "Init"`.
3. Tạo một Repo trên Github.com và Push code lên Repo đó.

### Bước 2: Tạo File cấu hình Github Action 
Trong thư mục dự án VS Code, bạn tạo 1 thư mục `.github` -> bên trong lại tạo mục `workflows` -> tạo 1 file đặt tên là `deploy.yml`. 
(Nó sẽ có cấu trúc: `.github/workflows/deploy.yml`)

Copy đoạn mã sau dán vào file đó (Đoạn này giúp tự động upload file qua FTP):

```yml
name: Deploy Web Hub to cPanel

on:
  push:
    branches:
      - main # Khi nhánh main được push code mới 

jobs:
  web-deploy:
    runs-on: ubuntu-latest
    steps:
    - name: Lấy mã nguồn mới nhất
      uses: actions/checkout@v3
    
    - name: Đồng bộ hoá thư mục lên cPanel
      uses: SamKirkland/FTP-Deploy-Action@v4.3.4
      with:
        server: ${{ secrets.FTP_SERVER }}
        username: ${{ secrets.FTP_USERNAME }}
        password: ${{ secrets.FTP_PASSWORD }}
        server-dir: public_html/
```

### Bước 3: Đặt Bảo mật Thông tin (Secrets)
Cái hay của GitHub Actions là bạn không phải lộ Password FTP trong mã nguồn. 
1. Truy cập Repo Github của bạn trên trình duyệt -> Chuyển sang thẻ **Settings**.
2. Tìm tay trái: **Secrets and variables** -> **Actions**.
3. Thêm 3 biến môi trường (Lấy thông tin tài khoản FTP bạn tạo trên cPanel):
   - Name: `FTP_SERVER`, Value: *Tên miền hoặc IP Web của bạn*
   - Name: `FTP_USERNAME`, Value: *Tên user FTP (vd: deploy@...)*
   - Name: `FTP_PASSWORD`, Value: *Mật khẩu FTP*

Xong! Từ giờ mỗi khi bạn sửa code và gõ lệnh `git push` ở máy cục bộ, GitHub sẽ chạy tự động mất khoảng 10 giây và đẩy thẳng code lên cPanel. Bạn bấm F5 trang web sẽ thấy sự cập nhật!

---

## Cách 2: Dùng Git Trực Tiếp Trên cPanel 
Dùng cách này sẽ kéo rào cản từ việc push gián tiếp qua Github, thay vào đó bạn Push thẳng code từ máy tính lên Server cPanel.

**Nhược điểm:** Bạn phải cấu hình kết nối SSH (rất rườm rà) cho Windows và nhập SSH keys trên cPanel.

1. Đăng nhập cPanel, tìm biểu tượng **Git Version Control**.
2. Bấm `Create` -> Tắt chế độ `Clone Repository` -> Gõ tên thư mục lưu git trên máy chủ. (Lưu ý: Git rỗng trên Server sẽ lưu riêng, Web trên Server sẽ là thư mục `public_html` riêng bộ).
3. cPanel sẽ cho bạn một đường Link SSH dạng `ssh://username@domain...`.
4. Mở VS Code -> Thêm remote này vào máy: `git remote add cpanel ssh://...`
5. Khi Update bạn dùng lệnh `git push cpanel master`.
6. **Deploy Update**: Bạn phải vào lại cPanel, bấm vào Repository đó, nhảy sang mục `Pull or Deploy` và bấm nút Publish thì code mới "chạy" từ thư mục Git vào thư mục `public_html`.

> 👉 **Kết Luận:** Cách 1 dùng *Github Actions* là chân ái của lập trình viên hiện nay. Bạn chỉ cần Setup 1 lần duy nhất, sau đó cứ nhàn nhã code và Push thôi. 

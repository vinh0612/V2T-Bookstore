# V2T Bookstore

Ứng dụng quản lý và bán sách trực tuyến được xây dựng bằng Laravel framework.

## 🔧 Yêu cầu hệ thống

- **PHP**: >= 8.1
- **Composer**: ~2.0
- **Node.js**: >= 14.0
- **NPM**: >= 6.0
- **MySQL/MariaDB**: >= 5.7
- **Git**: Để clone repository

## 📥 Cài đặt

### 1. Clone repository
```bash
git clone https://github.com/vinh0612/V2T-Bookstore.git
cd V2T-Bookstore
```

### 2. Cài đặt PHP dependencies
```bash
composer install
```

### 3. Cài đặt Node.js dependencies
```bash
npm install
```

### 4. Tạo file .env
```bash
cp .env.example .env
```

### 5. Sinh application key
```bash
php artisan key:generate
```

### 6. Cấu hình database
Mở file `.env` và cập nhật thông tin database:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=v2t_bookstore
DB_USERNAME=root
DB_PASSWORD=
```

### 7. Chạy migration
```bash
php artisan migrate
```

### 8. (Tùy chọn) Seed database
```bash
php artisan db:seed
```

## ▶️ Chạy ứng dụng

### Cách 1: Sử dụng PHP built-in server
```bash
php artisan serve
```
Truy cập: http://localhost:8000

### Cách 2: Sử dụng Artisan (Port tùy chỉnh)
```bash
php artisan serve --host=0.0.0.0 --port=8000
```

### Chạy Asset Compilation
Mở terminal khác và chạy:
```bash
npm run dev
```

Hoặc để build cho production:
```bash
npm run build
```

## 🛠️ Các lệnh quan trọng

### Database
```bash
# Tạo bảng database
php artisan migrate

# Rollback migration gần nhất
php artisan migrate:rollback

# Rollback tất cả migrations
php artisan migrate:reset

# Rollback rồi migrate lại
php artisan migrate:refresh

# Rollback, migrate, và seed
php artisan migrate:refresh --seed
```

### Cache
```bash
# Xóa cache
php artisan cache:clear

# Xóa config cache
php artisan config:clear

# Xóa route cache
php artisan route:clear

# Xóa view cache
php artisan view:clear
```

### Development
```bash
# Xóa tất cả cache
php artisan optimize:clear

# Tạo link symbolic cho storage
php artisan storage:link

# Tạo file mẫu
php artisan make:model <ModelName>
php artisan make:controller <ControllerName>
php artisan make:migration <migration_name>
```

## 📁 Cấu trúc thư mục

```
V2T-Bookstore/
├── app/                 # Mã ứng dụng (Models, Controllers, Requests)
├── bootstrap/           # File khởi động
├── config/              # File cấu hình
├── database/            # Migrations và Seeders
├── public/              # Public assets (CSS, JS, images)
├── resources/           # Views (Blade templates), CSS, JS
├── routes/              # Định tuyến ứng dụng
├── storage/             # Lưu trữ files
├── tests/               # Unit tests
└── .env.example         # File cấu hình mẫu
```

## 🚀 Deployment

### Chuẩn bị cho Production
```bash
# Cài đặt dependencies
composer install --no-dev

# Build assets
npm run build

# Cache config
php artisan config:cache

# Cache routes
php artisan route:cache

# Cache views
php artisan view:cache
```

### Cấu hình Web Server (Nginx)
```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /path/to/V2T-Bookstore/public;

    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

## 🧪 Testing

```bash
# Chạy unit tests
php artisan test

# Chạy với code coverage
php artisan test --coverage
```

## 📝 Troubleshooting

### Lỗi: "No application encryption key has been generated"
```bash
php artisan key:generate
```

### Lỗi: "Migrations don't exist"
```bash
php artisan migrate --path=database/migrations
```

### Lỗi: Permission Denied trên storage
```bash
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### Lỗi: npm dependencies
```bash
rm -rf node_modules package-lock.json
npm install
```

## 📚 Tài liệu tham khảo

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Blade Templates](https://laravel.com/docs/blade)
- [Eloquent ORM](https://laravel.com/docs/eloquent)

## 📄 License

This project is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 👤 Author

- **Vinh0612** - Initial work

---

**Lưu ý**: Đảm bảo rằng bạn đã cấu hình đúng database trước khi chạy migrations!
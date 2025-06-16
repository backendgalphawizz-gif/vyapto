# 📚 Learner and Employee Management System (LEMS)

**LEMS** is a Laravel-based web platform that simplifies school administrative tasks through QR code attendance, role-based access, OTP-secured email registration, and targeted email announcements — all in a clean, responsive user interface.

> "Build systems not just for grades, but for real-world impact."

---

## 🚀 Features

- 📌 QR Code-based learner attendance logging  
- 👤 Role-based access control (Admin, Employee, Learner)  
- 📧 Email-based announcement system (filter by users or grade level and section)  
- 🔐 Email OTP verification for registration  
- 📊 Dashboard and user management  
- ✅ Responsive and mobile-friendly UI  
- 🗂️ Modular and scalable Laravel 12 codebase  

---

## 🛠️ Tech Stack

- **Laravel 12**  
- **PHP 8+**  
- **MySQL**  
- **Tailwind CSS** – for modern and responsive styling  
- **Bootstrap 5** – for layout and components  
- **Spatie Laravel Permission** – role and permission management  
- **Laravel Breeze** – for authentication scaffolding  
- **SweetAlert2** – for alert feedback  
- **QR Code Scanner** – for real-time attendance capture  

---

## 💡 Composer Requirements

- ✅ **Recommended Version:** Composer **2.6 or higher**

### 🔍 To check your Composer version:

```bash
composer --version
```

📦 **Need Composer?**  
🔗 [Official Composer Installer](https://getcomposer.org/download/)  
📥 [Download from Google Drive (Backup)](https://drive.google.com/file/d/1_RvlePpUOzqaVPJYQ-HQKCGEkAYxxkZU/view?usp=sharing)

---

## ⚙️ Installation Instructions

> These steps assume you have Composer, PHP, Node.js, and a local server (like XAMPP) installed.

### 🔧 Steps 1–8: Set up the Project Locally

```bash
# 1. Clone the repository
git clone https://github.com/leonardtdomingovida/learner-and-employee-management-system.git
cd learner-and-employee-management-system

# 2. Install PHP dependencies
composer install

# 3. Install frontend assets (Tailwind, Bootstrap, etc.)
npm install && npm run dev

# 4. Copy the example environment file and configure it
cp .env.example .env
# Open .env and set up database, mail, and app configs

# 5. Generate application key
php artisan key:generate

# 6. Run database migrations
php artisan migrate

# 7. (Optional) Link storage for public access
php artisan storage:link

# 8. Start Laravel server
php artisan serve
```

---

## 🖼️ UI Screenshots

### 🏠 Landing Page
![Landing Page](public/screenshots/landing_page.png)

### 🔐 Login Page
![Login Page](public/screenshots/login.png)

### 📝 Register Page
![Register Page](public/screenshots/register.png)

### 📊 Admin Dashboard
![Admin Dashboard](public/screenshots/admin_dashboard.png)

---

👨‍💻 **Developer**  
**Leonard Domingo**

---

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).  
The Laravel framework used in this project is also licensed under the MIT license.
<h1 align="center">🎨 Hobby Hub – Community & Club Management Platform</h1>
<p align="center">
  <img src="https://img.shields.io/badge/Status-In%20Progress-yellow.svg" />
  <img src="https://img.shields.io/github/license/abhishak1305/Hobby-Hub-community-platform" />
  <img src="https://img.shields.io/badge/Made%20With-PHP-informational" />
  <img src="https://img.shields.io/github/languages/top/abhishak1305/Hobby-Hub-community-platform" />
</p>

<p align="center">
  A lightweight full-stack web app for <b>clubs</b>, <b>college groups</b>, and <b>hobby communities</b> to organize events, engage members, and streamline interactions — all under one dashboard.
</p>

---

## ✨ Features Overview

🔐 **Authentication**  
- Secure signup & login (session-based)  
- Password hashing  
- CSRF-protected forms  

👥 **Group Management**  
- Create or join hobby groups  
- Admin roles for group control  
- View members and group info  

💬 **Discussion Board**  
- Create & comment on threads  
- Group-specific and public forums  

📆 **Event Scheduler**  
- Create and manage events  
- RSVP options: Going, Maybe, Not Going  
- Date, time, and venue fields  

📇 **Member Directory**  
- Search members  
- Filter by groups  
- Profile with activity info  

🛡 **Security**  
- PDO with prepared statements  
- XSS input sanitization  
- Strong password policy  
- Role-based access controls  

---

## 🛠 Tech Stack

| Layer         | Technology                      |
|---------------|----------------------------------|
| 🎨 Frontend   | HTML, Tailwind CSS, JavaScript   |
| 🧠 Backend    | PHP (vanilla)                    |
| 🗃 Database   | MySQL                            |
| 🎭 UI Icons   | Font Awesome                     |
| 🔠 Fonts      | Google Fonts (Inter)             |

---

## 📂 Folder Structure

📁 hobby_platform/
│
├── 📁 includes/ # Core config and reusable components
│ ├── config.php # Database credentials
│ ├── functions.php # Utility functions
│ ├── header.php # Page header
│ └── footer.php # Page footer
│
├── 📁 public/ # All user-facing pages
│ ├── index.php
│ ├── register.php
│ ├── login.php
│ ├── logout.php
│ ├── dashboard.php
│ ├── groups.php
│ ├── group_detail.php
│ ├── discussion.php
│ ├── events.php
│ ├── event_detail.php
│ └── members.php
│
└── 📄 schema.sql # MySQL database schema


---

## ⚙️ Setup Guide

### 📁 Clone and Configure
```bash
git clone https://github.com/abhishak1305/Hobby-Hub-community-platform.git
cd Hobby-Hub-community-platform


🗄 Import MySQL Schema
mysql -u root -p < schema.sql
🧩 Edit Database Config
In includes/config.php:

php

define('DB_HOST', 'localhost');
define('DB_NAME', 'hobby_platform');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
🌐 Run on Localhost
Use XAMPP/WAMP → Place files inside /htdocs → Start Apache/MySQL
Visit: http://localhost/Hobby-Hub-community-platform/public/

📸 Screenshots
Login Page	Dashboard

(Replace with real screenshots later)

🧠 Coming Soon
✅ Email OTP login 🔐

✅ AI-powered group recommendations 🤖

✅ Admin dashboard with insights 📊


📢 Usage Tips
Register with a strong password (min. 8 chars)

Join or create a group → Post discussions → Manage events

Use search bar to find members or events

Only group admins can create/edit events in that group

🤝 Contributing
# Fork this repo
# Create a new branch: git checkout -b feature/feature-name
# Make your changes and commit: git commit -m "Add feature"
# Push: git push origin feature/feature-name
# Submit a Pull Request ✔️

🧾 License
This project is licensed under the MIT License.

# AIVORA - Your Intelligent Everyday Assistant

> **University Assignment:** Advanced Web Development in PHP  
> **Course Code:** 2010043347  
> **Degree Program:** B.Tech CE / CSE / IT, Semester V  
> **Website Name:** Aivora (`Aivora.ai`)  
> **Topic:** AI-Assistant (SaaS-style product website presenting conversational chat, writing, coding, summarizing, and productivity)  
> **Backend:** Pure PHP 8 (Zero frameworks, zero Composer/npm dependencies, 100% FTP-deployable)  
> **Database:** MySQL 5.7+ / 8.0+ (Full PDO prepared statements, fully compatible with SmarterASP.NET and XAMPP)  
> **Frontend:** HTML5, CSS3, ES6 JavaScript, Bootstrap 5 CDN, Bootstrap Icons CDN, Google Fonts (Inter + Poppins)

---

## 1. Table of Contents
1. [Project Overview](#2-project-overview)
2. [Design & UI/UX Features](#3-design--uiux-features)
3. [Architecture & Folder Structure](#4-architecture--folder-structure)
4. [Database Design & Schema](#5-database-design--schema)
5. [Local Setup Guide (XAMPP)](#6-local-setup-guide-xampp)
6. [SmarterASP.NET Deployment Guide](#7-smarteraspnet-deployment-guide)
7. [Security Standards Implemented](#8-security-standards-implemented)
8. [Default Authentication Credentials](#9-default-authentication-credentials)
9. [Viva Questions & Answers (5 Essential Academic Q&A)](#10-viva-questions--answers)

---

## 2. Project Overview
**Aivora** is an AI assistant platform designed to empower students, software engineers, researchers, and small businesses with fast, context-aware artificial intelligence.

### Key Functional Capabilities:
- **Conversational AI Chat:** Natural language interaction with context-aware responses.
- **Interactive "Try Aivora" Playground (`demo.php`):** Sends messages asynchronously via `fetch()` to `chat_api.php`, evaluates rule-based keywords from the `faq_responses` database table, displays animated typing indicators, and logs conversations to `chat_logs`.
- **Smart Writing Assistant:** Tone selection and structured essay/article generation.
- **Code Copilot & Debugger:** Multi-language syntax formatting (PHP, JS, Python, SQL) and logic explanations.
- **Deep Document Summarization:** Condenses dense text into executive takeaways.
- **Dynamic Content Engine:** Features, pricing plans, testimonials, team members, blog articles, and FAQs are loaded dynamically from MySQL using PDO.
- **User Authentication & Dashboard:** User registration with `password_hash(PASSWORD_BCRYPT)`, secure login with `password_verify()`, session regeneration, personal chat history, and `.txt` transcript download.
- **Contact Inquiries with Spam Protection:** Server-side validation, client-side validation, CSRF tokens, and hidden honeypot spam traps.
- **AJAX Newsletter:** Subscribes visitors seamlessly without full page reloads.
- **Full Administrative Panel (`admin/`):** Session-protected dashboard displaying counts and complete CRUD for blog articles, capability modules, contact submissions, and subscriber lists (with CSV export).

---

## 3. Design & UI/UX Features
- **Color Palette:** Deep Navy (`#0B1020`), Electric Violet (`#7C3AED`), Cyan Accent (`#06B6D4`), and crisp soft whites.
- **Glassmorphism:** Translucent glass cards with backdrop blur (`backdrop-filter: blur(14px)`), ambient neon gradient orbs, and subtle glowing borders.
- **Dark & Light Mode:** Toggle switch saved to browser `localStorage` and applied across pages without page reload flashes.
- **Vector Brand Identity:** Custom SVG neural core icon + text logo without external image dependencies.
- **Interactive Micro-Animations:** Glowing button hovers, animated stats counters counting up via `IntersectionObserver`, and natural chat typing dots.
- **SEO & Responsiveness:** Mobile hamburger menu, fluid Bootstrap 5 layout, meta descriptions, Open Graph social cards, `sitemap.xml`, and `robots.txt`.

---

## 4. Architecture & Folder Structure

```text
/AIVORA
├── 404.php                 # Custom friendly 404 error page
├── about.php               # Mission, vision, core values, team from DB, timeline
├── blog.php                # Articles listing with search (?q=) and pagination
├── blog-single.php         # Single article view with recent posts sidebar & share links
├── chat_api.php            # AJAX API endpoint: matches keywords & logs to chat_logs
├── contact.php             # Contact form with CSRF, honeypot, validation & DB insert
├── dashboard.php           # User profile, personal chat history & .txt export
├── database.sql            # Full MySQL database schema + rich seed data
├── demo.php                # Interactive "Try Aivora" chat playground
├── features.php            # Detailed AI capability breakdown loaded from DB
├── index.php               # Hero, live mockup, DB features, how it works, stats, testimonials
├── login.php               # User login with password_verify & session fixation protection
├── logout.php              # Destroys user session & redirects with flash message
├── pricing.php             # 3 Plans from DB, monthly/annual toggle, FAQ accordion
├── README.md               # Project documentation & deployment guide
├── register.php            # New user registration with password_hash (bcrypt)
├── robots.txt              # Search engine crawler instructions
├── sitemap.xml             # XML sitemap for search engines
├── subscribe.php           # AJAX endpoint for newsletter subscriptions
│
├── includes/
│   ├── auth.php            # Auth guards: is_logged_in(), require_login(), require_admin()
│   ├── config.php          # Database credentials, constants, session params, error config
│   ├── db.php              # PDO singleton connection with robust error logging
│   ├── footer.php          # Dark footer, newsletter form, academic info, scripts
│   ├── functions.php       # Sanitization, CSRF token helpers, flash alerts, slugify
│   └── header.php          # Responsive navbar, theme switch, user dropdown, SEO tags
│
├── admin/
│   ├── admin_dashboard.php # Admin dashboard: metrics counts, recent activity tables
│   ├── admin_footer.php    # Shared admin footer
│   ├── admin_header.php    # Admin navigation & session auth guard
│   ├── admin_login.php     # Admin authentication portal
│   ├── admin_logout.php    # Admin session termination
│   ├── manage_blog.php     # Full CRUD (Add, Edit, Delete, View) for blog posts
│   ├── manage_features.php # Full CRUD for AI feature capabilities
│   ├── messages.php        # Contact inquiries viewer & modal detail reader
│   └── subscribers.php     # Subscriber list viewer, deletion & CSV export
│
└── assets/
    ├── css/
    │   └── style.css       # Complete design system, glassmorphism, glowing animations
    ├── js/
    │   ├── chat.js         # Interactive chat logic, typing animation, fetch(), copy btn
    │   └── main.js         # Dark/light toggle, preloader, stats counter, newsletter AJAX
    └── images/
        └── logo.svg        # Custom neural SVG vector logo
```

---

## 5. Database Design & Schema

Database Name: `aivora_db` (or user's SmarterASP database name)

| Table | Description | Key Columns |
| :--- | :--- | :--- |
| `users` | Registered end-users | `id`, `name`, `email` (UNIQUE), `password` (bcrypt hash), `created_at` |
| `admins` | Administrative users | `id`, `username` (UNIQUE), `password` (bcrypt hash), `created_at` |
| `contacts` | Submissions from contact form | `id`, `name`, `email`, `subject`, `message`, `created_at` |
| `subscribers` | Newsletter email list | `id`, `email` (UNIQUE), `subscribed_at` |
| `features` | 6 AI capability modules | `id`, `title`, `description`, `icon`, `created_at` |
| `plans` | Pricing tiers | `id`, `name`, `price_monthly`, `price_yearly`, `features_list`, `is_popular` |
| `testimonials`| Client reviews | `id`, `name`, `role`, `message`, `rating`, `avatar` |
| `team` | Company leadership | `id`, `name`, `role`, `bio`, `photo` |
| `blog_posts` | Research & tutorial articles | `id`, `title`, `slug` (UNIQUE), `excerpt`, `content`, `image`, `author`, `created_at` |
| `faq_responses`| Chatbot rule-based knowledge | `id`, `keyword`, `response` |
| `chat_logs` | Interaction history | `id`, `user_id` (NULLABLE FK to `users`), `user_message`, `bot_response`, `created_at` |

---

## 6. Local Setup Guide (XAMPP)

1. **Install and Start XAMPP:**
   - Launch the XAMPP Control Panel.
   - Start **Apache** and **MySQL**.
2. **Move Project Folder:**
   - Copy or move the `AIVORA` folder into `C:\xampp\htdocs\AIVORA`.
3. **Create Database in phpMyAdmin:**
   - Open browser and go to `http://localhost/phpmyadmin`.
   - Click **Databases**, enter `aivora_db`, and select collation `utf8mb4_unicode_ci`.
   - Click **Create**.
4. **Import Database Schema & Seed Data:**
   - Select the newly created `aivora_db` on the left.
   - Click the **Import** tab at the top.
   - Click **Choose File**, select `C:\xampp\htdocs\AIVORA\database.sql`, and click **Import**.
5. **Configure Database Credentials:**
   - Open `includes/config.php` in your text editor.
   - Verify the default local values:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'aivora_db');
     define('DB_USER', 'root');
     define('DB_PASS', '');
     define('DB_PORT', '3306');
     ```
6. **Launch Website:**
   - Open browser and visit: `http://localhost/AIVORA`
   - Access the Admin Panel at: `http://localhost/AIVORA/admin/admin_login.php`

---

## 7. SmarterASP.NET Deployment Guide

SmarterASP.NET provides shared Windows/IIS hosting supporting PHP 8.x and MySQL. Follow these exact steps for deployment:

### Step 1: Create the MySQL Database on SmarterASP.NET
1. Log in to your [SmarterASP.NET Control Panel](https://www.smarterasp.net).
2. Go to **Databases** &rarr; **MySQL**.
3. Click **Create Database**.
4. Fill in:
   - **Database Name:** e.g., `db_aivora` (SmarterASP will prefix this, e.g., `db_aivora_123`).
   - **Username:** e.g., `aivora_user`.
   - **Password:** A strong password (e.g., `SecurePass@2026`).
5. Note down your **MySQL Server Name / Host** (e.g., `mysql5045.site4now.net` or an IP address).

### Step 2: Import database.sql via phpMyAdmin
1. In the SmarterASP.NET MySQL management panel, click the **phpMyAdmin** link beside your database.
2. Log in using your database username and password.
3. Select your database from the left navigation tree.
4. Click the **Import** tab.
5. Click **Choose File**, select `database.sql`, and click **Import**.

### Step 3: Configure includes/config.php
Open `includes/config.php` and update the constants with your SmarterASP credentials:
```php
define('DB_HOST', 'mysqlXXXX.site4now.net'); // Provided by SmarterASP
define('DB_NAME', 'db_aivora_XXXX');         // Your created DB name
define('DB_USER', 'db_aivora_user');        // Your DB username
define('DB_PASS', 'YourSecurePassword123!'); // Your DB password
define('DB_PORT', '3306');
define('DB_CHARSET', 'utf8mb4');

// Turn off display_errors in production:
define('APP_DEBUG', false);
```

### Step 4: Upload All Files via FTP or File Manager
1. In the SmarterASP.NET Control Panel, locate your **FTP details** (FTP Host, FTP Username, FTP Password).
2. Open **FileZilla** (or any FTP client) or use the **Online File Manager**.
3. Connect to your site and navigate to the root directory (typically `/site/wwwroot/` or the root public folder).
4. Upload all files and subdirectories (`admin/`, `includes/`, `assets/`, `index.php`, etc.).

### Step 5: Verify Live Operations
1. Open your live domain in a browser.
2. Test the **Try Aivora** chatbox (`demo.php`) to confirm AJAX and `chat_logs` insertions work.
3. Test the **Contact form** (`contact.php`) and **Newsletter form** in the footer.
4. Log into the **Admin Panel** (`/admin/admin_login.php`) and test creating or editing a blog article.

---

## 8. Security Standards Implemented

- **Prepared Statements (PDO):** Every database query employs native prepared statements with bound parameters (`$stmt->prepare()` and `$stmt->execute()`). String concatenation in SQL statements is strictly avoided, preventing SQL Injection.
- **Cross-Site Scripting (XSS) Prevention:** All dynamic variables rendered into HTML are filtered through `htmlspecialchars($val, ENT_QUOTES, 'UTF-8')` via the custom `escape()` helper.
- **Cross-Site Request Forgery (CSRF) Tokens:** All POST forms and destructive GET actions (e.g., post deletion) validate cryptographically secure random session tokens via `hash_equals()`.
- **Password Hashing:** Passwords are hashed using PHP's native `password_hash(PASSWORD_BCRYPT)` algorithm with automatic salting, and authenticated via `password_verify()`.
- **Session Fixation Defense:** `session_regenerate_id(true)` is executed upon every successful user and admin authentication.
- **Honeypot Anti-Spam:** A hidden form field traps automated bots on the contact page without requiring frustrating CAPTCHAs for real humans.

---

## 9. Default Authentication Credentials

### 1. Administrator Account:
- **URL:** `admin/admin_login.php`
- **Username:** `admin`
- **Password:** `Admin@123`

### 2. Demo User Account:
- **URL:** `login.php`
- **Email:** `demo@aivora.com`
- **Password:** `User@123`

---

## 10. Viva Questions & Answers (Academic Q&A)

Here are 5 questions with comprehensive answers for university oral examinations:

### Q1: What is the primary advantage of PDO over MySQLi in PHP?
**Answer:**
**PDO (PHP Data Objects)** provides a unified data-access abstraction layer. It allows developers to write consistent database code across 12+ database management systems (MySQL, PostgreSQL, SQLite, Oracle, etc.) simply by changing the Data Source Name (DSN) connection string. In contrast, `mysqli` is tightly coupled exclusively to MySQL. Furthermore, PDO supports named parameters (e.g., `:email`), robust exception handling (`PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION`), and object-oriented result mapping.

### Q2: How does a PDO prepared statement protect against SQL Injection?
**Answer:**
SQL Injection occurs when untrusted user input is treated as executable SQL code. In a prepared statement, the database engine compiles the query template first (`SELECT * FROM users WHERE email = :email`), locking the query structure into the execution plan. When user input is bound during `execute([':email' => $userInput])`, the database engine treats that input strictly as **literal data**, never as executable commands. Even if the input contains SQL tokens like `' OR '1'='1`, it is evaluated purely as text characters.

### Q3: What is Session Fixation, and how does your application mitigate it?
**Answer:**
Session Fixation is an attack where an adversary sets or predicts a victim's session identifier before the victim authenticates. If the application retains the same session ID after login, the attacker can hijack the authenticated session. In Aivora, we neutralize this by calling `session_regenerate_id(true)` inside `login_user()` and `login_admin()` immediately upon successful credential verification. This generates a brand-new cryptographic session ID and invalidates the old session identifier.

### Q4: Why should password_hash() with BCRYPT be used instead of md5() or sha1()?
**Answer:**
`md5()` and `sha1()` are general-purpose, high-speed checksum algorithms. Modern GPUs can calculate billions of MD5 or SHA1 hashes per second, making them vulnerable to brute-force and rainbow table attacks. Conversely, `password_hash()` utilizes **Bcrypt** (or Argon2), which is an adaptive cryptographic key-derivation function. It incorporates a random cryptographic salt automatically and uses a configurable work factor (computational cost), making mass offline brute-force attacks computationally infeasible.

### Q5: What modifications are necessary when deploying a local PHP/MySQL project to a cloud/shared hosting environment like SmarterASP.NET?
**Answer:**
1. **Centralized Configuration:** Abstract all environment parameters (Host, DB Name, User, Password, Port) into a single configuration file (`includes/config.php`).
2. **Relative Paths & Path Traversal Safety:** Avoid hardcoded absolute local paths like `C:\xampp\...` or `http://localhost/...`; rely instead on `__DIR__` and relative links.
3. **Database Import:** Export the schema and seeds via `.sql` and import them into the remote host's MySQL server using phpMyAdmin or MySQL Workbench.
4. **Production Error Handling:** Disable `display_errors` in live production to prevent database credentials or directory paths from leaking to users during unexpected exceptions, and route errors to a secure server-side log file.

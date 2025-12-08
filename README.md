# 🚀 Mini ERP - Operational Reporting System

A robust, role-based operational management system built with **Laravel**. This application streamlines the process of daily stock reporting, validation workflows, and analytical dashboards for multi-level users.

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![Bootstrap](https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white)
![jQuery](https://img.shields.io/badge/jQuery-0769AD?style=for-the-badge&logo=jquery&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white)

## ✨ Key Features

### 🔐 Authentication & Roles
* **Multi-role System:** Super Admin, Admin (Manager), and User (Staff).
* **Secure Login:** Split-screen design with CSRF token protection.
* **Profile Management:** Update profile details and upload avatars with **Cropper.js** (Image cropping & resizing).

### 👑 Super Admin
* **User Management:** Create, Edit, and Delete accounts.
* **Master Data Management:** Manage operational items, units, and prices with auto-format Rupiah (JS).
* **Global Dashboard:** View system-wide statistics and charts.

### 👮 Admin (Manager)
* **Approval Workflow:** Review daily reports with **Approve (ACC)** or **Reject** actions.
* **Data Revision:** Edit quantity or notes on staff reports via AJAX Modals.
* **Analytics:** Visual charts (ApexCharts) for monthly expenses and usage trends.
* **Reporting:** Filterable tables (Date Range & Staff) with **Select2** integration.

### 👷 User (Staff)
* **Single Page Interface:** Input reports and view history in one seamless page.
* **Dynamic Forms:** Input multiple items dynamically based on active Master Data.
* **Status Tracking:** Real-time status updates (Pending, Approved, Rejected).
* **Safe Edit/Delete:** Can only modify reports that are still **Pending**.

---

## 🛠️ Tech Stack

* **Backend:** Laravel 10 / 11
* **Frontend:** Blade Templates, Bootstrap 4/5 (Nazox Theme)
* **Scripting:** jQuery, AJAX (for seamless CRUD without reload)
* **Database:** MySQL
* **Libraries:**
    * `yajra/laravel-datatables` (Optional/if used)
    * `select2` (Searchable Dropdowns)
    * `apexcharts` (Data Visualization)
    * `cropperjs` (Image Manipulation)
    * `sweetalert2` (Popups)

---

## 📸 Screenshots

*(You can upload screenshots to an 'assets' folder or an issue thread and link them here to make your repo look cool)*

| Login Page | Dashboard |
|Data Input | Approval Modal |

---

## ⚙️ Installation & Setup

Follow these steps to run the project locally:

1.  **Clone the Repository**
    ```bash
    git clone [https://github.com/yourusername/mini-erp-laravel.git](https://github.com/yourusername/mini-erp-laravel.git)
    cd mini-erp-laravel
    ```

2.  **Install Dependencies**
    ```bash
    composer install
    ```

3.  **Environment Setup**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

4.  **Database Configuration**
    * Create a new database in MySQL (e.g., `db_minierp`).
    * Open `.env` and update DB credentials:
        ```env
        DB_DATABASE=db_minierp
        DB_USERNAME=root
        DB_PASSWORD=
        ```

5.  **Migrate & Seed** (Important to get default accounts)
    ```bash
    php artisan migrate:fresh --seed
    ```

6.  **Link Storage** (For profile pictures)
    ```bash
    php artisan storage:link
    ```

7.  **Run Application**
    ```bash
    php artisan serve
    ```

---

## 👤 Default Accounts (Seeder)

Use these credentials to test different roles:

| Role | Email | Password |
| :--- | :--- | :--- |
| **Super Admin** | `super@app.com` | `password` |
| **Admin** | `admin@app.com` | `password` |
| **User** | `user@app.com` | `password` |

---

## 🤝 Contributing

1.  Fork the repository
2.  Create your feature branch (`git checkout -b feature/AmazingFeature`)
3.  Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4.  Push to the branch (`git push origin feature/AmazingFeature`)
5.  Open a Pull Request

---

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

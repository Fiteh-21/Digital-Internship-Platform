# InternHub - Professional Internship Portal

InternHub is a comprehensive, three-way platform designed to bridge the gap between talented students and forward-thinking companies. Built with a focus on professional aesthetics and seamless user experience, it provides a robust ecosystem for managing internship opportunities, applications, and direct communications.

## 🚀 Key Features

### 👨‍🎓 For Students
*   **Professional Profiles**: Build a comprehensive digital resume with profile pictures, headlines, and professional details.
*   **Advanced Search**: Browse internships by category, location, and stipend.
*   **Application Tracking**: Manage and track the status of all your applications in real-time.
*   **Direct Messaging**: Receive instant notifications and detailed feedback directly from system administrators.

### 🏢 For Employers
*   **Opportunity Management**: Post, edit, and manage internship listings with ease.
*   **Applicant Tracking System (ATS)**: Review student profiles, download resumes, and manage application statuses (Accepted/Rejected) with custom messages.
*   **Company Dashboard**: Get a high-level overview of active postings and pending applications.

### 🛡️ For Administrators
*   **System Oversight**: Comprehensive dashboard to manage all students and companies.
*   **Mass Communication**: Direct messaging system to contact any user or company on the platform.
*   **Content Moderation**: Ability to delete internships with automated notifications to the posting company.
*   **System Analytics**: Real-time tracking of total students, companies, and applications.

## 🛠️ Technology Stack
*   **Backend**: PHP 8.x
*   **Database**: MySQL
*   **Frontend**: HTML5, Vanilla CSS3 (Custom Design System), JavaScript (ES6)
*   **UI Components**: Bootstrap 5 (for interactive Modals & Admin UI), FontAwesome 6
*   **Typography**: Google Fonts (Outfit)

## 📁 Project Structure
```text
anti/
├── admin/          # Administrative control panel & APIs
├── assets/         # Compiled CSS and JavaScript files
├── company/        # Employer dashboard and management tools
├── student/        # Student profile and application portal
├── includes/       # Core shared components (header, footer, DB)
├── uploads/        # User-uploaded resumes and profile pictures
├── api/            # Global API endpoints for auth and actions
└── database/       # SQL schema and database configurations
```

## ⚙️ Installation & Setup

1.  **Clone the Repository**:
    ```bash
    git clone [repository-url]
    ```

2.  **Database Configuration**:
    *   Import the SQL schema located in `database/` into your MySQL server.
    *   Update `includes/db.php` with your local database credentials (host, user, password, database name).

3.  **Local Environment**:
    *   Ensure you are using a local server like **MAMP**, **XAMPP**, or **WAMP**.
    *   Set the root directory to the project folder.

4.  **Admin Access**:
    *   Default admin credentials can be configured in the `users` table with the role `admin`.

## 📍 Contact & Support
*   **Location**: Adama, Ethiopia
*   **Email**: internhub@gmail.com
*   **Website**: InternHub.com

---
*Developed with focus on efficiency and professional excellence.*

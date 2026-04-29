# Job Board Platform

A comprehensive job recruitment platform built with **Laravel** and **Vue.js**, designed to bridge the gap between employers and job seekers with a streamlined, professional workflow.

## 🚀 Tech Stack
- **Backend:** Laravel
- **Frontend:** Vue.js
- **Database:** MySQL / PostgreSQL
- **Payments:** Stripe & PayPal Integration

---

## 👥 User Roles & Access

### 🏢 Employers
- **Account Management:** Register and manage company profiles.
- **Job Posting:** Create detailed job listings with:
    - Categories (Programming, Management, etc.)
    - Location & Work Type (Remote, On-site, Hybrid)
    - Required Technologies & Skills.
- **Listing Management:** Edit, update, or delete posted jobs.
- **Applicant Tracking:** Review applications and change status (Accept/Reject).
- **Social Interaction:** Comment on job-related queries.
- **Analytics (Bonus):** Access detailed insights on job posting performance (e.g., number of applicants).

### 🎓 Candidates
- **Account Management:** Register and build a professional profile.
- **Advanced Search:** Find jobs by keywords, location, category, experience level, and salary range.
- **Application Process:** 
    - Apply by uploading a resume directly.
    - Obtain contact details (Email, Phone) for direct application.
    - Manage and track application status.
- **Notifications (Bonus):** Receive real-time updates regarding application status.

### 🛡️ Admins
- **Moderation:** Approve or reject job postings submitted by employers to ensure quality.
- **Platform Monitoring:** Track overall activity and user behavior.
- **Comment Management (Bonus):** Remove inappropriate or "bad" comments to maintain a professional environment.

---

## 🛠️ Functional Modules

### Job Listing Management
Employers can create rich job descriptions including:
- Job title, responsibilities, and requirements.
- Salary range and benefits.
- Application deadlines.
- **Branding:** Upload company logos and branding elements.

### Application Process
- **Direct Upload:** Candidates can upload resumes to the platform.
- **Contact Forwarding:** Provide contact info (Email/Phone) to be forwarded to employers.
- **Internal Forms (Bonus):** Fill out application forms within the platform and connect profiles with **LinkedIn**.
- **Payment:** Employers process payments after candidate approval/hiring via **Stripe** or **PayPal**.

### Search & Filtering
Powerful filtering system based on:
- Keywords in title/description.
- Geographic Location.
- Industry/Category.
- Experience Level & Salary Range.
- Posting Date.

---

## 🌟 Bonus Features
- **Resume Database:** Employers can search the candidate pool for specific skills or experience.
- **Analytics Dashboard:** Visual representation of application trends.
- **LinkedIn Integration:** One-click profile syncing for candidates.

---

## ⚙️ Installation & Setup

1. **Clone the repository:**
   ```bash
   git clone https://github.com/Abdullah2elsman/Job-Board.git
   ```
2. **Backend Setup:**
   ```bash
   composer install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate
   ```
3. **Frontend Setup:**
   ```bash
   npm install
   npm run dev
   ```
4. **Environment Configuration:**
   Configure your DB, Stripe, and PayPal credentials in the `.env` file.

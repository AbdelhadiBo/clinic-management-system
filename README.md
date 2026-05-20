🏥 MediCare - Clinic Management System
A comprehensive web-based clinic management system built with Vue.js 3 (frontend) and Laravel 11 (backend). Designed for healthcare facilities to efficiently manage patients, appointments, consultations, prescriptions, and billing.
 Vue.js 

 Laravel 

 PHP 

 MySQL 

 License 
✨ Features
🔐 Authentication & Roles
Admin - Full system access (doctors, secretaries, nurses, patients, reports)
Secrétaire - Patient registration, appointment scheduling, invoice management
Infirmier - Daily consultation management
Secure token-based authentication (Laravel Sanctum)
👥 Patient Management
Complete patient profiles with medical history
Blood type tracking, allergies, and antecedents
Quick search and filtering
Medical Record Viewer (read-only for secretaries)
📅 Appointment System
Schedule appointments with doctors
Status tracking: En attente, Confirmé, Annulé, Terminé
Auto-refresh dashboard with today's appointments
Calendar integration ready
🩺 Consultations
Doctor-patient consultation records
Diagnosis, treatment, and observations
Prescription (Ordonnance) generation
Linked to appointments and invoices
💊 Prescriptions
Medication listings with dosage instructions
Printable prescription format
Linked to specific consultations
💰 Billing & Invoices (Factures)
Automatic invoice generation from consultations
Payment status tracking: Payé / Non payé
Multiple payment methods: Cash, Card, Insurance, Cheque
Revenue statistics and reporting
📊 Dashboard Analytics
Total patients, today's appointments
Monthly revenue overview
Active doctors count
Pending invoices alert

🏗️ Architecture
CLINIC-PROJECT-MAIN/
├── backend/                    # Laravel 11 API
│   ├── app/
│   │   ├── Http/Controllers/   # API Controllers
│   │   ├── Models/             # Eloquent Models
│   │   └── ...
│   ├── database/migrations/    # Database Schema
│   ├── routes/api.php          # API Routes
│   └── .env                    # Environment Config
│
├── frontend/                   # Vue.js 3 SPA
│   ├── src/
│   │   ├── views/              # Page Components
│   │   │   ├── admin/          # Admin Dashboard
│   │   │   └── secretaire/     # Secretary Dashboard
│   │   ├── components/         # Reusable Components
│   │   ├── services/api.js     # API Service Layer
│   │   └── router/index.js     # Vue Router
│   └── package.json
│
└── README.md

🚀 Getting Started
Prerequisites
PHP >= 8.2
Composer
Node.js >= 18
MySQL >= 8.0
npm or yarn
Backend Setup (Laravel)
bash
Copy
cd backend

# Install dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure your database in .env
# DB_DATABASE=clinic_db
# DB_USERNAME=root
# DB_PASSWORD=your_password

# Run migrations
php artisan migrate

# (Optional) Seed demo data
php artisan db:seed

# Start development server
php artisan serve
API will be available at: http://localhost:8000
Frontend Setup (Vue.js)

cd frontend :

# Install dependencies
npm install

# Start development server
npm run dev
App will be available at: http://localhost:5173


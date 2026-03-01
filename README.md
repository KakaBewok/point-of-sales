# Modern POS System
> Clean, responsive, and web-based Point of Sale system for small businesses and retail.

## Project Description

This project is a complete Web-Based Point of Sale (POS) application tailored specifically for UMKM (Micro, Small, and Medium Enterprises), small retail stores, and independent businesses. The core objective of this system is to simplify daily store operations, accelerate checkout processes, and provide accurate tracking for products and stock.

Built with a highly modern and interactive tech stack, this application relies on:
- **Laravel**: Robust PHP framework for backend logic and APIs.
- **Livewire & Blade**: For dynamic, reactive, and seamless real-time UI without full-page reloads.
- **TailwindCSS**: For rapid, beautiful, and fully responsive styling.

### Payment & Access
- **Payment Methods Supported**: Cash (direct transaction) and QRIS (seamless digital payment processing).
- **Role System**: Dedicated operational roles separating capabilities for **Admin** (full system control) and **Cashier** (payment and transaction processing).

---

## Features

This POS system ships with comprehensive retail features:
- **POS Transaction System**: A robust, fast cashier interface.
- **Product Management**: Full create, read, update, and delete access to items.
- **Category Management**: Organize products intelligently.
- **Stock Management**: Track inflow and outflow of your inventory easily.
- **Soft Delete Support**: Ensures transaction history remains intact even if associated products are removed.
- **QRIS Payment**: Simplified digital payments with QR code integration.
- **Role & Permission System**: Granular access control for Admins and Cashiers.
- **Collapsible Sidebar**: Clean and immersive dashboard navigation.
- **Responsive Layout**: Adapts gracefully across Desktop, Tablet, and Mobile views.
- **Bulk Delete**: Powerful data management capabilities.
- **Image Preview**: Real-time product image uploads and previews.
- **Auto-dismiss Notifications**: Non-intrusive, smooth feedback alerts.

---

## System Flow (Application Flow)

The application handles operations via clearly segmented operational flows based on User Roles:

**A. Admin Flow:**
- Login to the Admin Dashboard
- Manage products and categories
- Manage and review system-wide stock
- Manage user accounts, assign roles, and configure specific permissions

**B. Cashier Flow:**
- Login to the Cashier Dashboard
- Access the POS screen
- Select products or search via SKU/Name
- Add items to the cart
- Apply available discount vouchers
- Choose payment method (Cash or QRIS)
- Complete the transaction to print receipt

**C. Payment Flow:**
- **If Cash**: Input tendered amount -> Calculate change -> Direct success state -> Save transaction.
- **If QRIS**: Select QRIS -> System generates/shows QR Code -> Wait for user/bank confirmation -> Transaction completed.

---

## ERD (Entity Relationship Description)

The core application data relies on a structured relational database heavily utilizing foreign keys and data protection rules (such as soft deletes via `deleted_at`).

### Core Tables:
- `users`: Stores application users (Admins, Cashiers).
- `roles`: Defines system-wide access levels.
- `permissions`: Granular action rights linked to roles.
- `categories`: Organizes and groups products.
- `products`: Master catalog of store items.
- `stocks`: Logs and tracks inventory adjustments (In/Out/Set).
- `transactions`: Core sales headers securely recording payment status.
- `transaction_items`: Line-items containing specific product details per transaction.

### Relationships:
- **One-to-Many**: 
  - A `category` has many `products`.
  - A `product` has many `stocks` adjustments.
  - A `transaction` has many `transaction_items`.
  - A `product` can belong to many `transaction_items`.
- **Soft Deleting**: Tables like `products` utilize `deleted_at` to obscure deleted resources from standard views while maintaining historical relational integrity for old `transactions`.

### Text Diagram:
```text
[ categories ] 1 ------ * [ products ]
                               |
                               | 1
                               |
[ users ]                      *
   |                   [ transaction_items ] * ------ 1 [ transactions ]
   *
[ roles ]
   |
   *
[ permissions ]

[ products ] 1 ------ * [ stocks ]
```

---

## Installation Guide

Follow these steps to get the POS system running on your local machine:

1. **Clone repository**
```bash
git clone https://github.com/your-username/pos-system.git
cd pos-system
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Copy environment variables**
```bash
cp .env.example .env
```

4. **Generate application key**
```bash
php artisan key:generate
```

5. **Configure database**
Open your `.env` file and configure your database credentials:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=root
DB_PASSWORD=
```

6. **Run migrations**
```bash
php artisan migrate
```

7. **Seed database**
```bash
php artisan db:seed
```

8. **Build and run the development servers**
You need to run both Laravel and Vite dev servers concurrently:
```bash
# In your first terminal instance:
php artisan serve

# In your second terminal instance:
npm run dev
```

---

## Default Login

If you have executed the database seeder above, you may log in using the following default administration credentials:

**Admin Access:**
- **Email:** `admin@pos.com`
- **Password:** `password`

**Cashier Access:**
- **Email:** `cashier@pos.com`
- **Password:** `password`

---

## Project Structure

A brief overview of the primary application directories:
- `app/` - Contains the core backend logic, Livewire components, Models, and Controllers.
- `resources/` - Houses all raw frontend assets (CSS, JS) and Blade views (`resources/views`).
- `routes/` - Defines all accessible application endpoints (`web.php` handles web requests).
- `database/` - Stores database structural logic (migrations), model factories, and seeders.
- `public/` - The root directory exposed to the web, catching all incoming requests and housing built assets.

---

## License

This project is licensed under the [MIT License](https://opensource.org/licenses/MIT). You are free to modify and adapt this system for your own commercial or open-source projects.

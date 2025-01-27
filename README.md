# SAINS Inventory Management System

This Inventory Management System is developed as part of our Industrial Training (Internship) at SAINS (Sarawak Information Systems Sdn. Bhd.). 
The project is a collaboration between **Mohamad Haris** and **Sebestiano Spencer**, students from **Politeknik Kuching Sarawak**. This system is designed specifically for managing the inventory of IT equipment in the Technical Support Office at SAINS.

---

## Project Overview

The system is a Laravel-based web application for efficiently managing stock items such as routers, switches, modems, and other IT assets. It provides real-time search, category filtering, and CRUD operations with a user-friendly interface to streamline inventory management tasks.

---

## Features

- **Dashboard**: View all inventory items in a well-organized table.
- **CRUD Operations**: Add, edit, view, and delete inventory items.
- **Search Suggestions**: Real-time autocomplete dropdown with clickable suggestions to directly view specific items.
- **Category Filtering**: Easily filter inventory by predefined categories.
- **Responsive Design**: Mobile-friendly design using Bootstrap 5.
- **Success Popups**: Animated popup messages for successful actions (e.g., add/edit/delete).

---

## Technologies Used

- **Backend**: Laravel 10 (PHP Framework)
- **Frontend**: Bootstrap 5, FontAwesome
- **Database**: MySQL (via XAMPP/PhpMyAdmin)
- **Version Control**: GitHub for collaboration
- **Development Tools**: Visual Studio Code, Postman

---

## Setup Instructions

1. Clone the repository:
   ```bash
   git clone <repository_url>
   cd <project_folder>

2. Install dependencies:
   ```bash
   composer install
   npm install

3. Configure environmental variables:
   - Rename .env.example to .env.
   - Update database credentials.

4. Generate application key:
   ```bash
   php artisan key:generate

5. Run migrations and seed data (if applicable):
   ```
   php artisan migrate

6. Serve the application locally:
   ```
   php artisan serve

---

## About Laravel

<p align="center"> <a href="https://laravel.com" target="_blank"> <img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"> </a> </p> <p align="center"> <a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a> <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a> <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a> <a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a> </p>

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- Simple, fast routing engine.
- Powerful dependency injection container.
- Multiple back-ends for session and cache storage.
- Expressive, intuitive database ORM.
- Database agnostic schema migrations.
- Robust background job processing.
- Real-time event broadcasting.

Laravel is accessible, powerful and provides tools required for large, robust applications.

## License

The Laravel framework is open-sourced software licensed under the MIT license.


This structure integrates both the Laravel description and your project's details while keeping it well-organized. Let me know if you'd like further adjustments!
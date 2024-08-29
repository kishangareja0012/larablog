# Larablog

## Overview
**Larablog** is a blogging project where users can register, post blogs, and engage in conversations with other bloggers. The platform also provides statistics on views and likes for each blog post.

## Tech Stack
- **Laravel**: Backend framework
- **Bootstrap**: CSS framework
- **Pusher**: Real-time communication

## Installation

### Prerequisites
- **PHP**: Version 7.3
- **Node.js**: Version 20 or lower

### Clone the Repository
```bash
git clone https://github.com/kishangareja0012/larablog.git
cd larablog
```

### Install Dependencies

Install PHP dependencies:
```bash
composer install
```

Install Node.js dependencies:
```bash
npm install
```

### Set Up Environment Variables

Create a copy of the `.env.example` file:
```bash
cp .env.example .env
```

Generate the application key:
```bash
php artisan key:generate
```

### Configure Database

Edit the `.env` file to set up your database connection:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel-blog
DB_USERNAME=root
DB_PASSWORD=your_password_here
```

### Set Up Pusher

Go to [Pusher](https://pusher.com/), create an account, and retrieve your Pusher keys. Then, add them to the `.env` file:
```env
PUSHER_APP_ID=your_pusher_app_id
PUSHER_APP_KEY=your_pusher_app_key
PUSHER_APP_SECRET=your_pusher_app_secret
PUSHER_APP_CLUSTER=your_pusher_app_cluster
```

### Run Database Migrations

Run the following command to create the necessary database tables:
```bash
php artisan migrate
```

### Start the Development Server

In one terminal, start the Laravel server:
```bash
php artisan serve
```

In another terminal, compile the frontend assets:

- For development:
  ```bash
  npm run dev
  ```
- To watch for changes:
  ```bash
  npm run watch
  ```
- For production:
  ```bash
  npm run prod
  ```

## Conclusion
You are now ready to start using **Larablog**! Visit `http://localhost:8000` in your browser to access the platform.

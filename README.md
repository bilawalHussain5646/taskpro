# TaskPro - Kanban Task Manager

TaskPro is a **Laravel + Vue 3 (Vite)** Kanban-style task manager.  
It allows you to create, edit, delete, and manage tasks **day-wise**. Tasks are categorized by status: **Pending**, **In Progress**, and **Completed**.

---

## Features

-   **Daily Tasks:** Tasks are stored per day. Selecting a different date shows tasks for that day.
-   **Kanban Columns:** Drag and drop tasks between columns (Pending, In Progress, Completed).
-   **Add/Edit/Delete:** Easily add, edit, and delete tasks via modal dialogs.
-   **Responsive UI:** Works on desktop, tablet, and mobile devices.
-   **Date Navigation:** Move between previous and next days using arrows or a date picker.
-   **Materialize CSS:** Clean, responsive design for modern look and feel.

---

## Tech Stack

-   **Backend:** Laravel 10
-   **Frontend:** Vue 3 + Vite
-   **Database:** MySQL (or your preferred database)
-   **UI Framework:** Materialize CSS
-   **Drag & Drop:** `vuedraggable`

---

## Installation

### 1. Clone Repository

```bash
git clone https://github.com/<your-username>/taskpro.git
cd taskpro
```

### 2. Install PHP Dependencies

```bash
   composer install
```

### ### 3. Install Node.js Dependencies

```bash
   npm install
```

### 4. Configure Environment

```bash
   cp .env.example .env
   php artisan key:generate
```

### Update database credentials in .env:

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=taskpro
DB_USERNAME=root
DB_PASSWORD=
```

### Database Setup

Run migrations to create tables:

php artisan migrate

This will create the tasks table with the following columns:

Column Type Description
id bigint Primary key
title string Task title
due_date date Optional due date
status enum Task status: pending, progress, completed
task_date date The date the task belongs to
created_at timestamp Auto-generated
updated_at timestamp Auto-generated

### API Endpoints

Endpoint Method Description Body / Params
/api/tasks GET Get all tasks. Optional: ?date=YYYY-MM-DD -
/api/tasks POST Add a new task { title, due_date, status, task_date }
/api/tasks/{id} PUT Update a task { title, due_date, status, task_date }
/api/tasks/{id} DELETE Delete a task -

### Frontend Usage

-   **Date Picker & Arrows**: Select a date or move between previous/next days to view tasks.

-   **Add Task**: Click "Add Task" and fill in details.

-   **Edit Task**: Click a task card to edit.

-   **Delete Task**: Use the "Delete" link on each task card.

-   **Drag & Drop**: Move tasks between columns to change status.

### Run Development Server

```bash
npm run dev
php artisan serve
```

Open **http://127.0.0.1:8000**
in your browser.

### Build for Production

```bash
npm run build
```

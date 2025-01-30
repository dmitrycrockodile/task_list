# Task List API

This project is a task management application designed for users to create, update, and manage their tasks effectively. It provides a set of API endpoints that allow users to perform CRUD operations on tasks, including marking tasks as completed or incomplete. The application enforces strict authorization, ensuring that users can only interact with their own tasks, and includes error handling for database and other operational issues.

## Prerequisites

Before deploying the project, ensure you have the following installed:

- **Docker**: Docker and Docker Compose are required to containerize the application and PostgreSQL database. You can download Docker from [here](https://www.docker.com/get-started).
- **PHP**: PHP is used for managing Laravel dependencies; however, it will be handled by Docker containers.
- **Git**: Git is required to clone the repository.
- **PostgreSQL**: The application uses PostgreSQL as its database, which will be configured using Docker Compose.

## Step-by-Step Deployment Instructions

Follow these steps to deploy the application:

### 1. Clone the Repository

Clone the repository to your local machine using Git:

```bash
git clone https://github.com/dmitrycrockodile/task_list.git your-repository-name
cd your-repository-name
```

### 2. Create the ```.env``` File

Copy the example environment file to create your own `.env `file:

```bash
cp .env.example .env
```

This file contains the environment variables required for the Laravel application to function, including database connection settings.

### 3. Docker setup

The project uses Docker Compose to set up the environment. In the project root directory, run the following command to start the Docker containers:

```bash
docker-compose up -d
```

This will build and start the following containers:

app: The Laravel application container.  
postgres: The PostgreSQL database container.  
Make sure that the `.env` file is properly configured to connect to the PostgreSQL container:

```bash
DB_CONNECTION=pgsql
DB_HOST=postgres
DB_PORT=5432
DB_DATABASE=your_database_name
DB_USERNAME=your_database_username
DB_PASSWORD=your_database_password
```

The `DB_HOST` should be set to postgres, which is the name of the PostgreSQL container defined in the `docker-compose.yml` file.

### 4. Install Composer Dependencies

Once the containers are running, install the Laravel dependencies using Composer. Enter the `app` container and run:

```bash
docker exec -it <container_name> bash
composer install
```

### 5. Set Up the Database

After Composer installs the dependencies, run the following command to migrate the database:

```bash
php artisan migrate
```

This will create the necessary database tables (like `tasks` and `users`) according to the migrations defined in the Laravel application.

### 6. Create and Seed the Database to get pre-created users

To populate the database with test users run:

```bash
php artisan db:seed UserSeeder
```

### 7. Run the tests (optional)

You can run the tests using PHPUnit to ensure everything is working correctly:

```bash
docker exec -it <container_name> bash
php artisan test --filter TaskControllerTest
```
## Testing the API Endpoints

#### Get all tasks
**Description:** Retrieves a list of tasks for the authenticated user.  
**Response:** A confirmation message and a list of the tasks in JSON format.

```bash
  GET /api/tasks
```

#### Create task
**Description:** Creates a new task. Requires a JSON payload with `name` and `description`.  
**Response:** A confirmation message and the created task in JSON format.

```bash
  POST /api/tasks
```

#### Update task
**Description:** Updates an existing task. Requires the task `id` and updated data (`name`, `description`).  
**Response:** A confirmation message and the updated task in JSON format.

```bash
  PUT /api/tasks/{task}
```

#### Mark task as complete
**Description:** Marks the task as complete.  
**Response:** A confirmation message indicating that the task is marked as complete and the marked task in JSON format.
```bash
  PUT /api/tasks/{task}/complete
```

#### Mark task as incomplete
**Description:** Marks the task as incomplete.  
**Response:** A confirmation message indicating that the task is marked as incomplete and the marked task in JSON format.

```bash
  PUT /api/tasks/{task}/incomplete
```

#### Delete task
**Description:** Deletes the specified task.  
**Response:** A confirmation message indicating that the task is deleted.

```bash
  DELETE /api/tasks/{task}
```
## Pre-created users

For testing purposes, the following users have been pre-created in the database. You can use these credentials to test the API:

### 1. Joe
**Name:** `Joe`  
**Email:** `joe.test@example.com`  
**Password:** `joes_password`

### 2. John
**Name:** `John`  
**Email:** `john.test@example.com`  
**Password:** `johns_password`

### 3. Joel
**Name:** `Joel`  
**Email:** `joel.test@example.com`  
**Password:** `joels_password`
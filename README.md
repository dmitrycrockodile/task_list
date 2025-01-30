

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
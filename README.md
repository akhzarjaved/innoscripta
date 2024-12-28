# Project Setup Guide

This guide will help you set up and run the project using Docker. The project uses SQLite for the database, Laravel Sanctum for API authentication, and Swagger for API documentation.

## Prerequisites

Before you begin, ensure you have the following installed:
- Docker
- Docker Compose

## Setting Up the Project
### 1 - Clone the Repository

Clone the repository to your local machine:

```
git clone https://github.com/akhzarjaved/innoscripta.git
cd innoscripta
```

### 2 - Create .env File

The project relies on an .env file to configure environment variables. You will need to create this file in the root of the project. You can copy the .env.example file to create the .env file:

```
cp .env.example .env
```

Make sure to adjust any necessary configuration values in the .env file, especially database settings.

### 3 - Build and Start Docker Containers

Run the following command to build and start the Docker containers:
```
docker-compose up -d
```
This will:
- Build the app container using the provided Dockerfile.
- Set up the project directory with the appropriate volume mapping.
- Expose port 8000 on your local machine for the Laravel app.

The volumes configuration ensures that any changes you make to your project files on your local machine will be reflected inside the Docker container at `/var/www/html`

## Database

The project uses SQLite as the database. The SQLite database file will be stored locally in the project directory under database folder.

## API Documentation

The project uses Swagger for API documentation. You can access the API documentation at the following URL:

http://localhost:8000/api/documentation

Swagger will provide a user-friendly interface to interact with and explore the API.

## API Authentication

This project uses Laravel Sanctum for API authentication. To authenticate API requests, you need to generate an authentication token.

## Important Notes

If you make any changes to the .env file, the container may crash because Laravel automatically reloads the local server when the .env file is modified. Since the container has already occupied port 8000, this causes a conflict and results in a crash. To resolve this, you will need to restart the container whenever you modify the .env file. You can do so with the following command:

```
docker-compose up -d
```

This will start the container and apply the updated environment settings.

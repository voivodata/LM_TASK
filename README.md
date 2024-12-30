# Symfony Demonstration Api

## Features

-   Create users
-   Create and modify projects and related tasks

## Tech

Application uses following technologies:

-   Docker
-   Symfony 5
-   Mysql 8
-   Nginx

## Installation

In main folder run

```sh
docker compose up -d
```

with

```sh
docker ps
```

get id of php container and exec to it

docker exec -it {container id} bash

and run

```sh
composer install
php bin/console doctrine:migrations:migrate
```

update pems

```sh
php bin/console lexik:jwt:generate-keypair
```

Only dev environment

## Routing info

| Name                | Method | Path              |
| ------------------- | ------ | ----------------- |
| add_project         | POST   | /api/project      |
| soft_delete_project | DELETE | /api/project/{id} |
| get_projects        | GET    | /api/projects     |
| get_project         | GET    | /api/project/{id} |
| edit_project        | PUT    | /api/project/{id} |
| edit_task           | PUT    | /api/task/{id}    |
| soft_delete_task    | DELETE | /api/task/{id}    |
| create_task         | POST   | /api/task/{id}    |
| login               | POST   | /api/login        |
| test_exeption       | GET    | /api/testexeption |
| add_user            | POST   | /api/user         |

## How to test

Use postman app with following enviroment and collection

```sh
LinkM.postman_environment.json
LinkMobility.postman_collection.json
```

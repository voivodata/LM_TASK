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

## How to test

Use postman app with following enviroment and collection

```sh
LinkM.postman_environment.json
LinkMobility.postman_collection.json
```

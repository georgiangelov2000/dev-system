# Dev System docker setup guide

## Only if in src/ directory files aren't exist
#### docker-compose run --rm composer create-project --prefer-dist laravel/laravel:^8.0 .

## Existing project
#### docker-compose up -d nginx mysql or || docker-compose up -d (this command will setup all services in docker-compose.yml)
#### in src/ directory - docker-compose run composer install || update
#### in src/ directory - docker-compose run artisan migrate
#### in src/ directory - docker-compose run npm install

## Ports
#### Laravel project: port 80
#### PhpMyadmin: port 81
#### Mysql: Port 3306
#### FPM: Port 9000

## Generate nginx protection
#### htpasswd -c /etc/pwd/.htpasswd <username>
#### /etc/pwd/.htpasswd - directory for generated credentials: username:hashed password

## Countries and states
#### docker-compose run artisan g:c
#### docker-compose run artisan g:s all
  

  

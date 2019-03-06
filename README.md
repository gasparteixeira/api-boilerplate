## API BOILERPLATE Symfony 4.2, MySQL & JWT Authentication
This project is to help people to start a fast API

### Requirements
PHP, MySQL, Git, Composer, openssl
> Symfony has a _build-in web server_ you do not need Apache or Ngnx to run this project. Thanks Symfony :) 

### Install with composer
> Change (my-project-name) with the name of your project
```sh
$ composer create-project gasparteixeira/api-boilerplate my-project-name
```
### Check it out
```sh
cd my-project-name
$ php bin/console cache:clear
```
### Environment
1. Open the file .env and configure your database connection (user, password and database_name)
```sh
DATABASE_URL=mysql://user:password@127.0.0.1:3306/database_name
```
2. For security reasons, change this password
```sh
JWT_PASSPHRASE=boilerplate #change this password name and use the same password when you are generating the ssh keys 
```
3. Basic Auth, change username and password
```sh
// config/services.yml
parameters:
    app_username: boilerplate
    app_password: S3cr37W0rd
```
### Generate SSH keys
> use the same password you defined in .env, when asked for it 
```sh
$ mkdir -p config/jwt # For Symfony3+, no need of the -p option
$ openssl genrsa -out config/jwt/private.pem -aes256 4096
$ openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
```
### Creating database
> execute the command line to create your database
```sh
$ php bin/console doctrine:database:create
```
> make migrations
```sh
$ php bin/console make:migration
```
> lets commit the migration (it will create a table called user)
```sh
$ php bin/console doctrine:migrations:migrate
```
### Running 
```sh
$ php bin/console server:run
```
### Testing
> You can test it using Postman or through the terminal with curl using the username and password you defined in config\services.yml
```sh
curl -X POST -H "Content-Type: application/json" http://localhost:8000/api/token -d '{"username": "","password": ""}'
```
> Response must start like:
```sh
{ "token":"eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE1NTE4MjAwNzEsImVtYWlsIjo.. " }
```
### PHPUnit
```sh
$ php bin/phpunit
```
### API documentation 
> Go to http://localhost:8000/api/doc
More stuff check out [https://symfony.com](https://symfony.com)


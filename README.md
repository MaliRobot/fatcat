
# How to run

- Clone from Git
- Generate JWT keys and put them in config/jwt
- Create .env file, you can use .env.test and just substitute placeholder values. Create database and user in MySQL, 
use these values in .env
- Run `composer install`
- Run `php bin/console doctrine:schema:create`
- Run `php bin/console doctrine:migrations:migrate`
- Make sure permissions are set: https://symfony.com/doc/current/setup/file_permissions.html
- You need to substitute base URL you are using for this app in fatcat.wsdl file

When server is running check /api/doc or api/doc.json for Nelmio API documentation

To run bot which will use all routes excatly once, use following command:
`php bin/console app:run`

# Store Branches Tree

Based on the requirements, I've decided to setup the API in Laravel to get a quick dev environment.
The major features of the task are:
- Store branches tree structure
- API functions for branches
- Secure API calls
- Unit test for the code

## Tree Structure
Use Mysql table as storage. The tree structure is simple, each row in table represents a node in the tree.
The `parent_id` points to the nodes parent. When creating a new branch node, if API call specifies the `parent_id`, it will use the given `parent_id`. Otherwise, the `parent_id` will use `0` as default.

## API functions
The controller will be 


## Env setup
Clone the code, then 
1. create `.env` with following parameters
    APP_NAME="Store Branches API"
    APP_ENV=local
    APP_KEY=
    APP_DEBUG=true
    APP_URL=http://localhost
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=<PRODUCTION_DB_NAME>
    DB_USERNAME=
    DB_PASSWORD=
    DB_ROOT_PASSWORD=
    TEST_DB_HOST=127.0.0.1
    TEST_DB_PORT=3307
    TEST_DB_DATABASE=<TESTING_DB_NAME>
    TEST_DB_USERNAME=
    TEST_DB_PASSWORD=
    TEST_DB_ROOT_PASSWORD=

2. run `composer install` to install PHP packages
3. the docker environment is already setup, can just run `docker-compose up --build` to build the images and start them. Then next time, just need to run `docker-compose up` because the images are already built
4. run `php artisan migrate` to setup database structure in production db `<PRODUCTION_DB_NAME>`
5. run `php artisan migrate --database=testing` to setup db structure in testing db `<TESTING_DB_NAME>`


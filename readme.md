# Store Branches Tree

Based on the requirements, I've decided to setup the API in Laravel to get a quick dev environment.
The major requirements of the task are:
- Store branches tree structure
- API functions for branches
- Secure API calls
- Unit test for the code

## Tree Structure
Use Mysql table as storage. The tree structure is simple, each row in table represents a node in the tree.
The `parent_id` points to the nodes parent. When creating a new branch node, if API call specifies the `parent_id`, it will use the given `parent_id`. Otherwise, the `parent_id` will use `0` as default.

## API functions
The model will be `App\Branch`, it contains the functions: 
1. for building up the tree by traversing all nodes from root. The traversing method uses "Depth-first search".
2. for deleting all nodes under one root or node
3. for checking if a node is a parent/ancestor of another node. This function is used to prevent creating cirle

The controller will be `App\Http\Controllers\BranchController`, it contains all the required functions for the API

## Secure API calls
To secure the API calls, the Laravel Passport is installed and applied to the `BranchController`. The password based granting token is tested in API tests.

## Unit test for the code
Two test cases were created `tests\Feature\ApiTest` and `tests\Feature\BranchTest`
`tests\Feature\ApiTest` is used to test the API calls to get the access token, and test the usage of the token for Branch API call.

`tests\Feature\BranchTest` is used to test all the creating, updating, moving, viewing and deleting functions.

Run `phpunit` after environment setup, it will run tests for all of them. Some of the sample data was created during testing by the `database\Factory` and the `Faker` library.

And it will show a tree in console, `-=` stands for one level of the tree node.


## Env setup
Clone the code, then 
1. create `.env` with following parameters
    * APP_NAME="Store Branches API"
    * APP_ENV=local
    * APP_KEY=
    * APP_DEBUG=true
    * APP_URL=http://localhost
    * DB_CONNECTION=mysql
    * DB_HOST=127.0.0.1
    * DB_PORT=3306
    * DB_DATABASE=<PRODUCTION_DB_NAME>
    * DB_USERNAME=
    * DB_PASSWORD=
    * DB_ROOT_PASSWORD=
    * TEST_DB_HOST=127.0.0.1
    * TEST_DB_PORT=3307
    * TEST_DB_DATABASE=<TESTING_DB_NAME>
    * TEST_DB_USERNAME=
    * TEST_DB_PASSWORD=
    * TEST_DB_ROOT_PASSWORD=
2. run `composer install` to install PHP packages
3. the docker environment is already setup, can just run `docker-compose up --build` to build the images and start them. Then next time, just need to run `docker-compose up` because the images are already built
4. run `php artisan migrate` to setup database structure in production db `<PRODUCTION_DB_NAME>`
5. run `php artisan migrate --database=testing` to setup db structure in testing db `<TESTING_DB_NAME>`
6. run `php artisan passport:install` to generate two initial API auth clients 
7. run `php artisan passport:client --client` to generate a client_credentials auth client
8. copy the three clients into testing db `oauth_clients` table
9. add following values into `.env` to setup the credentials that needed for API testing
    * PASSWORD_GRANT_SECRET=
    * PASSWORD_GRANT_ID=2
    * CLIENT_CREDENTIAL_SECRET=
    * CLIENT_CREDENTIAL_ID=3
    


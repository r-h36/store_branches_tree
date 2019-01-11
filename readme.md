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


- [Simple, fast routing engine](https://laravel.com/docs/routing).


Grocery CRUD for Codeigniter 4
=============
⚠️ Warning: Work in progress! This repository is not in a working stage just yet!

## API and Functions list

| Function name  | Example | Small description |
| ------------- | ------------- | ------------- |
| addFields  | ```$crud->addFields(['first_name', 'last_name', 'fullname', 'address']);``` |The fields that will be visible to the end user for add/insert form.  |
| setRelation  | ```$crud->setRelation('officeCode', 'offices', 'city');``` | This is the function that is used to connect two tables with a 1 to n (1:n) relation.  |
| setTable  | ```$crud->setTable('customers');``` | This is the database table that the developer will use to create the CRUD.  |

For more information, visit http://www.grocerycrud.com
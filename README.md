Grocery CRUD for Codeigniter 4
=============
⚠️ Warning: Work in progress! This repository is not in a working stage just yet!

## API and Functions list

| Function name  | Example | Small description |
| ------------- | ------------- | ------------- |
| addFields  | ```$crud->addFields(['first_name', 'last_name', 'fullname', 'address']);``` |The fields that will be visible to the end user for add/insert form.  |
| cloneFields  | ```$crud->cloneFields(['customerName', 'phone', 'addressLine1', 'creditLimit']);``` |The fields that will be visible to the end user for clone form.  |
| columns  | ```$crud->columns(['first_name', 'last_name', 'age']);``` |Specifying the fields that the end user will see as the datagrid columns.  |
| defaultOrdering  | ```$crud->defaultOrdering('country', 'desc');``` |The default ordering that the datagrid will have before the user will press any button to order by column.  |
| displayAs  | ```$crud->displayAs('contact_first_name', 'First Name');``` |Displaying the field name with a more readable label to the end-user.  |
| setRelation  | ```$crud->setRelation('officeCode', 'offices', 'city');``` | This is the function that is used to connect two tables with a 1 to n (1:n) relation.  |
| setTable  | ```$crud->setTable('customers');``` | This is the database table that the developer will use to create the CRUD.  |

For more information, visit http://www.grocerycrud.com
Grocery CRUD community edition for Codeigniter 4
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
| editFields  | ```$crud->editFields(['first_name', 'last_name', 'fullname', 'address']);``` | The fields that will be visible to the end user for edit/update form.  |
| fieldType  | ```$crud->fieldType('contact_last_name', 'integer');``` | Changing the default field type from the database to fit to our needs.  |
| fields  | ```$crud->fields(['first_name', 'last_name', 'address']);``` | This function is really just a facade function to call all the 4 functions at once: addFields, editFields, readFields and cloneFields.  |
| getState  | ```$crud->getState();``` | Simply get the current state name as a string.  |
| getStateInfo  | ```$crud->getStateInfo();``` | Get all the information about the current state.  |
| readFields  | ```$crud->readFields(['first_name', 'last_name', 'fullname', 'address']);``` | The fields that will be visible when the end-user navigates to the view form.  |
| render  | ```$output = $crud->render();``` | This is the most basic function. In other words this means “make it work”.  |
| requiredFields  | ```$crud->requiredFields(['first_name', 'last_name']);``` | The most common validation. Checks is the field provided by the user is empty.  |
| setActionButton  | ```$crud->setActionButton('User Avatar', 'el el-user', function ($primaryKey) { return site_url('/view_avatar/' . $primaryKey); }, true);``` | Adding extra action buttons to the rows of the datagrid.  |
| setRelation  | ```$crud->setRelation('officeCode', 'offices', 'city');``` | This is the function that is used to connect two tables with a 1 to n (1:n) relation.  |
| setTable  | ```$crud->setTable('customers');``` | This is the database table that the developer will use to create the CRUD.  |

## Migration from Grocery CRUD v1 to v2 (from Codeigniter 3 to Codeigniter 4)

Although Grocery CRUD Community v2 was built by having in mind to not change the main logic of Grocery CRUD please have 
in mind that that it is not a backwards compatible version. We've always been backwards compatible from Codeigniter version
1 to version 3 but as Codeigniter had changed the approach as well (and we think that they did the right move) 
Grocery CRUD community edition is following the same direction.

If you are migrating from version 1 to 2 you will need to consider the below migration notes.  

* `add_fields` is now renamed to `addFields` and it only gets an array as argument 
* `clone_fields` is now renamed to `cloneFields` and it only gets an array as argument 

For more information, visit http://www.grocerycrud.com
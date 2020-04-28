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
| setAdd  | ```$crud->setAdd();``` | Setting the insert functionality. This function is rare to use as the default is already enabled.  |
| setApiUrlPath  | ```$crud->setApiUrlPath(site_url('/example/index'), site_url('/'));``` | Change the default API URL path and instead use the provided URL. Useful when we use Routes. |
| setClone  | ```$crud->setClone();``` | Enabling the clone functionality for the datagrid. Clone is basically copying all the data to an insert form. |
| setDelete  | ```$crud->setDelete();``` | Setting the delete functionality. This function is rare to use as the default is already enabled.. |
| setEdit  | ```$crud->setEdit();``` | Setting the update functionality. This function is rare to use as the default is already enabled. |
| setExport  | ```$crud->setExport();``` | Setting the export functionality. This function is rare to use as the default is already enabled. |
| setLangString  | ```$crud->setLangString('action_delete', 'Destroy');``` | Change any handle of the translation. |
| setLanguage  | ```$crud->setLanguage('Greek');``` | Set the language of the CRUD. All the languages that Grocery CRUD supports are listed at the [Languages Support](#languages-support) section. |
| setModel  | ```$crud->setModel(model('App\Models\MyCustomModel'));``` | Changing the default model with a custom one. |
| setPrimaryKey  | ```$crud->setPrimaryKey('reference_id', 'products');``` | Set manually the primary key for a table. |
| setPrint  | ```$crud->setPrint();``` | Setting the print functionality. This function is rare to use as the default is already enabled. |
| setRead  | ```$crud->setRead();``` | In order to enable the “View” button at your grid you will need to use the function setRead. The view of the form (read only) is false by default. |
| setRelation  | ```$crud->setRelation('officeCode', 'offices', 'city');``` | This is the function that is used to connect two tables with a 1 to n (1:n) relation.  |
| setRelationNtoN  | ```$crud->setRelationNtoN('actors', 'film_actor', 'actor', 'film_id', 'actor_id', 'fullname');``` | A connection for 3 tables with n-n relation (also known as n:n or m:n).  |
| setRule  | <code>$crud->setRule('username', 'Username', 'required&#124;valid_email');</code> | The setRule function is used to set a validation rule at the backend. Same as Codeigniter 4 [setRule](https://codeigniter4.github.io/userguide/libraries/validation.html#setrule)  |
| setSubject  | ```$crud->setSubject('Customer', 'Customers');``` | Set a subject title for all the CRUD operations for the current CRUD.  |
| setTable  | ```$crud->setTable('customers');``` | This is the database table that the developer will use to create the CRUD.  |
| setTexteditor  | ```$crud->setTexteditor(['description', 'full_description']);``` |  Specifying the fields that will open with a texteditor (ckeditor). |
| setTheme  | ```$crud->setTheme('datatables');``` |  The setTheme is used in order to change the default theme (flexigrid). |
| uniqueFields  | ```$crud->uniqueFields(['url', 'reference_id']);``` |  Check if the data for the specified fields are unique. This is used at the insert and the update operation. |
| unsetAdd  | ```$crud->unsetAdd();``` |  Removing the insert functionality at the current CRUD. |
| unsetAddFields  | ```$crud->unsetAddFields(['address_1', 'address_2', 'credit_limit']);``` |  Unset (do not display) the specified fields for the insert form. |
| unsetBootstrap  | ```$crud->unsetBootstrap();``` |  Do not load Bootstrap CSS. This is used when the Bootstrap CSS is already loaded at the template. |
| unsetClone  | ```$crud->unsetClone();``` |  The method unsetClone is removing completely the Clone operation for the end-user. |
| unsetTexteditor  | ```$crud->unsetTexteditor(['description', 'full_description']);``` |  Unsets the texteditor for the selected fields. This function is really rare to use as by default there is not any load of the texteditor for optimising purposes. |

## Languages Support

So far Grocery CRUD is translated into 36 languages:

- Afrikaans
- Arabic
- Bengali
- Bulgarian
- Catalan
- Chinese
- Croatian
- Czech
- Danish
- Dutch
- English
- French
- German
- Greek
- Hindi
- Hungarian
- Indonesian
- Italian
- Japanese
- Korean
- Lithuanian
- Mongolian
- Norwegian
- Persian
- Polish
- pt-BR.Portuguese
- pt-PT.Portuguese
- Romanian
- Russian
- Slovak
- es-UY.Spanish
- Spanish
- Thai
- Turkish
- Ukrainian
- Vietnamese

Thank you all for the support of translating 😄

## Themes support

- flexigrid (default)
- datatables
- bootstrap (only after [purchase](https://www.grocerycrud.com/bootstrap-theme))
- boostrap-v4 (only after [purchase](https://www.grocerycrud.com/bootstrap-theme))

## Migration from Grocery CRUD v1 to v2 (from Codeigniter 3 to Codeigniter 4)

Although Grocery CRUD Community v2 was built by having in mind to not change the main logic of Grocery CRUD please have 
in mind that that it is not a backwards compatible version. We've always been backwards compatible from Codeigniter version
1 to version 3 but as Codeigniter had changed the approach as well (and we think that they did the right move) 
Grocery CRUD community edition is following the same direction.

If you are migrating from version 1 to 2 you will need to consider the below migration notes.  

### Renaming of functions

* `add_fields` is now renamed to `addFields` and it only gets an array as an argument 
* `clone_fields` is now renamed to `cloneFields` and it only gets an array as an argument 
* `columns` now only gets an array as an argument
* `set_rules` is now renamed to `setRule` and it is supporting only one rule at the time (currently there is no ability 
to add multiple rules at once)

### Removed features/functions
* `set_field_upload` is now removed. The upload functionality was a feature that was causing security issues as it 
could work only to a public folder and the uploader was not up to date and it was causing confusion to the developers 
that just wanted to see it working and unfortunately they couldn't.
* Removing the ability to use Grocery CRUD as preloaded library. For example `$this->grocery_crud->render();`. This was
 causing bugs and unwanted issues as the library most of the cases wasn't initialised correctly.  

### Changing default configurations/values

* "Read" and "Clone" feature is disabled by default. You can enable them by adding `$crud->setRead();` or 
`$crud->setClone();` on your CRUD.
* By default texteditor is disabled for performance purposes. In case you would like to enable texteditor you will 
need to specify which fields with `$crud->setTexteditor`

For more information, visit http://www.grocerycrud.com
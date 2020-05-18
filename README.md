Grocery CRUD community edition for Codeigniter 4
=============

# Installation guide

The installation is really easy. You just copy all the files to your project and you are ready to use grocery CRUD!

By the end of the installation, your folder structure should look similar to this: (new folders/files are with bold)

<pre>├── app
│   ├── Common.php
│   ├── Config
│   │   ├── App.php
│   │   ├── Autoload.php
│   │   ├── ...
│   │   ├── Format.php
│   │   ├── <strong>GroceryCrud.php</strong>
│   │   ├── Honeypot.php
│   │   ├── ...
│   │   └── View.php
│   ├── Controllers
│   │   ├── BaseController.php
│   │   ├── <strong>Examples.php</strong>
│   │   └── Home.php
│   ├── Database
│   ├── ...
│   ├── Libraries
│   │   └── <strong>GroceryCrud.php</strong>
│   ├── Models
│   │   └── <strong>GroceryCrudModel.php</strong>
│   ├── ThirdParty
│   ├── Views
│   │   ├── errors
│   │   ├── <strong>example.php</strong>
│   │   └── welcome_message.php
│   └── index.html
├── public
│   ├── <strong>assets</strong>
│   │   ├── <strong>grocery_crud</strong>
│   │   ├── <strong>index.html</strong>
│   │   └── <strong>uploads</strong>
│   ├── favicon.ico
│   ├── index.php
│   └── robots.txt
├── spark
├── system
└── writable</pre>

The app/Controllers/Examples.php will look like this:

	<?php namespace App\Controllers;

	use App\Libraries\GroceryCrud;

	class Examples extends BaseController
	{
        public function customers_management()
	    {
	        $crud = new GroceryCrud();

            $crud->setTable('customers');

            $output = $crud->render();

            return $this->_exampleOutput($output);
	    }

	    private function _exampleOutput($output = null) {
	        return view('example', (array)$output);
	    }
	}

The only required configurations is to add your database credentials into a .env file  if you haven't already done that.

In order to access the URL file for customers_management your URL will look something like this:

http://www.example.com/index.php/examples/customers_management

or:

http://www.example.com/examples/customers_management

The variable $output is an object that always includes the following properties - output, js_files, css_files. 
Below you see an example of a print_r of a variable `$output` :

    stdClass Object
    (
        [output] => Your output will appear here....
        [js_files] => Array
            (
                [32fd432b4478200b5aacd62b65d5bdc269337910] => http://localhost/grocery-crud-codeigniter-4/public/assets/grocery_crud/js/jquery-1.11.1.min.js
                [d04ba7f0d55dda1d4ba9b6532414c653c58b0318] => http://localhost/grocery-crud-codeigniter-4/public/assets/grocery_crud/js/common/list.js
                [2d2b031fb606852768dc4c9a3c457545558cc924] => http://localhost/grocery-crud-codeigniter-4/public/assets/grocery_crud/themes/flexigrid/js/cookies.js
                [6629a324ade6d489aff77292cb02e31d9188a6bb] => http://localhost/grocery-crud-codeigniter-4/public/assets/grocery_crud/themes/flexigrid/js/flexigrid.js
                [5238a822ff2c6cced38a61590ac6debcc847bc0b] => http://localhost/grocery-crud-codeigniter-4/public/assets/grocery_crud/js/jquery_plugins/jquery.form.min.js
                [41101518af3f8fb416f60152aa019d963ae9293b] => http://localhost/grocery-crud-codeigniter-4/public/assets/grocery_crud/js/jquery_plugins/jquery.numeric.min.js
                [8823261dedf8eda49cfa2a7a528b5182350a90ae] => http://localhost/grocery-crud-codeigniter-4/public/assets/grocery_crud/themes/flexigrid/js/jquery.printElement.min.js
                [2ea588263ae884c476a96f40dc6cedd5316bbd57] => http://localhost/grocery-crud-codeigniter-4/public/assets/grocery_crud/js/jquery_plugins/ui/jquery-ui-1.10.3.custom.min.js
            )
        [css_files] => Array
            (
                [f1731e27afe02ab899b16daf8ae4a5ac8ac05d4e] => http://localhost/grocery-crud-codeigniter-4/public/assets/grocery_crud/themes/flexigrid/css/flexigrid.css
                [3e3f44ffabdcdd9017fa9db5262ce0465dde1322] => http://localhost/grocery-crud-codeigniter-4/public/assets/grocery_crud/css/ui/simple/jquery-ui-1.10.1.custom.min.css
            )
    )
    
The view at `app/Views/example.php` is a simple Codeigniter view file and includes the below code:

	<!DOCTYPE html>
	<html>
	<head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php 
	foreach($css_files as $file): ?>
		<link type="text/css" rel="stylesheet" href="<?php echo $file; ?>" />
	<?php endforeach; ?>
	</head>
	<body>
		<div>
		    <a href='<?php echo site_url('examples/customers_management')?>'>Customers</a> |
		    <a href='<?php echo site_url('examples/orders_management')?>'>Orders</a> |
		    <a href='<?php echo site_url('examples/products_management')?>'>Products</a> |
		    <a href='<?php echo site_url('examples/offices_management')?>'>Offices</a> | 
		    <a href='<?php echo site_url('examples/employees_management')?>'>Employees</a> |		 
		    <a href='<?php echo site_url('examples/film_management')?>'>Films</a>
		</div>
		<div style='height:20px;'></div>  
	    <div style="padding: 10px">
			<?php echo $output; ?>
	    </div>
	    <?php foreach($js_files as $file): ?>
	        <script src="<?php echo $file; ?>"></script>
	    <?php endforeach; ?>
	</body>
	</html>

    
### Default Routes

### Installation Troubleshooting 


# Usage example

### Simplest example

	$crud = new GroceryCrud();
	$crud->setTable('customers');
	$output = $crud->render();

### Most common usage example

    $crud = new GroceryCrud();
    $crud->setTable('customers')
        ->setSubject('Customer', 'Customers')
        ->columns(['customerName', 'contactLastName', 'phone', 'city', 'country', 'creditLimit'])
        ->displayAs('customerName', 'Name')
        ->displayAs('contactLastName', 'Last Name')
        ->fields(['customerName', 'contactLastName', 'phone', 'city', 'country', 'creditLimit'])
        ->requiredFields(['customerName', 'contactLastName']);
    
    $output = $crud->render();

# API and Functions list

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
| like  | ```$crud->like('contact_last_name', 'Tse');``` | Filter the queries with a extra where LIKE statement. |
| readFields  | ```$crud->readFields(['first_name', 'last_name', 'fullname', 'address']);``` | The fields that will be visible when the end-user navigates to the view form.  |
| render  | ```$output = $crud->render();``` | This is the most basic function. In other words this means “make it work”.  |
| requiredFields  | ```$crud->requiredFields(['first_name', 'last_name']);``` | The most common validation. Checks is the field provided by the user is empty.  |
| setActionButton  | [Example](#setactionbutton) | Adding extra action buttons to the rows of the datagrid.  |
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
| unsetBackToDatagrid  | ```$crud->unsetBackToDatagrid();``` |  Unsets everything that has to do with buttons or links with go back to datagrid message |
| unsetBootstrap  | ```$crud->unsetBootstrap();``` |  Do not load Bootstrap CSS. This is used when the Bootstrap CSS is already loaded at the template. |
| unsetClone  | ```$crud->unsetClone();``` |  The method unsetClone is removing completely the Clone operation for the end-user. |
| unsetCloneFields  | ```$crud->unsetCloneFields(['address_1', 'address_2', 'credit_limit']);``` |  Unset (do not display) the specified fields from the clone form. |
| unsetColumns | ```$crud->unsetColumns(['address_1', 'address_2', 'credit_limit']);``` | Unset (do not display) the specified columns. |
| unsetDelete | ```$crud->unsetDelete();```| Unset (and do not display) the delete functionality (also unsetting the delete multiple functionality) |
| unsetEdit | ```$crud->unsetEdit();``` | Removing the edit operation for the end-user (from the frontend and the backend) |
| unsetEditFields | ```$crud->unsetEditFields(['address_1', 'address_2', 'credit_limit']);``` | Unset (do not display) the specified fields for the update form. |
| unsetExport | ```$crud->unsetExport();``` | Removing the export functionality for the current CRUD. |
| unsetFields | ```$crud->unsetFields(['address_1', 'address_2', 'credit_limit']);``` | Unset (do not display) the specified fields for insert, update, clone and view form. This method is simply combining the methods: unsetAddFields, unsetEditFields, unsetCloneFields, unsetReadFields. |
| unsetJquery | ```$crud->unsetJquery();``` | Do not load jQuery. This is used when jQuery is already loaded at the template. |
| unsetJqueryUi | ```$crud->unsetJqueryUi();``` | Do not load jQuery UI. This is used when the jQuery UI (CSS and JS) is already loaded at the template. |
| unsetOperations | ```$crud->unsetOperations();``` | Removing all the permissions for any operation (expect print and export) for the end-user. |
| unsetPrint | ```$crud->unsetPrint();``` | The method unsetPrint is removing completely the Print operation for the end-user. |
| unsetRead | ```$crud->unsetRead();``` | The method unsetRead is removing completely the Read operation for the end-user. |
| unsetReadFields | ```$crud->unsetReadFields(['address_1', 'address_2', 'credit_limit']);``` | Unset (do not display) the specified fields for the view (read only) form. |
| unsetTexteditor  | ```$crud->unsetTexteditor(['description', 'full_description']);``` |  Unsets the texteditor for the selected fields. This function is really rare to use as by default there is not any load of the texteditor for optimising purposes. |
| where  | ```$crud->like('country', 'USA');``` | Filter the queries with an extra where statement. |

### Callback Functionality (Changing or enhancing the default behavior with callbacks)

| Function name  | Example | Small description |
| ------------- | ------------- | ------------- |
| callbackAddField  | [Example](#callbackaddfield) |  A callback that is used in case you need to create a custom field for the add form. |
| callbackAfterDelete  | [Example](#callbackafterdelete) |  The callback that will be used right after the delete. |
| callbackAfterInsert  | [Example](#callbackafterinsert) | The callback that will be used right after the insert of the data. |
| callbackAfterUpdate  | [Example](#callbackafterupdate) |  The callback that will be used right after the update of the data. |
| callbackBeforeDelete  | [Example](#callbackbeforedelete) |  The callback will be triggered before the delete functionality. |
| callbackBeforeInsert  | [Example](#callbackbeforeinsert) |  The callback is used in cases we need to add or change data before the insert functionality. |
| callbackBeforeUpdate  | [Example](#callbackbeforeupdate) | The callback is used in cases we need to add or change data before the update functionality. |
| callbackCloneField  | [Example](#callbackclonefield) | A callback that is used in case you need to create a custom field for the clone form. |
| callbackColumn  | [Example](#callbackcolumn) | The method callbackColumn is the transformation of the data for a column at the datagrid. |
| callbackDelete  | [Example](#callbackdelete) | The basic usage of callbackDelete is when we want to replace the default delete functionality. |
| callbackEditField  | [Example](#callbackeditfield) | A callback that is used in case you need to create a custom field for the edit/update form. |
| callbackInsert  | [Example](#callbackinsert) | The callback is used when we need to replace the default functionality of the insert. |
| callbackReadField  | [Example](#callbackreadfield) | This is a callback in order to create a custom field at the read/view form. |
| callbackUpdate  | [Example](#callbackupdate) | The callback is used when we need to replace the default update functionality. |

# Examples 

#### setActionButton

    $crud->setActionButton('User Avatar', 'el el-user', function ($primaryKey) { 
        return site_url('/view_avatar/' . $primaryKey); 
    }, true);

### callbackAddField

Simple example:

    $crud->callbackAddField('contact_telephone_number', function () {
        return '<input class="form-control" name="contact_telephone_number" id="something-unique" />';
    });

Example with the `use` keyword: 
    
    $username = 'john';
    $userId = '123';
    $crud->callbackAddField('contact_telephone_number', function () use ($username, $userId) {
        // You have access now at the extra custom variable $username and $userId
        return '+30 <input name="contact_telephone_number"  /> for: ' . $username . '(id: ' . $userId . ')' ;
    });

### callbackAfterDelete

    $crud->callbackAfterDelete(function ($stateParameters) {
        /* $stateParameters will be an object with the below structure:
         * (object)[
         *      'primaryKeyValue' => '1234'
         * ]
         */
        
        // Your code here    

        return $stateParameters;
    });
    
⚠️ Warning: Please have in mind that the callbackAfterDelete is called right after the delete operation. This means that:
 1. The record/row that you are looking for with the primary key will not exist if you have a query within the callback
 2. If you would like to have a soft delete then consider using [callbackDelete](#callbackdelete) instead


### callbackAfterInsert

    $crud->callbackAfterInsert(function ($stateParameters) {
        /* $stateParameters will be an object with the below structure:
         * (object)[
         *      'data' => [ 
         *            // Your inserted data
         *            'customer_fist_name' => 'John',
         *            'customer_last_name' => 'Smith',
         *            ... 
         *       ],
         *      'insertId' => '123456'
         * ]
         */
    
        // Your custom code goes here. 
                
        return $stateParameters;
    });

### callbackAfterUpdate

    $crud->callbackAfterUpdate(function ($stateParameters) {
        /* $stateParameters will be an object with the below structure:
         * (object)[
         *      'data' => [ 
         *            // Your posted data
         *            'customer_fist_name' => 'John',
         *            'customer_last_name' => 'Smith',
         *            ... 
         *      ],
         *      'primaryKeyValue' => '1234',
         * ]
         */
    
        // Your code goes here.
    
        return $stateParameters;
    });

### callbackBeforeDelete

        $crud->callbackBeforeDelete(function ($stateParameters) use ($userId) {
            /* $stateParameters will be an object with the below structure:
             * (object)[
             *      'primaryKeyValue' => '1234'
             * ]
             */
             
             $model = new DemoExampleModel();
             
             if (!$model->userCanRemoveCustomerWithId($userId, $stateParameters->primaryKeyValue)) {
                return false;
             }

            return $stateParameters;
        });

### callbackBeforeInsert

    $crud->callbackBeforeInsert(function ($stateParameters) {
        /* $stateParameters will be an object with the below structure:
         * (object)[
         *      'data' => [ 
         *            // Your inserted data
         *            'customer_fist_name' => 'John',
         *            'customer_last_name' => 'Smith',
         *            ... 
         *       ]
         * ]
         */
    
        // Your custom code goes here. 
        // returning false (e.g. return false;) will stop the form to continue 
        // and hence we will not insert any data. This callback can also be 
        // used as an extra validation check
                
        // Example that we can't insert a rejected entry if the message is empty                
        if ($stateParameters->data['status'] === 'Rejected' && $stateParameters->data['message'] === '') {
              return false;
        }
                
        return $stateParameters;
    });

### callbackBeforeUpdate

    $crud->callbackBeforeUpdate(function ($stateParameters) {
        /* $stateParameters will be an object with the below structure:
         * (object)[
         *      'data' => [ 
         *            // Your posted data
         *            'customer_fist_name' => 'John',
         *            'customer_last_name' => 'Smith',
         *            ... 
         *      ],
         *      'primaryKeyValue' => '1234',
         * ]
         */
    
        // Your custom code goes here. 
        // returning false (e.g. return false;) will stop the form to continue 
        // and hence we will not update any data. This callback can also be 
        // used as an extra validation check
                
        // Example that we can't update a rejected entry if the message is empty                
        if ($stateParameters->data['status'] === 'Rejected' && $stateParameters->data['message'] === '') {
              return false;
        }
                
        return $stateParameters;
    });

### callbackCloneField

    $crud->callbackCloneField('telephone_number', function ($fieldValue, $primaryKeyValue, $rowData) {
        return '+30 <input name="telephone_number" value="' . $fieldValue . '"  />';
    });

### callbackColumn

    $crud->callbackColumn('menu_title', function ($value, $row) {
        return "<a href='" . site_url('menu/' . $row->id) . "'>$value</a>";
    });

### callbackDelete

    $crud->callbackDelete(function ($stateParameters) {
        /* $stateParameters will be an object with the below structure:
         * (object)[
         *      'primaryKeyValue' => '1234'
         * ]
         */
        
        // Your code goes here
       
        return $stateParameters;
    });
    
The most commmon example of usage is the "soft" delete:

    $crud->callbackDelete(function ($stateParameters) {

        $model = new DemoExampleModel();

        // Soft delete
        $model->customersSoftDelete($stateParameters->primaryKeyValue);

        return $stateParameters;
    });
    
where the function customersSoftDelete at our model is the below:

    /**
     * Update customer record with deleted = 1 instead of removing the entry from database
     *
     * @param string $primaryKeyValue
     */
    public function customersSoftDelete($primaryKeyValue) {
        $this->db->table('customers')->update(
            ['deleted' => '1'],
            ['customerNumber' => $primaryKeyValue]);
    }
    

### callbackEditField

    $crud->callbackEditField('telephone_number', function ($fieldValue, $primaryKeyValue, $rowData) {
        return '+30 <input name="telephone_number" value="' . $fieldValue . '"  />';
    });

### callbackInsert

    $crud->callbackInsert(function ($stateParameters) {
        /* $stateParameters will be an object with the below structure:
         * (object)[
         *      'data' => [ 
         *            // Your inserted data
         *            'customer_fist_name' => 'John',
         *            'customer_last_name' => 'Smith',
         *            ... 
         *       ]
         *
         * ]
         */
    
        // Your code goes here. Have in mind that you are replacing the default insert functionality so your
        // code will need to include some insert methods
    
        // If you would like to return the inserted Id you should add it to the object like this
        $stateParameters->insertId = '1234';
    
        return $stateParameters;
    });

### callbackReadField

    $crud->callbackReadField('contact_telephone_number', function ($fieldValue, $primaryKeyValue, $rowData) {
        return '+30 ' . $fieldValue;
    });

### callbackUpdate

    $crud->callbackUpdate(function ($stateParameters) {
        /* $stateParameters will be an object with the below structure:
         * (object)[
         *      'data' => [ 
         *            // Your posted data
         *            'customer_fist_name' => 'John',
         *            'customer_last_name' => 'Smith',
         *            ... 
         *      ],
         *      'primaryKeyValue' => '1234',
         * ]
         */
    
        // Your code goes here.
    
        return $stateParameters;
    });


# Languages Support

So far Grocery CRUD has been translated into 36 languages:

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

# Themes support

- flexigrid (default)
- datatables
- bootstrap (only after [purchase](https://www.grocerycrud.com/bootstrap-theme))
- boostrap-v4 (only after [purchase](https://www.grocerycrud.com/bootstrap-theme))

# Migration from Grocery CRUD v1 to v2 (from Codeigniter 3 to Codeigniter 4)

**TL;DR** Ideally we highly recommend you to start from scratch! The usage logic hasn't change but almost 90% of the 
functions has!

**Notice:** Although Grocery CRUD Community v2 was built by having in mind to not change the main logic of Grocery CRUD please have 
in mind that Grocery CRUD version 2 **is not** backwards compatible. We've always been backwards compatible 
from Codeigniter version 1 to version 3; however as Codeigniter had changed the approach as well 
(and we believe that they've made the right choice) Grocery CRUD community edition is following the same direction.

If you are migrating from version 1 to 2 you will need to consider the below migration notes:

### Renaming of functions

* `add_fields` is now renamed to `addFields` and it only gets an array as an argument 
* `clone_fields` is now renamed to `cloneFields` and it only gets an array as an argument 
* `columns` now only gets an array as an argument
* `order_by` is now renamed to `defaultOrdering`
* `set_rules` is now renamed to `setRule` and it is supporting only one rule at the time. 
Currently, there is no ability to add multiple rules at once
* `default_ordering` is now renamed to `defaultOrdering`
* `display_as` is now renamed to `displayAs`
* `edit_fields` is now renamed to `editFields` and it only gets an array as an argument
* `field_type` is now renamed to `fieldType`
* `fields` is now renamed to `fields` and it only gets an array as an argument
* `read_fields` is now renamed to `readFields` and it only gets an array as an argument
* `required_fields` is now renamed to `requiredFields` and it only gets an array as an argument
* `set_action_button` is now renamed to `setActionButton` and almost all the parameters has changed. You can see an example at the section [setActionButton](#setactionbutton)
* `set_crud_url_path` is now renamed to `setApiUrlPath`
* `set_clone` is now renamed to `setClone`
* `set_lang_string` is now renamed to `setLangString`
* `set_language` is now renamed to `setLanguage`
* `set_model` is now renamed to `setModel` and it gets the model as an object parameter and not as a string
* `set_primary_key` is now renamed to `setPrimaryKey`
* `set_relation` is now renamed to `setRelation`
* `set_relation_n_n` is now renamed to `setRelationNtoN` and we've removed the ability to add a priority field as this 
was a very old outdated jquery plugin that was causing many issues (especially with newer versions of jQuery). We want
to revisit this section at the future
* `set_subject` is now renamed to `setSubject`
* `set_table` is now renamed to `setTable`
* `set_theme` is now renamed to `setTheme`
* `unique_fields` is now renamed to `uniqueFields` and it only gets an array as an argument
* `unset_add` is now renamed to `unsetAdd`
* `unset_add_fields` is now renamed to `unsetAddFields` and it only gets an array as an argument
* `unset_back_to_list` is now renamed to `unsetBackToDatagrid`
* `unset_bootstrap` is now renamed to `unsetBootstrap`
* `unset_clone` is now renamed to `unsetClone`
* `unset_clone_fields` is now renamed to `unsetCloneFields` and it only gets an array as an argument
* `unset_columns` is now renamed to `unsetColumns` and it only gets an array as an argument
* `unset_delete` is now renamed to `unsetDelete`
* `unset_edit` is now renamed to `unsetEdit`
* `unset_edit_fields` is now renamed to `unsetEditFields` and it only gets an array as an argument
* `unset_export` is now renamed to `unsetExport`
* `unset_fields` is now renamed to `unsetFields` and it only gets an array as an argument
* `unset_jquery` is now renamed to `unsetJquery`
* `unset_jquery_ui` is now renamed to `unsetJqueryUi`
* `unset_operations` is now renamed to `unsetOperations`
* `unset_print` is now renamed to `unsetPrint`
* `unset_read` is now renamed to `unsetRead`
* `unset_read_fields` is now renamed to `unsetReadFields` and it only gets an array as an argument
* `unset_texteditor` is now renamed to `unsetTexteditor` and it only gets an array as an argument

### New functions

* `setAdd`
* `setEdit`
* `setRead`
* `setDelete`
* `setExport`
* `setPrint`
* `setTexteditor`

### Removed features/functions
* `callback_field` is now removed. We are still having separate functions: `callbackAddField`, `callbackEditField`, 
`callbackCloneField`, `callbackReadField` but having one to trigger all of them was causing issues as every operation 
 usually needed different implementation.
* `set_field_upload` is now removed. The upload functionality was a feature that was causing security issues as it 
could work only to a public folder and the uploader was not up to date and it was causing confusion to the developers 
that just wanted to see it working and unfortunately they couldn't.
* Removing the ability to use Grocery CRUD as preloaded library. For example `$this->grocery_crud->render();`. This was
 causing bugs and unwanted issues as the library most of the cases wasn't initialised correctly. 
* `unset_list` is now removed. Unsetting the initial grid causing issues with the redirections and the URLs to not be 
able to change and this is causing a development and an end-user confusion. It is very possible that we will revisit 
`unset_list` at the future (probably renamed as `unsetDatagrid`)

### Changing default configurations/values

* "Read" and "Clone" feature is disabled by default. You can enable them by adding `$crud->setRead();` or 
`$crud->setClone();` on your CRUD.
* By default texteditor is disabled for performance purposes. In case you would like to enable texteditor you will 
need to specify which fields with `$crud->setTexteditor`
* `unsetOperations` (previously `unset_operations`) is not removing the print and the export ability

For more information, visit http://www.grocerycrud.com
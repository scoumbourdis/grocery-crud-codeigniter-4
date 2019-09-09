<?php namespace App\Controllers;

use App\Libraries\Grocery_CRUD;

class Example extends BaseController
{
	public function customers()
	{
	    $crud = new Grocery_CRUD();

	    $crud->set_table('customers');

	    $output = $crud->render();

		return $this->_example_output($output);
	}

    public function _example_output($output = null) {
        return view('example', $output);
    }


}

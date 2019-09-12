<?php namespace App\Controllers;

use App\Libraries\GroceryCrud;

class Examples extends BaseController
{
	public function customers_management()
	{
	    $crud = new GroceryCrud();

	    $crud->set_table('customers');

	    $output = $crud->render();

		return $this->_example_output($output);
	}

    public function _example_output($output = null) {
        return view('example', (array)$output);
    }


}

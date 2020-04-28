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

    function offices_management () {
        $crud = new GroceryCrud();

        $crud->setTheme('datatables');
        $crud->setTable('offices');
        $crud->setSubject('Office');
        $crud->requiredFields(['city']);
        $crud->columns(['city','country','phone','addressLine1','postalCode']);

        $output = $crud->render();

        return $this->_exampleOutput($output);
    }

    private function _exampleOutput($output = null) {
        return view('example', (array)$output);
    }


}

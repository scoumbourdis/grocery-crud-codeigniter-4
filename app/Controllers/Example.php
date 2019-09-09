<?php namespace App\Controllers;

use App\Libraries\Grocery_CRUD;

class Example extends BaseController
{
	public function index()
	{
	    new Grocery_CRUD();

		return view('welcome_message');
	}

}

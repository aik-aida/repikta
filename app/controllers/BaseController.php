<?php

class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

	protected function login()
	{
		$data = Input::only(['username', 'password']);
		$uname = $data['username'];
		$pass = $data['password'];
		
		if($uname=='repikta' && $pass=='aidasw112'){
			return View::make('test')
					->with('nama', '-- Aida Muflichah --');
		} else {
			return View::make('home');
		}

	}

}

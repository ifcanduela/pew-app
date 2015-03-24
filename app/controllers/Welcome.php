<?php 

namespace app\controllers;

class Welcome extends Base
{
	public function index($name = 'dude')
	{
        $this->view->title('Welcome!');
        
		return ['name' => $name];
	}
}

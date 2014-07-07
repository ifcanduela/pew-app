<?php 

namespace app\controllers;

class Welcome extends Base
{
	public function index($name = 'dude')
	{
		return ['name' => $name];
	}
}

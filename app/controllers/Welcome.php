<?php 

namespace app\controllers;

class Welcome extends \pew\Controller
{
	public function index($name = 'dude')
	{
		return ['name' => $name];
	}
}

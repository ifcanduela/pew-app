<?php

require_once 'functions.php';
require_once 'pew.class.php';
require_once 'pew_database.class.php';
require_once 'model.class.php';

class ModelTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Model
     */
    protected $object;

    public static function setUpBeforeClass()
    {
        # create a nice SQLite database, with a three tables and a few records
        $db = new PewDatabase(array(
                'engine' => 'sqlite',
                'file'   => ':memory:'
            ));

        $db->pdo->query('CREATE TABLE users (id INTEGER PRIMARY KEY, username TEXT)');
        $db->pdo->query('CREATE TABLE projects (id INTEGER PRIMARY KEY, title TEXT, user_id INTEGER)');
        $db->pdo->query('CREATE TABLE tasks (id INTEGER PRIMARY KEY, title TEXT, priority INTEGER, project_id INTEGER, assigned_to INTEGER)');

        $db->values(array('username' => 'admin'))->insert('users');
        $db->values(array('username' => 'projman'))->insert('users');
        $db->values(array('username' => 'developer'))->insert('users');

        $db->values(array('title' => 'project1', 'user_id' => 1))->insert('projects');
        $db->values(array('title' => 'project2', 'user_id' => 1))->insert('projects');
        $db->values(array('title' => 'project3', 'user_id' => 2))->insert('projects');
        $db->values(array('title' => 'project4', 'user_id' => 3))->insert('projects');
        $db->values(array('title' => 'project5', 'user_id' => 3))->insert('projects');

        $db->values(array('title' => 'task1',  'priority' => 1, 'project_id' => 1, 'assigned_to' => 1))->insert('tasks');
        $db->values(array('title' => 'task2',  'priority' => 1, 'project_id' => 1, 'assigned_to' => 2))->insert('tasks');
        $db->values(array('title' => 'task3',  'priority' => 2, 'project_id' => 1, 'assigned_to' => 3))->insert('tasks');
        $db->values(array('title' => 'task4',  'priority' => 1, 'project_id' => 1, 'assigned_to' => 1))->insert('tasks');
        $db->values(array('title' => 'task5',  'priority' => 3, 'project_id' => 2, 'assigned_to' => 2))->insert('tasks');
        $db->values(array('title' => 'task6',  'priority' => 5, 'project_id' => 2, 'assigned_to' => 3))->insert('tasks');
        $db->values(array('title' => 'task7',  'priority' => 1, 'project_id' => 3, 'assigned_to' => 1))->insert('tasks');
        $db->values(array('title' => 'task8',  'priority' => 2, 'project_id' => 3, 'assigned_to' => 2))->insert('tasks');
        $db->values(array('title' => 'task9',  'priority' => 2, 'project_id' => 3, 'assigned_to' => 3))->insert('tasks');
        $db->values(array('title' => 'task10', 'priority' => 3, 'project_id' => 3, 'assigned_to' => 1))->insert('tasks');

        Pew::set('PewDatabase', $db);
    }

    public static function tearDownAfterClass()
    {

    }

    public function test_construct()
    {
        $users  = new Model('users');
        $this->assertEquals('Model', get_class($users));
        $this->assertEquals(true, $users->db->table_exists('users'));
        $projects = new Model('projects');
        $this->assertEquals('Model', get_class($projects));
        $tasks = new Model('tasks');
        $this->assertEquals('Model', get_class($tasks));
    }

    public function test_add_child()
    {
        $users = new Model('users');
        $users->add_child('projects', 'user_id');
        $this->assertEquals('Model', get_class($users->projects));
    }

    public function test_add_parent()
    {
        $projects = new Model('projects');
        $projects->add_parent('users', 'user_id');
        $this->assertEquals('Model', get_class($projects->users));
    }

    public function test_remove_child()
    {
        $model = new Model('users');
        $model->add_child('users', 'user_id');
        $model->remove_child('users');
        
        //$this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function test_remove_parent()
    {
        $model = new Model('users');
        $model->add_parent('users', 'user_id');
        $model->remove_parent('users');
        
        //$this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function test_magic_get()
    {
        $projects = new Model('projects');
        $projects->add_parent('users', 'user_id');
        $projects->add_child('tasks', 'project_id');
        $this->assertEquals('Model', get_class($projects->users));
        $this->assertEquals('Model', get_class($projects->tasks));
    }

    public function test_magic_set()
    {
        $users = new Model('users');
        $projects = new Model('projects');
        $users->projects = $projects;
        $this->assertEquals($projects, $users->projects);
        $this->assertEquals('Model', get_class($users->projects));
    }

    public function test_magic_call()
    {
        $model = new Model('users');
        $user = $model->find_by_id(1);
        
        $this->assertEquals(1, $user['id']);
    }

    public function test_find()
    {
        $users = new Model('users');
        $user = $users->find(1);
        $this->assertTrue(is_array($user));
        $this->assertEquals('1', $user['id']);
        $this->assertEquals('admin', $user['username']);

        $no_user = $users->find(-1);
        $this->assertFalse($no_user);
    }

    public function test_find_all()
    {
        $model = new Model('users');
        $users = $model->find_all();
        $this->assertTrue(is_array($users));
        
        $user = $users[0];
        $this->assertEquals('1', $user['id']);
        $this->assertEquals('admin', $user['username']);

        $user = $users[1];
        $this->assertEquals('2', $user['id']);
        $this->assertEquals('projman', $user['username']);
    }

    public function test_count()
    {
        $model = new Model('users');
        $user_count = $model->count();
        $this->assertTrue(is_numeric($user_count));
        
        $user_count = $model->count(array('id' => array('>', 0)));
        $this->assertTrue(is_numeric($user_count));
    }

    public function test_save()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    public function test_delete()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    public function test_find_related()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    public function test_last_insert_id()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    public function test_select()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    public function test_where()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    public function test_limit()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    public function test_order_by()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    public function test_group_by()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }

    public function test_having()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
                'This test has not been implemented yet.'
        );
    }
}

<?php

use Phinx\Migration\AbstractMigration;

class CreateTableUsers extends AbstractMigration
{
    public function change()
    {
        $table = $this->table("users");

        $table->addColumn("username", "string")
              ->addColumn("password", "string")
              ->addColumn("email", "string")
              ->addColumn("login_token", "text", ["null" => true])
              ->addColumn("created", "integer")
              ->addColumn("updated", "integer", ["null" => true])
              ->addIndex(["username"], ["unique" => true])
              ->create();
    }
}

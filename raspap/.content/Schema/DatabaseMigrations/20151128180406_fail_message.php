<?php
use Phinx\Migration\AbstractMigration;

class FailMessage extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('interfaces');
        $table->addColumn('fail_message', 'string', [ 'limit' => 1024 ]);
        $table->save();
    }
}
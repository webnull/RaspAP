<?php
use Phinx\Migration\AbstractMigration;

class Interfaces extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('interfaces');
        $table->addColumn('name', 'string', [ 'limit' => 16 ]);
        $table->addColumn('type', 'string', [ 'limit' => 16 ]);
        $table->addColumn('role', 'string', [ 'limit' => 16 ]);
        $table->addColumn('daemons', 'string', [ 'limit' => 2048 ]);
        $table->addColumn('last_updated', 'datetime');
        $table->addIndex('name', [ 'unique' => true ]);
        $table->save();
    }
}
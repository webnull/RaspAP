<?php
use Phinx\Migration\AbstractMigration;

class PskCollection extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('mac_address');
        $table->addColumn('mac', 'string');
        $table->addColumn('title', 'string');
        $table->addIndex('mac', [ 'unique' => true ]);
        $table->save();
    }
}
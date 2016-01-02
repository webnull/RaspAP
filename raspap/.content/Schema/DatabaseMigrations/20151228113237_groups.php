<?php
use Phinx\Migration\AbstractMigration;

class Groups extends AbstractMigration
{
    public function change()
    {
        $table = $this->table('groups', ['id' => 'group_id']);
        $table->addColumn('group_name', 'string', [ 'limit' => 64 ]);
        $table->addColumn('group_title', 'string', [ 'limit' => 64 ]);
        $table->addColumn('group_created', 'datetime', [ 'default' => 'CURRENT_TIMESTAMP' ]);
        $table->addColumn('group_updated', 'datetime', [ 'null' => true ]);
        $table->addIndex([ 'group_name' ], [ 'unique' => true ]);
        $table->save();

        // add a foreign key to connect users <==> groups
        $users = $this->table('users');
        $users->addColumn('user_primary_group', 'integer');
        $users->addForeignKey('user_primary_group', 'groups', 'group_id', ['delete' => 'SET_NULL', 'update' => 'NO_ACTION']);
        $users->save();
    }
}

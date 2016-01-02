<?php
use Phinx\Migration\AbstractMigration;

/**
 * Class UsersTable
 *
 * @author Damian Kęska <damian@pantheraframework.org>
 * @package Panthera\modules\usersManagement\migrations
 */
class UsersTable extends AbstractMigration
{
    /**
     * Initially create a users table
     *
     * @author Damian Kęska <damian@pantheraframework.org>
     * @author Mateusz Warzyński <lxnmen@gmail.com>
     */
    public function change()
    {
        $table = $this->table('users', array('id' => 'user_id'));

        $table->addColumn('user_login',      'string',   array('limit' => 32))
              ->addColumn('user_passwd',     'string',   array('limit' => 64))
              ->addColumn('user_first_name', 'string',   array('limit' => 64))
              ->addColumn('user_last_name',  'string',   array('limit' => 64))
              ->addColumn('user_email',      'string',   array('limit' => 32))
              ->addColumn('user_created', 'datetime', array('null' => true))
              ->addColumn('user_updated', 'datetime', array('null' => true))
              ->create();

        /*
         * todo: fix default value for `user_created` database field
         *
         * MySQL database throws error if default value for `user_created` is defined:
         *      ->addColumn('user_created', 'datetime'), array('default' => 'CURRENT_TIMESTAMP'),
         *
         * Throws: SQLSTATE[42000]: Syntax error or access violation: 1067 Invalid default value for 'user_created'
         */
    }
}

<?php
use Phinx\Migration\AbstractMigration;

/**
 * Class ConfigurationTable
 *
 * @author Damian Kęska <damian@pantheraframework.org>
 * @package Panthera\modules\configuration\migrations
 */
class ConfigurationTable extends AbstractMigration
{
    /**
     * Create a "configuration" table
     *
     * @author Damian Kęska <damian@pantheraframework.org>
     */
    public function change()
    {
        $table = $this->table('configuration', array('id' => 'configuration_id'));

        $table->addColumn('configuration_key',     'string', array('limit' => 32))
              ->addColumn('configuration_value',   'string', array('limit' => 4096))
              ->addColumn('configuration_section', 'string', array('limit' => 32))
              ->create();
    }
}

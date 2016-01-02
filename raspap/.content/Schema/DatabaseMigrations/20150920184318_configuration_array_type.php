<?php
use Phinx\Migration\AbstractMigration;

/**
 * Class ConfigurationTable
 *
 * @author Damian Kęska <damian@pantheraframework.org>
 * @package Panthera\Modules\Configuration\Migrations
 */
class ConfigurationArrayType extends AbstractMigration
{
    /**
     * Add a new column configuration_is_json of boolean type
     */
    public function change()
    {
        $config = $this->table('configuration');
        $config->addColumn('configuration_is_json', 'boolean', [
            'default' => 0,
        ]);
        $config->save();
    }
}

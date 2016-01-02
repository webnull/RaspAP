<?php
use Phinx\Migration\AbstractMigration;

class TestUserAndGroup extends AbstractMigration
{
    public function up()
    {
        $this->execute('insert into users ("user_id", "user_login", "user_passwd", "user_email", "user_created", "user_updated", "user_primary_group") values ("1", "root", "$2y$12$awZOz8KzBRVDrxmPLOMAIuGlIG0EKRFWiJ5gLEdgqOjm69rBdQigW", "root@localhost", "2015-12-28 13:24:27", NULL, "1");');
        $this->execute('insert into groups ("group_id", "group_name", "group_title", "group_created", "group_updated") values ("1", "Users", "Users", "2015-12-28 13:20:54", NULL);');
    }

    public function down()
    {
        $this->execute('delete from users where user_id = 1 AND user_login = "root" AND user_created = "2015-12-28 13:24:27";');
        $this->execute('delete from groups where group_id = 1 AND group_name = "Users" AND group_created = "2015-12-28 13:20:54";');
    }
}

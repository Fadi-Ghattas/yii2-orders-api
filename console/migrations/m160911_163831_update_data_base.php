<?php

use yii\db\Migration;

class m160911_163831_update_data_base extends Migration
{
    public function up()
    {
        $this->addColumn('areas','active',$this->boolean()->notNull()->defaultValue(0));
        $this->addColumn('cuisines','image',$this->string(255)->null());
    }

    public function down()
    {
        echo "m160911_163831_update_data_base cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}

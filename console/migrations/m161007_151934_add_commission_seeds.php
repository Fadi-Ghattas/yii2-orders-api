<?php

use yii\db\Migration;

class m161007_151934_add_commission_seeds extends Migration
{
    public function up()
    {
        $this->insert('commissions', ['from' => 0, 'to' => 20, 'value'=> 0 ,'created_at' => date('Y-m-d H:i:s')]);
        $this->insert('commissions', ['from' => 20, 'to' => 40, 'value'=> 1.30 ,'created_at' => date('Y-m-d H:i:s')]);
        $this->insert('commissions', ['from' => 40, 'to' => 60, 'value'=> 2.30 ,'created_at' => date('Y-m-d H:i:s')]);
        $this->insert('commissions', ['from' => 60, 'to' => 100, 'value'=> 3.30 ,'created_at' => date('Y-m-d H:i:s')]);
        $this->insert('commissions', ['from' => 100, 'to' => 1000, 'value'=> 4.30 ,'created_at' => date('Y-m-d H:i:s')]);
    }

    public function down()
    {
        echo "m161007_151934_add_commission_seeds cannot be reverted.\n";

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

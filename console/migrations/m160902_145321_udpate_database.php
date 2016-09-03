<?php

use yii\db\Migration;

class m160902_145321_udpate_database extends Migration
{
    public function up()
    {
//        $this->addColumn('order_item_addon', 'price', $this->decimal(7, 2)->null()->defaultValue(0));
//        $this->addColumn('order_item_addon', 'quantity', $this->integer(11)->null()->defaultValue(0));
//        $this->addColumn('order_item_choices', 'price', $this->decimal(7, 2)->null()->defaultValue(0));

        if (!$this->tableExists('countries')) {
            $this->createTable('countries',
                ['id' => $this->primaryKey()->unsigned()->notNull(),
                    'name' => $this->string(255)->notNull(),
                    'ISO_code' => $this->string(25)->notNull(),
                    'created_at' => $this->timestamp()->null(),
                    'updated_at' => $this->timestamp()->null(),
                    'deleted_at' => $this->timestamp()->null()
                ], 'ENGINE = InnoDB');
        }

        $this->addColumn('restaurants', 'country_id', $this->integer(11)->unsigned()->notNull());

        $this->createIndex('IDX_Restaurants_Countries', 'restaurants', 'country_id');
        $this->addForeignKey('FK_Restaurants_Countries', 'restaurants', 'country_id', 'countries', 'id');

        $this->dropColumn('clients', 'reg_id');

        $this->dropColumn('restaurants', 'contact_number');
        $this->addColumn('restaurants', 'owner_number', $this->string(255)->notNull());
    }

    public function down()
    {
        echo "m160902_145321_udpate_database cannot be reverted.\n";

        return false;
    }

    public function tableExists($tableName)
    {
        return in_array($tableName, Yii::$app->db->schema->tableNames);
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
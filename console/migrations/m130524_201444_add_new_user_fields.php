<?php

use yii\db\Migration;

class m130524_201444_add_new_user_fields extends Migration
{
    public function up()
    {
        $this->addColumn('restaurants', 'user_id', $this->integer(11)->notNull());
        $this->addColumn('clients', 'user_id', $this->integer(11)->notNull());

        //Users FK'S
        $this->createIndex('IDX_Restaurant_User', 'restaurants', 'user_id');
        $this->addForeignKey('FK_Restaurant_User', 'restaurants', 'user_id' , 'user', 'id');

        $this->createIndex('IDX_Clients_User', 'clients', 'user_id');
        $this->addForeignKey('FK_Clients_User', 'clients', 'user_id' , 'user', 'id');

        //Creating Roles
        $auth = Yii::$app->authManager;
        $auth->init();
        $admin =  $auth->createRole("admin");
        $RM = $auth->createRole("restaurant_manager");
        $client = $auth->createRole("client");
        $auth->add($admin);
        $auth->add($RM);
        $auth->add($client);
    }

    public function down()
    {

    }
}

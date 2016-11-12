<?php

use yii\db\Migration;

class m161104_152146_add_one_signal_keys extends Migration
{
	public function up()
	{
		$this->insert('setting', ['collection' => 'general', 'key' => 'one_signal_app_id', 'value' => '5ad49ccd-01c5-4fd6-9b72-34214d4f9554']);
		$this->insert('setting', ['collection' => 'general', 'key' => 'one_signal_api_url', 'value' => 'https://onesignal.com/api/v1/notifications']);
		$this->insert('setting', ['collection' => 'general', 'key' => 'one_signal_api_Authorization', 'value' => 'ZDkwMDBiZWQtMTNlYS00YzgzLWFhMjYtYzAwZmI4ZWVmZjEz']);
	}

	public function down()
	{
		echo "m161104_152146_add_one_signal_keys cannot be reverted.\n";

		return FALSE;
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

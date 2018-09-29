<?php

use yii\db\Schema;
use yii\db\Migration;

class m130524_201442_init extends Migration
{
    public function safeUp()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }
        try {
            $this->createTable('{{%user}}', [
                'id' => $this->primaryKey(),
                'username' => $this->string(30)->notNull()->unique(),
                'email' => $this->string(50)->defaultValue(''),
                'password_hash' => $this->string(100)->notNull(),
                'password_reset_token' => $this->string(100)->defaultValue(''),
                'status' => $this->smallInteger(4)->defaultValue(1),
                'created' => $this->dateTime(),
                'created_by' => $this->integer(),
                'updated' => $this->dateTime(),
                'updated_by' => $this->integer(),
            ], $tableOptions);
        } catch (\Exception $e) {
            
        }
    }

    public function safeDown()
    {
        $this->dropTable('{{%user}}');
    }
}

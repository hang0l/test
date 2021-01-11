<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%users}}`.
 */
class m201222_113850_create_player_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%player}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%player}}');
    }
}

<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%game}}`.
 */
class m201220_184653_create_game_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%game}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(),
            'shape' => $this->string(),
            'xCoord' => $this->integer(),
            'yCoord' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%game}}');
    }
}

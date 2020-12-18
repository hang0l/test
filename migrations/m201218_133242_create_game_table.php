<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%game}}`.
 */
class m201218_133242_create_game_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%game}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(),
            'x_coord' => $this->integer(),
            'y_coord' => $this->integer(),
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

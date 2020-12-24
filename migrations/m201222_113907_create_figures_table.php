<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%figures}}`.
 */
class m201222_113907_create_figures_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%figures}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'shape' => $this->string(),
            'x' => $this->integer()->defaultValue(rand(50, 750)),
            'y' => $this->integer()->defaultValue(rand(150, 550)),
        ]);

        $this->createIndex(
            'idx-figures-user_id',
            'figures',
            'user_id'
        );

        $this->addForeignKey(
            'fk-figures-user_id',
            'figures',
            'user_id',
            'users',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%figures}}');
    }
}

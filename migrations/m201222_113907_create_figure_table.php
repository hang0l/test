<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%figure}}`.
 */
class m201222_113907_create_figure_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%figure}}', [
            'id' => $this->primaryKey(),
            'player_id' => $this->integer(),
            'shape' => $this->string(),
            'x' => $this->float()->defaultValue(rand(50, 750)),
            'y' => $this->float()->defaultValue(rand(150, 550)),
        ]);

        $this->createIndex(
            'idx-figure-player_id',
            'figure',
            'player_id'
        );

        $this->addForeignKey(
            'fk-figure-player_id',
            'figure',
            'player_id',
            'player',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%figure}}');
    }
}

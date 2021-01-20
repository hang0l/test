<?php

use yii\db\Migration;

/**
 * Handles adding columns to table `{{%figure}}`.
 */
class m210120_082843_add_isActive_column_to_figure_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%figure}}', 'isActive', $this->boolean()->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%figure}}', 'isActive');
    }
}

<?php

namespace app\models;

use yii\db\ActiveRecord;
use app\models\Users;

class Figures extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{figures}}';
    }

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            ['shape', 'string'],
            ['x', 'default', 'value' => rand(50, 740)],
            ['y', 'default', 'value' =>  rand(150, 550)],
        ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'x' => 'X',
            'y' => 'Y',
            'shape' => 'Shape',
            'user_id' => 'User ID',
        ];
    }
}


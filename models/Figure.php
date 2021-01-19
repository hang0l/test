<?php

namespace app\models;

use yii\db\ActiveRecord;
use app\models\Player;

class Figure extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{figure}}';
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
            ['isActive', 'boolean'],
            ['isActive', 'default', 'value' => 1]
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
            'isActive' => 'Active',
            'player_id' => 'Player ID',
        ];
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function safeDelete(): bool
    {
        try {
            $this->isActive = false;
            $this->save();
            return true;
        } catch (\Exception $error) {
            throw $error;
        }
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function restoreFigure(): bool
    {
        try {
            $this->isActive = true;
            $this->save();
            return true;
        } catch (\Exception $error) {
            throw $error;
        }
    }
}


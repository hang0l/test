<?php

namespace app\models;

use yii\db\ActiveRecord;
use app\models\Figure;

class Player extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{player}}';
    }

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['username'], 'required', 'message' => 'Please, enter your name'],
            [['username'], 'string', 'min' => 4, 'max' => 30],
            ];
    }

    /**
     * @return string[]
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFigure(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Figure::className(), ['player_id' => 'id']);
    }

    public function getFigureInformation(): array
    {
        $figureCount['square'] = $this->getFigure()
            ->where(['shape' => 'square'])
            ->count();
        $figureCount['circle'] = $this->getFigure()
            ->where(['shape' => 'circle'])
            ->count();
        $figureCount['triangle'] = $this->getFigure()
            ->where(['shape' => 'triangle'])
            ->count();
        $figureCount['hexagon'] = $this->getFigure()
            ->where(['shape' => 'hexagon'])
            ->count();
        return $figureCount;
    }
}
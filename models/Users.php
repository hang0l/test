<?php

namespace app\models;

use yii\db\ActiveRecord;
use app\models\Figures;

class Users extends ActiveRecord
{
    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{users}}';
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
    public function getFigures(): \yii\db\ActiveQuery
    {
        return $this->hasMany(Figures::className(), ['user_id' => 'id']);
    }

    public function getFiguresInformation(): string
    {
        return 'Squares: ' . $this->getFigures()
                ->where(['shape' => 'square'])
                ->count() .', Circles: ' . $this->getFigures()
                ->where(['shape' => 'circle'])
                ->count();
    }
}
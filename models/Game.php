<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "game".
 *
 * @property int $id
 * @property string|null $username
 * @property string|null $shape
 * @property int|null $xCoord
 * @property int|null $yCoord
 */
class Game extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'game';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['xCoord', 'yCoord'], 'integer'],
            [['username', 'shape'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'shape' => 'Shape',
            'xCoord' => 'X Coord',
            'yCoord' => 'Y Coord',
        ];
    }
}

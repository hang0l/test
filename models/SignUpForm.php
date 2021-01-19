<?php

namespace app\models;
use yii\base\Model;

class SignUpForm extends Model {

    public $username;

    /**
     * @return array[]
     */
    public function rules(): array
    {
        return [
            [['username'], 'required'],
            [['username'], 'string']
        ];
    }



    public function login()
    {

    }
}
<?php

namespace app\controllers;

use Codeception\PHPUnit\Constraint\Page;
use Yii;
use yii\web\Controller;
use app\models\Figures;
use app\models\Users;
use yii\helpers\Json;


class GameController extends Controller
{
    /**
     * @return string
     * @throws \Exception
     */
    public function actionGame(): string
    {
        $users = Users::find()
            ->joinWith('figures')
            ->asArray()
            ->all();
        $json_objects_users = json_encode($users);
        $user = new Users();
        $figure = new Figures();
        if ($user->load(Yii::$app->request->post()) && $user->validate() &&
            $figure->load(Yii::$app->request->post()) && $figure->validate()) {
            try {
                if (!($userModel = Users::findOne(['username' => $user->username])))
                {
                    $figure->user_id = $user->id;
                    $user->save();

                }
                else {
                    $userModel = Users::findOne(['username' => $user->username]);
                    $figure->user_id = $userModel->id;
                }
                $figure->loadDefaultValues();
                $figure->save();
            }
            catch (\Exception $error) {
                throw $error;
            }
            $this->refresh();
        }
        return $this->render('game', [
            'user' => $user,
            'figure' => $figure,
            'json_objects_users' => $json_objects_users,
        ]);
    }

    /**
     * @return bool
     */
    public function actionDeleteObject(): bool
    {
        if (Yii::$app->request->isAjax) {
            $data = json_decode(file_get_contents("php://input"), true);
            $id = (int)$data['id'];
            $figureModel = Figures::findOne($id);
            if ($figureModel->delete()) {
                return true;
            }
            else{
                return false;
            }
        }
    }

    /**
     * @return bool
     */
    public function actionUpdateCoords(): bool
    {
        if (Yii::$app->request->isAjax) {
            $data = json_decode(file_get_contents("php://input"), true);
            $id = (int)$data['id'];
            $x = (float)$data['x'];
            $y = (float)$data['y'];
            $figureModel = Figures::findOne($id);
            $figureModel->x = $x;
            $figureModel->y = $y;
            if ($figureModel->save()) {
                return true;
            }
            else{
                return false;
            }
        }
    }
}
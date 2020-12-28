<?php

namespace app\controllers;

use Codeception\PHPUnit\Constraint\Page;
use Yii;
use yii\web\Controller;
use app\models\Figures;
use app\models\Users;
use yii\helpers\Json;
use yii\helpers\Url;


class GameController extends Controller
{
    /**
     * @return string
     * @throws \Exception
     */
    public function actionGame(): string
    {
        $users = Users::find()
            ->with('figures')
            ->asArray()
            ->all();
        $user = new Users();
        $figure = new Figures();
        if ($user->load(Yii::$app->request->post()) && $user->validate() &&
            $figure->load(Yii::$app->request->post()) && $figure->validate()) {
            try {
                if (!($userModel = Users::findOne(['username' => $user->username])))
                {
                    $user->save();
                    $figure->user_id = $user->id;

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
            'users' => $users,
        ]);
    }

    /**
     * @return bool
     */
    public function actionDeleteObject(): bool
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request;
            $id = (int)$data->post('id');
            $figureModel = Figures::findOne($id);
            if ($figureModel->delete()) {
                return true;
            }
            else{
                return false;
            }
        }
        else if(Yii::$app->request->get()) {
            $id = Yii::$app->request->get('id');
            $figureModel = Figures::findOne($id);
            if ($figureModel->delete()) {
                $this->redirect('/');
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * @return bool
     */
    public function actionUpdateCoords(): bool
    {
        if (Yii::$app->request->isAjax) { //У меня все получилось, когда я перестал отправлять
            $data = Yii::$app->request;  //данные через json. Не понял, почему так
            $id = (int)$data->post('id');
            $x = (float)$data->post('x');
            $y = (float)$data->post('y');
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
        else if(Yii::$app->request->get()) {
            $id = Yii::$app->request->get('id');
            $x = Yii::$app->request->get('x');
            $y = Yii::$app->request->get('y');
            $figureModel = Figures::findOne($id);
            $figureModel->x = $x;
            $figureModel->y = $y;
            if ($figureModel->save()) {
                $this->redirect('/');
                return true;
            }
            else{
                return false;
            }
        }
    }
}
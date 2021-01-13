<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Figure;
use app\models\Player;
use yii\data\ActiveDataProvider;


class GameController extends Controller
{
    /**
     * @return string
     * @throws \Exception
     */
    public function actionGame(): string
    {
        $dataProvider = new ActiveDataProvider([
            'query' => Player::find(),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        $players = Player::find()
            ->with('figure')
            ->asArray()
            ->all();
        return $this->render('game', [
            'players' => $players,
            'dataProvider' => $dataProvider
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
            $figureModel = Figure::findOne($id);
            var_dump($figureModel);
            if ($figureModel->delete()) {
                return true;
            }
            return false;
        }
        /*
        else if(Yii::$app->request->get()) {
            $id = Yii::$app->request->get('id');
            $figureModel = Figure::findOne($id);
            if ($figureModel->delete()) {
                $this->redirect('/');
                return true;
            }
            return false;

        }
        */
    }

    /**
     * @return bool
     */
    public function actionUpdateCoords(): bool
    {
        if (Yii::$app->request->isAjax) {
            $id = Yii::$app->request->post('id');
            $figureModel = Figure::findOne($id);
            $figureModel->load(Yii::$app->request->post(), '');
            if ($figureModel->save()) {
                return true;
            }
            return false;

        }
        /*
        else if(Yii::$app->request->get()) {
            $id = Yii::$app->request->get('id');
            $figureModel = Figure::findOne($id);
            $figureModel->load(Yii::$app->request->get(), '');
            if ($figureModel->save()) {
                $this->redirect('/');
                return true;
            }
            return false;
        }
        */
    }

    public function actionCreateFigure()
    {
        $figure = new Figure();
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        try {
            $figure->load(Yii::$app->request->post(), '');
            $playerModel = Player::findOne(['username' => Yii::$app->request->post('username')]);
            if ($figure->validate()) {
                if (!($playerModel)) {
                    $playerModel = new Player();
                    if ($playerModel->validate()) {
                        $playerModel->save();
                    } else {
                        return $response->data = ['error' => $playerModel->getErrors()];
                    }
                }
                $figure->player_id = $playerModel->id;
                $figure->save();
                $response->data = ['player' => $playerModel, 'figure' => $figure];
                return $response->data;
            }
            else{
                    return $response->data = ['error' => $figure->getErrors()];
                }
        }
        catch (\Exception $error) {
            throw $error;
        }
    }
}
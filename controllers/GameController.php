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
        $player = new Player();
        $figure = new Figure();
        $player->username = Yii::$app->request->post('username');
        $figure->shape = Yii::$app->request->post('shape');
        if ($player->validate() && $figure->validate())
        {
            try {
                if (!($playerModel = Player::findOne(['username' => $player->username])))
                {
                    $player->save();
                    $figure->player_id = $player->id;

                }
                else {
                    $playerModel = Player::findOne(['username' => $player->username]);
                    $player = $playerModel;
                    $figure->player_id = $playerModel->id;

                }
                $figure->save();
            }
            catch (\Exception $error) {
                throw $error;
            }
        }
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $response->data = ['player' => $player, 'figure' => $figure];
        return $data;
    }
}
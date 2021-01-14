<?php

namespace app\controllers;

use app\models\CheckCollision;
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
        $figureModel = new Figure();
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        try {
            $figureModel->load(Yii::$app->request->post(), '');
            $playerModel = Player::findOne(['username' => Yii::$app->request->post('username')]);
            if (!($playerModel)) {
                $playerModel = new Player();
                $playerModel->load(Yii::$app->request->post(), '');
            }
            if ($figureModel->validate() && $playerModel->validate()) {
                $playerModel->save();
                $figureModel->player_id = $playerModel->id;
                $figureModel->save();
                return $response->data = ['player' => $playerModel, 'figure' => $figureModel];
            } else {
                return $response->data = $playerModel->getErrors() ? ['error' => $playerModel->getErrors()] :
                    ['error' => $figureModel->getErrors()];
            }
        } catch (\Exception $error) {
            throw $error;
        }
    }

    public function actionCheckCollision()
    {
        $figureOne = new CheckCollision();
        $figureOne->load(Yii::$app->request->post(), '');
        $figures = Figure::find()->asArray()->all();
        for($i = 0; $i < count($figures); $i ++)
		{
            if ($figureOne->id !== $figures[$i]['id']) {
                $figureTwoX = $figures[$i]['x'];
				$figureTwoY = $figures[$i]['y'];
				$distanceBetweenCentres = $figureOne->d;
				if ($figureOne->x > $figureTwoX) {
                    if ($figureOne->y > $figureTwoY) {
                        if ($figureOne->x - $figureTwoX < $distanceBetweenCentres &&
                            $figureOne->y - $figureTwoY < $distanceBetweenCentres) {
                            return $figureOne->returnIdToDelete($figureOne->id,
                                $figures[$i]['id']);
                        }
                    } else {
                        if ($figureOne->x - $figureTwoX < $distanceBetweenCentres &&
                            $figureTwoY - $figureOne->y < $distanceBetweenCentres) {
                            return $figureOne->returnIdToDelete($figureOne->id,
                                $figures[$i]['id']);
                        }
                    }
                } else {
                    if ($figureOne->y > $figureTwoY) {
                        if ($figureTwoX - $figureOne->x < $distanceBetweenCentres &&
                            $figureOne->y - $figureTwoY < $distanceBetweenCentres) {
                            return $figureOne->returnIdToDelete($figureOne->id,
                                $figures[$i]['id']);
                        }
                    } else {
                        if ($figureTwoX - $figureOne->x < $distanceBetweenCentres &&
                            $figureTwoY - $figureOne->y < $distanceBetweenCentres) {
                            return $figureOne->returnIdToDelete($figureOne->id,
                                $figures[$i]['id']);
                        }
                    }
                }
			}
        }
    }
}
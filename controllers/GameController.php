<?php

namespace app\controllers;

use app\models\CheckCollision;
use app\models\SignUpForm;
use Yii;
use yii\helpers\Url;
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
     * @throws \Exception
     */
    public function actionDeleteObject(): bool
    {
        if (Yii::$app->request->isAjax) {
            $data = Yii::$app->request;
            $id = (int)$data->post('id');
            $figureModel = Figure::findOne($id);
            if ($figureModel->safeDelete()) {
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

    /**
     * @return array
     * @throws \Exception
     */
    public function actionCreateFigure(): array
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
                return $response->data = ['error' => $playerModel->getErrors()] ??
                    ['error' => $figureModel->getErrors()];
            }
        } catch (\Exception $error) {
            throw $error;
        }
    }

    /**
     * @return array
     */
    public function actionCheckCollision()
    {
        $response = Yii::$app->response;
        $response->format = \yii\web\Response::FORMAT_JSON;
        $figureOne = new CheckCollision();
        $figureOne->load(Yii::$app->request->post(), '');
        $figureOne->collisionDistance = (int)Yii::$app->request->post('collisionDistance');
        $figures = Figure::find()->where(['not in', 'id', $figureOne->id])->all();
        $figuresLength = count($figures);
        for($index = 0; $index <  $figuresLength; $index ++) {
            $distanceBetweenCenters = sqrt(
                (($figureOne->x - $figures[$index]->x) ** 2) +
                (($figureOne->y - $figures[$index]->y) ** 2)
            );
            if ($distanceBetweenCenters < $figureOne->collisionDistance) {
                //echo '<pre>';
                //var_dump($figureOne->id, $figures[$index]->id);
                //echo '</pre>';
                return $response->data =
                    ['id' => $figureOne->getIdToDelete(
                        $figureOne->id, $figures[$index]->id)
                    ];
            }
        }
    }

    /**
     * @return string|\yii\web\Response
     */
    public function actionSignIn()
    {
        $playerModel = new SignUpForm();
        if ($playerModel->load(Yii::$app->request->post()) && $playerModel->validate()) {
            return $this->redirect(Url::toRoute(['game/figures-list', 'username' => $playerModel->username]));
        } else {
            return $this->render('sign-in', ['playerModel' => $playerModel]);
        }
    }

    /**
     * @param $username
     * @return string
     */
    public function actionFiguresList($username): string
    {
        $player = Player::findOne(['username' => $username]);
        $dataProvider = new ActiveDataProvider([
            'query' => Figure::find()
            ->where(['player_id' => $player->id]),
            'pagination' => [
                'pageSize' => 10,
            ],
        ]);
        return $this->render('figures-list', ['dataProvider' => $dataProvider]);
    }

    /**
     * @param $id
     * @return false|\yii\web\Response
     * @throws \Exception
     */
    public function actionRestoreFigure($id)
    {
        if($figure = Figure::findOne($id)) {
            $figure->restoreFigure();
            return $this->redirect(Yii::$app->request->referrer);
        } else {
            return false;
        }
    }
}
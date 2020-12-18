<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Game;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionPlay()
    {
        $objects = Game::find()->asArray()->all();
        $json_objects = json_encode($objects);
        $model = new Game();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->x_coord = rand(10, 740);
            $model->y_coord = rand(10, 540);
            $model->save();
            $this->refresh();
        }
        else
        {
            return $this->render('play', ['model' => $model, 'json_objects' => $json_objects]);
        }
    }
    public function beforeAction($action) 
    { 
        $this->enableCsrfValidation = false; 
        return parent::beforeAction($action); 
    }

    public function actionDeleteobject()
    {
        $json = json_decode(file_get_contents("php://input"));
        $array = json_decode(json_encode($json),true);
        $id = (int)$array['id'];
        $model=Game::findOne($id); // предполагаем, что запись с ID=10 существует
        $model->delete();
    }

    public function actionUpdatecoords()
    {
        $json = json_decode(file_get_contents("php://input"));
        $array = json_decode(json_encode($json),true);
        $id = (int)$array['id'];
        $x_coord = (int)$array['x_coord'];
        $y_coord = (int)$array['y_coord'];
        $model=Game::findOne($id); 
        $model->x_coord = $x_coord;
        $model->y_coord = $y_coord;
        $model->save();
        var_dump($model->x_coord);
    }
}

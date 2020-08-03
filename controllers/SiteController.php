<?php

namespace app\controllers;

use app\models\Tree;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
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
     * Move node
     *
     * @return string
     */
    public function actionMove(){
        if (Yii::$app->request->isPost && Yii::$app->request->isAjax){
            $id = Yii::$app->request->post('id');
            $to = Yii::$app->request->post('to');
            if ($node = Tree::findOne(['id' => $id])){
                if ($to){
                    // Move to node
                    if ( $nodeTo = Tree::findOne(['id' => $to]) ){
                        /** @var Tree $node */
                        /** @var Tree $nodeTo */
                        $node->appendTo($nodeTo);
                    } else {
                        throw new NotFoundHttpException('Указанная нода не существует');
                    }
                } else {
                    // Move to root
                    $node->makeRoot();
                }
            } else {
                throw new NotFoundHttpException('Указанная нода не существует');
            }
        } else {
            throw new NotFoundHttpException('Запрошенная страница не найдена');
        }
    }
}

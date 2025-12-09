<?php

namespace app\controllers;

use Yii;
use app\models\Author;
use app\models\AuthorSearch;
use app\models\forms\SubscribeForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * AuthorController реализует CRUD операции для модели Author.
 */
class AuthorController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'subscribe'],
                        'allow' => true,
                        'roles' => ['?', '@'], // guest и user
                    ],
                    [
                        'actions' => ['create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'], // только авторизованные
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Список всех авторов.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new AuthorSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Просмотр детальной информации об авторе.
     *
     * @param int $id
     * @return string
     * @throws NotFoundHttpException если автор не найден
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $subscribeForm = new SubscribeForm();
        $subscribeForm->author_id = $id;

        if ($subscribeForm->load(Yii::$app->request->post()) && $subscribeForm->subscribe()) {
            Yii::$app->session->setFlash('success', 'Вы успешно подписались на автора.');
            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->render('view', [
            'model' => $model,
            'subscribeForm' => $subscribeForm,
        ]);
    }

    /**
     * Подписка на автора.
     *
     * @param int $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException если автор не найден
     */
    public function actionSubscribe($id)
    {
        $author = $this->findModel($id);
        $model = new SubscribeForm();
        $model->author_id = $id;

        if ($model->load(Yii::$app->request->post()) && $model->subscribe()) {
            Yii::$app->session->setFlash('success', 'Вы успешно подписались на автора.');
            return $this->redirect(['view', 'id' => $id]);
        }

        return $this->render('subscribe', [
            'model' => $model,
            'author' => $author,
        ]);
    }

    /**
     * Создание нового автора.
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Author();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::info("Author created: ID={$model->id}, Name={$model->full_name}", __CLASS__ . '::' . __FUNCTION__);
            Yii::$app->session->setFlash('success', 'Автор успешно создан.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Редактирование автора.
     *
     * @param int $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException если автор не найден
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::info("Author updated: ID={$model->id}, Name={$model->full_name}", __CLASS__ . '::' . __FUNCTION__);
            Yii::$app->session->setFlash('success', 'Автор успешно обновлен.');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Удаление автора.
     *
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException если автор не найден
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $authorName = $model->full_name;
        
        $model->delete();
        
        Yii::info("Author deleted: ID={$id}, Name={$authorName}", __CLASS__ . '::' . __FUNCTION__);
        Yii::$app->session->setFlash('success', 'Автор успешно удален.');

        return $this->redirect(['index']);
    }

    /**
     * Найти модель автора по ID.
     *
     * @param int $id
     * @return Author загруженная модель
     * @throws NotFoundHttpException если модель не найдена
     */
    protected function findModel($id)
    {
        if (($model = Author::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрашиваемая страница не найдена.');
    }
}


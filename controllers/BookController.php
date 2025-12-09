<?php

namespace app\controllers;

use Yii;
use app\models\Book;
use app\models\BookSearch;
use app\models\BookAuthor;
use app\models\AuthorSubscription;
use app\services\SmsService;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * BookController реализует CRUD операции для модели Book.
 */
class BookController extends Controller
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
                        'actions' => ['index', 'view', 'toggle-view'],
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
     * Список всех книг.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new BookSearch();
        // Получить режим просмотра из сессии (по умолчанию 'table')
        $viewMode = Yii::$app->session->get('book_view_mode', 'table');
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $viewMode);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'viewMode' => $viewMode,
        ]);
    }

    /**
     * Переключение режима просмотра книг.
     *
     * @return Response
     */
    public function actionToggleView()
    {
        $currentMode = Yii::$app->session->get('book_view_mode', 'table');
        $newMode = $currentMode === 'table' ? 'cards' : 'table';
        Yii::$app->session->set('book_view_mode', $newMode);
        
        Yii::info("Book view mode toggled: {$currentMode} -> {$newMode}", __CLASS__ . '::' . __FUNCTION__);
        
        return $this->redirect(['index']);
    }

    /**
     * Просмотр детальной информации о книге.
     *
     * @param int $id
     * @return string
     * @throws NotFoundHttpException если книга не найдена
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Создание новой книги.
     *
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Book();

        if ($model->load(Yii::$app->request->post())) {
            $model->coverFile = UploadedFile::getInstance($model, 'coverFile');
            
            if ($model->save()) {
                // Загрузить обложку
                if ($model->coverFile) {
                    $model->upload();
                    $model->save(false); // Сохранить путь к обложке
                }
                
                // Сохранить связи с авторами
                if ($model->authorIds) {
                    foreach ($model->authorIds as $authorId) {
                        $bookAuthor = new BookAuthor();
                        $bookAuthor->book_id = $model->id;
                        $bookAuthor->author_id = $authorId;
                        $bookAuthor->save();
                    }
                    
                    // Отправить SMS подписчикам авторов
                    $subscriptions = AuthorSubscription::find()
                        ->where(['author_id' => $model->authorIds])
                        ->all();
                    
                    $uniquePhones = [];
                    foreach ($subscriptions as $subscription) {
                        $phone = $subscription->phone;
                        if (!in_array($phone, $uniquePhones)) {
                            $uniquePhones[] = $phone;
                            SmsService::send(
                                $phone,
                                "Новая книга от ваших авторов: {$model->title}"
                            );
                        }
                    }
                }
                
                Yii::info("Book created: ID={$model->id}, Title={$model->title}", __CLASS__ . '::' . __FUNCTION__);
                Yii::$app->session->setFlash('success', 'Книга успешно создана.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Редактирование книги.
     *
     * @param int $id
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException если книга не найдена
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->coverFile = UploadedFile::getInstance($model, 'coverFile');
            
            if ($model->save()) {
                // Загрузить обложку (если выбрана новая)
                if ($model->coverFile) {
                    $model->upload();
                    $model->save(false); // Сохранить путь к обложке
                }
                
                // Обновить связи с авторами
                // Очистить старые связи
                BookAuthor::deleteAll(['book_id' => $model->id]);
                // Создать новые связи
                if ($model->authorIds) {
                    foreach ($model->authorIds as $authorId) {
                        $bookAuthor = new BookAuthor();
                        $bookAuthor->book_id = $model->id;
                        $bookAuthor->author_id = $authorId;
                        $bookAuthor->save();
                    }
                }
                
                Yii::info("Book updated: ID={$model->id}, Title={$model->title}", __CLASS__ . '::' . __FUNCTION__);
                Yii::$app->session->setFlash('success', 'Книга успешно обновлена.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Удаление книги.
     *
     * @param int $id
     * @return \yii\web\Response
     * @throws NotFoundHttpException если книга не найдена
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $bookTitle = $model->title;
        
        $model->delete();
        
        Yii::info("Book deleted: ID={$id}, Title={$bookTitle}", __CLASS__ . '::' . __FUNCTION__);
        Yii::$app->session->setFlash('success', 'Книга успешно удалена.');

        return $this->redirect(['index']);
    }

    /**
     * Найти модель книги по ID.
     *
     * @param int $id
     * @return Book загруженная модель
     * @throws NotFoundHttpException если модель не найдена
     */
    protected function findModel($id)
    {
        if (($model = Book::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Запрашиваемая страница не найдена.');
    }
}


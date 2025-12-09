<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\BookSearch $searchModel */
/** @var yii\data\ActiveDataProvider $dataProvider */
/** @var string $viewMode Режим просмотра ('table' или 'cards') */

$this->title = 'Книги';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="book-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
        <?php if (!Yii::$app->user->isGuest): ?>
            <?= Html::a('Создать книгу', ['create'], ['class' => 'btn btn-success']) ?>
        <?php endif; ?>
        </div>
        <div class="btn-group" role="group" aria-label="Переключение вида">
            <?= Html::a(
                '☰ Таблица',
                ['toggle-view'],
                [
                    'class' => 'btn btn-sm ' . ($viewMode === 'table' ? 'btn-primary' : 'btn-outline-primary'),
                    'title' => 'Табличный вид',
                ]
            ) ?>
            <?= Html::a(
                '▦ Карточки',
                ['toggle-view'],
                [
                    'class' => 'btn btn-sm ' . ($viewMode === 'cards' ? 'btn-primary' : 'btn-outline-primary'),
                    'title' => 'Вид карточек',
                ]
            ) ?>
        </div>
    </div>

    <?php if ($viewMode === 'table'): ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            [
                'attribute' => 'title',
                'label' => 'Название',
                'format' => 'raw',
                'value' => function ($model) {
                    $content = '<div class="d-flex align-items-center">';
                    
                    // Обложка
                    if ($model->cover_path) {
                        $content .= Html::a(
                            Html::img('/' . $model->cover_path, [
                                'alt' => Html::encode($model->title),
                                'style' => 'width: 50px; height: 70px; object-fit: cover; margin-right: 10px;',
                                'class' => 'img-thumbnail',
                            ]),
                            ['view', 'id' => $model->id],
                            ['class' => 'text-decoration-none']
                        );
                    } else {
                        $content .= '<div class="bg-light d-flex align-items-center justify-content-center" style="width: 50px; height: 70px; margin-right: 10px; font-size: 10px; text-align: center; padding: 5px;">Нет обложки</div>';
                    }
                    
                    // Название и авторы
                    $content .= '<div>';
                    $content .= Html::a(
                        Html::encode($model->title),
                        ['view', 'id' => $model->id],
                        ['class' => 'text-decoration-none fw-bold']
                    );
                    
                    // Авторы
                    if (!empty($model->authors)) {
                        $content .= '<br><small class="text-muted">';
                        $authors = [];
                        foreach ($model->authors as $author) {
                            $authors[] = Html::a(
                                Html::encode($author->full_name),
                                ['/author/view', 'id' => $author->id],
                                ['class' => 'text-decoration-none']
                            );
                        }
                        $content .= implode(', ', $authors);
                        $content .= '</small>';
                    }
                    
                    $content .= '</div>';
                    $content .= '</div>';
                    
                    return $content;
                },
            ],
            'year',
            'isbn:ntext',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'visibleButtons' => [
                    'update' => function ($model, $key, $index) {
                        return !Yii::$app->user->isGuest;
                    },
                    'delete' => function ($model, $key, $index) {
                        return !Yii::$app->user->isGuest;
                    },
                ],
            ],
        ],
    ]); ?>
    <?php else: ?>
        <?php
        // Карточный вид
        // Форма поиска
        $form = ActiveForm::begin([
            'action' => ['index'],
            'method' => 'get',
            'options' => ['class' => 'mb-4'],
        ]);
        ?>
        <div class="row mb-3">
            <div class="col-md-8">
                <?= $form->field($searchModel, 'searchQuery')->textInput([
                    'placeholder' => 'Поиск по названию или автору...',
                    'class' => 'form-control',
                ])->label(false) ?>
            </div>
            <div class="col-md-4">
                <?= Html::submitButton('Поиск', ['class' => 'btn btn-primary w-100']) ?>
            </div>
        </div>
        <div class="mb-3">
            <?= Html::a('Сбросить', ['index'], ['class' => 'btn btn-outline-secondary']) ?>
        </div>
        <?php ActiveForm::end(); ?>
        
        <?php
        $books = $dataProvider->getModels();
        ?>
        <div class="row g-3" id="books-cards">
            <?php if (empty($books)): ?>
                <div class="col-12">
                    <div class="alert alert-info">Книги не найдены.</div>
                </div>
            <?php else: ?>
                <?php foreach ($books as $book): ?>
                    <div class="col-md-4 col-lg-3">
                        <div class="card h-100">
                            <?php if ($book->cover_path): ?>
                                <?= Html::a(
                                    Html::img('/' . $book->cover_path, [
                                        'class' => 'card-img-top',
                                        'alt' => Html::encode($book->title),
                                        'style' => 'height: 250px; object-fit: cover;',
                                    ]),
                                    ['view', 'id' => $book->id],
                                    ['class' => 'text-decoration-none']
                                ) ?>
                            <?php else: ?>
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 250px;">
                                    <span class="text-muted">Нет обложки</span>
                                </div>
                            <?php endif; ?>
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">
                                    <?= Html::a(
                                        Html::encode($book->title),
                                        ['view', 'id' => $book->id],
                                        ['class' => 'text-decoration-none']
                                    ) ?>
                                </h5>
                                <?php if ($book->year): ?>
                                    <p class="card-text text-muted mb-1">
                                        <small>Год: <?= Html::encode($book->year) ?></small>
                                    </p>
                                <?php endif; ?>
                                <?php if ($book->isbn): ?>
                                    <p class="card-text text-muted mb-1">
                                        <small>ISBN: <?= Html::encode($book->isbn) ?></small>
                                    </p>
                                <?php endif; ?>
                                <?php if (!empty($book->authors)): ?>
                                    <p class="card-text mb-2">
                                        <small class="text-muted">Авторы:</small><br>
                                        <?php foreach ($book->authors as $index => $author): ?>
                                            <?= Html::a(
                                                Html::encode($author->full_name),
                                                ['/author/view', 'id' => $author->id],
                                                ['class' => 'text-decoration-none']
                                            ) ?>
                                            <?php if ($index < count($book->authors) - 1): ?>, <?php endif; ?>
                                        <?php endforeach; ?>
                                    </p>
                                <?php endif; ?>
                                <div class="mt-auto pt-2">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <?= Html::a('Просмотр', ['view', 'id' => $book->id], ['class' => 'btn btn-outline-primary']) ?>
                                        <?php if (!Yii::$app->user->isGuest): ?>
                                            <?= Html::a('Редактировать', ['update', 'id' => $book->id], ['class' => 'btn btn-outline-secondary']) ?>
                                            <?= Html::a('Удалить', ['delete', 'id' => $book->id], [
                                                'class' => 'btn btn-outline-danger',
                                                'data' => [
                                                    'confirm' => 'Вы уверены, что хотите удалить эту книгу?',
                                                    'method' => 'post',
                                                ],
                                            ]) ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <?php
        // Пагинация для карточного вида
        echo \yii\widgets\LinkPager::widget([
            'pagination' => $dataProvider->pagination,
            'options' => ['class' => 'pagination justify-content-center mt-4'],
        ]);
        ?>
    <?php endif; ?>

</div>


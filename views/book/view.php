<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Book $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Книги', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="book-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if (!Yii::$app->user->isGuest): ?>
            <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы уверены, что хотите удалить эту книгу?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif; ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title:ntext',
            'year',
            'description:ntext',
            'isbn:ntext',
            [
                'attribute' => 'cover_path',
                'format' => 'raw',
                'value' => $model->cover_path 
                    ? Html::img('/' . $model->cover_path, ['alt' => $model->title, 'style' => 'max-width: 200px;'])
                    : 'Обложка не загружена',
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

    <?php if (!empty($model->authors)): ?>
        <h3>Авторы</h3>
        <ul>
            <?php foreach ($model->authors as $author): ?>
                <li><?= Html::a(Html::encode($author->full_name), ['/author/view', 'id' => $author->id]) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

</div>


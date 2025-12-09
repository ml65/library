<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\Author $model */
/** @var app\models\forms\SubscribeForm $subscribeForm */

$this->title = $model->full_name;
$this->params['breadcrumbs'][] = ['label' => 'Авторы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="author-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php if (!Yii::$app->user->isGuest): ?>
            <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы уверены, что хотите удалить этого автора?',
                    'method' => 'post',
                ],
            ]) ?>
        <?php endif; ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'full_name:ntext',
        ],
    ]) ?>

    <?php if (!empty($model->books)): ?>
        <h3>Книги автора</h3>
        <ul>
            <?php foreach ($model->books as $book): ?>
                <li><?= Html::a(Html::encode($book->title), ['/book/view', 'id' => $book->id]) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <hr>

    <h3>Подписаться на автора</h3>
    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success"><?= Yii::$app->session->getFlash('success') ?></div>
    <?php endif; ?>

    <?php $form = ActiveForm::begin([
        'action' => ['view', 'id' => $model->id],
        'options' => ['class' => 'form-inline'],
    ]); ?>

        <?= $form->field($subscribeForm, 'author_id')->hiddenInput()->label(false) ?>
        <?= $form->field($subscribeForm, 'phone')->textInput(['placeholder' => '+79001234567', 'maxlength' => true])->label('Телефон') ?>
        
        <div class="form-group">
            <?= Html::submitButton('Подписаться', ['class' => 'btn btn-primary']) ?>
        </div>

    <?php ActiveForm::end(); ?>

</div>


<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Author;

/** @var yii\web\View $this */
/** @var app\models\Book $model */
/** @var yii\widgets\ActiveForm $form */
?>

<div class="book-form">

    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
        'fieldConfig' => [
            'errorOptions' => ['class' => 'text-danger', 'style' => 'color: red;'],
        ],
    ]); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'year')->textInput() ?>

    <?= $form->field($model, 'description')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'isbn')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'coverFile')->fileInput() ?>
    <?php if ($model->cover_path): ?>
        <div class="form-group">
            <label>Текущая обложка:</label><br>
            <?= Html::img('/' . $model->cover_path, ['alt' => $model->title, 'style' => 'max-width: 200px;']) ?>
        </div>
    <?php endif; ?>

    <?= $form->field($model, 'authorIds')->checkboxList(
        ArrayHelper::map(Author::find()->all(), 'id', 'full_name'),
        [
            'separator' => '<br>',
        ]
    )->label('Авторы') ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/** @var array $authors */
/** @var int|null $year */

$this->title = 'ТОП-10 авторов';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="report-top-authors">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin([
        'method' => 'get',
        'action' => ['/report/top-authors'],
        'options' => ['class' => 'form-inline mb-3'],
    ]); ?>

        <div class="form-group me-2">
            <?= Html::label('Год:', 'year', ['class' => 'me-2']) ?>
            <?= Html::textInput('year', $year, [
                'type' => 'number',
                'id' => 'year',
                'class' => 'form-control',
                'placeholder' => 'Все годы',
                'min' => 1000,
                'max' => 2100,
            ]) ?>
        </div>
        
        <?= Html::submitButton('Показать', ['class' => 'btn btn-primary']) ?>
        <?php if ($year !== null): ?>
            <?= Html::a('Сбросить', ['/report/top-authors'], ['class' => 'btn btn-secondary']) ?>
        <?php endif; ?>

    <?php ActiveForm::end(); ?>

    <?php if (!empty($authors)): ?>
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Автор</th>
                        <th style="width: 150px;" class="text-center">Количество книг</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($authors as $index => $author): ?>
                        <tr>
                            <td class="text-center"><?= $index + 1 ?></td>
                            <td><?= Html::encode($author['full_name']) ?></td>
                            <td class="text-center"><strong><?= $author['books_count'] ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if ($year !== null): ?>
            <p class="text-muted">Показаны авторы за <?= Html::encode($year) ?> год</p>
        <?php else: ?>
            <p class="text-muted">Показаны авторы за все годы</p>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert alert-info">
            <p>Авторы не найдены.</p>
            <?php if ($year !== null): ?>
                <p>Попробуйте выбрать другой год или <a href="<?= \yii\helpers\Url::to(['/report/top-authors']) ?>">показать за все годы</a>.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

</div>


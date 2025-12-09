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

    <div class="form-group field-book-coverfile">
        <label class="form-label"><?= $model->getAttributeLabel('coverFile') ?></label>
        <div class="file-upload-wrapper">
            <div class="file-upload-area" id="fileUploadArea">
                <input type="file" 
                       id="book-coverfile" 
                       name="Book[coverFile]" 
                       class="file-input" 
                       accept="image/jpeg,image/jpg,image/png"
                       style="display: none;">
                <div class="file-upload-content">
                    <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="#6c757d" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="margin: 0 auto;">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="17 8 12 3 7 8"></polyline>
                        <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                    <p class="file-upload-text">Нажмите для выбора файла или перетащите изображение сюда</p>
                    <p class="file-upload-hint">Поддерживаются форматы: JPG, PNG (макс. 5MB)</p>
                </div>
            </div>
            <div class="file-preview-container" id="filePreviewContainer" style="display: none;">
                <div class="file-preview-wrapper">
                    <img id="filePreview" src="" alt="Предварительный просмотр" class="file-preview-image">
                    <button type="button" class="file-preview-remove" id="filePreviewRemove" title="Удалить">
                        <span>&times;</span>
                    </button>
                </div>
                <p class="file-preview-name" id="filePreviewName"></p>
            </div>
            <?php if ($model->cover_path): ?>
                <div class="current-cover-container">
                    <label class="form-label">Текущая обложка:</label>
                    <div class="current-cover-wrapper">
                        <?= Html::img('/' . $model->cover_path, [
                            'alt' => $model->title, 
                            'class' => 'current-cover-image',
                            'id' => 'currentCoverImage'
                        ]) ?>
                        <div class="current-cover-overlay" id="currentCoverOverlay" style="display: none;">
                            <p>Будет заменена новым изображением</p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="help-block text-danger" id="fileError" style="display: none;"></div>
        </div>
    </div>

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

<?php
$this->registerJs("
(function() {
    const fileInput = document.getElementById('book-coverfile');
    const fileUploadArea = document.getElementById('fileUploadArea');
    const filePreviewContainer = document.getElementById('filePreviewContainer');
    const filePreview = document.getElementById('filePreview');
    const filePreviewName = document.getElementById('filePreviewName');
    const filePreviewRemove = document.getElementById('filePreviewRemove');
    const fileError = document.getElementById('fileError');
    const currentCoverImage = document.getElementById('currentCoverImage');
    const currentCoverOverlay = document.getElementById('currentCoverOverlay');
    
    const maxFileSize = 5 * 1024 * 1024; // 5MB
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
    
    // Клик по области загрузки
    fileUploadArea.addEventListener('click', function(e) {
        if (e.target !== fileInput) {
            fileInput.click();
        }
    });
    
    // Drag and Drop
    fileUploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        fileUploadArea.classList.add('dragover');
    });
    
    fileUploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        fileUploadArea.classList.remove('dragover');
    });
    
    fileUploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        fileUploadArea.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFile(files[0]);
        }
    });
    
    // Выбор файла через input
    fileInput.addEventListener('change', function(e) {
        if (this.files && this.files.length > 0) {
            handleFile(this.files[0]);
        }
    });
    
    // Удаление превью
    if (filePreviewRemove) {
        filePreviewRemove.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            resetFileInput();
        });
    }
    
    function handleFile(file) {
        // Валидация типа файла
        if (!allowedTypes.includes(file.type)) {
            showError('Неподдерживаемый формат файла. Используйте JPG или PNG.');
            return;
        }
        
        // Валидация размера
        if (file.size > maxFileSize) {
            showError('Размер файла превышает 5MB.');
            return;
        }
        
        hideError();
        
        // Чтение файла для превью
        const reader = new FileReader();
        reader.onload = function(e) {
            filePreview.src = e.target.result;
            filePreviewName.textContent = file.name;
            filePreviewContainer.style.display = 'block';
            fileUploadArea.style.display = 'none';
            
            // Скрыть текущую обложку, если есть
            if (currentCoverImage && currentCoverOverlay) {
                currentCoverImage.style.opacity = '0.5';
                currentCoverOverlay.style.display = 'block';
            }
        };
        reader.readAsDataURL(file);
    }
    
    function resetFileInput() {
        fileInput.value = '';
        filePreview.src = '';
        filePreviewContainer.style.display = 'none';
        fileUploadArea.style.display = 'block';
        hideError();
        
        // Показать текущую обложку обратно
        if (currentCoverImage && currentCoverOverlay) {
            currentCoverImage.style.opacity = '1';
            currentCoverOverlay.style.display = 'none';
        }
    }
    
    function showError(message) {
        fileError.textContent = message;
        fileError.style.display = 'block';
    }
    
    function hideError() {
        fileError.style.display = 'none';
        fileError.textContent = '';
    }
})();
", \yii\web\View::POS_READY);
?>


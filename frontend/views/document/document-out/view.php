<?php

use common\helpers\DateFormatter;
use common\helpers\files\FilesHelper;
use common\helpers\StringFormatter;
use frontend\models\work\document_in_out\DocumentOutWork;
use frontend\models\work\general\PeopleWork;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model \frontend\models\work\document_in_out\DocumentOutWork */

$this->title = $model->document_theme;
$this->params['breadcrumbs'][] = ['label' => 'Исходящая документация', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>


<div class="document-out-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="card">
        <div class="card-block-1">
            <div class="card-set">
                <div class="card-head">Основное</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Имя
                    </div>
                    <div class="field-date">
                        <?= $model->getFullName() ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Тип
                    </div>
                    <div class="field-date">
                        Исходящая документация
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Тема
                    </div>
                    <div class="field-date">
                        <?= $model->getDocumentTheme() ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Способ отправки
                    </div>
                    <div class="field-date">
                        <?= $model->getSendMethodName() ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Адресат</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Корреспондент
                    </div>
                    <div class="field-date">
                        <?= $model->getCompanyName() ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Должность и ФИО
                    </div>
                    <div class="field-date">
                        <?= $model->getCorrespondentName() ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Дата отправки
                    </div>
                    <div class="field-date">
                        <?= $model->getSentDate()?>
                    </div>
                </div>
            </div>
            <?php if ($model->getAnswer()) : ?>
                <div class="card-set">
                    <div class="card-head">Является ответом</div>
                        <div class="card-field flexx">
                            <div class="field-title">
                                Документ
                            </div>
                            <div class="field-date">
                                <?= $model->getIsAnswer(StringFormatter::FORMAT_LINK) ?>
                            </div>
                        </div>
                </div>
            <?php endif; ?>
            <div class="card-set">
                <div class="card-head">Адресант</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Исполнитель
                    </div>
                    <div class="field-date">
                        <?= $model->getExecutorName() ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Кто подписал
                    </div>
                    <div class="field-date">
                        <?= $model->getSignedName() ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-block-2">
            <div class="card-set">
                <div class="card-head">Дата и номер</div>
                <div class="card-field flexx">
                    <div class="field-title">
                        № п/п
                    </div>
                    <div class="field-date">
                        <?= $model->getFullNumber() ?>
                    </div>
                </div>
                <div class="card-field flexx">
                    <div class="field-title">
                        Дата
                    </div>
                    <div class="field-date">
                        <?= $model->getDate() ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Ключевые слова</div>
                <div class="card-field">
                    <div class="field-date">
                        <?= $model->getKeyWords() ?>
                    </div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Файлы</div>
                <div class="flexx" style="justify-content: space-evenly;">
                    <div style="width: 50px;"></div>
                    <div style="width: 50px;"></div>
                    <div style="width: 50px;"></div>
                </div>
                <div class="flexx" style="justify-content: space-evenly;">
                    <div>Сканы</div>
                    <div>Редактируемые</div>
                    <div>Приложения</div>
                </div>
            </div>
            <div class="card-set">
                <div class="card-head">Свойства</div>
                <div class="flexx">
                    <div class="card-field flexx">
                        <div class="field-title field-option">
                            Создатель карточки
                        </div>
                        <div class="field-date">
                            <?= $model->getCreatorName() ?>
                        </div>
                    </div>
                    <div class="card-field flexx">
                        <div class="field-title field-option">
                            Последний редактор
                        </div>
                        <div class="field-date">
                            <?= $model->getLastEditorName() ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

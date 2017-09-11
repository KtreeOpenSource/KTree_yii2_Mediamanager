<?php
/**
 * @link      http://ktree.com/
 * @copyright Copyright (c) 2017 KTree.com.
 * @license   http://ktree.com/license
 */
use ktree\filemanager\Module;
use dosamigos\fileupload\FileUploadUI;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $searchModel system\filemanager\models\Mediafile */
?>
<div class="media-upload-manager-container">
    <header id="header"><span class="glyphicon glyphicon-upload"></span> <?= Module::t('main', 'Upload manager') ?>
    </header>

    <div id="uploadmanager">
            <p><?=
                Html::a(
                    '← ' . Module::t('main', 'Back to file manager'),
                    [
                        'file/filemanager-view',
                        'parent' => $parent,
                        'view' => $view
                    ]
                ) ?></p>
        <?=
        FileUploadUI::widget(
            [
                'model' => $model,
                'attribute' => 'file',
                'clientOptions' => [
                    'autoUpload' => Yii::$app->getModule('filemanager')->autoUpload,
                ],
                'url' => ['upload', 'parent' => $parent],
                'gallery' => false,
            ]
        ) ?>
    </div>
</div>

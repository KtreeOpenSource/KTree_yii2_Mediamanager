<?php
/**
 * @link      http://ktree.com/
 * @copyright Copyright (c) 2017 KTree.com.
 * @license   http://ktree.com/license
 */
use ktree\filemanager\assets\FilemanagerAsset;
use ktree\filemanager\Module;
use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = Module::t('main', 'File manager');
$this->params['breadcrumbs'][] = $this->title;

$assetPath = FilemanagerAsset::register($this)->baseUrl;
?>

<div class="filemanager-default-index">
    <h1><?= Module::t('main', 'File manager module'); ?></h1>

    <div class="row">
        <div class="col-md-6">

            <div class="text-center">
                <h2>
                    <?= Html::a(Module::t('main', 'Files'), ['file/index']) ?>
                </h2>
                <?=
                Html::a(
                    Html::img($assetPath . '/images/files.png', ['alt' => 'Files']),
                    ['file/index']
                ) ?>
            </div>
        </div>

        <div class="col-md-6">

            <div class="text-center">
                <h2>
                    <?= Html::a(Module::t('main', 'Settings'), ['default/settings']) ?>
                </h2>
                <?=
                Html::a(
                    Html::img($assetPath . '/images/settings.png', ['alt' => 'Tools']),
                    ['default/settings']
                ) ?>
            </div>
        </div>
    </div>
</div>

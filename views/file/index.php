<?php
/**
 * @link      http://ktree.com/
 * @copyright Copyright (c) 2017 KTree.com.
 * @license   http://ktree.com/license
 */
use ktree\filemanager\assets\ModalAsset;
use ktree\filemanager\Module;
use yii\helpers\Url;

/* @var $this yii\web\View */

$this->title = Module::t('main', 'Media Manager');
//$this->params['breadcrumbs'][] = $this->title;

ModalAsset::register($this);
?>

<iframe src="<?= Url::to(['file/filemanager-view']) ?>" id="post-original_thumbnail-frame" frameborder="0"
        role="filemanager-frame" name="iframe_execute"></iframe>

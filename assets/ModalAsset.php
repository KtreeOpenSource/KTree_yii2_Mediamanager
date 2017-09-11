<?php
/**
 * @link      http://ktree.com/
 * @copyright Copyright (c) 2017 KTree.com.
 * @license   http://ktree.com/license
 */
namespace ktree\filemanager\assets;

use yii\web\AssetBundle;

class ModalAsset extends AssetBundle
{
    public $sourcePath = '@vendor/ktree/filemanager/assets/source';
    public $css
        = [
            'css/modal.css',
        ];
    public $depends
        = [
            'yii\bootstrap\BootstrapAsset',
        ];
}

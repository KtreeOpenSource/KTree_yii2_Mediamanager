<?php
/**
 * @link      http://ktree.com/
 * @copyright Copyright (c) 2017 KTree.com.
 * @license   http://ktree.com/license
 */
namespace ktree\filemanager\assets;

use yii\web\AssetBundle;

class FileInputAsset extends AssetBundle
{
    public $sourcePath = '@vendor/ktree/filemanager/assets/source';

    public $js
        = [
            'js/fileinput.js',
            'js/default.js'
        ];

    public $depends
        = [
            'yii\bootstrap\BootstrapAsset',
            'yii\web\JqueryAsset',
            'ktree\filemanager\assets\ModalAsset',
            'ktree\filemanager\assets\ThemeAsset'
        ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }
}

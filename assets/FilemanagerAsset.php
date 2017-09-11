<?php
/**
 * @link      http://ktree.com/
 * @copyright Copyright (c) 2017 KTree.com.
 * @license   http://ktree.com/license
 */
namespace ktree\filemanager\assets;

use yii\web\AssetBundle;

class FilemanagerAsset extends AssetBundle
{
    public $sourcePath = '@vendor/ktree/filemanager/assets/source';
    public $css
        = [
            'css/filemanager.css',
        ];
    public $js
        = [
            'js/filemanager.js',
            'js/fileupload-jstree.js',
        ];
    public $depends
        = [
            'yii\bootstrap\BootstrapAsset',
            'yii\web\JqueryAsset',
            'yii\bootstrap\BootstrapPluginAsset',
            'yii\jui\JuiAsset'
        ];

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }
}

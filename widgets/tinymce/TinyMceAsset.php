<?php
/**
 * @link      http://ktree.com/
 * @copyright Copyright (c) 2017 KTree.com.
 * @license   http://ktree.com/license
 */
namespace ktree\filemanager\widgets\tinymce;

use yii\web\AssetBundle;

class TinyMceAsset extends AssetBundle
{
    public $sourcePath = '@vendor/tinymce/tinymce';

    public function init()
    {
        parent::init();
        $this->js[] = YII_DEBUG ? 'tinymce.js' : 'tinymce.min.js';
    }
}

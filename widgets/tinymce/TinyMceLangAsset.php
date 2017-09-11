<?php
/**
 * @link      http://ktree.com/
 * @copyright Copyright (c) 2017 KTree.com.
 * @license   http://ktree.com/license
 */
namespace ktree\filemanager\widgets\tinymce;

use yii\web\AssetBundle;

class TinyMceLangAsset extends AssetBundle
{
    public $sourcePath = '@vendor/ktree/filemanager/widgets/tinymce/assets';

    public $depends = [
        'ktree\filemanager\widgets\tinymce\TinyMceAsset'
    ];
}

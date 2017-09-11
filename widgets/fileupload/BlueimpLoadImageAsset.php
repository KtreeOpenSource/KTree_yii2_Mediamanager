<?php
/**
 * @link      http://ktree.com/
 * @copyright Copyright (c) 2017 KTree.com.
 * @license   http://ktree.com/license
 */
namespace ktree\filemanager\widgets\fileupload;

use yii\web\AssetBundle;

/**
 * FileUploadPlusAsset
 *
 * @author Alexander Kochetov <creocoder@gmail.com>
 */
class BlueimpLoadImageAsset extends AssetBundle
{
    public $sourcePath = '@vendor/ktree/filemanager/widgets/assets/blueimp-load-image';
    public $js = [
        'js/load-image.all.min.js',
    ];
}

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
class BlueimpCanvasToBlobAsset extends AssetBundle
{
    public $sourcePath = '@vendor/ktree/filemanager/widgets/assets/blueimp-canvas-to-blob';
    public $js = [
        'js/canvas-to-blob.min.js',
    ];
}

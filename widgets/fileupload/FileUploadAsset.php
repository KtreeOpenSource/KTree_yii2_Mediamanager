<?php
/**
 * @link      http://ktree.com/
 * @copyright Copyright (c) 2017 KTree.com.
 * @license   http://ktree.com/license
 */

namespace ktree\filemanager\widgets\fileupload;

use yii\web\AssetBundle;

/**
 * FileUploadAsset
 *
 * @author Alexander Kochetov <creocoder@gmail.com>
 */
class FileUploadAsset extends AssetBundle
{
    public $sourcePath = '@vendor/ktree/filemanager/widgets/assets/blueimp-file-upload';
    public $css = [
        'css/jquery.fileupload.css'
    ];
    public $js = [
        'js/vendor/jquery.ui.widget.js',
        'js/jquery.iframe-transport.js',
        'js/jquery.fileupload.js'
    ];
    public $depends = [
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}

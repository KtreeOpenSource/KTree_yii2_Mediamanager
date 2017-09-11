<?php
/**
 * @link      http://ktree.com/
 * @copyright Copyright (c) 2017 KTree.com.
 * @license   http://ktree.com/license
 */

namespace ktree\filemanager\widgets\fileupload;

use yii\web\AssetBundle;

/**
 * FileUploadUIAsset
 *
 * @author Alexander Kochetov <creocoder@gmail.com>
 */
class FileUploadUIAsset extends AssetBundle
{
    public $sourcePath = '@vendor/ktree/filemanager/widgets/assets/blueimp-file-upload';
    public $css = [
        'css/jquery.fileupload.css'
    ];
    public $js = [
        'js/vendor/jquery.ui.widget.js',
        'js/jquery.iframe-transport.js',
        'js/jquery.fileupload.js',
        'js/jquery.fileupload-process.js',
        'js/jquery.fileupload-image.js',
        'js/jquery.fileupload-audio.js',
        'js/jquery.fileupload-video.js',
        'js/jquery.fileupload-validate.js',
        'js/jquery.fileupload-ui.js',

    ];
    public $depends = [
        //'bgtheme\web\AdminLteAsset',
        'yii\web\JqueryAsset',
        'yii\bootstrap\BootstrapPluginAsset',
        'ktree\filemanager\widgets\fileupload\BlueimpLoadImageAsset',
        'ktree\filemanager\widgets\fileupload\BlueimpCanvasToBlobAsset',
        'ktree\filemanager\widgets\fileupload\BlueimpTmplAsset'
    ];
}

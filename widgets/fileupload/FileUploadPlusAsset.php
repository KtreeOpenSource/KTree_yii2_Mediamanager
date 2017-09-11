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
class FileUploadPlusAsset extends AssetBundle
{
    public $sourcePath = '@vendor/ktree/filemanager/widgets/assets/blueimp-file-upload';
    public $css = [
        'css/jquery.fileupload.css'
    ];
    public $js = [
        'js/jquery.iframe-transport.js',
        'js/jquery.fileupload-process.js',
        'js/jquery.fileupload-image.js',
        'js/jquery.fileupload-audio.js',
        'js/jquery.fileupload-video.js',
        'js/jquery.fileupload-validate.js'
    ];
    public $depends = [
        'ktree\filemanager\widgets\fileupload\FileUploadAsset',
        'ktree\filemanager\widgets\fileupload\BlueimpLoadImageAsset',
        'ktree\filemanager\widgets\fileupload\BlueimpCanvasToBlobAsset',
    ];
}

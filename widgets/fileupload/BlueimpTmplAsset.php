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
class BlueimpTmplAsset extends AssetBundle
{
    public $sourcePath = '@vendor/ktree/filemanager/widgets/assets/blueimp-tmpl';
    public $js = [
        'js/tmpl.min.js',
    ];
}

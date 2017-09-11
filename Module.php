<?php
/**
 * @link      http://ktree.com/
 * @copyright Copyright (c) 2017 KTree.com.
 * @license   http://ktree.com/license
 */


namespace ktree\filemanager;

use Yii;

class Module extends \yii\base\Module
{
    public $controllerNamespace = 'ktree\filemanager\controllers';

    /**
     *  Set true if you want to rename files if the name is already in use
     *
     * @var boolean
     */
    public $rename = false;

    /**
     *  Set true to enable autoupload
     *
     * @var boolean
     */
    public $autoUpload = false;

    /**
     * @var array upload routes
     */
    public $routes
        = [
            // base absolute path to web directory
            'baseUrl' => '',
            // base web directory url
            'basePath' => '@webroot',
            // path for uploaded files in web directory
            'uploadPath' => 'uploads',
        ];

    /**
     * @var array thumbnails info
     */
    public $thumbs
        = [
            'small' => [
                'name' => 'Small size',
                'size' => [100, 100],
            ],
            'medium' => [
                'name' => 'Medium size',
                'size' => [300, 200],
            ],
            'large' => [
                'name' => 'Large size',
                'size' => [500, 400],
            ],
            'gallery' => [
                'name' => 'Gallery size',
                'size' => [700, 850],
            ],
        ];

    /**
     * @var array default thumbnail size, using in filemanager view.
     */
    private static $defaultThumbSize = [128, 128];

    public function init()
    {
        parent::init();
        $this->registerTranslations();
    }

    public function registerTranslations()
    {
        Yii::$app->i18n->translations['modules/filemanager/*'] = [
            'class' => 'yii\i18n\PhpMessageSource',
            'sourceLanguage' => 'en-US',
            'basePath' => '@vendor/ktree/filemanager/messages',
            'fileMap' => [
                'modules/filemanager/main' => 'main.php',
            ],
        ];
    }

    public static function t($category, $message, $params = [], $language = null)
    {
        if (!isset(Yii::$app->i18n->translations['modules/filemanager/*'])) {
            return $message;
        }

        return Yii::t("modules/filemanager/$category", $message, $params, $language);
    }

    /**
     * @return array default thumbnail size. Using in filemanager view.
     */
    public static function getDefaultThumbSize()
    {
        return self::$defaultThumbSize;
    }
}

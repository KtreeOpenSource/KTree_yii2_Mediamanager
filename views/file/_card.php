<?php
/**
 * @link      http://ktree.com/
 * @copyright Copyright (c) 2017 KTree.com.
 * @license   http://ktree.com/license
 */
use yii\helpers\Html;
use yii\helpers\Url;
use ktree\filemanager\models\Mediafile;
use ktree\filemanager\assets\FilemanagerAsset;
use yii\helpers\StringHelper;

$bundle = FilemanagerAsset::register($this);
$imageFileTypes = Mediafile::$imageFileTypes;

$fileName = (strlen($model->filename) > 18) ? StringHelper::truncate(
            $model->filename,
            18,
            $suffix = '...',
            $encoding = null,
            $asHtml = true
        ) : $model->filename;
        
if ($model->type == Mediafile::TYPE) {
    $filemanagerUrl = [
            'file/filemanager-view',
            'parent' => $model->id,
            'parentId' => $model->parent,
            'view' => 'listView'
        ];
    echo Html::a(
        Html::img($bundle->baseUrl.'/images/files.png', ['alt' => 'Files']),
        '#mediafile',
        [
            'data-key' => $model->id,
            'url' => Url::to($filemanagerUrl),
            'updateUrl' => Url::to(['file/update-folder', 'id' => $model->id])
        ]
    );
    echo Html::beginTag('div', ['class'=>'media-manager-title']);
    echo $fileName;
    echo Html::endTag('div');
} elseif ($model->type == Mediafile::EMBED_VIDEO_TYPE) {
    echo \ktree\filemanager\widgets\VideoEmbed::widget(['url' => $model->url]);
    echo Html::beginTag('div', ['class'=>'media-manager-title']);
    echo $fileName;
    echo Html::endTag('div');
} else {
    if (in_array($model->type, $imageFileTypes)) {
        echo Html::a(
                  Html::img(
                      Yii::getAlias('@web') . $model->getDefaultThumbUrl(
                          $this->params['routes']['baseUrl']
                      )
                  ),
                  '#mediafile',
                  ['data-key' => $key]
              );
        echo Html::beginTag('div', ['class'=>'media-manager-title']);
        echo $fileName;
        echo Html::endTag('div');
    } else {
        echo Html::a(
                  Html::img($bundle->baseUrl.'/images/file.png', ['style'=>'height:30px;width:30px;']),
                  '#mediafile',
                  ['data-key' => $key]
              );
        echo Html::beginTag('div', ['class'=>'media-manager-title']);
        echo $fileName;
        echo Html::endTag('div');
    }
}

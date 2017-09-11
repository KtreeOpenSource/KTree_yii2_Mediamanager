<?php
/**
 * @link      http://ktree.com/
 * @copyright Copyright (c) 2017 KTree.com.
 * @license   http://ktree.com/license
 */
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ListView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel pendalf89\filemanager\models\Mediafile */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->params['routes'] = $routes;
?>
<div id="filemanager" data-url-info="<?= Url::to(['file/info']) ?>">
    <div class="box-body">
        <div class="table-responsive">
            <?php Pjax::begin(
                [
                    'id' => 'pjax-grid-filtering',
                    'timeout' => false,
                    'enablePushState' => false,
                    'clientOptions' => [
                        'method' => 'GET'
                    ]
                ]
            );?>
            <?=
            ListView::widget(
                [
                    'dataProvider' => $dataProvider,
                    'id' => 'ajaxListView',
                    'layout' => '<div class="items">{items}</div>{pager}',
                    'itemOptions' => ['class' => 'item'],
                    'itemView' => function ($model, $key, $index, $widget) {
                        if ($model->type == 'folder') {
                            $filemanagerUrl = [
                                        'file/filemanager-view',
                                        'parent' => $model->id,
                                        'parentId' => $model->parent,
                                        'view' => 'listView'
                                    ];
                            return Html::a(
                                    Html::img('@web/resources/images/files.png', ['alt' => 'Files'])
                                    . '<span id="folderName' . $model->id . '" class="folderName">' . $model->filename
                                    . '</span><span class="checked glyphicon glyphicon-check"></span>',
                                    '#mediafolder',
                                    [
                                        'data-key' => $model->id,
                                        'url' => Url::to($filemanagerUrl),
                                        'updateUrl' => Url::to(['file/update-folder', 'id' => $model->id])
                                    ]
                                );
                        } else {
                            if (file_exists(
                                    substr($model->getDefaultThumbUrl($this->params['routes']['baseUrl']), 1)
                                )
                                ) {
                                return Html::a(
                                        Html::img(
                                            Yii::getAlias('@web') . $model->getDefaultThumbUrl(
                                                $this->params['routes']['baseUrl']
                                            )
                                        ) . '<span class="checked glyphicon glyphicon-check"></span>',
                                        '#mediafile',
                                        ['data-key' => $key]
                                    );
                            } else {
                                return Html::a(
                                        $model->filename . '<span class="checked glyphicon glyphicon-check"></span>',
                                        '#mediafile',
                                        ['data-key' => $key]
                                    );
                            }
                        }
                    },
                ]
            ) ?>
            <?php Pjax::end(); ?>
        </div>
    </div>
</div>

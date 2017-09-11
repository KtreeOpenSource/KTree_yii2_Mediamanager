<?php
/**
 * @link      http://ktree.com/
 * @copyright Copyright (c) 2017 KTree.com.
 * @license   http://ktree.com/license
 */
use ktree\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use ktree\filemanager\Module;
use ktree\filemanager\models\Mediafile;
use yii\jui\DatePicker;

$this->params['routes'] = $routes;
$imageFileTypes = Mediafile::$imageFileTypes;
$params = Yii::$app->request->get();

?>
<?php
    $searchUrl = [
        'file/filemanager-view',
        'popup' => $popup,
    ];
?>

<div id="filemanager"
     data-url-info="<?= Url::to(['file/info', 'popup' => $popup]) ?>"
     class="filemanager-grid admin-grid">
    <?=
    GridView::widget(
        [
            'dataProvider' => $dataProvider,
            'options' => ['id' => 'kt_media_grid'],
            'pageSize' => true,
            'globalSearch' => [
                'searchField' => 'fileName',
                'value' => $params['Mediafile']['fileName']
            ],
            'cardView' => [
                'template' => '_card',
            ],
            'filterSelector' => '.kt_media_grid_search',
            'filterModel' => $model,
            'model' => $dataProvider->query->modelClass,
            'columns' => [
                [
                    'class' => 'ktree\grid\CheckboxColumn',
                    'checkboxOptions' => function ($model, $key, $index, $column) {
                        return ['id' => 'mediafile_' . $model->id, 'class' => 'mediafile-checkbox'];
                    }
                    // you may configure additional properties here
                ],
                [
                    'label' => Module::t('main', 'Image'),
                    'format' => 'raw',
                    'value' => function ($data) use ($imageFileTypes, $bundle) {
                        if ($data->type == Mediafile::TYPE) {
                            return Html::img($bundle->baseUrl.'/images/files.png',
                                    ['alt' => 'Files', 'style' => "height:30px;width:30px"]);
                        } elseif (in_array($data->type, $imageFileTypes)) {
                            return Html::img(
                                    Yii::getAlias('@web') . $data->getDefaultThumbUrl(
                                        $this->params['routes']['baseUrl']
                                    ),
                                    ['alt' => $data->alt]
                                );
                        } elseif ($data->type == Mediafile::EMBED_VIDEO_TYPE) {
                            return \ktree\filemanager\widgets\VideoEmbed::widget(['url' => $data->url]);
                        } else {
                            return Html::img(
                                    $bundle->baseUrl.'/images/file.png',
                                    ['alt' => $data->alt,'style'=>'height:30px;weight:30px;']
                                );
                        }
                    },
                ],
                [
					'attribute' => 'filename',
					'contentOptions' => ['class' => 'media_file_name'],
				],
                'type',
                [
                    'attribute' => 'size',
                    'filter' => '',
                    'label' => Module::t('main', 'Size'),
                    'value' => function ($data) {
                        if ($data->size != '') {
                            if ($data->size >= 1024) {
                                $data->size = $data->size * 0.001;
                                return $data->size . 'KB';
                            } else {
                                if ($data->size >= 1024 * 1000) {
                                    $data->size = $data->size * 0.001;
                                    return $data->size . 'MB';
                                } else {
                                    if ($data->size >= 1024 * 1000 * 1000) {
                                        $data->size = $data->size * 0.001;
                                        return $data->size . 'GB';
                                    } else {
                                        return $data->size . 'bytes';
                                    }
                                }
                            }
                        } else {
                            return '';
                        }
                    },
                ],
                [
                    'attribute' => 'updated_at',
                    'format' => ['Date', Yii::$app->formatter->dateFormat], //'Y-MM-dd'
                    'filterInputOptions' => [
                        'class' => 'form-control updated_at',
                    ],
                    'filter' => DatePicker::widget(
                            [
                                'model' => $model,
                                'attribute' => 'updated_at',
                                'dateFormat' => Yii::$app->formatter->dateFormat,
                                'options' => ['class' => 'form-control'],
                            ]
                        ),

                ],
                [
                    'class' => 'ktree\grid\ActionColumn',
                    'header' => Module::t('main', 'Actions'),
                    'template' => '{update}{delete}{view}{folder_view}',
                    'visibleButtons' => [
                        'view' => function ($model, $key, $index) use ($popup) {
                            return ($popup == 1 && $model->type != 'folder') ? true : false;
                        },
                        'folder_view' => function ($model, $key, $index) {
                            return ($model->type == 'folder') ? true : false;
                        }
                    ],
                    'buttons' => [
                        'update' => function ($url, $model) {
                            return Html::a(
                                    '<span class="glyphicon glyphicon-pencil"></span>',
                                    '#mediafile',
                                    ['title' => Yii::t('app', 'Edit'), 'data-key' => $model->id, 'data-name' => $model->filename, 'data-pjax' => '0']
                                );
                        },
                        'delete' => function ($url, $model) {
                            $url = Url::toRoute(['file/delete/', 'id' => $model->id]);
                            return Html::a(
                                    '<span class="glyphicon glyphicon-trash"></span>',
                                    $url,
                                    [
                                        'title' => Yii::t('app', 'Delete'),
                                        'class' => 'text-delete',
                                        'data-id' => $model->id,
                                        'role' => 'mediafile-delete',
                                        'type' => $model->type,
                                        'data-pjax' => '0'
                                    ]
                                );
                        },
                        'view' => function ($url, $model) {
                            return Html::a(
                                    '<span class="glyphicon glyphicon glyphicon-plus"></span>',
                                    Url::toRoute(['file/insert', 'id' => $model->id]),
                                    ['title' => Yii::t('app', 'Insert'), 'id' => 'insert-btn', 'data-key' => $model->id, 'data-pjax' => '0']
                                );
                        },
                        'folder_view' => function ($url, $model) {
                            $url = Url::toRoute(['file/filemanager-view', 'parent' => $model->id]);
                            return Html::a(
                                    '<span class="glyphicon glyphicon-eye-open"></span>',
                                    $url,
                                    [
                                        'title' => Yii::t('app', 'View'),
                                        'class' => 'text-view filemanager-folder-view',
                                        'data-id' => $model->id,
                                        'role' => 'mediafile-folder',
                                        'type' => $model->type,
                                        'data-pjax' => '0'
                                    ]
                                );
                        },
                    ],
                ],
            ]
        ]
    ); ?>
</div>

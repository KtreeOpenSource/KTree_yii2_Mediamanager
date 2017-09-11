<?php
/**
 * @link      http://ktree.com/
 * @copyright Copyright (c) 2017 KTree.com.
 * @license   http://ktree.com/license
 */
use ktree\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use ktree\filemanager\Module;
use ktree\filemanager\models\Mediafile;
use yii\jui\DatePicker;

?>
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

<?php
$imageFileTypes = Mediafile::$imageFileTypes;

$gridColumns[] = [
  'label' => Module::t('main', 'Image'),
  'format' => 'raw',
  'value' => function ($data) use ($imageFileTypes, $bundle, $routes, $inputAttribute) {
      $hiddenField = Html::hiddenInput('media-form-'.$inputAttribute.'[media_attachments]['.$data->id.']', $data->id,
      ['class'=>'media_attachments_'.$data->id]);
      if (in_array($data->type, $imageFileTypes)) {
          return $hiddenField.Html::img(
              Yii::getAlias('@web'). $data->getDefaultThumbUrl(
                  $routes['baseUrl']
              ),
              ['alt' => $data->alt,'style'=>'height:30px;weight:30px;']
            );
      } elseif ($data->type == Mediafile::EMBED_VIDEO_TYPE) {
          return $hiddenField.\ktree\filemanager\widgets\VideoEmbed::widget(['url' => $data->url]);
      } else {
          return $hiddenField.Html::img(
                $bundle->baseUrl,
                ['alt' => $data->alt,'style'=>'height:30px;weight:30px;']
              );
      }
  },
];
$gridColumns[] = 'type';
$gridColumns[] = [
	'attribute' => 'filename',
	'contentOptions' => ['class' => 'media_file_name'],
];
$gridColumns[] = [
    'attribute' => 'updated_at',
    'format' => ['Date', Yii::$app->formatter->dateFormat], //'Y-MM-dd'
    'filterInputOptions' => [
            'class' => 'form-control updated_at',
    ],
  'filter'=>$dateFilter = DatePicker::widget(
    [
      'model' => $model,
      'attribute' => 'updated_at',
      'dateFormat' => Yii::$app->formatter->dateFormat,
      'options' => ['class' => 'form-control'],
    ]
  ),

];

$gridColumns[] = [
    'class' => 'ktree\grid\ActionColumn',
    'header' => Module::t('main', 'Actions'),
    'template' => '{delete}',
    'buttons' => [
        'delete' => function ($url, $model) {
            return Html::button(
                    yii::t('app', 'Delete'),
                    [
                        'title' => Yii::t('app', 'Delete'),
                        'class' => 'btn btn-default delete-media-manager',
                        'data-rel' => $model->id,
                    ]
                );
        },
    ],
];

?>
<div id="media-manager-grid" class="media-manager-grid admin-grid">
  <?= GridView::widget(
    [
      'dataProvider' => $dataProvider,
      'tableOptions' => [
        'class' => 'table table-striped table-bordered',
        'id' => 'grid-view-media-manager'
      ],
      'options' => ['id'=>'grid-view-media-manager','class'=>'grid-view panel panel-primary'],
      'title' => Yii::t('app','Media Manager'),
      'filterModel' => $model,
      'pageSize'=>true,
      'columns' => $gridColumns
    ]
  ); ?>
</div>
<?php Pjax::end(); ?>

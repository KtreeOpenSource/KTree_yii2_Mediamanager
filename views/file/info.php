<?php
/**
 * @link      http://ktree.com/
 * @copyright Copyright (c) 2017 KTree.com.
 * @license   http://ktree.com/license
 */
use ktree\filemanager\assets\FilemanagerAsset;
use ktree\filemanager\Module;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use ktree\filemanager\models\Mediafile;

/* @var $this yii\web\View */
/* @var $model system\filemanager\models\Mediafile */
/* @var $form ktree\app\widgets\ActiveForm */

$bundle = FilemanagerAsset::register($this);
$this->params['routes'] = $routes;

?>

<?php
  if ($model->isImage()) {
      echo Html::img(Yii::getAlias('@web') . $model->getDefaultThumbUrl(
        $this->params['routes']['baseUrl']
    ));
  } elseif ($model->type == Mediafile::TYPE) {
      echo Html::img($bundle->baseUrl.'/images/files.png',
        ['alt' => 'Files', 'style' => "height:30px;width:30px"]);
  } elseif ($model->type == Mediafile::EMBED_VIDEO_TYPE) {
      echo \ktree\filemanager\widgets\VideoEmbed::widget(['url' => $model->url]);
  } else {
      echo Html::img($bundle->baseUrl.'/images/file.png', ['style'=>'height:100px;width:100px;']);
  }?>

<ul class="detail">
    <li><?= $model->type ?></li>
    <li><?= Yii::$app->formatter->asDatetime($model->getLastChanges()) ?></li>
    <?php if ($model->isImage()) {
      ?>
        <li><?= $model->getOriginalImageSize($this->context->module->routes) ?></li>
    <?php
  }?>
    <li><?= $model->getFileSize() ?></li>
    <li><?=
        Html::a(
            Module::t('main', 'Delete'),
            ['file/delete/', 'id' => $model->id],
            [
                'class' => 'text-danger',
                'data-id' => $model->id,
                'role' => 'mediafile-delete',
            ]
        ) ?></li>
    <li class="filename"><?= $model->filename ?></li>
</ul>
<div class="clearfix"></div>
<?php $form = ActiveForm::begin(
    [
        'action' => ['file/update', 'id' => $model->id,'popup'=>$popup],
        'id'=> 'control-form',
        'options' => ['class'=>'control-form']
    ]
); ?>
<?= $form->field($model, 'description')->textArea(['rows' => 3,'class' => 'form-control']); ?>

<?= $form->field($model, 'type')->hiddenInput(['class' => 'form-control input-sm'])->label(false); ?>

<?php if ($model->isImage()) {
    ?>
  <?= $form->field($model, 'alt')->textInput(['class' => 'form-control','maxlength' => true]); ?>

<?php
} ?>

<?= Html::hiddenInput('url', $model->url) ?>

<?= Html::hiddenInput('id', $model->id) ?>

<div class="box-footer">
  <?php
      if (($model->type != 'folder') && ($popup == 1)) {
          echo Html::a(Module::t('main', 'Insert'), Url::toRoute(['file/insert', 'id' => $model->id]), ['title' => Yii::t('app', 'Insert'),'id' => 'insert-btn','data-key' => $model->id,'class' => 'btn btn-secondary']);
      }
      echo Html::submitButton(Module::t('main', 'Update'), ['class' => 'btn btn-primary']);
  ?>
  <div class="clearfix"></div>
</div>

<?php ActiveForm::end(); ?>

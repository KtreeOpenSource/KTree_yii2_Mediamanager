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

/* @var $this yii\web\View */
/* @var $model system\filemanager\models\Mediafile */
/* @var $form yii\widgets\ActiveForm */

$bundle = FilemanagerAsset::register($this);
$this->params['routes'] = $routes;
?>
<?php $form = ActiveForm::begin(
    [
        'action' => ['file/update', 'id' => $model->id],
        'id' => 'control_form_'.$model->id,
        'options' => ['class'=>'control-form','name'=>'foo'],
    ]
); ?>
<?php if ($model->isImage()) : ?>
    <?= $form->field($model, 'alt')->hiddenInput(['class' => 'form-control input-sm'])->label(false); ?>
<?php endif; ?>

<?= $form->field($model, 'description')->hiddenInput(['class' => 'form-control input-sm'])->label(false); ?>

<?= $form->field($model, 'type')->hiddenInput(['class' => 'form-control input-sm'])->label(false); ?>

 <?= Html::hiddenInput('url', $model->url) ?>


<?= Html::hiddenInput('id', $model->id) ?>

<?= Html::a('<span class="glyphicon glyphicon glyphicon-plus"></span>', '#', ['id' => 'insert-btn']) ?>

<?php ActiveForm::end(); ?>

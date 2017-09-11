<?php
/**
 * @link      http://ktree.com/
 * @copyright Copyright (c) 2017 KTree.com.
 * @license   http://ktree.com/license
 */
use ktree\filemanager\Module;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model system\filemanager\models\Mediafile */
/* @var $form yii\widgets\ActiveForm */

?>
<?php
    $createVideoUrl = [
      'file/save-embed-video',
      'parent' => $parent,
      'parentId' => $parentId,
    ];
?>
<?php $form = ActiveForm::begin(
    [
        'enableAjaxValidation' => true,
        'action' => $createVideoUrl,
        'options' => ['id' => 'saveVideo-form']
    ]
);  ?>

<?= $form->field($model, 'filename')->textInput(['maxlength' => true, 'class' => 'form-control'])->label(Module::t('main', 'Video Title')); ?>

<?= $form->field($model, 'url')->textInput(['maxlength' => true, 'class' => 'form-control']); ?>

<div class="box-footer">
  <?php
      echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'),
      ['class' => 'btn btn-primary']);
  ?>

</div>
<div class="clearfix"></div>
<?php ActiveForm::end(); ?>

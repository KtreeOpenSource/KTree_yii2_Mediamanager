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
/* @var $searchModel system\filemanager\models\Mediafile */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->params['moduleBundle'] = FilemanagerAsset::register($this);
$this->params['routes'] = $routes;
?>

<?= Html::img($this->params['moduleBundle']->baseUrl . '/images/files.png', ['alt' => 'Files']) ?>

<ul class="detail">
    <li><?= $model->type ?></li>
    <li><?= Yii::$app->formatter->asDatetime($model->getLastChanges()) ?></li>
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
</ul>

<div class="filename"><?= $model->filename ?></div>

<?php $form = ActiveForm::begin(
    [
        'enableAjaxValidation' => true,
        'action' => ['file/update-folder', 'id' => $model->id],
        'options' => ['id' => 'updateFolder-form'],
    ]
);  ?>
<?= $form->field($model, 'id')->hiddenInput(['class' => 'form-control input-sm folder_id'])->label(false); ?>
<?=
$form->field($model, 'filename')->textInput(['class' => 'form-control input-sm folder_filename'])->label(
    'Folder Name'
); ?>
<?=
Html::submitButton(
    $model->isNewRecord
        ? '<span class="glyphicon glyphicon-plus-sign"></span> ' . Yii::t('app', 'Create')
        : Yii::t(
        'app',
        'Update'
    ),
    ['class' => 'btn btn-success', 'id' => $model->id]
) ?>


<?php if ($message = Yii::$app->session->getFlash('mediafileUpdateResult')) : ?>
    <div class="text-success"><?= $message ?></div>
<?php endif; ?>
<?php ActiveForm::end(); ?>

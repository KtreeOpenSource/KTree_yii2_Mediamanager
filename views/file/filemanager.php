<?php

use ktree\filemanager\assets\FilemanagerAsset;
use ktree\filemanager\Module;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\widgets\ListView;

/* @var $this yii\web\View */
/* @var $searchModel system\filemanager\models\Mediafile */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->params['moduleBundle'] = FilemanagerAsset::register($this);
$this->params['routes'] = $routes;
?>

<header id="header"><span class="glyphicon glyphicon-picture"></span> <?= Module::t('main', 'File manager') ?></header>

<?php if ($message = Yii::$app->session->getFlash('mediafolderResult')) : ?>
    <div class="text-success"><?= $message ?></div>
<?php endif;
if ($parent != 0) { ?>
        <p><?=
            Html::a(
                '← ' . Module::t('main', 'Back to file manager'),
                ['file/filemanager', 'parent' => $parentId]
            ) ?></p>

<?php }?>


<div id="filemanager" data-url-info="<?= Url::to(['file/info']) ?>">

    <div class="search-form">
        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'filename')->textInput(['placeholder' => 'search'])->label(false); ?>
        <div class="form-group">
            <?= Html::submitButton('Apply', ['class' => 'btn btn-success']) ?>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <?=
    ListView::widget(
        [
            'dataProvider' => $dataProvider,
            'id' => 'ajaxListView',
            'layout' => '<div class="items">{items}</div>{pager}',
            'itemOptions' => ['class' => 'item'],
            'itemView' => function ($model, $key, $index, $widget) {
                $filemanagerUrl = ['file/filemanager', 'parent' => $model->id, 'parentId' => $model->parent];
                if ($model->type == 'folder') {
                    return Html::a(
                            Html::img($this->params['moduleBundle']->baseUrl . '/images/files.png', ['alt' => 'Files'])
                            . '<span id="folderName' . $model->id . '">' . $model->filename
                            . '</span><span class="checked glyphicon glyphicon-check"></span>',
                            '#mediafolder',
                            [
                                'data-key' => $model->id,
                                'url' => Url::to($filemanagerUrl),
                                'updateUrl' => Url::to(['file/update-folder', 'id' => $model->id])
                            ]
                        );
                } else {
                    //echo substr($model->getDefaultThumbUrl($this->params['routes']['baseUrl']), 1);exit;
                        if (file_exists(substr($model->getDefaultThumbUrl($this->params['routes']['baseUrl']), 1))) {
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
    <div class="dashboard">

        <?php
        $createFolderUrl = ['file/create-folder', 'parent' => $parent, 'parentId' => $parentId]; ?>
            <p><?=
                Html::a(
                    '<span class="glyphicon glyphicon-upload"></span> ' . Module::t('main', 'Upload manager'),
                    ['file/uploadmanager', 'parent' => $parent],
                    ['class' => 'btn btn-default']
                ) ?></p>

        <p>
            <button class="btn btn-default create_folder" data-toggle="modal" data-target="#createFolder-dialog"><span
                    class="glyphicon glyphicon-plus"></span><?php echo Module::t('main', 'Create Folder'); ?></button>
        </p>
        <div id="fileinfo">

        </div>
    </div>
</div>

<!--Modal to display widget to create-->
<div class="modal" role="dialog" id="createFolder-dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <?php $form = ActiveForm::begin(
                [
                    'enableAjaxValidation' => true,
                    'action' => $createFolderUrl,
                    'options' => ['id' => 'createFolder-form'],
                ]
            );  ?>
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span
                        aria-hidden="true">×</span></button>
                <h4 class="modal-title"><?= Yii::t('app', 'Create Folder') ?></h4>
            </div>
            <div class="modal-body">
                <?php
                echo $form->field($model, 'filename')->textInput(['class' => 'form-control input-sm'])->label(
                    'Folder Name'
                ); ?>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default pull-left" type="button"><?=
                    Yii::t(
                        'app',
                        'Close'
                    ) ?></button>
                <?=
                Html::submitButton(
                    $model->isNewRecord
                        ? '<span class="glyphicon glyphicon-plus-sign"></span> ' . Yii::t('app', 'Create')
                        : Yii::t(
                        'app',
                        'Update'
                    ),
                    ['class' => 'btn btn-success']
                ) ?>

            </div>
            <?php ActiveForm::end(); ?>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div><!-- /.modal -->

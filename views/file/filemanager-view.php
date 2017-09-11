<?php
/**
 * @link      http://ktree.com/
 * @copyright Copyright (c) 2017 KTree.com.
 * @license   http://ktree.com/license
 */
use ktree\filemanager\Module;
use yii\helpers\Html;
use yii\helpers\Url;
use ktree\filemanager\widgets\JsTreeWidget;
use yii\widgets\Pjax;
use ktree\filemanager\widgets\fileupload\FileUploadUI;

/* @var $this yii\web\View */
/* @var $searchModel cms\content\models\PageSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

\ktree\filemanager\assets\ThemeAsset::register($this);

$this->title = Yii::t('app', 'Media Manager');
$this->params['breadcrumbs'][] = $this->title;
$attributes = $model->attributeLabels();
unset($attributes['thumbs']);
unset($attributes['parent']);

$params = Yii::$app->request->get();
?>
<?php if ($message = Yii::$app->session->getFlash('mediafolderResult')) : ?>
    <?= Html::beginTag('div', ['class' => 'alert-success alert fade in', 'id' => 'w6-success']) ?>
    <?= Html::button('x', ['class' => 'close', 'aria-hidden' => 'true', 'data-dismiss' => 'alert']) ?>
    <?= Html::Tag('i', '', ['class' => 'icon fa fa-check']) ?>
    <?= $message ?>
    <?= Html::endTag('div') ?>
<?php endif; ?>
<div class="main-content-header">
    <h1><?php echo Module::t('app', 'Media Manager'); ?></h1>

    <div class="container">
        <div class="btn-container pull-right global-buttons">
            <input type="hidden" id="filemanager_parent"
                   value="<?= $parent; ?>">
            <?php if ($parent != 0) {
    ?>
                    <?=

                    Html::a(
                        'Back',
                        ['file/filemanager-view', 'parent' => $parentId, 'popup' => $popup],
                        ['class' => 'btn btn-secondary back_button']
                    ) ?>
            <?php
}?>
            <?= Html::hiddenInput('images_selected', '', ['class' => 'images_selected']) ?>
            <?= Html::a(Module::t('app', 'Delete'), Url::toRoute(['file/mass-delete']), ['class' => 'btn btn-secondary', 'role' => 'mediafile-mass-delete']) ?>
            <?=
            Html::a(
                '',
                Url::toRoute(['file/filemanager-view']),
                ['class' => 'btn btn-primary glyphicon glyphicon-home back_button']
            ) ?>
        </div>

    </div>
</div>

<div class="cat-left-container">
    <div class="box-body" id="folder-tree">
        <div class="row media-manager-buttons-row">
            <div class="col-md-4">
                <div class="form-group pull-left">
                    <?=
                    Html::a('<span class="glyphicon glyphicon-plus"></span> ' . Module::t('main', 'New Folder'),
                        ['file/create-folder', 'parent' => $parent],
                        ['class' => 'btn btn-default create_folder']
                    ) ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group pull-left">
                    <?=
                    Html::a('<span class="glyphicon glyphicon-plus"></span>' . Module::t('main', 'Upload File'),
                        '#',
                        ['class' => 'btn btn-default upload-file']
                    ) ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group pull-left">
                    <?=
                    Html::a('<span class="glyphicon glyphicon-plus"></span>' . Module::t('main', ' Root'),
                        ['file/create-folder', 'parent' => 0],
                        ['class' => 'btn btn-default add-media-root']
                    ) ?>
                </div>
            </div>


        </div>
        <div class="folder_search">
          <span class="glyphicon glyphicon-search folder-search-icon"></span>
            <input id="folder-search" type="text" autofocus=""
                   placeholder="Search folder"
                   class="folder-search form-control">
            <span
                class="glyphicon glyphicon-remove remove_folder_search"></span>
        </div>
        <input type="hidden" name="popup-visible" class='popup-visible'
               value="<?php echo $popup; ?>">

        <div class="treeoverflow">
            <div class="col-md-12 content-categories">

                <?php
                /**
                 * Here it jstree widget to get Tree dynamically,
                 * Here we can pass model and tree related model class and parent value of model
                 * or else we can use it with out model
                 * and pass plugin's that that you need,
                 */
                echo JsTreeWidget::widget(
                    [ //for using include this "ktree\admin\widgets\JsTreeWidget"

                        'attribute' => 'filename',
                        'model' => $model,
                        'data' => $model->getFilemanagerFolderData(),
                        'parentId' => 0,
                        'core' => [
                            'check_callback' => true,
                            'plugins' => [
                                'types',
                                'dnd',
                                'contextmenu',
                                'wholerow',
                                'state',
                                'search',
                                'crrm',
                                'checkbox'
                            ],
                        ],
                        'dnd' => [
                            'check_while_dragging' => true
                        ],
                        'plugins' => [
                            'dnd',
                        ],
                    ]
                );
                ?>

            </div>
        </div>
    </div>
</div>

<div class="cat-right-container data">
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

    <div class="filemanager-grid">
        <?php
            echo Yii::$app->controller->renderPartial(
                'gridview',
                [
                    'dataProvider' => $dataProvider,
                    'parent' => $parent,
                    'parentId' => $parentId,
                    'routes' => $routes,
                    'model' => $model,
                    'popup' => $popup,
                    'bundle' => $bundle
                ]
            );
        ?>
    </div>
    <?php Pjax::end(); ?>
    <div class="clearfix"></div>
    <div class="media-manager-upload-view">
        <div id="uploadmanager">
            <?php
            $fileManagerModule = Yii::$app->getModule('filemanager');
            $autoLoad = '';
            if ($fileManagerModule) {
                $autoLoad = $fileManagerModule->autoUpload;
            }
            ?>
            <?=
            FileUploadUI::widget(
                [
                    'model' => $model,
                    'attribute' => 'file',
                    'parent' => $parent,
                    'title' => Module::t('main', 'Click here to upload'),
                    'clientOptions' => [
                        'autoUpload' => $autoLoad,
                    ],
                    'url' => ['upload', 'parent' => $parent, 'popup' => $popup],
                    'gallery' => false,
                ]
            ) ?>
        </div>
    </div>
    <input type="hidden" name="id" class='page-load-id'
           value="<?php echo $id; ?>">

</div>

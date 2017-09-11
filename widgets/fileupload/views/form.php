<?php
/**
 * @link      http://ktree.com/
 * @copyright Copyright (c) 2017 KTree.com.
 * @license   http://ktree.com/license
 */
use yii\helpers\Html;
use ktree\filemanager\Module;
use yii\helpers\Url;

$context = $this->context;

?>
    <!-- The file upload form used as target for the file upload widget -->
<?= Html::beginTag('div', $context->options); ?>

    <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
    <div class="row fileupload-buttonbar" style="display:none;">
      <!-- The global progress state -->
      <div class="col-lg-5 col-sm-5 fileupload-progress fade btn-container pull-right">
          <!-- The global progress bar -->
          <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
              <div class="progress-bar progress-bar-success" style="width:0%;"></div>
          </div>
          <!-- The extended global progress state -->
          <div class="progress-extended">&nbsp;</div>
      </div>
      <div class="btn-container upload-file-options">
        <!-- The table listing the files available for upload/download -->
        <ul class="files"></ul>
      </div>
    </div>
    <div class="file-upload-area">
      <input type="hidden" id="fileupload-parent" value="<?= $context->url['parent']?>">
      <input type="hidden" id="fileupload-insert" value="<?= $context->url['popup']?>">
      <div class="drop-element drop-zone-cont">
        <div class="drop-zone " style="">
          <span class="drop-text"><?= Module::t('app', 'Drag your files/folders here') ?></span>
          <?= $context->model instanceof \yii\base\Model && $context->attribute !== null
          ? Html::activeFileInput($context->model, $context->attribute, $context->fieldOptions)
          : Html::fileInput($context->name, $context->value, $context->fieldOptions);?>
        </div>
      </div>
      <div class="drop-element drop-or"> <?= Module::t('app', 'Or') ?></div>
        <!-- The fileinput-button span is used to style the file input field as button -->
      <div class="drop-element drop-button">
         <div class="col-md-7 col-sm-8 col-xs-12">
        <span class="btn btn-primary fileinput-button">
          <span><?= $context->title ?></span>
          <?php $context->fieldOptions['id'] = 'media-file-upload';?>
          <?= $context->model instanceof \yii\base\Model && $context->attribute !== null
          ? Html::activeFileInput($context->model, $context->attribute, $context->fieldOptions)
          : Html::fileInput($context->name, $context->value, $context->fieldOptions);?>
        </span>
          <br/>
        <span class="file-upload-text"><?= Module::t('app', 'Press CTRL + to select multiple files')?></span>
      </div>
      <div class="col-md-4 col-sm-4 col-xs-12">
        <?= Html::a(Module::t('app', 'Embed Video'), Url::toRoute(['file/save-embed-video','parent' => $context->parent]), ['class' =>'btn btn-primary','role' => 'mediafile-embed-video']) ?>
    </div>
  </div>
    <div class="clearfix"></div>
  </div>
<?= Html::endTag('div');?>

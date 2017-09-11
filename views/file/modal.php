<?php
/**
 * @link      http://ktree.com/
 * @copyright Copyright (c) 2017 KTree.com.
 * @license   http://ktree.com/license
 */
?>
<div role="filemanager-modal" data-backdrop="false" class="modal filemanager-modal" tabindex="-1"
     data-frame-id="<?= $frameId ?>"
     data-displayimage-class="<?= ($displayImageClass) ? $displayImageClass : '' ?>"
     data-hiddenimage-id="<?= ($hiddenImage) ? $hiddenImage : '' ?>"
     data-image-validation="<?= ($imageValidation) ? $imageValidation : '' ?>"
     data-frame-src="<?= $frameSrc ?>"
     data-btn-id="<?= $btnId ?>"
     data-input-id="<?= $inputId ?>"
     data-image-container="<?= isset($imageContainer) ? $imageContainer : '' ?>"
     data-paste-data="<?= isset($pasteData) ? $pasteData : '' ?>"
     data-thumb="<?= $thumb ?>"
     data-multiple="<?= $multiple ?>"
     data-displayGridView="<?= (!empty($displayGridView)) ? $displayGridView : null ?>"
     data-input-attribute="<?= $dataInputAttribute ?>">


    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
              <button type="button" class="upload-filemanager-close-btn" data-btn-id="<?= $btnId ?>">&times;</button>
              <div class="filemanager-modal-body"></div>
            </div>
        </div>
    </div>
</div>

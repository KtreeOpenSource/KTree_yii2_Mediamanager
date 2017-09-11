<?php
/**
 * @link      http://ktree.com/
 * @copyright Copyright (c) 2017 KTree.com.
 * @license   http://ktree.com/license
 */
/** @var string $input the code for the input */
?>

<span class="btn btn-success fileinput-button">
   <i class="glyphicon glyphicon-plus"></i>
   <span><?= Yii::t('app', 'Select file...') ?></span>
   <!-- The file input field used as target for the file upload widget -->
    <?= $input ?>
</span>

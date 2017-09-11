<?php
/**
 * @link      http://ktree.com/
 * @copyright Copyright (c) 2017 KTree.com.
 * @license   http://ktree.com/license
 */
use ktree\filemanager\assets\FilemanagerAsset;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */

FilemanagerAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<?= $content ?>
<?php

\yii\bootstrap\Modal::begin(
    [
        'headerOptions' => ['id' => 'modalHeader'],
        'id' => 'global-modal',
        'size' => 'modal-lg',
        'clientOptions' => ['keyboard' => false],
    ]
);
echo "<div id='modalContent'></div>";

\yii\bootstrap\Modal::end();

?>
<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>

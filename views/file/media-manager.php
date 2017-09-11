<?php
/**
 * @link      http://ktree.com/
 * @copyright Copyright (c) 2017 KTree.com.
 * @license   http://ktree.com/license
 */
use ktree\filemanager\models\Mediafile;
use yii\helpers\Html;

$imageTypes = Mediafile::$imageFileTypes;
?>

<tr data-key="<?= $mediaModel->id ?>">
    <td>
        <?php if ($image != '' && in_array($mediaModel->type, $imageTypes)) {
    ?>
            <?=
            Html::hiddenInput(
                'media-form-'.$inputAttribute.'[media_attachments][' . $mediaModel->id . ']',
                $mediaModel->id,
                ['class' => 'media_attachments_' . $mediaModel->id]
            ) . Html::img($image, ['style'=>'height:30px;weight:30px;'])?>
        <?php
} else {
                if ($mediaModel->type == Mediafile::EMBED_VIDEO_TYPE) {
                    echo Html::hiddenInput(
                        'media-form-'.$inputAttribute.'[media_attachments][' . $mediaModel->id . ']',
                        $mediaModel->id,
                        ['class' => 'media_attachments_' . $mediaModel->id]
                    ) . \ktree\filemanager\widgets\VideoEmbed::widget(['url' => $mediaModel->url]);
                } else {
                    echo Html::hiddenInput(
                        'media-form-'.$inputAttribute.'[media_attachments][' . $mediaModel->id . ']',
                        $mediaModel->id,
                        ['class' => 'media_attachments_' . $mediaModel->id]
                    ) . Html::img(
                        $bundle->baseUrl.'/images/file.png',
                        ['alt' => $mediaModel->alt,'style'=>'height:30px;weight:30px;']
                    );
                }
            }?>
    </td>
    <td><?= $mediaModel->type ?></td>
    <td class = 'media_file_name'><?= $mediaModel->filename ?></td>
    <td><?= Yii::$app->formatter->asDate($mediaModel->created_at, Yii::$app->formatter->dateFormat) ?></td>
    <td><?=
        Html::button(
            yii::t('app', 'Delete'),
            [
                'class' => 'btn btn-default delete-media-manager',
                'data-rel' => $mediaModel->id
            ]
        )?></td>
</tr>

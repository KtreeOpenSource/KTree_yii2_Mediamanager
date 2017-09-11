<?php
/**
 * @link      http://ktree.com/
 * @copyright Copyright (c) 2017 KTree.com.
 * @license   http://ktree.com/license
 */
namespace ktree\filemanager\widgets;

use ktree\filemanager\assets\FileInputAsset;
use ktree\filemanager\models\Mediafile;
use yii\helpers\Html;
use Yii;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\InputWidget;

/**
 * Class FileInput
 *
 * Basic example of usage:
 *
 *  <?= FileInput::widget([
 *      'name' => 'mediafile',
 *      'buttonTag' => 'button',
 *      'buttonName' => 'Browse',
 *      'buttonOptions' => ['class' => 'btn btn-default'],
 *      'options' => ['class' => 'form-control'],
 *      // Widget template
 *      'template' => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',
 *      // Optional, if set, only this image can be selected by user
 *      'thumb' => 'original',
 *      // Optional, if set, in container will be inserted selected image
 *      'imageContainer' => '.img',
 *      // Default to FileInput::DATA_URL. This data will be inserted in input field
 *      'pasteData' => FileInput::DATA_URL,
 *      // JavaScript function, which will be called before insert file data to input.
 *      // Argument data contains file data.
 *      // data example: [alt: "Witch with cat", description: "123", url: "/uploads/2014/12/vedma-100x100.jpeg", id: "45"]
 *      'callbackBeforeInsert' => 'function(e, data) {
 *      console.log( data );
 *      }',
 *  ]) ?>
 *
 * This class provides filemanager usage. You can optional select all media file info to your input field.
 * More samples of usage see on github: https://github.com/PendalF89/yii2-filemanager
 *
 * @package ktree\filemanager\widgets
 * @author  Zabolotskikh Boris <zabolotskich@bk.ru>
 */
class FileInput extends InputWidget
{
    /**
     * @var string widget template
     */
    public $template = '<div class="input-group">{input}<span class="input-group-btn">{button}{reset-button}</span></div>';

    /**
     * @var string button tag
     */
    public $buttonTag = 'button';

    /**
     * @var string button name
     */
    public $buttonName = 'Browse';

    /**
     * @var array button html options
     */
    public $buttonOptions = ['class' => 'btn btn-default'];

    /**
     * @var string reset button tag
     */
    public $resetButtonTag = 'button';

    /**
     * @var string reset button name
     */
    public $resetButtonName = '<span class="text-danger glyphicon glyphicon-remove"></span>';

    /**
     * @var array reset button html options
     */
    public $resetButtonOptions = ['class' => 'btn btn-default'];

    /**
     * @var string Optional, if set, only this image can be selected by user
     */
    public $thumb = '';

    /**
     * @var string Optional, if set, in container will be inserted selected image
     */
    public $imageContainer = '';

    /**
     * @var string JavaScript function, which will be called before insert file data to input.
     * Argument data contains file data.
     * data example: [alt: "Witch with cat", description: "123", url: "/uploads/2014/12/vedma-100x100.jpeg", id: "45"]
     */
    public $callbackBeforeInsert = '';

    /**
     * @var string This data will be inserted in input field
     */
    public $pasteData = self::DATA_URL;

    /**
     * @var array widget html options
     */
    public $options = ['class' => 'form-control'];

    /**
     *
     * @var array selecte the frameSrc in case you use a different module name
     */
    public $frameSrc = ['/admin/filemanager/file/filemanager-view', 'popup' => true];

    /**
     * @var string to select the multiple images
     */
    public $multiple = false;

    /**
     * @var string to display the selected images in grid view
     */
    public $displayGridView = false;

    /**
     * @var array to display the data in grid view or image
     */
    public $mediaData = [];

    const DATA_ID = 'id';
    const DATA_URL = 'url';
    const DATA_ALT = 'alt';
    const DATA_DESCRIPTION = 'description';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->frameSrc = [
            '/filemanager/file/filemanager-view',
            'popup' => true
        ];

        if (empty($this->buttonOptions['id'])) {
            $this->buttonOptions['id'] = $this->options['id'] . '-btn';
        }
        $this->options['display-image-class'] =  isset($this->options['display-image-class']) ? $this->options['display-image-class'] : '';
        $this->options['hidden-image'] = isset($this->options['hidden-image']) ? $this->options['hidden-image'] : '';
        $this->options['image-validation'] = isset($this->options['image-validation']) ? $this->options['image-validation'] : '';
        $this->buttonOptions['role'] = 'filemanager-launch';
        $this->resetButtonOptions['role'] = 'clear-input';
        $this->resetButtonOptions['data-clear-element-id'] = $this->options['id'];
        $this->resetButtonOptions['data-image-container'] = $this->imageContainer;
        $this->resetButtonOptions['data-clear-display-image'] = isset($this->options['display-image-class'])?$this->options['display-image-class']:'';
        $this->resetButtonOptions['data-clear-hiddenelement-id'] = isset($this->options['hidden-image']) ?
        $this->options['hidden-image'] : null;
    }

    /**
     * Runs the widget.
     */
    public function run()
    {
        $routes = Yii::$app->modules['filemanager']['routes'];
        if ($this->hasModel()) {
            $inputAttribute = $this->attribute;
            $replace['{input}'] = Html::activeTextInput($this->model, $this->attribute, $this->options);
        } else {
            $inputAttribute = $this->name;
            $replace['{input}'] = Html::textInput($this->name, $this->value, $this->options);
        }

        if ($this->hasModel()) {
            $inputAttribute = $this->attribute;
            $replace['{hiddenInput}'] = Html::activeHiddenInput($this->model, $this->attribute, $this->options);
        } else {
            $inputAttribute = $this->name;
            $replace['{hiddenInput}'] = Html::hiddenInput($this->name, $this->value, $this->options);
        }

        $replace['{button}'] = Html::tag($this->buttonTag, $this->buttonName, $this->buttonOptions);
        $replace['{reset-button}'] = Html::tag(
            $this->resetButtonTag,
            $this->resetButtonName,
            $this->resetButtonOptions
        );

        $bundle = FileInputAsset::register($this->view);

        if ($this->displayGridView == true) {
            $gridData = Mediafile::getGridData($this->mediaData);
            $dataProvider = $gridData['dataProvider'];
            $model = $gridData['model'];
            $result = Html::beginTag('div', ['class' => 'media_manager_grid_view']);
            $result .= Html::beginTag(
                'div',
                ['class' => 'grid-view', 'style' => ($this->mediaData != '') ? 'display:block' : 'display:none']
            );
            $result .= $this->render(
                '@vendor/ktree/filemanager/views/file/media-manager-grid.php',
                [
                    'dataProvider' => $dataProvider,
                    'model' => $model,
                    'bundle' => $bundle,
                    'routes' => $routes,
                    'inputAttribute' => $inputAttribute
                ]
            );
            $result .= Html::endTag('div');
            $result .= Html::endTag('div');
            $replace['{mediaManagerPreview}'] = $result;
        } else {
            $result = Html::beginTag(
                'div',
                ['class' => isset($this->options['display-image-class'])?$this->options['display-image-class']:'', 'id' => isset($this->options['display-image-class'])?$this->options['display-image-class']:'']
            );
            if ($this->mediaData != '' && $this->pasteData == FileInput::DATA_ID) {
                $model = Mediafile::findOne($this->mediaData);
            } else {
                if ($this->mediaData != '' && $this->pasteData == FileInput::DATA_URL) {
                    $model = Mediafile::find()->where(['url' => $this->mediaData])->one();
                }
            }
            if (isset($model) && $model) {
                if ($model->isImage()) {
                    $result .= Html::img(Yii::getAlias('@web') . '/' .$routes['baseUrl'].'/'. $model->getThumbUrl($this->thumb));
                } elseif ($model->type == Mediafile::EMBED_VIDEO_TYPE) {
                    $result .= \ktree\filemanager\widgets\VideoEmbed::widget(['url' => $model->url]);
                } else {
                    $result .= Html::img($bundle->baseUrl.'/images/file.png', ['style'=>'width:100px;height:100px;']);
                }
            }

            $result .= Html::endTag('div');
            $result .= Html::a(
                'Clear Image',
                '#',
                [
                    'role' => 'clear-input',
                    'data-clear-element-id' => $this->options['id'],
                    'data-image-container' => $this->imageContainer,
                    'data-clear-display-image' => isset($this->options['display-image-class'])?$this->options['display-image-class']:'',
                    'data-clear-hiddenelement-id' => isset($this->options['hidden-image'])?$this->options['hidden-image']:'',
                    'style' => ($this->mediaData != '') ? 'display:block' : 'display:none',
                    'class' => 'media-clear-input'
                ]
            );
            $replace['{mediaManagerPreview}'] = $result;
        }

        $this->view->registerJs(
          "var editorPath = '".Url::Base(true).'/'.$routes['baseUrl'].'/'."';", View::POS_HEAD);


        if (!empty($this->callbackBeforeInsert)) {
            $this->view->registerJs(
                '$("#' . $this->options['id'] . '").on("fileInsert", ' . $this->callbackBeforeInsert . ');'
            );
        }


        $modal = $this->render(
            '@vendor/ktree/filemanager/views/file/modal.php',
            [
                'inputId' => $this->options['id'],
                'btnId' => $this->buttonOptions['id'],
                'frameId' => $this->options['id'] . '-frame',
                'frameSrc' => Url::to($this->frameSrc),
                'thumb' => $this->thumb,
                'imageContainer' => $this->imageContainer,
                'pasteData' => $this->pasteData,
                'displayImageClass' => isset($this->options['display-image-class'])?$this->options['display-image-class']:'',
                'hiddenImage' => isset($this->options['hidden-image']) ?
                $this->options['hidden-image'] : null,
                'imageValidation' => isset($this->options['image-validation'])?$this->options['image-validation']:'',
                'multiple' => $this->multiple,
                'displayGridView' => $this->displayGridView,
                'dataInputAttribute' => $inputAttribute
            ]
        );

        $this->view->registerJs("$('body').append(" . json_encode($modal) . ");");

        return strtr($this->template, $replace);
    }
}

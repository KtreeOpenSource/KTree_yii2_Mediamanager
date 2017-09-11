<?php
/**
 * @link      http://ktree.com/
 * @copyright Copyright (c) 2017 KTree.com.
 * @license   http://ktree.com/license
 */
namespace ktree\filemanager\widgets\tinymce;

use ktree\filemanager\assets\FileInputAsset;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\widgets\InputWidget;

/**
 * TinyMCE renders a tinyMCE js plugin for WYSIWYG editing.
 */
class TinyMce extends InputWidget
{
    /**
     * @var string widget template
     */
    public $template = '{input}';

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
     * @var array select the frameSrc in case you use a different module name
     */
    public $frameSrc = ['/filemanager/file/filemanager-view', 'popup' => true];

    /**
     * @var string to select the multiple images
     */
    public $multiple = true;

    const DATA_ID = 'id';
    const DATA_URL = 'url';
    const DATA_ALT = 'alt';
    const DATA_DESCRIPTION = 'description';
    /**
     * @var string the language to use. Defaults to null (en).
     */
    public $language;
    /**
     * @var array the options for the TinyMCE JS plugin.
     * Please refer to the TinyMCE JS plugin Web page for possible options.
     * @see http://www.tinymce.com/wiki.php/Configuration
     */
    public $clientOptions = [];
    /**
     * @var bool whether to set the on change event for the editor. This is required to be able to validate data.
     * @see https://github.com/2amigos/yii2-tinymce-widget/issues/7
     */
    public $triggerSaveOnBeforeValidateForm = true;

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

        $this->buttonOptions['role'] = 'filemanager-launch';
        $this->resetButtonOptions['role'] = 'clear-input';
        $this->resetButtonOptions['data-clear-element-id'] = $this->options['id'];
        $this->resetButtonOptions['data-image-container'] = $this->imageContainer;
        $this->resetButtonOptions['data-clear-display-image'] = isset($this->options['display-image-class']) ?
        $this->options['display-image-class'] : null;
        $this->resetButtonOptions['data-clear-hiddenelement-id'] = isset($this->options['hidden-image']) ?
        $this->options['hidden-image'] : null;
    }

    /**
     * @inheritdoc
     */
    public function run()
    {
        if ($this->hasModel()) {
            $replace['{input}'] = Html::activeTextarea($this->model, $this->attribute, $this->options);
        } else {
            $replace['{input}'] = Html::textarea($this->name, $this->value, $this->options);
        }
        if ($this->hasModel()) {
            $replace['{hiddenInput}'] = Html::activeHiddenInput($this->model, $this->attribute, $this->options);
        } else {
            $replace['{hiddenInput}'] = Html::hiddenInput($this->name, $this->value, $this->options);
        }

        FileInputAsset::register($this->view);

        if (!empty($this->callbackBeforeInsert)) {
            $this->view->registerJs(
                '
                                $("#' . $this->options['id'] . '").on("fileInsert", ' . $this->callbackBeforeInsert
                . ');'
            );
        }

        $this->registerClientScript();

        $modal = $this->renderFile(
            '@vendor/ktree/filemanager/views/file/modal.php',
            [
                'inputId' => $this->options['id'],
                'btnId' => $this->buttonOptions['id'],
                'frameId' => $this->options['id'] . '-frame',
                'frameSrc' => Url::to($this->frameSrc),
                'thumb' => $this->thumb,
                'imageContainer' => $this->imageContainer,
                'pasteData' => $this->pasteData,
                'displayImageClass' => isset($this->options['display-image-class']) ?
                $this->options['display-image-class'] : null,
                'hiddenImage' => isset($this->options['hidden-image']) ?
                $this->options['hidden-image'] : null,
                'imageValidation' => isset($this->options['image-validation']) ?
                $this->options['image-validation'] : null,
                'multiple' => $this->multiple,
            ]
        );

        return strtr($this->template, $replace) . $modal;
    }

    /**
     * Registers tinyMCE js plugin
     */
    protected function registerClientScript()
    {
        $js = [];
        $view = $this->getView();

        TinyMceAsset::register($view);

        $id = $this->options['id'];

        $this->clientOptions['selector'] = "#$id";

        $langAssetBundle = TinyMceLangAsset::register($view);

        // @codeCoverageIgnoreStart
        if ($this->language !== null) {
            $langFile = "langs/{$this->language}.js";
            $langAssetBundle->js[] = $langFile;
            $this->clientOptions['language_url'] = $langAssetBundle->baseUrl . "/{$langFile}";
        }
        // @codeCoverageIgnoreEnd

        $langAssetBundle->js[] = YII_DEBUG ? 'plugins/addmedia/plugin.js' : 'plugins/addmedia/plugin.min.js';

        $this->clientOptions['setup'] = (isset($this->clientOptions['setup']) && $this->clientOptions['setup'])
            ? $this->clientOptions['setup']
            : new \yii\web\JsExpression(
                "function(editor)
                 {
                     editor.on('change',function(e){
                       $('#{$id}').parents('form').trigger('FORM_UPDATE')

                     })
                 }"
            );

        $options = Json::encode($this->clientOptions);
        $js[] = "tinymce.init($options);";
        if ($this->triggerSaveOnBeforeValidateForm) {
            $js[] = "$('#{$id}').parents('form').on('beforeValidate', function() { tinymce.triggerSave(); });";
        }
        $view->registerJs(implode("\n", $js));
    }
}

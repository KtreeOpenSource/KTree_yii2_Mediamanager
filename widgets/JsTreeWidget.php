<?php
/**
 * @link      http://ktree.com/
 * @copyright Copyright (c) 2017 KTree.com.
 * @license   http://ktree.com/license
 */
namespace ktree\filemanager\widgets;

use ktree\filemanager\assets\JsTreeAsset;
use Yii;
use yii\caching\TagDependency;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\widgets\InputWidget;

/**
 * Class Menu
 * Theme menu widget.
 */
class JsTreeWidget extends InputWidget
{
    /**
     * @var array Data configuration.
     * If left as false the HTML inside the jstree container element is used to populate the tree (that should be an unordered list with list items).
     */
    public $data = [];

    public $checked_values = [];

    /**
     * @var array Stores all defaults for the core
     */
    public $core
        = [
            'expand_selected_onload' => true,
            'themes' => [
                'icons' => false
            ]
        ];

    /**
     * @var array Stores all defaults for the checkbox plugin
     */
    public $checkbox
        = [
            'three_state' => true,
            'keep_selected_style' => false
        ];

    /**
     * @var array Stores all defaults for the contextmenu plugin
     */
    public $contextmenu = [];

    /**
     * @var array Stores all defaults for the drag'n'drop plugin
     */
    public $dnd = [];

    /**
     * @var array Stores all defaults for the search plugin
     */
    public $search = [];

    /**
     * @var string the settings function used to sort the nodes.
     * It is executed in the tree's context, accepts two nodes as arguments and should return `1` or `-1`.
     */
    public $sort = [];

    /**
     * @var array Stores all defaults for the state plugin
     */
    public $state = [];

    /**
     * @var array Stores all defaults for the callback function
     */
    public $callback
        = [
            'check_callback' => false,
        ];

    /**
     * @var array Configure which plugins will be active on an instance. Should be an array of strings, where each element is a plugin name.
     */
    public $plugins = [];

    /**
     * @var array Stores all defaults for the types plugin
     */
    public $types
        = [
            '#' => [],
            'default' => [],
        ];

    public $parentId;

    public $language;

    const DEFAULT_PARENT = 0;
    const ACTIVE = "Active";
    const DELETED = 'Deleted';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        $this->registerAssets();

        if (!$this->hasModel()) {
            echo Html::hiddenInput($this->options['id'], null, ['id' => $this->options['id']]);
        } else {
            echo Html::activeTextInput($this->model, $this->attribute, ['class' => 'hidden', 'value' => $this->value]);
            Html::addCssClass($this->options, "js_tree_{$this->attribute}");
        }
        $this->options['id'] = 'jsTree_' . $this->options['id'];
        echo Html::tag('div', '', $this->options);
    }

    /**
     * Registers the needed assets
     */
    public function registerAssets()
    {
        $view = $this->getView();
        JsTreeAsset::register($view);

        $config = [
            'core' => array_merge(['data' => $this->data], $this->core),
            'checkbox' => $this->checkbox,
            'contextmenu' => $this->contextmenu,
            'dnd' => $this->dnd,
            'search' => $this->search,
            'sort' => $this->sort,
            'state' => $this->state,
            'plugins' => isset($this->plugins)?$this->plugins:'',
            'types' => $this->types,
            'callback' => $this->callback,
        ];

        $defaults = Json::encode($config);
        $inputId = (!$this->hasModel()) ? $this->options['id'] : Html::getInputId($this->model, $this->attribute);
        $this->checked_values = Json::encode(array_values($this->checked_values));

        if ($this->name == '') {
            echo Html::hiddenInput($this->attribute, $this->checked_values, ['class' => 'jstree_checked_list']);
        } else {
            echo Html::hiddenInput($this->name, $this->checked_values, ['class' => 'jstree_checked_list']);
        }

        $js
            = <<<SCRIPT
;(function($, window, document, undefined) {

    $('#jsTree_{$this->options['id']}').jstree({$defaults}).on("hover_node.jstree", function(node, selected, event) {
          $("#" + selected.node.id).prop('title', selected.node.customTitle);
    });
    $(document).on("click",".expand-tags",function(){
      $(this).next( ".extended-tag-container" ).toggle();
    })

})(window.jQuery, window, document);
SCRIPT;

        $view->registerJs($js);
    }
}

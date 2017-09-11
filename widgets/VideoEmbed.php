<?php

/**
 * @link      http://ktree.com/
 * @copyright Copyright (c) 2017 KTree.com.
 * @license   http://ktree.com/license
 */

namespace ktree\filemanager\widgets;

use yii\helpers\Html;
use Embed\Embed;

class VideoEmbed extends \yii\base\Widget
{
    public $url        = null;
    public $show_errors    = false;
    public $responsive    = true;
    public $containerId    = '';
    public $containerClass = '';
    public $config = [
        'height'=>120,
        'width'=>120
    ];
    public function run()
    {
        // make sure a source url was provided
    if (is_null($this->url) || empty($this->url)) {
        return $this->show_errors ? 'Please pass a URL parameter to scan for a video to embed.' : false;
    }

        // include embed class
    //include_once(__DIR__ . '/../../../vendor/embed/embed/src/autoloader.php');

        // look up data for the supplied url
        $data = Embed::create($this->url, $this->config);

        // make sure we received a video embed code from the lookup
        if (!is_object($data) || is_null($data->code)) {
            return $this->show_errors ? "Embed code could not be generated for this URL ({$this->url})" : false;
        }

    // build the video container with custom id and class if desired
    $customContainer = !empty($this->containerId) || !empty($this->containerClass);
        $videoEmbed = $customContainer ? '<div id="' . $this->containerId . '" class="' . $this->containerClass . '">' : '';

    // also set responsiveness class (video-container) if desired
    $videoEmbed .= $this->responsive ? '<div class="video-container">' : '';

    // insert the embed code
    $videoEmbed .= $data->code;

    // close the containers
    $videoEmbed .= $this->responsive ? '</div>' : '';
        $videoEmbed .= $customContainer ? '</div>' : '';

    // return the video embed code
        return $videoEmbed;
    }
}

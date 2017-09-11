<?php
/**
 * @link      http://ktree.com/
 * @copyright Copyright (c) 2017 KTree.com.
 * @license   http://ktree.com/license
 */
namespace ktree\filemanager\behaviours;

use Yii;
use yii\base\Behavior;
use yii\helpers\Json;
use yii\db\ActiveRecord;
use yii\helpers\Url;

class FilemanagerBehaviour extends Behavior
{
    /**
     * @var array
     */
    public $attributes = [];

    public $model = null;

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
            ActiveRecord::EVENT_AFTER_FIND => 'afterFind',
        ];
    }


    public function beforeSave($event)
    {
        $attributes = $this->attributes;
        $postData = Yii::$app->request->post();
        foreach ($attributes as $value) {
            if (isset($postData['media-form-'.$value])) {
                $attachments =  $postData['media-form-'.$value];
                if (is_array($attachments) && $attachments['media_attachments'] && is_array($attachments['media_attachments'])) {
                    array_unique($attachments['media_attachments']);
                }
                $event->sender->$value = (empty($attachments['media_attachments'])) ? 0 : json_encode($attachments['media_attachments']);
            }
        }
    }

    public function afterFind($event)
    {
        $attributes = $this->attributes;
        foreach ($attributes as $value) {
            $event->sender->$value = json_decode($event->sender->$value, true);
        }
    }

    /**
     * @param string $alias thumb alias
     *
     * @return string thumb url
     */
    public function getThumbUrl($alias)
    {
        $model = $this->model;
        $mediaData = $model->media;
        $routes = Yii::$app->modules['filemanager']['routes'];
        $thumbUrl = $mediaData->getThumbUrl($alias);

        return Url::to([Yii::getAlias('@web').'/'.$routes['baseUrl'].'/'.$thumbUrl]);
    }

    /**
     * Thumbnail image html tag
     *
     * @param string $alias thumbnail alias
     * @param array $options html options
     *
     * @return string Html image tag
     */
    public function getThumbImage($alias, $options = [])
    {
        $model = $this->model;
        $mediaData = $model->media;
        return $mediaData->getThumbImage($alias, $options = []);
    }
}

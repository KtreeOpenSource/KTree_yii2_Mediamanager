<?php
/**
 * @link      http://ktree.com/
 * @copyright Copyright (c) 2017 KTree.com.
 * @license   http://ktree.com/license
 */
namespace ktree\filemanager\models;

use Yii;
use ktree\filemanager\Module;
use Imagine\Image\ImageInterface;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\data\ActiveDataProvider;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yii\helpers\Inflector;
use yii\imagine\Image;
use yii\web\UploadedFile;
use yii\db\Expression;

/**
 * This is the model class for table "filemanager_mediafile".
 *
 * @property integer  $id
 * @property string   $filename
 * @property string   $type
 * @property string   $url
 * @property string   $alt
 * @property integer  $size
 * @property string   $description
 * @property string   $thumbs
 * @property integer  $created_at
 * @property integer  $updated_at
 *
 */
class Mediafile extends ActiveRecord
{
    public $file;
    const TYPE = "folder";
    const EMBED_VIDEO_TYPE = "embed";
    const DEFAULT_IMAGE_PATH = 'modules/eCommerce/assets/images/defaultImage.jpg';

    public static $imageFileTypes = ['image/gif', 'image/jpeg', 'image/png'];

    public $fileName;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%filemanager_mediafile}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['filename', 'type', 'url', 'size'], 'required', 'on' => 'uploadView'],
            ['filename', 'required', 'on' => 'createFolder', 'message' => 'Foldername cannot be blank'],
            [['url', 'alt', 'description', 'thumbs'], 'string'],
            [['size', 'parent', 'created_by', 'modified_by'], 'integer'],
            [['filename', 'type'], 'string', 'max' => 255],
            [['file'], 'file'],
            [['created_at', 'updated_at'], 'safe'],
            ['url', 'url', 'on' => 'saveVideo'],
            [['parent'],'default','value'=>0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Module::t('main', 'ID'),
            'filename' => Module::t('main', 'Filename'),
            'type' => Module::t('main', 'Type'),
            'url' => Module::t('main', 'Url'),
            'alt' => Module::t('main', 'Alternate Text'),
            'size' => Module::t('main', 'Size'),
            'description' => Module::t('main', 'Description'),
            'thumbs' => Module::t('main', 'Thumbnails'),
            'created_at' => Module::t('main', 'Created On'),
            'updated_at' => Module::t('main', 'Modified On'),
            'parent' => Module::t('main', 'Parent'),
            'created_by' => Module::t('main', 'Created By'),
            'modified_by' => Module::t('main', 'Modified By'),
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                  'class' => TimestampBehavior::className(),
                  'attributes' => [
                      ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                      ActiveRecord::EVENT_BEFORE_UPDATE => 'updated_at',
                  ],
                  'value' => new Expression('NOW()'),
            ],
            'CreatedUser' => [
                'class' => AttributeBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_by', 'modified_by'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['modified_by'],
                ],
                'value' => function () {
                    return Yii::$app->user->identity->id;
                },
            ],
        ];
    }


    /**
     * Save just uploaded file
     *
     * @param array $routes routes from module settings
     *
     * @return bool
     */
    public function saveUploadedFile(array $routes, $rename = false, $file = null, $structure = null, $fileName = null)
    {
        if ($structure == null) {
            $year = date('Y', time());
            $month = date('m', time());
            $structure = "$year/$month";
        }

        $basePath = Yii::getAlias($routes['basePath']);

        $absolutePath = "$basePath/$structure";

        // create actual directory structure "yyyy/mm"
        if (!file_exists($absolutePath)) {
            mkdir($absolutePath, 0777, true);
        }

        // get file instance
        $this->file = ($file != null) ? $file : UploadedFile::getInstance($this, 'file');

        if (empty($fileName)) {
            //if a file with the same name already exist append a number
            $counter = 0;
            do {
                if ($counter == 0) {
                    $fileName = Inflector::slug($this->file->baseName) . '.' . $this->file->extension;
                } else {
                    //if we don't want to rename we finish the call here
                    if ($rename == false) {
                        return false;
                    }
                    $fileName = Inflector::slug($this->file->baseName) . $counter . '.' . $this->file->extension;
                }
                $url = "$structure/$fileName";
                $counter++;
            } while (self::findByUrl($url)); // checks for existing url in db
        }

        // save original uploaded file
        $this->file->saveAs("$absolutePath/$fileName");
        $this->filename = $fileName;
        $this->type = $this->file->type;
        $this->size = $this->file->size;
        $this->url = $url;

        return $this->save();
    }

    /**
     * Create thumbs for this image
     *
     * @param array $routes see routes in module config
     * @param array $presets thumbs presets. See in module config
     *
     * @return bool
     */
    public function createThumbs(array $routes, array $presets)
    {
        $thumbs = [];
        $basePath = Yii::getAlias($routes['basePath']);
        $originalFile = pathinfo($this->url);
        $dirname = $originalFile['dirname'];
        $filename = $originalFile['filename'];
        $extension = $originalFile['extension'];

        Image::$driver = [Image::DRIVER_GD2, Image::DRIVER_GMAGICK, Image::DRIVER_IMAGICK];

        foreach ($presets as $alias => $preset) {
            $width = $preset['size'][0];
            $height = $preset['size'][1];
            $mode = (isset($preset['mode']) ? $preset['mode'] : ImageInterface::THUMBNAIL_OUTBOUND);

            $thumbUrl = "$dirname/$filename-{$width}x{$height}.$extension";

            Image::thumbnail("$basePath/{$this->url}", $width, $height, $mode)->save("$basePath/$thumbUrl");

            $thumbs[$alias] = $thumbUrl;
        }

        $this->thumbs = serialize($thumbs);
        $this->detachBehavior('timestamp');

        // create default thumbnail
        $this->createDefaultThumb($routes);

        return $this->save();
    }

    /**
     * Create default thumbnail
     *
     * @param array $routes see routes in module config
     */
    public function createDefaultThumb(array $routes)
    {
        $originalFile = pathinfo($this->url);
        $dirname = $originalFile['dirname'];
        $filename = $originalFile['filename'];
        $extension = $originalFile['extension'];

        Image::$driver = [Image::DRIVER_GD2, Image::DRIVER_GMAGICK, Image::DRIVER_IMAGICK];

        $size = Module::getDefaultThumbSize();
        $width = $size[0];
        $height = $size[1];
        $thumbUrl = "$dirname/$filename-{$width}x{$height}.$extension";
        $basePath = Yii::getAlias($routes['basePath']);
        Image::thumbnail("$basePath/{$this->url}", $width, $height)->save("$basePath/$thumbUrl");
    }

    /**
     * @return bool if type of this media file is image, return true;
     */
    public function isImage()
    {
        return in_array($this->type, self::$imageFileTypes);
    }

    /**
     * @param $baseUrl
     *
     * @return string default thumbnail for image
     */
    public function getDefaultThumbUrl($baseUrl = '', $bundleUrl = '')
    {
        if ($this->isImage()) {
            $size = Module::getDefaultThumbSize();
            $originalFile = pathinfo($this->url);
            $dirname = $baseUrl . '/' . $originalFile['dirname'];
            $filename = $originalFile['filename'];
            $extension = $originalFile['extension'];
            $width = $size[0];
            $height = $size[1];
            return "/$dirname/$filename-{$width}x{$height}.$extension";
        }
        return "$bundleUrl/images/file.png";
    }

    /**
     * @param $baseUrl
     *
     * @return string default thumbnail for image
     */
    public function getDefaultUploadThumbUrl($baseUrl = '')
    {
        $size = Module::getDefaultThumbSize();
        $originalFile = pathinfo($this->url);
        $dirname = $baseUrl . '/' . $originalFile['dirname'];
        $filename = $originalFile['filename'];
        $extension = $originalFile['extension'];
        $width = $size[0];
        $height = $size[1];
        return "/$dirname/$filename-{$width}x{$height}.$extension";
    }

    /**
     * @return array thumbnails
     */
    public function getThumbs()
    {
        return unserialize($this->thumbs);
    }

    /**
     * @param string $alias thumb alias
     *
     * @return string thumb url
     */
    public function getThumbUrl($alias)
    {
        $thumbs = $this->getThumbs();

        if ($alias === 'original') {
            return $this->url;
        }

        return !empty($thumbs[$alias]) ? $thumbs[$alias] : '';
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
        $url = $this->getThumbUrl($alias);

        if (empty($url)) {
            return '';
        }

        if (empty($options['alt'])) {
            $options['alt'] = $this->alt;
        }
        $routes = Yii::$app->modules['filemanager']['routes'];

        return Html::img(Yii::getAlias('@web').'/'.$routes['baseUrl'].'/'.$url, $options);
    }

    /**
     * @param Module $module
     *
     * @return array images list
     */
    public function getImagesList(Module $module)
    {
        $thumbs = $this->getThumbs();
        $list = [];
        $originalImageSize = $this->getOriginalImageSize($module->routes);
        $list[$this->url] = Module::t('main', 'Original') . ' ' . $originalImageSize;

        foreach ($thumbs as $alias => $url) {
            $preset = $module->thumbs[$alias];
            $list[$url] = $preset['name'] . ' ' . $preset['size'][0] . ' × ' . $preset['size'][1];
        }
        return $list;
    }

    /**
     * Delete thumbnails for current image
     *
     * @param array $routes see routes in module config
     */
    public function deleteThumbs(array $routes)
    {
        $basePath = Yii::getAlias($routes['basePath']);
        foreach ($this->getThumbs() as $thumbUrl) {
            unlink("$basePath/$thumbUrl");
        }
        unlink(Yii::$app->basePath . "{$this->getDefaultThumbUrl($routes['baseUrl'])}");
    }


    /**
     * Delete file
     *
     * @param array $routes see routes in module config
     *
     * @return bool
     */
    public function deleteFile(array $routes)
    {
        $basePath = Yii::getAlias($routes['basePath']);
        return unlink("$basePath/{$this->url}");
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @return ActiveDataProvider
     */
     public function search($search)
     {
         $query = self::find();
         $dataProvider = new ActiveDataProvider([
           'query' => $query,
           'sort' => ['defaultOrder' => ['created_at' => SORT_DESC]]
         ]);

         $parent = (isset($search['Mediafile']) && $search['Mediafile']['fileName'] != '') ? 0 : $this->parent;

         if ((isset($search['Mediafile']) && $search['Mediafile']['fileName'] == '') || empty($search['Mediafile'])) {
             $query->andFilterWhere(['=', 'parent', $parent]);
         }

         $this->load($search);


         $filename = (isset($search['Mediafile']) && $search['Mediafile']['fileName'] != '') ? $search['Mediafile']['fileName'] : $this->filename;
       /*if (!($this->load($search))) {
           return $dataProvider;
       }*/
       $query->andFilterWhere(
           [
               'id' => $this->id,
               'size' => $this->size,
               'modified_by' => $this->modified_by,
           ]
       );

         $updatedAt = isset($search['Mediafile']['updated_at']) ? $search['Mediafile']['updated_at'] : null;
         $updatedAt = date("Y-m-d", strtotime($updatedAt));

         if (isset($search['Mediafile']['updated_at']) && $search['Mediafile']['updated_at']) {
             $query->andFilterWhere(['like', "DATE(updated_at)", $updatedAt]);
         }

         $query->andFilterWhere(['like', 'filename', $filename])
           ->andFilterWhere(['like', 'type', $this->type])
           ->andFilterWhere(['like', 'url', $this->url])
           ->andFilterWhere(['like', 'alt', $this->alt])
           ->andFilterWhere(['like', 'description', $this->description])
           ->andFilterWhere(['like', 'thumbs', $this->thumbs]);

         return $dataProvider;
     }

    /**
     * @return int last changes timestamp
     */
    public function getLastChanges()
    {
        return !empty($this->updated_at) ? $this->updated_at : $this->created_at;
    }

    /**
     * This method wrap getimagesize() function
     *
     * @param array $routes see routes in module config
     * @param string $delimiter delimiter between width and height
     *
     * @return string image size like '1366x768'
     */
    public function getOriginalImageSize(array $routes, $delimiter = ' × ')
    {
        $imageSizes = $this->getOriginalImageSizes($routes);
        return "$imageSizes[0]$delimiter$imageSizes[1]";
    }

    /**
     * This method wrap getimagesize() function
     *
     * @param array $routes see routes in module config
     *
     * @return array
     */
    public function getOriginalImageSizes(array $routes)
    {
        $basePath = Yii::getAlias($routes['basePath']);
        return getimagesize("$basePath/{$this->url}");
    }

    /**
     * @return string file size
     */
    public function getFileSize()
    {
        Yii::$app->formatter->sizeFormatBase = 1000;
        return Yii::$app->formatter->asShortSize($this->size, 0);
    }

    /**
     * Find model by url
     *
     * @param $url
     *
     * @return static
     */
    public static function findByUrl($url)
    {
        return self::findOne(['url' => $url]);
    }

    /**
     * Find model by id
     *
     * @param $id
     *
     * @return static
     */
    public static function getFileById($id)
    {
        return self::findOne(['id' => $id]);
    }

    /**
     * Search models by file types
     *
     * @param array $types file types
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function findByTypes(array $types)
    {
        return self::find()->filterWhere(['in', 'type', $types])->all();
    }


    /**
     * Relation to get created User
     */
    public function getCreatedUser()
    {
        return Yii::$app->user->identity->username;
    }



    /*
      *To display the folders in the form of tree
    */
    public function getFilemanagerFolderData()
    {
        $result = Mediafile::find()->where(['type' => Mediafile::TYPE])->all();
        $folderList = self::buildTree($result);
        return $folderList;
    }

    public function buildTree(array &$elements, $parentId = 0)
    {
        $result = array();

        foreach ($elements as &$element) {
            $id = $element['id'];
            if ($element['parent'] == $parentId) {
                $children = self::buildTree($elements, $id);
                $data['id'] = $id;
                $data['text'] = $element['filename'];
                if ($children) {
                    $data['children'] = $children;
                }
                $result[] = $data;
                unset($data);
            }
        }
        return $result;
    }

    public static function getGridData($id)
    {
        $model = new Mediafile();
        if (!empty($id)) {
            $model->id = $id;
            $dataProvider = $model->search(Yii::$app->request->queryParams);
            $dataProvider->pagination->pageSize = Yii::$app->params['backEndPageSize'];
            return ['model' => $model, 'dataProvider' => $dataProvider];
        }
        $query = Mediafile::find()->where(['id' => $id]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => false
        ]);
        return ['model' => $model, 'dataProvider' => $dataProvider];
    }

    /**
     * If the folder exists on the file system, will return a new Folder instance, otherwise, false.
     *
     * @param string $name name of the folder.
     * @param int $parent
     *
     * @return array|bool|null|ActiveRecord
     */
    public static function getFolder($name, $parent = 0)
    {
        $folder = Mediafile::find()->where(
            [
                'type' => Mediafile::TYPE,
                'filename' => $name,
                'parent' => $parent
            ]
        )->one();

        if ($folder != null) {
            return $folder;
        }

        $folder = new Mediafile([
            'type' => Mediafile::TYPE,
            'filename' => $name,
            'parent' => $parent
        ]);

        $folder->save();
        return $folder;
    }
}

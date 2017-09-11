Yii2 file manager
================
This module provide interface to collect and access all mediafiles in one place. Inspired by WordPress file manager.

Features
------------
* Integrated with TinyMCE editor.
* Automatically create actually directory for uploaded files like "2014/12".
* Automatically create thumbs for uploaded images
* Unlimited number of sets of miniatures
* All media files are stored in a database that allows you to attach to your object does not link to the image, and the id of the media file. This provides greater flexibility since in the future will be easy to change the size of thumbnails.
* If your change thumbs sizes, your may resize all existing thumbs in settings.

Screenshots
------------
<img src="http://zabolotskikh.com/wp-content/uploads/2014/12/yii2-filemanager-upload.png">

<img src="http://zabolotskikh.com/wp-content/uploads/2014/12/yii2-filemanager-image-select.png">

<img src="http://zabolotskikh.com/wp-content/uploads/2014/12/yii2-filemanager-index.png">

<img src="http://zabolotskikh.com/wp-content/uploads/2014/12/yii2-filemanager-files-in-admin.png">

<img src="http://zabolotskikh.com/wp-content/uploads/2014/12/yii2-filemanager-settings.png">

<img src="http://zabolotskikh.com/wp-content/uploads/2014/12/yii2-filemanager-selected-image.png">

<img src="http://zabolotskikh.com/wp-content/uploads/2014/12/yii2-filemanager-selected-image-without-input.png">

Installation
------------
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist ktree/filemanager "*"
```

or add

```
"ktree/filemanager": "*"
```

to the require section of your `composer.json` file.

run the command composer.install or composer.update

Apply migration
```sh
yii migrate --migrationPath=vendor/ktree/filemanager/migrations
```

Configuration:

```php
'modules' => [
    'filemanager' => [
        'class' => 'ktree\filemanager\Module',
        // Upload routes
        'routes' => [
            // Base absolute path to web directory
            'baseUrl' => '',
            // Base web directory url
            'basePath' => '@frontend/web',
            // Path for uploaded files in web directory
            'uploadPath' => 'uploads',
        ],
        // Thumbnails info
        'thumbs' => [
            'small' => [
                'name' => 'Мелкий',
                'size' => [100, 100],
            ],
            'medium' => [
                'name' => 'Средний',
                'size' => [300, 200],
            ],
            'large' => [
                'name' => 'Большой',
                'size' => [500, 400],
            ],
        ],
    ],
],
```
By default, thumbnails are resized in "outbound" or "fill" mode. To switch to "inset" or "fit" mode, use `mode` parameter and provide. Possible values: `outbound` (`\Imagine\Image\ImageInterface::THUMBNAIL_OUTBOUND`) or `inset` (`\Imagine\Image\ImageInterface::THUMBNAIL_INSET`):

```php
'thumbs' => [
    'small' => [
        'name' => 'Мелкий',
        'size' => [100, 100],
    ],
    'medium' => [
        'name' => 'Средний',
        'size' => [300, 200],
    ],
    'large' => [
        'name' => 'Большой',
        'size' => [500, 400],
        'mode' => \Imagine\Image\ImageInterface::THUMBNAIL_INSET
    ],
],
```

To get the media manager data or to save the media manager data in the form of json or single element, you need to add this to your model behaviour :
```php
'FilemanagerBehaviour' => [
  'class' => \ktree\filemanager\behaviours\FilemanagerBehaviour::className(),
  'attributes' => ['image'] //media manager attributes of your model
],
```


Usage
------------
Simple standalone field:

```php
use ktree\filemanager\widgets\FileInput;

File input widget calling using form element

echo $form->field($model, 'image')->widget(FileInput::className(), [
  'buttonTag' => 'button',
  'buttonName' => Yii::t('app','Media Manager'),
  'buttonOptions' => ['class' => 'btn btn-default'],
  'options' => [
      'id' => 'file-image',
      'class' => 'form-control hidden-image',
      'hidden-image' => 'hidden-image',
      'display-image-class' => 'display-image'
  ],
  // Widget template
  'template' => '{mediaManagerPreview}<div class="input-group">{input}<span class="input-group-btn">{button}</span><span class="input-group-btn">{reset-button}</span></div>',
  // Optional, if set, only this image can be selected by user
  'thumb' => 'small',
  'mediaData' => $model->post_image,
  // Optional, if set, in container will be inserted selected image
  'imageContainer' => '.img',
  // Default to FileInput:DATA_URL. This data will be inserted in input field
  'pasteData' => FileInput:DATA_ID,
  'resetButtonName' => '<span class="text-danger glyphicon glyphicon-remove"></span>',
  // JavaScript function, which will be called before insert file data to input.
  // Argument data contains file data.
  // data example: [alt: "Ведьма с кошкой", description: "123",
  //url: "/uploads/2014/12/vedma-100x100.jpeg", id: "45"]
  'callbackBeforeInsert' => 'function(e, data) {
        console.log( data );
    }',
  ]);

Fileinput widget calling without form element

echo FileInput::widget([
    'name' => 'mediafile',
    'buttonTag' => 'button',
    'buttonName' => Yii::t('app','Media Manager'),
    'buttonOptions' => ['class' => 'btn btn-default'],
    'options' => [
        'id' => 'file-image',
        'class' => 'form-control hidden-image',
        'hidden-image' => 'hidden-image',
        'display-image-class' => 'display-image'
    ],
    // Widget template
    'template' => '{mediaManagerPreview}<div class="input-group">{input}<span class="input-group-btn">{button}</span><span class="input-group-btn">{reset-button}</span></div>',
    // Optional, if set, only this image can be selected by user
    'thumb' => 'small',
    'mediaData' => $model->post_image,
    // Optional, if set, in container will be inserted selected image
    'imageContainer' => '.img',
    // Default to FileInput:DATA_URL. This data will be inserted in input field
    'pasteData' => FileInput:DATA_ID,
    'resetButtonName' => '<span class="text-danger glyphicon glyphicon-remove"></span>',
    // JavaScript function, which will be called before insert file data to input.
    // Argument data contains file data.
    // data example: [alt: "Ведьма с кошкой", description: "123",
    //url: "/uploads/2014/12/vedma-100x100.jpeg", id: "45"]
    'callbackBeforeInsert' => 'function(e, data) {
          console.log( data );
      }',
]);
```

Options:

'multiple' => true, -- optional
By default mutiple option is false if we set true then we can select multiple images

'template' => '{mediaManagerPreview}<div class="input-group">{input}<span class="input-group-btn">{button}</span><span class="input-group-btn">{reset-button}</span></div>',
{mediaManagerPreview}- used to display the uploaded images
{input}-input field is displayed along with media manager browse button,
{hiddenInput} - in place of {input} we can use {hiddenInput} option to hide the input field
{button} - media manager browse button,
{reset-button} - used to reset the input field

'display-image-class' => 'your class name'
used to display the uploaded image in particular div with class name as 'display-image-class

'displayGridView' => true, -- optional
used to display the uploaded images in media manager grid view, by default it is false.

'mediaData' => $model->image, -- required
used to display the saved images,
$model->image - imaged saved id or url


With TinyMCE:
```php
use ktree\filemanager\widgets\tinymce\TinyMce;

<?=
$form->field($model, 'content')->widget(
    TinyMce::className(),
    [
        'options' => ['rows' => 6],
        'clientOptions' => [
            'plugins' => [
                "advlist autolink lists link charmap print preview anchor",
                "searchreplace visualblocks code fullscreen",
                "insertdatetime media table contextmenu paste addmedia"
            ],
            'toolbar' => "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | imagebutton"
        ],
        'thumb' => 'small',
    ]
);?>
```
create image field in your model, if you want to save more than one image then save that field with Text data type or else save with integer data type with default value as 0.

Than, you may get mediafile from your owner model.
See example:

```php
use ktree\filemanager\models\Mediafile;

$model = Post::findOne(1);
```

Add relation to the model to get the media information

multiple option false then add hasone relation
```php
public function getMedia()
{
    return $this->hasOne(Mediafile::className(), ['id' => 'image']);
}
```

multiple option true then add hasMany relation
```php
public function getMedia()
{
    return $this->hasMany(Mediafile::className(), ['id' => 'image']);
}
```

// Ok, we have mediafile object! Let's do something with him:
// return url for small thumbnail, for example: '/uploads/2014/12/flying-cats.jpg'
echo $model->getThumbUrl($model,'small');
// return image tag for thumbnail, for example: '<img src="/uploads/2014/12/flying-cats.jpg" alt="Летающие коты">'
echo $model->getThumbImage($model,'small'); // return url for small thumbnail
```

<?php
/**
 * @link      http://ktree.com/
 * @copyright Copyright (c) 2017 KTree.com.
 * @license   http://ktree.com/license
 */
namespace ktree\filemanager\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\Response;
use yii\web\UploadedFile;
use ktree\filemanager\assets\FilemanagerAsset;
use ktree\filemanager\models\Mediafile;
use ktree\filemanager\Module;
use yii\filters\AccessControl;

class FileController extends Controller
{
    public function beforeAction($action)
    {
        return parent::beforeAction($action);
    }

    public function behaviors()
    {
        return [
          'GridFilterQueryBehaviour' => [
              'class' => \ktree\grid\behaviours\GridFilterQueryBehaviour::className()
          ],
        ];
    }

    public function actions()
    {
        return [
          'save-grid-changes' => [
              'class' => 'ktree\grid\actions\SaveGridChanges',
          ],
          'save-grid-edit' => [
              'class' => 'ktree\grid\actions\SaveGridEdit',
          ],
          'save-grid-preference' => [
              'class' => 'ktree\grid\actions\SaveGridPreference',
          ],
          'delete-filters' => [
              'class' => 'ktree\grid\actions\DeleteFilters',
          ],
          'save-filters' => [
              'class' => 'ktree\grid\actions\SaveFilters',
          ],
          'validate-save-filters' => [
              'class' => 'ktree\grid\actions\ValidateSaveFilters',
          ]
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionUploadmanager()
    {
        $this->layout = '@vendor/ktree/filemanager/views/layouts/main';
        $parent = Yii::$app->request->get('parent');
        return $this->render('uploadmanager', ['model' => new Mediafile(), 'parent' => $parent]);
    }

    /**
     * Provides upload file
     *
     * @return mixed
     */
    public function actionUpload()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $bundle = FilemanagerAsset::register($this->view);

        $model = new Mediafile();
        $parentId = Yii::$app->request->get('parent');
        $type = Yii::$app->request->get('type');
        $postData = Yii::$app->request->post();

        if (Yii::$app->request->isAjax && $model->load($postData)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->scenario = 'uploadView';
            return ActiveForm::validate($model);
        }

        $model->parent = $parentId;
        $routes = $this->module->routes;
        $rename = $this->module->rename;
        $rename = true;

        if ($type == 'dropzone') {
            $model->saveUploadedFile($routes, $rename, UploadedFile::getInstanceByName('file'));
        } else {
            $model->saveUploadedFile($routes, $rename);
        }

        if ($model->isImage()) {
            $model->createThumbs($routes, $this->module->thumbs);
        }

        $response['files'][] = [
            'url' => $model->url,
            'thumbnailUrl' => Yii::getAlias('@web') . $model->getDefaultThumbUrl($routes['baseUrl'], $bundle->baseUrl),
            'name' => $model->filename,
            'type' => $model->type,
            'size' => $model->file->size,
            'deleteUrl' => Url::to(['file/delete', 'id' => $model->id]),
            'deleteType' => 'POST',
        ];

        return $response;
    }

    /**
     * Updated mediafile by id
     *
     * @param $id
     *
     * @return array
     */
    public function actionUpdate($id)
    {
        $model = Mediafile::findOne($id);
        $popup = Yii::$app->request->get('popup');
        $routes = $this->module->routes;
        if ($model->load(Yii::$app->request->post())) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            if ($model->save()) {
                $message = Module::t('main', 'Changes saved!');
                return ['success' => 'true', 'filename' => $model->filename];
            }
            return ['error' => 'true', 'filename' => $model->filename];
        }

        Yii::$app->session->setFlash('mediafileUpdateResult', $message);
        return $this->renderPartial(
            'info',
            [
                'model' => $model,
                'strictThumb' => null,
                'routes' => $routes,
                'popup' => $popup
            ]
        );
    }

    /**
     * Delete model with files
     *
     * @param $id
     *
     * @return array
     */
    public function actionDelete($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $routes = $this->module->routes;

        $model = Mediafile::findOne($id);
        if ($model->type == 'folder') {
            $folders = Mediafile::find()->where(['parent' => $model->id])->all();
            if (!empty($folders)) {
                foreach ($folders as $value) {
                    if ($value->isImage()) {
                        $value->deleteThumbs($routes);
                    }
                    if ($value->type != 'folder') {
                        $value->deleteFile($routes);
                    }
                    $value->delete();
                }
            }
        }

        if ($model->isImage()) {
            $model->deleteThumbs($routes);
        }
        $filename = $model->filename;
        if ($model->type != 'folder') {
            $model->deleteFile($routes);
        }
        $model->delete();
        $newModel = new Mediafile();
        Yii::$app->session->setFlash('mediafolderResult', 'Deleted Successfully');
        return ['success' => 'true', 'jstreeData' => $newModel->getFilemanagerFolderData(), 'filename' => $filename];
    }

    /**
     * Resize all thumbnails
     */
    public function actionResize()
    {
        $models = Mediafile::findByTypes(Mediafile::$imageFileTypes);
        $routes = $this->module->routes;

        foreach ($models as $model) {
            if ($model->isImage()) {
                $model->deleteThumbs($routes);
                $model->createThumbs($routes, $this->module->thumbs);
            }
        }

        Yii::$app->session->setFlash('successResize');
        $this->redirect(Url::to(['default/settings']));
    }

    /** Render model info
     *
     * @param int $id
     * @param string $strictThumb only this thumb will be selected
     *
     * @return string
     */
    public function actionInfo($id, $strictThumb = null)
    {
        $model = Mediafile::findOne($id);
        $routes = $this->module->routes;
        $popup = Yii::$app->request->get('popup');
        return $this->renderAjax(
            'info',
            [
                'model' => $model,
                'strictThumb' => $strictThumb,
                'routes' => $routes,
                'popup' => $popup
            ]
        );
    }

    /*
     *To create the folder
    */
    public function actionCreateFolder()
    {
        $model = new Mediafile();
        $routes = $this->module->routes;
        $parent = Yii::$app->request->get('parent');
        $parentId = Yii::$app->request->get('parentId');
        $this->layout = '@vendor/ktree/filemanager/views/layouts/main';
        $postData = Yii::$app->request->post();
        if (Yii::$app->request->isAjax && $model->load($postData)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->scenario = 'createFolder';
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            if (!empty($postData['Mediafile'])) {
                $model->filename = $postData['Mediafile']['filename'];
                $model->type = Mediafile::TYPE;
                $model->parent = $parent;
            }
            if ($model->save()) {
                $message = $model->filename . Module::t('main', ' folder created successfully');
                Yii::$app->session->setFlash('mediafolderResult', $message);
                return Yii::$app->getResponse()->redirect(
                        Url::to(
                            ['file/filemanager-view', 'parent' => $parent, 'parentId' => $parentId]
                        )
                    );
            }
            $message = $model->filename . Module::t('main', ' folder failed to create');
            Yii::$app->session->setFlash('mediafolderResult', $message);
            return Yii::$app->getResponse()->redirect(
                        Url::to(
                            ['file/filemanager-view', 'parent' => $parent, 'parentId' => $parentId]
                        )
                    );
        }
        return $this->renderAjax(
                'create-folder',
                [
                    'model' => $model,
                    'parent' => $parent,
                    'parentId' => $parentId,
                    'routes' => $routes
                ]
            );
    }

    public function actionUpdateFolder()
    {
        $id = Yii::$app->request->get('id');
        $model = Mediafile::findOne($id);
        $routes = $this->module->routes;
        $postData = Yii::$app->request->post();
        if (Yii::$app->request->isAjax && $model->load($postData)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->scenario = 'createFolder';
            $message = ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            $message = Module::t('main', 'Changes not saved.');
            if ($model->save()) {
                $message = Module::t('main', 'Changes saved!');
            }
        }
        Yii::$app->session->setFlash('mediafileUpdateResult', $message);
        return $this->renderPartial(
            'folder',
            [
                'model' => $model,
                'routes' => $routes
            ]
        );
    }

    public function actionFilemanagerView()
    {
        $post = Yii::$app->request->post();
        $parent = Yii::$app->request->get('parent');
        $mediaFile = Mediafile::findOne($parent);
        $bundle = FilemanagerAsset::register($this->view);

        $parentId = (Yii::$app->request->get('parentId')) ? Yii::$app->request->get('parentId') :
        (!empty($mediaFile->parent) ? $mediaFile->parent : null);

        $popup = (Yii::$app->request->get('popup')) ? Yii::$app->request->get('popup') : 0;

        $model = new Mediafile();
        if ($parent != null) {
            $model->parent = $parent;
        }

        $searchItems = (!empty($post)) ? $post : Yii::$app->request->queryParams;
        $dataProvider = $model->search($searchItems);
        $dataProvider->pagination->pageSize = Yii::$app->params['backEndPageSize'];

        $routes = $this->module->routes;
        $this->layout = '@vendor/ktree/filemanager/views/layouts/main';
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax(
                    'gridview',
                    [
                        'dataProvider' => $dataProvider,
                        'parent' => ($parent != null) ? $parent : 0,
                        'parentId' => $parentId,
                        'routes' => $routes,
                        'model' => $model,
                        'popup' => $popup,
                        'bundle' => $bundle
                    ]
                );
        }
        return $this->render(
                'filemanager-view',
                [
                    'model' => $model,
                    'dataProvider' => $dataProvider,
                    'parent' => ($parent != null) ? $parent : 0,
                    'parentId' => $parentId,
                    'routes' => $routes,
                    'id' => $parent,
                    'searchModel' => new Mediafile(),
                    'popup' => $popup,
                    'bundle' => $bundle
                ]
            );
    }

    public function actionGetMediaManagerData($id)
    {
        $model = Mediafile::findOne($id);
        $inputAttribute = Yii::$app->request->get('inputAttribute');
        $routes = $this->module->routes;
        $bundle = FilemanagerAsset::register($this->view);
        $image = \Yii::getAlias('@web') . '/' .$routes['baseUrl'].'/'. $model->getThumbUrl('small');
        return $this->renderPartial(
            'media-manager',
            [
                'mediaModel' => $model,
                'image' => $image,
                'routes'=>$routes,
                'bundle' => $bundle,
                'inputAttribute' => $inputAttribute
            ]
        );
    }

    /*
     *Used to delete multiple Value
    */
    public function actionMassDelete()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $routes = $this->module->routes;
        $post = Yii::$app->request->post();
        $mediaIds = explode(",", $post['id']);

        foreach ($mediaIds as $value) {
            $model = Mediafile::findOne($value);
            if ($model->type == 'folder') {
                $folders = Mediafile::find()->where(['parent' => $model->id])->all();
                if (!empty($folders)) {
                    foreach ($folders as $value) {
                        if ($value->isImage()) {
                            $value->deleteThumbs($routes);
                        }

                        $value->deleteFile($routes);
                        $value->delete();
                    }
                }
            }
            if ((!empty($model)) && $model->isImage()) {
                $model->deleteThumbs($routes);
            }

            $model->deleteFile($routes);
            $model->delete();
        }
        $newModel = new Mediafile();

        return ['success' => 'true', 'jstreeData' => $newModel->getFilemanagerFolderData()];
    }

    /*
      *Used to insert the multiple values to the input
    */
    public function actionMassInsert()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $post = Yii::$app->request->post();
        $mediaIds = explode(",", $post['id']);
        $routes = $this->module->routes;
        $bundle = FilemanagerAsset::register($this->view);
        $result = [];
        foreach ($mediaIds as $value) {
            $model = Mediafile::findOne($value);
            $result[$model->id]['alt'] = $model->alt;
            $result[$model->id]['description'] = $model->description;
            $result[$model->id]['id'] = $model->id;
            $result[$model->id]['type'] = $model->type;
            $result[$model->id]['url'] = $model->url;
            $result[$model->id]['dataThumbUrl'] = $model->getThumbUrl(Yii::$app->request->get('thumb'));
            if ($model->isImage()) {
                $result[$model->id]['thumbUrl'] = Html::img(
                    \Yii::getAlias('@web') . '/' .$routes['baseUrl'].'/'. $model->getThumbUrl(Yii::$app->request->get('thumb'))
                );
                $result[$model->id]['largeThumbUrl'] = \Yii::getAlias('@web') . '/' .$routes['baseUrl'].'/'. $model->getThumbUrl('large');
            } else {
                if ($model->type == Mediafile::EMBED_VIDEO_TYPE) {
                    $result[$model->id]['thumbUrl'] = \ktree\filemanager\widgets\VideoEmbed::widget(
                        ['url' => $model->url]
                    );
                } else {
                    $result[$model->id]['thumbUrl'] = Html::img($bundle->baseUrl.'/images/file.png', ['style'=>'height:100px;width:100px;']);
                }
            }
        }

        return $result;
    }

    /*
      *Used to insert the values to the input
    */
    public function actionInsert($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $bundle = FilemanagerAsset::register($this->view);
        $model = Mediafile::findOne($id);
        $result['alt'] = $model->alt;
        $result['description'] = $model->description;
        $result['id'] = $model->id;
        $result['type'] = $model->type;
        $result['url'] = $model->url;
        $result['dataThumbUrl'] = $model->getThumbUrl(Yii::$app->request->get('thumb'));
        $routes = $this->module->routes;
        if ($model->isImage()) {
            $result['thumbUrl'] = Html::img(
                \Yii::getAlias('@web') . '/' .$routes['baseUrl'].'/'. $model->getThumbUrl(Yii::$app->request->get('thumb'))
            );
            $result['largeThumbUrl'] = \Yii::getAlias('@web') . '/' .$routes['baseUrl'].'/'. $model->getThumbUrl('large');
        } elseif ($model->type == Mediafile::EMBED_VIDEO_TYPE) {
            $result['thumbUrl'] = \ktree\filemanager\widgets\VideoEmbed::widget(['url' => $model->url]);
        } else {
            $result['thumbUrl'] = Html::img($bundle->baseUrl.'/images/file.png', ['style'=>'height:100px;width:100px;']);
        }

        return $result;
    }

    public function actionAutocompleteData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $newModel = new Mediafile();
        $result = $newModel->getFilemanagerFolderData();
        return $result;
    }

    public function actionUpdateFolderData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = Yii::$app->request->get('id');
        $folderName = Yii::$app->request->get('filename');
        $parent = Yii::$app->request->get('parent');
        $model = Mediafile::findOne($id);
        $model->filename = ($folderName != '') ? $folderName : $model->filename;
        $model->parent = ($parent != '') ? $parent : $model->parent;
        $newModel = new Mediafile();
        if ($model->save()) {
            return [
                'success' => 'true',
                'jstreeData' => $newModel->getFilemanagerFolderData(),
                'filename' => $model->filename
            ];
        }
        return [
                'error' => 'true',
                'jstreeData' => $newModel->getFilemanagerFolderData(),
                'filename' => $model->filename
            ];
    }

    /*
     *To save the video
    */
    public function actionSaveEmbedVideo()
    {
        $model = new Mediafile();
        $routes = $this->module->routes;
        $parent = Yii::$app->request->get('parent');
        $parentId = Yii::$app->request->get('parentId');
        $this->layout = '@vendor/ktree/filemanager/views/layouts/main';
        $postData = Yii::$app->request->post();
        if (Yii::$app->request->isAjax && $model->load($postData)) {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $model->scenario = 'saveVideo';
            return ActiveForm::validate($model);
        }
        if ($model->load(Yii::$app->request->post())) {
            if (!empty($postData['Mediafile'])) {
                $model->filename = $postData['Mediafile']['filename'];
                $model->url = $postData['Mediafile']['url'];
                $model->type = Mediafile::EMBED_VIDEO_TYPE;
                $model->parent = $parent;
            }
            if ($model->save()) {
                $message = $model->filename . Module::t('main', ' video created successfully');
                Yii::$app->session->setFlash('mediafolderResult', $message);
                return Yii::$app->getResponse()->redirect(
                        Url::to(
                            ['file/filemanager-view', 'parent' => $parent, 'parentId' => $parentId]
                        )
                    );
            }
            $message = $model->filename . Module::t('main', ' video failed to create');
            Yii::$app->session->setFlash('mediafolderResult', $message);
            return Yii::$app->getResponse()->redirect(
                        Url::to(
                            ['file/filemanager-view', 'parent' => $parent, 'parentId' => $parentId]
                        )
                    );
        }
        return $this->renderAjax(
                'create-embed-video',
                [
                    'model' => $model,
                    'parent' => $parent,
                    'parentId' => $parentId,
                    'routes' => $routes
                ]
            );
    }
}

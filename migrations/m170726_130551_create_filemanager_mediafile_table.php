<?php
/**
 * @link      http://ktree.com/
 * @copyright Copyright (c) 2017 KTree.com.
 * @license   http://ktree.com/license
 */
use yii\db\Migration;
use yii\db\Schema;

class m170726_130551_create_filemanager_mediafile_table extends Migration
{
    public function up()
    {
        $this->createTable(
            'filemanager_mediafile',
            [
                'id' => 'pk',
                'filename' => Schema::TYPE_STRING . ' NOT NULL',
                'type' => Schema::TYPE_STRING . ' NOT NULL',
                'url' => Schema::TYPE_TEXT . ' NOT NULL',
                'alt' => Schema::TYPE_TEXT,
                'size' => Schema::TYPE_STRING . ' NOT NULL',
                'description' => Schema::TYPE_TEXT,
                'thumbs' => Schema::TYPE_TEXT,
                'parent' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
                'created_at' => Schema::TYPE_DATETIME . ' NOT NULL',
                'updated_at' => Schema::TYPE_DATETIME,
                'created_by' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
                'modified_by' => Schema::TYPE_INTEGER . ' NOT NULL DEFAULT 0',
            ]
        );
    }

    public function down()
    {
        $this->dropTable('filemanager_mediafile');
    }
}

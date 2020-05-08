<?php
namespace kr0lik\compositeModel\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use kr0lik\compositeModel\CompositeModelTrait;
use kr0lik\resource\ResourceBehavior;

class File extends Model
{
    public static $AllowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];

    public $file;
    public $name;
    public $original_file_name;

    protected $folder;

    use CompositeModelTrait;

    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name', 'original_file_name'], 'string', 'max' => 125],
            [['name', 'original_file_name'], 'trim'],
            [['name'], 'filter', 'filter' => function ($value) { return str_replace('  ', ' ', $value); }],
            [['file'], 'file', 'extensions' => static::$AllowedExtensions, 'checkExtensionByMimeType' => false, 'skipOnEmpty' => true],
            [['file'], 'required', 'whenClient' => "function (attribute, value) {
                return " . $this->isNewRecord . ";
            }"]
        ];
    }

    public function attributeLabels()
    {
        return [
            'file' => 'Файл',
            'name' => 'Название',
            'original_file_name' => 'Оригинальное название файла',
        ];
    }

    public function behaviors()
    {
        return [
            'resource' => [
                'class' => ResourceBehavior::class,
                'attributes' => ['file'],
                'folder' => 'upload/file/' . $this->folder,
                'tmpFolder' => Yii::$app->params['UploadTempFolder']
            ]
        ];
    }

    public function getFile(): string
    {
        $resourceName = $this->file;

        if (! $resourceName) return '';

        $absoluteResourcePath = $this->getResource('file', true);

        if (! file_exists($absoluteResourcePath)) {
            return '';
        }

        return $this->getResource('file');
    }

    public function getName($defaultName = null): string
    {
        return $this->name ?: ($defaultName ?: strtoupper(pathinfo($this->file, PATHINFO_EXTENSION)));
    }

    public function getType()
    {
        $type = strtolower(pathinfo($this->file, PATHINFO_EXTENSION));

        switch ($type) {
            case 'doc':
            case 'docx':
                $type = 'word';
                break;
            case 'xls':
            case 'xlsx':
                $type = 'excel';
                break;
        }

        return $type;
    }

    public function getSize()
    {
        $resourceName = $this->file;

        if (! $resourceName) return 0;

        $path = $this->getResource('file', true);

        if (! file_exists($path)) {
            return 0;
        }

        return filesize($path);
    }
}

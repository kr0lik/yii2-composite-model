<?php
namespace kr0lik\compositeModel\models;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use kr0lik\compositeModel\CompositeModelTrait;
use kr0lik\resource\ResourceBehavior;

class Image extends Model
{
    public static $AllowedExtensions = ['jpg', 'jpeg', 'png'];

    const IMAGE_MIN_WIDTH = 500;
    const IMAGE_MIN_HEIGHT = 500;
    const IMAGE_CHECK_OPERATOR = 'OR';

    public $image;
    public $name;
    public $original_file_name;

    protected $folder;

    use CompositeModelTrait;

    public function rules()
    {
        return [
            [['name', 'original_file_name'], 'string', 'max' => 125],
            [['name', 'original_file_name'], 'trim'],
            [['name'], 'filter', 'filter' => function ($value) { return str_replace('  ', ' ', $value); }],
            //[['image'], 'image', 'extensions' => self::$AllowedExtensions, 'skipOnEmpty' => true, 'whenClient' => "function (attribute, value) {
            //    return false;
            //}", 'minWidth' => self::IMAGE_MIN_WIDTH, 'minHeight' => self::IMAGE_MIN_HEIGHT],
            [['image'], 'image', 'extensions' => static::$AllowedExtensions, 'checkExtensionByMimeType' => false, 'skipOnEmpty' => true, 'minWidth' => static::IMAGE_MIN_WIDTH, 'minHeight' => static::IMAGE_MIN_HEIGHT],
            [['image'], 'required', 'whenClient' => "function (attribute, value) {
                return " . $this->isNewRecord . ";
            }"]
        ];
    }

    public function attributeLabels()
    {
        return [
            'image' => 'Изображение',
            'name' => 'Название',
            'original_file_name' => 'Оригинальное название файла',
        ];
    }

    public function behaviors()
    {
        return [
            'resource' => [
                'class' => ResourceBehavior::class,
                'attributes' => ['image'],
                'folder' => 'upload/image/' . $this->folder,
                'tmpFolder' => Yii::$app->params['UploadTempFolder']
            ]
        ];
    }

    public function getImage($filter = null, $defaultImage = null)
    {
        $resourceName = trim($this->image, '"\'');
        $defaultImage = $defaultImage ?: (Yii::$app->params['DefaultImage'] ?? '');

        if (! $resourceName) return $defaultImage;

        $absoluteResourcePath = $this->getResource('image', true);

        if (! file_exists($absoluteResourcePath)) {
            return $defaultImage;
        }

        $relativeResourcePath = $this->getResource('image');

        if ($filter) {
            $relativeResourcePath = str_replace('.', '.' . $filter . '.', $relativeResourcePath);
        }

        return $relativeResourcePath;
    }

    public function getName($num = null, $name = null)
    {
        return ($name ?: $this->name) . ($num ? " Фото № $num" : '');
    }
}

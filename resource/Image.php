<?php
namespace kr0lik\compositeModel\resource;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use kr0lik\compositeModel\CompositeModelTrait;
use kr0lik\resource\ResourceBehavior;
use kr0lik\resource\GetResourceTrait;

class Image extends Model
{
    public static $AllowedExtensions = ['jpg', 'jpeg', 'png'];

    const IMAGE_MIN_WIDTH = 500;
    const IMAGE_MIN_HEIGHT = 500;
    const IMAGE_CHECK_OPERATOR = 'OR';

    /**
     * @var string|null
     */
    public $resource;
    /**
     * @var string|null
     */
    public $name;
    /**
     * @var string|null
     */
    public $original_file_name;

    /**
     * @var string|null
     */
    protected $folder;

    use CompositeModelTrait;
    use CompositeResourceTrait;

    public function rules()
    {
        return [
            [['name', 'original_file_name'], 'string', 'max' => 125],
            [['name', 'original_file_name'], 'trim'],
            [['name'], 'filter', 'filter' => function ($value) { return str_replace('  ', ' ', $value); }],
            [['resource'], 'image', 'extensions' => static::$AllowedExtensions, 'checkExtensionByMimeType' => false, 'skipOnEmpty' => true, 'minWidth' => static::IMAGE_MIN_WIDTH, 'minHeight' => static::IMAGE_MIN_HEIGHT],
            [['resource'], 'required', 'whenClient' => "function (attribute, value) {
                return " . $this->isNewRecord . ";
            }"]
        ];
    }
    
    public function attributeLabels(): array
    {
        return [
            'resource' => 'Изображение',
            'name' => 'Название',
            'original_file_name' => 'Оригинальное название файла',
        ];
    }

    public function behaviors(): array
    {
        return [
            'resource' => [
                'class' => ResourceBehavior::class,
                'attributes' => ['resource'],
                'folder' => $this->folder,
                'tmpFolder' => Yii::$app->params['UploadTempFolder'] ?? 'temp'
            ]
        ];
    }
}

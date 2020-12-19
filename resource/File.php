<?php
namespace kr0lik\compositeModel\resource;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use kr0lik\compositeModel\CompositeModelTrait;
use kr0lik\resource\ResourceBehavior;

class File extends Model
{
    public static $AllowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];

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
     * @var string
     */
    protected $folder;

    use CompositeModelTrait;
    use CompositeResourceTrait;

    public function rules(): array
    {
        return [
            [['name'], 'required'],
            [['name', 'original_file_name'], 'string', 'max' => 125],
            [['name', 'original_file_name'], 'trim'],
            [['name'], 'filter', 'filter' => function ($value) { return str_replace('  ', ' ', $value); }],
            [['resource'], 'file', 'extensions' => static::$AllowedExtensions, 'checkExtensionByMimeType' => false, 'skipOnEmpty' => true],
            [['resource'], 'required', 'whenClient' => "function (attribute, value) {
                return " . $this->isNewRecord . ";
            }"]
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'resource' => 'Файл',
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
                'folder' => 'upload/file/' . $this->folder,
                'tmpFolder' => Yii::$app->params['UploadTempFolder']
            ]
        ];
    }

    public function getType(): string
    {
        $type = strtolower(pathinfo($this->resource, PATHINFO_EXTENSION));

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
}

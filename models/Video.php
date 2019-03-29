<?php
namespace kr0lik\compositeModel\models;

use yii\base\Model;
use kr0lik\compositeModel\CompositeModelTrait;

class Video extends Model
{
    const TYPE_YOUTUBE = 'youtube';
    const TYPE_VIMEO = 'vimeo';

    public $video;
    public $name;

    public static $AllowedSources = [
        self::TYPE_YOUTUBE => 'YouTube',
        self::TYPE_VIMEO => 'Vimeo'
    ];

    use CompositeModelTrait;

    public function rules()
    {
        return [
            [['video', 'name'], 'required'],
            [['name'], 'string', 'max' => 125],
            [['name'], 'trim'],
            [['video'], 'url'],
            [['video'], function ($attribute, $params, $validator) {
                $re = join('\.|', array_keys(static::$AllowedSources)) . '\.|youtu.be';

                if (! preg_match("/({$re})/", $this->$attribute)) {
                    $this->addError($attribute, 'Допустимы только ссылки на ресурсы: ' . join(', ', static::$AllowedSources));
                }
            }],
            [['name'], 'filter', 'filter' => function ($value) { return str_replace('  ', ' ', $value); }]
        ];
    }

    public function attributeLabels()
    {
        return [
            'video' => 'Видео',
            'name' => 'Название'
        ];
    }

    public function getVideo(): string
    {
        if (strpos($this->video, self::TYPE_VIMEO) !== false) {
            $url = str_replace('//vimeo.com/', '//player.vimeo.com/video/', $this->video);

            return $url;
        }

        if (strpos($this->video, self::TYPE_YOUTUBE) !== false) {
            $data = parse_url($this->video );

            if (isset($data['query'])) {
                parse_str($data['query'], $queryArgs);
                $videoId = $queryArgs['v'];
            } else {
                $videoId = trim($data['path'], '/');
            }

            $url = "https://youtube.com/embed/{$videoId}";

            return $url;
        }

        return $this->video;
    }

    public function getName($num = null, $name = null): string
    {
        return ($name ?: $this->name) . ($num ? " Видео № $num" : '');
    }
}

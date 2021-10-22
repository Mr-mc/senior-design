<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace humhub\modules\calendar\assets;

use humhub\modules\ui\view\components\View;
use Yii;
use yii\web\AssetBundle;

class CalendarAsset extends AssetBundle
{
    public $defer = true;

    public $publishOptions = [
        'forceCopy' => false
    ];
    
    public $sourcePath = '@calendar/resources/js';

    public $js = [
        'humhub.calendar.Calendar.min.js'
    ];

    public $depends = [
        FullCalendarAssets::class,
        CalendarBaseAssets::class
    ];

    /**
     * @param View $view
     * @return AssetBundle
     */
    public static function register($view)
    {
        $view->registerJsConfig('calendar.Calendar', [
            'text' => [
                'button.today' => Yii::t('CalendarModule.calendar', 'today'),
                'button.month' => Yii::t('CalendarModule.calendar', 'month'),
                'button.week' => Yii::t('CalendarModule.calendar', 'week'),
                'button.day' => Yii::t('CalendarModule.calendar', 'day'),
                'button.list' => Yii::t('CalendarModule.calendar', 'list'),
            ]
        ]);
        return parent::register($view);
    }

}
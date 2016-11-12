<?php
/*
 * Created by PhpStorm.
 * User: Ahmad
 * Date: 2016-06-12
 * Time: 4:34 PM
 */
namespace api\modules\v1;

/**
 * AWAMIR API V1 Module
 * 
 * @author Ahmad
 * @since 1.0
 */
class Module extends \yii\base\Module
{
    public $controllerNamespace = 'api\modules\v1\controllers';

    public function init()
    {
        parent::init();
        //Disable sessions in the API module
        \Yii::$app->user->enableSession = false;
    }
}

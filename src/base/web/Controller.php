<?php
namespace verbi\yii2RestApi\base\web;

/**
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-REST-API/
 * @license https://opensource.org/licenses/GPL-3.0
 */
class Controller extends \yii\web\Controller {
    public function behaviors()
    {
        return array_merge(parent::behaviors(),[
            // get field names
            \verbi\yii2Helpers\behaviors\base\ComponentBehavior::className(),
        ]);
    }
}
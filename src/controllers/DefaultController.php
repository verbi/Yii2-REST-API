<?php

namespace verbi\yii2RestApi\controllers;

use yii\web\Controller;

/**
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-REST-API/
 * @license https://opensource.org/licenses/GPL-3.0
 */
class DefaultController extends Controller
{
    public function actionIndex()
    {
        return $this->render('index');
    }
}

<?php

namespace verbi\yii2RestApi\controllers;

use yii\rest\Controller;
use yii\filters\auth\HttpBasicAuth;

/**
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-REST-API/
 * @license https://opensource.org/licenses/GPL-3.0
 */
class OauthController extends Controller
{
    public function behaviors() {
        $behaviors = parent::behaviors();
        switch (\Yii::$app->requestedAction->id) {
            case 'refreshToken':
                $behaviors['authenticator'] = [
                    'class' => HttpBasicAuth::className(),
                ];
            case 'csrf':
            default:
                
        }
        $behaviors['authenticator'] = [
            'class' => HttpBasicAuth::className(),
        ];
        $behaviors['contentNegotiator']['formats']['application/json'] = isset($_GET['callback']) ? \yii\web\Response::FORMAT_JSONP : \yii\web\Response::FORMAT_JSON;
        $behaviors['contentNegotiator']['formats']['application/jsonp'] = \yii\web\Response::FORMAT_JSONP;

        return $behaviors;
    }
    
    protected function verbs()
    {
        return [
            'refreshToken' => ['POST'],
            'clientId' => ['POST'],
            'secret' => ['POST',],
        ];
    }
    
    public function actionIndex() {
        return [];
    }
    
    public function actionToken( ) {
        return [
            'access_token' => \Yii::$app->getUser()->getIdentity()->getAccessToken()
                ];
    }
    
    public function actionRefreshToken( ) {
        return [
            'access_token' => \Yii::$app->getUser()->getIdentity()->getRefreshToken()
                ];
    }
    
    public function actionClientId( ) {
        return [
            'id' => '',
            'token' => '',
        ];
    }
    
    public function actionSecret( ) {
        return [
            //encrypt secret using clientId + Token
            'secret' => \yii\base\Security::encryptByPassword($secret, $clientId.$token),
        ];
    }
    
    public function actionCsrf( ) {
        return [
            'csrf' => \Yii::$app->request->getCsrfToken(),
        ];
    }
}
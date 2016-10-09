<?php

namespace verbi\yii2RestApi;

use yii\base\BootstrapInterface;

/**
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-REST-API/
 * @license https://opensource.org/licenses/GPL-3.0
 */
class Module extends \verbi\yii2RestApi\base\Module implements BootstrapInterface {
    protected $pluralizeRoute = false;
    public $controllerNamespace = 'verbi\yii2RestApi\controllers';

    public function bootstrap($app) {
        if ($app instanceof \yii\web\Application) {
            $app->getUrlManager()->addRules([
                'POST api/oauth2/<action:\w+>' => 'api/oauth2/rest/<action>',
                    ], false);
            $app->getUrlManager()->addRules($this->getUrlRules(), false);
        }
    }

    public function init() {
        parent::init();

        // custom initialization code goes here
        $this->initializeModules();
    }

    private function initializeModules() {
        $this->setModules(array_merge(
                        $this->generateModuleArray(), [
            'oauth2' => [
                'class' => 'verbi\yii2Oauth2Server\Module',
                'tokenParamName' => 'token',
                'tokenAccessLifetime' => 300,
                'storageMap' => [
                    'user_credentials' => '\verbi\yii2Oauth2Server\storages\GenericStorage',
                    'client_credentials' => '\verbi\yii2Oauth2Server\storages\GenericStorage',
                    'refresh_token' => '\verbi\yii2Oauth2Server\storages\GenericStorage',
                ],
                'grantTypes' => [
                    'user_credentials' => [
                        'class' => 'verbi\yii2Oauth2Server\grantTypes\UserCredentials',
                    ],
                    'client_credentials' => [
                        'class' => 'verbi\yii2Oauth2Server\grantTypes\ClientCredentials',
                        'allow_public_clients' => false,
                    ],
                    'refresh_token' => [
                        'class' => 'verbi\yii2Oauth2Server\grantTypes\RefreshToken',
                        'always_issue_new_refresh_token' => false
                    ],
                ]
            ],
                        ]
                )
        );
    }

    public function generateModuleArray($array = array()) {
        if (!sizeof($array)) {
            $array = \Yii::$app->modules;
        }
        $modules = array();
        foreach ($array as $name => $module) {
            $moduleArray = [];
            if (is_array($module) && isset($module['class']) && sizeof(explode('\\', $module['class'])) > 1) {
                $moduleClass = explode('\\', $module['class']);
                array_pop($moduleClass);
                $moduleClass = implode('\\', $moduleClass) . '\rest\Api';
            } elseif ($module instanceof \yii\base\Module) {

                $moduleClass = explode('\\', $module->className());
                array_pop($moduleClass);
                $moduleClass = implode('\\', $moduleClass) . '\rest\Api';
            }
            if (class_exists($moduleClass)) {
                $moduleArray['class'] = $moduleClass;

                if (isset($module['modules'])) {
                    $moduleArray['modules'] = $this->generateModuleArray($moduleArray['modules']);
                }
                $modules[$name] = $moduleArray;
            } else {
                if (isset($moduleArray['modules'])) {
                    $modules = array_merge($modules, $this->generateModuleArray($moduleArray['modules']));
                }
            }
        }
        return $modules;
    }
}

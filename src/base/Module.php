<?php
namespace verbi\yii2RestApi\base;
use yii\base\BootstrapInterface;

/**
 * @author Philip Verbist <philip.verbist@gmail.com>
 * @link https://github.com/verbi/Yii2-REST-API/
 * @license https://opensource.org/licenses/GPL-3.0
 */
class Module extends \verbi\yii2ExtendedModule\Module implements BootstrapInterface {

    public $controllerNamespace = __NAMESPACE__ . '\controllers';
    
    protected $defaultController;
    
    protected function getUrlRules() {
        $path = null;
        if( $this->module && method_exists( $this->module, 'getPath' ) ) {
            $path = $this->module->getPath() . '/';
        }
        $urlRules = [];
        
        foreach($this->getModules() as $name => $value){
            if(method_exists($this->getModule($name),'getUrlRules')) {
                $urlRules = array_merge( $urlRules, $this->getModule($name)->getUrlRules());
            }
        }
        
        $extraPatterns = $this->getExtraPatterns();
        
        return array_merge(
        $urlRules,
        [
           [
               'class' => 'verbi\yii2ExtendedRestController\UrlRule',
               'controller' => $path . $this->id,
               'extraPatterns' => $extraPatterns,
           ],
        ]);
    }
    
    public function getDefaultController() {
        if( $this->defaultController ) {
            return $this->defaultController;
        }
        $controller = $this->createController($this->defaultRoute);
        if(
                is_array( $controller )
                && isset( $controller[0] )
                && $controller[0]
        ) {
            $this->defaultController = $controller[0];
        }
        return $this->defaultController;
    }
    
    public function getAvailableActions() {
        $actions = [];
        if( ($controller = $this->getDefaultController()) ) {
            if( method_exists( $controller, 'getAvailableActions' ) ) {
                $actions = $controller->getAvailableActions();
            }
            else {
                $actions = array_keys( $controller->actions() );
            }
        }
        return $actions;
    }
    
    public function getExtraPatterns() {
        $patterns = [];
        if( ($controller = $this->getDefaultController()) ) {
            if( method_exists( $controller, 'getExtraPatterns' ) ) {
                $patterns = $controller->getExtraPatterns();
            }
            else {
               $patterns = $this->generateExtraPatterns();
            }
        }
        return $patterns;
    }
    
    protected function generateExtraPatterns() {
        $patterns = [];
        $verbs = [];
        if( ($controller = $this->getDefaultController()) ) {
            if( method_exists( $controller, 'verbs' ) ) {
                $verbs = $controller->verbs();
            }
            
            foreach( $this->getAvailableActions() as $action ) {
                $prefix = '';
                if( isset( $verbs[$action] ) ) {
                    $prefix = implode( ',', $verbs[$action] ) . ' ';
                }
                $patterns[] = $prefix . \yii\helpers\Inflector::camel2id($action);
            }
        }
        return $patterns;
    }
}

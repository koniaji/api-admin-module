<?php


namespace Obvu\Modules\Api\Admin\submodules\crud;


use Obvu\Modules\Api\Admin\submodules\crud\components\element\FullCrudElementComponent;
use Obvu\Modules\Api\Admin\submodules\crud\components\element\handlers\mongo\MongoFullCrudElementHandler;
use Obvu\Modules\Api\Admin\submodules\crud\components\element\handlers\simple\SimpleFullCrudElementHandler;
use Obvu\Modules\Api\Admin\submodules\crud\components\settings\helpers\FieldCollectorHelper;
use Obvu\Modules\Api\Admin\submodules\crud\components\settings\models\FullCrudSettings;
use yii\base\Module;
use yii2mod\rbac\filters\AccessControl;
use Zvinger\BaseClasses\api\controllers\ApiDocsSwaggerController;


/**
 * @SWG\Swagger(
 *     basePath="/api/admin/new-site/crud",
 *     produces={"application/json"},
 *     consumes={"application/json"},
 *     host=API_HOST,
 *     @SWG\Info(version="1.0", title="API Форм Deworkacy"),
 * )
 */
class FullCrudModule extends Module
{
    public $crudSettings;

    public $handlers = [];

    public $useMongo = [];

    public $useMongoAsDefault = false;

    public $accessRole = null;

    private $elementComponent;

    public $cachedData = [];

    /**
     * @return self
     */
    public static function getCurrentFullCrudHandlingModule()
    {
        if (static::$currentFullCrudHandlingModule instanceof static) {
            return static::$currentFullCrudHandlingModule;
        }
        return \Yii::$app->currentFullCrud;
    }

    public function init()
    {
//        !defined("BASE_CRUD_MODULE_PATH") ? define("BASE_CRUD_MODULE_PATH", '/'.$this->getUniqueId()) : true;
        $docsScanPaths[] = $this->basePath;
        foreach ($this->modules as $id => $module) {
            $docsScanPaths[] = $this->getModule($id)->basePath;
        }
        $this->controllerMap = [
            'docs' => [
                'class' => ApiDocsSwaggerController::class,
                'scanPaths' => $docsScanPaths,
            ],
        ];
        \Yii::$container->set(
            static::class,
            function () {
                return $this;
            }
        );
        parent::init(); // TODO: Change the autogenerated stub
    }

    private $rememberFormatting = false;

    public function holdFormatting()
    {
        $this->rememberFormatting = $this->getElementComponent()->isFormat();
        $this->getElementComponent()->setFormat(false);
    }

    public function releaseFormatting()
    {
        $this->getElementComponent()->setFormat($this->rememberFormatting);
    }


    public function getCrudSettings()
    {
        if (!($this->crudSettings instanceof FullCrudSettings)) {
            if (is_callable($this->crudSettings)) {
                $this->crudSettings = ($this->crudSettings)($this);
            }
        }

        return $this->crudSettings;
    }

    public function getElementComponent()
    {
        if (!$this->elementComponent) {
            if ($this->useMongo) {
                foreach ($this->useMongo as $useMongo) {
                    $this->handlers[$useMongo] = MongoFullCrudElementHandler::class;
                }
            }
            $this->elementComponent = \Yii::createObject(
                [
                    'class' => FullCrudElementComponent::class,
                    'handlers' => $this->handlers,
                    'module' => $this->getUniqueId(),
                    'defaultHandlerClass' => $this->useMongoAsDefault ? MongoFullCrudElementHandler::class : SimpleFullCrudElementHandler::class,
                ]
            );
        }

        return $this->elementComponent;
    }

    public static function generateModels()
    {
        \Yii::$app->runAction(
            'gii/model',
            [
                'tableName' => 'full_crud_element_table',
                'modelClass' => 'DBFullCrudElementObject',
                'ns' => '\Obvu\Modules\Api\Admin\submodules\crud\components\database\models\crud\element',
                'generateRelations' => 'none',
            ]
        );
    }

    public function getFieldHelper()
    {
        return \Yii::createObject(FieldCollectorHelper::class, [$this]);
    }

    private static $currentFullCrudHandlingModule = null;

    /**
     * @param FullCrudModule|null $module
     */
    public static function setCurrentFullCrudHandlingModule($module)
    {
        static::$currentFullCrudHandlingModule = $module;
    }
}

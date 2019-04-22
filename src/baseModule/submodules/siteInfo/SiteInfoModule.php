<?php


namespace Obvu\Modules\Api\Admin\submodules\siteInfo;


use Obvu\Modules\Api\Admin\submodules\siteInfo\components\getter\BaseSiteInfoDataGetter;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\Module;

class SiteInfoModule extends Module implements BootstrapInterface
{
    /**
     * @var BaseSiteInfoDataGetter
     */
    public $dataGetter;

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
    }

    public function getDataGetter()
    {
        if (!($this->dataGetter instanceof BaseSiteInfoDataGetter)) {
            $this->dataGetter = \Yii::createObject($this->dataGetter);
        }

        return $this->dataGetter;
    }

    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param Application $app the application currently running
     */
    public function bootstrap($app)
    {
        $modules = $this->modules;
        foreach ($modules as $module => $definition) {
            $resultModule = $this->getModule($module);
            if ($resultModule instanceof BootstrapInterface) {
                $resultModule->bootstrap($app);
            }
        }
    }
}

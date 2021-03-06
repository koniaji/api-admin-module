<?php
/**
 * Created by PhpStorm.
 * User: zvinger
 * Date: 05.05.18
 * Time: 0:00
 */

namespace Obvu\Modules\Api\AdminSubmodules\Content\components\post\object;

use Obvu\Modules\Api\Admin\models\FilledInformationEvent;
use Obvu\Modules\Api\AdminSubmodules\Content\components\post\category\PostCategoryAdminRepository;
use Obvu\Modules\Api\AdminSubmodules\Content\ContentModule;
use Obvu\Modules\Api\AdminSubmodules\Content\models\post\AdminPostModel;
use Obvu\Modules\Api\AdminSubmodules\Content\models\post\object\PostObject;
use Obvu\Modules\Api\AdminSubmodules\Content\models\post\repository\PostRepository;
use Obvu\Modules\Api\AdminSubmodules\Content\models\post\request\AdminPostInfoRequest;
use yii\base\Event;
use yii\db\ActiveRecord;
use Zvinger\BaseClasses\app\components\data\miscInfo\VendorUserMiscInfoService;
use Zvinger\BaseClasses\app\components\database\repository\BaseApiRepository;

class PostAdminRepository extends BaseApiRepository
{
    /**
     * @var PostCategoryAdminRepository
     */
    private $postCategoryAdminRepository;

    /**
     * PostAdminRepository constructor.
     * @param PostRepository $postRepository
     * @param PostCategoryAdminRepository $postCategoryAdminRepository
     */
    public function __construct(
        PostRepository $postRepository,
        PostCategoryAdminRepository $postCategoryAdminRepository
    )
    {
        $this->repository = $postRepository;
        $this->postCategoryAdminRepository = $postCategoryAdminRepository;
    }

    /**
     * @param PostObject $postObject
     * @return AdminPostModel
     */
    public function fillApiModelFromObject($postObject)
    {
        $model = $this->createElement();
        $model->category = $this->postCategoryAdminRepository->getModel($postObject->category_id);
        $skipKeys = ['category'];
        foreach ($model as $key => $value) {
            if (in_array($key, $skipKeys)) {
                continue;
            }
            $model->{$key} = $postObject->{$key};
        }
        $event = new FilledInformationEvent();
        $event->filledInformation = &$model;
        ContentModule::getInstance()->trigger(ContentModule::EVENT_FILL_POST_MODEL, $event);

        return $model;
    }

    /**
     * @param PostObject $postObject
     * @param AdminPostInfoRequest $postModel
     * @return PostObject
     */
    public function fillObjectFromApiModel($postObject, $postModel)
    {
        $postObject->category_id = $postModel->categoryId;
        $skipKeys = ['categoryId'];

        foreach ($postModel as $key => $value) {
            if (in_array($key, $skipKeys)) {
                continue;
            }
            if ($value !== null)
            $postObject->{$key} = $value;
        };
        $saved = false;
        $callback = function () use ($postObject, $postModel, $saved) {
            if ($saved) {
                return;
            }
            $event = new FilledInformationEvent();
            $event->filledInformation = &$postObject;
            $event->baseInformation = $postModel;
            ContentModule::getInstance()->trigger(ContentModule::EVENT_FILL_POST_OBJECT, $event);
            $saved = true;
        };
        $postObject->on(ActiveRecord::EVENT_AFTER_INSERT, $callback);
        $postObject->on(ActiveRecord::EVENT_AFTER_UPDATE, $callback);

        return $postObject;
    }

    public function getMiscInfo($postId)
    {
        return new VendorUserMiscInfoService($postId, 'post');
    }

    /**
     * @return AdminPostModel
     */
    private function createElement()
    {
        return new AdminPostModel();
    }
}

<?php
/**
 * Created by PhpStorm.
 * User: zvinger
 * Date: 05.05.18
 * Time: 0:00
 */

namespace Obvu\Modules\Api\Admin\submodules\content\components\post\object;


use app\components\database\repository\post\models\object\PostObject;
use app\components\database\repository\post\PostRepository;
use Obvu\Modules\Api\Admin\submodules\content\components\post\category\PostCategoryAdminRepository;
use Obvu\Modules\Api\Admin\submodules\content\models\post\AdminPostModel;
use Obvu\Modules\Api\Admin\submodules\content\models\post\request\AdminPostInfoRequest;
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
            $postObject->{$key} = $value;
        }

        return $postObject;
    }

    /**
     * @param bool $fake
     * @return AdminPostModel
     */
    private function createElement($fake = FALSE)
    {
        $object = new AdminPostModel();

        return $object;
    }
}
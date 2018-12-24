<?php


namespace Obvu\Modules\Api\Admin\submodules\crud\controllers;


use Obvu\Modules\Api\Admin\submodules\crud\models\element\index\ElementListRequest;
use Obvu\Modules\Api\Admin\submodules\crud\models\element\single\ElementSingleRequest;
use Obvu\Modules\Api\Admin\submodules\crud\models\element\single\ElementSingleResponse;

class ElementController extends BaseFullCrudController
{
    /**
     * @SWG\Post(path="/element/index",
     *     tags={"element"},
     *     summary="Получение списка элементов",
     *     @SWG\Parameter(
     *          in="body",
     *          name="body",
     *          @SWG\Schema(ref = "#/definitions/ElementListRequest")
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "Массив мероприятий",
     *         @SWG\Schema(ref = "#/definitions/ElementListResponse")
     *     ),
     * )
     */
    public function actionIndex()
    {
        $request = ElementListRequest::createRequest();

        return $this->module->getElementComponent()->listElement($request);
    }

    /**
     * @SWG\Post(path="/element/single",
     *     tags={"element"},
     *     summary="Получение конкретного элемента",
     *     @SWG\Parameter(
     *          in="body",
     *          name="body",
     *          @SWG\Schema(ref = "#/definitions/ElementSingleRequest")
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "Массив мероприятий",
     *         @SWG\Schema(ref = "#/definitions/ElementSingleResponse")
     *     ),
     * )
     */
    public function actionSingle()
    {
        $request = ElementSingleRequest::createRequest();

        return $this->module->getElementComponent()->singleElement($request);
    }

    /**
     * @SWG\Post(path="/element/update",
     *     tags={"element"},
     *     summary="Сохранение конкретного элемента",
     *     @SWG\Parameter(
     *          in="body",
     *          name="body",
     *          @SWG\Schema(ref = "#/definitions/ElementSingleResponse")
     *     ),
     *     @SWG\Response(
     *         response = 200,
     *         description = "Массив мероприятий",
     *         @SWG\Schema(ref = "#/definitions/ElementSingleResponse")
     *     ),
     * )
     */
    public function actionUpdate()
    {
        $request = ElementSingleResponse::createRequest();

        return $this->module->getElementComponent()->updateElement($request);
    }
}
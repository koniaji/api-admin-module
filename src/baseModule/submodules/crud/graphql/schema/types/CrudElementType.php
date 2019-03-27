<?php
/**
 * Created by PhpStorm.
 * User: amorev
 * Date: 06.02.2019
 * Time: 11:05
 */

namespace Obvu\Modules\Api\Admin\submodules\crud\graphql\schema\types;


use GraphQL\Type\Definition\InputObjectType;
use Obvu\Modules\Api\Admin\submodules\crud\graphql\schema\Types;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use Obvu\Modules\Api\Admin\submodules\crud\FullCrudModule;
use Obvu\Modules\Api\Admin\submodules\crud\graphql\schema\types\input\CrudFullDataInputData;
use Obvu\Modules\Api\Admin\submodules\crud\graphql\schema\types\input\CrudSortInputData;
use Obvu\Modules\Api\Admin\submodules\crud\models\element\index\ElementListRequest;
use Obvu\Modules\Api\Admin\submodules\crud\models\element\single\ElementSingleRequest;
use yii\helpers\ArrayHelper;
use yii\web\BadRequestHttpException;
use Zvinger\BaseClasses\app\graphql\base\BaseGraphQLObjectType;
use Zvinger\BaseClasses\app\graphql\base\types\input\PaginationInputType;

class CrudElementType extends BaseGraphQLObjectType
{
    public function __construct(FullCrudModule $module)
    {
        $config = [
            'fields' => function () use ($module) {
                $fullCrud = $module;
                $resultFields = [];
                foreach ($fullCrud->getCrudSettings()->blocks as $crudSingleBlock) {
                    $crudBlockType = CrudBlockType::initType([$crudSingleBlock->entityKey, $module]);
                    if ($crudSingleBlock->type == $crudSingleBlock::TYPE_CRUD) {
                        $crudBlockType = Type::listOf($crudBlockType);
                    }
                    $baseSpecialEntityFilter = $module->getSpecialFilterData($crudSingleBlock->entityKey);
                    $arr = [
                        'id' => Type::string(),
                        'fullData' => CrudFullDataInputData::initType([$crudSingleBlock->entityKey, $module]),
                        'sortData' => CrudSortInputData::initType([$crudSingleBlock->entityKey, $module]),
                        'paginationData' => PaginationInputType::initType(),
                    ];
                    if ($baseSpecialEntityFilter) {
                        $arr['specialFilters'] = $baseSpecialEntityFilter->getType();
                    }
                    $resultFields[$crudSingleBlock->entityKey] = [
                        'type' => $crudBlockType,
                        'args' => $arr,
                        'resolve' => function ($root, $args) use ($crudSingleBlock, $fullCrud, $module) {
                            $type = $crudSingleBlock->entityKey;
                            $id = $args['id'];
                            if ($crudSingleBlock->type == $crudSingleBlock::TYPE_SINGLE) {
                                $id = '0';
                            }
                            if ($crudSingleBlock->type == $crudSingleBlock::TYPE_SINGLE) {
                                $request = \Yii::createObject(
                                    [
                                        'class' => ElementSingleRequest::class,
                                        'type' => $type,
                                        'id' => $id,
                                    ]
                                );
                                $singleCrudElementModel = $module->getElementComponent()->singleElement(
                                    $request
                                );

                                $result = $singleCrudElementModel ? $singleCrudElementModel->element : null;
                                if ($crudSingleBlock->type == $crudSingleBlock::TYPE_CRUD) {
                                    $result = [$result];
                                }
                            } else {
                                $elementListFilter = $module
                                    ->getElementComponent()
                                    ->buildFilterFromGraphQLArgs($type, $args);
                                $result = $module
                                    ->getElementComponent()
                                    ->listElement(
                                        \Yii::createObject(
                                            [
                                                'class' => ElementListRequest::class,
                                                'type' => $type,
                                                'filter' => $elementListFilter,
                                                'page' => ArrayHelper::getValue($args, 'paginationData.page', 1),
                                                'perPage' => ArrayHelper::getValue($args, 'paginationData.perPage', 20),
                                            ]
                                        )
                                    )
                                    ->elements;
                            }

                            return $result;
                        },
                    ];
                };

                return $resultFields;
            },
        ];

        parent::__construct($config);
    }
}

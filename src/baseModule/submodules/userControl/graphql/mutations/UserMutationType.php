<?php
/**
 * Created by PhpStorm.
 * User: nik
 * Date: 23.04.19
 * Time: 14:48
 */

namespace Obvu\Modules\Api\Admin\submodules\userControl\graphql\mutations;


use app\models\work\user\object\UserObject;
use GraphQL\Type\Definition\Type;
use Yii;
use yii\web\BadRequestHttpException;
use Zvinger\BaseClasses\app\graphql\base\BaseGraphQLObjectType;

class UserMutationType extends BaseGraphQLObjectType
{
    public function __construct()
    {
        $config = [
            'fields' => function () {
                return [
                    'changeUserMainInfo' => [
                        'type' => Type::boolean(),
                        'args' => [
                            'username' => Type::string(),
                            'email' => Type::string(),
                            'fullName' => Type::string(),
                        ],
                        'resolve' => function (UserObject $userObject, $args) {
                            $userObject->setAttributes($args);
                            return $userObject->save();
                        }
                    ],
                    'changePassword' => [
                        'type' => Type::boolean(),
                        'args' => [
                            'old' => Type::nonNull(Type::string()),
                            'new' => Type::nonNull(Type::string()),
                            'confirm' => Type::nonNull(Type::string()),
                        ],
                        'resolve' => function (UserObject $userObject, $args) {
                            if (!Yii::$app->security->validatePassword($args['old'], $userObject->password_hash)) {
                                throw new BadRequestHttpException('Не верный текущий пароль');
                            }
                            if ($args['new'] !== $args['confirm']) {
                                throw new BadRequestHttpException('Пароли не освпадают');
                            }
                            $userObject->password_hash = Yii::$app->security->generatePasswordHash($args['new']);
                            return $userObject->save();
                        }
                    ],
                ];
            },
        ];

        parent::__construct($config);
    }
}

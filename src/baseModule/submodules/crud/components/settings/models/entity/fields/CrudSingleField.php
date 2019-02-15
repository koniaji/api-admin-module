<?php


namespace Obvu\Modules\Api\Admin\submodules\crud\components\settings\models\entity\fields;


use GraphQL\Type\Definition\Type;
use Obvu\Modules\Api\Admin\submodules\crud\graphql\schema\Types;
use Obvu\Modules\Api\Admin\submodules\crud\graphql\schema\types\crud\CrudFieldFileType;
use yii\base\BaseObject;
use yii\helpers\ArrayHelper;

class CrudSingleField extends BaseObject
{
    const TYPE_INPUT_TEXT = 'input_text';
    const TYPE_TEXTAREA = 'textarea';
    const TYPE_TEXT_EDITOR = 'textarea';
    const TYPE_SELECT = 'select';
    const TYPE_BOOLEAN_SELECT = 'boolean_select';
    const TYPE_DATE = 'input_text';
    const TYPE_FILE_PHOTO = 'file_photo';
    const TYPE_FILE_SIMPLE = 'file_photo';

    public $type;

    public $name;

    public $label;

    public $defaultValue = null;

    /**
     * @var CrudSingleSelectVariant[]
     */
    public $variants = null;

    public $multiple = false;

    public $component = 'default';

    public $fileName = null;

    /**
     * @var callable
     */
    public $variantsCallBack = null;

    public function getGraphQLFieldType()
    {
        $map = [
            self::TYPE_INPUT_TEXT => Type::string(),
            self::TYPE_TEXT_EDITOR => Type::string(),
            self::TYPE_TEXTAREA => Type::string(),
            self::TYPE_SELECT => 'select',
            self::TYPE_DATE => Type::string(),
            self::TYPE_FILE_PHOTO => new CrudFieldFileType(),
            self::TYPE_FILE_SIMPLE => new CrudFieldFileType(),
        ];

        return ArrayHelper::getValue($map, $this->type);
    }

    public function resolveField($a)
    {
//        switch ($this->type) {
//            case self::TYPE_INPUT_TEXT:
//            case self::TYPE_TEXTAREA:
//            case self::TYPE_SELECT:
//            case self::TYPE_DATE:
//                return $a[$this->name];
//                break;
//            case self::TYPE_FILE_SIMPLE:
//            case self::TYPE_FILE_PHOTO:
//                return $a[$this->name];
//                break;
//        }

        return true;
    }

    public function init()
    {
        parent::init();
        if (empty($this->variants)) {
            if (!empty($this->variantsCallBack)) {
                $variantsCallBack = $this->variantsCallBack;
                $this->variants = $variantsCallBack($this, \Yii::$app->controller->module);
            } elseif ($this->type === $this::TYPE_BOOLEAN_SELECT) {

            }
        }
    }
}

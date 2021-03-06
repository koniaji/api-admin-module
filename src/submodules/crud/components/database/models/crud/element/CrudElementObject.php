<?php
/**
 * Created by PhpStorm.
 * User: zvinger
 * Date: 24.05.18
 * Time: 12:19
 */

namespace Obvu\Modules\Api\AdminSubmodules\Crud\components\database\models\crud\element;


class CrudElementObject extends DBCrudElementObject
{
    public function getDataObject()
    {
        return json_decode($this->data);
    }

    public function setDataObject($data)
    {
        $this->data = json_encode($data);
    }

    public static function find()
    {
        return new CrudElementQuery(static::class);
    }
}
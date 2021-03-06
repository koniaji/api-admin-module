<?php
/**
 * Created by PhpStorm.
 * User: zvinger
 * Date: 05.05.18
 * Time: 0:03
 */

namespace Obvu\Modules\Api\AdminSubmodules\Content\models\textBlock;

use Zvinger\BaseClasses\app\helpers\fakeData\DataFakerGenerator;
use Zvinger\BaseClasses\app\helpers\fakeData\FakeFilledInterface;

/**
 * Class PostModel
 * @package Obvu\Modules\Api\AdminSubmodules\Content\models\textBlock
 * @SWG\Definition()
 */
class TextBlockModel implements FakeFilledInterface
{
    /**
     * @var int
     * @SWG\Property()
     */
    public $id;

    /**
     * @var int
     * @SWG\Property()
     */
    public $type;

    /**
     * @var string
     * @SWG\Property()
     */
    public $title;

    /**
     * @var string
     * @SWG\Property()
     */
    public $text;

    /**
     * @var string
     * @SWG\Property()
     */
    public $key;

    public function fillFakeData()
    {
        $faker = DataFakerGenerator::go();
        $this->id = $faker->randomDigitNotNull;
    }
}
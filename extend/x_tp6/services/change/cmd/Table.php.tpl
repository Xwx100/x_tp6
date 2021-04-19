<?php
// +----------------------------------------------------------------------
// | <headAuthor>: <headDate>
// +----------------------------------------------------------------------

namespace <namespace>;

/**
 * Class <className>
 * <recordCli>
 * @property string $database
 * @property string $table
<classProperty>
 * @package <namespace>
 */
class <className> extends \x_tp6\services\change\Table
{
    public $connection = '<connection>';

    public $database = '<database>';

    public $table = '<classTable>';

    public $isSubTable = <isSubTable>;

    public $createSql = "<createSql>";

    public $subTableKeys = <subTableKeys>;

    public $isPartition = <isPartition>;

    protected $schema = <classSchema>;

    public $modelSchema = <modelSchema>;

    public $modelType = <modelType>;
}
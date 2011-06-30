<?php
/**
 * This is the template for generating the model class of a specified table.
 * - $this: the ModelCode object
 * - $tableName: the table name for this class (prefix is already removed if necessary)
 * - $modelClass: the model class name
 * - $columns: list of table columns (name=>CDbColumnSchema)
 * - $labels: list of attribute labels (name=>label)
 * - $rules: list of validation rules
 * - $relations: list of relations (name=>relation declaration)
 */

echo "<?php\n"; ?>

/**
 * This is the model class for table "<?php echo $tableName; ?>".
 *
 * The followings are the available columns:
<?php foreach($columns as $column) { ?>
 * @property <?php echo $column->type.' $'.$column->name."\n"; ?>
<?php } ?>
<?php if(!empty($relations)) { ?>
 *
 * The followings are the available model relations:
<?php foreach($relations as $name => $relation) { ?>
 * @property <?php
    if (preg_match("~^array\(self::([^,]+), '([^']+)', '([^']+)'\)$~", $relation, $matches))
    {
        $relationType = $matches[1];
        $relationModel = $matches[2];

        switch($relationType){
            case 'HAS_ONE':
                echo $relationModel.' $'.$name."\n";
            break;
            case 'BELONGS_TO':
                echo $relationModel.' $'.$name."\n";
            break;
            case 'HAS_MANY':
                echo $relationModel.'[] $'.$name."\n";
            break;
            case 'MANY_MANY':
                echo $relationModel.'[] $'.$name."\n";
            break;
            default:
                echo 'mixed $'.$name."\n";
        }
    }
    ?>
<?php } ?>
<?php } ?>
 */
class <?php echo $modelClass; ?> extends <?php echo $this->baseClass."\n"; ?>
{

    /**
     * Returns the static model class.
     *
     * @return <?php echo $modelClass; ?> the static model class.
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    /**
     * Retrieves the associated database table name.
     *
     * @return string the associated database table name.
     */
    public function tableName()
    {
        return '<?php echo $tableName; ?>';
    }

    /**
     * Retrieves the list of associated behaviors.
     *
     * @return array associated behaviors.
     */
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        /*
        Available behaviors:
        - zii.behaviors.CTimestampBehavior
        - common.behaviors.SerializeBehavior

        $behaviors['BEHAVIOURNAME'] = array(
            'class' => 'alias.to.BEHAVIOURNAME',
        );
        */

        return $behaviors;
    }

    /**
     * Retrieves the list of validation rules for model attributes.
     *
     * @see http://www.yiiframework.com/doc/api/1.1/CModel#rules-detail
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
<?php foreach($rules as $rule): ?>
            <?php echo str_replace('=>', ' => ', $rule).",\n"; ?>
<?php endforeach; ?>

            array('<?php echo implode(', ', array_keys($columns)); ?>', 'safe', 'on' => 'search'),
        );
    }

    /**
     * Retrieves the list of relational rules.
     *
     * @see http://www.yiiframework.com/doc/api/1.1/CActiveRecord#relations-detail
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
<?php foreach($relations as $name => $relation) {
        $relation = substr($relation, 0, -1) . ", 'condition' => '', 'order' => '')";
        echo "\t\t\t'$name' => $relation,\n";
} ?>
        );
    }

    /**
     * Retrieves the list of customized attribute labels (name => label)
     *
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
<?php
foreach($labels as $name => $label) {
    echo "\t\t\t'$name' => Yii::t('model_{$modelClass}', '$label'),\n";
}
if (!empty($relation)) {
    echo "\n\t\t\t# Relations\n";
    foreach($relations as $name=>$relation) {
        echo "\t\t\t'$name' => Yii::t('model_{$modelClass}', '$name'),\n";
    }
}
?>
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     *
     * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
     */
    public function search()
    {
        $criteria = new CDbCriteria;

        // Remove attributes that should not be searched.
<?php
foreach($columns as $name => $column)
{
    if($column->type==='string')
    {
        echo "\t\t\$criteria->compare('$name', \$this->$name, true);\n";
    }
    else
    {
        echo "\t\t\$criteria->compare('$name', \$this->$name);\n";
    }
}
?>

        return new CActiveDataProvider('<?php echo $modelClass; ?>', array(
            'criteria' => $criteria,
        ));
    }

    /**
     * Returns the list of attributes for jqGrid.
     *
     * @return array
     */
    public function gridAttributes()
    {
        return array(
            // Remove attributes that should not be available in the grid.
<?php
foreach($columns as $name => $column)
{
    echo "\t\t\t'$name',\n";
}
?>
        );
    }
}

<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $queryClassName string query class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */

echo "<?php\n";
?>

namespace <?= $generator->ns ?>;

use Yii;

/**
 * This is the model class for table "<?= $generator->generateTableName($tableName) ?>".
 *
<?php foreach ($tableSchema->columns as $column): ?>
 * @property <?= "{$column->phpType} \${$column->name}\n" ?>
<?php endforeach; ?>
<?php if (!empty($relations)): ?>
 *
<?php foreach ($relations as $name => $relation): ?>
 * @property <?= $relation[1] . ($relation[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?>
<?php endforeach; ?>
<?php endif; ?>
 */
class <?= $className ?> extends <?= '\\' . ltrim($generator->baseClass, '\\') . "\n" ?>
{

<?php /* Builds manyMany helper variables for CRUD utility */?>
<?php foreach($relations as $name => $relation) : ?>
<?php if(!empty($relation['implementation']['build']) && $relation['type'] == app\templates\helpers\Relation::TYPE_MANY_MANY) : ?>
    private $_<?php echo lcfirst($name); ?>;
<?php endif; ?>
<?php endforeach; ?>

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '<?= $generator->generateTableName($tableName) ?>';
    }
<?php if ($generator->db !== 'db'): ?>

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('<?= $generator->db ?>');
    }
<?php endif; ?>    
    /**
     * @inheritdoc
     */
    /*
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['timestamp'],
                ],
            ],
            'date' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['date'],
                ],
                'value' => function(){return date('Y-m-d');},
            ],
            'time' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['time'],
                ],
                'value' => function(){return date('h:i');},
            ],
        ];
    }
    */

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [<?= "\n            " . implode(",\n            ", $rules) . "\n        " ?>];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
<?php foreach ($labels as $name => $label): ?>
            <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
<?php endforeach; ?>
<?php foreach ($relationLabels as $name => $label): ?>
            <?=  "'$name' => $label,\n" ?>
<?php endforeach; ?>
        ];
    }    

<?php foreach ($relations as $name => $relation): ?>

    /**
     * @return \yii\db\ActiveQuery
     */
    public function get<?= $name ?>()
    {
        <?= $relation[0] . "\n" ?>
    }
<?php endforeach; ?>
<?php /* Builds manyMany relation functions for CRUD utility */?>
<?php foreach($relations as $name => $relation) : 
    
        $relationModel = $relation['model'];
        $lcRelationName = lcfirst($name);
        $upRelationName = ucfirst($name);
        $varName = '$' . lcfirst($name);
        
        if(!empty($relation['implementation']['build'])) : ?>
    
    <?php     
    switch ($relation['implementation']['representation']){
        
        case app\templates\helpers\Relation::CHECKBOXLIST : 
        case app\templates\helpers\Relation::RECURSIVE_CHECKBOXLIST : 
            ?>    
    /**
     * @brief Sets <?= $upRelationName; ?> relation on helper variable and handles events insert and update
     */
    public static function set<?= $upRelationName; ?>(<?= $varName; ?>){

        if(empty(<?= $varName; ?>)){
            <?= $varName; ?> = [];
        }

        $this->_<?= $lcRelationName; ?> = <?= $varName; ?>;

        $save<?= $name; ?> = function($event){
            $this->unlinkAll('<?= $lcRelationName; ?>', true);

            foreach ($this->_<?= $lcRelationName; ?> as $id) {
                $this->link('<?= $lcRelationName; ?>', <?= $relationModel; ?>::findOne($id));
            }
        };
        $this->on(self::EVENT_AFTER_INSERT, $save<?= $name; ?>);
        $this->on(self::EVENT_AFTER_UPDATE, $save<?= $name; ?>);
    }
    
    <?php break;
        case app\templates\helpers\Relation::RECURSIVE_CHECKBOXLIST : 
        case app\templates\helpers\Relation::RECURSIVE_RADIOLIST :
            $relatedRelationName = $generator->getRelatedRelation($relationModel);
            ?>    
    /**
     * @brief Returns ordered <?= lcfirst($relationModel); ?> by each parent
     * @param <?= $relationModel; ?>[] $parents
     * @return <?= $relationModel; ?>[]
     * @throws \InvalidArgumentException
     */
    public static function getOrdered<?= $relationModel; ?>($parents = []) 
    {        
        if (!is_array($parents)) {
            throw new \InvalidArgumentException('Invalid argument. Expected: Array.');
        }        
        $nested = array();   
        
        if (empty($parents)) {
            $parents = <?= $relationModel; ?>::find()->where("parent_id IS NULL")->all();
        }
        foreach ($parents as $parent) {
            
            if (empty($parent-><?= $relatedRelationName; ?>))
                $nested[] = $parent;
            else {
                $nested[] = $parent;
                $nested = array_merge($nested, self::getOrdered<?= $relationModel; ?>($parent-><?= $relatedRelationName; ?>));
            }
        }
        return $nested;
    }
    
    /**
     * @brief Returns indented <?= lcfirst($relationModel); ?> list by its parents
     * @param <?= $relationModel; ?>[] $parents
     * @return <?= $relationModel; ?>[]
     */
    public static function getNested<?= $relationModel; ?>($parents = []) 
    {
        if (!is_array($parents)) {
            throw new \InvalidArgumentException('Invalid argument. Expected: Array.');
        }
        $nested = array();        
        if (empty($parents)) {
            $parents = <?= $relationModel; ?>::find()->where("parent_id IS NULL")->all();
        }
        foreach ($parents as $parent) {
            if (empty($parent-><?= $lcRelationName; ?>))
                $nested[] = [$parent];
            else {
                $nested[] = [
                    $parent,
                    'children'=>array_merge($nested, self::getNested<?= $relationModel; ?>($parent-><?= $relatedRelationName; ?>))
                ];
            }
        }

        return $nested;
    }
    
    /**
    * Returns each model name, indented by its parent
    * @return string $name
    */
   public function getIndentName() {
       $indent = '&nbsp;&nbsp;&nbsp;&nbsp;';
       $parent = $this->parent;
       $name = empty($parent) ? $this->name : $indent . $this->name;
       if (!empty($parent))
           do {
               if ($parent = $parent->parent)
                   $name = $indent . $name; 
           }
           while (!empty($parent)); 

       return $name;
   }
    
    <?php
    }
    ?>
    
    <?php endif; ?>
<?php endforeach; ?>
<?php if ($queryClassName): ?>
<?php
    $queryClassFullName = ($generator->ns === $generator->queryNs) ? $queryClassName : '\\' . $generator->queryNs . '\\' . $queryClassName;
    echo "\n";
?>
    /**
     * @inheritdoc
     * @return <?= $queryClassFullName ?> the active query used by this AR class.
     */
    public static function find()
    {
        return new <?= $queryClassFullName ?>(get_called_class());
    }
<?php endif; ?>
<?php if (!empty($dateColumns)) : ?>
    
    /**
     * @inheritdoc
     */
     
    public function beforeSave($insert)
    {
        if (parent::beforeSave($insert)) {            
            $this->formatDatesBeforeSave();            
            return true;
        } else {
            return false;
        }     
    }
    
    /**
     * @inheritdoc
     */
    public function afterFind()
    {        
        $this->formatDatesAfterFind();
        parent::afterFind();
    }
     
    /**
     * @brief Format dates using formatter local configuration
     */
    private function formatDatesAfterFind()
    {
    <?php foreach($dateColumns as $column) : ?>
        $this-><?= $column->name; ?> = Yii::$app->formatter->asDate($this-><?= $column->name; ?>);
    <?php endforeach; ?>
    }
     
    /**
     * @brief Format dates as database requieres it
     */
    private function formatDatesBeforeSave()
    {
    <?php foreach($dateColumns as $column) : ?>
        $this-><?= $column->name; ?> = Yii::$app->formatter->asDate($this-><?= $column->name; ?>, 'yyyy-MM-dd');
    <?php endforeach; ?>
    }
    
<?php endif; ?>
<?php /*Checks if any relations is strong. If any, deleteable will be false. */ ?>
<?php $deleteable = 'true'; $commentNames = ''; $deleteableNames = ''; ?>
<?php foreach($relations as $name => $relation) : ?>
    <?php if(isset($relation['implementation']['isDeleteable']) && $relation['implementation']['isDeleteable'] == 0):
        $deleteable = "false"; 
        $commentNames .= $name . ', ';
        else: 
            $deleteableNames .= $name . ', ';
        endif; ?>
<?php endforeach; ?>
<?php $commentNames = substr_replace($commentNames, "", -2); ?>
<?php $deleteableNames = substr_replace($deleteableNames, "", -2); ?>
     
    /**
     * @inheritdoc
     * Strong relations: <?php echo ($commentNames) ? $commentNames : 'None' ; ?>.
     */
    public function getDeletable()
    {
<?php foreach($relations as $name => $relation) : ?>
<?php if(isset($relation['implementation']['isDeleteable']) && $relation['implementation']['isDeleteable'] == 0): ?>
        if($this->get<?= ucfirst($name)?>()->exists()){
            return false;
        }
<?php endif; ?>
<?php endforeach; ?>
        return true;
    }
    
    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: <?php echo ($deleteableNames) ? $deleteableNames : 'None' ; ?>.
     */
    protected function unlinkWeakRelations(){
<?php foreach($relations as $name => $relation) : ?>
<?php if(isset($relation['implementation']['build']) && $relation['implementation']['build'] == 1 && isset($relation['implementation']['isDeleteable']) && $relation['implementation']['isDeleteable'] == 1 && $relation['type'] == app\templates\helpers\Relation::TYPE_MANY_MANY): ?>
        $this->unlinkAll('<?= lcfirst($name)?>', true);
<?php endif; ?>
<?php endforeach; ?>
    }
    
    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        if (parent::beforeDelete()) {
            if($this->getDeletable()){
                $this->unlinkWeakRelations();
                return true;
            }
        } else {
            return false;
        }
    }

}

<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\templates\generators\model;

use Yii;
use yii\db\ActiveQuery;
use yii\db\Connection;
use yii\db\Schema;
use yii\gii\CodeFile;
use yii\helpers\Inflector;
use yii\base\NotSupportedException;
use app\components\db\ActiveRecord;
use app\templates\helpers\Attribute;
use app\templates\helpers\Relation;

/**
 * This generator will generate one or multiple ActiveRecord classes for the specified database table.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Generator extends \yii\gii\Generator {

    public $db = 'db';
    public $ns = 'app\models';
    public $tableName;
    public $modelClass;
    public $baseClass = '\app\components\ActiveRecord';
    public $generateRelations = true;
    public $generateLabelsFromComments = false;
    public $useTablePrefix = false;
    public $useSchemaName = true;
    public $generateQuery = false;
    public $queryNs = 'app\models';
    public $queryClass;
    public $queryBaseClass = 'yii\db\ActiveQuery';
    public $relationOptions = [];
    public $relations = [];
    public $namespace = 'app\models';
    private $representationTypes = [
        Relation::TYPE_HAS_ONE => [
            Relation::DROPDOWNLIST => 'Dropdown list',
            Relation::RECURSIVE_RADIOLIST => 'Recursive radio button list'
        ],
        Relation::TYPE_HAS_MANY => [
        ],
        Relation::TYPE_MANY_MANY => [
            Relation::CHECKBOXLIST => 'Checkbox list',
            Relation::RECURSIVE_CHECKBOXLIST => 'Recursive checkbox list'
        ],
    ];

    /**
     * @inheritdoc
     */
    public function getName() {
        return 'Model Generator';
    }

    /**
     * @inheritdoc
     */
    public function getDescription() {
        return 'This generator generates an ActiveRecord class for the specified database table.';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return array_merge(parent::rules(), [
            [['db', 'ns', 'tableName', 'modelClass', 'baseClass', 'queryNs', 'queryClass', 'queryBaseClass'], 'filter', 'filter' => 'trim'],
            [['ns', 'queryNs'], 'filter', 'filter' => function($value) {
            return trim($value, '\\');
        }],
            [['db', 'ns', 'tableName', 'baseClass', 'queryNs', 'queryBaseClass'], 'required'],
            [['db', 'modelClass', 'queryClass'], 'match', 'pattern' => '/^\w+$/', 'message' => 'Only word characters are allowed.'],
            [['ns', 'baseClass', 'queryNs', 'queryBaseClass'], 'match', 'pattern' => '/^[\w\\\\]+$/', 'message' => 'Only word characters and backslashes are allowed.'],
            [['tableName'], 'match', 'pattern' => '/^(\w+\.)?([\w\*]+)$/', 'message' => 'Only word characters, and optionally an asterisk and/or a dot are allowed.'],
            [['db'], 'validateDb'],
            [['ns', 'queryNs'], 'validateNamespace'],
            [['tableName'], 'validateTableName'],
            [['modelClass'], 'validateModelClass', 'skipOnEmpty' => false],
            [['baseClass'], 'validateClass', 'params' => ['extends' => ActiveRecord::className()]],
            [['queryBaseClass'], 'validateClass', 'params' => ['extends' => ActiveQuery::className()]],
            [['generateRelations', 'generateLabelsFromComments', 'useTablePrefix', 'useSchemaName', 'generateQuery'], 'boolean'],
            [['enableI18N'], 'boolean'],
            [['messageCategory'], 'validateMessageCategory', 'skipOnEmpty' => false],
            [['relationOptions'], 'safe'],
        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return array_merge(parent::attributeLabels(), [
            'ns' => 'Namespace',
            'db' => 'Database Connection ID',
            'tableName' => 'Table Name',
            'modelClass' => 'Model Class',
            'baseClass' => 'Base Class',
            'generateRelations' => 'Generate Relations',
            'generateLabelsFromComments' => 'Generate Labels from DB Comments',
            'generateQuery' => 'Generate ActiveQuery',
            'queryNs' => 'ActiveQuery Namespace',
            'queryClass' => 'ActiveQuery Class',
            'queryBaseClass' => 'ActiveQuery Base Class',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function hints() {
        return array_merge(parent::hints(), [
            'ns' => 'This is the namespace of the ActiveRecord class to be generated, e.g., <code>app\models</code>',
            'db' => 'This is the ID of the DB application component.',
            'tableName' => 'This is the name of the DB table that the new ActiveRecord class is associated with, e.g. <code>post</code>.
                The table name may consist of the DB schema part if needed, e.g. <code>public.post</code>.
                The table name may end with asterisk to match multiple table names, e.g. <code>tbl_*</code>
                will match tables who name starts with <code>tbl_</code>. In this case, multiple ActiveRecord classes
                will be generated, one for each matching table name; and the class names will be generated from
                the matching characters. For example, table <code>tbl_post</code> will generate <code>Post</code>
                class.',
            'modelClass' => 'This is the name of the ActiveRecord class to be generated. The class name should not contain
                the namespace part as it is specified in "Namespace". You do not need to specify the class name
                if "Table Name" ends with asterisk, in which case multiple ActiveRecord classes will be generated.',
            'baseClass' => 'This is the base class of the new ActiveRecord class. It should be a fully qualified namespaced class name.',
            'generateRelations' => 'This indicates whether the generator should generate relations based on
                foreign key constraints it detects in the database. Note that if your database contains too many tables,
                you may want to uncheck this option to accelerate the code generation process.',
            'generateLabelsFromComments' => 'This indicates whether the generator should generate attribute labels
                by using the comments of the corresponding DB columns.',
            'useTablePrefix' => 'This indicates whether the table name returned by the generated ActiveRecord class
                should consider the <code>tablePrefix</code> setting of the DB connection. For example, if the
                table name is <code>tbl_post</code> and <code>tablePrefix=tbl_</code>, the ActiveRecord class
                will return the table name as <code>{{%post}}</code>.',
            'useSchemaName' => 'This indicates whether to include the schema name in the ActiveRecord class
                when it\'s auto generated. Only non default schema would be used.',
            'generateQuery' => 'This indicates whether to generate ActiveQuery for the ActiveRecord class.',
            'queryNs' => 'This is the namespace of the ActiveQuery class to be generated, e.g., <code>app\models</code>',
            'queryClass' => 'This is the name of the ActiveQuery class to be generated. The class name should not contain
                the namespace part as it is specified in "ActiveQuery Namespace". You do not need to specify the class name
                if "Table Name" ends with asterisk, in which case multiple ActiveQuery classes will be generated.',
            'queryBaseClass' => 'This is the base class of the new ActiveQuery class. It should be a fully qualified namespaced class name.',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function autoCompleteData() {
        $db = $this->getDbConnection();
        if ($db !== null) {
            return [
                'tableName' => function () use ($db) {
                    return $db->getSchema()->getTableNames();
                },
            ];
        } else {
            return [];
        }
    }

    /**
     * @inheritdoc
     */
    public function requiredTemplates() {
        // @todo make 'query.php' to be required before 2.1 release
        return ['model.php'/* , 'query.php' */];
    }

    /**
     * @inheritdoc
     */
    public function stickyAttributes() {
        return array_merge(parent::stickyAttributes(), ['ns', 'db', 'baseClass', 'generateRelations', 'generateLabelsFromComments', 'queryNs', 'queryBaseClass']);
    }

    /**
     * @inheritdoc
     */
    public function generate() {
        $files = [];
        $relations = $this->generateRelations();

        //Si es para generación, no para preview, armamos carpetas i18n
        if (isset($_POST['generate'])) {
            $this->generateI18Nfolder();
        }

        $db = $this->getDbConnection();
        foreach ($this->getTableNames() as $tableName) {
            // model :
            $this->relations = $relations;
            $modelClassName = $this->generateClassName($tableName);
            $queryClassName = ($this->generateQuery) ? $this->generateQueryClassName($modelClassName) : false;
            $tableSchema = $db->getTableSchema($tableName);

            $params = [
                'tableName' => $tableName,
                'className' => $modelClassName,
                'queryClassName' => $queryClassName,
                'tableSchema' => $tableSchema,
                'labels' => $this->generateLabels($tableSchema),
                'relationLabels' => $this->generateRelationLabels(isset($relations[$tableName]) ? $relations[$tableName] : []),
                'rules' => $this->generateRules($tableSchema),
                'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
                'dateColumns' => $this->getDateColumns($tableSchema->columns),
            ];


            $files[] = new CodeFile(
                    Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/' . $modelClassName . '.php', $this->render('model.php', $params)
            );

            // query :
            if ($queryClassName) {
                $params = [
                    'className' => $queryClassName,
                    'modelClassName' => $modelClassName,
                ];
                $files[] = new CodeFile(
                        Yii::getAlias('@' . str_replace('\\', '/', $this->queryNs)) . '/' . $queryClassName . '.php', $this->render('query.php', $params)
                );
            }
        }

        return $files;
    }

    /**
     * Generates the attribute labels for the specified table.
     * @param \yii\db\TableSchema $table the table schema
     * @return array the generated attribute labels (name => label)
     */
    public function generateLabels($table) {
        $labels = [];

        foreach ($table->columns as $column) {
            if ($this->generateLabelsFromComments && !empty($column->comment)) {
                $labels[$column->name] = $column->comment;
            } elseif (!strcasecmp($column->name, 'id')) {
                $labels[$column->name] = 'ID';
            } else {
                $label = Inflector::camel2words($column->name);
                if (!empty($label) && substr_compare($label, ' id', -3, 3, true) === 0) {
                    $label = substr($label, 0, -3) . ' ID';
                }
                $labels[$column->name] = $label;
            }
        }

        return $labels;
    }

    /**
     * Generates relation labels
     * @param type $relations
     * @return type
     */
    public function generateRelationLabels($relations) {
        $labels = [];
        foreach ($relations as $name => $relation) {
            if (!empty($relation['implementation']['build']))
                $labels[lcfirst($name)] = $this->generateString($name);
        }
        return $labels;
    }

    /**
     * Generates relation labels
     * @param type $relations
     * @return type
     */
    public function generateRawRelationLabels($relations) {
        $labels = [];
        foreach ($relations as $name => $relation) {
            if (!empty($relation['implementation']['build']))
                $labels[lcfirst($name)] = $this->generateRelationString($name);
        }
        return $labels;
    }

    /**
     * Generates validation rules for the specified table.
     * @param \yii\db\TableSchema $table the table schema
     * @return array the generated validation rules
     */
    public function generateRules($table) {
        $types = [];
        $lengths = [];

        foreach ($table->columns as $column) {
            if ($column->autoIncrement) {
                continue;
            }
            if (!$column->allowNull && $column->defaultValue === null) {
                $types['required'][] = $column->name;
            }
            switch ($column->type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                    $types['integer'][] = $column->name;
                    break;
                case Schema::TYPE_BOOLEAN:
                    $types['boolean'][] = $column->name;
                    break;
                case Schema::TYPE_FLOAT:
                case 'double': // Schema::TYPE_DOUBLE, which is available since Yii 2.0.3
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                    $types['number'][] = $column->name;
                    break;
                case Schema::TYPE_DATE:
                    $types['safe'][] = $column->name;
                    $types['date'][] = $column->name;
                    break;
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                    $types['safe'][] = $column->name;
                    break;
                default: // strings
                    if ($column->size > 0) {
                        $lengths[$column->size][] = $column->name;
                    } else {
                        $types['string'][] = $column->name;
                    }
            }
        }

        //Obtenemos relaciones a construir que sean manyMany y agregamos safe a las rules
        $relations = $this->getRelationsForRules($this->processRelationOptions($this->generateRelations()));
        if (!empty($relations)) {

            foreach ($relations as $name => $relation) {

                $types['safe'][] = lcfirst($name);
            }
        }

        $rules = [];
        foreach ($types as $type => $columns) {
            $rules[] = "[['" . implode("', '", $columns) . "'], '$type']";
        }
        foreach ($lengths as $length => $columns) {
            $rules[] = "[['" . implode("', '", $columns) . "'], 'string', 'max' => $length]";
        }

        // Unique indexes rules
        try {
            $db = $this->getDbConnection();
            $uniqueIndexes = $db->getSchema()->findUniqueIndexes($table);
            foreach ($uniqueIndexes as $uniqueColumns) {
                // Avoid validating auto incremental columns
                if (!$this->isColumnAutoIncremental($table, $uniqueColumns)) {
                    $attributesCount = count($uniqueColumns);

                    if ($attributesCount == 1) {
                        $rules[] = "[['" . $uniqueColumns[0] . "'], 'unique']";
                    } elseif ($attributesCount > 1) {
                        $labels = array_intersect_key($this->generateLabels($table), array_flip($uniqueColumns));
                        $lastLabel = array_pop($labels);
                        $columnsList = implode("', '", $uniqueColumns);
                        $rules[] = "[['" . $columnsList . "'], 'unique', 'targetAttribute' => ['" . $columnsList . "'], 'message' => 'The combination of " . implode(', ', $labels) . " and " . $lastLabel . " has already been taken.']";
                    }
                }
            }
        } catch (NotSupportedException $e) {
            // doesn't support unique indexes information...do nothing
        }

        return $rules;
    }

    /**
     * Generates relations using a junction table by adding an extra viaTable().
     * @param \yii\db\TableSchema the table being checked
     * @param array $fks obtained from the checkPivotTable() method
     * @param array $relations
     * @return array modified $relations
     */
    private function generateManyManyRelations($table, $fks, $relations) {
        $db = $this->getDbConnection();
        $table0 = $fks[$table->primaryKey[0]][0];
        $table1 = $fks[$table->primaryKey[1]][0];
        $className0 = $this->generateClassName($table0);
        $className1 = $this->generateClassName($table1);
        $table0Schema = $db->getTableSchema($table0);
        $table1Schema = $db->getTableSchema($table1);

        $link = $this->generateRelationLink([$fks[$table->primaryKey[1]][1] => $table->primaryKey[1]]);
        $viaLink = $this->generateRelationLink([$table->primaryKey[0] => $fks[$table->primaryKey[0]][1]]);
        $relationName = $this->generateRelationName($relations, $table0Schema, $table->primaryKey[1], true);
        $relations[$table0Schema->fullName][$relationName] = [
            "return \$this->hasMany($className1::className(), $link)->viaTable('"
            . $this->generateTableName($table->name) . "', $viaLink);",
            $className1,
            true,
            'relatedModel' => $className0,
            'type' => Relation::TYPE_MANY_MANY,
            'relationName' => $relationName,
            'implementation' => '',
            'namespace' => $this->ns,
        ];

        $link = $this->generateRelationLink([$fks[$table->primaryKey[0]][1] => $table->primaryKey[0]]);
        $viaLink = $this->generateRelationLink([$table->primaryKey[1] => $fks[$table->primaryKey[1]][1]]);
        $relationName = $this->generateRelationName($relations, $table1Schema, $table->primaryKey[0], true);
        $relations[$table1Schema->fullName][$relationName] = [
            "return \$this->hasMany($className0::className(), $link)->viaTable('"
            . $this->generateTableName($table->name) . "', $viaLink);",
            $className0,
            true,
            'relatedModel' => $className1,
            'type' => Relation::TYPE_MANY_MANY,
            'relationName' => $relationName,
            'implementation' => '',
            'namespace' => $this->ns,
        ];

        return $relations;
    }

    /**
     * @return array the generated relation declarations
     */
    public function generateRelations() {
        if (!$this->generateRelations) {
            return [];
        }

        $db = $this->getDbConnection();

        $schema = $db->getSchema();
        if ($schema->hasMethod('getSchemaNames')) { // keep BC to Yii versions < 2.0.4
            try {
                $schemaNames = $schema->getSchemaNames();
            } catch (NotSupportedException $e) {
                // schema names are not supported by schema
            }
        }
        if (!isset($schemaNames)) {
            if (($pos = strpos($this->tableName, '.')) !== false) {
                $schemaNames = [substr($this->tableName, 0, $pos)];
            } else {
                $schemaNames = [''];
            }
        }

        $relations = [];
        foreach ($schemaNames as $schemaName) {
            foreach ($db->getSchema()->getTableSchemas($schemaName) as $table) {
                $className = $this->generateClassName($table->fullName);
                foreach ($table->foreignKeys as $refs) {
                    $refTable = $refs[0];
                    $refTableSchema = $db->getTableSchema($refTable);
                    unset($refs[0]);
                    $fks = array_keys($refs);
                    $refClassName = $this->generateClassName($refTable);
                    // Add relation for this table
                    $link = $this->generateRelationLink(array_flip($refs));
                    $relationName = $this->generateRelationName($relations, $table, $fks[0], false);
                    $relations[$table->fullName][$relationName] = [
                        "return \$this->hasOne($refClassName::className(), $link);",
                        $refClassName,
                        false,
                        'type' => Relation::TYPE_HAS_ONE,
                        'relationName' => $relationName,
                        'foreignKey' => $fks[0],
                        'implementation' => '',
                        'namespace' => $this->ns,
                    ];

                    // Add relation for the referenced table
                    $uniqueKeys = [$table->primaryKey];
                    try {
                        $uniqueKeys = array_merge($uniqueKeys, $db->getSchema()->findUniqueIndexes($table));
                    } catch (NotSupportedException $e) {
                        // ignore
                    }
                    $hasMany = true;
                    foreach ($uniqueKeys as $uniqueKey) {
                        if (count(array_diff(array_merge($uniqueKey, $fks), array_intersect($uniqueKey, $fks))) === 0) {
                            $hasMany = false;
                            break;
                        }
                    }
                    $link = $this->generateRelationLink($refs);
                    $relationName = $this->generateRelationName($relations, $refTableSchema, $className, $hasMany);
                    $relations[$refTableSchema->fullName][$relationName] = [
                        "return \$this->" . ($hasMany ? 'hasMany' : 'hasOne') . "($className::className(), $link);",
                        $className,
                        $hasMany,
                        'type' => ($hasMany ? Relation::TYPE_HAS_MANY : Relation::TYPE_HAS_ONE),
                        'relationName' => $relationName,
                        'foreignKey' => $fks[0],
                        'implementation' => '',
                        'namespace' => $this->ns,
                    ];
                }

                if (($fks = $this->checkPivotTable($table)) === false) {
                    continue;
                }

                $relations = $this->generateManyManyRelations($table, $fks, $relations);
            }
        }

        if (count($relations) > 0) {
            $relations = $this->processRelationOptions($relations);
        }

        return $relations;
    }

    /**
     * Adds user selected options for each of the relations on this Generator for a model
     * @param type $relations
     * @return type
     */
    private function processRelationOptions($relations) {

        $relationBuilder = new Relation();

        if (isset($relations[$this->tableName])) {

            $tableRelations = $relations[$this->tableName];

            foreach ($tableRelations as $relationName => $relation) {

                if (!empty($this->relationOptions[$relationName])) {

                    //Sets relation and options on relationBuilder
                    $relationBuilder->setRelation($relation);
                    $relationBuilder->setOptions($this->relationOptions[$relationName]);

                    $relations[$this->tableName][$relationName] = $relationBuilder->buildRelation($relationName);
                }
            }
        }

        return $relations;
    }

    /**
     * @brief Parse relations and returns if they are manyMany and build = 1
     * @param [] $relations
     * @return []
     */
    private function getRelationsForRules($relations = []) {

        $toBuildRelations = [];
        
        if(!empty($relations[$this->tableName])){

            foreach ($relations[$this->tableName] as $name => $relation) {

                if ($relation['implementation']['build'] == 1) {

                    switch ($relation['type']) {

                        case Relation::TYPE_MANY_MANY :
                            $toBuildRelations[$name] = $relation;
                            break;
                        case Relation::TYPE_HAS_ONE :
                            $toBuildRelations[$name] = $relation;
                            break;
                    }
                }
            }
        }

        return $toBuildRelations;
    }

    /**
     * Generates the link parameter to be used in generating the relation declaration.
     * @param array $refs reference constraint
     * @return string the generated link parameter.
     */
    protected function generateRelationLink($refs) {
        $pairs = [];
        foreach ($refs as $a => $b) {
            $pairs[] = "'$a' => '$b'";
        }

        return '[' . implode(', ', $pairs) . ']';
    }

    /**
     * Checks if the given table is a junction table.
     * For simplicity, this method only deals with the case where the pivot contains two PK columns,
     * each referencing a column in a different table.
     * @param \yii\db\TableSchema the table being checked
     * @return array|boolean the relevant foreign key constraint information if the table is a junction table,
     * or false if the table is not a junction table.
     */
    protected function checkPivotTable($table) {
        $pk = $table->primaryKey;
        if (count($pk) !== 2) {
            return false;
        }
        $fks = [];
        foreach ($table->foreignKeys as $refs) {
            if (count($refs) === 2) {
                if (isset($refs[$pk[0]])) {
                    $fks[$pk[0]] = [$refs[0], $refs[$pk[0]]];
                } elseif (isset($refs[$pk[1]])) {
                    $fks[$pk[1]] = [$refs[0], $refs[$pk[1]]];
                }
            }
        }
        if (count($fks) === 2 && $fks[$pk[0]][0] !== $fks[$pk[1]][0]) {
            return $fks;
        } else {
            return false;
        }
    }

    /**
     * Generate a relation name for the specified table and a base name.
     * @param array $relations the relations being generated currently.
     * @param \yii\db\TableSchema $table the table schema
     * @param string $key a base name that the relation name may be generated from
     * @param boolean $multiple whether this is a has-many relation
     * @return string the relation name
     */
    protected function generateRelationName($relations, $table, $key, $multiple) {
        if (!empty($key) && substr_compare($key, 'id', -2, 2, true) === 0 && strcasecmp($key, 'id')) {
            $key = rtrim(substr($key, 0, -2), '_');
        }
        if ($multiple) {
            $key = Inflector::pluralize($key);
        }
        $name = $rawName = Inflector::id2camel($key, '_');
        $i = 0;
        while (isset($table->columns[lcfirst($name)])) {
            $name = $rawName . ($i++);
        }
        while (isset($relations[$table->fullName][$name])) {
            $name = $rawName . ($i++);
        }

        return $name;
    }

    /**
     * Validates the [[db]] attribute.
     */
    public function validateDb() {
        if (!Yii::$app->has($this->db)) {
            $this->addError('db', 'There is no application component named "db".');
        } elseif (!Yii::$app->get($this->db) instanceof Connection) {
            $this->addError('db', 'The "db" application component must be a DB connection instance.');
        }
    }

    /**
     * Validates the [[ns]] attribute.
     */
    public function validateNamespace() {
        $this->ns = ltrim($this->ns, '\\');
        $path = Yii::getAlias('@' . str_replace('\\', '/', $this->ns), false);
        if ($path === false) {
            $this->addError('ns', 'Namespace must be associated with an existing directory.');
        }
    }

    /**
     * Validates the [[modelClass]] attribute.
     */
    public function validateModelClass() {
        if ($this->isReservedKeyword($this->modelClass)) {
            $this->addError('modelClass', 'Class name cannot be a reserved PHP keyword.');
        }
        if ((empty($this->tableName) || substr_compare($this->tableName, '*', -1, 1)) && $this->modelClass == '') {
            $this->addError('modelClass', 'Model Class cannot be blank if table name does not end with asterisk.');
        }
    }

    /**
     * Validates the [[tableName]] attribute.
     */
    public function validateTableName() {
        if (strpos($this->tableName, '*') !== false && substr_compare($this->tableName, '*', -1, 1)) {
            $this->addError('tableName', 'Asterisk is only allowed as the last character.');

            return;
        }
        $tables = $this->getTableNames();
        if (empty($tables)) {
            $this->addError('tableName', "Table '{$this->tableName}' does not exist.");
        } else {
            foreach ($tables as $table) {
                $class = $this->generateClassName($table);
                if ($this->isReservedKeyword($class)) {
                    $this->addError('tableName', "Table '$table' will generate a class which is a reserved PHP keyword.");
                    break;
                }
            }
        }
    }

    protected $tableNames;
    protected $classNames;

    /**
     * @return array the table names that match the pattern specified by [[tableName]].
     */
    protected function getTableNames() {
        if ($this->tableNames !== null) {
            return $this->tableNames;
        }
        $db = $this->getDbConnection();
        if ($db === null) {
            return [];
        }
        $tableNames = [];
        if (strpos($this->tableName, '*') !== false) {
            if (($pos = strrpos($this->tableName, '.')) !== false) {
                $schema = substr($this->tableName, 0, $pos);
                $pattern = '/^' . str_replace('*', '\w+', substr($this->tableName, $pos + 1)) . '$/';
            } else {
                $schema = '';
                $pattern = '/^' . str_replace('*', '\w+', $this->tableName) . '$/';
            }

            foreach ($db->schema->getTableNames($schema) as $table) {
                if (preg_match($pattern, $table)) {
                    $tableNames[] = $schema === '' ? $table : ($schema . '.' . $table);
                }
            }
        } elseif (($table = $db->getTableSchema($this->tableName, true)) !== null) {
            $tableNames[] = $this->tableName;
            $this->classNames[$this->tableName] = $this->modelClass;
        }

        return $this->tableNames = $tableNames;
    }

    public function setTableName($tableName) {

        if (!empty($tableName)) {

            $this->validateTableName($tableName);

            $this->tableName = $tableName;
        }
    }

    /**
     * Generates the table name by considering table prefix.
     * If [[useTablePrefix]] is false, the table name will be returned without change.
     * @param string $tableName the table name (which may contain schema prefix)
     * @return string the generated table name
     */
    public function generateTableName($tableName) {
        if (!$this->useTablePrefix) {
            return $tableName;
        }

        $db = $this->getDbConnection();
        if (preg_match("/^{$db->tablePrefix}(.*?)$/", $tableName, $matches)) {
            $tableName = '{{%' . $matches[1] . '}}';
        } elseif (preg_match("/^(.*?){$db->tablePrefix}$/", $tableName, $matches)) {
            $tableName = '{{' . $matches[1] . '%}}';
        }
        return $tableName;
    }

    /**
     * Generates a class name from the specified table name.
     * @param string $tableName the table name (which may contain schema prefix)
     * @param boolean $useSchemaName should schema name be included in the class name, if present
     * @return string the generated class name
     */
    protected function generateClassName($tableName, $useSchemaName = null) {
        if (isset($this->classNames[$tableName])) {
            return $this->classNames[$tableName];
        }

        $schemaName = '';
        $fullTableName = $tableName;
        if (($pos = strrpos($tableName, '.')) !== false) {
            if (($useSchemaName === null && $this->useSchemaName) || $useSchemaName) {
                $schemaName = substr($tableName, 0, $pos) . '_';
            }
            $tableName = substr($tableName, $pos + 1);
        }

        $db = $this->getDbConnection();
        $patterns = [];
        $patterns[] = "/^{$db->tablePrefix}(.*?)$/";
        $patterns[] = "/^(.*?){$db->tablePrefix}$/";
        if (strpos($this->tableName, '*') !== false) {
            $pattern = $this->tableName;
            if (($pos = strrpos($pattern, '.')) !== false) {
                $pattern = substr($pattern, $pos + 1);
            }
            $patterns[] = '/^' . str_replace('*', '(\w+)', $pattern) . '$/';
        }
        $className = $tableName;
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $tableName, $matches)) {
                $className = $matches[1];
                break;
            }
        }

        return $this->classNames[$fullTableName] = Inflector::id2camel($schemaName . $className, '_');
    }

    /**
     * Generates a query class name from the specified model class name.
     * @param string $modelClassName model class name
     * @return string generated class name
     */
    protected function generateQueryClassName($modelClassName) {
        $queryClassName = $this->queryClass;
        if (empty($queryClassName) || strpos($this->tableName, '*') !== false) {
            $queryClassName = $modelClassName . 'Query';
        }
        return $queryClassName;
    }

    /**
     * @return Connection the DB connection as specified by [[db]].
     */
    protected function getDbConnection() {
        return Yii::$app->get($this->db, false);
    }

    /**
     * Checks if any of the specified columns is auto incremental.
     * @param \yii\db\TableSchema $table the table schema
     * @param array $columns columns to check for autoIncrement property
     * @return boolean whether any of the specified columns is auto incremental.
     */
    protected function isColumnAutoIncremental($table, $columns) {
        foreach ($columns as $column) {
            if (isset($table->columns[$column]) && $table->columns[$column]->autoIncrement) {
                return true;
            }
        }

        return false;
    }

    /**
     * 
     * @param ColumnSchema[] $columns
     */
    protected function getDateColumns($columns) {

        $dateColumns = [];

        if (isset($columns) && !empty($columns)) {

            foreach ($columns as $key => $column) {

                if ($column->dbType === \yii\db\oci\Schema::TYPE_DATE) {

                    $dateColumns[$key] = $column;
                }
            }
        }

        return $dateColumns;
    }

    /**
     * @brief Returns a specific array of representations for relation type 
     * @param string $relationType
     * @return type
     */
    public function fetchRelationTypes($relationType) {

        return $this->representationTypes[$relationType];
    }

    /**
     * @brief Gets another relation from this table, scoping the $modelName
     * @param type $modelName
     * @return $relation
     */
    public function getRelatedRelation($modelName) {

        $name = null;

        if (!empty($this->relations[$this->tableName])) {

            foreach ($this->relations[$this->tableName] as $relationName => $relatedRelation) {

                if ($relatedRelation['model'] == $modelName) {

                    $name = $relationName;
                }
            }
        }

        return lcfirst($name);
    }

    /**
     * @brief Create folders for i18n
     */
    public function generateI18Nfolder() {

        if ($this->enableI18N && isset(\Yii::$app->language)) {

            try {

                //Obtenemos lang de app params
                $lang = \Yii::$app->language;

                //Main folder path
                $mainFolder = \Yii::$app->basePath . "/messages";

                //Folder path
                $folder = \Yii::$app->basePath . "/messages/$lang";

                //File path
                $filename = \Yii::$app->basePath . "/messages/$lang/" . $this->messageCategory . ".php";

                //Tratamos de crear la carpeta base, sino agregamos error y seguimos 
                if (!file_exists($mainFolder)) {
                    mkdir($mainFolder, 0777, true);
                } else {
                    //chmod($mainFolder, 0777);
                }

                //Tratamos de crear la carpeta de traduccion para un lang especifico
                if (!file_exists($folder)) {
                    mkdir($folder, 0777, true);
                } else {
                    //chmod($folder, 0777);                    
                }

                //Generamos el archivo
                $this->generateI18Nfile($filename);
            } catch (yii\base\ErrorException $exception) {

                $this->addError('i18n', 'Cannot create I18N file [<strong>' . $exception->getMessage() . '</strong>]');
            }
        } else {

            $this->addError('i18n', 'Cannot create I18N file [App language param not setted]');
        }
    }

    /**
     * @brief Generates a file with translations for each attribute and relation from the model being created
     * @param type $file
     */
    public function generateI18Nfile($file) {

        //Si el archivo no existe, lo creamos
        if (!file_exists($file)) {
            fopen($file, "wb");
            //chmod($file, 0777);
            file_put_contents($file, '<?php return array();');
        }

        $relations = $this->generateRelations();

        $translations = include $file;

        if (is_array($translations)) {
            $tableSchema = $this->getDbConnection()->getTableSchema($this->tableName);

            $labels = $this->generateLabels($tableSchema);
            $relationLabels = $this->generateRawRelationLabels($relations[$this->tableName]);

            $translationsWithLabels = [];
            $translationsWithRelations = [];

            if (!empty($labels)) {
                $translationsWithLabels = array_merge($translations, $labels);
            }
            if (!empty($relationLabels)) {
                $translationsWithRelations = array_merge($translations, $relationLabels);
            }

            //Unimos, dejamos solo los elementos unicos y ordenamos alfabeticamente por key
            $translations = array_merge($translationsWithLabels, $translationsWithRelations);
            $translations = array_unique($translations);
            ksort($translations);

            file_put_contents($file, '<?php return ' . var_export($translations, true) . ';');
        }
    }

    /**
     * @brief Generates a string for a relation name
     * @param type $string
     * @return string
     */
    public function generateRelationString($string = '') {
        $string = addslashes($string);
        $str = $string;
        return $str;
    }

}

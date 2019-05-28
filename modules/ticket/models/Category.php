<?php

namespace app\modules\ticket\models;

use app\components\helpers\DbHelper;
use app\modules\config\models\Config;
use app\modules\westnet\mesa\components\models\Usuario;
use app\modules\westnet\mesa\components\request\UsuarioRequest;
use webvimark\modules\UserManagement\models\User;
use Yii;
use app\modules\ticket\TicketModule;
use app\modules\ticket\models\Schema;

/**
 * This is the model class for table "category".
 *
 * @property integer $category_id
 * @property string $name
 * @property string $description
 * @property string $slug
 * @property integer $parent_id
 * @property integer $lft
 * @property integer $rgt
 * @property integer $notify
 * @property integer $external_user_id
 * @property integer $responsible_user_id //Responde al usuario de gestion al cual se le autoasignará el ticket que pertenezca a esta categoría
 *
 * @property Category $parent
 * @property Category[] $categories
 * @property Ticket[] $tickets
 * @property integer $schema_id
 */
class Category extends \app\components\db\ActiveRecord {

    public $_statuses;
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return  DbHelper::getDbName(Yii::$app->dbticket) . '.category';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb() {
        return Yii::$app->get('dbticket');
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name', 'slug'], 'required'],
            [['description'], 'string'],
            [['parent_id', 'lft', 'rgt', 'notify', 'external_user_id', 'responsible_user_id', 'schema_id'], 'integer'],
            [['parent'], 'safe'],
            [['name'], 'string', 'max' => 100],
            [['slug'], 'string', 'max' => 45],
            [['external_user_id', 'responsible_user_id'], 'validateResponsibleUsers'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'category_id' => TicketModule::t('app', 'Category'),
            'name' => TicketModule::t('app', 'Name'),
            'description' => TicketModule::t('app', 'Description'),
            'slug' => TicketModule::t('app', 'Slug'),
            'type_id' => TicketModule::t('app', 'Type'),
            'parent_id' => TicketModule::t('app', 'Parent'),
            'tickets' => TicketModule::t('app', 'Tickets'),
            'notify' => TicketModule::t('app', 'Notify'),
            'external_user_id' => TicketModule::t('app', 'External User'),
            'responsible_user_id' => Yii::t('app', 'Responsible user'),
            'schema_id' => Yii::t('app', 'Schema'),
        ];
    }

    public function validateResponsibleUsers($attribute, $params, $validator)
    {
        if($this->external_user_id && $this->responsible_user_id) {
            $this->addError($attribute, Yii::t('app', 'Cant set as responsible an external user and a gestion user at the same time'));
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Category::class, ['category_id' => 'parent_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTickets()
    {
        return $this->hasMany(Ticket::class, ['category_id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategories()
    {
        return $this->hasMany(Category::class, ['parent_id' => 'category_id']);
    }


    /**
     * @return Usuario
     */
    public function getExternaUser()
    {
        $api = new UsuarioRequest(Config::getValue('mesa_server_address'));
        return $api->findById($this->external_user_id);
    }

    /**
     * @return Usuario
     */
    public function getResponsibleUser()
    {
        return $this->hasOne(User::class, ['id' => 'responsible_user_id']);
    }

    /**
     * @return Usuario
     */
    public function getSchema()
    {
        return $this->hasOne(Schema::class, ['schema_id' => 'schema_id']);
    }

    /**
     * @inheritdoc
     * Strong relations: None.
     */
    public function getDeletable() {
        if($this->getTickets()->exists()){
            return false;
        }
        if($this->getCategories()->exists()){
            return false;
        }
        return true;
    }

    /**
     * @brief Deletes weak relations for this model on delete
     * Weak relations: Type, Tickets.
     */
    protected function unlinkWeakRelations() {
        
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete() {
        if (parent::beforeDelete()) {
            if ($this->getDeletable()) {
                $this->unlinkWeakRelations();
                return true;
            }
        } else {
            return false;
        }
    }

    public function beforeSave($insert)
    {
        // Si tiene padre busco el ultimo hijo para sacar rgt
        if($this->parent) {
            $lastChild = $this->parent->getCategories()->orderBy(['category_id'=>SORT_DESC])->one();
            $rgt = ($lastChild ? $lastChild->rgt : $this->parent->lft );
            // Actualizo todos los nodos
            Yii::$app->dbticket->createCommand('UPDATE category SET rgt = rgt+2 WHERE rgt > ' . $rgt)->execute();
            Yii::$app->dbticket->createCommand('UPDATE category SET lft = lft+2 WHERE lft > ' . $rgt)->execute();
            $this->lft = $rgt +1;
            $this->rgt = $rgt +2;
        } else {
            // Si no hay padre busco algun hermano
            $sibling = Category::find()->where('parent_id is null')->orderBy(['category_id'=>SORT_DESC])->one();
            $rgt = ($sibling ? $sibling->rgt : 0 );
            $this->lft = $rgt +1;
            $this->rgt = $rgt +2;
        }
        return true;
    }


    public function afterDelete()
    {
        parent::afterDelete();

        $width = $this->rgt - $this->lft + 1;
        // Borro todos los hijos.
        Yii::$app->dbticket->createCommand('DELETE FROM category WHERE lft BETWEEN ' . $this->lft . " AND " . $this->rgt)->execute();

        Yii::$app->dbticket->createCommand('UPDATE category SET rgt = rgt - ' . $width . " WHERE rgt > " . $this->rgt)->execute();
        Yii::$app->dbticket->createCommand('UPDATE category SET lft = lft - ' . $width . " WHERE lft > " . $this->rgt)->execute();
    }

    private $rebuilded = false;
    private function rebuildTree()
    {
        if(!$this->rebuilded) {
            $left = ($this->parent ? $this->parent->lft : 0 );
            $this->updateAllCounters(['rgt'=>2], 'rgt > :left and category_id <> :category_id', ['left'=>$left, 'category_id'=>$this->category_id]);
            $this->updateAllCounters(['lft'=>2], 'lft > :left and category_id <> :category_id', ['left'=>$left, 'category_id'=>$this->category_id]);
            $this->rebuilded = true;
        }
    }

    /**
     * Actualiza el arbol de cuetnas.
     * @param null $parent_account_id
     * @param int $left
     * @return int
     */
    public function updateTree($parent_id=0, $left = 0)
    {
        $right = $left +1;
        $query = Category::find()->where(['parent_id'=>$parent_id]);

        if($parent_id==0) {
            $query->orWhere(['parent_id'=>null]);
        }
        $categories = $query->all();

        foreach($categories as $category) {
            $right = $this->updateTree($category->category_id, $right);
        }
        $this->updateAll(['lft'=>$left, 'rgt'=>$right], ['parent_id'=>$parent_id]);

        return $right +1;
    }

    /**
     * Retorna las categorias para ser listadas en el Arbol
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getForTree() {
        return Category::find()
            ->select("node.category_id, node.parent_id, node.name, (COUNT(parent.name) - 1) AS level")
            ->from(['category AS node'])
            ->innerJoin(['category AS parent'], 'node.lft BETWEEN parent.lft AND parent.rgt' )
            ->groupBy('node.name')
            ->orderBy('node.lft')->all();
    }

    /**
     * Retorna las categorias para ser listadas en los selects
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getForSelect() {
        return Category::find()
            ->select(["node.category_id", "node.external_user_id", "node.parent_id", "CONCAT( REPEAT('&nbsp;&nbsp;', COUNT(parent.name) - 1), node.name ) as name", "(COUNT(parent.name) - 1) AS level", "node.lft", "node.rgt"])
            ->from(['category AS node'])
            ->innerJoin(['category AS parent'], 'node.lft BETWEEN parent.lft AND parent.rgt' )
            ->groupBy('node.name')
            ->orderBy('node.lft')->all();
    }

    /**
     * Retorna las categorias para ser listadas en los selects
     *
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getForSelectChilds($category_id) {
        $category = Category::findOne(['category_id'=>$category_id]);

        return Category::find()
            ->select(["node.category_id", "node.external_user_id", "node.parent_id", "CONCAT( REPEAT('&nbsp;&nbsp;', COUNT(parent.name) - 1), node.name ) as name", "(COUNT(parent.name) - 1) AS level", "node.lft", "node.rgt"])
            ->from(['category AS node'])
            ->innerJoin(['category AS parent'], 'node.lft BETWEEN parent.lft AND parent.rgt' )
            ->where('node.lft >= '.$category->lft.' and node.rgt <= '.$category->rgt)
            ->groupBy('node.name')
            ->orderBy('node.lft')->all();
    }
}

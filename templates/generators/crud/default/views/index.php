<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

echo "<?php\n";
?>

use yii\helpers\Html;
<?php if($generator->buildExportOptions) : ?>
use kartik\grid\GridView;
use kartik\export\ExportMenu;
<?php else : ?>
use <?= $generator->indexWidgetType === 'grid' ? "yii\\grid\\GridView" : "yii\\widgets\\ListView" ?>;
<?php endif; ?>

/* @var $this yii\web\View */
<?= !empty($generator->searchModelClass) ? "/* @var \$searchModel " . ltrim($generator->searchModelClass, '\\') . " */\n" : '' ?>
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))); ?>;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index">

    <h1><?= "<?= " ?>Html::encode($this->title) ?></h1>
<?php if(!empty($generator->searchModelClass)): ?>
<?= "    <?php " . ($generator->indexWidgetType === 'grid' ? "// " : "") ?>echo $this->render('_search', ['model' => $searchModel]); ?>
<?php endif; ?>

    <p>
        <?= "<?= " ?>Html::a("<span class='glyphicon glyphicon-plus'></span> " . <?= $generator->generateString('Create {modelClass}', ['modelClass' => StringHelper::basename($generator->modelClass) ]); ?>, 
        ['create'], 
        ['class' => 'btn btn-success']) 
        ;?>
    </p>
    
<?php if($generator->buildExportOptions) : ?>
    
     <?= "<?= " ?>ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
<?php 
$count = 0;
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        if (++$count < 20) {
            echo "            '" . $name . "',\n";
        } else {
            echo "            // '" . $name . "',\n";
        }
    }
} else {
    foreach ($tableSchema->columns as $column) {
        
        //Attribute
        $attr = new \app\templates\helpers\Attribute($column);
        if ($attr->analizer->isToBuildRelation($generator->relations)) {
            //Obtenemos relacion
            $relation = $attr->analizer->getRelation($generator->relations); 
            //Obtenemos attr nomenclador del model relacionado
            if(!empty($relation['name']) && !empty($relation['model'])){ 
            $nameAttribute = \app\templates\helpers\RelationBuilder::fetchNameAttribute($relation['model'], $generator->namespace); ?>
            [
                'header'=> '<?php echo $relation['name']; ?>',
                'value'=>function($model){ if(!empty($model-><?php echo lcfirst($relation['name']); ?>)) return $model-><?php echo lcfirst($relation['name']); ?>-><?php echo $nameAttribute; ?>; }
            ],        
        <?php }else{                
                $format = $generator->generateColumnFormat($column);
                if (++$count < 20) {
                    echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
                } else {
                    echo "            // '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
                }
            }
            
        }else{
            $format = $generator->generateColumnFormat($column);
            if (++$count < 20) {
                echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
            } else {
                echo "            // '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
            }
        }
    }
} ?>
        ],
        'showConfirmAlert'=>false
    ]); ?>

<?php endif; ?>

<?php if ($generator->indexWidgetType === 'grid'): ?>
    <?= "<?= " ?>GridView::widget([
        'dataProvider' => $dataProvider,
        <?= !empty($generator->searchModelClass) ? "'filterModel' => \$searchModel,\n        'columns' => [\n" : "'columns' => [\n"; ?>
            ['class' => 'yii\grid\SerialColumn'],

<?php
$count = 0;
if (($tableSchema = $generator->getTableSchema()) === false) {
    foreach ($generator->getColumnNames() as $name) {
        if (++$count < 6) {
            echo "            '" . $name . "',\n";
        } else {
            echo "            // '" . $name . "',\n";
        }
    }
} else {
    foreach ($tableSchema->columns as $column) {
        
        //Attribute
        $attr = new \app\templates\helpers\Attribute($column);
        if ($attr->analizer->isToBuildRelation($generator->relations)) {
            //Obtenemos relacion
            $relation = $attr->analizer->getRelation($generator->relations); 
            
            //Obtenemos attr nomenclador del model relacionado, si no existe, no mostramos la relacion
            if(!empty($relation['name']) && !empty($relation['model'])){
            $nameAttribute = \app\templates\helpers\RelationBuilder::fetchNameAttribute($relation['model'], $relation['namespace']); 
            if($nameAttribute) : ?>
            [
                'header'=> '<?php echo $relation['name']; ?>',
                'value'=>function($model){ if(!empty($model-><?php echo lcfirst($relation['name']); ?>)) return $model-><?php echo lcfirst($relation['name']); ?>-><?php echo $nameAttribute; ?>; }
            ],        
            <?php endif; ?>
        <?php }else{
                $format = $generator->generateColumnFormat($column);
                if (++$count < 6) {
                    echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
                } else {
                    echo "            // '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
                }
            }
            
        }else{
            $format = $generator->generateColumnFormat($column);
            if (++$count < 6) {
                echo "            '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
            } else {
                echo "            // '" . $column->name . ($format === 'text' ? "" : ":" . $format) . "',\n";
            }
        }
    }
}
?>

            [
                'class' => 'app\components\grid\ActionColumn',
            ],
        ],
    ]); ?>
<?php else: ?>
    <?= "<?= " ?>ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'item'],
        'itemView' => function ($model, $key, $index, $widget) {
            return Html::a("<span class='glyphicon glyphicon-eye-open'></span>" . Html::encode($model-><?= $nameAttribute ?>), ['view', <?= $urlParams ?>]);
        },
    ]); ?>
<?php endif; ?>

</div>

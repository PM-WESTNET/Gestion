<?php

namespace app\modules\westnet\isp\models;

use yii\base\Model;

/**
 * Description of Plan
 *
 * @author smaldonado
 */
class Plan extends Model {

    public $id;
    public $name;
    public $code;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [

            [
                ['id', 'name', 'code'], 'safe'
            ],
            [['name'], 'required'],
            [['name'], 'string', 'min' => 3, 'max' => 255],
        ];
    }

    public function __construct($plan)
    {
        if ($plan instanceof \app\modules\sale\modules\contract\models\Plan) {
            $this->name = $plan->name;
            $this->id = $plan->getPlan_id();
            $this->code = $plan->system;
        } elseif(is_array($plan)) {
            $this->load(['Plan'=>$plan]);
        }
    }

    public function merge(\app\modules\sale\modules\contract\models\Plan $plan)
    {
        $this->id                       = $plan->getPlan_id();
        $this->name                     = $plan->name;
        $this->code                     = $plan->system;
    }
}

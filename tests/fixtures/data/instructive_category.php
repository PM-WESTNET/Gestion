<?php

return [
  [
      'instructive_category_id' => 1,
      'name' => 'Test Me',
      'status' => \app\modules\instructive\models\InstructiveCategory::STATUS_ENABLED,
      'created_at' => (new \DateTime('now'))->getTimestamp(),
      'updated_at' => (new \DateTime('now'))->getTimestamp(),
  ],
  [
      'instructive_category_id' => 2,
      'name' => 'Test Me2',
      'status' => \app\modules\instructive\models\InstructiveCategory::STATUS_ENABLED,
      'created_at' => (new \DateTime('now'))->getTimestamp(),
      'created_at' => (new \DateTime('now'))->getTimestamp(),
      'updated_at' => (new \DateTime('now'))->getTimestamp(),
  ]
];
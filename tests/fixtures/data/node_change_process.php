<?php

$node = [
    [
        'node_change_process_id' => 1,
        'created_at' => (new \DateTime())->modify('-1 month')->format('Y-m-d H:i:s'),
        'ended_at' => (new \DateTime())->modify('-1 month')->format('Y-m-d H:i:s'),
        'ended_at' => (new \DateTime())->modify('-1 month')->format('Y-m-d 23:59:48'),
        'node_id' => 1,
        'creator_user_id' => 1
    ]
];

return $node;
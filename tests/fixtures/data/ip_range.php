<?php

return [
    [
        'ip_range_id' => 1,
        'ip_start' => ip2long('10.1.0.2'),
        'ip_end' => ip2long('10.1.0.255'),
        'status' => 'enabled',
        'node_id' => 1
    ],
    [
        'ip_range_id' => 2,
        'ip_start' => ip2long('10.2.0.2'),
        'ip_end' => ip2long('10.2.0.255'),
        'status' => 'enabled',
        'node_id' => 2
    ],
    [
        'ip_range_id' => 3,
        'ip_start' => ip2long('172.2.0.2'),
        'ip_end' => ip2long('172.2.0.5'),
        'status' => 'enabled',
        'access_point_id' => 1
    ],
    [
        'ip_range_id' => 4,
        'ip_start' => ip2long('172.4.0.2'),
        'ip_end' => ip2long('172.4.0.5'),
        'status' => 'enabled',
        'access_point_id' => 1
    ],
];
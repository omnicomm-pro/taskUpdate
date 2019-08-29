<?php require 'crest.php';

$usersID = [];

if(!empty($_POST['data']['FIELDS_AFTER']['ID'])) {
    $taskID = $_POST['data']['FIELDS_AFTER']['ID'];
    $batch = [
        'get_users' => [
            'method' => 'user.get',
            'params' => [
                'FILTER' => [
                    'UF_DEPARTMENT' => 83,
                    'ACTIVE' => true
                ]
            ]
        ],
        'get_task' => [
            'method' => 'tasks.task.get',
            'params' => [
                'taskId' => $taskID,
                'select' => ['ID', 'RESPONSIBLE_ID', 'AUDITORS']
            ]
        ]
    ];
    $Request = CRest::callBatch($batch);
    foreach ($Request['result']['result']['get_users'] as $getUser) {
        $usersID[] = $getUser['ID'];
    }
    $responsibleID = $Request['result']['result']['get_task']['task']['responsibleId'];
    $auditors = $Request['result']['result']['get_task']['task']['auditors'];
    
    if($responsibleID) {
        $key = array_search($responsibleID, $usersID);
        unset($usersID[$key]);
    }
    $updFields = [
        'taskId' => $taskID,
        'fields' => [
            'AUDITORS' => $usersID
        ]
    ];
    $taskUpdate = CRest::call('tasks.task.update', $updFields);
}
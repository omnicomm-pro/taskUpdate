<?php

// Получение списка пользователей отдела исполнителя задачи
global $USER;
$usersID = [];
$Filter = [
	'ACTIVE' => true,
	'UF_DEPARTMENT' => 83
];
$Select = [
	'FIELDS' => ['ID']
];
$Users = CUser::GetList($by='ID', $order='ASC', $Filter, $Select);
while($user = $Users->Fetch()) {
	$usersID[] = $user['ID'];
}

// Событие перед созданием задачи
AddEventHandler("tasks", "OnBeforeTaskAdd", "OnBeforeTaskAdd"); 
function OnBeforeTaskAdd(&$arFields) {
	global $usersID;
	$responsibleID = $arFields['RESPONSIBLE_ID'];
	if($responsibleID) {
		$key = array_search($responsibleID, $usersID);
		unset($usersID[$key]);
	}
	$arFields['AUDITORS'] = $usersID; 
	return $arFields;
}

//Событие перед изменением задачи
AddEventHandler("tasks", "OnBeforeTaskUpdate", "OnBeforeTaskUpdate"); 
function OnBeforeTaskUpdate($taskID, &$arFields) {
	global $usersID;
	$responsibleID = $arFields['RESPONSIBLE_ID'];
	if($responsibleID) {
		$key = array_search($responsibleID, $usersID);
		unset($usersID[$key]);
	}
	$arFields['AUDITORS'] = $usersID; 
	return $arFields;
}

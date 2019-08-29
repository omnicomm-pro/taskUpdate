<?php
// Обработчик, срабатывает при создании/делегировании задачи, если ответственным назначена Хлопцева О.Е., добавляет в наблюдатели сотрудников её отдела
AddEventHandler('tasks', 'OnTaskAdd', ['TaskHandler', 'taskUpdate']);
AddEventHandler('tasks', 'OnTaskUpdate', ['TaskHandler', 'taskUpdate']);

class TaskHandler {
	function taskUpdate($taskID) {
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
		if(CModule::IncludeModule('tasks')) {
			
			$rsTask = CTasks::GetByID($taskID);
			$arTask = $rsTask->GetNext();
			$responsibleID = $arTask['RESPONSIBLE_ID'];
			if($responsibleID) {
				$key = array_search($responsibleID, $usersID);
				unset($usersID[$key]);
			}
			$updFields = [
				'AUDITORS' => $usersID
			];
			$TASKS = new CTasks;
			$taskUpdate = $TASKS->Update($taskID, $updFields);
		}
	}
}
<?php
class Application {

	public $error;

	function getTeam() {
		$teamList = $this->getTeamData();
		$html = '';
		for ($i = 0; $i < count($teamList); $i++) {
			$html .= '<div class="addFormTeamItem"><img src="media/images/team/' . $teamList[$i]['id'] . '.jpg" alt=""></div>';
		}
		$html .= '<div class="addFormTeamItem addFormTeamItemAdd"><img src="media/images/icons/userPlus.svg
		"></div>';
		return $html;
	}

	function getTeamData() {
        if (session_status() !== PHP_SESSION_ACTIVE) {
			session_start();
		}
		require_once $_SERVER['DOCUMENT_ROOT'] . '/core/_dataSource.class.php';
		$dataRow = new DataSource('select id, name from dir_users where id != "' . $_SESSION["userId"] . '"');
		$data = $dataRow->getData();
		if (!$data) {
            $this->error = 'Совсем пусто';
            return false;
        }
        return $data;
	}
	function getBoardsData() {
		if (session_status() !== PHP_SESSION_ACTIVE) {
			session_start();
		}
		require_once $_SERVER['DOCUMENT_ROOT'] . '/core/_dataSource.class.php';
		$dataRow = new DataSource('select id, name, (select count(*) from dir_boards b join dir_tasks_boards tb on b.id = tb.board_id where br.id = tb.board_id) amount from dir_boards br where br.owner = "' . $_SESSION["userId"] . '"');
		$data = $dataRow->getData();
		if (!$data) {
            $this->error = 'Совсем пусто';
            return false;
        }
        return $data;
	}

	function getTasksData() {
		if (session_status() != PHP_SESSION_ACTIVE) {
			session_start();
		}
		require_once $_SERVER['DOCUMENT_ROOT'] . '/core/_dataSource.class.php';
		$dataRow = new DataSource('select
(select count(*) from dir_tasks_users u where u.task_id = t.id) members,
t.name taskName, t.id taskId, t.est_date estDate, t.time_start timeStart, t.time_end timeEnd, b.name boardName from dir_tasks t left join dir_tasks_boards tb on tb.task_id = t.id left join dir_boards b on b.id = tb.board_id where t.owner_id = "' . $_SESSION["userId"] . '"');
		$data = $dataRow->getData();
		if (!$data) {
            $this->error = 'Совсем пусто';
            return false;
        }
        return $data;
	}

	function getBoards() {
		if (session_status() != PHP_SESSION_ACTIVE) {
			session_start();
		}
		$boardsList = $this->getBoardsData();
		$html = '<div class="boards row">';
		for ($i = 0; $i < count($boardsList); $i++) {
			$html .= '<div class="boardsItem">
				<div class="boardsItemImage"></div>
				<p class="boardsItemName">' . $boardsList[$i]['name'] . '</p>
				<p class="boardsItemAmount">' . $boardsList[$i]['amount'] . ' задач</p>
			</div>';
		}
		$html .= '<div class="boardsItem">
			<div class="boardsItemImage"></div>
			<p class="boardsItemName">Новая доска</p>
			<p class="boardsItemAmount"></p>
		</div>';
		return $html;
	}


	function getBoardsList() {
		$boardsList = $this->getBoardsData();
		$html = '';
		for ($i = 0; $i < count($boardsList); $i++) {
			$html .= '<div class="boardsListItem">
				<p class="boardsListItemName">' . $boardsList[$i]['name'] . '</p>
			</div>';
		}
			$html .= '<div class="boardsListItem boardsListItemAdd">
				<p class="boardsListItemName">+</p>
			</div>';
		return $html;
	}

	function getTasks() {
		$tasksList = $this->getTasksData();
		$html = '<div class="buttonTask">Новая задача</div>
		<div class="tasks row">';
		for ($i = 0; $i < count($tasksList); $i++) {
			$html .= '<div class="boardsItem">
				<div class="boardsItemImage"></div>
				<p class="boardsItemName">' . $tasksList[$i]['name'] . '</p>
				<p class="boardsItemAmount">' . ($tasksList[$i]['members']+1) . ' участников</p>
			</div>';
		}
		$html .= '</div>';
		return $html;
	}

	function addTaskForm() {
		$html .= '<div class="overlayTitle">
        			<img src="media/images/icons/arrowBack.svg" class="overlayTitleIcon">Добавить задачу
        		</div>
        		<div class="addForm overlayInner">
                   <div class="addFormTitle">Название</div>
                   <input type="text">
                   <div class="addFormTitle">Участники</div>
                   <div class="addFormTeam">
                   		<div class="addFormTeamArrow" id="teamArrowLeft" data-id="-1">
                   			<img src="media/images/icons/teamArrowLeft.svg" alt="">
                   		</div>
						<div class="row addFormTeamInner">'
						 . $this->getTeam()
						 . '</div>
                    	<div class="addFormTeamArrow" id="teamArrowRight" data-id="1">
                   			<img src="media/images/icons/teamArrowRight.svg" alt="">
                   		</div>
                   </div>
                   <div class="addFormTitle">Дата</div>
                   <div class="addFormDate"><span>' . date('m/d/Y') . '</span><img src="media/images/icons/calendar.svg" alt=""></div>
                   <input type="text" id="datepicker">
                   <div class="row addFormTime">
                   		<div class="addFormTimeFrom">
                   			<div class="addFormTitle">Начало</div>
                   			<select name="" id="">';
                   	for ($i=0;$i<24;$i++){
                   					$html .= '<option>' . $i . ':00</option>';
                   				}		
                   				$html .= '</select>
                   		</div>	
                   		<div class="addFormTimeTo">
                   			<div class="addFormTitle">Конец</div>
                   			<select name="" id="">';
                   		for ($i=0;$i<24;$i++){
                   					$html .= '<option>' . $i . ':00</option>';
                   				}	
                   				$html .= '</select>
                   		</div>	
                   </div>
                   <div class="addFormTitle">Описание</div>
                   <textarea name="" id="" cols="30" rows="10"></textarea>
                   <div class="addFormTitle">Доска</div>
                   <div class="boardsList row">'
						 . $this->getBoardsList()
						 . '
                   </div>
                   <div class="button buttonDone">Готово</div>
               </div>';	
		return $html;
	}
}
?>
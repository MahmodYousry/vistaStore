<?php

	/*
	**	Get All Function v2.0
	**	Function To get All Records From Any Database Table
	*/

	function getAllFrom($field, $table, $where = NULL, $and = NULL, $orderfield, $ordering = "DESC") {

		global $con;

		$getAll = $con->prepare("SELECT $field FROM $table $where $and ORDER BY $orderfield $ordering");

		$getAll->execute();

		$all = $getAll->fetchAll();

		return $all;

	}

	/*
	** Title Function v1.0
	** Title Function That Echo The Page Title In Case The Page
	** Has The Variable $pageTitle And Echo Default Title For Other Pages
	*/

	function getTitle() {

		global $pageTitle;

		if (isset($pageTitle)) {

			echo $pageTitle;

		} else {

			echo 'Default';

		}

	}

	/*
	** Home Redirect Function v2.0
	** This Function Accept Parameters.
	** $theMsg = Echo The Message [ Error | Success | Warning ].
	** $url = Link That You Want to Redirect To.
	** $seconds = Seconds Before Redirecting.
	*/

	function redirectHome($theMsg, $url = null, $seconds = 3) {

		if ($url === null) {

			$url = 'index.php';

			$link = 'Homepage';

		} else {

			if (isset($_SERVER['HTTP_REFERER']) && $_SERVER['HTTP_REFERER'] !== '') {

				$url = $_SERVER['HTTP_REFERER'];

				$link = 'Previous Page';

			} else {

				$url = 'index.php';

				$link = 'Homepage';

			}

		}

		echo $theMsg;

		echo "<div class='alert alert-info'>You Will Be Redirected To $link After $seconds Seconds.</div>";

		header("refresh:$seconds;url=$url");

		exit();

	}

	/* 
	** Check Items Function v1.0
	** Function To Check Item In Database [ Function Accept Parameters ]
	** $select = The Item To Select [ Example: user, item, category ]
	** $from = The Table To Select From [ Example: users, items, categories ]
	** $Value = The Value Of Select [ Example: Osama, Box, Electronics ]
	*/

	function checkItem($select, $from, $value) {

		global $con;
		$statement = $con->prepare("SELECT $select FROM $from WHERE $select = ?");
		$statement->execute(array($value));
		$count = $statement->rowCount();
		return $count;

	}

	/* 
	** Check avatar Function v1.0
	** Function To Check avatar In Database [ Function Accept Parameters ]
	** $select = The avatar To Select [ Example: user, item, category ]
	** $from = The Table To Select From [ Example: users, items, categories ]
	** $Value = The Value Of avatar [ Example: Osama, Box, Electronics ]
	*/

	function avatarCheck($select, $from, $value, $value2) {

		global $con;

		$statement = $con->prepare("SELECT $select FROM $from WHERE $value $value2");
		$statement->execute();
		$count = $statement->rowCount();

		return $count;

	}



	/*
	**	Count Number Of Items Function v1.0
	**	Function To Count Number Of Items Rows.
	**	$item = The Item To Count.
	**	$table = The Table To Choose From.
	**
	*/

	function countItems($item, $table) {

		global $con;

		$stmt2 = $con->prepare("SELECT COUNT($item) FROM $table");

     	$stmt2->execute();

     	return $stmt2->fetchColumn();

	}



	/*
	**	Get Latest Records Function v1.0
	**	Function To get Latest items from database [ Users, Items, Comments ]
	**	$select = Field To Select.
	**	$table = The Tabke To Choose From.
	**  $order = the DESC ordering.
	**	$limit = number of records to get.
	*/


	function getLatest($select, $table, $order, $limit = 5) {

		global $con;

		$getStmt = $con->prepare("SELECT $select FROM $table ORDER BY $order DESC limit $limit");

		$getStmt->execute();

		$rows = $getStmt->fetchAll();

		return $rows;

	}


	/*
	**	Get Members without admins Function v1.0
	**	Function To get Latest items from database [ Users, Items, Comments ]
	**	$select = Field To Select.
	**	$table = The Tabke To Choose From.
	**  $order = the DESC ordering.
	**	$limit = number of records to get.
	*/
	
	function getAllMembers() {
		global $con;
		$getStmt = $con->prepare("SELECT * FROM users WHERE GroupID = 0");
		$getStmt->execute();
		$rows = $getStmt->rowCount();
		return $rows;
	}


	// function that gets image from items to show it on items.php
	function getimg($img) {
		
		global $con;
		
		$getAny = $con->prepare("SELECT Image FROM items WHERE Item_ID = ? Limit 1");
		
		$getAny->execute(array($img));
		
		$lnk = $getAny->fetchAll();
		
		return $lnk;
		
	}



	/* 
	** Delete Complete Row Function v1.0
	** Function To Delete Item In Database [ Function Accept Parameters ]
	** $select = The Item To Select [ Example: user, item, category ]
	** $from = The Table To Select From [ Example: users, items, categories ]
	** $Value = The Value Of Select [ Example: Osama, Box, Electronics ]
	*/

	function deleteFrom($table, $id_name, $id_value) {

		global $con;
		$statement = $con->prepare("DELETE FROM $table WHERE $id_name = ?");
		$statement->execute([$id_value]);
		$count = $statement->rowCount();
		return $count;

	}


	/* 
	** get avatar from db using id of row in specific table Function v1.0
	** $select = The Item To Select [ Example: user, item, category ]
	** $from = The Table To Select From [ Example: users, items, categories ]
	** $Value = The Value Of Select [ Example: Osama, Box, Electronics ]
	*/

	function getAvatar($row, $table, $idRowName, $id) {

		global $con;
		$statement = $con->prepare("SELECT $row FROM $table WHERE $idRowName = ?");
		$statement->execute([$id]);
		$avaUrl = $statement->fetch();
		return $avaUrl[$row];

	}


		/* 
	** get avatar from db using id of user Function v1.0
	** $select = The Item To Select [ Example: user, item, category ]
	** $from = The Table To Select From [ Example: users, items, categories ]
	** $Value = The Value Of Select [ Example: Osama, Box, Electronics ]
	*/

	function getUserAvatar($id) {

		global $con;
		$statement = $con->prepare("SELECT avatar FROM users WHERE UserID = ?");
		$statement->execute([$id]);
		$avaUrl = $statement->fetch();

		if ($statement->rowCount() > 0) {
			return $avaUrl['avatar'];
		} else {
			return 0;
		}

	}

	/* 
	** DeleteServFile Function v1.0
	** To delete files Using the Url Given Parameters
	*/

	function DeleteServFile($url) {
		if (file_exists($url)) { // if file exits
			// Delete The file
			unlink($url);
		} else {
			return "<div class='alert alert-info text-capitalize'>".$url." <strong>File Does Not Exist In The Server</strong></div>";
		}
	}

	
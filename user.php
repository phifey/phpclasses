<?php
include 'userValidation.php';
class User
{
	public $_error = false;
	private $_user;
	private $_pass;
	private $_errorMsg;
	private $_successMsg;
	protected $_db;
	
	public function __construct(PDO $db, $user = '', $pass = '')
	{
		$this->_db = $db;
		$this->_user = $user;
		$this->_pass = $pass;
	}
	
	public function login()
	{
		if(strLength($this->_user,6,50) && strLength($this->_pass,6,50))
		{
			if($this->_checkCredentials($this->_user, $this->_pass))
			{
				$_SESSION['validUser'] = true;
				$_SESSION['userID'] = $this->_user;
				$_SESSION['userPass'] = $this->_pass;
				return true;
			}
			$this->_error = true;
			$this->_errorMsg = "Credentials did not match any of our users.";
		} else {
			$this->_error = true;
			$this->_errorMsg = "Username and password need a minimum of 6 characters and no more than 50!";
			return false;
		}
	}
	
	public function logoutUser()
	{
	    session_unset();
		session_destroy();
		$message = urlencode("true");
		header("Location: index.php?LogoutSuccess=".$message);
		exit();
	}
	
	public function displayTable()
	{
		if($this->_fetchUserTable())
		{
			return true;
		}
		$this->_error = true;
		$this->_errorMsg = "<p style='color:white'>User table could not be loaded</p>";
		return false;
	}
	
	public function updateUser()
	{
		if($this->_updateUserRow())
		{
			return true;
		}
		return false;
	}
	
	public function deleteUser()
	{
		if($this->_deleteUserRow())
		{
			$this->_successMsg = "<p style='color: black'>Row successfully deleted from the database</p>";
			return true;
		}
		$this->_error = true;
		$this->_errorMsg = "<p style='color: black'>Row couldn't be deleted. Unexpected error has occured</p>";
		return false;
	}
	
	protected function _checkCredentials(string $username, string $password)
	{
		$filterName = filter_var($username, FILTER_SANITIZE_STRING);
		$filterPass = filter_var($password, FILTER_SANITIZE_STRING);
		$stmt = $this->_db->prepare("SELECT * FROM event_user WHERE event_user_name = :name AND event_user_password = :password");
		$stmt->bindParam(':name',$filterName, PDO::PARAM_STR);
		$stmt->bindParam(':password',$filterPass, PDO::PARAM_STR);
		$stmt->execute();
		if($stmt->rowCount() == 1)
			{
				return true;
			}
		return false;
	}
	
	protected function _fetchUserTable()
	{
		$stmt = $this->_db->prepare("SELECT * FROM wdv341_event");
		$stmt->execute();
		if($stmt->rowCount() >= 1)
		{
			$styles = "style='color:white; border: 1px solid white; padding-left: 1rem; padding-right: 1rem;'";
			while ($row = $stmt->fetch(PDO::FETCH_ASSOC))
			{
				echo "<tr>";
					echo "<td $styles>" . $row['event_name'] . "</td>";
					echo "<td $styles>" . $row['event_description'] . "</td>";	
					echo "<td $styles>" . $row['event_presenter'] . "</td>";	
					echo "<td $styles>" . $row['event_date'] . "</td>";	
					echo "<td $styles><a href='updateEvent.php?eventID=" . $row['event_id'] . "'>Update</a></td>"; 
					echo "<td $styles><a href='deleteEvent.php?eventID=" . $row['event_id'] . "'>Delete</a></td>"; 		
				echo "</tr>";
			}
			return true;
		} else
		return false;
	}
	
	protected function _deleteUserRow()
	{
		try {
			$id = $_GET['eventID'];
			$filterID = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
			
			$stmt = $this->_db->prepare("DELETE FROM wdv341_event WHERE event_id = :id");
			$stmt->bindParam(':id', $filterID, PDO::PARAM_INT);
			$stmt->execute();
			if($stmt->rowCount() > 0)
			{
				return true;
			}
			else {
				return false;
			}
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}
	}
	
	protected function _updateUserRow()
	{
		try {
			$id = $_GET['eventID'];
			$set_presenter = $_SESSION['presenter_name'];
			$filterID = filter_var($id, FILTER_SANITIZE_NUMBER_INT);
			$filterName = filter_var($set_presenter, FILTER_SANITIZE_STRING);
			if($_SESSION['presenter_name'] == "")
			{
				$this->_error = true;
				$this->_errorMsg = "<p style='color:black'>Please enter a new presenter name to update!</p>";
				return false;
			}
			else {
				$stmt = $this->_db->prepare("UPDATE wdv341_event SET event_presenter = '$filterName' WHERE event_id = :id");
				$stmt->bindParam(':id', $filterID, PDO::PARAM_INT);
				$stmt->execute();
				if($stmt->rowCount() > 0)
				{
					$this->_successMsg = "<p style='color: black'>Row successfully updated to the database</p>";
					return true;
				}
				else {
					$this->_error = true;
					$this->_errorMsg = "<p style='color:black'>Something unexpected went wrong! Please try again.</p>";
					return false;
				}
			}
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
		}
	}
	
	public function getFirstName()
	{
		return $this->_user;
	}
	
	public function getSuccessMsg()
	{
		return $this->_successMsg;
	}
	
	public function getErrorMsg()
	{
		return $this->_errorMsg;
	}
}
?>
<?php
include 'dbconnect.php';
$test = new DatabaseConnect("root","","phphomework","localhost");
if($test)
{

  $params = array(
    ":eventName" => "Jeff",
    ":eventPass" => "23131"
  );
  if($test->dbPrepare("INSERT INTO event_user (event_user_name, event_user_password) VALUES (:eventName,:eventPass)"))
  {
    $test->bindParams($params);
    $test->executeQuery("INSERT");
  }

$test2 = new DatabaseConnect("root","","phphomework","localhost");
if($test2)
{
  if($test->dbPrepare("SELECT * FROM event_user"))
  {
    $rows = array(
      "0" => "event_user_id",
      "1" => "event_user_name",
      "2" => "event_user_password"
    );
    $test->executeQuery("SELECT");
    $test->setRows($rows);
    $test->displayTable();
  }
}

}
?>

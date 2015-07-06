<?php

###
# @name			Users Module
# @copyright	2015 by Viktor Hansson
###

if (!defined('LYCHEE')) exit('Error: Direct access is not allowed!');

class Users extends Module {

	private $database = null;

	public function __construct($database) {

		# Init vars
		$this->database = $database;

		return true;

	}

  public function get($username){
      
      # Check dependenices
      self::dependencies(isset($this->database));

      if($username !== ''){
        $query = Database::prepare($this->database, "SELECT name, role FROM ? WHERE name = '?'", array(LYCHEE_TABLE_USERS, $username));
      }
      else{
        $query = Database::prepare($this->database, "SELECT name, role FROM ?", array(LYCHEE_TABLE_USERS));
      }
      $result = $this->database->query($query);

      $data = [];
      while($row = $result->fetch_assoc()){
        $data[] = $row;
      }
      return json_encode($data);
  }
  public function addUser($username, $password, $role){
      
      # Check dependencies
      self::dependencies(isset($this->database));

      # Check if user already exists
      $query = Database::prepare($this->database, "SELECT * FROM ? WHERE name = '?'", array(LYCHEE_TABLE_USERS, $username));
      $result = $this->database->query($query);
      if($result->num_rows > 0){
          Log::warning($this->database, __METHOD__, __LINE__, "User: ". $username . " already exists");
          exit("User already exists");
      }
      
      # Hash password
      $pwhash = password_hash($password, PASSWORD_BCRYPT);

      # Insert in database
      # Do not prepare pwhash, since the escaping would
      # destroy it
      $query = Database::prepare($this->database, "INSERT INTO ? (name, pwhash, role) VALUES ('?', '". $pwhash ."', '?')", array(LYCHEE_TABLE_USERS, $username, $role));
      $result = $this->database->query($query);
      if(!$result){
          Log::error($this->database, __METHOD__, __LINE__, "Failed to create user: " . $database->error);
          return false;
      }
      return true;
  }
  
  public function deleteUser($username){

       # Check dependencies
      self::dependencies(isset($this->database));

      # Check if user exists
      $query = Database::prepare($this->database, "SELECT * FROM ? WHERE name = '?'", array(LYCHEE_TABLE_USERS, $username));
      $result = $this->database->query($query);
      if($result->num_rows === 0){
          Log::warning($this->database, __METHOD__, __LINE__, "User: ". $username . " does not exists");
          exit("User does not exists");
      }

      # Delete from database
      $query = Database::prepare($this->database, "DELETE FROM ? WHERE name = '?'", array(LYCHEE_TABLE_USERS, $username));
      $result = $this->database->query($query);
      if(!$result){
          Log::error($this->database, __METHOD__, __LINE__, "Failed to delete user: " . $database->error);
          return false;
      }
      return true; 
  }

  public function checkLogin($username, $password){

      # Check dependencies
      self::dependencies(isset($this->database));

      # Check credentials
      $query = Database::prepare($this->database, "SELECT * FROM ? WHERE name = '?'", array(LYCHEE_TABLE_USERS, $username));
      $result = $this->database->query($query);
      if($result->num_rows !== 1){
        Log::error($this->database, __METHOD__, __LINE__, "Multiple users with the same name exists...");
        exit("Failed to login");
      }
      $user = $result->fetch_object();

      # Check the password
      if (password_verify($password, $user->pwhash)){
        return array('role' => $user->role, 'userid' => $user->id);
      }
      return false; 
  }

  public function changePassword($username, $oldPassword, $newpassword, $newPwRepeat){

      # Check dependencies
      self::dependencies(isset($this->database));

      if($newpassword !== $newPwRepeat){
         return false;
      }

      # Check credentials
      $query = Database::prepare($this->database, "SELECT * FROM ? WHERE name = '?'", array(LYCHEE_TABLE_USERS, $username));
      $result = $this->database->query($query);
      if($result->num_rows !== 1){
        Log::error($this->database, __METHOD__, __LINE__, "Multiple users with the same name exists...");
        return false;
      }
      $user = $result->fetch_object();

      # Check the password
      if (password_verify($oldPassword, $user->pwhash)){

          $pwhash = password_hash($newpassword, PASSWORD_BCRYPT);

          # Contruct the update query 
          $query = Database::prepare($this->database, "UPDATE ? SET pwhash = '" . $pwhash . "' WHERE name = '?'", array(LYCHEE_TABLE_USERS, $username));
          $result = $this->database->query($query);
          if(!$query){
            Log::error($this->database, __METHOD__, __LINE__, "Failed to change password");
            return false;
          }

          return true;
      }
      return false; 
  }

  public function changePrivileges(){
      # TODO
  
  }

}

?>

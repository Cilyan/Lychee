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
      $query = Database::prepare($this->database, "INSERT INTO ? (name, pwhash, role) VALUES ('?', '". $pwhash ."', ?)", array(LYCHEE_TABLE_USERS, $username, $role));
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
        return true;
      }
      return false; 
  }

  public function changePassword($username, $oldPassword, $newpassword){

      # Check dependencies
      self::dependencies(isset($this->database));

      # Check credentials
      $query = Database::prepare($this->database, "SELECT * FROM ? WHERE name = '?'", array(LYCHEE_TABLE_USERS, $username));
      $result = $this->database->query($query);
      if($result->num_rows !== 1){
        Log::error($this->database, __METHOD__, __LINE__, "Multiple users with the same name exists...");
        exit("Failed to verify old password");
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

      }
      return true; 
  }

  public function changePrivileges(){
      # TODO
  
  }



/*
	public function setLogin($oldPassword = '', $username, $password) {

		# Check dependencies
		self::dependencies(isset($this->database));

		# Load settings
		$settings = $this->get();

		if ($oldPassword===$settings['password']||$settings['password']===crypt($oldPassword, $settings['password'])) {

			# Save username
			if ($this->setUsername($username)!==true) exit('Error: Updating username failed!');

			# Save password
			if ($this->setPassword($password)!==true) exit('Error: Updating password failed!');

			return true;

		}

		exit('Error: Current password entered incorrectly!');

	}

	private function setUsername($username) {

		# Check dependencies
		self::dependencies(isset($this->database));

		# Hash username
		$username = getHashedString($username);

		# Execute query
		# Do not prepare $username because it is hashed and save
		# Preparing (escaping) the username would destroy the hash
		$query	= Database::prepare($this->database, "UPDATE ? SET value = '$username' WHERE `key` = 'username'", array(LYCHEE_TABLE_SETTINGS));
		$result	= $this->database->query($query);

		if (!$result) {
			Log::error($this->database, __METHOD__, __LINE__, $this->database->error);
			return false;
		}
		return true;

	}

	private function setPassword($password) {

		# Check dependencies
		self::dependencies(isset($this->database));

		# Hash password
		$password = getHashedString($password);

		# Execute query
		# Do not prepare $password because it is hashed and save
		# Preparing (escaping) the password would destroy the hash
		$query	= Database::prepare($this->database, "UPDATE ? SET value = '$password' WHERE `key` = 'password'", array(LYCHEE_TABLE_SETTINGS));
		$result	= $this->database->query($query);

		if (!$result) {
			Log::error($this->database, __METHOD__, __LINE__, $this->database->error);
			return false;
		}
		return true;

	}
 */
}

?>

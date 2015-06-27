<?php

###
# @name     Update to unspecified version with multiuser support
# @copyright  2015 by Viktor Hansson
###

if (!defined('LYCHEE')) exit('Error: Direct access is not allowed!');

$version = '';

# Remove the username and password from the settings table
$query  = Database::prepare($database, "DELETE FROM ? WHERE key = '?'", array(LYCHEE_TABLE_SETTINGS, "username"));
$result = $database->query($query);
if (!$result) {
  Log::error($database, 'update_' . $version, __LINE__, 'Could not update database (' . $database->error . ')');
  return false;
}
$query  = Database::prepare($database, "DELETE FROM ? WHERE key = '?'", array(LYCHEE_TABLE_SETTINGS, "password"));
$result = $database->query($query);
if (!$result) {
  Log::error($database, 'update_' . $version, __LINE__, 'Could not update database (' . $database->error . ')');
  return false;
}

# Alter the engine of ablums to enable foreign keys
$query  = Database::prepare($database, "ALTER TABLE ? ENGINE = INNODB", array(LYCHEE_TABLE_ALBUMS));
$result = $database->query($query);
if (!$result) {
  Log::error($database, 'update_' . $version, __LINE__, 'Could not update database (' . $database->error . ')');
  return false;
}

#Add the user table
$file = @file_get_contents(__DIR__ . "users_table.sql");
$query = Database::prepare($database, $file, array(LYCHEE_TABLE_USERS));
$result = $database->query($query);
if (!$result){
  Log::error($database, 'update_' . $version, __LINE__, 'Could not add the users table (' . $database->error . ')');
  return false;
}

#Add the privileges table
$file = @file_get_contents(__DIR__ . "privileges_table.sql");
$query = Database::prepare($database, $file array(LYCHEE_TABLE_PRIVILEGES));
$result = $database->query($query);
if (!$result){
  Log::error($database, 'update_' . $version, __LINE__, 'Could not add the privileges table (' . $database->error . ')');
  return false;
}


#Add the privileges table connections
$file = @file_get_contents(__DIR__ . "privileges_table_connect.sql");
$query = Database::prepare($database, $file array(LYCHEE_TABLE_PRIVILEGES, LYCHEE_TABLE_ALBUMS, LYCHEE_TABLE_USERS));
$result = $database->query($query);
if (!$result){
  Log::error($database, 'update_' . $version, __LINE__, 'Could not add the privileges table (' . $database->error . ')');
  return false;
}

# Set version
if (Database::setVersion($database, $version)===false) return false;

?>

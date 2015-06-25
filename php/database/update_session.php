<?php

###
# @name			Update to unspecified version with session storage in cookie
# @copyright	2015 by Viktor Hansson
###

if (!defined('LYCHEE')) exit('Error: Direct access is not allowed!');

$version = '';

#Add the sessions table
$query = Database::prepare($database, "CREATE TABLE `lychee_sessions` (`value` varchar(40) DEFAULT NULL,`expires` int(11) DEFAULT NULL) ENGINE=InnoDB DEFAULT CHARSET=utf8;", array());
$result = $database->query($query);
if (!$result){
	Log::error($database, 'update_' . $version, __LINE__, 'Could not add the session table (' . $database->error . ')');
	return false;
}

# Add sessionLength to settings
$query	= Database::prepare($database, "SELECT `key` FROM `?` WHERE `key` = 'sessionLength' LIMIT 1", array(LYCHEE_TABLE_SETTINGS));
$result	= $database->query($query);
if ($result->num_rows===0) {
	$sessionLength	= 1440;
	$query		= Database::prepare($database, "INSERT INTO `?` (`key`, `value`) VALUES ('sessionLength', '?')", array(LYCHEE_TABLE_SETTINGS, $sessionLength));
	$result		= $database->query($query);
	if (!$result) {
		Log::error($database, 'update_' . $version, __LINE__, 'Could not update the database (' . $database->error . ')');
		return false;
	}
} else {
	$sessionLength		= 1440;
	$query			= Database::prepare($database, "UPDATE `?` SET `value` = '?' WHERE `key` = 'sessionLength' LIMIT 1", array(LYCHEE_TABLE_SETTINGS, $sessionLength));
	$result	= $database->query($query);
	if (!$result) {
		Log::error($database, 'update_' . $version, __LINE__, 'Could not add the sessionLength to settings (' . $database->error . ')');
		return false;
	}
}

# Set version
if (Database::setVersion($database, $version)===false) return false;

?>

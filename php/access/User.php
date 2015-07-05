<?php

###
# @name			User Access
# @copyright	2015 by Viktor Hansson
###

if (!defined('LYCHEE')) exit('Error: Direct access is not allowed!');
if (!defined('LYCHEE_ACCESS_USER')) exit('Error: You are not allowed to access this area!');

class User extends Access {

	public function check($fn) {

		switch ($fn) {

			# Album functions
			case 'Album::getAll':			$this->getAlbums(); break;
			case 'Album::get':				$this->getAlbum(); break;

			# Photo functions
			case 'Photo::get':				$this->getPhoto(); break;
			case 'Photo::setTitle':			$this->setPhotoTitle(); break;
			case 'Photo::setDescription':	$this->setPhotoDescription(); break;
			case 'Photo::setStar':			$this->setPhotoStar(); break;
			case 'Photo::setPublic':		$this->setPhotoPublic(); break;
			case 'Photo::setAlbum':			$this->setPhotoAlbum(); break;
			case 'Photo::setTags':			$this->setPhotoTags(); break;
			case 'Photo::duplicate':		$this->duplicatePhoto(); break;
			case 'Photo::delete':			$this->deletePhoto(); break;

			# Add functions
			case 'Photo::add':				$this->uploadImage(); break;
			case 'Video::add':				$this->uploadVideo(); break;
			case 'Import::url':				$this->importUrl(); break;
			case 'Import::server':			$this->importServer(); break;

			# Search functions
			case 'search':					$this->search(); break;

			# Session functions
			case 'Session::init':			$this->init(); break;
			case 'Session::login':			$this->login(); break;
			case 'Session::logout':			$this->logout(); break;

			# Settings functions
			case 'Settings::setLogin':		$this->setLogin(); break;
			case 'Settings::setSorting':	$this->setSorting(); break;

			# $_GET functions
			case 'Album::getArchive':		$this->getAlbumArchive(); break;
			case 'Photo::getArchive':		$this->getPhotoArchive(); break;

      # User functions
      case 'Users::changePassword':$this->changePassword(); break;

			# Error
			default:						exit('Error: Function not found! Please check the spelling of the called function.');
											return false; break;

		}

		return true;

	}

	# Album functions

	private function getAlbums() {

		$album = new Album($this->database, $this->plugins, $this->settings, null);
		echo json_encode($album->getAll(false));

	}

	private function getAlbum() {

		Module::dependencies(isset($_POST['albumID']));
		$album = new Album($this->database, $this->plugins, $this->settings, $_POST['albumID']);
		echo json_encode($album->get());

	}
	# Photo functions

	private function getPhoto() {

		Module::dependencies(isset($_POST['photoID'], $_POST['albumID']));
		$photo = new Photo($this->database, $this->plugins, null, $_POST['photoID']);
		echo json_encode($photo->get($_POST['albumID']));

	}

	private function setPhotoTitle() {

		Module::dependencies(isset($_POST['photoIDs'], $_POST['title']));
		$photo = new Photo($this->database, $this->plugins, null, $_POST['photoIDs']);
		echo $photo->setTitle($_POST['title']);

	}

	private function setPhotoDescription() {

		Module::dependencies(isset($_POST['photoID'], $_POST['description']));
		$photo = new Photo($this->database, $this->plugins, null, $_POST['photoID']);
		echo $photo->setDescription($_POST['description']);

	}

	private function setPhotoStar() {

		Module::dependencies(isset($_POST['photoIDs']));
		$photo = new Photo($this->database, $this->plugins, null, $_POST['photoIDs']);
		echo $photo->setStar();

	}

	private function setPhotoPublic() {

		Module::dependencies(isset($_POST['photoID']));
		$photo = new Photo($this->database, $this->plugins, null, $_POST['photoID']);
		echo $photo->setPublic();

	}

	private function setPhotoAlbum() {

		Module::dependencies(isset($_POST['photoIDs'], $_POST['albumID']));
		$photo = new Photo($this->database, $this->plugins, null, $_POST['photoIDs']);
		echo $photo->setAlbum($_POST['albumID']);

	}

	private function setPhotoTags() {

		Module::dependencies(isset($_POST['photoIDs'], $_POST['tags']));
		$photo = new Photo($this->database, $this->plugins, null, $_POST['photoIDs']);
		echo $photo->setTags($_POST['tags']);

	}

	private function duplicatePhoto() {

		Module::dependencies(isset($_POST['photoIDs']));
		$photo = new Photo($this->database, $this->plugins, null, $_POST['photoIDs']);
		echo $photo->duplicate();

	}

	private function deletePhoto() {

		Module::dependencies(isset($_POST['photoIDs']));
		$photo = new Photo($this->database, $this->plugins, null, $_POST['photoIDs']);
		echo $photo->delete();

	}

	# Add functions

	private function uploadImage() {

		Module::dependencies(isset($_FILES, $_POST['albumID'], $_POST['tags']));
		$photo = new Photo($this->database, $this->plugins, $this->settings, null);
		echo $photo->add($_FILES, $_POST['albumID'], '', $_POST['tags']);

	}

	private function uploadVideo() {

		Module::dependencies(isset($_FILES, $_POST['albumID'], $_POST['tags']));
		$video = new Video($this->database, $this->plugins, $this->settings, null);
		echo $video->add($_FILES, $_POST['albumID'], '', $_POST['tags']);

	}

	private function importUrl() {

		Module::dependencies(isset($_POST['url'], $_POST['albumID']));
		echo Import::url($_POST['url'], $_POST['albumID']);

	}

	# Search function

	private function search() {

		Module::dependencies(isset($_POST['term']));
		echo json_encode(search($this->database, $this->settings, $_POST['term']));

	}

	# Session functions

	private function init() {

		global $dbName;

		Module::dependencies(isset($_POST['version']));
		$session = new Session($this->database, $dbName, $this->plugins, $this->settings);
		echo json_encode($session->init($this->database, $dbName, false, $_POST['version']));

	}

	private function login() {

		global $dbName;

		Module::dependencies(isset($_POST['user'], $_POST['password']));
		$session = new Session($this->database, $dbName, $this->plugins, $this->settings);
		echo $session->login($_POST['user'], $_POST['password']);

	}

	private function logout() {

		global $dbName;

		$session = new Session($this->database, $dbName, $this->plugins, $this->settings);
		echo $session->logout();

	}

	# Settings functions

	private function setLogin() {

		Module::dependencies(isset($_POST['username'], $_POST['password']));
    $this->users = new Users($this->database);
    echo $this->users->addUser($_POST['username'], $_POST['password'], 'admin');

	}

	private function setSorting() {

		Module::dependencies(isset($_POST['typeAlbums'], $_POST['orderAlbums'], $_POST['typePhotos'], $_POST['orderPhotos']));
		$this->settings = new Settings($this->database);

		$sA = $this->settings->setSortingAlbums($_POST['typeAlbums'], $_POST['orderAlbums']);
		$sP = $this->settings->setSortingPhotos($_POST['typePhotos'], $_POST['orderPhotos']);

		if ($sA===true&&$sP===true)	echo true;
		else						echo false;

	}
	# Get functions

	private function getAlbumArchive() {

		Module::dependencies(isset($_GET['albumID']));
		$album = new Album($this->database, $this->plugins, $this->settings, $_GET['albumID']);
		$album->getArchive();

	}

	private function getPhotoArchive() {

		Module::dependencies(isset($_GET['photoID']));
		$photo = new Photo($this->database, $this->plugins, null, $_GET['photoID']);
		$photo->getArchive();

	}
  private function changePassword(){

      Module::dependencies(isset($_SESSION['username'], $_POST['oldPassword'], $_POST['newPassword'], $_POST['newPwRepeat'] ));
      $users = new Users($this->database);
      echo $users->changePassword( $_SESSION['username'], $_POST['oldPassword'], $_POST['newPassword'],$_POST['newPwRepeat']);

  }

}

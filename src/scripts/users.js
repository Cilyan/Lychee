/**
 * @description Does stuff to the users
 * @copyright 2015 by Viktor Hansson
 */

users = {}

users.addUser = function() {

	var action,
		msg = '';

	action = function(data) {
    console.log(data);

		var params,
			username = data.username,
			password = data.password;


		var role = $('.basicModal select#role').val();
	
		if (username.length<1) {
			basicModal.error('username');
			return false;
		}

		if (password.length<1) {
			basicModal.error('password');
			return false;
		}

		basicModal.close();

		params = {
			username,
			password,
      role
		}

		api.post('Users::addUser', params, function(data) {

			if (data!==true) {

				basicModal.show({
					body: '<p>Unable to create new user,</p>',
					buttons: {
						action: {
							title: 'Retry',
							fn: users.addUser
						},
						cancel: {
						 title: 'Cancel',
						 fn: basicModal.close
						}
					}
				});

			}
			else{
				users.manageUsers();
			}

		});

	}

	msg = `
			<p>
				Enter a username and password for the new user:
				<input data-name='username' class='text' type='text' placeholder='New Username' value=''>
				<input data-name='password' class='text' type='password' placeholder='New Password' value=''>
			</p>
			<p>
				Role:
				<span class="select">
					<select id='role'>
						<option value='admin'>Admin</option>
						<option value='user'>User</option>
					</select>
				</span>
			</p>
			`

	basicModal.show({
		body: msg,
		buttons: {
			action: {
				title: 'Create User',
				fn: action
			},
			cancel: {
				title: 'Cancel',
				fn: basicModal.close
			}
		}
	});

}

users.deleteUser = function(username){

	if (username.length<1) {
		return false;
	}

	params = {
		username,
	}

	api.post('Users::deleteUser', params, function(data) {

		console.log(data);
		if (data!==true) {

			basicModal.show({
				body: '<p>Unable to delete user!</p>',
				buttons: {
					action: {
						title: 'Close',
						fn: users.manageUsers
					}
				}
			});
		}
		else{
			users.manageUsers();
		}

	});


}

users.changePassword = function() {

	var action,
		msg = '';

	action = function(data) {

		console.log(data);
		var params,
			oldPassword = data.oldPassword,
			newPassword = data.newPassword,
			newPwRepeat = data.newPwRepeat;

		if (oldPassword.length<1) {
			basicModal.error('oldPassword');
			return false;
		}

		if (newPassword.length<1) {
			basicModal.error('newPassword');
			return false;
		}

		if (newPwRepeat.length<1) {
			basicModal.error('newPwRepeat');
			return false;
		}

		basicModal.close();

		params = {
			 oldPassword,
			 newPassword,
			 newPwRepeat
		}

		api.post('Users::changePassword', params, function(data) {

			if (data!==true) {

				basicModal.show({
					body: '<p>Unable to save new password. Please try again</p>',
					buttons: {
						action: {
							title: 'Retry',
							fn: users.changePassword
						},
						action: {
							title: 'Close',
							fn: basicModal.close
						}
					}
				});

			}

		});

	}

	msg = `
			<p>
				Enter your old password and the new password:
				<input data-name='oldPassword' class='text' type='password' placeholder='Old Password' value=''>
				<input data-name='newPassword' class='text' type='password' placeholder='New Password' value=''>
				<input data-name='newPwRepeat' class='text' type='password' placeholder='Repeat new password' value=''>
			</p>
			`

	basicModal.show({
		body: msg,
		buttons: {
			action: {
				title: 'Change Password',
				fn: action
			},
			cancel: {
				title: 'Cancel',
				fn: basicModal.close
			}
		}
	});

}


users.manageUsers = function(){

	api.post('Users::get', '',  function(data) {

		if (data!==true) {

			var userlist = JSON.parse(data);

			basicModal.show({
				body: build.usersModal('Users', userlist),
				buttons: {
					action : {
						title: 'Add User',
						fn: users.addUser
					},
					cancel: {
						title: 'Close',
						fn: basicModal.close
					}
				}
			});

		}

	});

}

users.manageUser = function(username, userid){

	var params = {'userid' : userid};

	api.post('Users::getPrivileges', params, function(data) {

		if (data!==true) {

			console.log(data.albums);

			basicModal.show({
				body: build.userModal(username, userid, JSON.parse(data)),
				buttons: {
					action : {
						title: 'Back',
						fn: users.manageUsers
					}
				}
			});

		}

	});

}

users.changePrivileges = function(userid, albumid, privilege, state){

    var state = state ? '1' : '0';
    var params = {'userid': userid, 'albumid': albumid, 'privilege' : privilege, 'state': state};

    api.post('Users::changePrivileges', params, function(data) {

        if (data!==true) {
            console.log("Failed to set privileges");
        }
        else{
          console.log("SEt privileges");
        }

    });

}



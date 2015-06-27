/**
 * @description Does stuff to the users
 * @copyright	2015 by Viktor Hansson
 */

users = {}

users.addUser = function() {

	var action,
		msg = '';

	action = function(data) {

		var params,
			username = data.username,
			password = data.password;

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
			password
		}

		api.post('Users::addUser', params, function(data) {

			if (data!==true) {
        console.log(data);

				basicModal.show({
					body: '<p>Unable to save login. Please try again with another username and password!</p>',
					buttons: {
						action: {
							title: 'Retry',
							fn: settings.createLogin
						}
					}
				});

			}

		});

	}

	msg =	`
			<p>
				Enter a username and password for the new user:
				<input data-name='username' class='text' type='text' placeholder='New Username' value=''>
				<input data-name='password' class='text' type='password' placeholder='New Password' value=''>
			</p>
			`

	basicModal.show({
		body: msg,
		buttons: {
			action: {
				title: 'Create Login',
				fn: action
			},
			cancel: {
				title: 'Cancel',
				fn: basicModal.close
			}
		}
	});

}

users.deleteUser = function() {

	var action,
		msg = '';

	action = function(data) {

		var params,
			username = data.username;

		if (username.length<1) {
			basicModal.error('username');
			return false;
		}

		basicModal.close();

		params = {
			username,
		}

		api.post('Users::deleteUser', params, function(data) {

			if (data!==true) {
        console.log(data);

				basicModal.show({
					body: '<p>Unable to save login. Please try again with another username and password!</p>',
					buttons: {
						action: {
							title: 'Retry',
							fn: settings.createLogin
						}
					}
				});

			}

		});

	}

	msg =	`
			<p>
				Enter a username and password for the new user:
				<input data-name='username' class='text' type='text' placeholder='User to delete' value=''>
			</p>
			`

	basicModal.show({
		body: msg,
		buttons: {
			action: {
				title: 'Delete User',
				fn: action
			},
			cancel: {
				title: 'Cancel',
				fn: basicModal.close
			}
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
        console.log(data);

				basicModal.show({
					body: '<p>Unable to save new password. Please try again</p>',
					buttons: {
						action: {
							title: 'Retry',
							fn: users.changePassword
						}
					}
				});

			}

		});

	}

	msg =	`
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



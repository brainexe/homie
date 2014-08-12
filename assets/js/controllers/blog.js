
App.ng.controller('BlogController', ['$scope', function($scope) {

	$scope.posts = {};
	$scope.users = {};
	$scope.current_user_id = null;
	$scope.active_user_id = null;

	$.get('/blog/', function(data) {
		$scope.posts = data.posts;
		$scope.users = data.users;
		$scope.current_user_id = data.current_user_id;
		$scope.active_user_id = data.active_user_id;
		$scope.$apply();
	});

	/**
	 * @param {String} text
	 * @param {Number} mood
	 */
	$scope.addPost = function(text, mood) {
		$.post('/blog/add/', {mood:mood, text:text}, function(data) {
			$scope.newMood = $scope.newText = '';
			$scope.posts[data[0]] = data[1];
			$scope.$apply();
		});
	};

	/**
	 * @param {Number} timestamp
	 */
	$scope.deletePost = function(timestamp) {
		if (!confirm('Remove this Post?')) {
			return false;
		}

		$.post('/blog/delete/{0}/'.format(timestamp), function() {
			delete $scope.posts[timestamp];
			$scope.$apply();
		});

		return false;
	};
}]);


App.ng.controller('BlogController', ['$scope', function($scope) {

	$scope.posts = {};
	$scope.post = {};
	$scope.users = {};
	$scope.current_user_id = null;
	$scope.active_user_id  = null;
    $scope.pending = false;

	$.get('/blog/', function(data) {
		$scope.posts = data.posts;
		$scope.users = data.users;
		$scope.current_user_id = data.current_user_id;
		$scope.active_user_id  = data.active_user_id;
		$scope.$apply();
	});

	/**
	 * @param {Object} post
	 */
	$scope.addPost = function(post) {
        $scope.pending = true;

        $.post('/blog/add/', post, function(data) {
            $scope.pending = false;
            post.text = '';
			post.mood = '';
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

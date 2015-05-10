
App.ng.controller('BlogController', ['$scope', function($scope) {

	$scope.posts = {};
	$scope.post = {};
	$scope.users = {};
	$scope.currentUserId = null;
	$scope.activeUserId  = null;
    $scope.pending = false;

	$.get('/blog/', function(data) {
		$scope.posts = data.posts;
		$scope.users = data.users;
		$scope.currentUserId = data.currentUserId;
		$scope.activeUserId  = data.activeUserId;
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

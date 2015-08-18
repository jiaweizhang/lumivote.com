var app = angular.module('myApp', ['ui.bootstrap']);
app.controller('myController', function($scope, $http, $modal) {

	$scope.submit = function() {
		$http.post("api/candidates", $scope.candidate).success(function(response) {
			$scope.names = response;
			$http.get("api/candidates").success(function(response) {
				$scope.candidates = response.candidates;
			});
		});
	}

	$scope.delete = function(ID) {
		$scope.deleteURL = "api/candidates?ID="+ ID;
		console.log($scope.deleteURL);
		$http.delete($scope.deleteURL).success(function(response) {
			$scope.names = response;
			$http.get("api/candidates").success(function(response) {
				$scope.candidates = response.candidates;
			});
		})

	}

	$scope.edit = function(ID, candidate) {
		console.log(ID);
		var modalInstance = $modal.open({
			animation: true,
			templateUrl: 'candidateModal.html',
			controller: 'candidateController',
			size: 'lg',
			resolve: {
				candidate: function() {
					return candidate;
				}
				
			}
		});
		modalInstance.result.then(function () {
      		//on modal close
      		$http.get("api/candidates").success(function(response) {
				$scope.candidates = response.candidates;
			});
    	})

	}


	$scope.init = function() {
		$http.get("api/candidates").success(function(response) {
			$scope.candidates = response.candidates;
		})
	}

	$scope.init();
});

app.controller('candidateController', function($http, $scope, $modalInstance, candidate) {
	$scope.candidate = candidate;

	$scope.putURL = "api/events?ID="+ candidate.ID;

	$scope.submit = function() {
		$http.post("api/candidates", $scope.candidate).success(function(response) {
			$scope.names = response;
			$http.get("api/candidates").success(function(response) {
				$scope.candidates = response.candidates;
			});
		});
	}

	$scope.submit = function() {
		$http.put($scope.putURL, $scope.candidate).success(function(response) {
			$modalInstance.close();
		});
	}

	$scope.ok = function () {
		$scope.submit();
  	};

  	$scope.cancel = function () {
    	$modalInstance.dismiss('cancel');
  	};
});
var app = angular.module('myApp', ['ui.bootstrap']);
app.controller('myController', function($scope, $http, $modal) {

	$scope.submit = function() {
		// alert($scope.user);
		$scope.event.date = $scope.date;
		console.log($scope.event.date);
		$http.post("../api/events", $scope.event).success(function(response) {
			$scope.names = response;
			$http.get("../api/events").success(function(response) {
				$scope.timeline = response.timeline;
			});
		});
	}

	$scope.delete = function(eventID) {
		$scope.deleteURL = "api/events?eventID="+ eventID;
		console.log($scope.deleteURL);
		$http.delete($scope.deleteURL).success(function(response) {
			$scope.names = response;
			$http.get("../api/events").success(function(response) {
				$scope.timeline = response.timeline;
			});
		})

	}

	$scope.edit = function(eventID, event) {
		console.log(eventID);
		var modalInstance = $modal.open({
			animation: true,
			templateUrl: 'eventModal.html',
			controller: 'eventController',
			size: 'lg',
			resolve: {
				eventID: function() {
					return eventID;
				},
				event: function() {
					return event;
				}
				
			}
		});
		modalInstance.result.then(function () {
      		// on modal close
      		$http.get("../api/events").success(function(response) {
				$scope.timeline = response.timeline;
			});
    	})

	}


	$scope.init = function() {
		$http.get("../api/events").success(function(response) {
			$scope.timeline = response.timeline;
		})
	}

	$scope.init();

	$scope.today = function() {
    	$scope.date = new Date();
  	};
  $scope.today();

  $scope.clear = function () {
    $scope.date = null;
  };

  $scope.open = function($event) {
    $scope.opened = true;
  };

  $scope.dateOptions = {
    formatYear: 'yy',
    startingDay: 1
  };

  $scope.format = "yyyy-MM-dd";

  $scope.getDayClass = function(date, mode) {
    if (mode === 'day') {
      var dayToCheck = new Date(date).setHours(0,0,0,0);

      for (var i=0;i<$scope.events.length;i++){
        var currentDay = new Date($scope.events[i].date).setHours(0,0,0,0);

        if (dayToCheck === currentDay) {
          return $scope.events[i].status;
        }
      }
    }

    return '';
  };


});

app.controller('eventController', function($http, $scope, $modalInstance, eventID, event) {
	console.log(eventID);
	$scope.eventID = eventID;
	$scope.event = event;

	$scope.putURL = "../api/events?eventID="+ eventID;
	console.log($scope.editURL);

	$scope.submit = function() {
		$http.put($scope.putURL, $scope.event).success(function(response) {
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
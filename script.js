var myApp = angular.module('myApp', ['ngRoute', 'ngSanitize']);

myApp.config(function($routeProvider) {
	$routeProvider

	.when('/', {
		templateUrl : 'pages/home.html',
		controller  : 'mainController'
	})

	.when('/timeline', {
		templateUrl : 'pages/timeline.html',
		controller  : 'timelineController'
	})

	.when('/candidates', {
		templateUrl : 'pages/candidates.html',
		controller  : 'candidateController'
	})

	.when('/legislators', {
		templateUrl : 'pages/legislators.html',
		controller  : 'legislatorController'
	})

	.when('/bills', {
		templateUrl : 'pages/bills.html',
		controller  : 'billsController'
	})

	.when('/votes', {
		templateUrl : 'pages/votes.html',
		controller  : 'votesController'
	})

	.when('/about', {
		templateUrl : 'pages/about.html',
		controller  : 'aboutController'
	})

	.when('/contact', {
		templateUrl : 'pages/contact.html',
		controller  : 'contactController'
	});
});

myApp.controller('mainController', function($scope) {
	// create a message to display in our view
	$scope.message = 'Everyone come and see how good I look!';
});

myApp.controller('aboutController', function($scope) {
	$scope.message = 'Look! I am an about page.';
});

myApp.controller('contactController', function($scope) {
	$scope.message = 'Contact us! JK. This is just a demo.';
});

myApp.controller('timelineController', function($scope, $http) {

	$scope.predicate = 'date';
	$scope.reverse = false;
	$scope.order = function(predicate) {
		$scope.reverse = ($scope.predicate === predicate) ? !$scope.reverse : false;
		$scope.predicate = predicate;
	};

	$scope.init = function() {
		$http.get("/app/timeline/api/events").success(function(response) {
			$scope.timeline = response.timeline;
		})
	}

	$scope.init();
});

myApp.controller('candidateController', function($scope) {
	$scope.message = 'Contact us! JK. This is just a demo.';
});

myApp.controller('legislatorController', function($scope, $http, $sce) {
	$http.get("https://congress.api.sunlightfoundation.com/legislators?per_page=all&apikey=33758a76204d4ac4aa866a5ef2e741a2")
	.success(function(response) {
		$scope.names = response.results;
		$scope.countCall = response.count;
		$scope.numTimes = Math.ceil($scope.countCall/50);
	});

	$scope.update = function() {
		alert($scope.stateInput);
		alert($scope.partyFilter.republican);
		$http.get("https://congress.api.sunlightfoundation.com/legislators?per_page=all&state=" +$scope.stateInput +"&apikey=33758a76204d4ac4aa866a5ef2e741a2")
		.success(function(response) { 
			$scope.names = response.results;
		});
	};

	$scope.theseParties = function(item) {
		if (($scope.partyFilter.republican==false) && ($scope.partyFilter.democrat==false) && ($scope.partyFilter.independent==false)) {
			return true;
		}
		if (($scope.partyFilter.republican==true) && (item.party=='R')) {
			return true;
		}
		if (($scope.partyFilter.democrat==true) && (item.party=='D')) {
			return true;
		}
		if (($scope.partyFilter.independent==true) && (item.party=='I')) {
			return true;
		}
		return false;
	};

	$scope.theseChambers = function(item) {
		if (($scope.chamberFilter.house==false)&&($scope.chamberFilter.senate==false)) {
			return true;
		}
		if (($scope.chamberFilter.house==true) && (item.chamber=='house')) {
			return true;
		}
		if (($scope.chamberFilter.senate==true) && (item.chamber=='senate')) {
			return true;
		}
		return false;
	};

	$scope.theseGenders = function(item) {
		if (($scope.genderFilter.male==false)&&($scope.genderFilter.female==false)) {
			return true;
		}
		if (($scope.genderFilter.male==true) && (item.gender=='M')) {
			return true;
		}
		if (($scope.genderFilter.female==true) && (item.gender=='F')) {
			return true;
		}
		return false;
	}

	$scope.theseStates = function(item) {
		if ($scope.stateFilter.length==0) {
			return true;
		}
		if (item.state.toLowerCase().indexOf(($scope.stateFilter).toLowerCase())==0) {
			return true;
		}
		if (item.state_name.toLowerCase().indexOf(($scope.stateFilter).toLowerCase())==0) {
			return true;
		}
		return false;
	}		

	$scope.predicate = 'last_name';
	$scope.reverse = false;
	$scope.order = function(predicate) {
		$scope.reverse = ($scope.predicate === predicate) ? !$scope.reverse : false;
		$scope.predicate = predicate;
	};	

	$scope.highlight = function(text, search) {
		if (!search) {
			return $sce.trustAsHtml(text);
		}
    //return $sce.trustAsHtml(text.replace(new RegExp(search, 'gi'), '<span class="highlightedText">$&</span>'));
    return $sce.trustAsHtml(unescape(escape(text).replace(new RegExp(escape(search), 'gi'), '<span class="highlightedText">$&</span>')));
};
});

myApp.controller('billsController', function($scope, $http) {
	$scope.pageNumber = 1;
	$http.get("https://congress.api.sunlightfoundation.com/bills?order=history.enacted_at&per_page=20&page=1&apikey=33758a76204d4ac4aa866a5ef2e741a2")
	.success(function(response) {
		$scope.bills = response.results;
	});



	$scope.addFifty = function() {
		$scope.pageNumber = $scope.pageNumber + 1;
		$http.get("https://congress.api.sunlightfoundation.com/bills?order=history.enacted_at&per_page=20&page="+$scope.pageNumber+"&apikey=33758a76204d4ac4aa866a5ef2e741a2")
		.success(function(response) {
			$scope.newBills = response.results;
			$scope.bills = $scope.bills.concat($scope.newBills);
		});
	}

});

myApp.controller('votesController', function($scope, $http) {
	$scope.pageNumber = 1;
	$http.get("https://congress.api.sunlightfoundation.com/votes?fields=bill,voted_at,vote_type,result,url,breakdown.total&order=voted_at&per_page=20&page=1&apikey=33758a76204d4ac4aa866a5ef2e741a2")
	.success(function(response) {
		$scope.votes = response.results;
	});



	$scope.addFifty = function() {
		$scope.pageNumber = $scope.pageNumber + 1;
		$http.get("https://congress.api.sunlightfoundation.com/votes?fields=bill,voted_at,vote_type,result,url,breakdown.total&order=voted_at&per_page=20&page="+$scope.pageNumber+"&apikey=33758a76204d4ac4aa866a5ef2e741a2")
		.success(function(response) {
			$scope.newVotes = response.results;
			$scope.votes = $scope.votes.concat($scope.newVotes);
		});
	}

});
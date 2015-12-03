var myApp = angular
    .module('myApp', ['ngRoute', 'ngSanitize', 'ui.bootstrap', 'ngLoadingSpinner']);

myApp.config(function ($routeProvider) {
    $routeProvider

        .when('/', {
            templateUrl: 'pages/home.html',
            controller: 'mainController'
        })

        .when('/timeline', {
            templateUrl: 'pages/timeline.html',
            controller: 'timelineController'
        })

        .when('/candidates', {
            templateUrl: 'pages/candidates.html',
            controller: 'candidateController'
        })

        .when('/legislators', {
            templateUrl: 'pages/legislators.html',
            controller: 'legislatorController'
        })

        .when('/bills', {
            templateUrl: 'pages/bills.html',
            controller: 'billsController'
        })

        .when('/votes', {
            templateUrl: 'pages/votes.html',
            controller: 'votesController'
        })

        .when('/about', {
            templateUrl: 'pages/about.html',
            controller: 'aboutController'
        });
});

myApp.controller('mainController', function ($scope) {

});

myApp.controller('aboutController', function ($scope) {

});

myApp.controller('timelineController', function ($scope, $http) {

    $scope.findDate = function (date) {
        var newDate = new Date(date);
        var d = newDate.toDateString().split(" ");
        return d[0] + ", " + d[1] + " " + d[2] + ", '" + d[3].substr(2);
    }

    $scope.findTime = function (time) {
        if (time == "00:00:00") {
            return " ";
        }
        return time;
    }

    $scope.predicate = 'date';
    $scope.reverse = false;
    $scope.order = function (predicate) {
        $scope.reverse = ($scope.predicate === predicate) ? !$scope.reverse
            : false;
        $scope.predicate = predicate;
    };

    $scope.init = function () {
        $http.get("http://lumivote.com/api/events").success(function (response) {
            $scope.timeline = response.timeline;
        })
    }

    $scope.init();
});

myApp.controller('candidateController', function ($scope, $http, $modal, $log) {

    $scope.open = function (candidate) {

        var modalInstance = $modal.open({
            animation: $scope.animationsEnabled,
            templateUrl: 'candidateContent.html',
            controller: 'candidateModalController',
            size: 'lg',
            resolve: {
                candidate: function () {
                    return candidate;
                }
            }
        });

        modalInstance.result.then(function (selectedItem) {
            $scope.selected = selectedItem;
        }, function () {
        });
    };

    $scope.findName = function (candidate) {
        if (candidate.nickName.length != 0) {
            return candidate.nickName + " " + candidate.lName;
        }
        return candidate.fName + " " + candidate.lName;
    }

    $scope.findDate = function (date) {
        if (date == "0000-00-00") {
            return " ";
        }
        var newDate = new Date(date);
        var d = newDate.toDateString().split(" ");
        return d[0] + ", " + d[1] + " " + d[2] + ", '" + d[3].substr(2);
    }

    $scope.init = function () {
        $http.get("http://lumivote.com/api/candidates?party=democratic").success(
            function (response) {
                $scope.democrats = response.candidates;
            })
        $http.get("http://lumivote.com/api/candidates?party=republican").success(
            function (response) {
                $scope.republicans = response.candidates;
            })
    }

    $scope.init();
});

myApp.controller('candidateModalController', function ($scope, $modalInstance,
                                                       candidate) {
    $scope.candidate = candidate;

    $scope.findName = function (candidate) {
        return candidate.fName + " " + candidate.mName + " " + candidate.lName;
    }

    $scope.findDate = function (date) {
        if (date == "0000-00-00") {
            return " ";
        }
        var newDate = new Date(date);
        var d = newDate.toDateString().split(" ");
        return d[0] + ", " + d[1] + " " + d[2] + ", '" + d[3].substr(2);
    }

    $scope.ok = function () {
        $modalInstance.close($scope.selected.item);
    };

    $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
    };
});

myApp
    .controller(
        'legislatorController',
        function ($scope, $http, $sce) {
            $http
                .get(
                    "https://congress.api.sunlightfoundation.com/legislators?per_page=all&apikey=33758a76204d4ac4aa866a5ef2e741a2")
                .success(
                    function (response) {
                        $scope.names = response.results;
                        $scope.countCall = response.count;
                        $scope.numTimes = Math
                            .ceil($scope.countCall / 50);
                    });

            $scope.update = function () {
                alert($scope.stateInput);
                alert($scope.partyFilter.republican);
                $http
                    .get(
                        "https://congress.api.sunlightfoundation.com/legislators?per_page=all&state="
                        + $scope.stateInput
                        + "&apikey=33758a76204d4ac4aa866a5ef2e741a2")
                    .success(function (response) {
                        $scope.names = response.results;
                    });
            };

            $scope.theseParties = function (item) {
                if (($scope.partyFilter.republican == false)
                    && ($scope.partyFilter.democrat == false)
                    && ($scope.partyFilter.independent == false)) {
                    return true;
                }
                if (($scope.partyFilter.republican == true)
                    && (item.party == 'R')) {
                    return true;
                }
                if (($scope.partyFilter.democrat == true)
                    && (item.party == 'D')) {
                    return true;
                }
                if (($scope.partyFilter.independent == true)
                    && (item.party == 'I')) {
                    return true;
                }
                return false;
            };

            $scope.theseChambers = function (item) {
                if (($scope.chamberFilter.house == false)
                    && ($scope.chamberFilter.senate == false)) {
                    return true;
                }
                if (($scope.chamberFilter.house == true)
                    && (item.chamber == 'house')) {
                    return true;
                }
                if (($scope.chamberFilter.senate == true)
                    && (item.chamber == 'senate')) {
                    return true;
                }
                return false;
            };

            $scope.theseGenders = function (item) {
                if (($scope.genderFilter.male == false)
                    && ($scope.genderFilter.female == false)) {
                    return true;
                }
                if (($scope.genderFilter.male == true)
                    && (item.gender == 'M')) {
                    return true;
                }
                if (($scope.genderFilter.female == true)
                    && (item.gender == 'F')) {
                    return true;
                }
                return false;
            }

            $scope.theseStates = function (item) {
                if ($scope.stateFilter.length == 0) {
                    return true;
                }
                if (item.state.toLowerCase().indexOf(
                        ($scope.stateFilter).toLowerCase()) == 0) {
                    return true;
                }
                if (item.state_name.toLowerCase().indexOf(
                        ($scope.stateFilter).toLowerCase()) == 0) {
                    return true;
                }
                return false;
            }

            $scope.findChamber = function (chamber) {
                if (chamber == "house") {
                    return "House";
                } else {
                    return "Senate";
                }
            }

            $scope.predicate = 'last_name';
            $scope.reverse = false;
            $scope.order = function (predicate) {
                $scope.reverse = ($scope.predicate === predicate) ? !$scope.reverse
                    : false;
                $scope.predicate = predicate;
            };

            $scope.highlight = function (text, search) {
                if (!search) {
                    return $sce.trustAsHtml(text);
                }
                // return $sce.trustAsHtml(text.replace(new
                // RegExp(search, 'gi'), '<span
                // class="highlightedText">$&</span>'));
                return $sce.trustAsHtml(unescape(escape(text).replace(
                    new RegExp(escape(search), 'gi'),
                    '<span class="highlightedText">$&</span>')));
            };
        });

myApp
    .controller(
        'billsController',
        function ($scope, $http) {
            $scope.pageNumber = 1;
            $http
                .get(
                    "https://congress.api.sunlightfoundation.com/bills?order=history.enacted_at&per_page=50&page=1&apikey=33758a76204d4ac4aa866a5ef2e741a2")
                .success(function (response) {
                    $scope.bills = response.results;
                });

            $scope.findTitle = function (bill) {
                if (bill.popular_title != null) {
                    return bill.popular_title;
                } else if (bill.short_title != null) {
                    return bill.short_title;
                }
                return bill.official_title;
            }

            $scope.findBillType = function (billType) {
                if (billType == "hr") {
                    return "H. R.";
                } else if (billType == "hres") {
                    return "H. Res.";
                } else if (billType == "hjres") {
                    return "H. J. Res.";
                } else if (billType == "hconres") {
                    return "H. Con. Res.";
                } else if (billType == "s") {
                    return "S.";
                } else if (billType == "sres") {
                    return "S. Res.";
                } else if (billType == "sjres") {
                    return "S. J. Res.";
                } else if (billType == "sconres") {
                    return "S. Con. Res";
                } else {
                    return billType;
                }
            }

            $scope.findChamber = function (chamber) {
                if (chamber == "house") {
                    return "House";
                } else {
                    return "Senate";
                }
            }

            $scope.findDate = function (date) {
                var newDate = new Date(date);
                var d = newDate.toDateString().split(" ");
                return d[0] + ", " + d[1] + " " + d[2] + ", '"
                    + d[3].substr(2);
            }

            $scope.addFifty = function () {
                $scope.pageNumber = $scope.pageNumber + 1;
                $http
                    .get(
                        "https://congress.api.sunlightfoundation.com/bills?order=history.enacted_at&per_page=50&page="
                        + $scope.pageNumber
                        + "&apikey=33758a76204d4ac4aa866a5ef2e741a2")
                    .success(
                        function (response) {
                            $scope.newBills = response.results;
                            $scope.bills = $scope.bills
                                .concat($scope.newBills);
                        });
            }

        });

myApp
    .controller(
        'votesController',
        function ($scope, $http) {
            $scope.pageNumber = 1;
            $http
                .get(
                    "https://congress.api.sunlightfoundation.com/votes?fields=bill,voted_at,vote_type,result,url,breakdown.total&order=voted_at&per_page=50&page=1&apikey=33758a76204d4ac4aa866a5ef2e741a2")
                .success(function (response) {
                    $scope.votes = response.results;
                });

            $scope.findTitle = function (vote) {
                if (vote.bill == null) {
                    return "No Title Found."
                }
                if (vote.bill.popular_title != null) {
                    return vote.bill.popular_title;
                } else if (vote.bill.short_title != null) {
                    return vote.bill.short_title;
                }
                return vote.bill.official_title;
            }

            $scope.findDate = function (date) {
                var newDate = new Date(date);
                var d = newDate.toDateString().split(" ");
                return d[0] + ", " + d[1] + " " + d[2] + ", '"
                    + d[3].substr(2);
            }

            $scope.findVoteType = function (voteType) {
                return voteType.charAt(0).toUpperCase()
                    + voteType.slice(1);
            }

            $scope.addFifty = function () {
                $scope.pageNumber = $scope.pageNumber + 1;
                $http
                    .get(
                        "https://congress.api.sunlightfoundation.com/votes?fields=bill,voted_at,vote_type,result,url,breakdown.total&order=voted_at&per_page=50&page="
                        + $scope.pageNumber
                        + "&apikey=33758a76204d4ac4aa866a5ef2e741a2")
                    .success(
                        function (response) {
                            $scope.newVotes = response.results;
                            $scope.votes = $scope.votes
                                .concat($scope.newVotes);
                        });
            }

        });
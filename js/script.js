var myApp = angular
    .module('myApp', ['ngRoute', 'ngSanitize', 'ngCookies', 'ui.bootstrap', 'ngLoadingSpinner']);

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
        })

        .
        when('/lumitrivia', {
            templateUrl: 'pages/lumitrivia.html',
            controller: 'lumiController'
        })

        .
        when('/login', {
            templateUrl: 'pages/login.html',
            controller: 'loginController'
        });
});

myApp.run(function ($rootScope, $cookies) {
    $rootScope.checkCookies = function () {
        var preval = $cookies.get('username');
        if (preval == null) {
            //$rootScope.loggedIn = false;
            console.log("cookie is not found");
            return false;
        } else {
            console.log("cookie is found: " + preval);
            return true;
        }
    };

});

myApp.run(function ($rootScope, $location) {

    // register listener to watch route changes
    $rootScope.$on("$routeChangeStart", function (event, next, current) {
        console.log("change route");
        if ($rootScope.checkCookies() == false) {
            console.log($location.path());
            if ($location.path() == '/lumitrivia') {
                console.log("Switching to login");
                $location.path("/login");
            }
            //if ($location.path() == '/login') {
            //    $location.path("/lumitrivia");
            //}
        }
    });
});

myApp.controller('mainController', function ($scope) {

});

myApp.controller('lumiController', function ($scope, $http, $cookies, $location, $rootScope) {

    $scope.logOut = function(){
        $cookies.remove("username");
        console.log("removed cookie");
        $location.path("/login");
    }

    $scope.next = function() {
        $scope.init();
    }

    $scope.submit = function(iscorrect){
        $scope.answered = true;
        if (iscorrect==1) {
            console.log("answered correctly");
            $scope.responseMessage = "Correct!"
        } else {
            console.log("answered incorrectly");
            $scope.responseMessage = "Incorrect!"
        }

        var username = $scope.username;
        var dataObj = {
            username : $scope.username,
            qid : $scope.question.qid,
            iscorrect : iscorrect
        };
        var res = $http.post('http://lumivote.com/api/lumitrivia/usersubmit', dataObj);
        res.success(function(data, status, headers, config) {
            console.log(data);
        });
        res.error(function(data, status, headers, config) {
            alert( "failure message: " + JSON.stringify({data: data}));
            console.log(data);
        });
        console.log("sent post request to submit response");
    }

    $scope.init = function () {
        console.log("reinitializing");
        $scope.responseMessage = "";
        $scope.answered = false;
        $scope.username = $cookies.get("username");
        console.log("username for this session is: "+$scope.username);


        // post for scoreboard
        $http.get("http://lumivote.com/api/lumitrivia/usersubmit/"+$scope.username).success(
            function (response) {
                $scope.score = response;
                console.log($scope.score);
            })

        $http.get("http://lumivote.com/api/lumitrivia/question").success(
            function (response) {
                $scope.question = response;
                console.log($scope.question);
            })
    }

    $scope.message = "some content goes inimessage";

    $scope.init();
});

myApp.controller('loginController', function ($scope, $http, $location, $cookies) {

    $scope.sendRequest = function() {
        var username = $scope.username;
        var dataObj = {
            username : $scope.username,
            password : $scope.password
        };
        var res = $http.post('http://lumivote.com/api/login', dataObj);
        res.success(function(data, status, headers, config) {
            $scope.message = data;
            console.log(data);
            if ($scope.message.data == 1) {
                // valid username
                console.log('valid username');
                $cookies.put('username', username);
                console.log("put username: " +username);
                $location.path("/lumitrivia");
            } else if ($scope.message.data == 0) {
                // invalid username
                console.log('invalid username');
                $scope.usernameError = "Invalid Username. Please try again.";
                alert("Username is invalid");
            } else {
                // error
                console.log('error logging in');
            }
        });
        res.error(function(data, status, headers, config) {
            alert( "failure message: " + JSON.stringify({data: data}));
            console.log(data);
        });
        // Making the fields empty
        //
        /*$cookies.put('username', $scope.username);
        console.log("put username: "+$scope.username);
        $location.path("/lumitrivia");*/
        $scope.username='';
        $scope.password='';
    }

    $scope.submit = function() {
        //console.log("received submit");
        $scope.sendRequest();
    }

    $scope.init = function() {

    }
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
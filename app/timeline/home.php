<!DOCTYPE html>
<html >
<head>
	<script src= "http://ajax.googleapis.com/ajax/libs/angularjs/1.4.3/angular.js"></script>
	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

	<script src="//angular-ui.github.io/bootstrap/ui-bootstrap-tpls-0.13.2.js"></script>
	<script src="js/myApp.js"></script>
</head>
<body ng-app="myApp" ng-controller="myController">
	<script type="text/ng-template" id="eventModal.html">
			<div class="modal-header">
				<h3 class="modal-title">
					Edit Event 
					<div class="btn-group pull-right">
						<button class="btn btn-primary" ng-click="ok()">Ok</button>
						<button class="btn btn-primary" ng-click="cancel()">Cancel</button>
					</div>
				</h3>
			</div>
			<div class="modal-body">
			<h2>Add Event</h2>
				<form role="form">
					<div class="form-group">
						<label>Name</label>
						<input type="text" class="form-control" ng-model="event.name">
					</div>
					<div class="form-group">
						<label>Date</label>
						<input type="text" class="form-control" ng-model="event.date">
					</div>
					<div class="form-group">
						<label>Time</label>
						<input type="text" class="form-control" ng-model="event.time">
					</div>
					<div class="form-group">
						<label>Party</label>
						<input type="text" class="form-control" ng-model="event.party">
					</div>
					<div class="form-group">
						<label>City</label>
						<input type="text" class="form-control" ng-model="event.city">
					</div>
					<div class="form-group">
						<label>State</label>
						<input type="text" class="form-control" ng-model="event.state">
					</div>
					<div class="form-group">
						<label>Type</label>
						<input type="text" class="form-control" ng-model="event.type">
					</div>
					<div class="form-group">
						<label>Description</label>
						<input type="text" class="form-control" ng-model="event.description">
					</div>
					<div class="form-group">
						<button type="submit" class="btn btn-default" ng-click="submit()">Submit</button>
					</div>
				</form>
			</div>
			<div class="modal-footer">
				<div class="btn-group pull-right">
						<button class="btn btn-primary" ng-click="ok()">Ok</button>
						<button class="btn btn-primary" ng-click="cancel()">Cancel</button>
					</div>
			</div>
		</script>

	<div class="container">
		<div class="jumbotron">
			<h1>Election Timeline Database</h1>
			<p>Add and remove timeline info from database.</p>
		</div>
		<div class="row">
			<div class="col-sm-6">
				<h2>Add Event</h2>
				<form role="form">
					<div class="form-group">
						<label>Name</label>
						<input type="text" class="form-control" ng-model="event.name" ng-init="event.name=''">
					</div>
					<div>
						<label>Date</label>
						<input type="text" class="form-control" datepicker-popup="{{format}}" ng-model="date" is-open="opened" datepicker-options="dateOptions" close-text="Close" />
              <span class="input-group-btn">
                <button type="button" class="btn btn-default" ng-click="open($event)"><i class="glyphicon glyphicon-calendar"></i></button>
              </span>
					</div>
					<div class="form-group">
						<label>Time</label>
						<input type="text" class="form-control" ng-model="event.time" ng-init="event.time=''">
					</div>
					<div class="form-group">
						<label>Party</label>
						<input type="text" class="form-control" ng-model="event.party" ng-init="event.party=''">
					</div>
					<div class="form-group">
						<label>City</label>
						<input type="text" class="form-control" ng-model="event.city" ng-init="event.city=''">
					</div>
					<div class="form-group">
						<label>State</label>
						<input type="text" class="form-control" ng-model="event.state" ng-init="event.state=''">
					</div>
					<div class="form-group">
						<label>Type</label>
						<input type="text" class="form-control" ng-model="event.type" ng-init="event.type=''">
					</div>
					<div class="form-group">
						<label>Description</label>
						<input type="text" class="form-control" ng-model="event.description" ng-init="event.description=''">
					</div>
					<div class="form-group">
						<button type="submit" class="btn btn-default" ng-click="submit()">Submit</button>
					</div>
				</form>
			</div>
			<div class="col-sm-6">

				<h2>Status</h2>
				<div ng-bind="names">Bananas</div>

			</div>
		</div>

		<table class="table table-hover">
			<thead>
				<tr>
					<th>Name</th>
					<th>Date</th>
					<th>Time</th>
					<th>Party</th>
					<th>City</th>
					<th>State</th>
					<th>Type</th>
					<th>Description</th>
					<th>Edit</th>
					<th>Delete</th>
				</tr>
				</thead>
				<tr ng-repeat="x in timeline">
					<td>{{ x.name}}</td>
					<td>{{ x.date }}</td>
					<td>{{ x.time }}</td>
					<td>{{ x.party }}</td>
					<td>{{ x.city }}</td>
					<td>{{ x.state }}</td>
					<td>{{ x.type }}</td>
					<td>{{ x.description }}</td>
					<td>
						<button type="button" class="btn btn-info" ng-click="edit(x.eventID,x)">Edit</button>
					</td>
					<td>
						<button type="button" class="btn btn-danger" ng-click="delete(x.eventID)">Delete</button>
					</td>
					<!--<th>
						<button type="button" class="btn btn-default btn-sm" ng-click="openByID(x.userID)">
							Open 
						</button>
					</th>
					<th>
						<button type="button" class="btn btn-default btn-sm" ng-click="deleteByID(x.userID)">
							<span class="glyphicon glyphicon-remove"></span> Delete 
						</button>
					</th>-->
				</tr>
			</table>



		</div>

	</body>
	</html>

<?php
require_once 'php/db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
  echo '<script type="text/javascript">';
  echo 'window.location.href = "login.html";</script>';
}
else{
  $user = $_SESSION['userID'];
  
}
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0 text-dark">Dashboard</h1>
			</div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
	<div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-4">
                        <div class="form-group">
                            <label>Start Date:</label>
                            <div class="input-group date" id="startDatePicker" data-target-input="nearest">
                                <input type="text" class="form-control datetimepicker-input" data-target="#startDatePicker" id="startDate"/>
                                <div class="input-group-append" data-target="#startDatePicker" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label>End Date:</label>
                            <div class="input-group date" id="endDatePicker" data-target-input="nearest">
                                <input type="text" class="form-control datetimepicker-input" data-target="#endDatePicker" id="endDate"/>
                                <div class="input-group-append" data-target="#endDatePicker" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="form-group">
                            <label style="visibility:hidden">Search:</label>
                            <button type="button" class="btn btn-block bg-gradient-warning btn-sm" id="filterSearch">
                                <i class="fas fa-search"></i>
                                Search
                            </button>
                        </div>
                    </div>
                    
                </div>

                <div class="row">
                    <!-- Donut Chart -->
                    <div class="col-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0 font-weight-bold">Total Stampings</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="stampChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0 font-weight-bold">Total Other Validations</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="otherChart"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0 font-weight-bold">Total Inhouse Validations</h5>
                            </div>
                            <div class="card-body">
                                <canvas id="inhouseChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /.container-fluid -->
</section><!-- /.content -->


<script>

let stampChart, otherChart, inhouseChart;

$(function () {
    const today = new Date();
    const tomorrow = new Date(today);
    const yesterday = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    yesterday.setDate(tomorrow.getDate() - 7);
    const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1); // First day of the current month
    const endOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0); // Last day of the current month

    $('#startDatePicker').datetimepicker({
        icons: { time: 'far fa-calendar' },
        format: 'DD/MM/YYYY',
        defaultDate: startOfMonth
    });

    $('#endDatePicker').datetimepicker({
        icons: { time: 'far fa-calendar' },
        format: 'DD/MM/YYYY',
        defaultDate: endOfMonth
    });

    var startDateValue = $('#startDate').val();
    var endDateValue = $('#endDate').val();

    if (startDateValue && endDateValue){
        $.post('php/getDashboard.php', {startDate: startDateValue, endDate: endDateValue}, function(data){
            var obj = JSON.parse(data);
            
            if(obj.status === 'success'){
                createCharts(obj.message);
            }
            else if(obj.status === 'failed'){
                toastr["error"](obj.message, "Failed:");
            }
            else{
                toastr["error"]("Something wrong when geting data", "Failed:");
            }
        });
    }
    
    $('#filterSearch').on('click', function(){
        var startDateValue = $('#startDate').val();
        var endDateValue = $('#endDate').val();

        if (startDateValue && endDateValue){
            $.post('php/getDashboard.php', {startDate: startDateValue, endDate: endDateValue}, function(data){
                var obj = JSON.parse(data);
                
                if(obj.status === 'success'){
                    createCharts(obj.message);
                }
                else if(obj.status === 'failed'){
                    toastr["error"](obj.message, "Failed:");
                }
                else{
                    toastr["error"]("Something wrong when geting data", "Failed:");
                }
            });
        }    
    });    
});

function createCharts(data) {
    // Destroy existing charts if they exist
    if (stampChart) stampChart.destroy();
    if (otherChart) otherChart.destroy();
    if (inhouseChart) inhouseChart.destroy();

    // Stamping Chart
    if (data.stamping) {
        const stampLabels = Object.keys(data.stamping);
        const stampData = Object.values(data.stamping);
        
        stampChart = new Chart($('#stampChart'), {
            type: 'doughnut',
            data: {
                labels: stampLabels,
                datasets: [{
                    data: stampData,
                    backgroundColor: ['#F7B731', '#00A8FF', '#4B7BEC', '#26de81', '#FF6B6B'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.label || '';
                                var value = context.parsed;
                                return label + ': ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    // Other Validations Chart
    if (data.other) {
        const otherLabels = Object.keys(data.other);
        const otherData = Object.values(data.other);
        
        otherChart = new Chart($('#otherChart'), {
            type: 'doughnut',
            data: {
                labels: otherLabels,
                datasets: [{
                    data: otherData,
                    backgroundColor: ['#F7B731', '#00A8FF', '#4B7BEC', '#26de81', '#FF6B6B'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.label || '';
                                var value = context.parsed;
                                return label + ': ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    // Inhouse Validations Chart
    if (data.inhouse) {
        const inhouseLabels = Object.keys(data.inhouse);
        const inhouseData = Object.values(data.inhouse);
        
        inhouseChart = new Chart($('#inhouseChart'), {
            type: 'doughnut',
            data: {
                labels: inhouseLabels,
                datasets: [{
                    data: inhouseData,
                    backgroundColor: ['#F7B731', '#00A8FF', '#4B7BEC', '#26de81', '#FF6B6B'],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.label || '';
                                var value = context.parsed;
                                return label + ': ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
}

</script>
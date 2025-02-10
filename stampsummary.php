<?php
require_once 'php/db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
    echo '<script type="text/javascript">';
    echo 'window.location.href = "login.php";</script>';
}
else{
    $_SESSION['page']='stampsummary';
}
?>

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Stamping Dashboard</h1>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<div class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12">
        <div class="card">
          <div class="card-header search-filter">
            <div class="d-flex justify-content-between align-items-center">
              <h5 class="card-title mb-0 font-weight-bold">Search Filters</h5>
              <button class="btn btn-link btn-sm p-0" type="button" data-toggle="collapse" data-target="#searchFilters" aria-expanded="true" aria-controls="searchFilters">
                <i class="fa fa-chevron-up" id="toggleIcon"></i>
              </button>
            </div>
          </div>

          <div class="collapse" id="searchFilters">
            <div class="card-body">
              <div class="row">
              <div class="form-group col-4">
                  <label>From Date:</label>
                  <div class="input-group date" id="fromDatePicker" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input" data-target="#fromDatePicker" id="fromDate"/>
                    <div class="input-group-append" data-target="#fromDatePicker" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
                </div>

                <div class="form-group col-4">
                  <label>To Date:</label>
                  <div class="input-group date" id="toDatePicker" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input" data-target="#toDatePicker" id="toDate"/>
                    <div class="input-group-append" data-target="#toDatePicker" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
                </div>
                <div class="col-9"></div>
                <div class="col-3">
                  <button type="button" class="btn btn-block bg-gradient-warning btn-sm"  id="filterSearch">
                    <i class="fas fa-search"></i>
                    Search
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <table id="dashboardTable" class="table table-bordered table-striped display" style="text-align:center">
                <thead>
                    <tr>
                        <th colspan="3">Metrology Cooperation</th>
                        <th colspan="3">DE Metrology Cooperation</th>
                    </tr>
                    <tr style="background-color: rgb(1, 162, 226);">
                        <th>Pending Job</th>
                        <th>Complete Job</th>
                        <th>Cancel Job</th>
                        <th>Pending Job</th>
                        <th>Complete Job</th>
                        <th>Cancel Job</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <table id="validatorTable" class="table table-bordered table-striped display">
                <thead>
                    <tr><th id="validatorHeader">Validator Ranking Count</th></tr>
                </thead>
            </table>
        </div>
    </div>
  </div>
</div>

<script>
$(function () {
    const currentYear = new Date().getFullYear();
    const startOfYear = new Date(currentYear, 0, 1);
    const endOfYear = new Date(currentYear, 11, 31, 23, 59, 59, 999);

    //Date picker
    $('#fromDatePicker').datetimepicker({
        icons: { time: 'far fa-calendar' },
        format: 'DD/MM/YYYY',
        defaultDate: startOfYear
    });

    $('#toDatePicker').datetimepicker({
        icons: { time: 'far fa-calendar' },
        format: 'DD/MM/YYYY',
        defaultDate: endOfYear
    });

    var fromDateValue = $('#fromDate').val();
    var toDateValue = $('#toDate').val();

    var table = $("#dashboardTable").DataTable({
        "responsive": true,
        "autoWidth": false,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'searching': false,
        'paging': false,
        'info': false,
        'ajax': {
            'type': 'POST',
            'url':'php/filterSumCountStamping.php',
            'data': {
                fromDate: fromDateValue,
                toDate: toDateValue
            } 
        },
        'columns': [
            { data: 'pending_metrology' },
            { data: 'complete_metrology' },
            { data: 'cancel_metrology' },
            { data: 'pending_demetrology' },
            { data: 'complete_demetrology' },
            { data: 'cancel_demetrology' },
        ]
    });

    $.ajax({
        url: 'php/filterSumValidatorStamping.php',
        type: 'POST',
        data: {
            fromDate: fromDateValue,
            toDate: toDateValue
        },
        dataType: 'json',
        success: function(response) {
            // Build dynamic columns but keep the first column fixed
            var dynamicColumns = response.columns.map(function(col, index) {
                return { data: col, orderable: false }; // Disable ordering per column
            });

            $('#validatorTable').find('#validatorHeader').attr("colspan", response.columns.length); 

            // Initialize DataTable
            var validatorTable = $("#validatorTable").DataTable({
                "destroy": true, // Allow reinitialization
                "responsive": true,
                "autoWidth": false,
                'processing': true,
                'paging': false,
                'info': false,
                'searching': false,
                "ordering": false,
                'data': response.aaData, // Load dynamic data
                'columns': dynamicColumns, // Use dynamic columns
                'createdRow': function(row, data, dataIndex) {
                    $('td:eq(0)', row).css({
                        'font-weight': 'bold',
                        'color': 'white',
                        'background-color': 'rgb(1, 162, 226)'
                    });

                    //Styling for Metrology & DE Metrology Row
                    if (dataIndex === 0 || dataIndex === 3) { 
                        $(row).css({
                            'font-weight': 'bold',
                            'color': 'white',
                            'background-color': 'rgb(1, 162, 226)'
                        });

                        // Apply styling to the last column of each row
                        $('td:last', row).css({
                            'font-weight': 'bold',
                            'color': 'black',
                            'background-color': '#FFECB3' // Example: Yellow background for last column
                        });
                    }
                }
            });
        }
    });

    $('#filterSearch').on('click', function(){
        var fromDateValue = $('#fromDate').val();
        var toDateValue = $('#toDate').val();

        //Destroy the old Datatable
        $("#dashboardTable").DataTable().clear().destroy();
        $("#validatorTable").DataTable().clear().destroy();

        //Create new Datatable
        table = $("#dashboardTable").DataTable({
            "responsive": true,
            "autoWidth": false,
            'processing': true,
            'serverSide': true,
            'serverMethod': 'post',
            'searching': false,
            'paging': false,
            'info': false,
            'ajax': {
                'type': 'POST',
                'url':'php/filterSumCountStamping.php',
                'data': {
                    fromDate: fromDateValue,
                    toDate: toDateValue
                } 
            },
            'columns': [
                { data: 'pending_metrology' },
                { data: 'complete_metrology' },
                { data: 'cancel_metrology' },
                { data: 'pending_demetrology' },
                { data: 'complete_demetrology' },
                { data: 'cancel_demetrology' },
            ]
        });

        $.ajax({
            url: 'php/filterSumValidatorStamping.php',
            type: 'POST',
            data: {
                fromDate: fromDateValue,
                toDate: toDateValue
            },
            dataType: 'json',
            success: function(response) {
                // Build dynamic columns but keep the first column fixed
                var dynamicColumns = response.columns.map(function(col, index) {
                    return { data: col, orderable: false }; // Disable ordering per column
                });

                $('#validatorTable').find('#validatorHeader').attr("colspan", response.columns.length); 

                // Initialize DataTable
                var validatorTable = $("#validatorTable").DataTable({
                    "destroy": true, // Allow reinitialization
                    "responsive": true,
                    "autoWidth": false,
                    'processing': true,
                    'paging': false,
                    'info': false,
                    'searching': false,
                    "ordering": false,
                    'data': response.aaData, // Load dynamic data
                    'columns': dynamicColumns, // Use dynamic columns
                    'createdRow': function(row, data, dataIndex) {
                        $('td:eq(0)', row).css({
                            'font-weight': 'bold',
                            'color': 'white',
                            'background-color': 'rgb(1, 162, 226)'
                        });

                        //Styling for Metrology & DE Metrology Row
                        if (dataIndex === 0 || dataIndex === 3) { 
                            $(row).css({
                                'font-weight': 'bold',
                                'color': 'white',
                                'background-color': 'rgb(1, 162, 226)'
                            });

                            // Apply styling to the last column of each row
                            $('td:last', row).css({
                                'font-weight': 'bold',
                                'color': 'black',
                                'background-color': '#FFECB3' // Example: Yellow background for last column
                            });
                        }
                    }
                });
            }
        });
    });

    // Event listener for changes in the #fromDatePicker
    $('#fromDatePicker').on('change.datetimepicker', function () {
        const fromDate = $('#fromDatePicker').datetimepicker('viewDate');
        const toDate = $('#toDatePicker').datetimepicker('viewDate');
        
        if (dateDifferenceInMonths(fromDate, toDate) > 12) {
            alert('The date difference cannot be more than 12 months.');
            const adjustedToDate = new Date(fromDate);
            adjustedToDate.setMonth(adjustedToDate.getMonth() + 12);
            $('#toDatePicker').datetimepicker('date', adjustedToDate);
        }
    });

    // Event listener for changes in the #toDatePicker
    $('#toDatePicker').on('change.datetimepicker', function () {
        const fromDate = $('#fromDatePicker').datetimepicker('viewDate');
        const toDate = $('#toDatePicker').datetimepicker('viewDate');
        
        if (dateDifferenceInMonths(fromDate, toDate) > 12) {
            alert('The date difference cannot be more than 12 months.');
            const adjustedFromDate = new Date(toDate);
            adjustedFromDate.setMonth(adjustedFromDate.getMonth() - 12);
            $('#fromDatePicker').datetimepicker('date', adjustedFromDate);
        }
    });
});

function dateDifferenceInMonths(fromDate, toDate) {
    const from = new Date(fromDate);
    const to = new Date(toDate);
    const months = (to.getFullYear() - from.getFullYear()) * 12 + (to.getMonth() - from.getMonth());
    return months;
}
</script>
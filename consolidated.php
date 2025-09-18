<?php
require_once 'php/db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
    echo '<script type="text/javascript">';
    echo 'window.location.href = "login.php";</script>';
}
else{
  $user = $_SESSION['userID'];
  $stmt = $db->prepare("SELECT * from users where id = ?");
	$stmt->bind_param('s', $user);
	$stmt->execute();
	$result = $stmt->get_result();
  $role = 'NORMAL';
  $branch = '';
  $_SESSION['page']='consolidated';
	
	if(($row = $result->fetch_assoc()) !== null){
    $role = $row['role_code'];
    $branch = $row['branch'];
  }

  $customers2 = $db->query("SELECT * FROM customers WHERE customer_status = 'CUSTOMERS' AND deleted = '0'");
  $validators = $db->query("SELECT * FROM validators WHERE deleted = '0'");
  if($role != 'ADMIN' && $role != 'SUPER_ADMIN'){
    $companyBranches = $db->query("SELECT * FROM company_branches WHERE deleted = '0' AND id = '$branch' ORDER BY branch_name ASC");
  }
  else{
    $companyBranches = $db->query("SELECT * FROM company_branches WHERE deleted = '0' ORDER BY branch_name ASC");
  }
}
?>

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Consolidated Report</h1>
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
                <div class="form-group col-3">
                  <label>From Date:</label>
                  <div class="input-group date" id="fromDatePicker" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input" data-target="#fromDatePicker" id="fromDate"/>
                    <div class="input-group-append" data-target="#fromDatePicker" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
                </div>

                <div class="form-group col-3">
                  <label>To Date:</label>
                  <div class="input-group date" id="toDatePicker" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input" data-target="#toDatePicker" id="toDate"/>
                    <div class="input-group-append" data-target="#toDatePicker" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
                </div>

                <div class="form-group col-3">
                  <label>Customer No</label>
                  <select class="form-control select2" id="customerNoFilter" name="customerNoFilter">
                    <option value="" selected disabled hidden>Please Select</option>
                    <?php while($rowCustomer2=mysqli_fetch_assoc($customers2)){ ?>
                      <option value="<?=$rowCustomer2['id'] ?>"><?=$rowCustomer2['customer_name'] ?></option>
                    <?php } ?>
                  </select>
                </div>

                <div class="form-group col-3">
                  <label>Type</label>
                  <select class="form-control select2" id="typeFilter" name="typeFilter">
                    <option value="Stamping" selected>Stamping</option>
                    <option value="Other">Other Validation</option>
                    <option value="Inhouse">Inhouse Validation</option>
                  </select>
                </div>

                <div class="form-group col-3" id="stampValidatorDiv">
                  <label>Validator</label>
                  <select class="form-control select2" id="validatorFilter" name="validatorFilter">
                    <option value="" selected disabled hidden>Please Select</option>
                    <?php while($rowValidators=mysqli_fetch_assoc($validators)){ ?>
                      <option value="<?=$rowValidators['id'] ?>"><?=$rowValidators['validator'] ?></option>
                    <?php } ?>
                  </select>
                </div>

                <div class="form-group col-3">
                  <label>Branch:</label>
                  <select class="form-control select2" id="branchFilter" name="branchFilter">
                    <option value="" disabled hidden>Please Select</option>
                    <?php while ($row = mysqli_fetch_assoc($companyBranches)) { ?>
                      <option value="<?= $row['id'] ?>" <?= (strtoupper($row['branch_code']) == 'HQ') ? 'selected' : '' ?>><?= $row['branch_name'] ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>

              <div class="row">
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
        <div class="card">
          <div class="card-header search-filter">
            <div class="row">
              <div class="col-10">
                <h5 class="card-title mb-0 font-weight-bold">Consolidated Report</h5>
              </div>
              <div class="col-2">
                <button type="button" class="btn btn-block btn-sm bg-gradient-success" id="exportExcel" data-bs-toggle="tooltip" title="Export Excel"><i class="fa-regular fa-file-excel"></i> Export Excel</button>
              </div>
            </div>
          </div>
          <div class="card-body">
            <table id="reportTable" class="table table-bordered table-striped display">
              <thead>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
  var table = null;

  $(function () {
    const currentYear = new Date().getFullYear();
    const startOfYear = new Date(currentYear, 0, 1);
    const endOfYear = new Date(currentYear, 11, 31, 23, 59, 59, 999);

    $('.select2').select2({
      allowClear: true,
      placeholder: "Please Select"
    });

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

    // Use event delegation for dynamically created selectAllCheckbox
    $(document).on('change', '#selectAllCheckbox', function() {
      var checkboxes = $('#reportTable tbody input[type="checkbox"]');
      checkboxes.prop('checked', $(this).prop('checked')).trigger('change');
    });

    var fromDateValue = $('#fromDate').val();
    var toDateValue = $('#toDate').val();
    var customerNoFilter = $('#customerNoFilter').val() ? $('#customerNoFilter').val() : '';
    var typeFilter = $('#typeFilter').val() ? $('#typeFilter').val() : '';
    var validatorFilter = $('#validatorFilter').val() ? $('#validatorFilter').val() : '';
    var branchFilter = $('#branchFilter').val() ? $('#branchFilter').val() : '';

    buildHeader(typeFilter);
    if (typeFilter == 'Stamping') {
      table = $("#reportTable").DataTable({
        "responsive": true,
        "autoWidth": false,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'searching': false,
        'paging': true,
        'info': false,
        'order': [[ 1, 'asc' ]],
        'ajax': {
          'type': 'POST',
          'url':'php/filterReport.php',
          'data': {
            fromDate: fromDateValue,
            toDate: toDateValue,
            customer: customerNoFilter,
            type: typeFilter,
            validator: validatorFilter,
            branch: branchFilter
          } 
        },
        'columns': [
          {
            // Add a checkbox with a unique ID for each row
            data: 'id', // Assuming 'serialNo' is a unique identifier for each row
            className: 'select-checkbox',
            orderable: false,
            render: function (data, type, row) {
              return '<input type="checkbox" class="select-checkbox" id="checkbox_' + data + '" value="'+data+'"/>';
            }
          },
          { data: 'customers' },
          { data: 'brand' },
          { data: 'machine_type' },
          { data: 'serial_no' },
          { data: 'validate_by' },
          { data: 'jenis_alat' },
          { data: 'capacity' },
          { data: 'no_daftar_lama' },
          { data: 'no_daftar_baru' },
          { data: 'stamping_date' },
          { data: 'due_date' },
          { data: 'status' }
        ]
      });
    } else if (typeFilter == 'Other') {
      table = $("#reportTable").DataTable({
        "responsive": true,
        "autoWidth": false,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'searching': false,
        'paging': true,
        'info': false,
        'order': [[ 1, 'asc' ]],  
        'ajax': {
          'type': 'POST',
          'url':'php/filterReport.php',
          'data': {
            fromDate: fromDateValue,
            toDate: toDateValue,
            customer: customerNoFilter,
            type: typeFilter,
            validator: validatorFilter,
            branch: branchFilter
          } 
        },
        'columns': [
          {
            // Add a checkbox with a unique ID for each row
            data: 'id', // Assuming 'serialNo' is a unique identifier for each row
            className: 'select-checkbox',
            orderable: false,
            render: function (data, type, row) {
              return '<input type="checkbox" class="select-checkbox" id="checkbox_' + data + '" value="'+data+'"/>';
            }
          },
          { data: 'customer' },
          { data: 'brand' },
          { data: 'machines' },
          { data: 'validate_by' },
          { data: 'capacity' },
          { data: 'auto_form_no' },
          { data: 'last_calibration_date' },
          { data: 'expired_calibration_date' },
          { data: 'status' },
        ]
      });
    } else if (typeFilter == 'Inhouse') {
      table = $("#reportTable").DataTable({
        "responsive": true,
        "autoWidth": false,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'searching': false,
        'paging': true,
        'info': false,
        'order': [[ 1, 'asc' ]],  
        'ajax': {
          'type': 'POST',
          'url':'php/filterReport.php',
          'data': {
            fromDate: fromDateValue,
            toDate: toDateValue,
            customer: customerNoFilter,
            type: typeFilter,
            validator: validatorFilter,
            branch: branchFilter
          } 
        },
        'columns': [
          {
            // Add a checkbox with a unique ID for each row
            data: 'id', // Assuming 'serialNo' is a unique identifier for each row
            className: 'select-checkbox',
            orderable: false,
            render: function (data, type, row) {
              return '<input type="checkbox" class="select-checkbox" id="checkbox_' + data + '" value="'+data+'"/>';
            }
          },
          { data: 'customer' },
          { data: 'brand' },
          { data: 'machines' },
          { data: 'capacity' },
          { data: 'auto_cert_no' },
          { data: 'validation_date' },
          { data: 'expired_date' },
          { data: 'calibrator' },
          { data: 'status' },
        ]
      });
    }

    $('#filterSearch').on('click', function(){
      var fromDateValue = $('#fromDate').val();
      var toDateValue = $('#toDate').val();
      var customerNoFilter = $('#customerNoFilter').val() ? $('#customerNoFilter').val() : '';
      var typeFilter = $('#typeFilter').val() ? $('#typeFilter').val() : '';
      var validatorFilter = $('#validatorFilter').val() ? $('#validatorFilter').val() : '';
      var branchFilter = $('#branchFilter').val() ? $('#branchFilter').val() : '';

      // Destroy the old Datatable
      $("#reportTable").DataTable().clear().destroy();

      buildHeader(typeFilter);
      if (typeFilter == 'Stamping') {
        // Create new Datatable
        table = $("#reportTable").DataTable({
          "responsive": true,
          "autoWidth": false,
          'processing': true,
          'serverSide': true,
          'serverMethod': 'post',
          'searching': false,
          'paging': true,
          'info': false,
          'order': [[ 1, 'asc' ]],
          'ajax': {
            'type': 'POST',
            'url':'php/filterReport.php',
            'data': {
              fromDate: fromDateValue,
              toDate: toDateValue,
              customer: customerNoFilter,
              type: typeFilter,
              validator: validatorFilter,
              branch: branchFilter
            } 
          },
          'columns': [
            {
              // Add a checkbox with a unique ID for each row
              data: 'id', // Assuming 'serialNo' is a unique identifier for each row
              className: 'select-checkbox',
              orderable: false,
              render: function (data, type, row) {
                return '<input type="checkbox" class="select-checkbox" id="checkbox_' + data + '" value="'+data+'"/>';
              }
            },
            { data: 'customers' },
            { data: 'brand' },
            { data: 'machine_type' },
            { data: 'serial_no' },
            { data: 'validate_by' },
            { data: 'jenis_alat' },
            { data: 'capacity' },
            { data: 'no_daftar_lama' },
            { data: 'no_daftar_baru' },
            { data: 'stamping_date' },
            { data: 'due_date' },
            { data: 'status' }
          ]
        });
      } else if (typeFilter == 'Other') {
        table = $("#reportTable").DataTable({
          "responsive": true,
          "autoWidth": false,
          'processing': true,
          'serverSide': true,
          'serverMethod': 'post',
          'searching': false,
          'paging': true,
          'info': false,
          'order': [[ 1, 'asc' ]],  
          'ajax': {
            'type': 'POST',
            'url':'php/filterReport.php',
            'data': {
              fromDate: fromDateValue,
              toDate: toDateValue,
              customer: customerNoFilter,
              type: typeFilter,
              validator: validatorFilter,
              branch: branchFilter
            } 
          },
          'columns': [
            {
              // Add a checkbox with a unique ID for each row
              data: 'id', // Assuming 'serialNo' is a unique identifier for each row
              className: 'select-checkbox',
              orderable: false,
              render: function (data, type, row) {
                return '<input type="checkbox" class="select-checkbox" id="checkbox_' + data + '" value="'+data+'"/>';
              }
            },
            { data: 'customer' },
            { data: 'brand' },
            { data: 'machines' },
            { data: 'validate_by' },
            { data: 'capacity' },
            { data: 'auto_form_no' },
            { data: 'last_calibration_date' },
            { data: 'expired_calibration_date' },
            { data: 'status' },
          ]
        });
      } else if (typeFilter == 'Inhouse') {
        table = $("#reportTable").DataTable({
          "responsive": true,
          "autoWidth": false,
          'processing': true,
          'serverSide': true,
          'serverMethod': 'post',
          'searching': false,
          'paging': true,
          'info': false,
          'order': [[ 1, 'asc' ]],  
          'ajax': {
            'type': 'POST',
            'url':'php/filterReport.php',
            'data': {
              fromDate: fromDateValue,
              toDate: toDateValue,
              customer: customerNoFilter,
              type: typeFilter,
              validator: validatorFilter,
              branch: branchFilter
            } 
          },
          'columns': [
            {
              // Add a checkbox with a unique ID for each row
              data: 'id', // Assuming 'serialNo' is a unique identifier for each row
              className: 'select-checkbox',
              orderable: false,
              render: function (data, type, row) {
                return '<input type="checkbox" class="select-checkbox" id="checkbox_' + data + '" value="'+data+'"/>';
              }
            },
            { data: 'customer' },
            { data: 'brand' },
            { data: 'machines' },
            { data: 'capacity' },
            { data: 'auto_cert_no' },
            { data: 'validation_date' },
            { data: 'expired_date' },
            { data: 'calibrator' },
            { data: 'status' },
          ]
        });
      }
    });

    $('#exportExcel').on('click', function(){
      var selectedIds = []; // An array to store the selected 'id' values

      $("#reportTable tbody input[type='checkbox']").each(function () {
        if (this.checked) {
          selectedIds.push($(this).val());
        }
      });

      var fromDateValue = $('#fromDate').val();
      var toDateValue = $('#toDate').val();
      var customerNoFilter = $('#customerNoFilter').val() ? $('#customerNoFilter').val() : '';
      var typeFilter = $('#typeFilter').val() ? $('#typeFilter').val() : '';
      var validatorFilter = $('#validatorFilter').val() ? $('#validatorFilter').val() : '';
      var branchFilter = $('#branchFilter').val() ? $('#branchFilter').val() : ''; console.log(selectedIds);
      
      if(selectedIds.length > 0){
        window.open("php/exportReport.php?reportType=multi&fromDate="+fromDateValue+"&toDate="+toDateValue+"&customer="+customerNoFilter+"&type="+typeFilter+"&validator="+validatorFilter+"&branch="+branchFilter+"&ids="+selectedIds);
      }else{
        window.open("php/exportReport.php?reportType=single&fromDate="+fromDateValue+"&toDate="+toDateValue+"&customer="+customerNoFilter+"&type="+typeFilter+"&validator="+validatorFilter+"&branch="+branchFilter+"&ids=");
      }
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

    $('#typeFilter').on('change', function() {
      var selectedType = $(this).val();
      
      if (selectedType) {
        $.post('php/getValidator.php', { type: selectedType, action: 'Report' }, function(data){
          var obj = JSON.parse(data); 
          if(obj.status === 'success'){
            var $validatorFilter = $('#validatorFilter');
            $validatorFilter.empty();
            $validatorFilter.append('<option value="" selected disabled hidden>Please Select</option>');

            obj.message.forEach(function(validator) {
              $validatorFilter.append('<option value="' + validator.id + '">' + validator.validator + '</option>');
            });

            $('.select2').select2({
              allowClear: true,
              placeholder: "Please Select"
            });

          }
          else if(obj.status === 'failed'){
            toastr["error"](obj.message, "Failed:");
          }
          else{
            toastr["error"]("Something wrong when edit", "Failed:");
          }
        });
      }
    });
  });

  function buildHeader(type) {
    let html = "";

    if (type == "Stamping") {
      html = `
        <tr>
          <th><input type="checkbox" id="selectAllCheckbox" class="selectAllCheckbox"></th>
          <th>Company Name</th>
          <th>Brand</th>
          <th>Description Instruments</th>
          <th>Serial No</th>
          <th>Validators</th>
          <th>Jenis Alat</th>
          <th>Capacity</th>
          <th>No Daftar Lama</th>
          <th>No Daftar Baru</th>
          <th>Stamp Date</th>
          <th>Next Due Date</th>
          <th>Status</th>
        </tr>
        `;
    }
    else if (type == "Other") {
      html = `
        <tr>
          <th><input type="checkbox" id="selectAllCheckbox" class="selectAllCheckbox"></th>
          <th>Company Name</th>
          <th>Brand</th>
          <th>Description Instruments</th>
          <th>Validators</th>
          <th>Capacity</th>
          <th>Previous Cert No</th>
          <th>Validation Date</th>
          <th>Expired Date</th>
          <th>Status</th>
        </tr>
        `;
    }
    else if (type == "Inhouse") {
      html = `
        <tr>
          <th><input type="checkbox" id="selectAllCheckbox" class="selectAllCheckbox"></th>
          <th>Company Name</th>
          <th>Brand</th>
          <th>Description Instruments</th>
          <th>Capacity</th>
          <th>Previous Cert No</th>
          <th>Inhouse Date</th>
          <th>Expired Date</th>
          <th>Calibrator By</th>
          <th>Status</th>
        </tr>
        `;
    }

    $("#reportTable thead").html(html);
  }

  function dateDifferenceInMonths(fromDate, toDate) {
    const from = new Date(fromDate);
    const to = new Date(toDate);
    const months = (to.getFullYear() - from.getFullYear()) * 12 + (to.getMonth() - from.getMonth());
    return months;
  }
</script>
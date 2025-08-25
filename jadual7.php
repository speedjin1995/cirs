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
  $_SESSION['page']='jadual7';
	
	if(($row = $result->fetch_assoc()) !== null){
    $role = $row['role_code'];
  }

  $customers2 = $db->query("SELECT * FROM customers WHERE customer_status = 'CUSTOMERS' AND deleted = '0'");
  $validators = $db->query("SELECT * FROM validators WHERE type = 'STAMPING' AND deleted = '0'");

  $stmt->close();
  $db->close();
}
?>

<style>
  th {
    text-align: center;
  }
</style>

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Jadual 7</h1>
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
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div></div>
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

                <div class="col-3">
                  <div class="form-group">
                    <label>Customer No</label>
                    <select class="form-control select2" id="customerNoFilter" name="customerNoFilter">
                      <option value="" selected disabled hidden>Please Select</option>
                      <?php while($rowCustomer2=mysqli_fetch_assoc($customers2)){ ?>
                        <option value="<?=$rowCustomer2['id'] ?>"><?=$rowCustomer2['customer_name'] ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>

                <div class="col-3">
                  <div class="form-group">
                    <label>Validator</label>
                    <select class="form-control select2" id="validatorFilter" name="validatorFilter">
                      <option value="" selected disabled hidden>Please Select</option>
                      <?php while($rowValidators=mysqli_fetch_assoc($validators)){ ?>
                        <option value="<?=$rowValidators['id'] ?>"><?=$rowValidators['validator'] ?></option>
                      <?php } ?>
                    </select>
                  </div>
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
        <div class="card card-primary">
          <div class="card-header">
            <div class="row">
              <!-- <div class="col-8"><p>Renewal Stamping</p></div> -->
              <div class="col-8"></div>
              <div class="col-2">
                <button type="button" class="btn btn-block btn-sm bg-gradient-info" id="exportBorangs" data-bs-toggle="tooltip" title="Export Borang"><i class="fa-solid fa-file-export"></i> Export Borang</button>
              </div>
              <div class="col-2">
                <button type="button" class="btn btn-block btn-sm bg-gradient-success" id="exportExcel" data-bs-toggle="tooltip" title="Export Excel"><i class="fa-regular fa-file-excel"></i> Export Excel</button>
              </div>
              <!--div class="col-2">
                <button type="button" class="btn btn-block bg-gradient-success btn-sm" id="uploadExccl">Upload Excel</button>
              </div-->
              <!--div class="col-2">
                <button type="button" class="btn btn-block bg-gradient-warning btn-sm" onclick="newEntry()">Add New Stamping</button>
              </div-->
            </div>
          </div>

          <div class="card-body">
            <table id="weightTable" class="table table-bordered table-striped display">
              <thead>
                <tr>
                  <th><input type="checkbox" id="selectAllCheckbox" class="selectAllCheckbox"></th>
                  <th width="8%">BRG D BIL NO.</th>
                  <th width="8%">BRG E BIL NO.</th>
                  <th width="8%">BRG E DATE</th>
                  <th>STAMPING DATE</th>
                  <th>NAME OF PURCHASE</th>
                  <th>ABOUT WEIGHING, MEASURING AND WEIGHING INSTRUMENTS</th>
                  <th>CAPACITY</th>
                  <th>LIST NO. (STMP. NO.)</th>
                  <th>NO. DAFTAR LAMA</th>
                  <th>NO. DAFTAR BARU</th>
                  <th>DETAILS OF REPAIR</th>
                  <th>CERTIFICATE NO./ NO. SIRI PELEKAT KESELAMATAN</th>
                  <th width='9%'>FEE</th>
                  <th></th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="printDOModal">
  <div class="modal-dialog modal-xl" style="max-width: 50%;">
    <div class="modal-content">

      <form role="form" id="printDOForm">
        <div class="modal-header bg-gray-dark color-palette">
          <h4 class="modal-title">Select Borang</h4>
          <button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <input type="hidden" class="form-control" id="id" name="id">
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <label>Jadual 7 *</label>
                <select class="form-control" id="driver" name="driver" required>
                  <option value="7">Jadual 7</option>
                </select>
              </div>
            </div>
          </div>  
        </div>

        <div class="modal-footer justify-content-between bg-gray-dark color-palette">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="saveButton">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="printJadualModal">
  <div class="modal-dialog modal-xl" style="max-width: 90%;">
    <div class="modal-content">

      <form role="form" id="printJadualForm">
        <div class="modal-header bg-gray-dark color-palette">
          <h4 class="modal-title">Order Records</h4>
          <button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <input type="hidden" class="form-control" id="ids" name="ids">
          <input type="hidden" class="form-control" id="validatorFilter" name="validatorFilter">
          <input type="hidden" class="form-control" id="cawanganFilter" name="cawanganFilter">
          <input type="hidden" class="form-control" id="driver" name="driver">
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <table id="orderJadualTable" class="table table-bordered table-striped display">
                  <thead>
                    <tr>
                      <th width="8%">BRG D BIL NO.</th>
                      <th width="8%">BRG E BIL NO.</th>
                      <th width="8%">BRG E DATE</th>
                      <th>STAMPING DATE</th>
                      <th>NAME OF PURCHASE</th>
                      <th>ABOUT WEIGHING, MEASURING AND WEIGHING INSTRUMENTS</th>
                      <th>CAPACITY</th>
                      <th>LIST NO. (STMP. NO.)</th>
                      <th>NO. DAFTAR LAMA</th>
                      <th>NO. DAFTAR BARU</th>
                      <th>DETAILS OF REPAIR</th>
                      <th>CERTIFICATE NO./ NO. SIRI PELEKAT KESELAMATAN</th>
                      <th width='9%'>FEE</th>
                    </tr>
                  </thead>
                </table>
              </div>
            </div>
          </div> 
        </div>

        <div class="modal-footer justify-content-between bg-gray-dark color-palette">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="saveButton">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
$(function () {
  $('#customerNoHidden').hide();

  const today = new Date();
  const tomorrow = new Date(today);
  const yesterday = new Date(today);
  tomorrow.setDate(tomorrow.getDate() + 1);
  yesterday.setDate(tomorrow.getDate() - 7);
  const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1); // First day of the current month
  const endOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0); // Last day of the current month

  $('.select2').select2({
    allowClear: true,
    placeholder: "Please Select"
  });

  //Date picker
  $('#fromDatePicker').datetimepicker({
    icons: { time: 'far fa-calendar' },
    format: 'DD/MM/YYYY',
    defaultDate: startOfMonth
  });

  $('#toDatePicker').datetimepicker({
    icons: { time: 'far fa-calendar' },
    format: 'DD/MM/YYYY',
    defaultDate: endOfMonth
  });

  $('#selectAllCheckbox').on('change', function() {
    var checkboxes = $('#weightTable tbody input[type="checkbox"]');
    checkboxes.prop('checked', $(this).prop('checked')).trigger('change');
  });

  var fromDateValue = $('#fromDate').val();
  var toDateValue = $('#toDate').val();
  var customerNoFilter = $('#customerNoFilter').val() ? $('#customerNoFilter').val() : '';
  var validatorFilter = $('#validatorFilter').val() ? $('#validatorFilter').val() : '';
  var cawanganFilter = $('#cawanganFilter').val() ? $('#cawanganFilter').val() : '';    
  var statusFilter = '7';

  var table = $("#weightTable").DataTable({
    "responsive": true,
    "autoWidth": false,
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
    'searching': true,
    'order': [[ 2, 'asc' ]],
    'columnDefs': [ { orderable: false, targets: [0] }],
    'ajax': {
      'type': 'POST',
      'url':'php/filterJaduals.php',
      'data': {
        fromDate: fromDateValue,
        toDate: toDateValue,
        customer: customerNoFilter,
        validator: validatorFilter,
        cawangan: cawanganFilter,
        status: statusFilter
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

          // if (row.status == 'Pending') { // Assuming 'isInvoiced' is a boolean field in your row data
          //   return '<input type="checkbox" class="select-checkbox" id="checkbox_' + data + '" value="'+data+'"/>';
          // } 
          // else {
          //   return ''; // Return an empty string or any other placeholder if the item is invoiced
          // }
        }
      },
      { 
        data: 'borang_d', 
        name: 'borang_d'
      },
      { 
        data: 'borang_e', 
        name: 'borang_e'
      },
      { 
        data: 'borang_e_date', 
        name: 'borang_e_date'
      },
      { 
        data: 'stamping_date', 
        name: 'stamping_date'
      },
      { 
        data: 'customers', 
        name: 'customers'
      }
      ,
      {
        data: null, // We set data to null to allow custom rendering
        name: 'brand_model',
        render: function (data, type, row) {
          return row.brand + '<br>' + row.model;
        }
      },
      { 
        data: 'capacity', 
        name: 'capacity'
      },
      { 
        data: 'pin_keselamatan', 
        name: 'pin_keselamatan'
      },
      { 
        data: 'no_daftar_lama', 
        name: 'no_daftar_lama'
      },
      { 
        data: 'no_daftar_baru', 
        name: 'no_daftar_baru'
      },
      { 
        orderable: false,
        data: 'reason', 
        name: 'reason'
      },
      { 
        data: 'siri_keselamatan', 
        name: 'siri_keselamatan'
      },
      {
        data: null, // Custom rendering for unit_price and cert_price
        name: 'price',
        orderable: false,
        render: function (data, type, row) {
          if (row.cert_price != 0){
            return 'RM ' + parseFloat(row.unit_price).toFixed(2) + '<br>' + 'RM ' + parseFloat(row.cert_price).toFixed(2) + ' (Laporan)';
          }else{
            return 'RM ' + parseFloat(row.unit_price).toFixed(2);
          }  
        }
      },
      { 
        className: 'dt-control',
        orderable: false,
        data: null,
        render: function ( data, type, row ) {
          return '<td class="table-elipse" data-toggle="collapse" data-target="#demo'+row.serialNo+'"><i class="fas fa-angle-down"></i></td>';
        }
      }
    ],
    "lengthMenu": [ [10, 25, 50, 100, 300, 600, 1000], [10, 25, 50, 100, 300, 600, 1000] ], // More show options
    "pageLength": 10 // Default rows per page
  });
  
  // Add event listener for opening and closing details
  $('#weightTable tbody').on('click', 'td.dt-control', function () {
    var tr = $(this).closest('tr');
    var row = table.row( tr );

    if ( row.child.isShown() ) {
      // This row is already open - close it
      row.child.hide();
      tr.removeClass('shown');
    }
    else {
      $.post('php/getStamp.php', { userID: row.data().id, format: 'EXPANDABLE' }, function (data) {
        var obj = JSON.parse(data);
        if (obj.status === 'success') {
          row.child(format(obj.message)).show();
          tr.addClass("shown");
        }
      });
    }
  });

  $.validator.setDefaults({
    submitHandler: function () {
      if($('#extendModal').hasClass('show')){
        $('#spinnerLoading').show();

        $.post('php/insertStamping.php', $('#extendForm').serialize(), function(data){
          var obj = JSON.parse(data); 
          if(obj.status === 'success'){
            $('#extendModal').modal('hide');
            toastr["success"](obj.message, "Success:");
            $('#weightTable').DataTable().ajax.reload();
          }
          else if(obj.status === 'failed'){
            toastr["error"](obj.message, "Failed:");
          }
          else{
            toastr["error"]("Something wrong when edit", "Failed:");
          }

          $('#spinnerLoading').hide();
        });
      }
      else if($('#uploadModal').hasClass('show')){
        $('#spinnerLoading').show();

        // Serialize the form data into an array of objects
        var formData = $('#uploadForm').serializeArray();
        var data = [];
        var rowIndex = -1;
        formData.forEach(function(field) {
            var match = field.name.match(/([a-zA-Z]+)\[(\d+)\]/);
            if (match) {
              var fieldName = match[1];
              var index = parseInt(match[2], 10);
              if (index !== rowIndex) {
                rowIndex = index;
                data.push({});
              }
              data[index][fieldName] = field.value;
            }
        });

        // Send the JSON array to the server
        $.ajax({
            url: 'php/uploadStampings.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify(data),
            success: function(response) {
              var obj = JSON.parse(response);
              if (obj.status === 'success') {
                $('#uploadModal').modal('hide');
                toastr["success"](obj.message, "Success:");
                $('#weightTable').DataTable().ajax.reload();
              } 
              else if (obj.status === 'failed') {
                toastr["error"](obj.message, "Failed:");
              } 
              else {
                toastr["error"]("Something went wrong when editing", "Failed:");
              }
              
              $('#spinnerLoading').hide();
            }
        });
      }
      else if($('#printDOModal').hasClass('show')){
        $.post('php/print_borang.php', $('#printDOForm').serialize(), function(data){
          var obj = JSON.parse(data);
      
          if(obj.status === 'success'){
            $('#printDOModal').modal('hide');
            $('#weightTable').DataTable().ajax.reload();
            var printWindow = window.open('', '', 'height=' + screen.height + ',width=' + screen.width);
            printWindow.document.write(obj.message);
            printWindow.document.close();
            setTimeout(function(){
              printWindow.print();
              printWindow.close();
            }, 1000);
          }
          else if(obj.status === 'failed'){
            toastr["error"](obj.message, "Failed:");
          }
          else{
            toastr["error"]("Something wrong when pull data", "Failed:");
          }
        });
      }
      else if($('#printJadualModal').hasClass('show')){
        $.post('php/export_borang.php', $('#printJadualForm').serialize(), function(data){
          var obj = JSON.parse(data);

          if(obj.status === 'success'){
            var printWindow = window.open('', '', 'height=' + screen.height + ',width=' + screen.width);
            printWindow.document.write(obj.message);
            printWindow.document.close();
            setTimeout(function(){
              printWindow.print();
              printWindow.close();
            }, 1000);
          }
          else if(obj.status === 'failed'){
            toastr["error"](obj.message, "Failed:");
          }
          else{
            toastr["error"]("Something wrong when pull data", "Failed:");
          }
        });
      }
    }
  });

  $('#filterSearch').on('click', function(){
    //$('#spinnerLoading').show();

    var fromDateValue = $('#fromDate').val();
    var toDateValue = $('#toDate').val();
    var customerNoFilter = $('#customerNoFilter').val() ? $('#customerNoFilter').val() : '';
    var validatorFilter = $('#validatorFilter').val() ? $('#validatorFilter').val() : '';  
    var cawanganFilter = $('#cawanganFilter').val() ? $('#cawanganFilter').val() : '';  
    var statusFilter = '7';

    //Destroy the old Datatable
    $("#weightTable").DataTable().clear().destroy();

    //Create new Datatable
    table = $("#weightTable").DataTable({
      "responsive": true,
      "autoWidth": false,
      'processing': true,
      'serverSide': true,
      'serverMethod': 'post',
      'searching': true,
      'order': [[ 2, 'asc' ]],
      'columnDefs': [ { orderable: false, targets: [0] }],
      'ajax': {
        'type': 'POST',
        'url':'php/filterJaduals.php',
        'data': {
          fromDate: fromDateValue,
          toDate: toDateValue,
          customer: customerNoFilter,
          validator: validatorFilter,
          cawangan: cawanganFilter,
          status: statusFilter
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

            // if (row.status == 'Pending') { // Assuming 'isInvoiced' is a boolean field in your row data
            //   return '<input type="checkbox" class="select-checkbox" id="checkbox_' + data + '" value="'+data+'"/>';
            // } 
            // else {
            //   return ''; // Return an empty string or any other placeholder if the item is invoiced
            // }
          }
        },
        { 
          data: 'borang_d', 
          name: 'borang_d'
        },
        { 
          data: 'borang_e', 
          name: 'borang_e'
        },
        { 
          data: 'borang_e_date', 
          name: 'borang_e_date'
        },
        { 
          data: 'stamping_date', 
          name: 'stamping_date'
        },
        { 
          data: 'customers', 
          name: 'customers'
        }
        ,
        {
          data: null, // We set data to null to allow custom rendering
          name: 'brand_model',
          render: function (data, type, row) {
            return row.brand + '<br>' + row.model;
          }
        },
        { 
          data: 'capacity', 
          name: 'capacity'
        },
        { 
          data: 'pin_keselamatan', 
          name: 'pin_keselamatan'
        },
        { 
          data: 'no_daftar_lama', 
          name: 'no_daftar_lama'
        },
        { 
          data: 'no_daftar_baru', 
          name: 'no_daftar_baru'
        },
        { 
          orderable: false,
          data: 'reason', 
          name: 'reason'
        },
        { 
          data: 'siri_keselamatan', 
          name: 'siri_keselamatan'
        },
        {
          data: null, // Custom rendering for unit_price and cert_price
          name: 'price',
          orderable: false,
          render: function (data, type, row) {
            if (row.cert_price != 0){
              return 'RM ' + parseFloat(row.unit_price).toFixed(2) + '<br>' + 'RM ' + parseFloat(row.cert_price).toFixed(2) + ' (Laporan)';
            }else{
              return 'RM ' + parseFloat(row.unit_price).toFixed(2);
            }  
          }
        },
        { 
          className: 'dt-control',
          orderable: false,
          data: null,
          render: function ( data, type, row ) {
            return '<td class="table-elipse" data-toggle="collapse" data-target="#demo'+row.serialNo+'"><i class="fas fa-angle-down"></i></td>';
          }
        }
      ],
      "lengthMenu": [ [10, 25, 50, 100, 300, 600, 1000], [10, 25, 50, 100, 300, 600, 1000] ], // More show options
      "pageLength": 10 // Default rows per page
    });
  });

  $('#exportBorangs').on('click', function () {
    var selectedIds = []; // An array to store the selected 'id' values

    $("#weightTable tbody input[type='checkbox']").each(function () {
      if (this.checked) {
        selectedIds.push($(this).val());
      }
    });

    // var fromDateValue = $('#fromDate').val();
    // var toDateValue = $('#toDate').val();
    // var customerNoFilter = $('#customerNoFilter').val() ? $('#customerNoFilter').val() : '';
    
    if(selectedIds.length > 0){
      $('#printJadualModal').find('#ids').val(selectedIds);
      $('#printJadualModal').find('#driver').val('7');

      // Destroy existing DataTable instance safely
      if ($.fn.DataTable.isDataTable("#printJadualModal #orderJadualTable")) {
        orderJadualTable.destroy();
      }

      orderJadualTable = $("#printJadualModal").find("#orderJadualTable").DataTable({
        "responsive": true,
        "autoWidth": false,
        "processing": true,
        "serverSide": true,
        "serverMethod": "post",
        "paging": false,        // Disable pagination
        "searching": false,     // Disable search box
        "ordering": false,      // Disable sorting
        "info": false,          // Disable "Showing X of Y entries"
        "rowReorder": {
          selector: 'tr', // Makes the entire row draggable
          dataSrc: 'id',   // Track row position using 'id'
          update: false // Prevent automatic update after reordering
        },
        "columnDefs": [ { orderable: false, targets: "_all" }], // Disable sorting
        "ajax": {
          "type": "POST",
          "url": "php/getMultiStamping.php",
          "data": function (d) {
            d.selectedIds = selectedIds; // Pass the selected IDs
          }
        },
        "columns": [
          { data: 'borang_d' },
          { data: 'borang_e' },
          { data: 'borang_e_date' },
          { data: 'stamping_date' },
          { data: 'customers' },

          {
            data: null, // We set data to null to allow custom rendering
            name: 'brand_model',
            render: function (data, type, row) {
              return row.brand + '<br>' + row.model;
            }
          },
          { data: 'capacity' },
          { data: 'pin_keselamatan' },
          { data: 'no_daftar_lama' },
          { data: 'no_daftar_baru' },
          { data: 'reason' },
          { data: 'siri_keselamatan' },
          {
            data: null, // Custom rendering for unit_price and cert_price
            name: 'price',
            orderable: false,
            render: function (data, type, row) {
              if (row.cert_price != 0){
                return 'RM ' + parseFloat(row.unit_price).toFixed(2) + '<br>' + 'RM ' + parseFloat(row.cert_price).toFixed(2) + ' (Laporan)';
              }else{
                return 'RM ' + parseFloat(row.unit_price).toFixed(2);
              }  
            }
          },
          { data: "id", visible: false }, // Hide 'id' but keep it in DataTable
        ]
      });

      $("#printJadualModal").find('#orderJadualTable').show();
      $("#printJadualModal").modal("show");

      orderJadualTable.off("row-reorder").on("row-reorder", function (e, diff, edit) {
        var newOrderedIds = [];

        $('#orderJadualTable tbody tr').each(function () {
            let rowData = orderJadualTable.row(this).data(); // Fetch row data
            if (rowData) {
              newOrderedIds.push(rowData.id); // Assuming ID is in column index 0
            }
        });

        $("#printJadualModal").find('#ids').val(newOrderedIds.join(','));
      });

      $('#printJadualForm').validate({
        errorElement: 'span',
        errorPlacement: function (error, element) {
          error.addClass('invalid-feedback');
          element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
          $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
          $(element).removeClass('is-invalid');
        }
      });
    }else{
      // Optionally, you can display a message or take another action if no IDs are selected
      alert("Please select at least one record.");
    }
  });

  $('#exportExcel').on('click', function () {
    var selectedIds = []; // An array to store the selected 'id' values

    $("#weightTable tbody input[type='checkbox']").each(function () {
      if (this.checked) {
        selectedIds.push($(this).val());
      }
    });

    var fromDateValue = $('#fromDate').val();
    var toDateValue = $('#toDate').val();
    var customerNoFilter = $('#customerNoFilter').val() ? $('#customerNoFilter').val() : '';
    
    if(selectedIds.length > 0){
      window.open("php/export.php?fromDate="+fromDateValue+"&toDate="+toDateValue+
      "&customer="+customerNoFilter+"&type=7&id="+selectedIds);
    }else{
      alert("Please select at least one record to export.");
    }
  });
});

function format (row) {
  const allowedAlats = ['ATK','ATP','ATS','ATE','BTU','ATN','ATL','ATP-AUTO MACHINE','SLL','ATS (H)','ATN (G)', 'ATP (MOTORCAR)', 'SIA', 'BAP', 'SIC'];

  var returnString = `
  <div class="row">
    <!-- Customer Section -->
    <div class="col-md-6">
      <p><span><strong style="font-size:120%; text-decoration: underline;">Stamping To : Customer</strong></span><br>
      <strong>${row.customers}</strong><br>
      ${row.address1}<br>${row.address2}<br>${row.address3}<br>${row.address4} `;

      if (row.pic) {
          returnString += `
              <br><b>PIC:</b> ${row.pic} <b>PIC Contact:</b> ${row.pic_phone}`;
      }     
      returnString += `</p></div>`;

  if (row.dealer){
    returnString += `
    <!-- Reseller Section -->
    <div class="col-md-6">
      <p><span><strong style="font-size:120%; text-decoration: underline;">Billing or Supply by Reseller</strong></span><br>
      <strong>${row.dealer}</strong><br>
      ${row.reseller_address1}<br>${row.reseller_address2}<br>${row.reseller_address3}<br>${row.reseller_address4} `;
      
      if (row.reseller_pic) {
          returnString += `
              <br><b>PIC:</b> ${row.reseller_pic} <b>PIC Contact:</b> ${row.reseller_pic_phone}`;
      }     
      returnString += `</p></div>`;
  }

  returnString += `</div><hr>
  <div class="row">
    <!-- Machine Section -->
    <div class="col-6">
      <p><strong>Brand:</strong> ${row.brand}</p>
      <p><strong>Model:</strong> ${row.model}</p>
      <p><strong>Machine Type:</strong> ${row.machine_type}</p>
      <p><strong>Capacity:</strong> ${row.capacity}</p>
      <p><strong>Jenis Alat:</strong> ${row.jenis_alat}</p>
      <p><strong>Serial No:</strong> ${row.serial_no}</p>
      <p><strong>Assigned To:</strong> ${row.assignTo}</p>
      <p><strong>Make In:</strong> ${row.make_in}</p>
    </div>`;

  if(row.stampType == 'RENEWAL'){
    returnString += `
      <!-- Stamping Section -->
        <div class="col-6">
          <p><strong>Lama No. Daftar:</strong> ${row.no_daftar_lama}</p>
          <p><strong>Baru No. Daftar:</strong> ${row.no_daftar_baru}</p>
          <p><strong>Siri Keselamatan:</strong> ${row.siri_keselamatan}</p>
          <p><strong>Borang D:</strong> ${row.borang_d}</p>
          <p><strong>Borang E:</strong> ${row.borang_e}</p>
          <p><strong>Last Year Stamping Date:</strong> ${row.last_year_stamping_date}</p>
          <p><strong>Stamping Date:</strong> ${row.stamping_date}</p>
          <p><strong>Next Due Date:</strong> ${row.due_date}</p>
          <p><strong>Create By:</strong> ${row.create_by}</p>
          <p><strong>Last Update By:</strong> ${row.modified_by}</p>
        </div>
      </div><hr>
    `;
  }else{
    returnString += `
      <!-- Stamping Section -->
        <div class="col-6">
          <p><strong>Baru No. Daftar:</strong> ${row.no_daftar_baru}</p>
          <p><strong>Siri Keselamatan:</strong> ${row.siri_keselamatan}</p>
          <p><strong>Borang D:</strong> ${row.borang_d}</p>
          <p><strong>Stamping Date:</strong> ${row.stamping_date}</p>
          <p><strong>Next Due Date:</strong> ${row.due_date}</p>
          <p><strong>Create By:</strong> ${row.create_by}</p>
          <p><strong>Last Update By:</strong> ${row.modified_by}</p>
        </div>
      </div><hr>
    `;
  }
    
  returnString += `
  <div class="row">
    <!-- Billing Section -->
    <div class="col-6">
      <p><strong>Quotation No:</strong> ${row.quotation_no} `;
      
      if(row.quotation_attachment){
        returnString += `<span class="ml-5"><a href="view_file.php?file=${row.quotation_attachment}" target="_blank" class="btn btn-success btn-sm" role="button"><i class="fa fa-file-pdf-o"></i></a></span></p>`;
      }else{
        returnString += `</p>`;
      }

      returnString += `
      <p><strong>Quotation Date:</strong> ${row.quotation_date}</p>
      <p><strong>Purchase No:</strong> ${row.purchase_no}</p>
      <p><strong>Purchase Date:</strong> ${row.purchase_date}</p>
      <p><strong>Invoice/Cash Bill No:</strong> ${row.invoice_no}`;

      if(row.invoice_attachment){
        returnString += `<span class="ml-5"><a href="view_file.php?file=${row.invoice_attachment}" target="_blank" class="btn btn-success btn-sm" role="button"><i class="fa fa-file-pdf-o"></i></a></span></p>`;
      }else{
        returnString += `</p>`;
      }
    returnString += `</div>

    <!-- Price Section -->
    <div class="col-6">
      <p><strong>Unit Price:</strong> ${row.unit_price}</p>
      <p><strong>Cert Price:</strong> ${row.cert_price}</p>
      <p><strong>Total Amount:</strong> ${row.total_amount}</p>
      <p><strong>SST Price:</strong> ${row.sst}</p>
      <p><strong>Sub Total Price:</strong> ${row.subtotal_amount}</p>
    </div>
  </div><hr>`;

  returnString += `
  <div class="row">
    <div class="col-6">
      <p><strong>Labour Charge:</strong> ${row.labour_charge}</p>
      <p><strong>Total Stamping Fee + Labour Charge:</strong> ${row.stampfee_labourcharge}</p>
      <p><strong>Remark:</strong> ${row.remarks}</p>
    </div>
    
    <div class="col-6">
      <p><strong>Internal Round Up:</strong> ${row.int_round_up}</p>
      <p><strong>Total Billing Price:</strong> ${row.total_charges}</p>
    </div>
  </div><br>
  `;
  
  if (row.log.length > 0) {
    returnString += '<h4>Log</h4><table style="width: 100%;"><thead><tr><th width="5%">No.</th><th width="15%">Date Created</th><th>Notes</th><th width="17%">Next Follow Date</th><th width="15%">Follow Up By</th><th width="13%">Status</th></tr></thead><tbody>'
    
    for (var i = 0; i < row.log.length; i++) {
      var item = row.log[i];
      returnString += '<tr><td>' + item.no + '</td><td>' + item.date + '</td><td>' + item.notes + '</td><td>' + item.followUpDate + '</td><td>' + item.picAttend + '</td><td>' + item.status + '</td></tr>'
    }

    returnString += '</tbody></table>';
  }

  if(row.jenis_alat == 'ATP'){
    returnString += `</div><hr>
                        <p><span><strong style="font-size:120%; text-decoration: underline;">Additional Information (ATP)</strong></span>
                        <div class="row">
                          <!-- ATP Section -->
                          <div class="col-6">
                            <p><strong>Jenis Penunjuk:</strong> ${row.jenis_penunjuk}</p>
                          </div>
                        </div>
                        `;
  }else if(row.jenis_alat == 'ATN' || row.jenis_alat == 'ATN (G)'){
    returnString += `</div><hr>
                        <p><span><strong style="font-size:120%; text-decoration: underline;">Additional Information (ATN)</strong></span>
                        <div class="row">
                          <!-- ATN Section -->
                          <div class="col-6">
                            <p><strong>Jenis Alat Type:</strong> ${row.alat_type}</p>
                          </div>
                          <div class="col-6">
                            <p><strong>Bentuk Dulang:</strong> ${row.bentuk_dulang}</p>
                          </div>
                        </div>
                        `;
  }else if(row.jenis_alat == 'ATE'){
    returnString += `</div><hr>
                        <p><span><strong style="font-size:120%; text-decoration: underline;">Additional Information (ATE)</strong></span>
                        <div class="row">
                          <!-- ATE Section -->
                          <div class="col-6">
                            <p><strong>Klass:</strong> ${row.class}</p>
                          </div>
                        </div>
                        `;
  }else if(row.jenis_alat == 'BTU'){
    returnString += `</div><hr>
                      <p><span><strong style="font-size:120%; text-decoration: underline;">Additional Information (BTU)</strong></span>
                        <div class="row">
                          <!-- BTU Section -->
                          <div class="col-6">
                            <p><strong>Penandaan Pada Batu Ujian:</strong> ${row.penandaan_batu_ujian}</p>
                          </div>`;
    if (row.batu_ujian == 'OTHER'){
      returnString += `
                      <div class="col-6">
                        <p><strong>Batu Ujian:</strong> ${row.batu_ujian_lain}</p>
                      </div>
                    </div>
                    `;
    }else{
      returnString += `
                      <div class="col-6">
                        <p><strong>Batu Ujian:</strong> ${row.batu_ujian}</p>
                      </div>
                    </div>
                    `;
    }
    
  }else if(row.jenis_alat == 'ATP-AUTO MACHINE'){
    returnString += `</div><hr>
                        <p><span><strong style="font-size:120%; text-decoration: underline;">Additional Information (ATP - AUTO MACHINE)</strong></span>
                        <div class="row">
                          <!-- ATP-AUTO MACHINE Section -->
                          <div class="col-6">
                            <p><strong>Jenis Penunjuk:</strong> ${row.jenis_penunjuk}</p>
                          </div>
                        </div>
                        `;
  }else if(row.jenis_alat == 'ATP (MOTORCAR)'){
    returnString += `</div><hr>
                        <p><span><strong style="font-size:120%; text-decoration: underline;">Additional Information (ATP - MOTORCAR)</strong></span>
                        <div class="row">
                          <!-- ATS Section -->
                          <div class="col-6">
                            <p><strong>Had Terima Steelyard:</strong> ${row.steelyard} kg</p>
                          </div>
                          <div class="col-6">
                            <p><strong>Bilangan Kaunterpois:</strong> ${row.bilangan_kaunterpois} biji</p>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-12">
                            <p><strong>Nilai Berat Kaunterpois (kg)</strong></p>
                            <p><strong>(1):</strong> ${row.nilais[0]?.nilai+' kg' || ''}</p>
                            <p><strong>(2):</strong> ${row.nilais[1]?.nilai+' kg' || ''}</p>
                            <p><strong>(3):</strong> ${row.nilais[2]?.nilai+' kg' || ''}</p>
                            <p><strong>(4):</strong> ${row.nilais[3]?.nilai+' kg' || ''}</p>
                            <p><strong>(5):</strong> ${row.nilais[4]?.nilai+' kg' || ''}</p>
                            <p><strong>(6):</strong> ${row.nilais[5]?.nilai+' kg' || ''}</p>
                          </div>
                        </div>
                        
                        `;
  }else if(row.jenis_alat == 'SIA'){
    returnString += `</div><hr>
                        <p><span><strong style="font-size:120%; text-decoration: underline;">Additional Information (SIA)</strong></span>
                        <div class="row">
                          <!-- SIA Section -->`;

    if (row.nilai_jangka == 'OTHER'){
      returnString += `
                          <div class="col-6">
                            <p><strong>Nilai Jangka Maksima:</strong> ${row.nilai_jangka_other} ml</p>
                          </div>`;
    }else{
      returnString += `
                          <div class="col-6">
                            <p><strong>Nilai Jangka Maksima:</strong> ${row.nilai_jangka} ml</p>
                          </div>`;
    }

    if (row.nilai_jangka == 'OTHER'){
      returnString += `
                          <div class="col-6">
                            <p><strong>Diperbuat Daripada:</strong> ${row.diperbuat_daripada_other}</p>
                          </div>
                        </div>
                        `;
    }else{
      returnString += `
                          <div class="col-6">
                            <p><strong>Diperbuat Daripada:</strong> ${row.diperbuat_daripada}</p>
                          </div>
                        </div>
                        `;
    }
    
                          
  }else if(row.jenis_alat == 'SLL'){
    returnString += `</div><hr>
                        <div class="row">
                          <!-- SLL Section -->
                          <div class="col-6">
                            <p><strong>Jenis Alat Type:</strong> ${row.alat_type}</p>
                          </div>
                        </div>
                        `;

    if (row.questions.length > 0) {
      returnString +=`
      <div class="card card-primary">
        <div class="card-header">
          BAHAGIAN II
        </div>
        <div class="card-body">
          <div class="row mb-3 ml-4">
              <div class="col-md-8">
                  <label>1. Adakah Sukat Linar ini diperbuat dari keluli, tembaga pancalogam, aluminium, ivory, bakelait berlapis, kaca gantian yang dikukuhkan, kayu keras atau apa-apa bahan lain yang diluluskan oleh Penjimpan Timbang dan Sukat.</label>
              </div>
              <div class="col-md-3 ml-4">
                <select class="form-control select2" id="question1" name="question1" disabled>
                    <option value="" selected>${row.questions[0]['answer']}</option>
                </select>
              </div>
          </div>
          <div class="row mb-3 ml-4">
            <div class="col-md-8">
                <label>2. Adakah Sukat Linar ini lurus dan tiada kecacatan.</label>
            </div>
            <div class="col-md-3 ml-4">
              <select class="form-control select2" id="question2" name="question2" disabled>
                    <option value="" selected>${row.questions[1]['answer']}</option>
              </select>
            </div>
          </div>
          <div class="row mb-3 ml-4">
              <div class="col-md-8">
                  <label>3. Adakah Sukat Linar yang diperbuat daripada kayu, dibubuh kedua-dua hujungnya dengan logam dan hujungnya dipaku menembusi kayu itu.</label>
              </div>
              <div class="col-md-3 ml-4">
                <select class="form-control select2" id="question3" name="question3" disabled>
                    <option value="" selected>${row.questions[2]['answer']}</option>
                </select>
              </div>
          </div>
          <div class="row mb-3 ml-4">
              <div class="col-md-8">
                  <label>4. Adakah Sukat Linar bersenggat dengan jelas dan tidak boleh dipadam, dan senggatan yang dinombor ditanda dengan garisan yang lebih panjang daripada senggatan yang tidak dinombor.</label>
              </div>
              <div class="col-md-3 ml-4">
                <select class="form-control select2" id="question4" name="question4" disabled>
                    <option value="" selected>${row.questions[3]['answer']}</option>
                </select>
              </div>
          </div>
          <div class="row mb-3 ml-4">
              <div class="col-md-8">
                  <label>5.1 Adakah Sukat Linar disenggat dengan jelas dan tidak boleh dipadam dalam ukuran sentimeter di atas satu belah dan dalam sukatan meter di sebelah belakang dan senggatan yang dinombor ditanda dengan garis yang lebih panjang daripada senggatan yang tidak dinombor.</label>
              </div>
              <div class="col-md-3 ml-4">
                <select class="form-control select2" id="question5_1" name="question5_1" disabled>
                    <option value="" selected>${row.questions[4]['answer']}</option>
                </select>
              </div>
          </div>
          <div class="row mb-3 ml-4">
              <div class="col-md-8">
                  <label>5.2 Adakah Sukat itu panjangnya 1 m (satu meter)</label>
              </div>
              <div class="col-md-3 ml-4">
                <select class="form-control select2" id="question5_2" name="question5_2" disabled>
                    <option value="" selected>${row.questions[5]['answer']}</option>
                </select>
              </div>
          </div>
          <div class="row mb-3 ml-4">
              <div class="col-md-8">
                  <label>6. Adakah Sukat Linar mempunyai nilai jangkahan maksimum yang mudah dibihat, diukir dan tidak boleh dipadam ditanda di satu hujung Sukat Linar dengan cara salah satu daripada cara salah satu tanda-pertukaran-ringkas yang berikut masing-masing di bawah satu meter (cm, in, atau mm)</label>
              </div>
              <div class="col-md-3 ml-4">
                <select class="form-control select2" id="question6" name="question6" disabled>
                    <option value="" selected>${row.questions[6]['answer']}</option>
                </select>
              </div>
          </div>
          <div class="row mb-3 ml-4">
              <div class="col-md-8">
                  <label>7. Adakah Sukat Linar ini ditanda dengan cap dekat permukaan Skel pada sebelah tiap-tiap tap yang bersenggat.</label>
              </div>
              <div class="col-md-3 ml-4">
                <select class="form-control select2" id="question7" name="question7" disabled>
                    <option value="" selected>${row.questions[7]['answer']}</option>
                </select>
              </div>
          </div>
        </div>
      </div>`;
    }
  }else if(row.jenis_alat == 'BAP'){
    returnString += `</div><hr>
                        <p><span><strong style="font-size:120%; text-decoration: underline;">Additional Information (BAP)</strong></span>
                        <div class="row">
                          <!-- BAP Section -->
                          <div class="col-6">
                            <p><strong>Pam No.:</strong> ${row.pam_no}</p>
                          </div>      
                          <div class="col-6">
                            <p><strong>No Kelulusan Bentuk:</strong> ${row.kelulusan_bentuk}</p>
                          </div>      
                          <div class="col-6">
                            <p><strong>Jenis Alat:</strong> ${row.alat_type}</p>
                          </div>      
                          <div class="col-6">
                            <p><strong>Kadar Pengaliran:</strong> ${row.kadar_pengaliran} liter/min</p>
                          </div>      
                          <div class="col-6">
                            <p><strong>Bentuk Penunjuk Harga/Kuantiti:</strong> ${row.bentuk_penunjuk}</p>
                          </div>      
                    `;

    if (row.jenama == 'OTHER'){
      returnString += `
                          <div class="col-6">
                            <p><strong>Jenama / Name Pembuat:</strong> ${row.jenama_other}</p>
                          </div>`;
    }else{
      returnString += `
                          <div class="col-6">
                            <p><strong>Jenama / Name Pembuat:</strong> ${row.jenama}</p>
                          </div>`;
    }                     
  }else if(row.jenis_alat == 'SIC'){
    returnString += `</div><hr>
                        <p><span><strong style="font-size:120%; text-decoration: underline;">Additional Information (SIC)</strong></span>
                        <div class="row">
                          <!-- SIC Section -->
                          <div class="col-6">
                            <p><strong>Nilai Jangkaan Maksimum (Kapasiti):</strong> ${row.nilai_jangkaan_maksimum} Liter</p>
                          </div>      
                    `;

    if (row.bahan_pembuat == 'OTHER'){
      returnString += `
                          <div class="col-6">
                            <p><strong>Bahan Pembuat:</strong> ${row.bahan_pembuat_other}</p>
                          </div>`;
    }else{
      returnString += `
                          <div class="col-6">
                            <p><strong>Bahan Pembuat:</strong> ${row.bahan_pembuat}</p>
                          </div>`;
    }                     
  }else if(row.jenis_alat == 'BTU - (BOX)'){
    returnString += `</div><hr>
                        <p><span><strong style="font-size:120%; text-decoration: underline;">Additional Information (BTU - BOX)</strong></span>
                        <div class="row">  
                    `;

    if (row.btu_box_info.length > 0){
      var batuUjianVal = '';
      returnString += `
        <table style="width: 100%;">
          <thead>
            <tr>
              <th>No.</th>
              <th>Batu Ujian</th>
              <th>Penandaan Pada Batu Ujian</th>
            </tr>
          </thead>
          <tbody>`;

          for (i = 0; i < row.btu_box_info.length; i++) {
            returnString += `<tr><td>${row.btu_box_info[i].no}</td>`;

            if (row.btu_box_info[i].batuUjian == 'OTHER'){
              returnString += `<td>${row.btu_box_info[i].batuUjianLain}</td>`;
            }else{
              if (row.btu_box_info[i].batuUjian == 'BESI_TUANGAN'){
                batuUjianVal = 'BESI TUANGAN';
              }
              else if (row.btu_box_info[i].batuUjian == 'TEMBAGA'){
                batuUjianVal = 'TEMBAGA';
              }
              else if (row.btu_box_info[i].batuUjian == 'NIKARAT'){
                batuUjianVal = 'NIKARAT';
              }

              returnString += `<td>${batuUjianVal}</td>`;
            }

            returnString += `<td>${row.btu_box_info[i].penandaanBatuUjian}</td></tr>`;
          }
      returnString += `</tbody>
        </table>
      `;
    }                
  }else if(row.jenis_alat == 'ATK'){
    returnString += `</div><hr>
                        <p><span><strong style="font-size:120%; text-decoration: underline;">Additional Information (ATK)</strong></span>
                        <div class="row">
                          <!-- ATK Section -->
                          <div class="col-6">
                            <p><strong>Penentusan Baru:</strong> ${row.penentusan_baru}</p>
                            <p><strong>Kelulusan MSPK:</strong> ${row.kelulusan_mspk}</p>
                            <p><strong>Platform Made In:</strong> ${row.platform_country}</p>
                            <p><strong>Structure Size:</strong> ${row.size}</p>
                            <p><strong>Lain-lain Butiran:</strong> ${row.other_info}</p>
                            <p><strong>No. of Load Cells:</strong> ${row.load_cell_no}</p>
                          </div>      
                          <div class="col-6">
                            <p><strong>Penetusan Semula:</strong> ${row.penentusan_semula}</p>
                            <p><strong>No. Kelulusan MSPK:</strong> ${row.no_kelulusan}</p>
                            <p><strong>Platform Type:</strong> ${row.platform_type}</p>
                            <p><strong>Jenis Pelantar:</strong> ${row.jenis_pelantar}</p>
                            <p><strong>Load Cells Made In:</strong> ${row.load_cell_country}</p>
                          </div>      
                        </div>
                        <div class="row">
                          <table style="width: 100%;">
                            <thead>
                              <tr>
                                <th>No.</th>
                                <th>Load Cells Type</th>
                                <th>Brand</th>
                                <th>Model</th>
                                <th>Load Cell Capacity</th>
                                <th>Serial No</th>
                              </tr>
                            </thead>
                            <tbody>`;

                            for (i = 0; i < row.load_cells_info.length; i++) {
                              returnString += 
                                `<tr>
                                  <td>${row.load_cells_info[i].no}</td>
                                  <td>${row.load_cells_info[i].loadCells}</td>
                                  <td>${row.load_cells_info[i].loadCellBrand}</td>
                                  <td>${row.load_cells_info[i].loadCellModel}</td>
                                  <td>${row.load_cells_info[i].loadCellCapacity}</td>
                                  <td>${row.load_cells_info[i].loadCellSerial}</td>
                                </tr>`;
                            }

                        returnString += `</tbody></table></div>`;                   
  }

  return returnString;
}

function formatNormal (row) {
  return '<div class="row"><div class="col-md-3"><p>Customer Name: '+row.customer_name+
  '</p></div><div class="col-md-3"><p>Unit Weight: '+row.unit+
  '</p></div><div class="col-md-3"><p>Weight Status: '+row.status+
  '</p></div><div class="col-md-3"><p>MOQ: '+row.moq+
  '</p></div></div><div class="row"><div class="col-md-3"><p>Address: '+row.customer_address+
  '</p></div><div class="col-md-3"><p>Batch No: '+row.batchNo+
  '</p></div><div class="col-md-3"><p>Weight By: '+row.userName+
  '</p></div><div class="col-md-3"><p>Package: '+row.packages+
  '</p></div></div><div class="row"><div class="col-md-3">'+
  '</div><div class="col-md-3"><p>Lot No: '+row.lots_no+
  '</p></div><div class="col-md-3"><p>Invoice No: '+row.invoiceNo+
  '</p></div><div class="col-md-3"><p>Unit Price: '+row.unitPrice+
  '</p></div></div><div class="row"><div class="col-md-3">'+
  '</div><div class="col-md-3"><p>Order Weight: '+row.supplyWeight+
  '</p></div><div class="col-md-3"><p>Delivery No: '+row.deliveryNo+
  '</p></div><div class="col-md-3"><p>Total Weight: '+row.totalPrice+
  '</p></div></div><div class="row"><div class="col-md-3"><p>Contact No: '+row.customer_phone+
  '</p></div><div class="col-md-3"><p>Variance Weight: '+row.varianceWeight+
  '</p></div><div class="col-md-3"><p>Purchase No: '+row.purchaseNo+
  '</p></div><div class="col-md-3"><div class="row"><div class="col-3"><button type="button" class="btn btn-warning btn-sm" onclick="edit('+row.id+
  ')"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" class="btn btn-info btn-sm" onclick="print('+row.id+
  ')"><i class="fas fa-print"></i></button></div><div class="col-3"><button type="button" class="btn btn-success btn-sm" onclick="portrait('+row.id+
  ')"><i class="fas fa-receipt"></i></button></div></div></div></div>'+
  '</div><div class="row"><div class="col-md-3"><p>Remark: '+row.remark+
  '</p></div><div class="col-md-3"><p>% Variance: '+row.variancePerc+
  '</p></div><div class="col-md-3"><p>Transporter: '+row.transporter_name+
  '</p></div></div>';
}

function formatDate(convert1) {
  convert1 = convert1.replace(":", "/");
  convert1 = convert1.replace(":", "/");
  convert1 = convert1.replace(" ", "/");
  convert1 = convert1.replace(" pm", "");
  convert1 = convert1.replace(" am", "");
  convert1 = convert1.replace(" PM", "");
  convert1 = convert1.replace(" AM", "");
  var convert2 = convert1.split("/");
  var date  = new Date(convert2[2], convert2[1] - 1, convert2[0], convert2[3], convert2[4], convert2[5]);
  return date
}

function newEntry(){
  var date = new Date();

  $('#extendModal').find('#id').val("");
  $('#extendModal').find('#customerType').val("EXISTING").attr('readonly', false).trigger('change');
  $('#extendModal').find('#brand').val('').trigger('change');
  $('#extendModal').find('#newRenew').val('NEW');
  $('#extendModal').find('#validator').val('').trigger('change');
  $('#extendModal').find('#product').val('').trigger('change');
  $('#extendModal').find('#company').val('');
  $('#extendModal').find('#companyText').val('').trigger('change');
  $('#extendModal').find('#machineType').val('').trigger('change');
  $('#extendModal').find('#jenisAlat').val('').trigger('change');
  $('#extendModal').find('#address1').val('');
  $('#extendModal').find('#model').val("").trigger('change');
  $('#extendModal').find('#stampDate').val('');
  $('#extendModal').find('#address2').val('');
  $('#extendModal').find('#capacity').val('').trigger('change');
  $('#extendModal').find('#noDaftar').val('');
  $('#extendModal').find('#address3').val('');
  $('#extendModal').find('#serial').val('');
  $('#extendModal').find('#pinKeselamatan').val('');
  $('#extendModal').find('#attnTo').val('<?=$user ?>');
  $('#extendModal').find('#siriKeselamatan').val('');
  $('#extendModal').find('#pic').val("");
  $('#extendModal').find('#borangD').val("");
  $('#extendModal').find('#remark').val("");
  $('#extendModal').find('#dueDate').val('');
  $('#extendModal').find('#quotation').val("");
  $('#extendModal').find('#quotationDate').val('');
  $('#extendModal').find('#includeCert').val("NO").trigger('change');
  $('#extendModal').find('#poNo').val("");
  $('#extendModal').find('#poDate').val('');
  $('#extendModal').find('#cashBill').val("");
  $('#extendModal').find('#invoice').val('');

  $('#pricingTable').html('');
  pricingCount = 0;
  $('#extendModal').find('#unitPrice').val("");
  $('#extendModal').find('#certPrice').val('');
  $('#extendModal').find('#totalAmount').val("");
  $('#extendModal').find('#sst').val('');
  $('#extendModal').find('#subAmount').val('');
  $('#cerId').hide();
  $('#extendModal').modal('show');
  
  $('#extendForm').validate({
    errorElement: 'span',
    errorPlacement: function (error, element) {
      error.addClass('invalid-feedback');
      element.closest('.form-group').append(error);
    },
    highlight: function (element, errorClass, validClass) {
      $(element).addClass('is-invalid');
    },
    unhighlight: function (element, errorClass, validClass) {
      $(element).removeClass('is-invalid');
    }
  });
}

function edit(id) {
  $('#spinnerLoading').show();
  $.post('php/getStamp.php', {userID: id}, function(data){
    var obj = JSON.parse(data);
    
    if(obj.status === 'success'){
      $('#extendModal').find('#id').val(obj.message.id);
      $('#extendModal').find('#customerType').val(obj.message.customer_type).attr('readonly', true).trigger('change');
      $('#extendModal').find('#brand').val(obj.message.brand).trigger('change');
      $('#extendModal').find('#validator').val(obj.message.validate_by).trigger('change');
      $('#extendModal').find('#company').val(obj.message.customers).trigger('change');
      $('#extendModal').find('#newRenew').val(obj.message.stampType);
      $('#extendModal').find('#companyText').val('');
      $('#extendModal').find('#product').val(obj.message.products).trigger('change');
      $('#extendModal').find('#machineType').val(obj.message.machine_type).trigger('change');
      $('#extendModal').find('#jenisAlat').val(obj.message.jenis_alat).trigger('change');
      $('#extendModal').find('#address1').val(obj.message.address1);
      $('#extendModal').find('#model').val(obj.message.model).trigger('change');
      $('#extendModal').find('#stampDate').val(formatDate3(obj.message.stamping_date));
      $('#extendModal').find('#address2').val(obj.message.address2);
      $('#extendModal').find('#capacity').val(obj.message.capacity).trigger('change');
      $('#extendModal').find('#noDaftar').val(obj.message.no_daftar);
      $('#extendModal').find('#address3').val(obj.message.address3);
      $('#extendModal').find('#serial').val(obj.message.serial_no);
      $('#extendModal').find('#pinKeselamatan').val(obj.message.pin_keselamatan);
      $('#extendModal').find('#attnTo').val(obj.message.pic);
      $('#extendModal').find('#siriKeselamatan').val(obj.message.siri_keselamatan);
      $('#extendModal').find('#pic').val(obj.message.pic);
      $('#extendModal').find('#borangD').val(obj.message.borang_d);
      $('#extendModal').find('#remark').val(obj.message.remarks);
      $('#extendModal').find('#dueDate').val(formatDate3(obj.message.due_date));
      $('#extendModal').find('#quotation').val(obj.message.quotation_no);
      $('#extendModal').find('#quotationDate').val(formatDate3(obj.message.quotation_date));
      $('#extendModal').find('#includeCert').val(obj.message.include_cert).trigger('change');
      $('#extendModal').find('#poNo').val(obj.message.purchase_no);
      $('#extendModal').find('#poDate').val(formatDate3(obj.message.purchase_date));
      $('#extendModal').find('#cashBill').val(obj.message.cash_bill);
      $('#extendModal').find('#invoice').val(obj.message.invoice_no);
      $('#extendModal').find('#unitPrice').val(obj.message.unit_price);
      $('#extendModal').find('#certPrice').val(obj.message.cert_price);
      $('#extendModal').find('#totalAmount').val(obj.message.total_amount);
      $('#extendModal').find('#sst').val(obj.message.sst);
      $('#extendModal').find('#subAmount').val(obj.message.subtotal_amount);

      $('#pricingTable').html('');
      pricingCount = 0;
      

      if(obj.message.log.length > 0){
        for(var i = 0; i < obj.message.log.length; i++){
          var item = obj.message.log[i];
          var $addContents = $("#pricingDetails").clone();
          $("#pricingTable").append($addContents.html());

          $("#pricingTable").find('.details:last').attr("id", "detail" + pricingCount);
          $("#pricingTable").find('.details:last').attr("data-index", pricingCount);
          //$("#pricingTable").find('#remove:last').attr("id", "remove" + pricingCount);

          $("#pricingTable").find('#no:last').attr('name', 'no['+pricingCount+']').attr("id", "no" + pricingCount).val(item.no);
          $("#pricingTable").find('#date:last').attr('name', 'date['+pricingCount+']').attr("id", "date" + pricingCount).val(item.date);
          $("#pricingTable").find('#notes:last').attr('name', 'notes['+pricingCount+']').attr("id", "notes" + pricingCount).val(item.notes);
          $("#pricingTable").find('#followUpDate:last').attr('name', 'followUpDate['+pricingCount+']').attr("id", "followUpDate" + pricingCount).val(item.followUpDate);
          $("#pricingTable").find('#picAttend:last').attr('name', 'picAttend['+pricingCount+']').attr("id", "picAttend" + pricingCount).val(item.picAttend);
          $("#pricingTable").find('#status').attr('name', 'status['+pricingCount+']').attr("id", "status" + pricingCount).val('Pending').val(item.status);

          var newDatePickerId = "datePicker5" + pricingCount;

          // Find the newly added date input and set the new ID
          var $newDateInputGroup = $("#pricingTable").find('#datePicker5:last');
          $newDateInputGroup.attr("id", newDatePickerId);
          $newDateInputGroup.find('input').attr("data-target", "#" + newDatePickerId);
          $newDateInputGroup.find('.input-group-append').attr("data-target", "#" + newDatePickerId);

          // Initialize the date picker on the new element
          $newDateInputGroup.datetimepicker({
            icons: { time: 'far fa-calendar' },
            format: 'DD/MM/YYYY'
          });

          pricingCount++;
        }
      }


      
      $('#extendModal').modal('show');

      $('#extendForm').validate({
        errorElement: 'span',
        errorPlacement: function (error, element) {
          error.addClass('invalid-feedback');
          element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
          $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
          $(element).removeClass('is-invalid');
        }
      });
    }
    else if(obj.status === 'failed'){
      toastr["error"](obj.message, "Failed:");
    }
    else{
      toastr["error"]("Something wrong when pull data", "Failed:");
    }
    $('#spinnerLoading').hide();
  });
}

function complete(id) {
  if (confirm('Are you sure you want to complete this items?')) {
    $('#spinnerLoading').show();
    $.post('php/completeStamp.php', {userID: id}, function(data){
      var obj = JSON.parse(data);

      if(obj.status === 'success'){
        toastr["success"](obj.message, "Success:");
        $('#weightTable').DataTable().ajax.reload();
      }
      else if(obj.status === 'failed'){
        toastr["error"](obj.message, "Failed:");
      }
      else{
        toastr["error"]("Something wrong when activate", "Failed:");
      }
      $('#spinnerLoading').hide();
    });
  }
}

function deactivate(id) {
  if (confirm('Are you sure you want to cancel this item?')) {
    $('#spinnerLoading').show();
    $.post('php/deleteStamp.php', {userID: id}, function(data){
      var obj = JSON.parse(data);

      if(obj.status === 'success'){
        toastr["success"](obj.message, "Success:");
        $('#weightTable').DataTable().ajax.reload();
      }
      else if(obj.status === 'failed'){
        toastr["error"](obj.message, "Failed:");
      }
      else{
        toastr["error"]("Something wrong when activate", "Failed:");
      }
      $('#spinnerLoading').hide();
    });
  }
}

function print(id) {
  $.post('php/print.php', {userID: id, file: 'weight'}, function(data){
    var obj = JSON.parse(data);

    if(obj.status === 'success'){
      var printWindow = window.open('', '', 'height=400,width=800');
      printWindow.document.write(obj.message);
      printWindow.document.close();
      setTimeout(function(){
        printWindow.print();
        printWindow.close();
      }, 500);

      /*$.get('weightPage.php', function(data) {
        $('#mainContents').html(data);
      });*/
    }
    else if(obj.status === 'failed'){
      toastr["error"](obj.message, "Failed:");
    }
    else{
      toastr["error"]("Something wrong when activate", "Failed:");
    }
  });
}

function portrait(id) {
  $.post('php/printportrait.php', {userID: id, file: 'weight'}, function(data){
    var obj = JSON.parse(data);

    if(obj.status === 'success'){
      var printWindow = window.open('', '', 'height=400,width=800');
      printWindow.document.write(obj.message);
      printWindow.document.close();
      setTimeout(function(){
        printWindow.print();
        printWindow.close();
      }, 500);
    }
    else if(obj.status === 'failed'){
      toastr["error"](obj.message, "Failed:");
    }
    else{
      toastr["error"]("Something wrong when activate", "Failed:");
    }
  });
}

function displayPreview(data) {
  // Parse the Excel data
  var workbook = XLSX.read(data, { type: 'binary' });

  // Get the first sheet
  var sheetName = workbook.SheetNames[0];
  var sheet = workbook.Sheets[sheetName];

  // Convert the sheet to an array of objects
  var jsonData = XLSX.utils.sheet_to_json(sheet, { header: 1 });

  // Get the headers
  var headers = jsonData[0];

  // Ensure we handle cases where there may be less than 15 columns
  while (headers.length < 15) {
    headers.push(''); // Adding empty headers to reach 15 columns
  }

  // Create HTML table headers
  var htmlTable = '<table style="width:100%;"><thead><tr>';
  headers.forEach(function(header) {
      htmlTable += '<th>' + header + '</th>';
  });
  htmlTable += '</tr></thead><tbody>';

  // Iterate over the data and create table rows
  for (var i = 1; i < jsonData.length; i++) {
      htmlTable += '<tr>';
      var rowData = jsonData[i];

      // Ensure we handle cases where there may be less than 15 cells in a row
      while (rowData.length < 15) {
        rowData.push(''); // Adding empty cells to reach 15 columns
      }

      for (var j = 0; j < 15; j++) {
        var cellData = rowData[j];
        var formattedData = cellData;

        // Check if cellData is a valid Excel date serial number and format it to DD/MM/YYYY
        if (typeof cellData === 'number' && cellData > 0) {
            var excelDate = XLSX.SSF.parse_date_code(cellData);
            if (excelDate) {
                formattedData = formatDate2(new Date(excelDate.y, excelDate.m - 1, excelDate.d));
            }
        }

        htmlTable += '<td><input type="text" id="'+headers[j]+(i-1)+'" name="'+headers[j]+'['+(i-1)+']" value="' + (formattedData == null ? '' : formattedData) + '" /></td>';
      }
      htmlTable += '</tr>';
  }

  htmlTable += '</tbody></table>';

  var previewTable = document.getElementById('previewTable');
  previewTable.innerHTML = htmlTable;
}

</script>
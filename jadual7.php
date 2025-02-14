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
                  <th width="8%">BRG E BIL NO.</th>
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
        data: 'borang_e', 
        name: 'borang_e'
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
      row.child( format(row.data()) ).show();tr.addClass("shown");
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
          data: 'borang_e', 
          name: 'borang_e'
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

    var fromDateValue = $('#fromDate').val();
    var toDateValue = $('#toDate').val();
    var customerNoFilter = $('#customerNoFilter').val() ? $('#customerNoFilter').val() : '';
    
    // $.post('php/export_borang.php', {"driver": "7", "fromDate": fromDateValue, "toDate": toDateValue, "customer": customerNoFilter}, function(data){
    $.post('php/export_borang.php', {"ids": selectedIds, "driver": "7"}, function(data){
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
  var returnString = `
  <div class="row">
    <!-- Customer Section -->
    <div class="col-md-6">
      <p><span><strong style="font-size:120%; text-decoration: underline;">Customer</strong></span><br>
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
      <p><span><strong style="font-size:120%; text-decoration: underline;">Reseller</strong></span><br>
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
    </div>

    <!-- Stamping Section -->
    <div class="col-6">
      <p><strong>Lama No. Daftar:</strong> ${row.no_daftar_lama}</p>
      <p><strong>Baru No. Daftar:</strong> ${row.no_daftar_baru}</p>
      <p><strong>Siri Keselamatan:</strong> ${row.siri_keselamatan}</p>
      <p><strong>Borang D:</strong> ${row.borang_d}</p>
      <p><strong>Borang E:</strong> ${row.borang_e}</p>
      <p><strong>Stamping Date:</strong> ${row.stamping_date}</p>
      <p><strong>Due Date:</strong> ${row.due_date}</p>
    </div>
  </div><hr>

  <div class="row">
    <!-- Billing Section -->
    <div class="col-6">
      <p><strong>Quotation No:</strong> ${row.quotation_no}</p>
      <p><strong>Quotation Date:</strong> ${row.quotation_date}</p>
      <p><strong>Purchase No:</strong> ${row.purchase_no}</p>
      <p><strong>Purchase Date:</strong> ${row.purchase_date}</p>
      <p><strong>Invoice/Cash Bill No:</strong> ${row.invoice_no}</p>
    </div>

    <!-- Price Section -->
    <div class="col-6">
      <p><strong>Unit Price:</strong> ${row.unit_price}</p>
      <p><strong>Cert Price:</strong> ${row.cert_price}</p>
      <p><strong>Total Amount:</strong> ${row.total_amount}</p>
      <p><strong>SST Price:</strong> ${row.sst}</p>
      <p><strong>Sub Total Price:</strong> ${row.subtotal_amount}</p>
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

  // Additional section for ATS
  if (row.jenis_alat == 'ATS'){
    returnString += `</div><hr>
                        <div class="row">
                          <!-- ATS Section -->
                          <div class="col-6">
                            <p><strong>Platform Made In:</strong> ${row.platform_country}</p>
                          </div>
                        </div>
                        `;
  }else if(row.jenis_alat == 'ATP'){
    returnString += `</div><hr>
                        <div class="row">
                          <!-- ATS Section -->
                          <div class="col-6">
                            <p><strong>Platform Made In:</strong> ${row.platform_country}</p>
                          </div>
                          <div class="col-6">
                            <p><strong>Jenis Penunjuk:</strong> ${row.jenis_penunjuk}</p>
                          </div>
                        </div>
                        `;
  }else if(row.jenis_alat == 'ATN'){
    returnString += `</div><hr>
                        <div class="row">
                          <!-- ATS Section -->
                          <div class="col-6">
                            <p><strong>Platform Made In:</strong> ${row.platform_country}</p>
                          </div>
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
                        <div class="row">
                          <!-- ATS Section -->
                          <div class="col-6">
                            <p><strong>Platform Made In:</strong> ${row.platform_country}</p>
                          </div>
                          <div class="col-6">
                            <p><strong>Klass:</strong> ${row.class}</p>
                          </div>
                        </div>
                        `;
  }else if(row.jenis_alat == 'SLL'){
    returnString += `</div><hr>
                        <div class="row">
                          <!-- ATS Section -->
                          <div class="col-6">
                            <p><strong>Platform Made In:</strong> ${row.platform_country}</p>
                          </div>
                          <div class="col-6">
                            <p><strong>Jenis Alat Type:</strong> ${row.alat_type}</p>
                          </div>
                        </div>
                        `;

    if (row.questions.length > 0) {
      returnString += '<h4>BAHAGIAN II</h4><table style="width: 100%;"><thead><tr><th width="5%">No.</th><th width="15%">Date Created</th><th>Notes</th><th width="17%">Next Follow Date</th><th width="15%">Follow Up By</th><th width="13%">Status</th></tr></thead><tbody>'
    
      for (var i = 0; i < row.log.length; i++) {
        var item = row.log[i];
        returnString += '<tr><td>' + item.no + '</td><td>' + item.date + '</td><td>' + item.notes + '</td><td>' + item.followUpDate + '</td><td>' + item.picAttend + '</td><td>' + item.status + '</td></tr>'
      }

      returnString += '</tbody></table>';
    }
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
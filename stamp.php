<?php
require_once 'php/db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
  echo '<script type="text/javascript">';
  echo 'window.location.href = "login.html";</script>';
}
else{
  $user = $_SESSION['userID'];
  $stmt = $db->prepare("SELECT * from users where id = ?");
	$stmt->bind_param('s', $user);
	$stmt->execute();
	$result = $stmt->get_result();
  $role = 'NORMAL';
	
	if(($row = $result->fetch_assoc()) !== null){
    $role = $row['role_code'];
  }

  $customers = $db->query("SELECT * FROM customers WHERE customer_status = 'CUSTOMERS' AND deleted = '0'");
  $customers2 = $db->query("SELECT * FROM customers WHERE customer_status = 'CUSTOMERS' AND deleted = '0'");
  $machinetypes = $db->query("SELECT * FROM machines WHERE deleted = '0'");
  $brands = $db->query("SELECT * FROM brand WHERE deleted = '0'");
  $models = $db->query("SELECT * FROM model WHERE deleted = '0'");
  $sizes = $db->query("SELECT * FROM size WHERE deleted = '0'");
  $capacities = $db->query("SELECT * FROM capacity WHERE deleted = '0'");
  $problems = $db->query("SELECT * FROM problem WHERE deleted = '0'");
  $users = $db->query("SELECT * FROM users WHERE deleted = '0'");
  $validators = $db->query("SELECT * FROM validators WHERE deleted = '0'");
}
?>

<style>
  @media screen and (min-width: 676px) {
    .modal-dialog {
      max-width: 1800px; /* New width for default modal */
    }
  }
</style>

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Stamping</h1>
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

    <div class="row">
      <div class="col-lg-12">
        <div class="card card-primary">
          <div class="card-header">
            <div class="row">
              <div class="col-4"></div>
              <div class="col-2">
                <button type="button" class="btn btn-block bg-gradient-info btn-sm" id="exportBorangs">Export Borangs</button>
              </div>
              <div class="col-2">
                <a href="/template/Stamping Record Template.xlsx" download><button type="button" class="btn btn-block bg-gradient-danger btn-sm" id="downloadExccl">Download Template</button></a>
              </div>
              <div class="col-2">
                <button type="button" class="btn btn-block bg-gradient-success btn-sm" id="uploadExccl">Upload Excel</button>
              </div>
              <div class="col-2">
                <button type="button" class="btn btn-block bg-gradient-warning btn-sm" onclick="newEntry()">Add New Stamping</button>
              </div>
            </div>
          </div>

          <div class="card-body">
            <table id="weightTable" class="table table-bordered table-striped display">
              <thead>
                <tr>
                  <th>Customers</th>
                  <th>Brands</th>
                  <th>Desc</th>
                  <th>Model</th>
                  <th>Capacity</th>
                  <th>Serial No.</th>
                  <th>Next Due Date</th>
                  <th>Status</th>
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

<div class="modal fade" id="extendModal">
  <div class="modal-dialog modal-xl" style="max-width: 90%;">
    <div class="modal-content">
      <form role="form" id="extendForm">
        <div class="modal-header bg-gray-dark color-palette">
          <h4 class="modal-title">Add New Stamping</h4>
          <button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body" >
          <input type="hidden" class="form-control" id="id" name="id">

          <div class="row">
            <div class="col-3">
              <div class="form-group">
                <label>Customer * </label>
                <select class="form-control" style="width: 100%;" id="customer" name="customer" required>
                  <option value="" selected disabled hidden>Please Select</option>
                  <?php while($rowCustomer=mysqli_fetch_assoc($customers)){ ?>
                    <option value="<?=$rowCustomer['id'] ?>"><?=$rowCustomer['customer_name'] ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-3">
              <div class="form-group">
                <label>Brand *</label>
                <select class="form-control" style="width: 100%;" id="brand" name="brand" required>
                  <option selected="selected">-</option>
                  <?php while($rowB=mysqli_fetch_assoc($brands)){ ?>
                    <option value="<?=$rowB['id'] ?>"><?=$rowB['brand'] ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-3">
              <div class="form-group">
                <label>Model *</label>
                <select class="form-control" style="width: 100%;" id="model" name="model" required>
                  <option selected="selected">-</option>
                  <?php while($rowM=mysqli_fetch_assoc($models)){ ?>
                    <option value="<?=$rowM['id'] ?>"><?=$rowM['model'] ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-3">
              <div class="form-group">
                <label>Machine Type *</label>
                <select class="form-control" style="width: 100%;" id="machineType" name="machineType" required>
                  <option selected="selected">-</option>
                  <?php while($rowS=mysqli_fetch_assoc($machinetypes)){ ?>
                    <option value="<?=$rowS['id'] ?>"><?=$rowS['machine_type'] ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-3">
              <div class="form-group">
                <label>Capacity * </label>
                <select class="form-control" style="width: 100%;" id="capacity" name="capacity" required>
                  <option selected="selected">-</option>
                  <?php while($rowCA=mysqli_fetch_assoc($capacities)){ ?>
                    <option value="<?=$rowCA['id'] ?>"><?=$rowCA['capacity'] ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-3">
              <div class="form-group">
                <label>Validator * </label>
                <select class="form-control" style="width: 100%;" id="validator" name="validator" required>
                  <option selected="selected">-</option>
                  <?php while($rowVA=mysqli_fetch_assoc($validators)){ ?>
                    <option value="<?=$rowVA['id'] ?>"><?=$rowVA['validator'] ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-3">
              <div class="form-group">
                <label>Serial No * </label>
                <input class="form-control" type="text" placeholder="Serial No." id="serial" name="serial" required>
              </div>
            </div>
            <div class="col-3">
              <div class="form-group">
                <label>Stamping No.</label>
                <input class="form-control" type="text" placeholder="Stamping No." id="stamping" name="stamping">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-3">
              <div class="form-group">
                <label>Stamp Date</label>
                <div class='input-group date' id="datePicker" data-target-input="nearest">
                  <input type='text' class="form-control datetimepicker-input" data-target="#datePicker" id="stampDate" name="stampDate"/>
                  <div class="input-group-append" data-target="#datePicker" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-3">
              <div class="form-group">
                <label>Next Due Date *</label>
                <div class='input-group date' id="datePicker2" data-target-input="nearest">
                  <input type='text' class="form-control datetimepicker-input" data-target="#datePicker2" id="dueDate" name="dueDate" required/>
                  <div class="input-group-append" data-target="#datePicker2" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-3">
              <div class="form-group">
                <label>Invoice No.</label>
                <input class="form-control" type="text" placeholder="Invoice No" id="invoice" name="invoice">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-3">
              <div class="form-group">
                <label>PIC</label>
                <input class="form-control" type="text" placeholder="PIC" id="pic" name="pic">
              </div>
            </div>
            <div class="col-3">
              <div class="form-group">
                <label>Follow Up Date</label>
                <div class='input-group date' id="datePicker3" data-target-input="nearest">
                  <input type='text' class="form-control datetimepicker-input" data-target="#datePicker3" id="followUpDate" name="followUpDate"/>
                  <div class="input-group-append" data-target="#datePicker3" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-3">
              <div class="form-group">
                <label>Quotation/PO No.</label>
                <input class="form-control" type="text" placeholder="PO No" id="quotation" name="quotation">
              </div>
            </div>
            <div class="col-3">
              <div class="form-group">
                <label>Remark</label>
                <textarea class="form-control" type="text" placeholder="Remark" id="remark" name="remark"></textarea>
              </div>
            </div>
          </div>
        </div>

        <div class="modal-footer justify-content-between bg-gray-dark color-palette">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="saveButton">Save</button>
        </div>
      </form>
    </div> <!-- /.modal-content -->
  </div> <!-- /.modal-dialog -->
</div> <!-- /.modal -->

<div class="modal fade" id="uploadModal">
  <div class="modal-dialog modal-xl" style="max-width: 90%;">
    <div class="modal-content">
      <form role="form" id="uploadForm">
        <div class="modal-header bg-gray-dark color-palette">
          <h4 class="modal-title">Upload Excel File</h4>
          <button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="file" id="fileInput">
          <button id="previewButton">Preview Data</button>
          <div id="previewTable" style="overflow: auto;"></div>
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
// Values
$(function () {
  $('#customerNoHidden').hide();
  $('#problems').select2();

  var table = $("#weightTable").DataTable({
    "responsive": true,
    "autoWidth": false,
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
    'searching': false,
    'order': [[ 1, 'asc' ]],
    'columnDefs': [ { orderable: false, targets: [0] }],
    'ajax': {
        'url':'php/loadStamping.php'
    },
    'columns': [
      { data: 'customer_name' },
      { data: 'brand' },
      { data: 'machine_type' },
      { data: 'model' },
      { data: 'capacity' },
      { data: 'serial_no' },
      { data: 'due_date' },
      { data: 'status' },
      { 
        data: 'id',
        render: function ( data, type, row ) {
          return '<div class="row"><div class="col-12"><button type="button" id="edit'+data+'" onclick="edit('+data+')" class="btn btn-success btn-sm"><i class="fas fa-pen"></i></button></div></div>';
        }
      }
    ],
    /*"rowCallback": function( row, data, index ) {
      $('td', row).css('background-color', '#E6E6FA');
    },
    "drawCallback": function(settings) {
      $('#salesInfo').text(settings.json.salesTotal);
      $('#purchaseInfo').text(settings.json.purchaseTotal);
      $('#localInfo').text(settings.json.localTotal);
    }*/
  });

  // Add event listener for opening and closing details
  /*$('#weightTable tbody').on('click', 'td.dt-control', function () {
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
  });*/
  
  //Date picker
  $('#fromDate').datetimepicker({
    icons: { time: 'far fa-clock' },
    format: 'DD/MM/YYYY'
  });

  $('#toDate').datetimepicker({
    icons: { time: 'far fa-clock' },
    format: 'DD/MM/YYYY'
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
    }
  });

  $('#filterSearch').on('click', function(){
    //$('#spinnerLoading').show();

    var fromDateValue = $('#fromDate').val();
    var toDateValue = $('#toDate').val();
    var customerNoFilter = $('#customerNoFilter').val() ? $('#customerNoFilter').val() : '';

    //Destroy the old Datatable
    $("#weightTable").DataTable().clear().destroy();

    //Create new Datatable
    table = $("#weightTable").DataTable({
      "responsive": true,
      "autoWidth": false,
      'processing': true,
      'serverSide': true,
      'serverMethod': 'post',
      'searching': false,
      'order': [[ 1, 'asc' ]],
      'columnDefs': [ { orderable: false, targets: [0] }],
      'ajax': {
        'type': 'POST',
        'url':'php/filterStamping.php',
        'data': {
          fromDate: fromDateValue,
          toDate: toDateValue,
          customer: customerNoFilter,
        } 
      },
      'columns': [
        { data: 'customer_name' },
        { data: 'brand' },
        { data: 'machine_type' },
        { data: 'model' },
        { data: 'capacity' },
        { data: 'serial_no' },
        { data: 'due_date' },
        { data: 'status' },
        { 
          data: 'id',
          render: function ( data, type, row ) {
            return '<div class="row"><div class="col-12"><button type="button" id="edit'+data+'" onclick="edit('+data+')" class="btn btn-success btn-sm"><i class="fas fa-pen"></i></button></div></div>';
          }
        }
      ],
    });
  });

  /*$('#refreshBtn').on('click', function(){
    var fromDateValue = '';
    var toDateValue = '';
    var statusFilter = '';
    var customerNoFilter = '';
    var vehicleFilter = '';
    var invoiceFilter = '';
    var batchFilter = '';
    var productFilter = '';

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
      'order': [[ 1, 'asc' ]],
      'columnDefs': [ { orderable: false, targets: [0] }],
      'ajax': {
        'type': 'POST',
        'url':'php/filterWeight.php',
        'data': {
          fromDate: fromDateValue,
          toDate: toDateValue,
          status: statusFilter,
          customer: customerNoFilter,
          vehicle: vehicleFilter,
          invoice: invoiceFilter,
          batch: batchFilter,
          product: productFilter,
        } 
      },
      'columns': [
        { data: 'no' },
        { data: 'pStatus' },
        { data: 'status' },
        { data: 'serialNo' },
        { data: 'veh_number' },
        { data: 'product_name' },
        { data: 'currentWeight' },
        { data: 'inCDateTime' },
        { data: 'tare' },
        { data: 'outGDateTime' },
        { data: 'totalWeight' },
        { 
          className: 'dt-control',
          orderable: false,
          data: null,
          render: function ( data, type, row ) {
            return '<td class="table-elipse" data-toggle="collapse" data-target="#demo'+row.serialNo+'"><i class="fas fa-angle-down"></i></td>';
          }
        }
      ],
      "rowCallback": function( row, data, index ) {
        $('td', row).css('background-color', '#E6E6FA');
      },
      "drawCallback": function(settings) {
        $('#salesInfo').text(settings.json.salesTotal);
        $('#purchaseInfo').text(settings.json.purchaseTotal);
        $('#localInfo').text(settings.json.localTotal);
      }
    });
  });

  $('#datePicker').on('click', function () {
    $('#datePicker').attr('data-info', '1');
  });*/

  $('#uploadExccl').on('click', function(){
    $('#uploadModal').modal('show');

    $('#uploadForm').validate({
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
  });

  $('#uploadModal').find('#previewButton').on('click', function(){
    var fileInput = document.getElementById('fileInput');
    var file = fileInput.files[0];
    var reader = new FileReader();
    
    reader.onload = function(e) {
      var data = e.target.result;
      // Process data and display preview
      displayPreview(data);
    };

    reader.readAsBinaryString(file);
  });
});

function format (row) {
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
  '</p></div><div class="col-md-3 money"><p>Unit Price: '+row.unitPrice+
  '</p></div></div><div class="row"><div class="col-md-3">'+
  '</div><div class="col-md-3"><p>Order Weight: '+row.supplyWeight+
  '</p></div><div class="col-md-3"><p>Delivery No: '+row.deliveryNo+
  '</p></div><div class="col-md-3 money"><p>Total Weight: '+row.totalPrice+
  '</p></div></div><div class="row"><div class="col-md-3"><p>Contact No: '+row.customer_phone+
  '</p></div><div class="col-md-3"><p>Variance Weight: '+row.varianceWeight+
  '</p></div><div class="col-md-3"><p>Purchase No: '+row.purchaseNo+
  '</p></div><div class="col-md-3"><div class="row"><div class="col-3"><button type="button" class="btn btn-warning btn-sm" onclick="edit('+row.id+
  ')"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" class="btn btn-danger btn-sm" onclick="deactivate('+row.id+
  ')"><i class="fas fa-trash"></i></button></div><div class="col-3"><button type="button" class="btn btn-info btn-sm" onclick="print('+row.id+
  ')"><i class="fas fa-print"></i></button></div><div class="col-3"><button type="button" class="btn btn-success btn-sm" onclick="portrait('+row.id+
  ')"><i class="fas fa-receipt"></i></button></div></div></div></div>'+
  '</div><div class="row"><div class="col-md-3"><p>Remark: '+row.remark+
  '</p></div><div class="col-md-3"><p>% Variance: '+row.variancePerc+
  '</p></div><div class="col-md-3"><p>Transporter: '+row.transporter_name+
  '</p></div></div>';
  ;
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

  $('#datePicker').datetimepicker({
    icons: { time: 'fa fa-calendar' },
    format: 'DD/MM/YYYY'
  });

  $('#datePicker2').datetimepicker({
    icons: { time: 'fa fa-calendar' },
    format: 'DD/MM/YYYY'
  });

  $('#datePicker3').datetimepicker({
    icons: { time: 'fa fa-calendar' },
    format: 'DD/MM/YYYY'
  });

  $('#extendModal').find('#id').val("");
  $('#extendModal').find('#customer').val("");
  $('#extendModal').find('#brand').val('');
  $('#extendModal').find('#model').val("");
  $('#extendModal').find('#machineType').val('');
  $('#extendModal').find('#capacity').val('');
  $('#extendModal').find('#validator').val('');
  $('#extendModal').find('#serial').val('');
  $('#extendModal').find('#stamping').val('');
  $('#extendModal').find('#stampDate').val(formatDate2(date));
  $('#extendModal').find('#dueDate').val(formatDate2(date));
  $('#extendModal').find('#invoice').val("");
  $('#extendModal').find('#pic').val("");
  $('#extendModal').find('#followUpDate').val(formatDate2(date));
  $('#extendModal').find('#quotation').val("");
  $('#extendModal').find('#remark').val("");
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
      var date = new Date();

      $('#datePicker').datetimepicker({
        icons: { time: 'fa fa-calendar' },
        format: 'DD/MM/YYYY'
      });

      $('#datePicker2').datetimepicker({
        icons: { time: 'fa fa-calendar' },
        format: 'DD/MM/YYYY'
      });

      $('#datePicker3').datetimepicker({
        icons: { time: 'fa fa-calendar' },
        format: 'DD/MM/YYYY'
      });

      $('#extendModal').find('#id').val(obj.message.id);
      $('#extendModal').find('#customer').val(obj.message.customers);
      $('#extendModal').find('#brand').val(obj.message.brand);
      $('#extendModal').find('#model').val(obj.message.model);
      $('#extendModal').find('#machineType').val(obj.message.descriptions);
      $('#extendModal').find('#capacity').val(obj.message.capacity);
      $('#extendModal').find('#validator').val(obj.message.validate_by);
      $('#extendModal').find('#serial').val(obj.message.serial_no);
      $('#extendModal').find('#stamping').val(obj.message.stamping_no);
      $('#extendModal').find('#stampDate').val(formatDate2(new Date(obj.message.stamping_date)));
      $('#extendModal').find('#dueDate').val(formatDate2(new Date(obj.message.due_date)));
      $('#extendModal').find('#invoice').val(obj.message.invoice_no);
      $('#extendModal').find('#pic').val(obj.message.customer_pic);
      $('#extendModal').find('#followUpDate').val(formatDate2(new Date(obj.message.follow_up_date)));
      $('#extendModal').find('#quotation').val(obj.message.quotation_no);
      $('#extendModal').find('#remark').val(obj.message.remarks);
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

function deactivate(id) {
  if (confirm('Are you sure you want to delete this items?')) {
    $('#spinnerLoading').show();
    $.post('php/deleteWeight.php', {userID: id}, function(data){
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
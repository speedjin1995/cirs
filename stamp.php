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
  $users2 = $db->query("SELECT * FROM users WHERE deleted = '0'");
  $validators = $db->query("SELECT * FROM validators WHERE deleted = '0'");
  $alats = $db->query("SELECT * FROM alat WHERE deleted = '0'");
}
?>

<select class="form-control" style="width: 100%;" id="customerNoHidden" style="display: none;">
  <option value="" selected disabled hidden>Please Select</option>
  <?php while($rowCustomer=mysqli_fetch_assoc($customers)){ ?>
    <option value="<?=$rowCustomer['id'] ?>"><?=$rowCustomer['customer_name'] ?></option>
  <?php } ?>
</select>

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
              <div class="col-8"></div>
              <div class="col-2">
                <button type="button" class="btn btn-block bg-gradient-info btn-sm" id="exportBorangs">Export Borangs</button>
              </div>
              <!--div class="col-2">
                <a href="/template/Stamping Record Template.xlsx" download><button type="button" class="btn btn-block bg-gradient-danger btn-sm" id="downloadExccl">Download Template</button></a>
              </div-->
              <!--div class="col-2">
                <button type="button" class="btn btn-block bg-gradient-success btn-sm" id="uploadExccl">Upload Excel</button>
              </div-->
              <div class="col-2">
                <button type="button" class="btn btn-block bg-gradient-warning btn-sm" onclick="newEntry()">Add New Stamping</button>
              </div>
            </div>
          </div>

          <div class="card-body">
            <table id="weightTable" class="table table-bordered table-striped display">
              <thead>
                <tr>
                  <th></th>
                  <th>Customers</th>
                  <th>Brands</th>
                  <th>Desc</th>
                  <th>Model</th>
                  <th>Capacity</th>
                  <th>Serial No.</th>
                  <th>Next Due Date</th>
                  <th>Status</th>
                  <th></th>
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
            <div class="col-4">
              <div class="form-group">
                <label>Customer Type * </label>
                <select class="form-control" style="width: 100%;" id="customerType" name="customerType" required>
                  <option value="NEW">NEW</option>
                  <option value="EXISTING">EXISTING</option>
                </select>
              </div>
            </div>
            <div class="col-4">
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
            <div class="col-4">
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
          </div>

          <div class="row">
            <div class="col-4">
              <div class="form-group">
                <label>Customer * </label>
                <select class="form-control" style="width: 100%;" id="company" name="company" required></select>
                <input class="form-control" type="text" placeholder="Company Name" id="companyText" name="companyText" style="display: none;">
              </div>
            </div>
            <div class="col-4">
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
            <div class="col-4">
              <div class="form-group">
                <label>Jenis Alat *</label>
                <select class="form-control" style="width: 100%;" id="jenisAlat" name="jenisAlat" required>
                  <option selected="selected">-</option>
                  <?php while($rowA=mysqli_fetch_assoc($alats)){ ?>
                    <option value="<?=$rowA['id'] ?>"><?=$rowA['alat'] ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-4">
              <div class="form-group">
                <label>Address Line 1 * </label>
                <input class="form-control" type="text" placeholder="Address Line 1" id="address1" name="address1" required>
              </div>
            </div>
            <div class="col-4">
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
            <div class="col-4">
              <div class="form-group">
                <label>Stamping Date</label>
                <div class='input-group date' id="datePicker" data-target-input="nearest">
                  <input type='text' class="form-control datetimepicker-input" data-target="#datePicker" id="stampDate" name="stampDate"/>
                  <div class="input-group-append" data-target="#datePicker" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="row">
            <div class="col-4">
              <div class="form-group">
                <label>Address Line 2 </label>
                <input class="form-control" type="text" placeholder="Address Line 2" id="address2" name="address2">
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <label>Capacity * </label>
                <select class="form-control" style="width: 100%;" id="capacity" name="capacity" required>
                  <option selected="selected">-</option>
                  <?php while($rowCA=mysqli_fetch_assoc($capacities)){ ?>
                    <option value="<?=$rowCA['id'] ?>" data-price="<?=$rowCA['price'] ?>"><?=$rowCA['capacity'] ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <label>No Daftar </label>
                <input class="form-control" type="text" placeholder="No Daftar" id="noDaftar" name="noDaftar">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-4">
              <div class="form-group">
                <label>Address Line 3 </label>
                <input class="form-control" type="text" placeholder="Address Line 3" id="address3" name="address3">
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <label>Serial No * </label>
                <input class="form-control" type="text" placeholder="Serial No." id="serial" name="serial" required>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <label>No PIN Pelekat Keselamatan </label>
                <input class="form-control" type="text" placeholder="No PIN Pelekat Keselamatan" id="pinKeselamatan" name="pinKeselamatan">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-4">
              <div class="form-group">
                <label>Attn To</label>
                <select class="form-control" style="width: 100%;" id="attnTo" name="attnTo" readonly>
                  <option selected="selected">-</option>
                  <?php while($rowU=mysqli_fetch_assoc($users2)){ ?>
                    <option value="<?=$rowU['id'] ?>"><?=$rowU['name'] ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
            <div class="col-4"></div>
            <div class="col-4">
              <div class="form-group">
                <label>No Siri Pelekat Keselamatan </label>
                <input class="form-control" type="text" placeholder="No Siri Pelekat Keselamatan" id="siriKeselamatan" name="siriKeselamatan">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-4">
              <div class="form-group">
                <label>Person Contact</label>
                <input class="form-control" type="text" placeholder="PIC" id="pic" name="pic">
              </div>
            </div>
            <div class="col-4"></div>
            <div class="col-4">
              <div class="form-group">
                <label>No. Borang D</label>
                <input class="form-control" type="text" placeholder="No. Borang D" id="borangD" name="borangD">
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-8">
              <div class="form-group">
                <label>Remark</label>
                <textarea class="form-control" type="text" placeholder="Remark" id="remark" name="remark"></textarea>
              </div>
            </div>
            <div class="col-4">
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
          </div>

          <div class="row">
            <div class="col-4">
              <div class="form-group">
                <label>Quotation No.</label>
                <input class="form-control" type="text" placeholder="PO No" id="quotation" name="quotation">
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <label>Quotation Date</label>
                <div class='input-group date' id="datePicker3" data-target-input="nearest">
                  <input type='text' class="form-control datetimepicker-input" data-target="#datePicker3" id="quotationDate" name="quotationDate"/>
                  <div class="input-group-append" data-target="#datePicker3" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <label>Included Certificate * </label>
                <select class="form-control" style="width: 100%;" id="includeCert" name="includeCert" required>
                  <option value="YES">YES</option>
                  <option value="NO">NO</option>
                </select>
              </div>
            </div>
          </div>

          <div class="row">
          <div class="col-4">
              <div class="form-group">
                <label>PO No.</label>
                <input class="form-control" type="text" placeholder="PO No" id="poNo" name="poNo">
              </div>
            </div>
            <div class="col-4">
              <div class="form-group">
                <label>PO Date *</label>
                <div class='input-group date' id="datePicker4" data-target-input="nearest">
                  <input type='text' class="form-control datetimepicker-input" data-target="#datePicker4" id="poDate" name="poDate"/>
                  <div class="input-group-append" data-target="#datePicker4" data-toggle="datetimepicker">
                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-4">
              <div class="form-group">
                <label>Cash Bill No.</label>
                <input class="form-control" type="text" placeholder="Cash Bill No." id="cashBill" name="cashBill">
              </div>
            </div>
            
            <div class="col-4">
              <div class="form-group">
                <label>Invoice No.</label>
                <input class="form-control" type="text" placeholder="Invoice No" id="invoice" name="invoice">
              </div>
            </div>
            
            <div class="col-4">
              <button style="margin-left:auto;margin-right: 25px;margin-top: 30px;" type="button" class="btn btn-primary add-price">Add Items</button>
            </div>
          </div>

          <div class="row">
            <div class="col-9">
              <table style="width: 100%;">
                <thead>
                  <tr>
                    <th width="5%">No.</th>
                    <th width="15%">Date Created</th>
                    <th>Notes</th>
                    <th width="17%">Next Follow Date</th>
                    <th width="15%">Follow Up By</th>
                    <th width="13%">Status</th>
                    <!--th>Delete</th-->
                  </tr>
                </thead>
                <tbody id="pricingTable"></tbody>
              </table>
            </div>
            <div class="col-3">
              <p><u>Unit Stamping Price Refering:</u></p>
              <table style="width: 100%;">
                <tbody id="pricingTable">
                  <tr><td>Unit Price</td><td><input type="text" class="form-control" id="unitPrice" name="unitPrice" readonly></td></tr>
                  <tr id="cerId"><td>Cert.Price</td><td><input type="text" class="form-control" id="certPrice" name="certPrice" readonly></td></tr>
                  <tr><td>Total Amount</td><td><input type="text" class="form-control" id="totalAmount" name="totalAmount" readonly></td></tr>
                  <tr><td>SST 6%</td><td><input type="text" class="form-control" id="sst" name="sst" readonly></td></tr>
                  <tr><td>Sub Amount</td><td><input type="text" class="form-control" id="subAmount" name="subAmount" readonly></td></tr>
                </tbody>
              </table>
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
                <label>Borang 6/7/Panjang *</label>
                <select class="form-control" id="driver" name="driver" required>
                  <option value="6">Borang 6</option>
                  <option value="7">Borang 7</option>
                  <option value="P">Borang Panjang</option>
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

<script type="text/html" id="pricingDetails">
  <tr class="details">
    <td>
      <input type="text" class="form-control" id="no" name="no" readonly>
    </td>
    <td>
      <input type="text" class="form-control" id="date" name="date" readonly>
    </td>
    <td>
      <input type="text" class="form-control" id="notes" name="notes">
    </td>
    <td>
      <div class='input-group date' id="datePicker5" data-target-input="nearest">
        <input type='text' class="form-control datetimepicker-input" data-target="#datePicker5" id="followUpDate" name="followUpDate"/>
        <div class="input-group-append" data-target="#datePicker5" data-toggle="datetimepicker">
          <div class="input-group-text"><i class="fa fa-calendar"></i></div>
        </div>
      </div>
    </td>
    <td>
      <select class="form-control" style="width: 100%;" id="picAttend" name="picAttend" readonly>
        <option selected="selected">-</option>
        <?php while($rowU=mysqli_fetch_assoc($users)){ ?>
          <option value="<?=$rowU['id'] ?>"><?=$rowU['name'] ?></option>
        <?php } ?>
      </select>
    </td>
    <td>
      <select class="form-control" style="width: 100%;" id="status" name="status">
        <option value="Pending">Pending</option>
        <option value="Complete">Complete</option>
      </select>
    </td>
    <!--td><button class="btn btn-danger btn-sm" id="remove"><i class="fa fa-times"></i></button></td-->
  </tr>
</script>

<script>
var pricingCount = $("#pricingTable").find(".details").length;

$(function () {
  $('#customerNoHidden').hide();

  const today = new Date();
  const tomorrow = new Date(today);
  const yesterday = new Date(today);
  tomorrow.setDate(tomorrow.getDate() + 1);
  yesterday.setDate(tomorrow.getDate() - 7);

  $('.select2').select2({
    allowClear: true,
    placeholder: "Please Select"
  });

  //Date picker
  $('#fromDate').datetimepicker({
    icons: { time: 'far fa-calendar' },
    format: 'DD/MM/YYYY',
    defaultDate: yesterday
  });

  $('#toDate').datetimepicker({
    icons: { time: 'far fa-calendar' },
    format: 'DD/MM/YYYY',
    defaultDate: today
  });

  $('#datePicker').datetimepicker({
    icons: { time: 'far fa-calendar' },
    format: 'DD/MM/YYYY',
    defaultDate: today
  });

  $('#datePicker2').datetimepicker({
    icons: { time: 'far fa-calendar' },
    format: 'DD/MM/YYYY',
    defaultDate: today
  });

  $('#datePicker3').datetimepicker({
    icons: { time: 'far fa-calendar' },
    format: 'DD/MM/YYYY',
    defaultDate: today
  });

  $('#datePicker4').datetimepicker({
    icons: { time: 'far fa-calendar' },
    format: 'DD/MM/YYYY',
    defaultDate: today
  });

  var fromDateValue = $('#fromDate').val();
  var toDateValue = $('#toDate').val();
  var customerNoFilter = $('#customerNoFilter').val() ? $('#customerNoFilter').val() : '';

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
      'type': 'POST',
      'url':'php/filterStamping.php',
      'data': {
        fromDate: fromDateValue,
        toDate: toDateValue,
        customer: customerNoFilter,
      } 
    },
    'columns': [
      {
        // Add a checkbox with a unique ID for each row
        data: 'id', // Assuming 'serialNo' is a unique identifier for each row
        className: 'select-checkbox',
        orderable: false,
        render: function (data, type, row) {
          if (row.status == 'Active') { // Assuming 'isInvoiced' is a boolean field in your row data
            return '<input type="checkbox" class="select-checkbox" id="checkbox_' + data + '" value="'+data+'"/>';
          } 
          else {
            return ''; // Return an empty string or any other placeholder if the item is invoiced
          }
        }
      },
      { data: 'customers' },
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
          return '<div class="row"><div class="col-4"><button type="button" id="edit'+data+'" onclick="edit('+data+
          ')" class="btn btn-success btn-sm"><i class="fas fa-pen"></i></button></div><div class="col-12"><button type="button" id="delete'+data+'" onclick="deactivate('+data+
          ')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></div></div>';
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
            var printWindow = window.open('', '', 'height=400,width=800');
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
        {
          // Add a checkbox with a unique ID for each row
          data: 'id', // Assuming 'serialNo' is a unique identifier for each row
          className: 'select-checkbox',
          orderable: false,
          render: function (data, type, row) {
            if (row.status == 'Active') { // Assuming 'isInvoiced' is a boolean field in your row data
              return '<input type="checkbox" class="select-checkbox" id="checkbox_' + data + '" value="'+data+'"/>';
            } 
            else {
              return ''; // Return an empty string or any other placeholder if the item is invoiced
            }
          }
        },
        { data: 'customers' },
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
            return '<div class="row"><div class="col-4"><button type="button" id="edit'+data+'" onclick="edit('+data+
            ')" class="btn btn-success btn-sm"><i class="fas fa-pen"></i></button></div><div class="col-12"><button type="button" id="delete'+data+'" onclick="deactivate('+data+
            ')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></div></div>';
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
    });
  });

  $('#exportBorangs').on('click', function () {
    var selectedIds = []; // An array to store the selected 'id' values

    $("#weightTable tbody input[type='checkbox']").each(function () {
      if (this.checked) {
        selectedIds.push($(this).val());
      }
    });

    if (selectedIds.length > 0) {
      $("#printDOModal").find('#id').val(selectedIds);
      $("#printDOModal").find('#driver').val('6');
      $("#printDOModal").modal("show");

      $('#printDOForm').validate({
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
    } else {
      // Optionally, you can display a message or take another action if no IDs are selected
      alert("Please select at least one DO to Deliver.");
    }
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
  

  $('#extendModal').find('#customerType').on('change', function(){
    if($(this).val() == "NEW"){
      $('#extendModal').find('#company').hide();
      $('#extendModal').find('#companyText').show();
    }
    else{
      $('#extendModal').find('#company').html($('select#customerNoHidden').html());
      $('#extendModal').find('#company').show();
      $('#extendModal').find('#companyText').hide();
    }
  });

  $('#extendModal').find('#company').on('change', function(){
    //$('#spinnerLoading').show();
    var id = $(this).find(":selected").val();

    $.post('php/getCustomer.php', {userID: id}, function(data){
      var obj = JSON.parse(data);
      
      if(obj.status === 'success'){
        $('#extendModal').find('#address1').val(obj.message.customer_address);
        $('#extendModal').find('#address2').val(obj.message.address2);
        $('#extendModal').find('#address3').val(obj.message.address3);
        $('#extendModal').find('#contact').val(obj.message.customer_phone);
        $('#extendModal').find('#email').val(obj.message.customer_email);
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
      //$('#spinnerLoading').hide();
    });
  });

  $('#extendModal').find('#capacity').on('change', function(){
    var price = parseFloat($(this).find(":selected").attr("data-price"));
    var includeCert = $('#includeCert').val();
    var certPrice = 30;
    var sst = 0;
    var totalAmt = price;

    $('#unitPrice').val(price);

    if(includeCert == 'YES'){
      $('#certPrice').val(certPrice);
      $('#cerId').show();
      totalAmt += certPrice;
    }
    else{
      $('#certPrice').val(0.00);
      $('#cerId').hide();
    }

    $('#totalAmount').val(totalAmt);
    $('#sst').val((totalAmt * 0.06).toFixed(2));
    $('#subAmount').val((totalAmt + (totalAmt * 0.06)).toFixed(2));
  });

  $('#extendModal').find('#includeCert').on('change', function(){
    var price = parseFloat($('#capacity').find(":selected").attr("data-price"));
    var includeCert = $(this).val();
    var certPrice = 30;
    var sst = 0;
    var totalAmt = price;

    $('#unitPrice').val(price);

    if(includeCert == 'YES'){
      $('#certPrice').val(certPrice);
      $('#cerId').show();
      totalAmt += certPrice;
    }
    else{
      $('#certPrice').val(0.00);
      $('#cerId').hide();
    }

    $('#totalAmount').val(totalAmt);
    $('#sst').val((totalAmt * 0.06).toFixed(2));
    $('#subAmount').val((totalAmt + (totalAmt * 0.06)).toFixed(2));
  });

  $(".add-price").click(function(){
    var $addContents = $("#pricingDetails").clone();
    $("#pricingTable").append($addContents.html());

    $("#pricingTable").find('.details:last').attr("id", "detail" + pricingCount);
    $("#pricingTable").find('.details:last').attr("data-index", pricingCount);
    //$("#pricingTable").find('#remove:last').attr("id", "remove" + pricingCount);

    $("#pricingTable").find('#no:last').attr('name', 'no['+pricingCount+']').attr("id", "no" + pricingCount).val((pricingCount + 1).toString());
    $("#pricingTable").find('#date:last').attr('name', 'date['+pricingCount+']').attr("id", "date" + pricingCount).val(formatDate2(today));
    $("#pricingTable").find('#notes:last').attr('name', 'notes['+pricingCount+']').attr("id", "notes" + pricingCount);
    $("#pricingTable").find('#followUpDate:last').attr('name', 'followUpDate['+pricingCount+']').attr("id", "followUpDate" + pricingCount).val(formatDate2(today));
    $("#pricingTable").find('#picAttend:last').attr('name', 'picAttend['+pricingCount+']').attr("id", "picAttend" + pricingCount).val('<?=$user ?>');
    $("#pricingTable").find('#status').attr('name', 'status['+pricingCount+']').attr("id", "status" + pricingCount).val('Pending');

    var newDatePickerId = "datePicker5" + pricingCount;

    // Find the newly added date input and set the new ID
    var $newDateInputGroup = $("#pricingTable").find('#datePicker5:last');
    $newDateInputGroup.attr("id", newDatePickerId);
    $newDateInputGroup.find('input').attr("data-target", "#" + newDatePickerId);
    $newDateInputGroup.find('.input-group-append').attr("data-target", "#" + newDatePickerId);

    // Initialize the date picker on the new element
    $newDateInputGroup.datetimepicker({
      icons: { time: 'far fa-calendar' },
      format: 'DD/MM/YYYY',
      defaultDate: today
    });

    pricingCount++;
  });
});

function format (row) {
  var returnString = '<div class="row"><div class="col-md-3"><p>Customer Name: '+row.customers+
  '</p></div><div class="col-md-3"><p>Brand: '+row.brand+
  '</p></div><div class="col-md-3"><p>Machine Type: '+row.machine_type+
  '</p></div><div class="col-md-3"><p>Model: '+row.model+
  '</p></div></div><div class="row"><div class="col-md-3"><p>Address: ' + row.address1 + '<br>' + row.address2 + '<br>' + row.address3 +
  '</p></div><div class="col-md-9"><div class="row"><div class="col-md-4"<p>Capacity: '+row.capacity+
  '</p></div><div class="col-md-4"><p>Validate By: '+row.validator+
  '</p></div><div class="col-md-4"><p>Jenis Alat: '+row.jenis_alat+
  '</p></div></div><div class="row"><div class="col-md-4"><p>No. Daftar: '+row.no_daftar+
  '</p></div><div class="col-md-4"><p>PIN Keselamatan: '+row.pin_keselamatan+
  '</p></div><div class="col-md-4"><p>Siri Keselamatan: '+row.siri_keselamatan+
  '</p></div></div><div class="row"><div class="col-md-4"><p>Borang D: '+row.borang_d+
  '</p></div><div class="col-md-4"><p>Invoice No: '+row.invoice_no+
  '</p></div><div class="col-md-4"><p>Cash Bill: '+row.cash_bill+
  '</p></div></div></div></div><div class="row"><div class="col-md-3"><p>Quotation No: '+row.quotation_no+
  '</p></div><div class="col-md-3"><p>Quotation Date: '+row.quotation_date+
  '</p></div><div class="col-md-3"><p>Purchase No: '+row.purchase_no+
  '</p></div><div class="col-md-3"><p>Purchase Date: '+row.purchase_date+
  '</p></div></div><div class="row"><div class="col-md-6"><p>Remark: '+row.remarks+
  '</p></div><div class="col-md-3"><p>Unit Price: '+row.unit_price+
  '</p></div><div class="col-md-3"><p>Cert Price: '+row.cert_price+
  '</p></div></div><div class="row"><div class="col-md-3"><p>Total Amount: '+row.total_amount+
  '</p></div><div class="col-md-3"><p>SST Price: '+row.sst+
  '</p></div><div class="col-md-3"><p>Sub Total Price: '+row.subtotal_amount+
  '</p></div></div><br>';
  
  if (row.log.length > 0) {
    returnString += '<h4>Log</h4><table style="width: 100%;"><thead><tr><th width="5%">No.</th><th width="15%">Date Created</th><th>Notes</th><th width="17%">Next Follow Date</th><th width="15%">Follow Up By</th><th width="13%">Status</th></tr></thead><tbody>'
    
    for (var i = 0; i < row.log.length; i++) {
      var item = row.log[i];
      returnString += '<tr><td>' + item.no + '</td><td>' + item.date + '</td><td>' + item.notes + '</td><td>' + item.followUpDate + '</td><td>' + item.picAttend + '</td><td>' + item.status + '</td></tr>'
    }

    returnString += '</tbody></table>';
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
  $('#extendModal').find('#brand').val('');
  $('#extendModal').find('#validator').val('');
  $('#extendModal').find('#company').val('');
  $('#extendModal').find('#companyText').val('');
  $('#extendModal').find('#machineType').val('');
  $('#extendModal').find('#jenisAlat').val('');
  $('#extendModal').find('#address1').val('');
  $('#extendModal').find('#model').val("");
  $('#extendModal').find('#stampDate').val('');
  $('#extendModal').find('#address2').val('');
  $('#extendModal').find('#capacity').val('');
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
  $('#extendModal').find('#includeCert').val("NO");
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
      $('#extendModal').find('#brand').val(obj.message.brand);
      $('#extendModal').find('#validator').val(obj.message.validate_by);
      $('#extendModal').find('#company').val(obj.message.customers);
      $('#extendModal').find('#companyText').val('');
      $('#extendModal').find('#machineType').val(obj.message.machine_type);
      $('#extendModal').find('#jenisAlat').val(obj.message.jenis_alat);
      $('#extendModal').find('#address1').val(obj.message.address1);
      $('#extendModal').find('#model').val(obj.message.model);
      $('#extendModal').find('#stampDate').val(formatDate3(obj.message.stamping_date));
      $('#extendModal').find('#address2').val(obj.message.address2);
      $('#extendModal').find('#capacity').val(obj.message.capacity);
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
      $('#extendModal').find('#includeCert').val(obj.message.include_cert);
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
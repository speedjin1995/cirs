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
  $_SESSION['page']='validation';
	
	if(($row = $result->fetch_assoc()) !== null){
    $role = $row['role_code'];
  }

  $autoFormNos = $db->query("SELECT DISTINCT auto_form_no FROM other_validations WHERE deleted='0'");
  $dealer = $db->query("SELECT * FROM dealer WHERE deleted = '0'");
  $customers = $db->query("SELECT * FROM customers WHERE customer_status = 'CUSTOMERS' AND deleted = '0'");
  $customers2 = $db->query("SELECT * FROM customers WHERE customer_status = 'CUSTOMERS' AND deleted = '0'");
  $machinetypes = $db->query("SELECT * FROM machines WHERE deleted = '0'");
  $brands = $db->query("SELECT * FROM brand WHERE deleted = '0' ORDER BY brand ASC");
  $models = $db->query("SELECT * FROM model WHERE deleted = '0'");
  $sizes = $db->query("SELECT * FROM size WHERE deleted = '0'");
  $capacities = $db->query("SELECT * FROM capacity WHERE deleted = '0'");
  $capacities2 = $db->query("SELECT * FROM capacity WHERE deleted = '0'");
  $problems = $db->query("SELECT * FROM problem WHERE deleted = '0'");
  $users = $db->query("SELECT * FROM users WHERE deleted = '0'");
  $users2 = $db->query("SELECT * FROM users WHERE deleted = '0'");
  $validators = $db->query("SELECT * FROM validators WHERE deleted = '0'");
  $validators2 = $db->query("SELECT * FROM validators WHERE deleted = '0' and type = 'OTHER'");
  $alats = $db->query("SELECT * FROM alat WHERE deleted = '0'");
  $products = $db->query("SELECT * FROM products WHERE deleted = '0'");
  $cancelledReasons = $db->query("SELECT * FROM reasons WHERE deleted = '0'");
  $country = $db->query("SELECT * FROM country");
  $country2 = $db->query("SELECT * FROM country");
  $loadCells = $db->query("SELECT load_cells.*, machines.machine_type AS machinetype, brand.brand AS brand_name, model.model AS model_name, alat.alat, country.nicename 
FROM load_cells, machines, brand, model, alat, country WHERE load_cells.machine_type = machines.id AND load_cells.brand = brand.id AND load_cells.model = model.id 
AND load_cells.jenis_alat = alat.id AND load_cells.made_in = country.id AND load_cells.deleted = '0'");
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
        <h1 class="m-0 text-dark">Complete Validations</h1>
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
              <div class="col-4">
                <div class="form-group">
                  <label>Select Validators</label>
                  <select class="form-control select2" id="validatorFilter" name="validatorFilter">
                    <option value="" selected disabled hidden>Please Select</option>
                    <?php while($validator=mysqli_fetch_assoc($validators)){ ?>
                      <option value="<?=$validator['id'] ?>"><?=$validator['validator'] ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <label>Name of Purchaser</label>
                  <select class="form-control select2" id="customerNoFilter" name="customerNoFilter">
                    <option value="" selected disabled hidden>Please Select</option>
                    <?php while($customer2=mysqli_fetch_assoc($customers2)){ ?>
                      <option value="<?=$customer2['id'] ?>"><?=$customer2['customer_name'] ?></option>
                    <?php } ?>
                  </select>
                </div>
              </div>
              <div class="col-4">
                <div class="form-group">
                  <label>Certificate No.</label>
                  <input class="form-control" type="text" placeholder="Certificate No." id="autoFormNoFilter" name="autoFormNoFilter">
                </div>
              </div>

              <!--div class="col-3">
                <div class="form-group">
                  <label>Status</label>
                  <select class="form-control" id="statusFilter" name="statusFilter">
                    <option value="" selected disabled hidden>Please Select</option>
                    <option value="Active">Active</option>
                    <option value="Complete">Complete</option>
                  </select>
                </div>
              </div-->
            </div>

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

    <div class="row">
      <div class="col-lg-12">
        <div class="card card-primary">
          <div class="card-header">
            <div class="row">
              <div class="col-8"><h4>Other Validation Record Pending / Expired Status :</h4></div>
              <div class="col-2">
                <!-- <button type="button" class="btn btn-block bg-gradient-info btn-sm" id="exportBorangs">Export Borangs</button> -->
              </div>
              <!--div class="col-2">
                <a href="/template/Stamping Record Template.xlsx" download><button type="button" class="btn btn-block bg-gradient-danger btn-sm" id="downloadExccl">Download Template</button></a>
              </div-->
              <!--div class="col-2">
                <button type="button" class="btn btn-block bg-gradient-success btn-sm" id="uploadExccl">Upload Excel</button>
              </div-->
              <div class="col-2">
                <!-- <button type="button" class="btn btn-block bg-gradient-warning btn-sm" onclick="newEntry()">Add New</button> -->
              </div>
            </div>
          </div>

          <div class="card-body">
            <table id="weightTable" class="table table-bordered table-striped display">
              <thead>
                <tr>
                  <!-- <th><input type="checkbox" id="selectAllCheckbox" class="selectAllCheckbox"></th> -->
                  <th>Company Name</th>
                  <th>Brand</th>
                  <th>Description Instruments for Weighing and Measuring</th>
                  <th>Validator By</th>
                  <th>Capacity</th>
                  <th>Previous Cert. No</th>
                  <th>Current Validation Date</th>
                  <th>Expired Date</th>
                  <th>Status</th>
                  <th>Action</th>
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
      <form role="form" id="extendForm" enctype="multipart/form-data">
        <div class="modal-header bg-gray-dark color-palette">
          <h4 class="modal-title">Add New</h4>
          <button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body" >
          <input type="hidden" class="form-control" id="id" name="id">
          <div class="row">
            <div class="col-4">
              <div class="form-group">
                <label>Direct Customer / Reseller * </label>
                <select class="form-control" style="width: 100%;" id="type" name="type" required>
                  <option value="DIRECT">DIRECT CUSTOMER</option>
                  <option value="RESELLER">RESELLER</option>
                </select>
              </div>
            </div>
          </div>
          <div class="card card-primary" id="isResseller" style="display: none;">
            <div class="card-body">
              <div class="row">
                <h4>Reseller Billing Information</h4>
              </div>
              <div class="row">
                <div class="col-4">
                  <div class="form-group">
                    <label for="code">Reseller</label>
                    <select class="form-control select2" id="dealer" name="dealer">
                      <option value="" selected disabled hidden>Please Select</option>
                      <?php while($rowD=mysqli_fetch_assoc($dealer)){ ?>
                        <option value="<?=$rowD['id'] ?>"><?=$rowD['customer_name'] ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="col-12" id="resellerbranch">
                  <div class="form-group">
                    <label>Branch * </label>
                    <select class="form-control select2" style="width: 100%;" id="reseller_branch" name="reseller_branch"></select>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="card card-primary">
            <div class="card-body">
              <div class="row">
                <h4>Customer Information</h4>
              </div>
              <div class="row col-12">
                <div class="col-3">
                  <div class="form-group">
                    <label>Customer Type * </label>
                    <select class="form-control" style="width: 100%;" id="customerType" name="customerType" required>
                      <option value="NEW">NEW</option>
                      <option value="EXISTING">EXISTING</option>
                    </select>
                    <input type="hidden" id="customerTypeEdit" name="customerTypeEdit">
                  </div>
                </div>
                <div class="col-3">
                  <div class="form-group">
                    <label>Customer * </label>
                    <select class="form-control select2" style="width: 100%;" id="company" name="company" required></select>
                    <input class="form-control" type="text" placeholder="Company Name" id="companyText" name="companyText" style="display: none;">
                  </div>
                </div>
                <div class="col-3">
                  <div class="form-group">
                    <label>Validator * </label>
                    <select class="form-control select2" style="width: 100%;" id="validator" name="validator" required>
                      <option selected="selected">-</option>
                      <?php while($validator2=mysqli_fetch_assoc($validators2)){ ?>
                        <option value="<?=$validator2['id'] ?>"><?=$validator2['validator'] ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
              </div>
              <div class="row col-12">
                <div class="col-12" id="custbranch">
                  <div class="form-group">
                    <label>Branch * </label>
                    <select class="form-control select2" style="width: 100%;" id="branch" name="branch" required></select>
                  </div>
                </div>
              </div>
              <div class="row col-12">
                <div class="col-3" id="addr1" style="display: none;">
                  <div class="form-group">
                    <label>Address Line 1 * </label>
                    <input class="form-control" type="text" placeholder="Address Line 1" id="address1" name="address1">
                  </div>
                </div>
                <div class="col-3" id="addr2" style="display: none;">
                  <div class="form-group">
                    <label>Address Line 2 </label>
                    <input class="form-control" type="text" placeholder="Address Line 2" id="address2" name="address2">
                  </div>
                </div>
                <div class="col-3" id="addr3" style="display: none;">
                  <div class="form-group">
                    <label>Address Line 3 </label>
                    <input class="form-control" type="text" placeholder="Address Line 3" id="address3" name="address3">
                  </div>
                </div>
                <div class="col-3" id="addr4" style="display: none;">
                  <div class="form-group">
                    <label>Address Line 4 </label>
                    <input class="form-control" type="text" placeholder="Address Line 4" id="address4" name="address4">
                  </div>
                </div>
              </div>
              <div class="row col-12">
                <div class="col-3" id="phone" style="display: none;">
                  <div class="form-group">
                    <label>Tel</label>
                    <input class="form-control" type="text" placeholder="Phone" id="phone" name="phone">
                  </div>
                </div>
                <div class="col-3" id="email" style="display: none;">
                  <div class="form-group">
                    <label>Email</label>
                    <input class="form-control" type="text" placeholder="Email" id="email" name="email">
                  </div>
                </div>
                <div class="col-3" id="pic" style="display: none;">
                  <div class="form-group">
                    <label>P.I.C</label>
                    <input class="form-control" type="text" placeholder="PIC" id="pic" name="pic">
                  </div>
                </div>
                <div class="col-3" id="contact" style="display: none;">
                  <div class="form-group">
                    <label>P.I.C Contact No.</label>
                    <input class="form-control" type="text" placeholder="PIC Contact" id="contact" name="contact">
                  </div>
                </div>
              </div>
                
            </div>
          </div>

          <div class="card card-primary">
            <div class="card-body">
              <div class="row">
                <h4>Machines / Instruments Information</h4>
              </div>
              <div class="row">
                <div class="col-3">
                  <div class="form-group">
                    <label>Machines / Instruments *</label>
                    <select class="form-control select2" style="width: 100%;" id="machineType" name="machineType" required>
                      <option selected="selected"></option>
                      <?php while($rowS=mysqli_fetch_assoc($machinetypes)){ ?>
                        <option value="<?=$rowS['id'] ?>"><?=$rowS['machine_type'] ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="col-3">
                  <div class="form-group">
                    <label>Brand *</label>
                    <select class="form-control select2" style="width: 100%;" id="brand" name="brand" required>
                      <option selected="selected"></option>
                      <?php while($rowB=mysqli_fetch_assoc($brands)){ ?>
                        <option value="<?=$rowB['id'] ?>"><?=$rowB['brand'] ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="col-3">
                  <div class="form-group">
                    <label>Model *</label>
                    <select class="form-control select2" style="width: 100%;" id="model" name="model" required>
                      <option selected="selected"></option>
                      <?php while($rowM=mysqli_fetch_assoc($models)){ ?>
                        <option value="<?=$rowM['id'] ?>"><?=$rowM['model'] ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="col-3">
                  <div class="form-group">
                    <label>Capacity * </label>
                    <select class="form-control select2" style="width: 100%;" id="capacity" name="capacity" required>
                      <option selected="selected"></option>
                      <?php while($rowCA=mysqli_fetch_assoc($capacities)){ ?>
                        <option value="<?=$rowCA['id'] ?>"><?=$rowCA['name'] ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="col-3">
                  <div class="form-group">
                    <label>Unit Serial No * </label>
                    <input class="form-control" type="text" placeholder="Serial No." id="serial" name="serial" required>
                  </div>
                </div>
                <div class="col-3">
                  <div class="form-group">
                    <label>Manufacturing *</label>
                    <select class="form-control select2" style="width: 100%;" id="manufacturing" name="manufacturing" required>
                      <option selected="selected"></option>
                      <option value="LOCAL">LOCAL</option>
                      <option value="OVERSEAS">OVERSEAS</option>
                    </select>
                  </div>
                </div>
                <div class="col-3">
                  <div class="form-group">
                    <label>Structure Size * </label>
                    <select class="form-control select2" style="width: 100%;" id="size" name="size" required>
                      <option selected="selected"></option>
                      <?php while($rowSI=mysqli_fetch_assoc($sizes)){ ?>
                        <option value="<?=$rowSI['id'] ?>"><?=$rowSI['size'] ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="col-3">
                  <div class="form-group">
                    <label>Create Date </label>
                    <div class='input-group date' id="datePicker3" data-target-input="nearest">
                      <input type='text' class="form-control datetimepicker-input" data-target="#datePicker3" id="validationDate" name="validationDate"/>
                      <div class="input-group-append" data-target="#datePicker3" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
                    <!-- <input class="form-control" type="text" placeholder="dd/mm/yyyy" id="validationDate" name="validationDate"> -->
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="card card-primary">
            <div class="card-body">
              <div class="row">
                <h4>Calibration Information</h4>
              </div>
              <div class="row">
                <div class="col-3">
                  <div class="form-group">
                    <label>Last Calibration Date</label>
                    <div class='input-group date' id="datePicker" data-target-input="nearest">
                      <input type='text' class="form-control datetimepicker-input" data-target="#datePicker" id="lastCalibrationDate" name="lastCalibrationDate"/>
                      <div class="input-group-append" data-target="#datePicker" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
                    <!-- <input class="form-control" type="date" id="lastCalibrationDate" name="lastCalibrationDate"> -->
                  </div>
                </div>
                <div class="col-3">
                  <div class="form-group">
                    <label>Expired Calibration Date</label>
                    <div class='input-group date' id="datePicker2" data-target-input="nearest">
                      <input type='text' class="form-control datetimepicker-input" data-target="#datePicker2" id="expiredCalibrationDate" name="expiredCalibrationDate"/>
                      <div class="input-group-append" data-target="#datePicker2" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
                    <!-- <input class="form-control" type="date" id="expiredCalibrationDate" name="expiredCalibrationDate"> -->
                  </div>
                </div>
                <div class="col-3">
                  <div class="form-group">
                    <label>Certificate Number</label>
                    <input class="form-control" type="text" placeholder="Certificate No." id="autoFormNo" name="autoFormNo">
                  </div>
                </div>

                <div class="col-3">
                  <div class="form-group">
                    <label>Upload Attachment Certificate 1</label>
                    <div class="d-flex">
                      <div class="col-10">
                        <input type="file" class="form-control" id="uploadAttachment1" name="uploadAttachment1">
                      </div>
                      <div class="col-2 mt-1">
                        <a href="" id="viewCalibrationPdf1" name="viewCalibrationPdf1" target="_blank" class="btn btn-success btn-sm" role="button" style="display: none;"><i class="fa fa-file-pdf-o"></i></a>
                      </div>
                    </div>
                    <input type="text" id="calibrationFilePath1" name="calibrationFilePath1" style="display:none">
                  </div>
                </div>

                <div class="col-3">
                  <div class="form-group">
                    <label>Upload Attachment Certificate 2</label>
                    <div class="d-flex">
                      <div class="col-10">
                        <input type="file" class="form-control" id="uploadAttachment2" name="uploadAttachment2">
                      </div>
                      <div class="col-2 mt-1">
                        <a href="" id="viewCalibrationPdf2" name="viewCalibrationPdf2" target="_blank" class="btn btn-success btn-sm" role="button" style="display: none;"><i class="fa fa-file-pdf-o"></i></a>
                      </div>
                    </div>
                    <input type="text" id="calibrationFilePath2" name="calibrationFilePath2" style="display:none">
                  </div>
                </div>

                <div class="col-3">
                  <div class="form-group">
                    <label>Upload Attachment Certificate 3</label>
                    <div class="d-flex">
                      <div class="col-10">
                        <input type="file" class="form-control" id="uploadAttachment3" name="uploadAttachment3">
                      </div>
                      <div class="col-2 mt-1">
                        <a href="" id="viewCalibrationPdf3" name="viewCalibrationPdf3" target="_blank" class="btn btn-success btn-sm" role="button" style="display: none;"><i class="fa fa-file-pdf-o"></i></a>
                      </div>
                    </div>
                    <input type="text" id="calibrationFilePath3" name="calibrationFilePath3" style="display:none">
                  </div>
                </div>

                <div class="col-3">
                  <div class="form-group">
                    <label>Upload Attachment Certificate 4</label>
                    <div class="d-flex">
                      <div class="col-10">
                        <input type="file" class="form-control" id="uploadAttachment4" name="uploadAttachment4">
                      </div>
                      <div class="col-2 mt-1">
                        <a href="" id="viewCalibrationPdf4" name="viewCalibrationPdf4" target="_blank" class="btn btn-success btn-sm" role="button" style="display: none;"><i class="fa fa-file-pdf-o"></i></a>
                      </div>
                    </div>
                    <input type="text" id="calibrationFilePath4" name="calibrationFilePath4" style="display:none">
                  </div>
                </div>

                <div class="col-3">
                  <div class="form-group">
                    <label>Upload Attachment Certificate 5</label>
                    <div class="d-flex">
                      <div class="col-10">
                        <input type="file" class="form-control" id="uploadAttachment5" name="uploadAttachment5">
                      </div>
                      <div class="col-2 mt-1">
                        <a href="" id="viewCalibrationPdf5" name="viewCalibrationPdf5" target="_blank" class="btn btn-success btn-sm" role="button" style="display: none;"><i class="fa fa-file-pdf-o"></i></a>
                      </div>
                    </div>
                    <input type="text" id="calibrationFilePath5" name="calibrationFilePath5" style="display:none">
                  </div>
                </div>

              </div>
            </div>
          </div>

          <!-- <div class="card card-primary">
            <div class="card-body">
              <div class="row mb-3">
                <div class="col-10">
                  <h4>Calibration Information</h4>
                </div>
              </div>
              <div class="row">
                <div class="col-10">
                  <div class="col-4">
                    <div class="form-group">
                      <label>No. of Calibrations *</label>
                      <input type="number" class="form-control" value='0' id="noOfCalibrationCell" name="noOfCalibrationCell" required>
                    </div>
                  </div>
                </div>
                <div class="col-2">
                  <button type="button" class="btn btn-primary add-load-cell" id="add-calibration-cell">Add Calibration</button>
                </div>
              </div>
              
              <table style="width: 100%;">
                <thead>
                  <tr>
                    <th width="5%">No.</th>
                    <th width="15%">Last Calibration Date</th>
                    <th width="15%">Expired Calibration Date</th>
                    <th width="25%">Upload Attachment</th>
                    <th width="5%">Delete</th>
                  </tr>
                </thead>
                <tbody id="loadCalibrationTable"></tbody>
              </table>
            </div>
          </div> -->
        </div>

        <div class="modal-footer justify-content-between bg-gray-dark color-palette">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="saveButton">Save</button>
        </div>
      </form>
    </div> <!-- /.modal-content -->
  </div> <!-- /.modal-dialog -->
</div> <!-- /.modal -->

<div class="modal fade" id="extraDetModal">
  <div class="modal-dialog modal-xl" style="max-width: 90%;">
    <div class="modal-content">
      <form role="form" id="extraDetForm">
        <div class="modal-header bg-gray-dark color-palette">
          <h4 class="modal-title">Extra Information</h4>
          <button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body" >
          <input type="hidden" class="form-control" id="id" name="id">
        </div>

        <div class="modal-footer justify-content-between bg-gray-dark color-palette">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="saveButton">Save</button>
        </div>
      </form>
    </div> <!-- /.modal-content -->
  </div> <!-- /.modal-dialog -->
</div> <!-- /.modal -->

<!-- <div class="modal fade" id="printDOModal">
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
</div> -->

<div class="modal fade" id="cancelModal"> 
  <div class="modal-dialog modal-xl" style="max-width: 50%;">
    <div class="modal-content">

      <form role="form" id="cancelForm">
        <div class="modal-header bg-gray-dark color-palette">
          <h4 class="modal-title">Cancellation Reason</h4>
          <button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body">
          <input type="hidden" class="form-control" id="id" name="id">
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <label>Cancellation Reason *</label>
                <select class="form-control" id="cancellationReason" name="cancellationReason" required>
                  <option value="" selected disabled hidden>Please Select</option>
                  <?php while($cancellationReason=mysqli_fetch_assoc($cancelledReasons)){ ?>
                    <option value="<?=$cancellationReason['id'] ?>"><?=$cancellationReason['reason'] ?></option>
                  <?php } ?>
                </select>
              </div>
            </div>
          </div>  
          <div class="row" id='otherRow'>
            <div class="col-6">
              <div class="form-group">
                <label>Remarks / Other Reasons</label>
                <textarea class="form-control" id ="otherReason" name="otherReason"></textarea>
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

<script type="text/html" id="atkDetails">
  <div class="card card-primary">
    <div class="card-body">
      <div class="row">
        <h4>Addtional Information (ATK)</h4>
      </div>
      <div class="row">
        <div class="col-4">
          <div class="form-group">
            <label>Penentusan Baru *</label>
            <input type="text" class="form-control" id="penentusanBaru" name="penentusanBaru" required>
          </div>
        </div>
        <div class="col-4">
          <div class="form-group">
            <label>Penetusan Semula</label>
            <input type="text" class="form-control" id="penentusanSemula" name="penentusanSemula" >
          </div>
        </div>
        <div class="form-group col-4">
          <label>Kelulusan MSPK * </label>
          <select class="form-control" style="width: 100%;" id="kelulusanMSPK" name="kelulusanMSPK" required>
            <option value="YES">YES</option>
            <option value="NO">NO</option>
          </select>
        </div>
        <div class="col-4">
          <div class="form-group">
            <label>No. Kelulusan MSPK</label>
            <input type="text" class="form-control" id="noMSPK" name="noMSPK">
          </div>
        </div>
        <div class="col-4">
          <div class="form-group">
            <label>No. Serial Indicator *</label>
            <input type="text" class="form-control" id="noSerialIndicator" name="noSerialIndicator">
          </div>
        </div>
        <div class="form-group col-4">
          <label for="model">Platform Made In *</label>
          <select class="form-control select2" id="platformCountry" name="platformCountry" required>
            <option value="" selected disabled hidden>Please Select</option>
            <?php while($rowcountry=mysqli_fetch_assoc($country)){ ?>
              <option value="<?=$rowcountry['id'] ?>"><?=$rowcountry['name'] ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="form-group col-4">
          <label for="model">Platform Type *</label>
          <select class="form-control select2" id="platformType" name="platformType" required>
            <option value="" selected disabled hidden>Please Select</option>
            <option value="MS Steel Deck">MS Steel Deck</option>
            <option value="Concrete Deck">Concrete Deck</option>
            <option value="Portable MS Steel Deck">Portable MS Steel Deck</option>
            <option value="Portable Concrete Deck">Portable Concrete Deck</option>
          </select>
        </div>
        <div class="col-4">
          <div class="form-group">
            <label>Structure Size * </label>
            <select class="form-control" style="width: 100%;" id="size" name="size" required>
              <option selected="selected">-</option>
              <?php while($rowSI=mysqli_fetch_assoc($sizes)){ ?>
                <option value="<?=$rowSI['id'] ?>"><?=$rowSI['size'] ?></option>
              <?php } ?>
            </select>
          </div>
        </div>
        <div class="form-group col-4">
          <label for="model">Jenis Pelantar *</label>
          <select class="form-control select2" id="jenisPelantar" name="jenisPelantar" required>
            <option value="" selected disabled hidden>Please Select</option>
            <option value="Pit">Pit</option>
            <option value="Pitless">Pitless</option>
          </select>
        </div>
        <div class="col-12">
          <div class="form-group">
            <label>Others</label>
            <textarea class="form-control" type="text" placeholder="Remark" id="others" name="others"></textarea>
          </div>
        </div>
      </div><hr>
      <div class="row">
        <h4>Load Cells</h4>
      </div>
      <div class="row">
        <div class="form-group col-4">
          <label for="model">Load Cells Made In *</label>
          <select class="form-control select2" id="loadCellCountry" name="loadCellCountry" required>
            <option value="" selected disabled hidden>Please Select</option>
            <?php while($rowcountry2=mysqli_fetch_assoc($country2)){ ?>
              <option value="<?=$rowcountry2['id'] ?>"><?=$rowcountry2['name'] ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="col-4">
          <div class="form-group">
            <label>No. of Load Cells *</label>
            <input type="number" class="form-control" id="noOfLoadCell" name="noOfLoadCell" required>
          </div>
        </div>
        <div class="col-4">
          <button style="margin-left:auto;margin-right: 25px;" type="button" class="btn btn-primary add-load-cell">Add Load Cells</button>
        </div>
      </div>
      <table style="width: 100%;">
        <thead>
          <tr>
            <th width="5%">No.</th>
            <th width="20%">Load Cells Type</th>
            <th width="20%">Brand</th>
            <th width="20%">Model</th>
            <th width="20%">Load Cell Capacity</th>
            <th width="10%">Serial No.</th>
            <th width="5%">Delete</th>
          </tr>
        </thead>
        <tbody id="loadCellTable"></tbody>
      </table>
    </div>
  </div>
</script>

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

<script type="text/html" id="loadCalibrationDetails">
  <tr class="details">
    <td style="display:none">
      <input type="text" id="calibrationId" name="calibrationId">
    </td>
    <td>
      <input type="text" class="form-control" id="no" name="no" readonly>
    </td>
    <td>
      <input type="date" id="lastCalibrationDate" name="lastCalibrationDate" style="width: 100%;">
    </td>
    <td>
      <input type="date" id="expiredCalibrationDate" name="expiredCalibrationDate" style="width: 100%;">
    </td>
    <td>
      <div class="row">
        <div class="col-10">
          <input type="file" class="form-control" id="uploadAttachment" name="uploadAttachment" required>
        </div>
        <div class="col-2 mt-1">
          <a href="" id="viewCalibrationPdf" name="viewCalibrationPdf" target="_blank" class="btn btn-success btn-sm" role="button" style="display: none;"><i class="fa fa-file-pdf-o"></i></a>
        </div>
        <input type="text" id="calibrationFilePath" name="calibrationFilePath"style="display:none">
      </div>
    </td>
    
    <td><button class="btn btn-danger btn-sm" id="remove"><i class="fa fa-times"></i></button></td>
  </tr>
</script>

<script>
var loadCalibrationCount = $("#loadCalibrationTable").find(".details").length;
var isModalOpen = false; // Flag to track modal visibility

$(function () {
  $('#customerNoHidden').hide();

  const today = new Date();
  const tomorrow = new Date(today);
  const yesterday = new Date(today);
  tomorrow.setDate(tomorrow.getDate() + 1);
  yesterday.setDate(tomorrow.getDate() - 7);
  const startOfMonth = new Date(today.getFullYear(), today.getMonth(), 1); // First day of the current month
  const endOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0); // Last day of the current month

  $('.select2').each(function() {
    $(this).select2({
        allowClear: true,
        placeholder: "Please Select",
        // Conditionally set dropdownParent based on the element’s location
        dropdownParent: $(this).closest('.modal').length ? $(this).closest('.modal-body') : undefined
    });
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

  $('#selectAllCheckbox').on('change', function() {
    var checkboxes = $('#weightTable tbody input[type="checkbox"]');
    checkboxes.prop('checked', $(this).prop('checked')).trigger('change');
  });

  var fromDateValue = $('#fromDate').val();
  var toDateValue = $('#toDate').val();
  var customerNoFilter = $('#customerNoFilter').val() ? $('#customerNoFilter').val() : '';
  var validatorFilter = $('#validatorFilter').val() ? $('#validatorFilter').val() : '';
  var autoFormNoFilter = $('#autoFormNoFilter').val() ? $('#autoFormNoFilter').val() : '';
  //var statusFilter = $('#statusFilter').val() ? $('#statusFilter').val() : '';

  var table = $("#weightTable").DataTable({
    "responsive": true,
    "autoWidth": false,
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
    'searching': true,
    "stateSave": true,
    'order': [[ 0, 'asc' ]],
    // 'columnDefs': [ { orderable: false, targets: [0] }],
    'ajax': {
      'type': 'POST',
      'url':'php/filterValidation.php',
      'data': {
        fromDate: fromDateValue,
        toDate: toDateValue,
        customer: customerNoFilter,
        validator: validatorFilter,
        autoFormNo: autoFormNoFilter,
        status: 'Pending'
      } 
    },
    'columns': [
      // {
      //   // Add a checkbox with a unique ID for each row
      //   data: 'id', // Assuming 'serialNo' is a unique identifier for each row
      //   className: 'select-checkbox',
      //   orderable: false,
      //   render: function (data, type, row) {
      //     if (row.status == 'Pending') { // Assuming 'isInvoiced' is a boolean field in your row data
      //       return '<input type="checkbox" class="select-checkbox" id="checkbox_' + data + '" value="'+data+'"/>';
      //     } 
      //     else {
      //       return ''; // Return an empty string or any other placeholder if the item is invoiced
      //     }
      //   }
      // },
      { data: 'customer' },
      { data: 'brand' },
      { data: 'machines' },
      { data: 'validate_by' },
      { data: 'capacity' },
      { data: 'auto_form_no' },
      { data: 'last_calibration_date' },
      { data: 'expired_calibration_date' },
      { data: 'status' },
      { 
        data: 'id',
        render: function (data, type, row) {
          let buttons = '<div class="row">';

          // Edit button
          buttons += '<div class="col-3"><button title="Edit" type="button" id="edit'+data+'" onclick="edit('+data+
                    ')" class="btn btn-warning btn-sm"><i class="fas fa-pen"></i></button></div>';

          // // Extra button if validate_by is 3
          // if (row.validate_by == 3) {
          //   buttons += '<div class="col-3"><button title="Extra Details" type="button" id="extra'+data+'" onclick="extraAction('+data+
          //             ')" class="btn btn-primary btn-sm"><i class="fas fa-star"></i></button></div>';
          // }

          // // Print button
          // buttons += '<div class="col-3"><button title="Print" type="button" id="print'+data+'" onclick="print('+data+
          //           ')" class="btn btn-info btn-sm"><i class="fas fa-print"></i></button></div>';

          // Complete button if conditions are met
          // buttons += '<div class="col-3"><button title="Complete" type="button" id="complete'+data+'" onclick="complete('+data+
          //           ')" class="btn btn-success btn-sm"><i class="fas fa-check"></i></button></div>';

          // Cancelled button
          buttons += '<div class="col-3"><button title="Cancelled" type="button" id="delete'+data+'" onclick="deactivate('+data+
                    ')" class="btn btn-danger btn-sm">X</button></div>';

          buttons += '</div>'; // Closing row div

          return buttons;
        }
      },
      { 
        className: 'dt-control',
        orderable: false,
        data: null,
        render: function ( data, type, row ) {
          return '<td class="table-elipse" data-toggle="collapse" data-target="#demo'+row.auto_form_no+'"><i class="fas fa-angle-down"></i></td>';
        }
      }
    ],
  });
  
  // Add event listener for opening and closing details
  $('#weightTable tbody').on('click', 'td.dt-control', function () {
    var tr = $(this).closest('tr');
    var row = table.row(tr);

    if ( row.child.isShown() ) {
      // This row is already open - close it
      row.child.hide();
      tr.removeClass('shown');
    }
    else {
      $.post('php/getValidation.php', {validationId: row.data().id, format: 'EXPANDABLE'}, function (data){
        var obj = JSON.parse(data); 
        if(obj.status === 'success'){
          row.child( format(obj.message) ).show();tr.addClass("shown");
        }
      });
    }
  });

  // Bind form submission handler once
	$('#extendForm').off('submit').on('submit', function(e) {
		e.preventDefault(); 
		var formData = new FormData(this); 
		$.ajax({
			url: 'php/insertValidation.php',
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			success: function(data) {
				var obj = JSON.parse(data); 
				if (obj.status === 'success') {
					$('#extendModal').modal('hide');
					toastr["success"](obj.message, "Success:");
					location.reload(); // Reload the page
				} else {
					toastr["error"](obj.message, "Failed:");
				}
				$('#spinnerLoading').hide();
				isModalOpen = false; // Set flag to false on error as well
			},
			error: function(xhr, status, error) {
				console.error("AJAX request failed:", status, error);
				toastr["error"]("An error occurred while processing the request.", "Failed:");
				$('#spinnerLoading').hide();
				isModalOpen = false; // Set flag to false on error as well
			}
		});
	});

  $.validator.setDefaults({
    submitHandler: function () {
      if($('#cancelModal').hasClass('show')){
        $.post('php/deleteValidation.php', $('#cancelForm').serialize(), function(data){
          var obj = JSON.parse(data); 
          if(obj.status === 'success'){
            $('#cancelModal').modal('hide');
            toastr["success"](obj.message, "Success:");
            $('#weightTable').DataTable().ajax.reload(null, false);
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
      // else if($('#extendModal').hasClass('show')){
      //   $('#spinnerLoading').show();

      //   $.post('php/insertValidation.php', $('#extendForm').serialize(), function(data){
      //     var obj = JSON.parse(data); 
      //     if(obj.status === 'success'){
      //       $('#extendModal').modal('hide');
      //       toastr["success"](obj.message, "Success:");
      //       $('#weightTable').DataTable().ajax.reload();
      //     }
      //     else if(obj.status === 'failed'){
      //       toastr["error"](obj.message, "Failed:");
      //     }
      //     else{
      //       toastr["error"]("Something wrong when edit", "Failed:");
      //     }

      //     $('#spinnerLoading').hide();
      //   });
      // }
      // else if($('#uploadModal').hasClass('show')){
      //   $('#spinnerLoading').show();

      //   // Serialize the form data into an array of objects
      //   var formData = $('#uploadForm').serializeArray();
      //   var data = [];
      //   var rowIndex = -1;
      //   formData.forEach(function(field) {
      //       var match = field.name.match(/([a-zA-Z]+)\[(\d+)\]/);
      //       if (match) {
      //         var fieldName = match[1];
      //         var index = parseInt(match[2], 10);
      //         if (index !== rowIndex) {
      //           rowIndex = index;
      //           data.push({});
      //         }
      //         data[index][fieldName] = field.value;
      //       }
      //   });

      //   // Send the JSON array to the server
      //   $.ajax({
      //       url: 'php/uploadStampings.php',
      //       type: 'POST',
      //       contentType: 'application/json',
      //       data: JSON.stringify(data),
      //       success: function(response) {
      //         var obj = JSON.parse(response);
      //         if (obj.status === 'success') {
      //           $('#uploadModal').modal('hide');
      //           toastr["success"](obj.message, "Success:");
      //           $('#weightTable').DataTable().ajax.reload();
      //         } 
      //         else if (obj.status === 'failed') {
      //           toastr["error"](obj.message, "Failed:");
      //         } 
      //         else {
      //           toastr["error"]("Something went wrong when editing", "Failed:");
      //         }
              
      //         $('#spinnerLoading').hide();
      //       }
      //   });
      // }
      // else if($('#printDOModal').hasClass('show')){
      //   $.post('php/print_borang.php', $('#printDOForm').serialize(), function(data){
      //     var obj = JSON.parse(data);
      
      //     if(obj.status === 'success'){
      //       $('#printDOModal').modal('hide');
      //       $('#weightTable').DataTable().ajax.reload();
      //       var printWindow = window.open('', '', 'height=400,width=800');
      //       printWindow.document.write(obj.message);
      //       printWindow.document.close();
      //       setTimeout(function(){
      //         printWindow.print();
      //         printWindow.close();
      //       }, 1000);
      //     }
      //     else if(obj.status === 'failed'){
      //       toastr["error"](obj.message, "Failed:");
      //     }
      //     else{
      //       toastr["error"]("Something wrong when pull data", "Failed:");
      //     }
      //   });
      // }
    }
  });

  $('#filterSearch').on('click', function(){
    //$('#spinnerLoading').show();

    var fromDateValue = $('#fromDate').val();
    var toDateValue = $('#toDate').val();
    var customerNoFilter = $('#customerNoFilter').val() ? $('#customerNoFilter').val() : '';
    var validatorFilter = $('#validatorFilter').val() ? $('#validatorFilter').val() : '';
    var autoFormNoFilter = $('#autoFormNoFilter').val() ? $('#autoFormNoFilter').val() : '';
    //var statusFilter = $('#statusFilter').val() ? $('#statusFilter').val() : '';

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
      "stateSave": true,
      'order': [[ 0, 'asc' ]],
      // 'columnDefs': [ { orderable: false, targets: [0] }],
      'ajax': {
        'type': 'POST',
        'url':'php/filterValidation.php',
        'data': {
          fromDate: fromDateValue,
          toDate: toDateValue,
          customer: customerNoFilter,
          validator: validatorFilter,
          autoFormNo: autoFormNoFilter,
          status: 'Pending'
        } 
      },
      'columns': [
        // {
        //   // Add a checkbox with a unique ID for each row
        //   data: 'id', // Assuming 'serialNo' is a unique identifier for each row
        //   className: 'select-checkbox',
        //   orderable: false,
        //   render: function (data, type, row) {
        //     if (row.status == 'Active') { // Assuming 'isInvoiced' is a boolean field in your row data
        //       return '<input type="checkbox" class="select-checkbox" id="checkbox_' + data + '" value="'+data+'"/>';
        //     } 
        //     else {
        //       return ''; // Return an empty string or any other placeholder if the item is invoiced
        //     }
        //   }
        // },
        { data: 'customer' },
        { data: 'brand' },
        { data: 'machines' },
        { data: 'validate_by' },
        { data: 'capacity' },
        { data: 'auto_form_no' },
        { data: 'last_calibration_date' },
        { data: 'expired_calibration_date' },
        { data: 'status' },
        { 
          data: 'id',
          render: function (data, type, row) {
            let buttons = '<div class="row">';

            // Edit button
            buttons += '<div class="col-3"><button title="Edit" type="button" id="edit'+data+'" onclick="edit('+data+
                      ')" class="btn btn-warning btn-sm"><i class="fas fa-pen"></i></button></div>';

            // // Extra button if validate_by is 3
            // if (row.validate_by == 3) {
            //   buttons += '<div class="col-3"><button title="Extra Details" type="button" id="extra'+data+'" onclick="extraAction('+data+
            //             ')" class="btn btn-primary btn-sm"><i class="fas fa-star"></i></button></div>';
            // }

            // // Print button
            // buttons += '<div class="col-3"><button title="Print" type="button" id="print'+data+'" onclick="print('+data+
            //           ')" class="btn btn-info btn-sm"><i class="fas fa-print"></i></button></div>';

            // Complete button if conditions are met
            // buttons += '<div class="col-3"><button title="Complete" type="button" id="complete'+data+'" onclick="complete('+data+
            //           ')" class="btn btn-success btn-sm"><i class="fas fa-check"></i></button></div>';

            // Cancelled button
            buttons += '<div class="col-3"><button title="Cancelled" type="button" id="delete'+data+'" onclick="deactivate('+data+
                      ')" class="btn btn-danger btn-sm">X</button></div>';

            buttons += '</div>'; // Closing row div

            return buttons;
          }
        },
        { 
          className: 'dt-control',
          orderable: false,
          data: null,
          render: function ( data, type, row ) {
            return '<td class="table-elipse" data-toggle="collapse" data-target="#demo'+row.auto_form_no+'"><i class="fas fa-angle-down"></i></td>';
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
      $("#printDOModal").find('#driver').val('P');
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

      //$('#printDOForm').submit();
    } 
    else {
      // Optionally, you can display a message or take another action if no IDs are selected
      alert("Please select at least one DO to Deliver.");
    }
  });

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

  $('#extendModal').find('#type').on('change', function(){
    if($(this).val() == "DIRECT"){
      $('#isResseller').hide();
    }
    else{
      $('#isResseller').show();
    }
  });

  $('#extendModal').find('#brand').on('change', function(){
    var brandId = $(this).find(":selected").val();

    $.post('php/getModelFromBrand.php', {id: brandId}, function (data){
      var obj = JSON.parse(data);

      if(obj.status === 'success'){
        $('#model').html('');
        $('#model').append('<option selected="selected">-</option>');

        for(var i=0; i<obj.message.length; i++){
          var modelInfo = obj.message[i];
          $('#model').append('<option value="'+modelInfo.id+'">'+modelInfo.model+'</option>')
        }

        $('#extendModal').trigger('modelsLoaded');
      }
      else if(obj.status === 'failed'){
        toastr["error"](obj.message, "Failed:");
      }
      else{
        toastr["error"]("Something wrong when pull data", "Failed:");
      }
    });
  });

  $('#extendModal').find('#dealer').on('change', function(){
    if($('#extendModal').find('#type').val() != 'DIRECT'){
      var id = $(this).find(":selected").val();

      $.post('php/getDealer.php', {userID: id}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
          
          $('#reseller_branch').html('');
          $('#reseller_branch').append('<option selected="selected">-</option>');

          for(var i=0; i<obj.message.branches.length; i++){
            var branchInfo = obj.message.branches[i];
            $('#reseller_branch').append('<option value="'+branchInfo.branchid+'">'+branchInfo.name+' - '+branchInfo.branch_address1+' '+branchInfo.branch_address2+' '+branchInfo.branch_address3+' '+branchInfo.branch_address4+'</option>')
          }
          /*$('#extendModal').modal('show');

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
          });*/
        }
        else if(obj.status === 'failed'){
          toastr["error"](obj.message, "Failed:");
        }
        else{
          toastr["error"]("Something wrong when pull data", "Failed:");
        }
      });

      $.post('php/listCustomers.php', {hypermarket: id}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
          $('#company').html('');
          $('#company').append('<option selected="selected">-</option>');
          $('#extendModal').find('#customerType').val('EXISTING');
          $('#extendModal').find('#company').show();
          $('#extendModal').find('#company').parents('.form-group').find('.select2-container').show();
          $('#extendModal').find('#companyText').hide();
          $('#extendModal').find('#companyText').val('');
          for(var i=0; i<obj.message.length; i++){
            $('#company').append('<option value="'+obj.message[i].id+'">'+obj.message[i].name+'</option>')
          }
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
  });

  // $('#extendModal').find('#dealer').on('change', function(){
  //   if($('#extendModal').find('#type').val() != 'OWN'){
  //     var id = $(this).find(":selected").val();

  //     $.post('php/getDealer.php', {userID: id}, function(data){
  //       var obj = JSON.parse(data);
        
  //       if(obj.status === 'success'){
  //         $('#extendModal').find('#address1s').val(obj.message.customer_address);
  //         $('#extendModal').find('#address2s').val(obj.message.address2);
  //         $('#extendModal').find('#address3s').val(obj.message.address3);
  //         $('#extendModal').find('#contacts').val(obj.message.customer_phone);
  //         $('#extendModal').find('#emails').val(obj.message.customer_email);
  //         $('#extendModal').modal('show');

  //         $('#extendForm').validate({
  //           errorElement: 'span',
  //           errorPlacement: function (error, element) {
  //             error.addClass('invalid-feedback');
  //             element.closest('.form-group').append(error);
  //           },
  //           highlight: function (element, errorClass, validClass) {
  //             $(element).addClass('is-invalid');
  //           },
  //           unhighlight: function (element, errorClass, validClass) {
  //             $(element).removeClass('is-invalid');
  //           }
  //         });
  //       }
  //       else if(obj.status === 'failed'){
  //         toastr["error"](obj.message, "Failed:");
  //       }
  //       else{
  //         toastr["error"]("Something wrong when pull data", "Failed:");
  //       }
  //     });

  //     $.post('php/listCustomers.php', {hypermarket: id}, function(data){
  //       var obj = JSON.parse(data);
        
  //       if(obj.status === 'success'){
  //         $('#company').html('');
  //         $('#company').append('<option selected="selected">-</option>');
  //         $('#extendModal').find('#customerType').val('EXISTING');
  //         $('#extendModal').find('#company').show();
  //         $('#extendModal').find('#company').parents('.form-group').find('.select2-container').show();
  //         $('#extendModal').find('#companyText').hide();
  //         $('#extendModal').find('#companyText').val('');
  //         for(var i=0; i<obj.message.length; i++){
  //           $('#company').append('<option value="'+obj.message[i].id+'">'+obj.message[i].name+'</option>')
  //         }
  //       }
  //       else if(obj.status === 'failed'){
  //         toastr["error"](obj.message, "Failed:");
  //       }
  //       else{
  //         toastr["error"]("Something wrong when pull data", "Failed:");
  //       }
  //       $('#spinnerLoading').hide();
  //     });
  //   }
  // });

  $('#extendModal').find('#stampDate').on('blur', function (e) {
    if($(this).val()){
      var parts = $(this).val().split('/');
      var day = parseInt(parts[0], 10);
      var month = parseInt(parts[1], 10) - 1; // Months are zero-based
      var year = parseInt(parts[2], 10);

      var date = new Date(year, month, day);
      
      // Add 1 year to the date
      date.setFullYear(date.getFullYear() + 1);
      
      /*/ Format the new date back to 'DD/MM/YYYY'
      var newDay = ("0" + date.getDate()).slice(-2);
      var newMonth = ("0" + (date.getMonth() + 1)).slice(-2); // Months are zero-based
      var newYear = date.getFullYear();
      
      var dueDate = newDay + '/' + newMonth + '/' + newYear;*/
      
      // Assign the new date to '#dueDate'
      $('#extendModal').find('#dueDate').val(formatDate3(date));
    }
  });

  $('#extendModal').find('#customerType').on('change', function(){
    if($(this).val() == "NEW"){
      $('#extendModal').find('#company').hide();
      $('#extendModal').find('#custbranch').hide();
      
      $('#extendModal').find('#addr1').show();
      $('#extendModal').find('#addr2').show();
      $('#extendModal').find('#addr3').show();
      $('#extendModal').find('#addr4').show();
      $('#extendModal').find('#contact').show();
      $('#extendModal').find('#email').show();
      $('#extendModal').find('#phone').show();
      $('#extendModal').find('#pic').show();

      $('#extendModal').find('#address1').val('');
      $('#extendModal').find('#address2').val('');
      $('#extendModal').find('#address3').val('');
      $('#extendModal').find('#address4').val('');
      $('#extendModal').find('#contact').val('');
      $('#extendModal').find('#email').val('');

      $('#extendModal').find('#company').parents('.form-group').find('.select2-container').hide();
      $('#extendModal').find('#companyText').show();
      $('#extendModal').find('#companyText').val('');
    }
    else{
      $('#extendModal').find('#company').html($('select#customerNoHidden').html());
      $('#extendModal').find('#company').show();
      $('#extendModal').find('#custbranch').show();

      $('#extendModal').find('#addr1').hide();
      $('#extendModal').find('#addr2').hide();
      $('#extendModal').find('#addr3').hide();
      $('#extendModal').find('#addr4').hide();
      $('#extendModal').find('#contact').hide();
      $('#extendModal').find('#email').hide();
      $('#extendModal').find('#phone').hide();
      $('#extendModal').find('#pic').hide();

      $('#extendModal').find('#company').parents('.form-group').find('.select2-container').show();
      $('#extendModal').find('#companyText').hide();
      $('#extendModal').find('#companyText').val('');
    }
  });

  $('#extendModal').find('#branch').on('change', function(){
    //$('#spinnerLoading').show();
    var id = $(this).find(":selected").val();

    $.post('php/getBranch.php', {userID: id}, function(data){
      var obj = JSON.parse(data);
      
      if(obj.status === 'success'){
        $('#extendModal').find('#address1').val(obj.message.address1);
        $('#extendModal').find('#address2').val(obj.message.address2);
        $('#extendModal').find('#address3').val(obj.message.address3);
        $('#extendModal').find('#address4').val(obj.message.address4);
        
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

  $('#extendModal').find('#company').on('change', function(){
    //$('#spinnerLoading').show();
    var id = $(this).find(":selected").val();

    $.post('php/getCustomer.php', {userID: id}, function(data){
      var obj = JSON.parse(data);
      
      if(obj.status === 'success'){
        $('#extendModal').find('#contact').val(obj.message.customer_phone);
        $('#extendModal').find('#email').val(obj.message.customer_email);

        $('#branch').html('');
        $('#branch').append('<option selected="selected">-</option>');

        for(var i=0; i<obj.message.pricing.length; i++){
          var branchInfo = obj.message.pricing[i];
          $('#branch').append('<option value="'+branchInfo.branchid+'">'+branchInfo.name+' - '+branchInfo.address1+' '+branchInfo.address2+' '+branchInfo.address3+' '+branchInfo.address4+'</option>')
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
      //$('#spinnerLoading').hide();
    });
  });

  $('#extendModal').find('#product').on('change', function(){
    var price = parseFloat($(this).find(":selected").attr("data-price"));
    var machine = parseFloat($(this).find(":selected").attr("data-machine"));
    var alat = parseFloat($(this).find(":selected").attr("data-alat"));
    var capacity = parseFloat($(this).find(":selected").attr("data-capacity"));
    var validator = parseFloat($(this).find(":selected").attr("data-validator"));
    var includeCert = $('#includeCert').val();
    var certPrice = 28.6;
    var sst = 0;
    var totalAmt = price;

    $('#unitPrice').val(price);
    $('#machineType').val(machine).trigger('change');
    $('#jenisAlat').val(alat).trigger('change');
    $('#capacity').val(capacity).trigger('change');
    $('#validator').val(validator).trigger('change');

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

  $('#extendModal').find('#unitPrice').on('change', function(){
    var price = parseFloat($(this).val());
    var includeCert = $('#includeCert').val();
    var certPrice = 28.6;
    var sst = 0;
    var totalAmt = price;

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
    var price = parseFloat($('#product').find(":selected").attr("data-price"));
    var includeCert = $(this).val();
    var certPrice = 28.6;
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

  $('#extendModal').find('#machineType').on('change', function(){
    if($('#machineType').val() && $('#jenisAlat').val() && $('#capacity').val() && $('#validator').val()){
      $.post('php/getProductsCriteria.php', {machineType: $('#machineType').val(), jenisAlat: $('#jenisAlat').val(), capacity: $('#capacity').val(), validator: $('#validator').val()}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
          $('#product').val(obj.message.id);
          $('#unitPrice').val(obj.message.price);
          $('#unitPrice').trigger('change');
        }
        else if(obj.status === 'failed'){
          toastr["error"](obj.message, "Failed:");
        }
        else{
          toastr["error"]("Something wrong when pull data", "Failed:");
        }
        //$('#spinnerLoading').hide();
      });
    }
  });

  // $('#extendModal').find('#jenisAlat').on('change', function(){
  //   if($('#machineType').val() && $('#jenisAlat').val() && $('#capacity').val() && $('#validator').val()){
  //     $.post('php/getProductsCriteria.php', {machineType: $('#machineType').val(), jenisAlat: $('#jenisAlat').val(), capacity: $('#capacity').val(), validator: $('#validator').val()}, function(data){
  //       var obj = JSON.parse(data);
        
  //       if(obj.status === 'success'){
  //         $('#product').val(obj.message.id);
  //         $('#unitPrice').val(obj.message.price);
  //         $('#unitPrice').trigger('change');
  //       }
  //       else if(obj.status === 'failed'){
  //         toastr["error"](obj.message, "Failed:");
  //       }
  //       else{
  //         toastr["error"]("Something wrong when pull data", "Failed:");
  //       }
  //       $('#spinnerLoading').hide();
  //     });
  //   }

  //   if(($('#validator').val() == '10' || $('#validator').val() == '9') && $(this).val() == '1'){
  //     $('#addtionalSection').html($('#atkDetails').html());
  //     loadCellCount = 0;
  //     $("#loadCellTable").html('');
  //   }
  //   else{
  //     $('#addtionalSection').html('');
  //   }
  // });

  $('#extendModal').find('#capacity').on('change', function(){
    if($('#machineType').val() && $('#jenisAlat').val() && $('#capacity').val() && $('#validator').val()){
      $.post('php/getProductsCriteria.php', {machineType: $('#machineType').val(), jenisAlat: $('#jenisAlat').val(), capacity: $('#capacity').val(), validator: $('#validator').val()}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
          $('#product').val(obj.message.id);
          $('#unitPrice').val(obj.message.price);
          $('#unitPrice').trigger('change');
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
  });

  // $('#extendModal').find('#validator').on('change', function(){
  //   if($('#machineType').val() && $('#jenisAlat').val() && $('#capacity').val() && $('#validator').val()){
  //     $.post('php/getProductsCriteria.php', {machineType: $('#machineType').val(), jenisAlat: $('#jenisAlat').val(), capacity: $('#capacity').val(), validator: $('#validator').val()}, function(data){
  //       var obj = JSON.parse(data);
        
  //       if(obj.status === 'success'){
  //         $('#product').val(obj.message.id);
  //         $('#unitPrice').val(obj.message.price);
  //         $('#unitPrice').trigger('change');
  //       }
  //       else if(obj.status === 'failed'){
  //         toastr["error"](obj.message, "Failed:");
  //       }
  //       else{
  //         toastr["error"]("Something wrong when pull data", "Failed:");
  //       }
  //       $('#spinnerLoading').hide();
  //     });
  //   }

  //   if(($(this).val() == '10' || $(this).val() == '9') && $('#jenisAlat').val() == '1'){
  //     $('#addtionalSection').html($('#atkDetails').html());
  //     loadCellCount = 0;
  //     $("#loadCellTable").html('');
  //   }
  //   else{
  //     $('#addtionalSection').html('');
  //   }
  // });

  $('#extendModal').find('#lastCalibrationDate').on('blur', function (e) {
    if($(this).val()){
      var parts = $(this).val().split('/');
      var day = parseInt(parts[0], 10);
      var month = parseInt(parts[1], 10) - 1; // Months are zero-based
      var year = parseInt(parts[2], 10);

      var date = new Date(year, month, day);
      
      // Add 1 year to the date
      date.setFullYear(date.getFullYear() + 1);
      date.setDate(date.getDate() - 1);      // Minus 1 day to the date
      
      // Assign the new date to '#expiredCalibrationDate'
      $('#extendModal').find('#expiredCalibrationDate').val(formatDate3(date));
    }
  });

  $('#cancelModal').find('#cancellationReason').on('change', function(){
    if($(this).val() == '0'){
      $('#otherReason').attr("required", true);
    }
  });

  // $(".add-price").click(function(){
  //   var $addContents = $("#pricingDetails").clone();
  //   $("#pricingTable").append($addContents.html());

  //   $("#pricingTable").find('.details:last').attr("id", "detail" + pricingCount);
  //   $("#pricingTable").find('.details:last').attr("data-index", pricingCount);
  //   //$("#pricingTable").find('#remove:last').attr("id", "remove" + pricingCount);

  //   $("#pricingTable").find('#no:last').attr('name', 'no['+pricingCount+']').attr("id", "no" + pricingCount).val((pricingCount + 1).toString());
  //   $("#pricingTable").find('#date:last').attr('name', 'date['+pricingCount+']').attr("id", "date" + pricingCount).val(formatDate2(today));
  //   $("#pricingTable").find('#notes:last').attr('name', 'notes['+pricingCount+']').attr("id", "notes" + pricingCount);
  //   $("#pricingTable").find('#followUpDate:last').attr('name', 'followUpDate['+pricingCount+']').attr("id", "followUpDate" + pricingCount).val(formatDate2(today));
  //   $("#pricingTable").find('#picAttend:last').attr('name', 'picAttend['+pricingCount+']').attr("id", "picAttend" + pricingCount).val('<?=$user ?>');
  //   $("#pricingTable").find('#status').attr('name', 'status['+pricingCount+']').attr("id", "status" + pricingCount).val('Pending');

  //   var newDatePickerId = "datePicker5" + pricingCount;

  //   // Find the newly added date input and set the new ID
  //   var $newDateInputGroup = $("#pricingTable").find('#datePicker5:last');
  //   $newDateInputGroup.attr("id", newDatePickerId);
  //   $newDateInputGroup.find('input').attr("data-target", "#" + newDatePickerId);
  //   $newDateInputGroup.find('.input-group-append').attr("data-target", "#" + newDatePickerId);

  //   // Initialize the date picker on the new element
  //   $newDateInputGroup.datetimepicker({
  //     icons: { time: 'far fa-calendar' },
  //     format: 'DD/MM/YYYY',
  //     defaultDate: today
  //   });

  //   pricingCount++;
  // });

  $(document).on('click', '#add-calibration-cell', function() {
    var loadCalibrationNoValue = parseInt($('#noOfCalibrationCell').val());
    // Trigger the cloning and appending logic loadCellNoValue times
    for (var i = 0; i < loadCalibrationNoValue; i++) {
      var $addContents = $("#loadCalibrationDetails").clone();
      $("#loadCalibrationTable").append($addContents.html());

      // Update the attributes based on the loadCalibrationCount
      $("#loadCalibrationTable").find('.details:last').attr("id", "detail" + loadCalibrationCount);
      $("#loadCalibrationTable").find('.details:last').attr("data-index", loadCalibrationCount);
      $("#loadCalibrationTable").find('#remove:last').attr("id", "remove" + loadCalibrationCount);

      $("#loadCalibrationTable").find('#no:last').attr('name', 'no[' + loadCalibrationCount + ']').attr("id", "no" + loadCalibrationCount).val((loadCalibrationCount + 1).toString());
      $("#loadCalibrationTable").find('#lastCalibrationDate:last').attr('name', 'lastCalibrationDate[' + loadCalibrationCount + ']').attr("id", "lastCalibrationDate" + loadCalibrationCount);
      $("#loadCalibrationTable").find('#expiredCalibrationDate:last').attr('name', 'expiredCalibrationDate[' + loadCalibrationCount + ']').attr("id", "expiredCalibrationDate" + loadCalibrationCount);
      $("#loadCalibrationTable").find('#uploadAttachment:last').attr('name', 'uploadAttachment[' + loadCalibrationCount + ']').attr("id", "uploadAttachment" + loadCalibrationCount);

      loadCalibrationCount++;
    }
  });

  // Event delegation: use 'select' instead of 'input' for dropdowns
  $(document).on('change', 'select[id^="loadCells"]', function(){
    // Retrieve the selected option's attributes
    var brand = $(this).find(":selected").attr('data-brand');
    var model = $(this).find(":selected").attr('data-model');

    // Update the respective inputs for brand and model
    $(this).closest('.details').find('input[id^="loadCellBrand"]').val(brand);
    $(this).closest('.details').find('input[id^="loadCellModel"]').val(model);
  });
});

function format (row) {
  var returnString = `
  <div class="row">
    <!-- Customer Section -->
    <div class="col-md-6">
      <p><span><strong style="font-size:120%; text-decoration: underline;">Customer</strong></span><br>
      <strong>${row.customer}</strong><br>
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
  <div class="row mb-3">
    <h3 class="m-0 text-dark">Machines / Instruments Information</h3>
  </div>
  <div class="row">
    <!-- Machine Section -->
    <div class="col-6">
      <p><strong>Machines / Instruments:</strong> ${row.machines}</p>
      <p><strong>Manufacturing:</strong> ${row.manufacturing}</p>
      <p><strong>Model:</strong> ${row.model}</p>
      <p><strong>Structure Size:</strong> ${row.size}</p>
    </div>
    <div class="col-6">
      <p><strong>Unit Serial No:</strong> ${row.unit_serial_no}</p>
      <p><strong>Brand:</strong> ${row.brand}</p>
      <p><strong>Capacity:</strong> ${row.capacity}</p>
    </div>
  </div>`;

  if (row.lastCalibrationDate && row.expiredCalibrationDate && row.auto_form_no) {
    returnString += `
                <hr><div class="row mb-3">
                  <h3 class="m-0 text-dark">Calibration Information</h3>
                </div>
                <div class="row">
                  <!-- Calibration Section -->
                  <div class="col-6">
                    <p><strong>Last Calibration Date:</strong> ${row.lastCalibrationDate}</p>
                    <p><strong>Certificate Number:</strong> ${row.auto_form_no}</p>
                  </div>
                  <div class="col-6">
                    <p><strong>Expired Calibration Date:</strong> ${row.expiredCalibrationDate}</p>
                    <p><strong>Certificate Attachments:</strong></p>
    `;

    // Check each certFilePath individually and add tooltips
    for (let i = 1; i <= 5; i++) {
        let filePath = row[`certFilePath${i}`];
        if (filePath) {
            returnString += `
                <a href="${filePath}" target="_blank" class="btn btn-success btn-sm" role="button" title="View Certificate Attachment ${i}">
                    <i class="fa fa-file-pdf-o"></i>
                </a> `;
        }
    }

    returnString += '</p></div></div>';
  }

  
  // if (row.calibrations !== undefined && row.calibrations !== null && row.calibrations !== ''){
  //   if (row.calibrations[0].length > 0) {
  //     returnString += '<h4>Calibrations</h4><table style="width: 100%;"><thead><tr><th width="5%">No.</th><th width="20%">Latest Date Calibration</th><th width="20%">Expire Date Calibration</th><th width="20%">Calibration Certificate Attachment</th></tr></thead><tbody>'
      
  //     var calibrations = row.calibrations[0];
  //     for (var i = 0; i < calibrations.length; i++) {
  //       var item = calibrations[i];
  //       returnString += '<tr><td>' + item.no + '</td><td>' + item.lastCalibrationDate + '<td>' + item.expiredCalibrationDate + '</td><td>';

  //       if (item.calibrationFilePath) {
  //         returnString += '<a href="' + item.calibrationFilePath + '" target="_blank" class="btn btn-success btn-sm" role="button"><i class="fa fa-file-pdf-o"></i></a>';
  //       }
  //     }

  //     returnString += '</td></tr></tbody></table>';
  //   }
  // }

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
  // $('#extendModal').find('#type').val("OWN");
  // $('#isResseller').hide();
  // $('#isResseller2').hide();
  // $('#isResseller3').hide();
  // $('#isResseller4').hide();
  // $('#isResseller5').hide();
  $('#extendModal').find('#customerType').val("EXISTING").attr('readonly', false).trigger('change');
  $('#extendModal').find('#brand').val('').trigger('change');
  $('#extendModal').find('#validator').val('').trigger('change');
  $('#extendModal').find('#product').val('');
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
  $('#extendModal').find('#validationDate').val(formattedDate);
  loadCalibrationCount = 0; 
  $('#loadCalibrationTable').html('');

  // $('#pricingTable').html('');
  // pricingCount = 0;
  // $('#extendModal').find('#unitPrice').val("");
  // $('#extendModal').find('#certPrice').val('');
  // $('#extendModal').find('#totalAmount').val("");
  // $('#extendModal').find('#sst').val('');
  // $('#extendModal').find('#subAmount').val('');
  // $('#cerId').hide();
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

function extraAction(id){
  $('#spinnerLoading').show();
  $.post('php/getStamp.php', {userID: id}, function(data){
    var obj = JSON.parse(data);
    
    if(obj.status === 'success'){
      $('#extraDetModal').find('#id').val(obj.message.id);

      $('#extraDetModal').modal('show');

      $('#extraDetForm').validate({
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
  });
}

function edit(id) {
  $('#spinnerLoading').show();
  $.post('php/getValidation.php', {validationId: id}, function(data){
    var obj = JSON.parse(data); 
    if(obj.status === 'success'){ 
      if(obj.message.type == 'DIRECT'){
        $('#extendModal').find('#id').val(obj.message.id);
        $('#extendModal').find('#type').val(obj.message.type).trigger('change');
        $('#extendModal').find('#dealer').val('');
        $('#extendModal').find('#reseller_branch').val('');
        $('#extendModal').find('#customerType').val(obj.message.customer_type).attr('disabled', true).trigger('change');
        $('#extendModal').find('#customerTypeEdit').val(obj.message.customer_type);
        $('#extendModal').find('#company').val(obj.message.customer).trigger('change');
        $('#extendModal').find('#companyText').val('');
        $('#extendModal').find('#validator').val(obj.message.validate_by).trigger('change');
        $('#extendModal').find('#autoFormNo').val(obj.message.auto_form_no);
        setTimeout(function(){
          $('#extendModal').find('#branch').val(obj.message.branch).trigger('change');
        }, 1000);
        $('#extendModal').find('#machineType').val(obj.message.machines).trigger('change');
        $('#extendModal').find('#serial').val(obj.message.unit_serial_no);
        $('#extendModal').find('#manufacturing').val(obj.message.manufacturing).trigger('change');
        $('#extendModal').find('#brand').val(obj.message.brand).trigger('change');
        $('#extendModal').on('modelsLoaded', function() {
            $('#extendModal').find('#model').val(obj.message.model);
        });
        $('#extendModal').find('#capacity').val(obj.message.capacity).trigger('change');
        $('#extendModal').find('#size').val(obj.message.size).trigger('change');
        $('#extendModal').find('#validationDate').val(formatDate3(obj.message.validation_date));
        $('#extendModal').find('#lastCalibrationDate').val(formatDate3(obj.message.lastCalibrationDate));
        $('#extendModal').find('#expiredCalibrationDate').val(formatDate3(obj.message.expiredCalibrationDate));
        for (let i = 1; i <= 5; i++) {
          // Clear the file input for each attachment
          $('#extendModal').find(`#uploadAttachment${i}`).val('');

          // Check if the certFilePath exists and update the modal
          if (obj.message[`certFilePath${i}`]) {
              const certFilePath = obj.message[`certFilePath${i}`];
              $('#extendModal').find(`#viewCalibrationPdf${i}`).attr('href', certFilePath).show();
              $('#extendModal').find(`#calibrationFilePath${i}`).val(certFilePath);
          }
        }
        // if(obj.message.calibrations != null && obj.message.calibrations.length > 0){
        //   $("#loadCalibrationTable").html('');
        //   loadCalibrationCount = 0; 

        //   for(var i = 0; i < obj.message.calibrations.length; i++){
        //     var calibrations = obj.message.calibrations[i];

        //     for(var j=0; j < calibrations.length; j++){
        //       var item = calibrations[j];
        //       var $addContents = $("#loadCalibrationDetails").clone();
        //       $("#loadCalibrationTable").append($addContents.html());

        //       $("#loadCalibrationTable").find('.details:last').attr("id", "detail" + loadCalibrationCount);
        //       $("#loadCalibrationTable").find('.details:last').attr("data-index", loadCalibrationCount);
        //       $("#loadCalibrationTable").find('#remove:last').attr("id", "remove" + loadCalibrationCount);

        //       $("#loadCalibrationTable").find('#no:last').attr('name', 'no['+loadCalibrationCount+']').attr("id", "no" + loadCalibrationCount).val(item.no);
        //       $("#loadCalibrationTable").find('#lastCalibrationDate:last').attr('name', 'lastCalibrationDate['+loadCalibrationCount+']').attr("id", "lastCalibrationDate" + loadCalibrationCount).val(item.lastCalibrationDate);
        //       $("#loadCalibrationTable").find('#expiredCalibrationDate:last').attr('name', 'expiredCalibrationDate['+loadCalibrationCount+']').attr("id", "expiredCalibrationDate" + loadCalibrationCount).val(item.expiredCalibrationDate);

        //       $("#loadCalibrationTable").find('#uploadAttachment:last').attr('name', 'uploadAttachment['+loadCalibrationCount+']').attr("id", "uploadAttachment" + loadCalibrationCount).removeAttr('required');
        //       if(item.calibrationFilePath){
        //         $("#loadCalibrationTable").find('#viewCalibrationPdf:last').attr('name', 'viewCalibrationPdf['+loadCalibrationCount+']').attr("id", "viewCalibrationPdf" + loadCalibrationCount).attr('href', item.calibrationFilePath).show();
        //         $("#loadCalibrationTable").find('#calibrationFilePath:last').attr('name', 'calibrationFilePath['+loadCalibrationCount+']').attr("id", "calibrationFilePath" + loadCalibrationCount).val(item.calibrationFilePath);
        //       }

        //       loadCalibrationCount++;
        //     }
        //   }
        // }
      }
      else{
        $('#extendModal').find('#id').val(obj.message.id);
        $('#extendModal').find('#type').val(obj.message.type).trigger('change');
        $('#extendModal').find('#dealer').val(obj.message.dealer).trigger('change');
        setTimeout(function(){
          $('#extendModal').find('#reseller_branch').val(obj.message.dealer_branch).trigger('change');
          $('#extendModal').find('#company').val(obj.message.customer).trigger('change');
        }, 500);
        setTimeout(function(){
          $('#extendModal').find('#branch').val(obj.message.branch).trigger('change');
        }, 1000);
        $('#extendModal').find('#customerType').val(obj.message.customer_type).attr('disabled', true).trigger('change');
        $('#extendModal').find('#customerTypeEdit').val(obj.message.customer_type);
        $('#extendModal').find('#validator').val(obj.message.validate_by).trigger('change');
        $('#extendModal').find('#autoFormNo').val(obj.message.auto_form_no);
        $('#extendModal').find('#machineType').val(obj.message.machines).trigger('change');
        $('#extendModal').find('#serial').val(obj.message.unit_serial_no);
        $('#extendModal').find('#manufacturing').val(obj.message.manufacturing).trigger('change');
        $('#extendModal').find('#brand').val(obj.message.brand).trigger('change');
        $('#extendModal').on('modelsLoaded', function() {
            $('#extendModal').find('#model').val(obj.message.model);
        });
        $('#extendModal').find('#capacity').val(obj.message.capacity).trigger('change');
        $('#extendModal').find('#size').val(obj.message.size).trigger('change');
        $('#extendModal').find('#validationDate').val(formatDate3(obj.message.validation_date));
        $('#extendModal').find('#lastCalibrationDate').val(formatDate3(obj.message.lastCalibrationDate));
        $('#extendModal').find('#expiredCalibrationDate').val(formatDate3(obj.message.expiredCalibrationDate));
        for (let i = 1; i <= 5; i++) {
          // Clear the file input for each attachment
          $('#extendModal').find(`#uploadAttachment${i}`).val('');

          // Check if the certFilePath exists and update the modal
          if (obj.message[`certFilePath${i}`]) {
              const certFilePath = obj.message[`certFilePath${i}`];
              $('#extendModal').find(`#viewCalibrationPdf${i}`).attr('href', certFilePath).show();
              $('#extendModal').find(`#calibrationFilePath${i}`).val(certFilePath);
          }
        }

        // if(obj.message.calibrations != null && obj.message.calibrations.length > 0){
        //   $("#loadCalibrationTable").html('');
        //   loadCalibrationCount = 0; 

        //   for(var i = 0; i < obj.message.calibrations.length; i++){
        //     var calibrations = obj.message.calibrations[i];

        //     for(var j=0; j < calibrations.length; j++){
        //       var item = calibrations[j];
        //       var $addContents = $("#loadCalibrationDetails").clone();
        //       $("#loadCalibrationTable").append($addContents.html());

        //       $("#loadCalibrationTable").find('.details:last').attr("id", "detail" + loadCalibrationCount);
        //       $("#loadCalibrationTable").find('.details:last').attr("data-index", loadCalibrationCount);
        //       $("#loadCalibrationTable").find('#remove:last').attr("id", "remove" + loadCalibrationCount);

        //       $("#loadCalibrationTable").find('#no:last').attr('name', 'no['+loadCalibrationCount+']').attr("id", "no" + loadCalibrationCount).val(item.no);
        //       $("#loadCalibrationTable").find('#lastCalibrationDate:last').attr('name', 'lastCalibrationDate['+loadCalibrationCount+']').attr("id", "lastCalibrationDate" + loadCalibrationCount).val(item.lastCalibrationDate);
        //       $("#loadCalibrationTable").find('#expiredCalibrationDate:last').attr('name', 'expiredCalibrationDate['+loadCalibrationCount+']').attr("id", "expiredCalibrationDate" + loadCalibrationCount).val(item.expiredCalibrationDate);

        //       $("#loadCalibrationTable").find('#uploadAttachment:last').attr('name', 'uploadAttachment['+loadCalibrationCount+']').attr("id", "uploadAttachment" + loadCalibrationCount).removeAttr('required');

        //       if(item.calibrationFilePath){
        //         $("#loadCalibrationTable").find('#viewCalibrationPdf:last').attr('name', 'viewCalibrationPdf['+loadCalibrationCount+']').attr("id", "viewCalibrationPdf" + loadCalibrationCount).attr('href', item.calibrationFilePath).show();
        //         $("#loadCalibrationTable").find('#calibrationFilePath:last').attr('name', 'calibrationFilePath['+loadCalibrationCount+']').attr("id", "calibrationFilePath" + loadCalibrationCount).val(item.calibrationFilePath);
        //       }
             
        //       loadCalibrationCount++;
        //     }
        //   }
        // }
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

  // Hide the spinner when the modal is closed
  // $('#extendModal').on('hidden.bs.modal', function() {
  //   $('#spinnerLoading').hide(); 
  //   location.reload();
  // });
}

function complete(id) {
  if (confirm('Are you sure you want to complete this items?')) {
    $('#spinnerLoading').show();
    $.post('php/completeStamp.php', {userID: id}, function(data){
      var obj = JSON.parse(data);

      if(obj.status === 'success'){
        toastr["success"](obj.message, "Success:");
        $('#weightTable').DataTable().ajax.reload(null, false);
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
    $.post('php/getValidation.php', {validationId: id}, function(data){
      var obj = JSON.parse(data);

      if(obj.status == 'success'){
        $('#cancelModal').find('#id').val(obj.message.id);
        $('#cancelModal').modal('show');

        $('#cancelForm').validate({
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
      } else if(obj.status === 'failed'){
        toastr["error"](obj.message, "Failed:");
      }
      else{
        toastr["error"]("Something wrong when pull data", "Failed:");
      }

      $('#spinnerLoading').hide();

    });
  }
}

function revertToPending(id) {
  
  if (confirm('Are you sure you want to set status back to Pending?')) {
    $('#spinnerLoading').show();
    $.post('php/changeStatusValidation.php', {userID: id}, function(data){
      var obj = JSON.parse(data);

      if(obj.status === 'success'){
        toastr["success"](obj.message, "Success:");
        $('#weightTable').DataTable().ajax.reload(null, false);
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
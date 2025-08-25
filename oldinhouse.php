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
  $_SESSION['page']='oldinhouse';
	
	if(($row = $result->fetch_assoc()) !== null){
    $role = $row['role_code'];
    $branch = $row['branch'];
  }
  $stmt->close();

  $autoFormNos = $db->query("SELECT DISTINCT auto_form_no FROM inhouse_validations WHERE deleted='0'");
  $dealer = $db->query("SELECT * FROM dealer WHERE deleted = '0'");
  $customers = $db->query("SELECT * FROM customers WHERE customer_status = 'CUSTOMERS' AND deleted = '0'");
  $customers2 = $db->query("SELECT * FROM customers WHERE customer_status = 'CUSTOMERS' AND deleted = '0'");
  $machinetypes = $db->query("SELECT * FROM machines WHERE deleted = '0'");
  $brands = $db->query("SELECT * FROM brand WHERE deleted = '0'");
  $models = $db->query("SELECT * FROM model WHERE deleted = '0'");
  $sizes = $db->query("SELECT * FROM size WHERE deleted = '0'");
  $capacities = $db->query("SELECT * FROM capacity WHERE deleted = '0'");
  $capacities2 = $db->query("SELECT * FROM capacity WHERE deleted = '0'");
  $problems = $db->query("SELECT * FROM problem WHERE deleted = '0'");
  $users = $db->query("SELECT * FROM users WHERE deleted = '0'");
  $users2 = $db->query("SELECT * FROM users WHERE deleted = '0'");
  $validators = $db->query("SELECT * FROM validators WHERE deleted = '0' AND type = 'INHOUSE'");
  $validators2 = $db->query("SELECT * FROM validators WHERE deleted = '0' AND type = 'INHOUSE'");
  $alats = $db->query("SELECT * FROM alat WHERE deleted = '0'");
  $products = $db->query("SELECT * FROM products WHERE deleted = '0'");
  $cancelledReasons = $db->query("SELECT * FROM reasons WHERE deleted = '0'");
  $country = $db->query("SELECT * FROM country");
  $country2 = $db->query("SELECT * FROM country");

  if($role != 'ADMIN' && $role != 'SUPER_ADMIN'){
    $companyBranches = $db->query("SELECT * FROM company_branches WHERE deleted = '0' AND id = '$branch' ORDER BY branch_name ASC");
  }
  else{
    $companyBranches = $db->query("SELECT * FROM company_branches WHERE deleted = '0' ORDER BY branch_name ASC");
  }

  if($role != 'ADMIN' && $role != 'SUPER_ADMIN'){
    $companyBranches2 = $db->query("SELECT * FROM company_branches WHERE deleted = '0' AND id = '$branch' ORDER BY branch_name ASC");
  }
  else{
    $companyBranches2 = $db->query("SELECT * FROM company_branches WHERE deleted = '0' ORDER BY branch_name ASC");
  }

  $loadCells = $db->query("SELECT load_cells.*, machines.machine_type AS machinetype, brand.brand AS brand_name, model.model AS model_name, alat.alat, country.nicename 
FROM load_cells, machines, brand, model, alat, country WHERE load_cells.machine_type = machines.id AND load_cells.brand = brand.id AND load_cells.model = model.id 
AND load_cells.jenis_alat = alat.id AND load_cells.made_in = country.id AND load_cells.deleted = '0'");

  $db->close(); // Close the database connection
}
?>
<style>
  #weightTable tbody tr.odd:hover {
    background-color: #DFFFFD; /* Light gray color on hover */
    cursor: pointer;          /* Pointer cursor to indicate clickability */
  }

  #weightTable tbody tr.even:hover {
    background-color: #DFFFFD; /* Light gray color on hover */
    cursor: pointer;          /* Pointer cursor to indicate clickability */
  }

  th {
    text-align: center;
  }
</style>

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
        <h1 class="m-0 text-dark">Cancelled In-House Validations</h1>
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
                <div class="col-4">
                  <div class="form-group">
                    <label>Branch:</label>
                    <select class="form-control select2" id="branchFilter" name="branchFilter">
                      <option value="" selected disabled hidden>Please Select</option>
                      <?php while ($row = mysqli_fetch_assoc($companyBranches)) { ?>
                          <option value="<?= $row['id'] ?>"><?= $row['branch_name'] ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="form-group col-4">
                  <label>From Inhouse Date:</label>
                  <div class="input-group date" id="fromDatePicker" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input" data-target="#fromDatePicker" id="fromDate"/>
                    <div class="input-group-append" data-target="#fromDatePicker" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
                </div>

                <div class="form-group col-4">
                  <label>To Expired Date:</label>
                  <div class="input-group date" id="toDatePicker" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input" data-target="#toDatePicker" id="toDate"/>
                    <div class="input-group-append" data-target="#toDatePicker" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
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
              <div class="col-10"><p class="mb-0" style="font-size: 110%">InHouse Validation Record Pending / Expired Status </p></div>
              <div class="col-2">
                <button type="button" class="btn btn-block btn-sm bg-gradient-danger" id="multiDeactivate" data-bs-toggle="tooltip" title="Delete Inhouse Validations"><i class="fa-solid fa-ban"></i> Delete Validations</button>
              </div>
              <!-- <div class="col-2">
                <button type="button" class="btn btn-block bg-gradient-info btn-sm" id="exportBorangs">Export Borangs</button>
              </div> -->
              <!--div class="col-2">
                <a href="/template/Stamping Record Template.xlsx" download><button type="button" class="btn btn-block bg-gradient-danger btn-sm" id="downloadExccl">Download Template</button></a>
              </div-->
              <!--div class="col-2">
                <button type="button" class="btn btn-block bg-gradient-success btn-sm" id="uploadExccl">Upload Excel</button>
              </div-->
              <!-- <div class="col-2">
                <button type="button" class="btn btn-block bg-gradient-warning btn-sm" onclick="newEntry()">Add New</button>
              </div> -->
            </div>
          </div>

          <div class="card-body">
            <table id="weightTable" class="table table-bordered table-striped display">
              <thead>
                <tr>
                  <th><input type="checkbox" id="selectAllCheckbox" class="selectAllCheckbox"></th>
                  <th>No</th>
                  <th>Company Name</th>
                  <th>Brand</th>
                  <th>Description Instruments for Weighing and Measuring</th>
                  <th width="10%">Capacity</th>
                  <th>Previous Cert. No</th>
                  <th>Inhouse Date</th>
                  <th>Expired Date</th>
                  <th>Calibrator By</th>
                  <th>Status</th>
                  <th>Action</th>
                  <!-- <th></th> -->
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
            <div class="col-4">
              <div class="form-group">
                <label>Company Branch * </label>
                <select class="form-control select2" id="companyBranch" name="companyBranch" required>
                  <?php while ($row = mysqli_fetch_assoc($companyBranches2)) { ?>
                      <option value="<?= $row['id'] ?>"><?= $row['branch_name'] ?></option>
                  <?php } ?>
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
              <div class="row">
                <div class="col-3">
                  <div class="form-group">
                    <label>Customer Type * </label>
                    <select class="form-control" style="width: 100%;" id="customerType" name="customerType" required>
                      <option value="NEW">NEW</option>
                      <option value="EXISTING">EXISTING</option>
                    </select>
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
                <div class="col-3">
                  <div class="form-group">
                    <label>Auto Form No. * </label>
                    <input type="text" class="form-control" id="autoFormNo" name="autoFormNo" required>
                  </div>
                </div>
                <div class="col-12" id="custbranch">
                  <div class="form-group">
                    <label>Branch * </label>
                    <select class="form-control select2" style="width: 100%;" id="branch" name="branch" required></select>
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
                <div class="col-3 d-flex">
                  <div class="col-6">
                    <div class="form-group">
                      <label>Unit Serial No * </label>
                      <input class="form-control" type="text" placeholder="Serial No." id="serial" name="serial" required>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="form-group">
                      <label>Last Calibration Date * </label>
                      <input class="form-control" type="date" id="lastCalibrationDate" name="lastCalibrationDate" required>
                    </div>
                  </div>
                </div>
                <div class="col-3 d-flex">
                  <div class="col-6">
                    <div class="form-group">
                      <label>Expired Date * </label>
                      <input class="form-control" type="date" id="expiredDate" name="expiredDate" required>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="form-group">
                      <label>Manufacturing *</label>
                      <select class="form-control select2" style="width: 100%;" id="manufacturing" name="manufacturing" required>
                        <option selected="selected"></option>
                        <option value="Local OEM">Local OEM</option>
                        <option value="Overseas">Overseas</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="col-3">
                  <div class="form-group">
                    <label>Auto Certificate No / Sticker No *</label>
                    <input class="form-control" type="text" placeholder="Sticker No" id="auto_cert_no" name="auto_cert_no">
                  </div>
                </div>
                <div class="col-3 d-flex">
                  <div class="col-6">
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
                  <div class="col-6">
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
                </div>
                <div class="col-3 d-flex">
                  <div class="col-6">
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
                  <div class="col-6">
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
                </div>
                <div class="col-3">
                  <div class="form-group">
                    <label>Inhouse Calibrator * </label>
                    <select class="form-control select2" style="width: 100%;" id="calibrator" name="calibrator" required>
                      <option selected="selected"></option>
                      <?php while($user=mysqli_fetch_assoc($users2)){ ?>
                        <option value="<?=$user['id'] ?>"><?=$user['name'] ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="col-3">
                  <div class="form-group">
                    <label>Validation Date * </label>
                    <input class="form-control" type="date" placeholder="dd/mm/yyyy" id="validationDate" name="validationDate" required>
                  </div>
                </div>
              </div>
            </div>
          </div>

          div class="card card-primary">
            <div class="card-body">
              <div class="row mb-3">
                <div class="col-10">
                  <h4 id="calibrationHeader">Note - Standard Average Temperature:	() / Average Relative Humidity:	()</h4>
                </div>
                <!-- <div class="col-2">
                  <button style="margin-left:auto;margin-right: 25px;" type="button" class="btn btn-primary add-load-cell" id="add-testing-cell">Add Testing</button>
                </div> -->
              </div>
              
              <table style="width: 100%;">
                <thead>
                  <tr>
                    <th width="10%">Number of Tests.</th>
                    <th width="20%">Setting Value Of Standard</th>
                    <th width="20%">As Received Under Calibration.</th>
                    <th width="20%" id="varianceHeader">Variance +/- </th>
                    <th width="20%">Reading After Adjustment.</th>
                  </tr>
                </thead>
                <tbody id="loadTestingTable">
                  <?php
                    for ($i=1; $i <=10; $i++){
                      echo "
                        <tr class='details'>
                          <td class='pr-5'>
                            <input type='text' class='form-control' id='no$i' name='no$i' value='$i' readonly>
                          </td>   
                          <td>
                            <div class='d-flex mt-1'>
                              <div class='col-6'>
                                <input type='number' placeholder='0.0' id='standardValue$i' name='standardValue$i' class='form-control' style='width: 100%;' value='0.0'>
                              </div>
                              <div class='col-2'>
                                <i class='fas fa-minus fa-2x'></i>
                              </div>
                              <div class='col-3'>
                                <span class='form-control' id='unitSymbolSV$i' style='background-color:lightgrey;'></span>
                              </div>
                            </div>
                          </td>
                          <td>
                            <div class='d-flex mt-1'> 
                              <div class='col-6'>
                                <input class='form-control' type='number' placeholder='0.0' id='calibrationReceived$i' name='calibrationReceived$i' style='width: 100%;' value='0.0'>
                              </div>
                              <div class='col-2'>
                                <i class='fas fa-minus fa-2x'></i>
                              </div>
                              <div class='col-3'>
                                <span class='form-control' id='unitSymbolCR$i' style='background-color:lightgrey;'></span>
                              </div>
                            </div>
                          </td>
                          <td>
                            <div class='d-flex mt-1'>
                              <div class='col-6'>
                                <input class='form-control' type='number' placeholder='0.0' id='variance$i' name='variance$i' style='width: 100%; background-color: lightgrey;' value='0.0' readonly>
                              </div>
                              <div class='col-2'>
                                <i class='fas fa-minus fa-2x'></i>
                              </div>
                              <div class='col-3'>
                                <span class='form-control' id='unitSymbolV$i' style='background-color:lightgrey;'></span>
                              </div>
                            </div>
                          </td>
                          <td>
                            <div class='d-flex mt-1'>
                              <div class='col-6'>
                                <input class='form-control' type='number' placeholder='0.0' id='afterAdjustReading$i' name='afterAdjustReading$i' value='0.0' style='width: 100%; background-color: lightgreen;'>
                              </div>
                              <div class='col-2'>
                                <i class='fas fa-minus fa-2x'></i>
                              </div>
                              <div class='col-3'>
                                <span class='form-control' id='unitSymbolAR$i' style='background-color:lightgrey;'></span>
                              </div>
                            </div>
                          </td>   
                        </tr>        
                      ";
                    }
                  ?>
                </tbody>
              </table>
            </div>
          </div>

          <!-- <div class="card card-primary">
            <div class="card-body">
              <div class="row mb-3">
                <div class="col-10">
                  <h4>Note - Standard Average Temperature:	(20 + 1)ºC / Average Relative Humidity:	(52 + 1)%RH</h4>
                </div>
                <div class="col-2">
                  <button style="margin-left:auto;margin-right: 25px;" type="button" class="btn btn-primary add-load-cell" id="add-testing-cell">Add Testing</button>
                </div>
              </div>
              
              <table style="width: 100%;">
                <thead>
                  <tr>
                    <th width="15%">Number of Tests.</th>
                    <th width="20%">Setting Value Of Standard</th>
                    <th width="20%">As Received Under Calibration.</th>
                    <th width="20%">Variance +/- 0.1kg</th>
                    <th width="20%">Reading After Adjustment.</th>
                    <th width="5%">Delete</th>
                  </tr>
                </thead>
                <tbody id="loadTestingTable"></tbody>
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
                <label>Other Reason</label>
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

<div class="modal fade" id="logModal"> 
  <div class="modal-dialog modal-xl" style="max-width: 80%;">
    <div class="modal-content">

      <div class="modal-header bg-gray-dark color-palette">
        <h4 class="modal-title">System Log</h4>
        <button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
        <table class="table table-striped table-bordered" id="logTable">
          <thead>
            <tr>
              <th>No.</th>
              <th>User</th>
              <th>Action</th>
              <th>Date</th>
              <th>Cancellation Reason</th>
              <th>Remark</th>
            </tr>
          </thead>
          <tbody>
          </tbody>
        </table>
      </div>

      <div class="modal-footer justify-content-between bg-gray-dark color-palette">
        <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script type="text/html" id="loadTestingDetails">
  <tr class="details">
    <td>
      <input type="text" class="form-control" id="no" name="no" readonly>
      <span class="form-control" id="noText" name="noText"></span>
    </td>
    <td>
      <div class="d-flex mt-1">
        <div class="col-6">
          <input type="number" placeholder="0.0" id="standardValue" name="standardValue" class="form-control" style="width: 100%;" value="0.0">
        </div>
        <div class="col-2">
          <i class="fas fa-minus fa-2x"></i>
        </div>
        <div class="col-4">
          <span class="form-control" id="unitSymbolSV" style="background-color:lightgrey;"></span>
        </div>
      </div>
    </td>
    <td>
      <div class="d-flex mt-1"> 
        <div class="col-6">
          <input class="form-control" type="number" placeholder="0.0" id="calibrationReceived" name="calibrationReceived" style="width: 100%;" value="0.0">
        </div>
        <div class="col-2">
          <i class="fas fa-minus fa-2x"></i>
        </div>
        <div class="col-4">
          <span class="form-control" id="unitSymbolCR" style="background-color:lightgrey;"></span>
        </div>
      </div>
    </td>
    <td>
      <div class="d-flex mt-1">
        <div class="col-6">
          <input class="form-control" type="number" placeholder="0.0" id="variance" name="variance" style="width: 100%; background-color: lightgrey;" value="0.0" readonly>
        </div>
        <div class="col-2">
          <i class="fas fa-minus fa-2x"></i>
        </div>
        <div class="col-4">
          <span class="form-control" id="unitSymbolV" style="background-color:lightgrey;"></span>
        </div>
      </div>
    </td>
    <td>
      <div class="d-flex mt-1">
        <div class="col-6">
          <input class="form-control" type="number" placeholder="0.0" id="afterAdjustReading" name="afterAdjustReading" value="0.0" style="width: 100%; background-color: lightgreen;">
        </div>
        <div class="col-2">
          <i class="fas fa-minus fa-2x"></i>
        </div>
        <div class="col-4">
          <span class="form-control" id="unitSymbolAR" style="background-color:lightgrey;"></span>
        </div>
      </div>
    </td>
    
    <td><button class="btn btn-danger btn-sm" id="remove"><i class="fa fa-times"></i></button></td>
  </tr>
</script>

<script>
var loadTestingCount = $("#loadTestingTable").find(".details").length;
var isModalOpen = false; // Flag to track modal visibility

$(function () {
  $('#customerNoHidden').hide();
  $('#add-testing-cell').hide();

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

  $('#validatorFilter').val(15).trigger('change');
  var fromDateValue = $('#fromDate').val();
  var toDateValue = $('#toDate').val();
  var customerNoFilter = $('#customerNoFilter').val() ? $('#customerNoFilter').val() : '';
  var validatorFilter = $('#validatorFilter').val() ? $('#validatorFilter').val() : '';
  var autoFormNoFilter = $('#autoFormNoFilter').val() ? $('#autoFormNoFilter').val() : '';
  var branchFilter = $('#branchFilter').val() ? $('#branchFilter').val() : '';
  //var statusFilter = $('#statusFilter').val() ? $('#statusFilter').val() : '';

  var table = $("#weightTable").DataTable({
    "responsive": true,
    "autoWidth": false,
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
    'searching': true,
    // "stateSave": true,
    'order': [[ 2, 'asc' ]],
    // 'columnDefs': [ { orderable: false, targets: [0] }],
    'ajax': {
      'type': 'POST',
      'url':'php/filterCancelledInHouseValidation.php',
      'data': {
        fromDate: fromDateValue,
        toDate: toDateValue,
        customer: customerNoFilter,
        validator: validatorFilter,
        autoFormNo: autoFormNoFilter,
        branch: branchFilter,
        status: 'Pending'
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
      {
        data: null, // The data property is null since this column is client-side only
        className: 'auto-increment',
        orderable: false,
        render: function (data, type, row, meta) {
          // meta.row provides the row index in the current page
          return meta.row + meta.settings._iDisplayStart + 1;
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
      {
        data: 'id',
        className: 'action-button',
        render: function (data, type, row) {
          let dropdownMenu = '<div class="dropdown" style="width=20%">' +
            '<button class="btn btn-secondary btn-sm" type="button" id="dropdownMenuButton' + data + '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
            '<i class="fa-solid fa-ellipsis"></i>' +
            '</button>' +
            '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton' + data + '">';

            if ('<?=$role ?>' == 'ADMIN' || '<?=$role ?>' == 'SUPER_ADMIN') {
              dropdownMenu += 
                '<a class="dropdown-item" id="revertBtn' + data + '" onclick="revertToPending(' + data + ')"><i class="fa fa-arrow-circle-left"></i> Revert</a>'+
                '<a class="dropdown-item" id="log' + data + '" onclick="log(' + data + ')"><i class="fa fa-list" aria-hidden="true"></i> Log</a>'+
                '<a class="dropdown-item" id="delete'+data+'" onclick="deactivate(' + data + ')"><i class="fa fa-times" aria-hidden="true"></i> Delete</a>';
            }else{
              dropdownMenu += '<a class="dropdown-item" id="log' + data + '" onclick="log(' + data + ')"><i class="fa fa-list" aria-hidden="true"></i> Log</a>';
            }
            
          
          dropdownMenu += '</div></div>';

          return dropdownMenu;
        }
      },
      // { 
      //   data: 'id',
      //   render: function ( data, type, row ) {
      //     let buttons = '<div class="row">';

      //       if ('<?=$role ?>' == 'ADMIN' || '<?=$role ?>' == 'SUPER_ADMIN') { // Assuming 'isInvoiced' is a boolean field in your row data
      //         buttons +=  '<div class="col-4"><button title="Revert" type="button" id="pendingBtn'+data+'" onclick="revertToPending('+data+
      //         ')" class="btn btn-success btn-sm"><i class="fa fa-arrow-circle-left"></i></button></div>';

      //         // System Log
      //         buttons += '<div class="col-4"><button title="Log" type="button" id="log'+data+'" onclick="log('+data+')" class="btn btn-info btn-sm"><i class="fa fa-list" aria-hidden="true"></i></button></div>';

      //         buttons += '<div class="col-4"><button title="Delete" type="button" id="delete'+data+'" onclick="deactivate('+data+')" class="btn btn-danger btn-sm">X</button></div>';

      //         return buttons;
      //       } 
      //       else {
      //         // System Log
      //         buttons += '<div class="col-4"><button title="Log" type="button" id="log'+data+'" onclick="log('+data+')" class="btn btn-info btn-sm"><i class="fa fa-list" aria-hidden="true"></i></button></div>';
      //         return buttons; // Return an empty string or any other placeholder if the item is invoiced
      //       }
      //   }
      // },
      // { 
      //   className: 'dt-control',
      //   orderable: false,
      //   data: null,
      //   render: function ( data, type, row ) {
      //     return '<td class="table-elipse" data-toggle="collapse" data-target="#demo'+row.auto_form_no+'"><i class="fas fa-angle-down"></i></td>';
      //   }
      // }
    ],
    "lengthMenu": [ [10, 25, 50, 100, 300, 600, 1000], [10, 25, 50, 100, 300, 600, 1000] ], // More show options
    "pageLength": 10 // Default rows per page
  });
  
  $('#weightTable tbody').on('click', 'tr', function (e) {
      var tr = $(this); // The row that was clicked
      var row = table.row(tr);

      // Exclude specific td elements by checking the event target
      if ($(e.target).closest('td').hasClass('select-checkbox') || $(e.target).closest('td').hasClass('action-button')) {
          return;
      }

      if (row.child.isShown()) {
          // This row is already open - close it
          row.child.hide();
          tr.removeClass('shown');
      } else {
          $.post('php/getInHouseValidation.php', { validationId: row.data().id, format: 'EXPANDABLE' }, function (data) {
              var obj = JSON.parse(data);
              if (obj.status === 'success') {
                  row.child(format(obj.message)).show();
                  tr.addClass("shown");
              }
          });
      }
  });

  // $('#weightTable tbody').on('click', 'td.dt-control', function () {
  //   var tr = $(this).closest('tr');
  //   var row = table.row(tr);

  //   if ( row.child.isShown() ) {
  //     // This row is already open - close it
  //     row.child.hide();
  //     tr.removeClass('shown');
  //   }
  //   else {
  //     $.post('php/getInHouseValidation.php', {validationId: row.data().id, format: 'EXPANDABLE'}, function (data){
  //       var obj = JSON.parse(data); 
  //       if(obj.status === 'success'){
  //         row.child( format(obj.message) ).show();tr.addClass("shown");
  //       }
  //     });
  //   }
  // });

  $.validator.setDefaults({
    submitHandler: function () {
      if($('#cancelModal').hasClass('show')){
        $.post('php/deleteInHouseValidation.php', $('#cancelForm').serialize(), function(data){
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
      else if($('#extendModal').hasClass('show')){
        $('#spinnerLoading').show();

        $.post('php/insertInHouseValidation.php', $('#extendForm').serialize(), function(data){
          var obj = JSON.parse(data); 
          if(obj.status === 'success'){
            $('#extendModal').modal('hide');
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
    var branchFilter = $('#branchFilter').val() ? $('#branchFilter').val() : '';
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
      // "stateSave": true,
      'order': [[ 2, 'asc' ]],
      // 'columnDefs': [ { orderable: false, targets: [0] }],
      'ajax': {
        'type': 'POST',
        'url':'php/filterCancelledInHouseValidation.php',
        'data': {
          fromDate: fromDateValue,
          toDate: toDateValue,
          customer: customerNoFilter,
          validator: validatorFilter,
          autoFormNo: autoFormNoFilter,
          branch: branchFilter,
          status: 'Pending'
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
        {
          data: null, // The data property is null since this column is client-side only
          className: 'auto-increment',
          orderable: false,
          render: function (data, type, row, meta) {
            // meta.row provides the row index in the current page
            return meta.row + meta.settings._iDisplayStart + 1;
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
        {
          data: 'id',
          className: 'action-button',
          render: function (data, type, row) {
            let dropdownMenu = '<div class="dropdown" style="width=20%">' +
              '<button class="btn btn-secondary btn-sm" type="button" id="dropdownMenuButton' + data + '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
              '<i class="fa-solid fa-ellipsis"></i>' +
              '</button>' +
              '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton' + data + '">';

              if ('<?=$role ?>' == 'ADMIN' || '<?=$role ?>' == 'SUPER_ADMIN') {
                dropdownMenu += 
                  '<a class="dropdown-item" id="revertBtn' + data + '" onclick="revertToPending(' + data + ')"><i class="fa fa-arrow-circle-left"></i> Revert</a>'+
                  '<a class="dropdown-item" id="log' + data + '" onclick="log(' + data + ')"><i class="fa fa-list" aria-hidden="true"></i> Log</a>'+
                  '<a class="dropdown-item" id="delete'+data+'" onclick="deactivate(' + data + ')"><i class="fa fa-times" aria-hidden="true"></i> Delete</a>';
              }else{
                dropdownMenu += '<a class="dropdown-item" id="log' + data + '" onclick="log(' + data + ')"><i class="fa fa-list" aria-hidden="true"></i> Log</a>';
              }
              
            
            dropdownMenu += '</div></div>';

            return dropdownMenu;
          }
        },
        // { 
        //   data: 'id',
        //   render: function ( data, type, row ) {
        //     let buttons = '<div class="row">';

        //     if ('<?=$role ?>' == 'ADMIN' || '<?=$role ?>' == 'SUPER_ADMIN') { // Assuming 'isInvoiced' is a boolean field in your row data
        //       buttons +=  '<div class="col-4"><button title="Revert" type="button" id="pendingBtn'+data+'" onclick="revertToPending('+data+
        //       ')" class="btn btn-success btn-sm"><i class="fa fa-arrow-circle-left"></i></button></div>';

        //       // System Log
        //       buttons += '<div class="col-4"><button title="Log" type="button" id="log'+data+'" onclick="log('+data+')" class="btn btn-info btn-sm"><i class="fa fa-list" aria-hidden="true"></i></button></div>';

        //       buttons += '<div class="col-4"><button title="Delete" type="button" id="delete'+data+'" onclick="deactivate('+data+')" class="btn btn-danger btn-sm">X</button></div>';

        //       return buttons;
        //     } 
        //     else {
        //       // System Log
        //       buttons += '<div class="col-4"><button title="Log" type="button" id="log'+data+'" onclick="log('+data+')" class="btn btn-info btn-sm"><i class="fa fa-list" aria-hidden="true"></i></button></div>';
        //       return buttons; // Return an empty string or any other placeholder if the item is invoiced
        //     }
        //   }
        // },
        // { 
        //   className: 'dt-control',
        //   orderable: false,
        //   data: null,
        //   render: function ( data, type, row ) {
        //     return '<td class="table-elipse" data-toggle="collapse" data-target="#demo'+row.auto_form_no+'"><i class="fas fa-angle-down"></i></td>';
        //   }
        // }
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
          $('#branch').append('<option value="'+branchInfo.branchid+'">'+branchInfo.name+' - '+branchInfo.branch_address1+' '+branchInfo.branch_address2+' '+branchInfo.branch_address3+' '+branchInfo.branch_address4+'</option>')
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

  $('#multiDeactivate').on('click', function () {
      $('#spinnerLoading').show();
      var selectedIds = []; // An array to store the selected 'id' values

      $("#weightTable tbody input[type='checkbox']").each(function () {
        if (this.checked) {
          selectedIds.push($(this).val());
        }
      });

      if (selectedIds.length > 0) {
        if (confirm('DO YOU CONFIRMED TO DELETE THE FOLLOWING INHOUSE VALIDATIONS?')) {
          $.post('php/deleteInHouseValidation.php', {id: selectedIds, status: 'DELETE', type: 'MULTI'}, function(data){
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
          });
        }
        $('#spinnerLoading').hide();

      }else{
        alert("Please select at least one other inhouse to delete.");
        $('#spinnerLoading').hide();
      }
  });

  $('#cancelModal').find('#cancellationReason').on('change', function(){
    if($(this).val() == '0'){
      $('#otherRow').show();
      $('#otherReason').attr("required", true);
    }
    else{
      $('#otherRow').hide();
      $('#otherReason').attr("required", false);
    }
  });

  $(document).on('click', '#add-testing-cell', function() {
    var $addContents = $("#loadTestingDetails").clone();
    $("#loadTestingTable").append($addContents.html());

    $("#loadTestingTable").find('.details:last').attr("id", "detail" + loadTestingCount);
    $("#loadTestingTable").find('.details:last').attr("data-index", loadTestingCount);
    $("#loadTestingTable").find('#remove:last').attr("id", "remove" + loadTestingCount);

    $("#loadTestingTable").find('#no:last').attr('name', 'no['+loadTestingCount+']').attr('id', 'no' + loadTestingCount).val((loadTestingCount + 1)).hide();

    var noCount = loadTestingCount + 1;
    $("#loadTestingTable").find('#noText:last').attr('name', 'noText['+loadTestingCount+']').attr('id', 'noText' + loadTestingCount).text('Tester / Time: ' + noCount);
    $("#loadTestingTable").find('#standardValue:last').attr('name', 'standardValue['+loadTestingCount+']').attr("id", "standardValue" + loadTestingCount).css('background-color', 'yellow');
    $("#loadTestingTable").find('#calibrationReceived:last').attr('name', 'calibrationReceived['+loadTestingCount+']').attr("id", "calibrationReceived" + loadTestingCount);
    $("#loadTestingTable").find('#variance:last').attr('name', 'variance['+loadTestingCount+']').attr("id", "variance" + loadTestingCount);
    $("#loadTestingTable").find('#afterAdjustReading:last').attr('name', 'afterAdjustReading['+loadTestingCount+']').attr("id", "afterAdjustReading" + loadTestingCount);

    loadTestingCount++;
  });

  // Event delegation: use 'select' instead of 'input' for dropdowns
  $(document).on('change', 'input[id^="standardValue"]', function(){
    // Retrieve the selected option's attributes
    var standardValue = $(this).val();
    var calibrationReceived = $(this).closest('.details').find('input[id^="calibrationReceived"]').val();
    var varianceCalculated = standardValue - calibrationReceived;

    // Update the variance input value
    if(calibrationReceived > 0){
      $(this).closest('.details').find('input[id^="variance"]').val(varianceCalculated).css({'background-color': 'red', 'color': 'white'});
    }else{
      $(this).closest('.details').find('input[id^="variance"]').val('').css('background-color', 'lightgrey');
    }
  });

  // Event delegation: use 'select' instead of 'input' for dropdowns
  $(document).on('change', 'input[id^="calibrationReceived"]', function(){
    var standardValue = $(this).closest('.details').find('input[id^="standardValue"]').val();
    var calibrationReceived = $(this).val();
    var varianceCalculated = standardValue - calibrationReceived;

    // Update the variance input value
    if(calibrationReceived > 0){
      $(this).closest('.details').find('input[id^="variance"]').val(varianceCalculated).css({'background-color': 'red', 'color': 'white'});
    }else{
      $(this).closest('.details').find('input[id^="variance"]').val('').css('background-color', 'lightgrey');
    }
  });
});

function format (row) {
  var returnString = `
  <div class="row">
    <!-- Customer Section -->
    <div class="col-md-6">
      <p><span><strong style="font-size:120%; text-decoration: underline;">Inhouse To : Customer</strong></span><br>
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
  <div class="row mb-3">
    <h3 class="m-0 text-dark">Machines / Instruments Information</h3>
  </div>
  <div class="row">
    <!-- Machine Section -->
    <div class="col-6">
      <p><strong>Machines / Instruments:</strong> ${row.machines}</p>
      <p><strong>Jenis Alat:</strong> ${row.alat}</p>
      <p><strong>Inhouse Date:</strong> ${row.validation_date}</p>
      <p><strong>Manufacturing:</strong> ${row.manufacturing}</p>
      <p><strong>Brand:</strong> ${row.brand}</p>
      <p><strong>Capacity:</strong> ${row.capacity}</p>
      <p><strong>Inhouse Calibrator:</strong> ${row.calibrator}</p>
    </div>
    <div class="col-6">
      <p><strong>Unit Serial No:</strong> ${row.unit_serial_no}</p>
      <p><strong>Expired Date:</strong> ${row.expired_date}</p>
      <p><strong>Auto Certificate No / Sticker No:</strong> ${row.auto_cert_no}</p>
      <p><strong>Model:</strong> ${row.model}</p>
      <p><strong>Structure Size:</strong> ${row.size}</p>
      <p><strong>Created Date:</strong> ${row.validation_date}</p>
      <div class="row">
        <div class="col-1"><button title="Edit" type="button" id="edit${row.id}" onclick="edit(${row.id})" class="btn btn-warning btn-sm"><i class="fas fa-pen"></i></button></div>
        <div class="col-1"><button title="Print" type="button" id="print${row.id}" onclick="print(${row.id})" class="btn btn-info btn-sm"><i class="fas fa-print"></i></button></div>
        <div class="col-1"><button title="Complete" type="button" id="complete${row.id}" onclick="complete(${row.id})" class="btn btn-success btn-sm"><i class="fas fa-check"></i></button></div>
        <div class="col-1"><button title="Log" type="button" id="log${row.id}" onclick="log(${row.id})" class="btn btn-secondary btn-sm"><i class="fa fa-list" aria-hidden="true"></i></button></div>
        <div class="col-1"><button title="Cancelled" type="button" id="deactivate${row.id}" onclick="deactivate(${row.id})" class="btn btn-danger btn-sm"><i class="fa fa-times" aria-hidden="true"></i></button></div>
      </div>
    </div> 
  </div><hr>
  `;
  
  if (row.tests !== undefined && row.tests !== null && row.tests !== ''){
    if (row.tests[0].length > 0) {
      var weightType = row.capacityUnit;
      var variance = row.variance;
      
      if(row.standard_avg_temp){
        var standardAvgTemp = row.standard_avg_temp;
      }else{
        var standardAvgTemp = '';
      }

      if(row.relative_humidity){
        var relHumid = row.relative_humidity;
      }else{
        var relHumid = '';
      }

      returnString += '<h4 class="mb-3">Note - Standard Average Temperature: (' + standardAvgTemp + ') / Average Relative Humidity: (' + relHumid + ')</h4><table style="width: 100%;"><thead><tr><th width="15%">Number of Tests.</th><th width="20%">Setting Value Of Standard (' +  weightType + ')</th><th width="20%">As Received Under Calibration (' +  weightType + ')</th><th width="20%">Variance +/- '+variance+weightType+'</th><th width="20%">Reading After Adjustment. (' +  weightType + ')</th></tr></thead><tbody>';
      
      var tests = row.tests[0]; 
      for (var i = 0; i < tests.length; i++) {
        var item = tests[i];
        returnString += '<tr><td>Tester / Time: ' + item.no + '</td><td>' + item.standardValue + '</td><td>' + item.calibrationReceived + '</td><td>' + item.variance + '</td><td>' + item.afterAdjustReading + '</td></tr>';
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
  $('#extendModal').find('#type').val("DIRECT").trigger('change');
  $('#extendModal').find('#dealer').val('').trigger('change');
  $('#extendModal').find('#reseller_branch').val('').trigger('change');
  // $('#isResseller').hide();
  // $('#isResseller2').hide();
  // $('#isResseller3').hide();
  // $('#isResseller4').hide();
  // $('#isResseller5').hide();
  $('#extendModal').find('#customerType').val("EXISTING").attr('readonly', false).trigger('change');
  $('#extendModal').find('#company').val('');
  $('#extendModal').find('#validator').val('').trigger('change');
  $('#extendModal').find('#branch').val('').trigger('change');
  $('#extendModal').find('#autoFormNo').val('');
  $('#extendModal').find('#address1').val('');
  $('#extendModal').find('#address2').val('');
  $('#extendModal').find('#address3').val('');
  $('#extendModal').find('#address4').val('');
  $('#extendModal').find('#phone').val('');
  $('#extendModal').find('#email').val('');
  $('#extendModal').find('#pic').val('');
  $('#extendModal').find('#contact').val('');
  $('#extendModal').find('#machineType').val('').trigger('change');
  $('#extendModal').find('#serial').val('');
  $('#extendModal').find('#lastCalibrationDate').val('');
  $('#extendModal').find('#expiredDate').val('');
  $('#extendModal').find('#manufacturing').val('').trigger('change');
  $('#extendModal').find('#auto_cert_no').val('');
  $('#extendModal').find('#brand').val('').trigger('change');
  $('#extendModal').find('#model').val("").trigger('change');
  $('#extendModal').find('#capacity').val('').trigger('change');
  $('#extendModal').find('#size').val('').trigger('change');
  $('#extendModal').find('#calibrator').val('').trigger('change');
  $('#extendModal').find('#companyText').val('').trigger('change');
  $('#extendModal').find('#validationDate').val('');

  $('#loadTestingTable').html('');
  loadTestingCount = 0;
  for (var i = 0; i < 10; i++) {
      $('#add-testing-cell').trigger('click');
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
  $.post('php/getInHouseValidation.php', {validationId: id}, function(data){
    var obj = JSON.parse(data);
    if(obj.status === 'success'){
      if(obj.message.type == 'DIRECT'){
        $('#extendModal').find('#id').val(obj.message.id);
        $('#extendModal').find('#type').val(obj.message.type).trigger('change');
        $('#extendModal').find('#dealer').val('');
        $('#extendModal').find('#reseller_branch').val('');
        $('#extendModal').find('#customerType').val(obj.message.customer_type).attr('readonly', true).trigger('change');
        $('#extendModal').find('#company').val(obj.message.customer).trigger('change');
        $('#extendModal').find('#validator').val(obj.message.validate_by).trigger('change');
        $('#extendModal').find('#autoFormNo').val(obj.message.auto_form_no);
        setTimeout(function(){
          $('#extendModal').find('#branch').val(obj.message.branch).trigger('change');
        }, 500);
        $('#extendModal').find('#machineType').val(obj.message.machines).trigger('change');
        $('#extendModal').find('#serial').val(obj.message.unit_serial_no);
        $('#extendModal').find('#manufacturing').val(obj.message.manufacturing).trigger('change');
        $('#extendModal').find('#brand').val(obj.message.brand).trigger('change');
        $('#extendModal').find('#model').val(obj.message.model).trigger('change');
        $('#extendModal').find('#capacity').val(obj.message.capacity).trigger('change');
        $('#extendModal').find('#size').val(obj.message.size).trigger('change');
        $('#extendModal').find('#lastCalibrationDate').val(obj.message.last_calibration_date);
        $('#extendModal').find('#expiredDate').val(obj.message.expired_date);
        $('#extendModal').find('#auto_cert_no').val(obj.message.auto_cert_no);
        $('#extendModal').find('#validationDate').val(obj.message.validation_date);
        $('#extendModal').find('#calibrator').val(obj.message.calibrator).trigger('change');

        if(obj.message.tests != null && obj.message.tests.length > 0){
          $("#loadTestingTable").html('');
          loadTestingCount = 0; 

          for(var i = 0; i < obj.message.tests.length; i++){
            var tests = obj.message.tests[i];

            for(var j=0; j < tests.length; j++){
              var item = tests[j];
              var $addContents = $("#loadTestingDetails").clone();
              $("#loadTestingTable").append($addContents.html());

              $("#loadTestingTable").find('.details:last').attr("id", "detail" + loadTestingCount);
              $("#loadTestingTable").find('.details:last').attr("data-index", loadTestingCount);
              $("#loadTestingTable").find('#remove:last').attr("id", "remove" + loadTestingCount);

              $("#loadTestingTable").find('#no:last').attr('name', 'no['+loadTestingCount+']').attr("id", "no" + loadTestingCount).val(item.no).hide();
              $("#loadTestingTable").find('#noText:last').attr('name', 'noText['+loadTestingCount+']').attr('id', 'noText' + loadTestingCount).text('Tester / Time: ' + item.no);
              $("#loadTestingTable").find('#standardValue:last').attr('name', 'standardValue['+loadTestingCount+']').attr("id", "standardValue" + loadTestingCount).css('background-color', 'yellow').val(item.standardValue);
              $("#loadTestingTable").find('#calibrationReceived:last').attr('name', 'calibrationReceived['+loadTestingCount+']').attr("id", "calibrationReceived" + loadTestingCount).val(item.calibrationReceived);
              $("#loadTestingTable").find('#variance:last').attr('name', 'variance['+loadTestingCount+']').attr("id", "variance" + loadTestingCount).val(item.variance);
              $("#loadTestingTable").find('#afterAdjustReading:last').attr('name', 'afterAdjustReading['+loadTestingCount+']').attr("id", "afterAdjustReading" + loadTestingCount).val(item.afterAdjustReading);

              loadTestingCount++;
            }
          }
        }
      }
      else{
        $('#extendModal').find('#id').val(obj.message.id);
        $('#extendModal').find('#type').val(obj.message.type).trigger('change');
        $('#extendModal').find('#dealer').val(obj.message.dealer).trigger('change');
        setTimeout(function(){
          $('#extendModal').find('#reseller_branch').val(obj.message.dealer_branch).trigger('change');
        }, 500);
        $('#extendModal').find('#customerType').val(obj.message.customer_type).attr('readonly', true).trigger('change');
        $('#extendModal').find('#company').val(obj.message.customer).trigger('change');
        $('#extendModal').find('#validator').val(obj.message.validate_by).trigger('change');
        $('#extendModal').find('#autoFormNo').val(obj.message.auto_form_no);
        setTimeout(function(){
          $('#extendModal').find('#branch').val(obj.message.branch).trigger('change');
        }, 1000);
        $('#extendModal').find('#machineType').val(obj.message.machines).trigger('change');
        $('#extendModal').find('#serial').val(obj.message.unit_serial_no);
        $('#extendModal').find('#manufacturing').val(obj.message.manufacturing).trigger('change');
        $('#extendModal').find('#brand').val(obj.message.brand).trigger('change');
        $('#extendModal').find('#model').val(obj.message.model).trigger('change');
        $('#extendModal').find('#capacity').val(obj.message.capacity).trigger('change');
        $('#extendModal').find('#size').val(obj.message.size).trigger('change');
        $('#extendModal').find('#lastCalibrationDate').val(obj.message.last_calibration_date);
        $('#extendModal').find('#expiredDate').val(obj.message.expired_date);
        $('#extendModal').find('#autoCertNo').val(obj.message.auto_cert_no);
        $('#extendModal').find('#validationDate').val(obj.message.validation_date);
        $('#extendModal').find('#calibrator').val(obj.message.calibrator).trigger('change');

        if(obj.message.tests != null && obj.message.tests.length > 0){
          $("#loadTestingTable").html('');
          loadTestingCount = 0; 

          for(var i = 0; i < obj.message.tests.length; i++){
            var tests = obj.message.tests[i];

            for(var j=0; j < tests.length; j++){
              var item = tests[j]; console.log(item);
              var $addContents = $("#loadTestingDetails").clone();
              $("#loadTestingTable").append($addContents.html());

              $("#loadTestingTable").find('.details:last').attr("id", "detail" + loadTestingCount);
              $("#loadTestingTable").find('.details:last').attr("data-index", loadTestingCount);
              $("#loadTestingTable").find('#remove:last').attr("id", "remove" + loadTestingCount);

              $("#loadTestingTable").find('#no:last').attr('name', 'no['+loadTestingCount+']').attr("id", "no" + loadTestingCount).val(item.no).hide();
              $("#loadTestingTable").find('#noText:last').attr('name', 'noText['+loadTestingCount+']').attr('id', 'noText' + loadTestingCount).text('Tester / Time: ' + item.no);
              $("#loadTestingTable").find('#standardValue:last').attr('name', 'standardValue['+loadTestingCount+']').attr("id", "standardValue" + loadTestingCount).css('background-color', 'yellow').val(item.standardValue);
              $("#loadTestingTable").find('#calibrationReceived:last').attr('name', 'calibrationReceived['+loadTestingCount+']').attr("id", "calibrationReceived" + loadTestingCount).val(item.calibrationReceived);
              $("#loadTestingTable").find('#variance:last').attr('name', 'variance['+loadTestingCount+']').attr("id", "variance" + loadTestingCount).val(item.variance);
              $("#loadTestingTable").find('#afterAdjustReading:last').attr('name', 'afterAdjustReading['+loadTestingCount+']').attr("id", "afterAdjustReading" + loadTestingCount).val(item.afterAdjustReading);

              loadTestingCount++;
            }
          }
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

  // Hide the spinner when the modal is closed
  // $('#extendModal').on('hidden.bs.modal', function() {
  //   $('#spinnerLoading').hide(); 
  //   location.reload();
  // });
}

function complete(id) {
  if (confirm('Are you sure you want to complete this items?')) {
    $('#spinnerLoading').show();
    $.post('php/completeInHouseValidation.php', {userID: id}, function(data){
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

function deactivate(id){
  if (confirm('DO YOU CONFIRMED TO DELETE FOLLOWING DETAILS?')) {
    $('#spinnerLoading').show();
    $.post('php/deleteInHouseValidation.php', {id: id, status: 'DELETE'}, function(data){
      var obj = JSON.parse(data);
      
      if(obj.status === 'success'){
        toastr["success"](obj.message, "Success:");
        $('#weightTable').DataTable().ajax.reload(null, false);
        $('#spinnerLoading').hide();
      }
      else if(obj.status === 'failed'){
        toastr["error"](obj.message, "Failed:");
        $('#spinnerLoading').hide();
      }
      else{
        toastr["error"]("Something wrong when activate", "Failed:");
        $('#spinnerLoading').hide();
      }
    });
  }
}

function revertToPending(id) {
  
  if (confirm('DO YOU CONFIRMED TO REVERT TO PENDING?')) {
    $('#spinnerLoading').show();
    $.post('php/changeStatusInHouseValidation.php', {userID: id}, function(data){
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

function log(id) {
  $('#spinnerLoading').show();
  $.post('php/getLog.php', {id: id, type: 'Inhouse'}, function(data){
    var obj = JSON.parse(data);
    
    if(obj.status === 'success'){ 
      $('#logTable tbody').empty();

      if (obj.message.length > 0){
        obj.message.forEach(row => {
          let newRow = '<tr>';
          newRow += '<td>' + row.no + '</td>';
          newRow += '<td>' + row.user_id + '</td>';
          newRow += '<td>' + row.action + '</td>';
          newRow += '<td>' + row.date + '</td>';
          newRow += '<td>' + row.cancel_id + '</td>';
          newRow += '<td>' + row.remark + '</td>';
          newRow += '</tr>';

          $('#logTable tbody').append(newRow);
        })
      } else {
        $('#logTable tbody').append('<tr><td colspan="6" class="text-center">No data available</td></tr>');
      }
      $('#logModal').modal('show');
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
</script>
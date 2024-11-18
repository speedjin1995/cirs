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
  $_SESSION['page']='pending';
	
	if(($row = $result->fetch_assoc()) !== null){
    $role = $row['role_code'];
  }

  $dealer = $db->query("SELECT * FROM dealer WHERE deleted = '0'");
  $customers = $db->query("SELECT * FROM customers WHERE customer_status = 'CUSTOMERS' AND deleted = '0'");
  $customers2 = $db->query("SELECT * FROM customers WHERE customer_status = 'CUSTOMERS' AND deleted = '0'");
  $machinetypes = $db->query("SELECT * FROM machines WHERE deleted = '0'");
  $brands = $db->query("SELECT * FROM brand WHERE deleted = '0'");
  $models = $db->query("SELECT * FROM model WHERE deleted = '0'");
  $sizes = $db->query("SELECT * FROM size WHERE deleted = '0'");
  $singleCapacities = $db->query("SELECT * FROM capacity WHERE range_type = 'SINGLE' AND deleted = '0'");
  $multiCapacities = $db->query("SELECT * FROM capacity WHERE range_type = 'MULTI' AND deleted = '0'");
  $problems = $db->query("SELECT * FROM problem WHERE deleted = '0'");
  $users = $db->query("SELECT * FROM users WHERE deleted = '0'");
  $users2 = $db->query("SELECT * FROM users WHERE deleted = '0'");
  $validators = $db->query("SELECT * FROM validators WHERE deleted = '0' AND type = 'STAMPING'");
  $states = $db->query("SELECT * FROM state WHERE deleted = '0'");
  $alats = $db->query("SELECT * FROM alat WHERE deleted = '0'");
  $products = $db->query("SELECT * FROM products WHERE deleted = '0'");
  $cancelledReasons = $db->query("SELECT * FROM reasons WHERE deleted = '0'");
  $sizes = $db->query("SELECT * FROM size WHERE deleted = '0'");
  $country = $db->query("SELECT * FROM country WHERE deleted = '0'");
  $countryAts = $db->query("SELECT * FROM country WHERE deleted = '0'");
  $countryAtp = $db->query("SELECT * FROM country WHERE deleted = '0'");
  $countryAtn = $db->query("SELECT * FROM country WHERE deleted = '0'");
  $countryAte = $db->query("SELECT * FROM country WHERE deleted = '0'");
  $countrySll = $db->query("SELECT * FROM country WHERE deleted = '0'");
  $countryBtu = $db->query("SELECT * FROM country WHERE deleted = '0'");
  $country2 = $db->query("SELECT * FROM country WHERE deleted = '0'");
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
      <div class="col-sm-12">
        <h3 class="m-0 text-dark">Pending/Expired Yearly Stamping Metrology & DE Metrology Pending Status</h3>
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

              <div class="col-3">
                <div class="form-group">
                  <label>Customer No: </label>
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
                  <label>No. Daftar:</label>
                  <input type="text" class="form-control" id="daftarNoFilter" name="daftarNoFilter">
                </div>
              </div>

              <div class="col-3">
                <div class="form-group">
                  <label>No. Borang D:</label>
                  <input type="text" class="form-control" id="borangNoFilter" name="borangNoFilter">
                </div>
              </div>

              <div class="col-3">
                <div class="form-group">
                  <label>Machine Serial No:</label>
                  <input type="text" class="form-control" id="serialNoFilter" name="serialNoFilter">
                </div>
              </div>
              
              <div class="col-3">
                <div class="form-group">
                  <label>Quotation No:</label>
                  <input type="text" class="form-control" id="quoteNoFilter" name="quoteNoFilter">
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
              <div class="col-8"><h4>Company Weight And Measure Details</h4></div>
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
                  <th><input type="checkbox" id="selectAllCheckbox" class="selectAllCheckbox"></th>
                  <th>Created Date</th>
                  <th>Company Name</th>
                  <th>Brands</th>
                  <th>Description Instruments for Weighing And Measuring</th>
                  <th>Capacity</th>
                  <th>Validator By</th>
                  <th>Previous Stamp Date</th>
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
      <form role="form" id="extendForm">
        <div class="modal-header bg-gray-dark color-palette">
          <h4 class="modal-title">Stamping Forms</h4>
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
                    <label>Customer * </label>
                    <select class="form-control select2" style="width: 100%;" id="company" name="company" required></select>
                    <input class="form-control" type="text" placeholder="Company Name" id="companyText" name="companyText" style="display: none;">
                  </div>
                </div>
                <div class="col-12" id="custbranch">
                  <div class="form-group">
                    <label>Branch * </label>
                    <select class="form-control select2" style="width: 100%;" id="branch" name="branch" required></select>
                  </div>
                </div>
                <div class="col-4" id="addr1" style="display: none;">
                  <div class="form-group">
                    <label>Address Line 1 * </label>
                    <input class="form-control" type="text" placeholder="Address Line 1" id="address1" name="address1">
                  </div>
                </div>
                <div class="col-4" id="addr2" style="display: none;">
                  <div class="form-group">
                    <label>Address Line 2 </label>
                    <input class="form-control" type="text" placeholder="Address Line 2" id="address2" name="address2">
                  </div>
                </div>
                <div class="col-4" id="addr3" style="display: none;">
                  <div class="form-group">
                    <label>Address Line 3 </label>
                    <input class="form-control" type="text" placeholder="Address Line 3" id="address3" name="address3">
                  </div>
                </div>
                <div class="col-4" id="addr4" style="display: none;">
                  <div class="form-group">
                    <label>Address Line 4 </label>
                    <input class="form-control" type="text" placeholder="Address Line 4" id="address4" name="address4">
                  </div>
                </div>
                <div class="col-4" id="pic1" style="display: none;">
                  <div class="form-group">
                    <label>P.I.C</label>
                    <input class="form-control" type="text" placeholder="PIC" id="pic" name="pic">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="card card-primary">
            <div class="card-body">
              <div class="row">
                <h4>Machine Information</h4>
              </div>
              <div class="row">
                <div class="col-4">
                  <div class="form-group">
                    <label>Brand *</label>
                    <select class="form-control select2" style="width: 100%;" id="brand" name="brand" required>
                      <option selected="selected">-</option>
                      <?php while($rowB=mysqli_fetch_assoc($brands)){ ?>
                        <option value="<?=$rowB['id'] ?>"><?=$rowB['brand'] ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>Model *</label>
                    <select class="form-control select2" style="width: 100%;" id="model" name="model" required>
                      <option selected="selected">-</option>
                      <?php while($rowM=mysqli_fetch_assoc($models)){ ?>
                        <option value="<?=$rowM['id'] ?>"><?=$rowM['model'] ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>Serial No * </label>
                    <input class="form-control" type="text" placeholder="Serial No." id="serial" name="serial" required>
                  </div>
                </div>
                <div class="col-4" style="display:none;">
                  <div class="form-group">
                    <label>Product *</label>
                    <select class="form-control select2" style="width: 100%;" id="product" name="product">
                      <option selected="selected">-</option>
                      <?php while($rowProduct=mysqli_fetch_assoc($products)){ ?>
                        <option 
                          value="<?=$rowProduct['id'] ?>" 
                          data-price="<?=$rowProduct['price'] ?>" 
                          data-machine="<?=$rowProduct['machine_type'] ?>" 
                          data-alat="<?=$rowProduct['jenis_alat'] ?>" 
                          data-capacity="<?=$rowProduct['capacity'] ?>" 
                          data-validator="<?=$rowProduct['validator'] ?>">
                          <?=$rowProduct['name'] ?>
                        </option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>Machine Type *</label>
                    <select class="form-control select2" style="width: 100%;" id="machineType" name="machineType" required>
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
                    <select class="form-control select2" style="width: 100%;" id="jenisAlat" name="jenisAlat" required>
                      <option selected="selected">-</option>
                      <?php while($rowA=mysqli_fetch_assoc($alats)){ ?>
                        <option value="<?=$rowA['id'] ?>"><?=$rowA['alat'] ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>Trade / Non-Trade *</label>
                    <select class="form-control select2" style="width: 100%;" id="trade" name="trade" required>
                      <option selected="selected"></option>
                      <option value="TRADE">TRADE</option>
                      <option value="NON-TRADE">NON-TRADE</option>
                    </select>
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>Capacity * </label>
                    <div class="d-flex align-items-center">
                      <div class="form-check mr-3">
                        <input type="checkbox" class="form-check-input" id="toggleMultiRange">
                        <label class="form-check-label" for="toggleMultiRange">Multi Range</label>
                      </div>

                      <div id="capacitySingle" class="flex-grow-1">
                        <select class="form-control select2" style="width: 100%;" id="capacity_single" name="capacity_single">
                          <option selected="selected">-</option>
                          <?php while($rowCA=mysqli_fetch_assoc($singleCapacities)){ ?>
                            <option value="<?=$rowCA['id'] ?>"><?=$rowCA['name'] ?></option>
                          <?php } ?>
                        </select>
                      </div>
                      
                      <div id="capacityMulti" style="display:none" class="flex-grow-1">
                        <select class="form-control select2" style="width: 100%;" id="capacity_multi" name="capacity_multi">
                          <option selected="selected">-</option>
                          <?php while($capacity2=mysqli_fetch_assoc($multiCapacities)){ ?>
                            <option value="<?=$capacity2['id'] ?>"><?=$capacity2['name'] ?></option>
                          <?php } ?>
                        </select>
                      </div>
                    </div>
                    
                    <input class="form-control" type="text" id="capacity" name="capacity" style="display: none;">
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <div class="card card-primary">
            <div class="card-body">
              <div class="row">
                <h4>Stamping Information</h4>
              </div>
              <div class="row">
                <div class="col-4">
                  <div class="form-group">
                    <label>Stamping Type * </label>
                    <select class="form-control" style="width: 100%;" id="newRenew" name="newRenew" required>
                      <option value="NEW">NEW</option>
                      <option value="RENEWAL">RENEWAL</option>
                    </select>
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>Validator * </label>
                    <select class="form-control select2" style="width: 100%;" id="validator" name="validator" required>
                      <option selected="selected">-</option>
                      <?php while($rowVA=mysqli_fetch_assoc($validators)){ ?>
                        <option value="<?=$rowVA['id'] ?>"><?=$rowVA['validator'] ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>Cawangan * </label>
                    <select class="form-control select2" style="width: 100%;" id="cawangan" name="cawangan" required>
                      <option selected="selected"></option>
                      <?php while($state=mysqli_fetch_assoc($states)){ ?>
                        <option value="<?=$state['id'] ?>"><?=$state['state'] ?></option>
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
                <div class="col-4">
                  <div class="form-group">
                    <label>No. Borang D</label>
                    <input class="form-control" type="text" placeholder="No. Borang D" id="borangD" name="borangD">
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>No Siri Pelekat Keselamatan </label>
                    <input class="form-control" type="text" placeholder="No Siri Pelekat Keselamatan" id="siriKeselamatan" name="siriKeselamatan">
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
                <div class="col-4">
                  <div class="form-group">
                    <label>Next Due Date </label>
                    <div class='input-group date' id="datePicker2" data-target-input="nearest">
                      <input type='text' class="form-control datetimepicker-input" data-target="#datePicker2" id="dueDate" name="dueDate"/>
                      <div class="input-group-append" data-target="#datePicker2" data-toggle="datetimepicker">
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
                <!-- <div class="col-4">
                  <div class="form-group">
                    <label>No PIN Pelekat Keselamatan </label>
                    <input class="form-control" type="text" placeholder="No PIN Pelekat Keselamatan" id="pinKeselamatan" name="pinKeselamatan">
                  </div>
                </div> -->
              </div>
            </div>
          </div>

          <div id="addtionalSection"></div>

          <div class="card card-primary">
            <div class="card-body">
              <div class="row">
                <h4>Billing Information</h4>
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
                    <label>PO No.</label>
                    <input class="form-control" type="text" placeholder="PO No" id="poNo" name="poNo">
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>PO Date </label>
                    <div class='input-group date' id="datePicker4" data-target-input="nearest">
                      <input type='text' class="form-control datetimepicker-input" data-target="#datePicker4" id="poDate" name="poDate"/>
                      <div class="input-group-append" data-target="#datePicker4" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>Invoice / Cash Bill No.</label>
                    <input class="form-control" type="text" placeholder="Invoice No" id="invoice" name="invoice">
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="card card-primary">
            <div class="card-body">
              <div class="row">
                <h4>Stamping Fees</h4>
              </div>
              <div class="row">
                <div class="col-4">
                  <div class="form-group">
                    <label>Unit Price *</label>
                    <input type="number" class="form-control" id="unitPrice" name="unitPrice" required>
                  </div>
                </div>
                <div class="col-4" id="cerId">
                  <div class="form-group">
                    <label>Cert.Price</label>
                    <input type="text" class="form-control" id="certPrice" name="certPrice" readonly>
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>Total Amount</label>
                    <input type="text" class="form-control" id="totalAmount" name="totalAmount" readonly>
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>SST 8%</label>
                    <input type="text" class="form-control" id="sst" name="sst" readonly>
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>Sub Total Amount</label>
                    <input type="text" class="form-control" id="subAmount" name="subAmount" readonly>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="row">
            <div class="col-12">
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
            <label>Penentusan Baru</label>
            <input type="text" class="form-control" id="penentusanBaru" name="penentusanBaru">
          </div>
        </div>
        <div class="col-4">
          <div class="form-group">
            <label>Penetusan Semula</label>
            <input type="text" class="form-control" id="penentusanSemula" name="penentusanSemula">
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

<script type="text/html" id="atsDetails">
  <div class="card card-primary">
    <div class="card-body">
      <div class="row">
        <h4>Addtional Information (ATS)</h4>
      </div>
      <div class="row">
        <div class="form-group col-4">
          <label for="model">Platform Made In *</label>
          <select class="form-control select2" id="platformCountry" name="platformCountry" required>
            <option value="" selected disabled hidden>Please Select</option>
            <?php while($rowcountry=mysqli_fetch_assoc($countryAts)){ ?>
              <option value="<?=$rowcountry['id'] ?>"><?=$rowcountry['name'] ?></option>
            <?php } ?>
          </select>
        </div>
      </div>
    </div>
  </div>
</script>

<script type="text/html" id="atpDetails">
  <div class="card card-primary">
    <div class="card-body">
      <div class="row">
        <h4>Addtional Information (ATP)</h4>
      </div>
      <div class="row">
        <div class="form-group col-4">
          <label for="model">Platform Made In *</label>
          <select class="form-control select2" id="platformCountry" name="platformCountry" required>
            <option value="" selected disabled hidden>Please Select</option>
            <?php while($rowcountry=mysqli_fetch_assoc($countryAtp)){ ?>
              <option value="<?=$rowcountry['id'] ?>"><?=$rowcountry['name'] ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="form-group col-4">
          <label for="model">Jenis Penunjuk *</label>
          <select class="form-control select2" id="jenis_penunjuk" name="jenis_penunjuk" required>
            <option value="" selected disabled hidden>Please Select</option>
            <option value="DIGITAL">DIGITAL</option>
            <option value="DAIL">DAIL</option>
          </select>
        </div>
      </div>
    </div>
  </div>
</script>

<script type="text/html" id="atnDetails">
  <div class="card card-primary">
    <div class="card-body">
      <div class="row">
        <h4>Addtional Information (ATN)</h4>
      </div>
      <div class="row">
        <div class="form-group col-4">
          <label for="model">Platform Made In *</label>
          <select class="form-control select2" id="platformCountry" name="platformCountry" required>
            <option value="" selected disabled hidden>Please Select</option>
            <?php while($rowcountry=mysqli_fetch_assoc($countryAtn)){ ?>
              <option value="<?=$rowcountry['id'] ?>"><?=$rowcountry['name'] ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="form-group col-4">
          <label for="model">Jenis Alat Type *</label>
          <select class="form-control select2" id="alat_type" name="alat_type" required>
            <option value="" selected disabled hidden>Please Select</option>
            <option value="PEDESTAL">PEDESTAL</option>
            <option value="SUSPENDED">SUSPENDED</option>
          </select>
        </div>
        <div class="form-group col-4">
          <label for="model">Bentuk Dulang *</label>
          <select class="form-control select2" id="bentuk_dulang" name="bentuk_dulang" required>
            <option value="" selected disabled hidden>Please Select</option>
            <option value="MANGKUK">BERBENTUK MANGKUK</option>
            <option value="NON-MANGKUK">BUKAN BERBENTUK MANGKUK</option>
          </select>
        </div>
      </div>
    </div>
  </div>
</script>

<script type="text/html" id="ateDetails">
  <div class="card card-primary">
    <div class="card-body">
      <div class="row">
        <h4>Addtional Information (ATE)</h4>
      </div>
      <div class="row">
        <div class="form-group col-4">
          <label for="model">Platform Made In *</label>
          <select class="form-control select2" id="platformCountry" name="platformCountry" required>
            <option value="" selected disabled hidden>Please Select</option>
            <?php while($rowcountry=mysqli_fetch_assoc($countryAte)){ ?>
              <option value="<?=$rowcountry['id'] ?>"><?=$rowcountry['name'] ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="form-group col-4">
          <label for="model">Klass *</label>
          <select class="form-control select2" id="class" name="class" required>
            <option value="" disabled hidden>Please Select</option>
            <option value="I">I</option>
            <option value="II" selected>II</option>
          </select>
        </div>
      </div>
    </div>
  </div>
</script>

<script type="text/html" id="sllDetails">
  <div class="card card-primary">
    <div class="card-body">
      <div class="row">
        <h4>Addtional Information (SLL)</h4>
      </div>
      <div class="row">
        <div class="form-group col-4">
          <label for="model">Platform Made In *</label>
          <select class="form-control select2" id="platformCountry" name="platformCountry" required>
            <option value="" selected disabled hidden>Please Select</option>
            <?php while($rowcountry=mysqli_fetch_assoc($countrySll)){ ?>
              <option value="<?=$rowcountry['id'] ?>"><?=$rowcountry['name'] ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="form-group col-4">
          <label for="model">Jenis Alat Type *</label>
          <select class="form-control select2" id="alat_type" name="alat_type" required>
            <option value="" selected disabled hidden>Please Select</option>
            <option value="KERAS">KAYU KERAS</option>
            <option value="LOGAM">LOGAM</option>
          </select>
        </div>
      </div>
      <div class="card card-primary">
        <div class="card-header">
          BAHAGIAN II
        </div>
        <div class="card-body">
          <!-- <div class="row mb-3">
              <div class="form-group col-12">
                <label  class="col-9" for="question1">1. Adakah Sukat Linar ini diperbuat dari keluli, tembaga pancalogam, aluminium, ivory, bakelait berlapis, kaca gantian yang dikukuhkan, kayu keras atau apa-apa bahan lain yang diluluskan oleh Penjimpan Timbang dan Sukat.</label>
                <select class="form-control select2 col-2" id="question1" name="question1" required>
                    <option value="" selected disabled hidden>Please Select</option>
                    <option value="YA">YA</option>
                    <option value="TIDAK">TIDAK</option>
                </select>
              </div>
          </div>
          <div class="row mb-3">
              <div class="form-group col-12">
                  <label for="question2">2. Adakah Sukat Linar ini lurus dan tiada kecacatan.</label>
                  <select class="form-control select2" id="question2" name="question2" required>
                      <option value="" selected disabled hidden>Please Select</option>
                      <option value="YA">YA</option>
                      <option value="TIDAK">TIDAK</option>
                  </select>
              </div>
          </div>
          <div class="row mb-3">
              <div class="form-group col-12">
                  <label for="question3">3. Adakah Sukat Linar yang diperbuat daripada kayu, dibubuh kedua-dua hujungnya dengan logam dan hujungnya dipaku menembusi kayu itu.</label>
                  <select class="form-control select2" id="question3" name="question3" required>
                      <option value="" selected disabled hidden>Please Select</option>
                      <option value="YA">YA</option>
                      <option value="TIDAK">TIDAK</option>
                  </select>
              </div>
          </div>
          <div class="row mb-3">
              <div class="form-group col-12">
                  <label for="question4">4. Adakah Sukat Linar bersenggat dengan jelas dan tidak boleh dipadam, dan senggatan yang dinombor ditanda dengan garisan yang lebih panjang daripada senggatan yang tidak dinombor.</label>
                  <select class="form-control select2" id="question4" name="question4" required>
                      <option value="" selected disabled hidden>Please Select</option>
                      <option value="YA">YA</option>
                      <option value="TIDAK">TIDAK</option>
                  </select>
              </div>
          </div>
          <div class="row mb-3">
              <div class="form-group col-12">
                  <label for="question5">5.1 Adakah Sukat Linar disenggat dengan jelas dan tidak boleh dipadam dalam ukuran sentimeter di atas satu belah dan dalam sukatan meter di sebelah belakang dan senggatan yang dinombor ditanda dengan garis yang lebih panjang daripada senggatan yang tidak dinombor.</label>
                  <select class="form-control select2" id="question5" name="question5" required>
                      <option value="" selected disabled hidden>Please Select</option>
                      <option value="YA">YA</option>
                      <option value="TIDAK">TIDAK</option>
                  </select>
              </div>
          </div>
          <div class="row mb-3">
              <div class="form-group col-12">
                  <label for="question6">5.2 Adakah Sukat itu panjangnya 1 m (satu meter)</label>
                  <select class="form-control select2" id="question6" name="question6" required>
                      <option value="" selected disabled hidden>Please Select</option>
                      <option value="YA">YA</option>
                      <option value="TIDAK">TIDAK</option>
                  </select>
              </div>
          </div>
          <div class="row mb-3">
              <div class="form-group col-12">
                  <label for="question7">6. Adakah Sukat Linar mempunyai nilai jangkahan maksimum yang mudah dibihat, diukir dan tidak boleh dipadam ditanda di satu hujung Sukat Linar dengan cara salah satu daripada cara salah satu tanda-pertukaran-ringkas yang berikut masing-masing di bawah satu meter (cm, in, atau mm)</label>
                  <select class="form-control select2" id="question7" name="question7" required>
                      <option value="" selected disabled hidden>Please Select</option>
                      <option value="YA">YA</option>
                      <option value="TIDAK">TIDAK</option>
                  </select>
              </div>
          </div>
          <div class="row mb-3">
              <div class="form-group col-12">
                  <label for="question8">7. Adakah Sukat Linar ini ditanda dengan cap dekat permukaan Skel pada sebelah tiap-tiap tap yang bersenggat.</label>
                  <select class="form-control select2" id="question8" name="question8" required>
                      <option value="" selected disabled hidden>Please Select</option>
                      <option value="YA">YA</option>
                      <option value="TIDAK">TIDAK</option>
                  </select>
              </div>
          </div> -->
          <div class="row mb-3 ml-4">
              <div class="col-md-8">
                  <label>1. Adakah Sukat Linar ini diperbuat dari keluli, tembaga pancalogam, aluminium, ivory, bakelait berlapis, kaca gantian yang dikukuhkan, kayu keras atau apa-apa bahan lain yang diluluskan oleh Penjimpan Timbang dan Sukat.</label>
              </div>
              <div class="col-md-3 ml-4">
                <select class="form-control select2" id="question1" name="question1" required>
                    <option value="" selected disabled hidden>Please Select</option>
                    <option value="YA">YA</option>
                    <option value="TIDAK">TIDAK</option>
                </select>
              </div>
          </div>
          <div class="row mb-3 ml-4">
            <div class="col-md-8">
                <label>2. Adakah Sukat Linar ini lurus dan tiada kecacatan.</label>
            </div>
            <div class="col-md-3 ml-4">
              <select class="form-control select2" id="question2" name="question2" required>
                  <option value="" selected disabled hidden>Please Select</option>
                  <option value="YA">YA</option>
                  <option value="TIDAK">TIDAK</option>
              </select>
            </div>
          </div>
          <div class="row mb-3 ml-4">
              <div class="col-md-8">
                  <label>3. Adakah Sukat Linar yang diperbuat daripada kayu, dibubuh kedua-dua hujungnya dengan logam dan hujungnya dipaku menembusi kayu itu.</label>
              </div>
              <div class="col-md-3 ml-4">
                <select class="form-control select2" id="question3" name="question3" required>
                    <option value="" selected disabled hidden>Please Select</option>
                    <option value="YA">YA</option>
                    <option value="TIDAK">TIDAK</option>
                </select>
              </div>
          </div>
          <div class="row mb-3 ml-4">
              <div class="col-md-8">
                  <label>4. Adakah Sukat Linar bersenggat dengan jelas dan tidak boleh dipadam, dan senggatan yang dinombor ditanda dengan garisan yang lebih panjang daripada senggatan yang tidak dinombor.</label>
              </div>
              <div class="col-md-3 ml-4">
                <select class="form-control select2" id="question4" name="question4" required>
                  <option value="" selected disabled hidden>Please Select</option>
                  <option value="YA">YA</option>
                  <option value="TIDAK">TIDAK</option>
                </select>
              </div>
          </div>
          <div class="row mb-3 ml-4">
              <div class="col-md-8">
                  <label>5.1 Adakah Sukat Linar disenggat dengan jelas dan tidak boleh dipadam dalam ukuran sentimeter di atas satu belah dan dalam sukatan meter di sebelah belakang dan senggatan yang dinombor ditanda dengan garis yang lebih panjang daripada senggatan yang tidak dinombor.</label>
              </div>
              <div class="col-md-3 ml-4">
                <select class="form-control select2" id="question5_1" name="question5_1" required>
                  <option value="" selected disabled hidden>Please Select</option>
                  <option value="YA">YA</option>
                  <option value="TIDAK">TIDAK</option>
                </select>
              </div>
          </div>
          <div class="row mb-3 ml-4">
              <div class="col-md-8">
                  <label>5.2 Adakah Sukat itu panjangnya 1 m (satu meter)</label>
              </div>
              <div class="col-md-3 ml-4">
                <select class="form-control select2" id="question5_2" name="question5_2" required>
                  <option value="" selected disabled hidden>Please Select</option>
                  <option value="YA">YA</option>
                  <option value="TIDAK">TIDAK</option>
                </select>
              </div>
          </div>
          <div class="row mb-3 ml-4">
              <div class="col-md-8">
                  <label>6. Adakah Sukat Linar mempunyai nilai jangkahan maksimum yang mudah dibihat, diukir dan tidak boleh dipadam ditanda di satu hujung Sukat Linar dengan cara salah satu daripada cara salah satu tanda-pertukaran-ringkas yang berikut masing-masing di bawah satu meter (cm, in, atau mm)</label>
              </div>
              <div class="col-md-3 ml-4">
                <select class="form-control select2" id="question6" name="question6" required>
                  <option value="" selected disabled hidden>Please Select</option>
                  <option value="YA">YA</option>
                  <option value="TIDAK">TIDAK</option>
                </select>
              </div>
          </div>
          <div class="row mb-3 ml-4">
              <div class="col-md-8">
                  <label>7. Adakah Sukat Linar ini ditanda dengan cap dekat permukaan Skel pada sebelah tiap-tiap tap yang bersenggat.</label>
              </div>
              <div class="col-md-3 ml-4">
                <select class="form-control select2" id="question7" name="question7" required>
                  <option value="" selected disabled hidden>Please Select</option>
                  <option value="YA">YA</option>
                  <option value="TIDAK">TIDAK</option>
                </select>
              </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</script>

<script type="text/html" id="btuDetails">
  <div class="card card-primary">
    <div class="card-body">
      <div class="row">
        <h4>Addtional Information (BATU)</h4>
      </div>
      <div class="row">
        <div class="form-group col-4">
          <label for="model">Platform Made In *</label>
          <select class="form-control select2" id="platformCountry" name="platformCountry" required>
            <option value="" selected disabled hidden>Please Select</option>
            <?php while($rowcountry=mysqli_fetch_assoc($countryBtu)){ ?>
              <option value="<?=$rowcountry['id'] ?>"><?=$rowcountry['name'] ?></option>
            <?php } ?>
          </select>
        </div>
        <div class="form-group col-4">
          <label for="model">Batu Ujian *</label>
          <select class="form-control select2" id="batuUjian" name="batuUjian" required>
            <option value="" disabled hidden selected>Please Select</option>
            <option value="BESI_TUANGAN">BESI TUANGAN</option>
            <option value="TEMBAGA">TEMBAGA</option>
            <option value="NIKARAT">NIKARAT</option>
            <option value="OTHER">LAIN-LAIN</option>
          </select>
          <input type="text" class="form-control" id="batuUjianLain" name="batuUjianLain" style="display:none">
        </div>
        <div class="form-group col-4" id="batuUjianLainDisplay" style="display:none">
          <label for="model">Batu Ujian Lain *</label>
          <input type="text" class="form-control" id="batuUjianLain" name="batuUjianLain">
        </div>
      </div>
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

<script type="text/html" id="loadCellDetails">
  <tr class="details">
    <td>
      <input type="text" class="form-control" id="no" name="no" readonly>
    </td>
    <td>
      <select class="form-control" style="width: 100%;" id="loadCells" name="loadCells">
        <option selected="selected">-</option>
        <?php while($rowLC=mysqli_fetch_assoc($loadCells)){ ?>
          <option 
            value="<?=$rowLC['id'] ?>" 
            data-brand="<?=$rowLC['brand_name'] ?>"
            data-model="<?=$rowLC['model_name'] ?>">
            <?=$rowLC['load_cell'] ?>
          </option>
        <?php } ?>
      </select>
    </td>
    <td>
      <input type="text" class="form-control" id="loadCellBrand" name="loadCellBrand" readonly>
    </td>
    <td>
      <input type="text" class="form-control" id="loadCellModel" name="loadCellModel" readonly>
    </td>
    <td>
      <input type="text" class="form-control" id="loadCellCapacity" name="loadCellCapacity" required>
    </td>
    <td>
      <input type="text" class="form-control" id="loadCellSerial" name="loadCellSerial" required>
    </td>
    <td><button class="btn btn-danger btn-sm" id="remove"><i class="fa fa-times"></i></button></td>
  </tr>
</script>

<script>
var pricingCount = $("#pricingTable").find(".details").length;
var loadCellCount = $("#loadCellTable").find(".details").length;
var customer = 0;
var branch = 0;

$(function () {
  $('#customerNoHidden').hide();

  const today = new Date();
  const tomorrow = new Date(today);
  const yesterday = new Date(today);
  tomorrow.setDate(tomorrow.getDate() + 1);
  yesterday.setDate(tomorrow.getDate() - 7);

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
    defaultDate: ''
  });

  $('#toDatePicker').datetimepicker({
    icons: { time: 'far fa-calendar' },
    format: 'DD/MM/YYYY',
    defaultDate: ''
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
  var daftarNoFilter = $('#daftarNoFilter').val() ? $('#daftarNoFilter').val() : '';
  var borangNoFilter = $('#borangNoFilter').val() ? $('#borangNoFilter').val() : '';
  var serialNoFilter = $('#serialNoFilter').val() ? $('#serialNoFilter').val() : '';
  var quoteNoFilter = $('#quoteNoFilter').val() ? $('#quoteNoFilter').val() : '';

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
      'url':'php/filterPendingStamping.php',
      'data': {
        fromDate: fromDateValue,
        toDate: toDateValue,
        customer: customerNoFilter,
        daftar: daftarNoFilter,
        borang: borangNoFilter,
        serial: serialNoFilter,
        quotation: quoteNoFilter,
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
          if (row.status == 'Pending') { // Assuming 'isInvoiced' is a boolean field in your row data
            return '<input type="checkbox" class="select-checkbox" id="checkbox_' + data + '" value="'+data+'"/>';
          } 
          else {
            return ''; // Return an empty string or any other placeholder if the item is invoiced
          }
        }
      },
      { data: 'created_datetime' },
      { data: 'customers' },
      { data: 'brand' },
      { data: 'machine_type' },
      { data: 'capacity' },
      { data: 'validate_by' },
      { data: 'stamping_date' },
      { data: 'due_date' },
      { data: 'status' },
      { 
        data: 'id',
        render: function (data, type, row) {
          let buttons = '<div class="row">';

          // Edit button
          buttons += '<div class="col-3"><button title="Edit" type="button" id="edit'+data+'" onclick="edit('+data+
                    ')" class="btn btn-warning btn-sm"><i class="fas fa-pen"></i></button></div>';

          // Extra button if validate_by is 3
          if (row.validate_by == 3) {
            buttons += '<div class="col-3"><button title="Extra Details" type="button" id="extra'+data+'" onclick="extraAction('+data+
                      ')" class="btn btn-primary btn-sm"><i class="fas fa-star"></i></button></div>';
          }

          // Print button
          buttons += '<div class="col-3"><button title="Print" type="button" id="print'+data+'" onclick="print('+data+
                    ', \''+row.jenis_alat+'\', \''+row.validate_by+'\')" class="btn btn-info btn-sm"><i class="fas fa-print"></i></button></div>';

          // Complete button if conditions are met
          if (row.stamping_date != '' && row.due_date != '' && row.siri_keselamatan != '' && row.borang_d != '') {
            buttons += '<div class="col-3"><button title="Complete" type="button" id="complete'+data+'" onclick="complete('+data+
                      ')" class="btn btn-success btn-sm"><i class="fas fa-check"></i></button></div>';
          }

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
          return '<td class="table-elipse" data-toggle="collapse" data-target="#demo'+row.serialNo+'"><i class="fas fa-angle-down"></i></td>';
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
      $.post('php/getStamp.php', {userID: row.data().id, format: 'EXPANDABLE'}, function (data){
        var obj = JSON.parse(data); 
        if(obj.status === 'success'){
          row.child( format(obj.message) ).show();tr.addClass("shown");
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
      else if($('#cancelModal').hasClass('show')){
        $.post('php/deleteStamp.php', $('#cancelForm').serialize(), function(data){
          var obj = JSON.parse(data); 
          if(obj.status === 'success'){
            $('#cancelModal').modal('hide');
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
    }
  });

  $('#filterSearch').on('click', function(){
    //$('#spinnerLoading').show();

    var fromDateValue = $('#fromDate').val();
    var toDateValue = $('#toDate').val();
    var customerNoFilter = $('#customerNoFilter').val() ? $('#customerNoFilter').val() : '';
    var daftarNoFilter = $('#daftarNoFilter').val() ? $('#daftarNoFilter').val() : '';
    var borangNoFilter = $('#borangNoFilter').val() ? $('#borangNoFilter').val() : '';
    var serialNoFilter = $('#serialNoFilter').val() ? $('#serialNoFilter').val() : '';
    var quoteNoFilter = $('#quoteNoFilter').val() ? $('#quoteNoFilter').val() : '';

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
        'url':'php/filterPendingStamping.php',
        'data': {
          fromDate: fromDateValue,
          toDate: toDateValue,
          customer: customerNoFilter,
          daftar: daftarNoFilter,
          borang: borangNoFilter,
          serial: serialNoFilter,
          quotation: quoteNoFilter,
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
            if (row.status == 'Pending') { // Assuming 'isInvoiced' is a boolean field in your row data
              return '<input type="checkbox" class="select-checkbox" id="checkbox_' + data + '" value="'+data+'"/>';
            } 
            else {
              return ''; // Return an empty string or any other placeholder if the item is invoiced
            }
          }
        },
        { data: 'created_datetime' },
        { data: 'customers' },
        { data: 'brand' },
        { data: 'machine_type' },
        { data: 'capacity' },
        { data: 'validate_by' },
        { data: 'stamping_date' },
        { data: 'due_date' },
        { data: 'status' },
        { 
          data: 'id',
          render: function (data, type, row) {
            let buttons = '<div class="row">';

            // Edit button
            buttons += '<div class="col-3"><button title="Edit" type="button" id="edit'+data+'" onclick="edit('+data+
                      ')" class="btn btn-warning btn-sm"><i class="fas fa-pen"></i></button></div>';

            // Extra button if validate_by is 3
            if (row.validate_by == 3) {
              buttons += '<div class="col-3"><button title="Extra Details" type="button" id="extra'+data+'" onclick="extraAction('+data+
                        ')" class="btn btn-primary btn-sm"><i class="fas fa-star"></i></button></div>';
            }

            // Print button
            buttons += '<div class="col-3"><button title="Print" type="button" id="print'+data+'" onclick="print('+data+
                    ', \''+row.jenis_alat+'\', \''+row.validate_by+'\')" class="btn btn-info btn-sm"><i class="fas fa-print"></i></button></div>';

            // Complete button if conditions are met
            if (row.stamping_date != '' && row.due_date != '' && row.siri_keselamatan != '' && row.borang_d != '') {
              buttons += '<div class="col-3"><button title="Complete" type="button" id="complete'+data+'" onclick="complete('+data+
                        ')" class="btn btn-success btn-sm"><i class="fas fa-check"></i></button></div>';
            }

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
      $('#isResseller2').hide();
      $('#isResseller3').hide();
      $('#isResseller4').hide();
      $('#isResseller5').hide();
    }
    else{
      $('#isResseller').show();
      $('#isResseller2').show();
      $('#isResseller3').show();
      $('#isResseller4').show();
      $('#isResseller5').show();
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

          if(customer != 0){
            $('#extendModal').find('#company').val(customer).trigger('change');
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
      $('#extendModal').find('#pic1').show();

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
      $('#extendModal').find('#pic1').hide();

      $('#extendModal').find('#company').parents('.form-group').find('.select2-container').show();
      $('#extendModal').find('#companyText').hide();
      $('#extendModal').find('#companyText').val('');
    }
  });

  $('#extendModal').find('#branch').on('change', function(){
    //$('#spinnerLoading').show();
    var id = $(this).find(":selected").val();

    if (id){
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
    }
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

        if(branch != 0){
            $('#extendModal').find('#branch').val(branch).trigger('change');
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

  $('#extendModal').find('#brand').on('change', function(){
    var brandId = $(this).find(":selected").val();

    if(brandId){
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
    }
  });

  $('#extendModal').find('#machineType').on('change', function(){
    var brandId = $(this).find(":selected").val();

    if(brandId){
      $.post('php/getJAFromMT.php', {id: brandId}, function (data){
        var obj = JSON.parse(data);

        if(obj.status === 'success'){
          $('#jenisAlat').html('');
          // $('#jenisAlat').append('<option selected="selected">-</option>');

          for(var i=0; i<obj.message.length; i++){
            var modelInfo = obj.message[i];
            $('#jenisAlat').append('<option value="'+modelInfo.id+'">'+modelInfo.jenis_alat+'</option>')
          }

          $('#extendModal').trigger('jaIsLoaded');
        }
        else if(obj.status === 'failed'){
          toastr["error"](obj.message, "Failed:");
        }
        else{
          toastr["error"]("Something wrong when pull data", "Failed:");
        }
      });
    }
  });

  $('#extendModal').find('#product').on('change', function(){
    var price = parseFloat($(this).find(":selected").attr("data-price"));
    var machine = parseFloat($(this).find(":selected").attr("data-machine"));
    var alat = parseFloat($(this).find(":selected").attr("data-alat"));
    var capacity = parseFloat($(this).find(":selected").attr("data-capacity"));
    var validator = parseFloat($(this).find(":selected").attr("data-validator"));
    var includeCert = $('#includeCert').val();
    var certPrice = 28.5;
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
    var certPrice = 28.5;
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
    var certPrice = 28.5;
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

  $('#extendModal').find('#jenisAlat').on('change', function(){
      alat = $(this).val();

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

    if(($('#validator').val() == '10' || $('#validator').val() == '9') && alat == '1'){
      $('#addtionalSection').html($('#atkDetails').html());
      loadCellCount = 0;
      $("#loadCellTable").html('');
      $('#extendModal').trigger('atkLoaded');

      type = $('#extendModal').find('#type').val();
      if(type == 'RESELLER'){
        $('#extendModal').find('#penentusanSemula').attr('required', true);
      }
    }
    else if(($('#validator').val() == '10' || $('#validator').val() == '9') && alat == '4'){
      $('#addtionalSection').html($('#atsDetails').html());
      $('#extendModal').trigger('atkLoaded');
    }
    else if(($('#validator').val() == '10' || $('#validator').val() == '9') && alat == '2'){
      $('#addtionalSection').html($('#atpDetails').html());
      $('#extendModal').trigger('atkLoaded');
    }
    else if(($('#validator').val() == '10' || $('#validator').val() == '9') && alat == '5'){
      $('#addtionalSection').html($('#atnDetails').html());
      $('#extendModal').trigger('atkLoaded');
    }
    else if(($('#validator').val() == '10' || $('#validator').val() == '9') && alat == '6'){
      $('#addtionalSection').html($('#ateDetails').html());
      $('#extendModal').trigger('atkLoaded');
    }
    else if(($('#validator').val() == '10' || $('#validator').val() == '9') && alat == '14'){
      $('#addtionalSection').html($('#sllDetails').html());
      $('#extendModal').trigger('atkLoaded');
    }
    else if(($('#validator').val() == '10' || $('#validator').val() == '9') && alat == '7'){
      $('#addtionalSection').html($('#btuDetails').html());
      $('#extendModal').trigger('atkLoaded');
    }
    else{
      $('#addtionalSection').html('');
    }
  });

  $('#extendModal').find('#toggleMultiRange').on('change', function() {
    if ($('#extendModal').find('#toggleMultiRange').is(':checked')) {
      $('#extendModal').find('#capacityMulti').val('').show();
      $('#extendModal').find('#capacitySingle').val('').hide();
    }else{
      $('#extendModal').find('#capacityMulti').val('').hide();
      $('#extendModal').find('#capacitySingle').val('').show();
    }
  });

  $('#extendModal').find('#capacity_single').on('change', function(){
    capacityId = $(this).val();
    $('#extendModal').find('#capacity').val(capacityId);
  });

  $('#extendModal').find('#capacity_multi').on('change', function(){
    capacityId = $(this).val();
    $('#extendModal').find('#capacity').val(capacityId);
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

  $('#extendModal').find('#validator').on('change', function(){
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

    if(($(this).val() == '10' || $(this).val() == '9') && $('#jenisAlat').val() == '1'){
      $('#addtionalSection').html($('#atkDetails').html());
      loadCellCount = 0;
      $("#loadCellTable").html('');
      $('#extendModal').trigger('atkLoaded');

      type = $('#extendModal').find('#type').val();
      if(type == 'RESELLER'){
        $('#extendModal').find('#penentusanSemula').attr('required', true);
      }
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && $('#jenisAlat').val() == '4'){
      $('#addtionalSection').html($('#atsDetails').html());
      $('#extendModal').trigger('atkLoaded');
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && $('#jenisAlat').val() == '2'){
      $('#addtionalSection').html($('#atpDetails').html());
      $('#extendModal').trigger('atkLoaded');
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && $('#jenisAlat').val() == '5'){
      $('#addtionalSection').html($('#atnDetails').html());
      $('#extendModal').trigger('atkLoaded');
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && $('#jenisAlat').val() == '6'){
      $('#addtionalSection').html($('#ateDetails').html());
      $('#extendModal').trigger('atkLoaded');
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && $('#jenisAlat').val() == '14'){
      $('#addtionalSection').html($('#sllDetails').html());
      $('#extendModal').trigger('atkLoaded');
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && $('#jenisAlat').val() == '7'){
      $('#addtionalSection').html($('#btuDetails').html());
      $('#extendModal').trigger('atkLoaded');
      
    }
    else{
      $('#addtionalSection').html('');
    }
  });

  $('#cancelModal').find('#cancellationReason').on('change', function(){
    if($(this).val() == '0'){
      $('#otherReason').attr("required", true);
    }
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

  $(document).on('click', '.add-load-cell', function() {
    var loadCellValue = parseInt($('#noOfLoadCell').val());
    $("#loadCellTable").html('');
    loadCellCount = 0;
    // Trigger the cloning and appending logic loadCellNoValue times
    for (var i = 0; i < loadCellValue; i++) {
      var $addContents = $("#loadCellDetails").clone();
      $("#loadCellTable").append($addContents.html());

      $("#loadCellTable").find('.details:last').attr("id", "detail" + loadCellCount);
      $("#loadCellTable").find('.details:last').attr("data-index", loadCellCount);
      $("#loadCellTable").find('#remove:last').attr("id", "remove" + loadCellCount);

      $("#loadCellTable").find('#no:last').attr('name', 'no['+loadCellCount+']').attr("id", "no" + loadCellCount).val((loadCellCount + 1).toString());
      $("#loadCellTable").find('#loadCells:last').attr('name', 'loadCells['+loadCellCount+']').attr("id", "loadCells" + loadCellCount);
      $("#loadCellTable").find('#loadCellBrand:last').attr('name', 'loadCellBrand['+loadCellCount+']').attr("id", "loadCellBrand" + loadCellCount);
      $("#loadCellTable").find('#loadCellModel:last').attr('name', 'loadCellModel['+loadCellCount+']').attr("id", "loadCellModel" + loadCellCount);
      $("#loadCellTable").find('#loadCellCapacity:last').attr('name', 'loadCellCapacity['+loadCellCount+']').attr("id", "loadCellCapacity" + loadCellCount);
      $("#loadCellTable").find('#loadCellSerial').attr('name', 'loadCellSerial['+loadCellCount+']').attr("id", "loadCellSerial" + loadCellCount);

      loadCellCount++;
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
      <p><strong>${row.customers}</strong><br>
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
      <p><strong>${row.dealer}</strong><br>
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
    </div>

    <!-- Stamping Section -->
    <div class="col-6">
      <p><strong>No. Daftar:</strong> ${row.no_daftar}</p>
      <p><strong>Siri Keselamatan:</strong> ${row.siri_keselamatan}</p>
      <p><strong>Borang D:</strong> ${row.borang_d}</p>
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
  $('#capacityHigh').hide();

  $('#extendModal').find('#id').val("");
  $('#extendModal').find('#type').val("DIRECT");
  $('#isResseller').hide();
  $('#isResseller2').hide();
  $('#isResseller3').hide();
  $('#isResseller4').hide();
  $('#isResseller5').hide();
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
  $('#extendModal').find('#cawangan').val("").trigger('change');
  $('#extendModal').find('#stampDate').val('');
  $('#extendModal').find('#address2').val('');
  $('#extendModal').find('#capacity_single').val('').trigger('change');
  $('#extendModal').find('#capacity_multi').val('').trigger('change');
  $('#extendModal').find('#trade').val('').trigger('change');
  $('#extendModal').find('#branch').val('').trigger('change');
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

  //Additonal field reset
  // var value = $('#extendModal').find('#additionalSection').find('#batuUjian').val();
  // $('#extendModal').find('#additionalSection').find('#jenis_penunjuk').val('').trigger('change');

  $('#extendModal').find('#jenisAlat').change(function() {
    if($(this).val() == 1) {
        $('#extendModal').find('#capacityHigh').show();
    } else {
        $('#extendModal').find('#capacityHigh').hide();
    }
  });

  $('#extendModal').on('atkLoaded', function() {
    $('#extendModal').find('#batuUjian').on('change', function(){
      var batuUjian = $(this).val();
      if (batuUjian == 'OTHER'){
        $('#extendModal').find('#batuUjianLainDisplay').show();
      }else{
        $('#extendModal').find('#batuUjianLainDisplay').hide();
      }
    });
  });

  customer = 0;
  branch = 0;
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
  $.post('php/getStamp.php', {userID: id}, function(data){
    var obj = JSON.parse(data);
    
    if(obj.status === 'success'){
      if(obj.message.type == 'DIRECT'){
        $('#extendModal').find('#id').val(obj.message.id);
        $('#extendModal').find('#type').val(obj.message.type).trigger('change');
        $('#extendModal').find('#dealer').val('');
        $('#extendModal').find('#reseller_branch').val('');
        $('#extendModal').find('#customerType').val(obj.message.customer_type).attr('readonly', true).trigger('change');
        $('#extendModal').find('#brand').val(obj.message.brand).trigger('change');
        $('#extendModal').find('#validator').val(obj.message.validate_by).trigger('change');
        $('#extendModal').find('#cawangan').val(obj.message.cawangan).trigger('change');
        $('#extendModal').find('#trade').val(obj.message.trade).trigger('change');
        $('#extendModal').find('#newRenew').val(obj.message.stampType);
        $('#extendModal').find('#company').val(obj.message.customers).trigger('change');
        $('#extendModal').find('#companyText').val('');
        $('#extendModal').find('#product').val(obj.message.products);
        $('#extendModal').find('#machineType').val(obj.message.machine_type).trigger('change');
        customer = obj.message.customers;
        branch = obj.message.branch;
        $('#extendModal').on('jaIsLoaded', function() {
          $('#extendModal').find('#jenisAlat').val(obj.message.jenis_alat).trigger('change');
        });
        $('#extendModal').find('#capacity').val(obj.message.capacity).trigger('change');
        if(obj.message.capacity_range == 'MULTI'){
          $('#extendModal').find('#toggleMultiRange').prop('checked', true).trigger('change');
          $('#extendModal').find('#capacity_multi').val(obj.message.capacity).trigger('change');
        }else{
          $('#extendModal').find('#toggleMultiRange').prop('checked', false).trigger('change');
          $('#extendModal').find('#capacity_single').val(obj.message.capacity).trigger('change');
        }

        //$('#extendModal').find('#address1').val(obj.message.address1);
        
        setTimeout(function(){
          $('#extendModal').find('#branch').val(obj.message.branch).trigger('change');
        }, 500);

        $('#extendModal').on('modelsLoaded', function() {
          $('#extendModal').find('#model').val(obj.message.model).trigger('change');
        });
        $('#extendModal').find('#stampDate').val(formatDate3(obj.message.stamping_date));
        $('#extendModal').find('#address2').val(obj.message.address2);
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

        $('#extendModal').on('atkLoaded', function() {
          if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && obj.message.jenis_alat == '1'){
            $('#addtionalSection').html($('#atkDetails').html());
            $('#extendModal').find('#penentusanBaru').val(obj.message.penentusan_baru);
            $('#extendModal').find('#penentusanSemula').val(obj.message.penentusan_semula);
            $('#extendModal').find('#kelulusanMSPK').val(obj.message.kelulusan_mspk);
            $('#extendModal').find('#noMSPK').val(obj.message.no_kelulusan);
            $('#extendModal').find('#noSerialIndicator').val(obj.message.indicator_serial);
            $('#extendModal').find('#platformCountry').val(obj.message.platform_country);
            $('#extendModal').find('#platformType').val(obj.message.platform_type);
            $('#extendModal').find('#size').val(obj.message.size);
            $('#extendModal').find('#jenisPelantar').val(obj.message.jenis_pelantar);
            $('#extendModal').find('#others').val(obj.message.other_info);
            $('#extendModal').find('#loadCellCountry').val(obj.message.load_cell_country);
            $('#extendModal').find('#noOfLoadCell').val(obj.message.load_cell_no);

            if(obj.message.load_cells_info.length > 0){
              $("#loadCellTable").html('');
              loadCellCount = 0;

              for(var i = 0; i < obj.message.load_cells_info.length; i++){
                var item = obj.message.load_cells_info[i];
                var $addContents = $("#loadCellDetails").clone();
                $("#loadCellTable").append($addContents.html());

                $("#loadCellTable").find('.details:last').attr("id", "detail" + loadCellCount);
                $("#loadCellTable").find('.details:last').attr("data-index", loadCellCount);
                $("#loadCellTable").find('#remove:last').attr("id", "remove" + loadCellCount);

                $("#loadCellTable").find('#no:last').attr('name', 'no['+loadCellCount+']').attr("id", "no" + loadCellCount).val(item.no);
                $("#loadCellTable").find('#loadCells:last').attr('name', 'loadCells['+loadCellCount+']').attr("id", "loadCells" + loadCellCount).val(item.loadCells);
                $("#loadCellTable").find('#loadCellBrand:last').attr('name', 'loadCellBrand['+loadCellCount+']').attr("id", "loadCellBrand" + loadCellCount).val(item.loadCellBrand);
                $("#loadCellTable").find('#loadCellModel:last').attr('name', 'loadCellModel['+loadCellCount+']').attr("id", "loadCellModel" + loadCellCount).val(item.loadCellModel);
                $("#loadCellTable").find('#loadCellCapacity:last').attr('name', 'loadCellCapacity['+loadCellCount+']').attr("id", "loadCellCapacity" + loadCellCount).val(item.loadCellCapacity);
                $("#loadCellTable").find('#loadCellSerial').attr('name', 'loadCellSerial['+loadCellCount+']').attr("id", "loadCellSerial" + loadCellCount).val(item.loadCellSerial);

                loadCellCount++;
              }
            }
          }else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && obj.message.jenis_alat == '4'){
            $('#addtionalSection').html($('#atsDetails').html());
            $('#extendModal').find('#platformCountry').val(obj.message.platform_country).trigger('change');
          }else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && obj.message.jenis_alat == '2'){
            $('#addtionalSection').html($('#atpDetails').html());
            $('#extendModal').find('#platformCountry').val(obj.message.platform_country).trigger('change');
            $('#extendModal').find('#jenis_penunjuk').val(obj.message.jenis_penunjuk).trigger('change');
          }else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && obj.message.jenis_alat == '5'){
            $('#addtionalSection').html($('#atnDetails').html());
            $('#extendModal').find('#platformCountry').val(obj.message.platform_country).trigger('change');
            $('#extendModal').find('#alat_type').val(obj.message.alat_type).trigger('change');
            $('#extendModal').find('#bentuk_dulang').val(obj.message.bentuk_dulang).trigger('change');
          }else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && obj.message.jenis_alat == '6'){
            $('#addtionalSection').html($('#ateDetails').html());
            $('#extendModal').find('#platformCountry').val(obj.message.platform_country).trigger('change');
            $('#extendModal').find('#class').val(obj.message.class).trigger('change');
          }else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && obj.message.jenis_alat == '14'){
            $('#addtionalSection').html($('#sllDetails').html());
            $('#extendModal').find('#platformCountry').val(obj.message.platform_country).trigger('change');
            $('#extendModal').find('#alat_type').val(obj.message.alat_type).trigger('change');
            $('#extendModal').find('#question1').val(obj.message.questions[0].answer).trigger('change');
            $('#extendModal').find('#question2').val(obj.message.questions[1].answer).trigger('change');
            $('#extendModal').find('#question3').val(obj.message.questions[2].answer).trigger('change');
            $('#extendModal').find('#question4').val(obj.message.questions[3].answer).trigger('change');
            $('#extendModal').find('#question5_1').val(obj.message.questions[4].answer).trigger('change');
            $('#extendModal').find('#question5_2').val(obj.message.questions[5].answer).trigger('change');
            $('#extendModal').find('#question6').val(obj.message.questions[6].answer).trigger('change');
            $('#extendModal').find('#question7').val(obj.message.questions[7].answer).trigger('change');
          }else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && obj.message.jenis_alat == '7'){
            $('#addtionalSection').html($('#btuDetails').html());
            $('#extendModal').find('#platformCountry').val(obj.message.platform_country).trigger('change');
            $('#extendModal').find('#batuUjian').val(obj.message.batu_ujian).trigger('change');
            $('#extendModal').find('#batuUjianLain').val(obj.message.batu_ujian_lain);

            $('#extendModal').find('#batuUjian').on('change', function(){
              var batuUjian = $(this).val();
              if (batuUjian == 'OTHER'){
                $('#extendModal').find('#batuUjianLainDisplay').show();
              }else{
                $('#extendModal').find('#batuUjianLainDisplay').hide();
              }
            });

            // if (obj.message.batu_ujian == 'OTHER'){
            //   $('#extendModal').find('#batuUjianLainDisplay').show();
            //   // $('#extendModal').find('#batuUjianLain').val(obj.message.batu_ujian_lain);
            // }else{
            //   $('#extendModal').find('#batuUjianLainDisplay').hide();
            //   // $('#extendModal').find('#batuUjianLain').val('');
            // }
          }
        });
        
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
      else{
        $('#extendModal').find('#id').val(obj.message.id);
        $('#extendModal').find('#type').val(obj.message.type).trigger('change');
        $('#extendModal').find('#customerType').val(obj.message.customer_type).attr('readonly', true).trigger('change');
        $('#extendModal').find('#dealer').val(obj.message.dealer).trigger('change');
        $('#extendModal').find('#brand').val(obj.message.brand).trigger('change');
        $('#extendModal').find('#validator').val(obj.message.validate_by).trigger('change');
        $('#extendModal').find('#cawangan').val(obj.message.cawangan).trigger('change');
        $('#extendModal').find('#trade').val(obj.message.trade).trigger('change');
        $('#extendModal').find('#newRenew').val(obj.message.stampType);
        customer = obj.message.customers;
        branch = obj.message.branch;
        setTimeout(function(){
          $('#extendModal').find('#reseller_branch').val(obj.message.dealer_branch).trigger('change');
          $('#extendModal').find('#company').val(obj.message.customers).trigger('change');

          setTimeout(function(){
            $('#extendModal').find('#branch').val(obj.message.branch).trigger('change');
          }, 1500);
        }, 1000);

        $('#extendModal').find('#companyText').val('');
        $('#extendModal').find('#product').val(obj.message.products);
        $('#extendModal').find('#machineType').val(obj.message.machine_type).trigger('change');
        $('#extendModal').on('jaIsLoaded', function() {
          $('#extendModal').find('#jenisAlat').val(obj.message.jenis_alat).trigger('change');
        });
        
        $('#extendModal').find('#capacity').val(obj.message.capacity).trigger('change');
        if(obj.message.capacity_range == 'MULTI'){
          $('#extendModal').find('#toggleMultiRange').prop('checked', true).trigger('change');
          $('#extendModal').find('#capacity_multi').val(obj.message.capacity).trigger('change');
        }else{
          $('#extendModal').find('#toggleMultiRange').prop('checked', false).trigger('change');
          $('#extendModal').find('#capacity_single').val(obj.message.capacity).trigger('change');
        }
        //$('#extendModal').find('#address1').val(obj.message.address1);

        $('#extendModal').on('modelsLoaded', function() {
          $('#extendModal').find('#model').val(obj.message.model).trigger('change');
        });
        $('#extendModal').find('#stampDate').val(formatDate3(obj.message.stamping_date));
        $('#extendModal').find('#address2').val(obj.message.address2);
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

        $('#extendModal').on('atkLoaded', function() {
          if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && obj.message.jenis_alat == '1'){
            $('#addtionalSection').html($('#atkDetails').html());
            $('#extendModal').find('#penentusanBaru').val(obj.message.penentusan_baru);
            $('#extendModal').find('#penentusanSemula').val(obj.message.penentusan_semula);
            $('#extendModal').find('#kelulusanMSPK').val(obj.message.kelulusan_mspk);
            $('#extendModal').find('#noMSPK').val(obj.message.no_kelulusan);
            $('#extendModal').find('#noSerialIndicator').val(obj.message.indicator_serial);
            $('#extendModal').find('#platformCountry').val(obj.message.platform_country);
            $('#extendModal').find('#platformType').val(obj.message.platform_type);
            $('#extendModal').find('#size').val(obj.message.size);
            $('#extendModal').find('#jenisPelantar').val(obj.message.jenis_pelantar);
            $('#extendModal').find('#others').val(obj.message.other_info);
            $('#extendModal').find('#loadCellCountry').val(obj.message.load_cell_country);
            $('#extendModal').find('#noOfLoadCell').val(obj.message.load_cell_no);

            if(obj.message.load_cells_info.length > 0){
              $("#loadCellTable").html('');
              loadCellCount = 0;

              for(var i = 0; i < obj.message.load_cells_info.length; i++){
                var item = obj.message.load_cells_info[i];
                var $addContents = $("#loadCellDetails").clone();
                $("#loadCellTable").append($addContents.html());

                $("#loadCellTable").find('.details:last').attr("id", "detail" + loadCellCount);
                $("#loadCellTable").find('.details:last').attr("data-index", loadCellCount);
                $("#loadCellTable").find('#remove:last').attr("id", "remove" + loadCellCount);

                $("#loadCellTable").find('#no:last').attr('name', 'no['+loadCellCount+']').attr("id", "no" + loadCellCount).val(item.no);
                $("#loadCellTable").find('#loadCells:last').attr('name', 'loadCells['+loadCellCount+']').attr("id", "loadCells" + loadCellCount).val(item.loadCells);
                $("#loadCellTable").find('#loadCellBrand:last').attr('name', 'loadCellBrand['+loadCellCount+']').attr("id", "loadCellBrand" + loadCellCount).val(item.loadCellBrand);
                $("#loadCellTable").find('#loadCellModel:last').attr('name', 'loadCellModel['+loadCellCount+']').attr("id", "loadCellModel" + loadCellCount).val(item.loadCellModel);
                $("#loadCellTable").find('#loadCellCapacity:last').attr('name', 'loadCellCapacity['+loadCellCount+']').attr("id", "loadCellCapacity" + loadCellCount).val(item.loadCellCapacity);
                $("#loadCellTable").find('#loadCellSerial').attr('name', 'loadCellSerial['+loadCellCount+']').attr("id", "loadCellSerial" + loadCellCount).val(item.loadCellSerial);

                loadCellCount++;
              }
            }
          }else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && obj.message.jenis_alat == '4'){
            $('#addtionalSection').html($('#atsDetails').html());
            $('#extendModal').find('#platformCountry').val(obj.message.platform_country).trigger('change');
          }else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && obj.message.jenis_alat == '2'){
            $('#addtionalSection').html($('#atpDetails').html());
            $('#extendModal').find('#platformCountry').val(obj.message.platform_country).trigger('change');
            $('#extendModal').find('#jenis_penunjuk').val(obj.message.jenis_penunjuk).trigger('change');
          }else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && obj.message.jenis_alat == '5'){
            $('#addtionalSection').html($('#atnDetails').html());
            $('#extendModal').find('#platformCountry').val(obj.message.platform_country).trigger('change');
            $('#extendModal').find('#alat_type').val(obj.message.alat_type).trigger('change');
            $('#extendModal').find('#bentuk_dulang').val(obj.message.bentuk_dulang).trigger('change');
          }else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && obj.message.jenis_alat == '6'){
            $('#addtionalSection').html($('#ateDetails').html());
            $('#extendModal').find('#platformCountry').val(obj.message.platform_country).trigger('change');
            $('#extendModal').find('#class').val(obj.message.class).trigger('change');
          }else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && obj.message.jenis_alat == '14'){
            $('#addtionalSection').html($('#sllDetails').html());
            $('#extendModal').find('#platformCountry').val(obj.message.platform_country).trigger('change');
            $('#extendModal').find('#alat_type').val(obj.message.alat_type).trigger('change');
            $('#extendModal').find('#question1').val(obj.message.questions[0].answer).trigger('change');
            $('#extendModal').find('#question2').val(obj.message.questions[1].answer).trigger('change');
            $('#extendModal').find('#question3').val(obj.message.questions[2].answer).trigger('change');
            $('#extendModal').find('#question4').val(obj.message.questions[3].answer).trigger('change');
            $('#extendModal').find('#question5_1').val(obj.message.questions[4].answer).trigger('change');
            $('#extendModal').find('#question5_2').val(obj.message.questions[5].answer).trigger('change');
            $('#extendModal').find('#question6').val(obj.message.questions[6].answer).trigger('change');
            $('#extendModal').find('#question7').val(obj.message.questions[7].answer).trigger('change');
          }else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && obj.message.jenis_alat == '7'){
            $('#addtionalSection').html($('#btuDetails').html());
            $('#extendModal').find('#platformCountry').val(obj.message.platform_country).trigger('change');
            $('#extendModal').find('#batuUjian').val(obj.message.batu_ujian).trigger('change');
          }
        });

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
    $.post('php/getStamp.php', {userID: id}, function(data){
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


    // $.post('php/deleteStamp.php', {userID: id}, function(data){
    //   var obj = JSON.parse(data);

    //   if(obj.status === 'success'){
    //     toastr["success"](obj.message, "Success:");
    //     $('#weightTable').DataTable().ajax.reload();
    //   }
    //   else if(obj.status === 'failed'){
    //     toastr["error"](obj.message, "Failed:");
    //   }
    //   else{
    //     toastr["error"]("Something wrong when activate", "Failed:");
    //   }
    //   $('#spinnerLoading').hide();
    // });
  }
}

function print(id, type, validate) {
  //var optionText = $('#jenisAlat option[value="' + type + '"]').text();
  window.open('php/printBorang.php?userID='+id+'&file='+type+'&validator='+validate, '_blank');
  /*$.get('php/printBorang.php', {userID: id, file: 'ATK'}, function(data){
    var obj = JSON.parse(data);

    if(obj.status === 'success'){
      var printWindow = window.open('', '', 'height=' + screen.height + ',width=' + screen.width);
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
  });*/
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
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
  $_SESSION['page']='stamping';
	
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
  $technicians = $db->query("SELECT * FROM users WHERE role_code != 'SUPER_ADMIN' AND deleted = '0'");
  $validators = $db->query("SELECT * FROM validators WHERE deleted = '0' AND type = 'STAMPING'");
  $states = $db->query("SELECT * FROM state WHERE deleted = '0'");
  $alats = $db->query("SELECT * FROM alat WHERE deleted = '0'");
  $products = $db->query("SELECT * FROM products WHERE deleted = '0'");
  $cancelledReasons = $db->query("SELECT * FROM reasons WHERE deleted = '0'");
  $sizes = $db->query("SELECT * FROM size WHERE deleted = '0'");
  $country = $db->query("SELECT * FROM country WHERE deleted = '0'");
  $country3 = $db->query("SELECT * FROM country WHERE deleted = '0'");
  $countryAts = $db->query("SELECT * FROM country WHERE deleted = '0'");
  $countryAtp = $db->query("SELECT * FROM country WHERE deleted = '0'");
  $countryAtn = $db->query("SELECT * FROM country WHERE deleted = '0'");
  $countryAte = $db->query("SELECT * FROM country WHERE deleted = '0'");
  $countrySll = $db->query("SELECT * FROM country WHERE deleted = '0'");
  $countryBtu = $db->query("SELECT * FROM country WHERE deleted = '0'");
  $countryAutoPack = $db->query("SELECT * FROM country WHERE deleted = '0'");
  $countryAtsH = $db->query("SELECT * FROM country WHERE deleted = '0'");
  $countrySia = $db->query("SELECT * FROM country WHERE deleted = '0'");
  $country2 = $db->query("SELECT * FROM country WHERE deleted = '0'");
  $loadCells = $db->query("SELECT load_cells.*, brand.brand AS brand_name, model.model AS model_name FROM load_cells join brand on load_cells.brand = brand.id join model on load_cells.model = model.id where load_cells.deleted = 0 and brand.deleted = 0 and model.deleted = 0");
//   $loadCells = $db->query("SELECT load_cells.*, machines.machine_type AS machinetype, brand.brand AS brand_name, model.model AS model_name, alat.alat, country.nicename 
// FROM load_cells, machines, brand, model, alat, country WHERE load_cells.machine_type = machines.id AND load_cells.brand = brand.id AND load_cells.model = model.id 
// AND load_cells.jenis_alat = alat.id AND load_cells.made_in = country.id AND load_cells.deleted = '0'");
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
                  <label>From Stamp Date:</label>
                  <div class="input-group date" id="fromDatePicker" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input" data-target="#fromDatePicker" id="fromDate"/>
                    <div class="input-group-append" data-target="#fromDatePicker" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
                </div>

                <div class="form-group col-3">
                  <label>To Expired Date:</label>
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
                    <label>No. Daftar Lama:</label>
                    <input type="text" class="form-control" id="daftarLamaNoFilter" name="daftarLamaNoFilter">
                  </div>
                </div>

                <div class="col-3">
                  <div class="form-group">
                    <label>No. Daftar Baru:</label>
                    <input type="text" class="form-control" id="daftarBaruNoFilter" name="daftarBaruNoFilter">
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
    </div>

    <div class="row">
      <div class="col-lg-12">
        <div class="card card-primary">
          <div class="card-header">
            <div class="row">
              <div class="col-10"><p class="mb-0" style="font-size: 110%">Company Weight And Measure Details</p></div>
              <div class="col-2">
                <button type="button" class="btn btn-block btn-sm bg-gradient-danger" id="multiDeactivate" data-bs-toggle="tooltip" title="Cancel Stampings"><i class="fa-solid fa-ban"></i> Cancel Stampings</button>
              </div>
            </div>
          </div>

          <div class="card-body">
            <table id="weightTable" class="table table-bordered table-striped display">
              <thead>
                <tr>
                  <th><input type="checkbox" id="selectAllCheckbox" class="selectAllCheckbox"></th>
                  <th>No</th>
                  <th>Company Name</th>
                  <th>Brands</th>
                  <th>Description<br> Instruments</th>
                  <th>Serial No.</th>
                  <th>Validators</th>
                  <th width="10%">Capacity</th>
                  <th>No. Daftar Lama</th>
                  <th>No. Daftar Baru</th>
                  <th>Stamp Date</th>
                  <th>Next Due Date</th>
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
                    <input type="hidden" id="customerTypeEdit" name="customerTypeEdit">
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
                <h4>Machine / Indicator Information</h4>
              </div>
              <div class="row">
                <div class="col-4">
                  <div class="form-group">
                    <label>Machine / Indicator Brand *</label>
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
                    <label>Machine / Indicator Serial No * </label>
                    <input class="form-control" type="text" placeholder="Serial No." id="serial" name="serial" required>
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>Make In * </label>
                    <select class="form-control select2" style="width: 100%;" id="makeIn" name="makeIn" required>
                      <option selected="selected">-</option>
                      <?php while($rowcountry=mysqli_fetch_assoc($country3)){ ?>
                        <option value="<?=$rowcountry['id'] ?>"><?=$rowcountry['name'] ?></option>
                      <?php } ?>
                    </select>
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
                <div class="col-4">
                  <div class="form-group">
                    <label>Assigned To *</label>
                    <select class="form-control select2" style="width: 100%;" id="assignTo" name="assignTo" required>
                      <?php while($technician=mysqli_fetch_assoc($technicians)){ ?>
                        <option value="<?=$technician['id'] ?>"><?=$technician['name'] ?></option>
                      <?php } ?>
                    </select>
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
                <div class="col-4" id="daftarLamaView" style="display:none;">
                  <div class="form-group">
                    <label>No Daftar (Lama)</label>
                    <input class="form-control" type="text" placeholder="No Daftar Lama" id="noDaftarLama" name="noDaftarLama">
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>No Daftar (Baru)</label>
                    <input class="form-control" type="text" placeholder="No Daftar Baru" id="noDaftarBaru" name="noDaftarBaru">
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>No. Borang D</label>
                    <input class="form-control" type="text" placeholder="No. Borang D" id="borangD" name="borangD">
                  </div>
                </div>
                <div class="col-4" id="borangEView" style="display:none;">
                  <div class="form-group">
                    <label>No. Borang E</label>
                    <input class="form-control" type="text" placeholder="No. Borang E" id="borangE" name="borangE">
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>No Siri Pelekat Keselamatan </label>
                    <input class="form-control" type="text" placeholder="No Siri Pelekat Keselamatan" id="siriKeselamatan" name="siriKeselamatan">
                  </div>
                </div>
                <div class="col-4" id="lastYearStampDateView" style="display:none;">
                  <div class="form-group">
                    <label>Last Year Stamping Date</label>
                    <div class='input-group date' id="lastYearDatePicker" data-target-input="nearest">
                      <input type='text' class="form-control datetimepicker-input" data-target="#lastYearDatePicker" id="lastYearStampDate" name="lastYearStampDate"/>
                      <div class="input-group-append" data-target="#lastYearDatePicker" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
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
                    <label>Upload Quotation Attachment</label>
                    <div class="d-flex">
                      <div class="col-10">
                        <input type="file" class="form-control" id="uploadQuotationAttachment" name="uploadQuotationAttachment">
                      </div>
                      <div class="col-2 mt-1">
                        <a href="" id="viewQuotation" name="viewQuotation" target="_blank" class="btn btn-success btn-sm" role="button" style="display: none;"><i class="fa fa-file-pdf-o"></i></a>
                      </div>
                    </div>
                    <input type="text" id="quotationFilePath" name="quotationFilePath" style="display:none">           
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
                <div class="col-4">
                  <div class="form-group">
                    <label>Upload Invoice Attachment</label>
                    <div class="d-flex">
                      <div class="col-10">
                        <input type="file" class="form-control" id="uploadInvoiceAttachment" name="uploadInvoiceAttachment">
                      </div>
                      <div class="col-2 mt-1">
                        <a href="" id="viewInvoice" name="viewInvoice" target="_blank" class="btn btn-success btn-sm" role="button" style="display: none;"><i class="fa fa-file-pdf-o"></i></a>
                      </div>
                    </div>
                    <input type="text" id="InvoiceFilePath" name="InvoiceFilePath" style="display:none">           
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
          <input type="hidden" class="form-control" id="type" name="type">
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
                <label>Remarks / Other Reasons *</label>
                <textarea class="form-control" id ="otherReason" name="otherReason" required></textarea>
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
        <!-- <div class="form-group col-4">
          <label for="model">Platform Made In *</label>
          <select class="form-control select2" id="platformCountry" name="platformCountry" required>
            <option value="" selected disabled hidden>Please Select</option>
            <?php while($rowcountry=mysqli_fetch_assoc($country)){ ?>
              <option value="<?=$rowcountry['id'] ?>"><?=$rowcountry['name'] ?></option>
            <?php } ?>
          </select>
        </div> -->
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
        <!-- <div class="form-group col-4">
          <label for="model">Platform Made In *</label>
          <select class="form-control select2" id="platformCountry" name="platformCountry" required>
            <option value="" selected disabled hidden>Please Select</option>
            <?php while($rowcountry=mysqli_fetch_assoc($countryAtp)){ ?>
              <option value="<?=$rowcountry['id'] ?>"><?=$rowcountry['name'] ?></option>
            <?php } ?>
          </select>
        </div> -->
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
        <!-- <div class="form-group col-4">
          <label for="model">Platform Made In *</label>
          <select class="form-control select2" id="platformCountry" name="platformCountry" required>
            <option value="" selected disabled hidden>Please Select</option>
            <?php while($rowcountry=mysqli_fetch_assoc($countryAtn)){ ?>
              <option value="<?=$rowcountry['id'] ?>"><?=$rowcountry['name'] ?></option>
            <?php } ?>
          </select>
        </div> -->
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
        <!-- <div class="form-group col-4">
          <label for="model">Platform Made In *</label>
          <select class="form-control select2" id="platformCountry" name="platformCountry" required>
            <option value="" selected disabled hidden>Please Select</option>
            <?php while($rowcountry=mysqli_fetch_assoc($countryAte)){ ?>
              <option value="<?=$rowcountry['id'] ?>"><?=$rowcountry['name'] ?></option>
            <?php } ?>
          </select>
        </div> -->
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
        <!-- <div class="form-group col-4">
          <label for="model">Platform Made In *</label>
          <select class="form-control select2" id="platformCountry" name="platformCountry" required>
            <option value="" selected disabled hidden>Please Select</option>
            <?php while($rowcountry=mysqli_fetch_assoc($countrySll)){ ?>
              <option value="<?=$rowcountry['id'] ?>"><?=$rowcountry['name'] ?></option>
            <?php } ?>
          </select>
        </div> -->
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
        <!-- <div class="form-group col-4">
          <label for="model">Platform Made In *</label>
          <select class="form-control select2" id="platformCountry" name="platformCountry" required>
            <option value="" selected disabled hidden>Please Select</option>
            <?php while($rowcountry=mysqli_fetch_assoc($countryBtu)){ ?>
              <option value="<?=$rowcountry['id'] ?>"><?=$rowcountry['name'] ?></option>
            <?php } ?>
          </select>
        </div> -->
        <div class="form-group col-4">
          <label for="model">Batu Ujian *</label>
          <select class="form-control select2" id="batuUjian" name="batuUjian" required>
            <option value="" disabled hidden selected>Please Select</option>
            <option value="BESI_TUANGAN">BESI TUANGAN</option>
            <option value="TEMBAGA">TEMBAGA</option>
            <option value="NIKARAT">NIKARAT</option>
            <option value="OTHER">LAIN-LAIN</option>
          </select>
        </div>
        <div class="form-group col-4" id="batuUjianLainDisplay" style="display:none">
          <label for="model">Batu Ujian Lain *</label>
          <input type="text" class="form-control" id="batuUjianLain" name="batuUjianLain">
        </div>
        <div class="form-group col-4">
          <label for="model">Penandaan Pada Batu Ujian</label>
          <input type="text" class="form-control" id="penandaanBatuUjian" name="penandaanBatuUjian">
        </div>
      </div>
    </div>
  </div>
</script>

<script type="text/html" id="autoPackDetails">
  <div class="card card-primary">
    <div class="card-body">
      <div class="row">
        <h4>Addtional Information (ATP-Auto Machine)</h4>
      </div>
      <div class="row">
        <!-- <div class="form-group col-4">
          <label for="model">Platform Made In *</label>
          <select class="form-control select2" id="platformCountry" name="platformCountry" required>
            <option value="" selected disabled hidden>Please Select</option>
            <?php while($rowcountry=mysqli_fetch_assoc($countryAutoPack)){ ?>
              <option value="<?=$rowcountry['id'] ?>"><?=$rowcountry['name'] ?></option>
            <?php } ?>
          </select>
        </div> -->
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

<!-- <script type="text/html" id="autoPackDetails">
  <div class="card card-primary">
    <div class="card-body">
      <div class="row">
        <h4>Addtional Information (Auto Packer)</h4>
      </div>
      <div class="row">
        <div class="form-group col-12">
          <label for="model">Platform Made In *</label>
          <select class="form-control select2" id="platformCountry" name="platformCountry" required>
            <option value="" selected disabled hidden>Please Select</option>
            <?php while($rowcountry=mysqli_fetch_assoc($countryAutoPack)){ ?>
              <option value="<?=$rowcountry['id'] ?>"><?=$rowcountry['name'] ?></option>
            <?php } ?>
          </select>
        </div>
      </div>
      <div class="row">
        <label for="model" class="form-group">Nilai Berat Kaunterpois (kg) *</label>
      </div>
      <div class="row">
        <div class="form-group col-4">
          <label for="model">Nilai Berat Kaunterpois 1 (kg)</label>
          <input class="form-control" id ="nilai1" name="nilai1">
        </div>
        <div class="form-group col-4">
          <label for="model">Nilai Berat Kaunterpois 2 (kg)</label>
          <input class="form-control" id ="nilai2" name="nilai2">
        </div>
        <div class="form-group col-4">
          <label for="model">Nilai Berat Kaunterpois 3 (kg)</label>
          <input class="form-control" id ="nilai3" name="nilai3">
        </div>
        <div class="form-group col-4">
          <label for="model">Nilai Berat Kaunterpois 4 (kg)</label>
          <input class="form-control" id ="nilai4" name="nilai4">
        </div>
        <div class="form-group col-4">
          <label for="model">Nilai Berat Kaunterpois 5 (kg)</label>
          <input class="form-control" id ="nilai5" name="nilai5">
        </div>
        <div class="form-group col-4">
          <label for="model">Nilai Berat Kaunterpois 6 (kg)</label>
          <input class="form-control" id ="nilai6" name="nilai6">
        </div>
      </div>

    </div>
  </div>
</script> -->

<script type="text/html" id="atsHDetails">
  <div class="card card-primary">
    <div class="card-body">
      <div class="row">
        <h4>Addtional Information (ATS - H)</h4>
      </div>
      <div class="row">
        <div class="form-group col-4">
          <label for="model">Platform Made In *</label>
          <select class="form-control select2" id="platformCountry" name="platformCountry" required>
            <option value="" selected disabled hidden>Please Select</option>
            <?php while($rowcountry=mysqli_fetch_assoc($countryAtsH)){ ?>
              <option value="<?=$rowcountry['id'] ?>"><?=$rowcountry['name'] ?></option>
            <?php } ?>
          </select>
        </div>
      </div>
    </div>
  </div>
</script>

<script type="text/html" id="siaDetails">
  <div class="card card-primary">
    <div class="card-body">
      <div class="row">
        <h4>Addtional Information (SIA)</h4>
      </div>
      <div class="row">
        <!-- <div class="form-group col-4">
          <label for="model">Platform Made In *</label>
          <select class="form-control select2" id="platformCountry" name="platformCountry" required>
            <option value="" selected disabled hidden>Please Select</option>
            <?php while($rowcountry=mysqli_fetch_assoc($countrySia)){ ?>
              <option value="<?=$rowcountry['id'] ?>"><?=$rowcountry['name'] ?></option>
            <?php } ?>
          </select>
        </div> -->
        <div class="form-group col-4">
          <label for="model">Nilai Jangka Maksima *</label>
          <select class="form-control select2" id="nilaiJangka" name="nilaiJangka" required>
            <option value="" disabled hidden selected>Please Select</option>
            <option value="30">30 ML</option>
            <option value="OTHER">OTHER</option>
          </select>
        </div>
        <div class="form-group col-4" id="nilaiJangkaOtherDisplay" style="display:none">
          <label for="model">Nilai Jangka Maksima Other *</label>
          <input type="text" class="form-control" id="nilaiJangkaOther" name="nilaiJangkaOther">
        </div>
        <div class="form-group col-4">
          <label for="model">Diperbuat Daripada *</label>
          <select class="form-control select2" id="diperbuatDaripada" name="diperbuatDaripada" required>
            <option value="" disabled hidden selected>Please Select</option>
            <option value="KACA">KACA</option>
            <option value="OTHER">OTHER</option>
          </select>
        </div>
        <div class="form-group col-4" id="diperbuatDaripadaOtherDisplay" style="display:none">
          <label for="model">Diperbuat Daripada Other *</label>
          <input type="text" class="form-control" id="diperbuatDaripadaOther" name="diperbuatDaripadaOther">
        </div>
      </div>
    </div>
  </div>
</script>

<script type="text/html" id="bapDetails">
  <div class="card card-primary">
    <div class="card-body">
      <div class="row">
        <h4>Addtional Information (BAP)</h4>
      </div>
      <div class="row">
        <div class="form-group col-4">
          <label for="pamNo">Pam No</label>
          <input type="text" class="form-control" id="pamNo" name="pamNo">
        </div>
        <div class="form-group col-4">
          <label for="kelulusanBentuk">No Kelulusan Bentuk</label>
          <input type="text" class="form-control" id="kelulusanBentuk" name="kelulusanBentuk">
        </div>
        <div class="form-group col-4">
          <label for="jenama">Jenama / Nama Pembuat</label>
          <select class="form-control select2" id="jenama" name="jenama" required>
            <option value="" disabled hidden selected>Please Select</option>
            <option value="GRACO">GRACO</option>
            <option value="BADGER">BADGER</option>
            <option value="OTHER">OTHER</option>
          </select>
        </div>
        <div class="form-group col-4" id="jenamaOtherDisplay" style="display:none">
          <label for="jenamaOther">Jenama / Nama Pembuat Other *</label>
          <input type="text" class="form-control" id="jenamaOther" name="jenamaOther">
        </div>
        <div class="form-group col-4">
          <label for="alatType">Alat Type</label>
          <select class="form-control select2" id="alatType" name="alatType" required>
            <option value="" disabled hidden selected>Please Select</option>
            <option value="AUTOMATIK">AUTOMATIK</option>
            <option value="MANUAL">MANUAL</option>
            <option value="PNEUMATIK">PNEUMATIK</option>
          </select>
        </div>
        <div class="form-group col-4">
          <label for="kadarPengaliran">Kadar Pengaliran</label>
          <input type="text" class="form-control" id="kadarPengaliran" name="kadarPengaliran">
        </div>
        <div class="form-group col-4">
          <label for="bentukPenunjuk">Bentuk Penunjuk Harga/Kuantiti</label>
          <select class="form-control select2" id="bentukPenunjuk" name="bentukPenunjuk" required>
            <option value="" disabled hidden selected>Please Select</option>
            <option value="MEKANIKAL">MEKANIKAL</option>
            <option value="DIGITAL">DIGITAL</option>
          </select>
        </div>
      </div>
    </div>
  </div>
</script>

<script type="text/html" id="sicDetails">
  <div class="card card-primary">
    <div class="card-body">
      <div class="row">
        <h4>Addtional Information (SIC)</h4>
      </div>
      <div class="row">
        <div class="form-group col-4">
          <label for="nilaiMaksimum">Nilai Jangka Maksimum (Kapasiti) *</label>
          <input type="text" class="form-control" id="nilaiMaksimum" name="nilaiMaksimum">
        </div>
        <div class="form-group col-4">
          <label for="bahanPembuat">Bahan Pembuat *</label>
          <select class="form-control select2" id="bahanPembuat" name="bahanPembuat" required>
            <option value="" disabled hidden selected>Please Select</option>
            <option value="PANCALOGAM">PANCALOGAM</option>
            <option value="LOGAM BERENAMEL">LOGAM BERENAMEL</option>
            <option value="BESI BERSADUR">BESI BERSADUR</option>
            <option value="KACA">KACA</option>
            <option value="TEMBIKAR">TEMBIKAR</option>
            <option value="KELULI">KELULI</option>
            <option value="OTHER">OTHER</option>
          </select>
        </div>
        <div class="form-group col-4" id="bahanPembuatOtherDisplay" style="display:none">
          <label for="bahanPembuatOther">Bahan Pembuat Other *</label>
          <input type="text" class="form-control" id="bahanPembuatOther" name="bahanPembuatOther">
        </div>
      </div>
    </div>
  </div>
</script>

<script type="text/html" id="btuBoxDetails">
  <div class="card card-primary">
    <div class="card-body">
      <div class="row">
        <h4>Addtional Information (BTU - BOX)</h4>
      </div>
      <div class="row">
        <div class="col-4">
          <div class="form-group">
            <label>No. of BTU *</label>
            <input type="number" class="form-control" id="noOfBtu" name="noOfBtu" required>
          </div>
        </div>
        <div class="col-8 d-flex justify-content-end align-items-start">
          <button style="margin-left:auto;margin-right: 25px;" type="button" class="btn btn-primary add-btu">Add BTU</button>
        </div>
        <div class="col-12">
          <table style="width: 100%;">
            <thead>
              <tr>
                <th width="10%">No.</th>
                <th width="40%">Batu Ujian</th>
                <th>Penandaan Pada Batu Ujian</th>
                <th>Delete</th>
              </tr>
            </thead>
            <tbody id="btuTable"></tbody>
          </table>
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

<script type="text/html" id="btuCellDetails">
  <tr class="details">
    <td>
      <input type="text" class="form-control" id="no" name="no" readonly>
    </td>
    <td>
      <div class="d-flex">
        <select class="form-control select2 w-100" id="batuUjian" name="batuUjian" required>
            <option value="" disabled hidden selected>Please Select</option>
            <option value="BESI_TUANGAN">BESI TUANGAN</option>
            <option value="TEMBAGA">TEMBAGA</option>
            <option value="NIKARAT">NIKARAT</option>
            <option value="OTHER">LAIN-LAIN</option>
        </select>
        <input type="text" class="form-control w-50 ms-2" id="batuUjianLain" name="batuUjianLain" style="display:none" placeholder="Batu Ujian Lain">
      </div>
    </td>
    <td>
      <input type="text" class="form-control" id="penandaanBatuUjian" name="penandaanBatuUjian" required>
    </td>
    <td class="d-flex justify-content-center">
      <button class="btn btn-danger btn-sm text-center" id="remove"><i class="fa fa-times"></i></button>
    </td>
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

  $('#lastYearDatePicker').datetimepicker({
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
  var daftarLamaNoFilter = $('#daftarLamaNoFilter').val() ? $('#daftarLamaNoFilter').val() : '';
  var daftarBaruNoFilter = $('#daftarBaruNoFilter').val() ? $('#daftarBaruNoFilter').val() : '';
  var borangNoFilter = $('#borangNoFilter').val() ? $('#borangNoFilter').val() : '';
  var serialNoFilter = $('#serialNoFilter').val() ? $('#serialNoFilter').val() : '';
  var quoteNoFilter = $('#quoteNoFilter').val() ? $('#quoteNoFilter').val() : '';

  var table = $("#weightTable").DataTable({
    "responsive": true,
    "autoWidth": false,
    'processing': true,
    'serverSide': true,
    'serverMethod': 'post',
    'searching': true,
    // "stateSave": true,
    'order': [[ 2, 'asc' ]],
    'columnDefs': [ { orderable: false, targets: [0] }],
    'ajax': {
      'type': 'POST',
      'url':'php/filterStamping.php',
      'data': {
        fromDate: fromDateValue,
        toDate: toDateValue,
        customer: customerNoFilter,
        daftarLama: daftarLamaNoFilter,
        daftarBaru: daftarBaruNoFilter,
        borang: borangNoFilter,
        serial: serialNoFilter,
        quotation: quoteNoFilter,
        status: 'Complete'
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
      { data: 'customers' },
      { data: 'brand' },
      { data: 'machine_type' },
      { data: 'serial_no' },
      { data: 'validate_by' },
      { data: 'capacity' },
      { data: 'no_daftar_lama' },
      { data: 'no_daftar_baru' },
      { data: 'stamping_date' },
      { data: 'due_date' },
      { data: 'status' },
      {
        data: 'id',
        className: 'action-button',
        render: function (data, type, row) {
          let dropdownMenu = '<div class="dropdown" style="width=20%">' +
            '<button class="btn btn-secondary btn-sm" type="button" id="dropdownMenuButton' + data + '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: #074979;">' +
            '<i class="fa-solid fa-ellipsis"></i>' +
            '</button>' +
            '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton' + data + '">';
            if(row.stamping_type == 'NEW'){
              dropdownMenu += '<a class="dropdown-item" id="edit' + data + '" onclick="edit(' + data + ')"><i class="fas fa-pen"></i> Edit</a>';
            }else{
              dropdownMenu += '<a class="dropdown-item" id="edit' + data + '" onclick="edit(' + data + ')"><i class="fas fa-pen"></i> Edit</a>';
            }
            dropdownMenu += '<a class="dropdown-item" id="log' + data + '" onclick="log(' + data + ')"><i class="fa fa-list" aria-hidden="true"></i> Log</a>'+
            '<a class="dropdown-item" id="deactivate' + data + '" onclick="deactivate(' + data + ')"><i class="fa fa-times" aria-hidden="true"></i> Cancel</a>';
          
          dropdownMenu += '</div></div>';

          return dropdownMenu;
        }
      },
      // { 
      //   data: 'id',
      //   render: function (data, type, row) {
      //     let buttons = '<div class="row">';

      //     // Edit button
      //     buttons += '<div class="col-4"><button title="Edit" type="button" id="edit'+data+'" onclick="edit('+data+
      //               ')" class="btn btn-warning btn-sm"><i class="fas fa-pen"></i></button></div>';

      //     // Extra button if validate_by is 3
      //     if (row.validate_by == 3) {
      //       buttons += '<div class="col-3"><button title="Extra Details" type="button" id="extra'+data+'" onclick="extraAction('+data+
      //                 ')" class="btn btn-primary btn-sm"><i class="fas fa-star"></i></button></div>';
      //     }

      //     // Print button
      //     // buttons += '<div class="col-3"><button title="Print" type="button" id="print'+data+'" onclick="print('+data+
      //     //           ', \''+row.validator+'\')" class="btn btn-info btn-sm"><i class="fas fa-print"></i></button></div>';

      //     // Complete button if conditions are met
      //     // if (row.stamping_date != '' && row.due_date != '' && row.pin_keselamatan != '' && row.siri_keselamatan != '' && row.borang_d != '') {
      //     //   buttons += '<div class="col-3"><button title="Complete" type="button" id="complete'+data+'" onclick="complete('+data+
      //     //             ')" class="btn btn-success btn-sm"><i class="fas fa-check"></i></button></div>';
      //     // }

      //     // Log Button
      //     buttons += '<div class="col-4"><button title="Log" type="button" id="log'+data+'" onclick="log('+data+')" class="btn btn-info btn-sm"><i class="fa fa-list" aria-hidden="true"></i></button></div>';

      //     // Cancelled button
      //     buttons += '<div class="col-4"><button title="Cancelled" type="button" id="delete'+data+'" onclick="deactivate('+data+
      //               ')" class="btn btn-danger btn-sm"><i class="fa fa-times" aria-hidden="true"></i></button></button></div>';

      //     buttons += '</div>'; // Closing row div

      //     return buttons;
      //   }
      // },
      // { 
      //   className: 'dt-control',
      //   orderable: false,
      //   data: null,
      //   render: function ( data, type, row ) {
      //     return '<td class="table-elipse" data-toggle="collapse" data-target="#demo'+row.serialNo+'"><i class="fas fa-angle-down"></i></td>';
      //   }
      // }
    ],
    "lengthMenu": [ [10, 25, 50, 100, 300, 600, 1000], [10, 25, 50, 100, 300, 600, 1000] ], // More show options
    "pageLength": 10 // Default rows per page
  });
  
  // Add event listener for opening and closing details
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
    submitHandler: function (form) {
      if ($('#extendModal').hasClass('show')) {
          var formData = new FormData(form);

          // Disable hidden file inputs before submission
          $('.quotation-file-input, .invoice-file-input').each(function () {
              if (!$(this).is(':visible')) {
                  $(this).prop('disabled', true);
              }
          });

          $('#spinnerLoading').show(); // Show loading indicator

          $.ajax({
              url: 'php/insertStamping.php',
              type: 'POST',
              data: formData,
              processData: false,
              contentType: false,
              success: function (data) {
                  var obj = JSON.parse(data);
                  if (obj.status === 'success') {
                      $('#extendModal').modal('hide');
                      toastr["success"](obj.message, "Success:");
                      $('#weightTable').DataTable().ajax.reload(null, false);
                  } else {
                      toastr["error"](obj.message, "Failed:");
                  }
              },
              error: function (xhr, status, error) {
                  console.error("AJAX request failed:", status, error);
                  toastr["error"]("An error occurred while processing the request.", "Failed:");
              },
              complete: function () {
                  // Re-enable file inputs and hide spinner
                  $('.quotation-file-input, .invoice-file-input').prop('disabled', false);
                  $('#spinnerLoading').hide();
                  isModalOpen = false; // Reset flag
              }
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
                $('#weightTable').DataTable().ajax.reload(null, false);
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
            $('#weightTable').DataTable().ajax.reload(null, false);
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
      else if($('#cancelModal').hasClass('show')){
        $.post('php/deleteStamp.php', $('#cancelForm').serialize(), function(data){
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
    }
  });

  $('#filterSearch').on('click', function(){
    //$('#spinnerLoading').show();

    var fromDateValue = $('#fromDate').val();
    var toDateValue = $('#toDate').val();
    var customerNoFilter = $('#customerNoFilter').val() ? $('#customerNoFilter').val() : '';
    var daftarLamaNoFilter = $('#daftarLamaNoFilter').val() ? $('#daftarLamaNoFilter').val() : '';
    var daftarBaruNoFilter = $('#daftarBaruNoFilter').val() ? $('#daftarBaruNoFilter').val() : '';
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
      'searching': true,
      // "stateSave": true,
      'order': [[ 2, 'asc' ]],
      'columnDefs': [ { orderable: false, targets: [0] }],
      'ajax': {
        'type': 'POST',
        'url':'php/filterStamping.php',
        'data': {
          fromDate: fromDateValue,
          toDate: toDateValue,
          customer: customerNoFilter,
          daftarLama: daftarLamaNoFilter,
          daftarBaru: daftarBaruNoFilter,
          borang: borangNoFilter,
          serial: serialNoFilter,
          quotation: quoteNoFilter,
          status: 'Complete'
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
        { data: 'customers' },
        { data: 'brand' },
        { data: 'machine_type' },
        { data: 'serial_no' },
        { data: 'validate_by' },
        { data: 'capacity' },
        { data: 'no_daftar_lama' },
        { data: 'no_daftar_baru' },
        { data: 'stamping_date' },
        { data: 'due_date' },
        { data: 'status' },
        {
          data: 'id',
          className: 'action-button',
          render: function (data, type, row) {
            let dropdownMenu = '<div class="dropdown" style="width=20%">' +
              '<button class="btn btn-secondary btn-sm" type="button" id="dropdownMenuButton' + data + '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: #074979;">' +
              '<i class="fa-solid fa-ellipsis"></i>' +
              '</button>' +
              '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton' + data + '">';
              if(row.stamping_type == 'NEW'){
                dropdownMenu += '<a class="dropdown-item" id="edit' + data + '" onclick="edit(' + data + ')"><i class="fas fa-pen"></i> Edit</a>';
              }else{
                dropdownMenu += '<a class="dropdown-item" id="edit' + data + '" onclick="edit(' + data + ')"><i class="fas fa-pen"></i> Edit</a>';
              }
              dropdownMenu += '<a class="dropdown-item" id="log' + data + '" onclick="log(' + data + ')"><i class="fa fa-list" aria-hidden="true"></i> Log</a>'+
              '<a class="dropdown-item" id="deactivate' + data + '" onclick="deactivate(' + data + ')"><i class="fa fa-times" aria-hidden="true"></i> Cancel</a>';
            
            dropdownMenu += '</div></div>';

            return dropdownMenu;
          }
        },
        // { 
        //   data: 'id',
        //   render: function (data, type, row) {
        //     let buttons = '<div class="row">';

        //     // Edit button
        //     buttons += '<div class="col-4"><button title="Edit" type="button" id="edit'+data+'" onclick="edit('+data+
        //               ')" class="btn btn-warning btn-sm"><i class="fas fa-pen"></i></button></div>';

        //     // Extra button if validate_by is 3
        //     if (row.validate_by == 3) {
        //       buttons += '<div class="col-3"><button title="Extra Details" type="button" id="extra'+data+'" onclick="extraAction('+data+
        //                 ')" class="btn btn-primary btn-sm"><i class="fas fa-star"></i></button></div>';
        //     }

        //     // Print button
        //     // buttons += '<div class="col-3"><button title="Print" type="button" id="print'+data+'" onclick="print('+data+
        //     //         ', \''+row.validator+'\')" class="btn btn-info btn-sm"><i class="fas fa-print"></i></button></div>';

        //     // Complete button if conditions are met
        //     // if (row.stamping_date != '' && row.due_date != '' && row.pin_keselamatan != '' && row.siri_keselamatan != '' && row.borang_d != '') {
        //     //   buttons += '<div class="col-3"><button title="Complete" type="button" id="complete'+data+'" onclick="complete('+data+
        //     //             ')" class="btn btn-success btn-sm"><i class="fas fa-check"></i></button></div>';
        //     // }

        //     // Log Button
        //     buttons += '<div class="col-4"><button title="Log" type="button" id="log'+data+'" onclick="log('+data+')" class="btn btn-info btn-sm"><i class="fa fa-list" aria-hidden="true"></i></button></div>';

        //     // Cancelled button
        //     buttons += '<div class="col-4"><button title="Cancelled" type="button" id="delete'+data+'" onclick="deactivate('+data+
        //               ')" class="btn btn-danger btn-sm"><i class="fa fa-times" aria-hidden="true"></i></button></button></div>';

        //     buttons += '</div>'; // Closing row div

        //     return buttons;
        //   }
        // },
        // { 
        //   className: 'dt-control',
        //   orderable: false,
        //   data: null,
        //   render: function ( data, type, row ) {
        //     return '<td class="table-elipse" data-toggle="collapse" data-target="#demo'+row.serialNo+'"><i class="fas fa-angle-down"></i></td>';
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

  $('#multiDeactivate').on('click', function () {
      $('#spinnerLoading').show();
      var selectedIds = []; // An array to store the selected 'id' values

      $("#weightTable tbody input[type='checkbox']").each(function () {
        if (this.checked) {
          selectedIds.push($(this).val());
        }
      });

      if (selectedIds.length > 0) {
        if (confirm('Are you sure you want to cancel these items?')) {
          $('#cancelModal').find('#id').val(selectedIds);
          $('#cancelModal').find('#type').val('MULTI');
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
        }

        $('#spinnerLoading').hide();
      } 
      else {
        // Optionally, you can display a message or take another action if no IDs are selected
        alert("Please select at least one stamping to cancel.");
        $('#spinnerLoading').hide();
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

  $('#extendModal').find('#newRenew').on('change', function(){
    if($(this).val() == "NEW"){
      $('#daftarLamaView').hide();
      $('#borangEView').hide();
      $('#lastYearStampDateView').hide();
    }
    else{
      $('#daftarLamaView').show();
      $('#borangEView').show();
      $('#lastYearStampDateView').show();
    }
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
      date.setDate(date.getDate() - 1);
      
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
      $('#extendModal').find('#otherCodeView').show();
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
      $('#extendModal').find('#otherCodeView').hide();
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
        // $('#branch').append('<option selected="selected">-</option>');

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

  $('#extendModal').find('#machineType').on('change', function(){
    var brandId = $(this).find(":selected").val();

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
    $('#sst').val((totalAmt * 0.08).toFixed(2));
    $('#subAmount').val((totalAmt + (totalAmt * 0.08)).toFixed(2));
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
    $('#sst').val((totalAmt * 0.08).toFixed(2));
    $('#subAmount').val((totalAmt + (totalAmt * 0.08)).toFixed(2));
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
    $('#sst').val((totalAmt * 0.08).toFixed(2));
    $('#subAmount').val((totalAmt + (totalAmt * 0.08)).toFixed(2));
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
    jalat = $(this).val();
    alatId = $(this).val();
    $('#addtionalSection').html('');

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

      $.post('php/getSizeFromJA.php', {jenisAlat: alatId}, function(data){
        var obj = JSON.parse(data);

        if(obj.status === 'success'){
          $('#size').html('');

          for(var i=0; i<obj.message.length; i++){
            var size = obj.message[i];
            $('#size').append('<option value="'+size.id+'">'+size.size+'</option>')
          }
        }
        else if(obj.status === 'failed'){
          toastr["error"](obj.message, "Failed:");
        }
        else{
          toastr["error"]("Something wrong when pull data", "Failed:");
        }
      });
    }
    // else if(($('#validator').val() == '10' || $('#validator').val() == '9') && alat == '4'){
    //   $('#addtionalSection').html($('#atsDetails').html());
    //   $('#extendModal').trigger('atkLoaded');
    // }
    else if(($('#validator').val() == '10' || $('#validator').val() == '9') && alat == '2'){
      $('#addtionalSection').html($('#atpDetails').html());
      $('#extendModal').trigger('atkLoaded');
    }
    else if(($('#validator').val() == '10' || $('#validator').val() == '9') && alat == '5'){
      $('#addtionalSection').html($('#atnDetails').html());
      $('#extendModal').trigger('atkLoaded');
    }
    else if(($('#validator').val() == '10' || $('#validator').val() == '9') && alat == '18'){
      $('#addtionalSection').html($('#atnDetails').html());
      $('#extendModal').trigger('atkLoaded');
    }else if(($('#validator').val() == '10' || $('#validator').val() == '9') && alat == '6'){
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
    else if(($('#validator').val() == '10' || $('#validator').val() == '9') && alat == '10'){
      $('#addtionalSection').html($('#autoPackDetails').html());
      $('#extendModal').trigger('atkLoaded');
    }
    // else if(($('#validator').val() == '10' || $('#validator').val() == '9') && alat == '17'){
    //   $('#addtionalSection').html($('#atsHDetails').html());
    //   $('#extendModal').trigger('atkLoaded');
    // }
    else if(($('#validator').val() == '10' || $('#validator').val() == '9') && alat == '12'){
      $('#addtionalSection').html($('#siaDetails').html());
      $('#extendModal').trigger('atkLoaded');
    }
    else if(($('#validator').val() == '10' || $('#validator').val() == '9') && alat == '11'){
      $('#addtionalSection').html($('#bapDetails').html());
      $('#extendModal').trigger('atkLoaded');
    }
    else if(($('#validator').val() == '10' || $('#validator').val() == '9') && alat == '13'){
      $('#addtionalSection').html($('#sicDetails').html());
      $('#extendModal').trigger('atkLoaded');
    }
    else if(($('#validator').val() == '10' || $('#validator').val() == '9') && alat == '26'){
      $('#addtionalSection').html($('#btuBoxDetails').html());
      btuCount = 0;
      $("#btuTable").html('');
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

      $.post('php/getSizeFromJA.php', {jenisAlat: alatId}, function(data){
        var obj = JSON.parse(data);

        if(obj.status === 'success'){
          $('#size').html('');

          for(var i=0; i<obj.message.length; i++){
            var size = obj.message[i];
            $('#size').append('<option value="'+size.id+'">'+size.size+'</option>')
          }
        }
        else if(obj.status === 'failed'){
          toastr["error"](obj.message, "Failed:");
        }
        else{
          toastr["error"]("Something wrong when pull data", "Failed:");
        }
      });
    }
    // else if(($(this).val() == '10' || $(this).val() == '9') && $('#jenisAlat').val() == '4'){
    //   $('#addtionalSection').html($('#atsDetails').html());
    //   $('#extendModal').trigger('atkLoaded');
    // }
    else if(($(this).val() == '10' || $(this).val() == '9') && $('#jenisAlat').val() == '2'){
      $('#addtionalSection').html($('#atpDetails').html());
      $('#extendModal').trigger('atkLoaded');
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && $('#jenisAlat').val() == '5'){
      $('#addtionalSection').html($('#atnDetails').html());
      $('#extendModal').trigger('atkLoaded');
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && $('#jenisAlat').val() == '18'){
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
    else if(($(this).val() == '10' || $(this).val() == '9') && $('#jenisAlat').val() == '10'){
      $('#addtionalSection').html($('#autoPackDetails').html());
      $('#extendModal').trigger('atkLoaded');
    }
    // else if(($(this).val() == '10' || $(this).val() == '9') && $('#jenisAlat').val() == '17'){
    //   $('#addtionalSection').html($('#atsHDetails').html());
    //   $('#extendModal').trigger('atkLoaded');
    // }
    else if(($(this).val() == '10' || $(this).val() == '9') && $('#jenisAlat').val() == '12'){
      $('#addtionalSection').html($('#siaDetails').html());
      $('#extendModal').trigger('atkLoaded');
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && $('#jenisAlat').val() == '11'){
      $('#addtionalSection').html($('#bapDetails').html());
      $('#extendModal').trigger('atkLoaded');
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && $('#jenisAlat').val() == '13'){
      $('#addtionalSection').html($('#sicDetails').html());
      $('#extendModal').trigger('atkLoaded');
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && $('#jenisAlat').val() == '26'){
      $('#addtionalSection').html($('#btuBoxDetails').html());
      btuCount = 0;
      $("#btuTable").html('');
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

  $(document).on('click', '.add-btu', function() {
    var btuValue = parseInt($('#noOfBtu').val());
    $("#btuTable").html('');
    btuCount = 0;
    // Trigger the cloning and appending logic btuValue times
    for (var i = 0; i < btuValue; i++) {
      var $addContents = $("#btuCellDetails").clone();
      $("#btuTable").append($addContents.html());

      $("#btuTable").find('.details:last').attr("id", "detail" + btuCount);
      $("#btuTable").find('.details:last').attr("data-index", btuCount);
      $("#btuTable").find('#remove:last').attr("id", "remove" + btuCount);

      $("#btuTable").find('#no:last').attr('name', 'no['+btuCount+']').attr("id", "no" + btuCount).val((btuCount + 1).toString());
      $("#btuTable").find('#batuUjian:last').attr('name', 'batuUjian['+btuCount+']').attr("id", "batuUjian" + btuCount);
      $("#btuTable").find('#batuUjianLain:last').attr('name', 'batuUjianLain['+btuCount+']').attr("id", "batuUjianLain" + btuCount);
      $("#btuTable").find('#penandaanBatuUjian:last').attr('name', 'penandaanBatuUjian['+btuCount+']').attr("id", "penandaanBatuUjian" + btuCount);

      btuCount++;
    }
  });

  // Event delegation: use 'select' instead of 'input' for dropdowns
  $(document).on('change', 'select[id^="batuUjian"]', function(){
    // Retrieve the selected option's value
    var batuUjian = $(this).find(":selected").val();

    // Show batuUjianLain input
    if (batuUjian === 'OTHER') {
      $(this).removeClass('w-100').addClass('w-50');
      $(this).closest('.details').find('input[id^="batuUjianLain"]').addClass('w-50').show();
    } else {
      $(this).removeClass('w-50').addClass('w-100');
      $(this).closest('.details').find('input[id^="batuUjianLain"]').removeClass('w-50').hide();
    }
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
      <div class="row">
        <div class="col-1"><button title="Edit" type="button" id="edit${row.id}" onclick="edit(${row.id})" class="btn btn-warning btn-sm"><i class="fas fa-pen"></i></button></div>
        <div class="col-1"><button title="Log" type="button" id="log${row.id}" onclick="log(${row.id})" class="btn btn-secondary btn-sm"><i class="fa fa-list" aria-hidden="true"></i></button></div>
        <div class="col-1"><button title="Cancel" type="button" id="deactivate${row.id}" onclick="deactivate(${row.id})" class="btn btn-danger btn-sm"><i class="fa fa-times" aria-hidden="true"></i></button></div>
      </div>
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
  // if (row.jenis_alat == 'ATS' || row.jenis_alat == 'ATS (H)'){
  //   returnString += `</div><hr>
  //                       <div class="row">
  //                         <!-- ATS Section -->
  //                         <div class="col-6">
  //                           <p><strong>Platform Made In:</strong> ${row.platform_country}</p>
  //                         </div>
  //                       </div>
  //                       `;
  // }else 
  
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
                          <!-- BAP Section -->
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
              returnString += `<td>${row.btu_box_info[i].batuUjian}</td>`;
            }

            returnString += `<td>${row.btu_box_info[i].penandaanBatuUjian}</td></tr>`;
          }
      returnString += `</tbody>
        </table>
      `;
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
  $('#extendModal').find('#assignTo').val('').trigger('change');
  $('#extendModal').find('#address1').val('');
  $('#extendModal').find('#model').val("").trigger('change');
  $('#extendModal').find('#makeIn').val("").trigger('change');
  $('#extendModal').find('#stampDate').val('');
  $('#extendModal').find('#address2').val('');
  $('#extendModal').find('#capacity').val('').trigger('change');
  $('#extendModal').find('#noDaftarLama').val('');
  $('#extendModal').find('#noDaftarBaru').val('');
  $('#extendModal').find('#address3').val('');
  $('#extendModal').find('#serial').val('');
  $('#extendModal').find('#pinKeselamatan').val('');
  $('#extendModal').find('#attnTo').val('<?=$user ?>');
  $('#extendModal').find('#siriKeselamatan').val('');
  $('#extendModal').find('#pic').val("");
  $('#extendModal').find('#borangD').val("");
  $('#extendModal').find('#borangE').val("");
  $('#extendModal').find('#remark').val("");
  $('#extendModal').find('#dueDate').val('');
  $('#extendModal').find('#quotation').val("");
  $('#extendModal').find('#quotationDate').val('');
  $('#extendModal').find('#includeCert').val("NO").trigger('change');
  $('#extendModal').find('#poNo').val("");
  $('#extendModal').find('#poDate').val('');
  $('#extendModal').find('#cashBill').val("");
  $('#extendModal').find('#invoice').val('');
  $('#extendModal').find('#viewQuotation').hide();
  $('#extendModal').find('#uploadQuotationAttachment').val('');
  $('#extendModal').find('#quotationFilePath').val('');
  $('#extendModal').find('#newInvoice').show();
  $('#extendModal').find('#uploadInvoiceAttachment').val('');
  $('#extendModal').find('#viewInvoice').hide();

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

    $('#extendModal').find('#nilaiJangka').on('change', function(){
      var nilaiJangka = $(this).val();
      if (nilaiJangka == 'OTHER'){
        $('#extendModal').find('#nilaiJangkaOtherDisplay').show();
      }else{
        $('#extendModal').find('#nilaiJangkaOtherDisplay').hide();
      }
    });

    $('#extendModal').find('#diperbuatDaripada').on('change', function(){
      var diperbuatDaripada = $(this).val();
      if (diperbuatDaripada == 'OTHER'){
        $('#extendModal').find('#diperbuatDaripadaOtherDisplay').show();
      }else{
        $('#extendModal').find('#diperbuatDaripadaOtherDisplay').hide();
      }
    });

    $('#extendModal').find('#jenama').on('change', function(){
      var jenama = $(this).val();
      if (jenama == 'OTHER'){
        $('#extendModal').find('#jenamaOtherDisplay').show();
      }else{
        $('#extendModal').find('#jenamaOtherDisplay').hide();
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
        $('#extendModal').find('#customerType').val(obj.message.customer_type).attr('disabled', true).trigger('change');
        $('#extendModal').find('#customerTypeEdit').val(obj.message.customer_type);
        $('#extendModal').find('#brand').val(obj.message.brand).trigger('change');
        $('#extendModal').find('#makeIn').val(obj.message.make_in).trigger('change');
        $('#extendModal').find('#validator').val(obj.message.validate_by).trigger('change');
        $('#extendModal').find('#cawangan').val(obj.message.cawangan).trigger('change');
        $('#extendModal').find('#assignTo').val(obj.message.assignTo).trigger('change');
        $('#extendModal').find('#trade').val(obj.message.trade).trigger('change');
        $('#extendModal').find('#newRenew').val(obj.message.stampType).trigger('change');
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
        $('#extendModal').find('#lastYearStampDate').val(formatDate3(obj.message.last_year_stamping_date));
        $('#extendModal').find('#address2').val(obj.message.address2);
        $('#extendModal').find('#noDaftarLama').val(obj.message.no_daftar_lama);
        $('#extendModal').find('#noDaftarBaru').val(obj.message.no_daftar_baru);
        $('#extendModal').find('#address3').val(obj.message.address3);
        $('#extendModal').find('#serial').val(obj.message.serial_no);
        $('#extendModal').find('#pinKeselamatan').val(obj.message.pin_keselamatan);
        $('#extendModal').find('#attnTo').val(obj.message.pic);
        $('#extendModal').find('#siriKeselamatan').val(obj.message.siri_keselamatan);
        $('#extendModal').find('#pic').val(obj.message.pic);
        $('#extendModal').find('#borangD').val(obj.message.borang_d);
        $('#extendModal').find('#borangE').val(obj.message.borang_e);
        $('#extendModal').find('#remark').val(obj.message.remarks);
        $('#extendModal').find('#dueDate').val(formatDate3(obj.message.due_date));
        $('#extendModal').find('#quotation').val(obj.message.quotation_no);

        if(obj.message.quotation_attachment){
          $('#extendModal').find('#viewQuotation').attr('href', "view_file.php?file="+obj.message.quotation_attachment).show();
          $('#extendModal').find('#quotationFilePath').val(obj.message.quotation_filepath);
        }

        if(obj.message.invoice_attachment){
          $('#extendModal').find('#InvoiceFilePath').val(obj.message.invoice_filepath);
          $('#extendModal').find('#viewInvoice').attr('href', "view_file.php?file="+obj.message.invoice_attachment).show();
        }
        
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

        jalat = obj.message.jenis_alat;

        $('#extendModal').on('atkLoaded', function() {
          if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && jalat == '1'){
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
          }
          // else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && jalat == '4'){
          //   $('#addtionalSection').html($('#atsDetails').html());
          //   $('#extendModal').find('#platformCountry').val(obj.message.platform_country).trigger('change');
          // }
          else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && jalat == '2'){
            $('#addtionalSection').html($('#atpDetails').html());
            // $('#extendModal').find('#platformCountry').val(obj.message.platform_country).trigger('change');
            $('#extendModal').find('#jenis_penunjuk').val(obj.message.jenis_penunjuk).trigger('change');
          }else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && jalat == '23'){
            $('#addtionalSection').html($('#atpMotorDetails').html());
            // $('#extendModal').find('#platformCountry').val(obj.message.platform_country).trigger('change');
            $('#extendModal').find('#steelyard').val(obj.message.steelyard).trigger('change');
            $('#extendModal').find('#bilanganKaunterpois').val(obj.message.bilangan_kaunterpois).trigger('change');
            $('#extendModal').find('#nilai1').val(obj.message.nilais[0].nilai);
            $('#extendModal').find('#nilai2').val(obj.message.nilais[1].nilai);
            $('#extendModal').find('#nilai3').val(obj.message.nilais[2].nilai);
            $('#extendModal').find('#nilai4').val(obj.message.nilais[3].nilai);
            $('#extendModal').find('#nilai5').val(obj.message.nilais[4].nilai);
            $('#extendModal').find('#nilai6').val(obj.message.nilais[5].nilai);
          }else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && jalat == '5'){
            $('#addtionalSection').html($('#atnDetails').html());
            // $('#extendModal').find('#platformCountry').val(obj.message.platform_country).trigger('change');
            $('#extendModal').find('#alat_type').val(obj.message.alat_type).trigger('change');
            $('#extendModal').find('#bentuk_dulang').val(obj.message.bentuk_dulang).trigger('change');
          }else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && jalat == '18'){
            $('#addtionalSection').html($('#atnDetails').html());
            // $('#extendModal').find('#platformCountry').val(obj.message.platform_country).trigger('change');
            $('#extendModal').find('#alat_type').val(obj.message.alat_type).trigger('change');
            $('#extendModal').find('#bentuk_dulang').val(obj.message.bentuk_dulang).trigger('change');
          }else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && jalat == '6'){
            $('#addtionalSection').html($('#ateDetails').html());
            // $('#extendModal').find('#platformCountry').val(obj.message.platform_country).trigger('change');
            $('#extendModal').find('#class').val(obj.message.class).trigger('change');
          }else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && jalat == '14'){
            $('#addtionalSection').html($('#sllDetails').html());
            // $('#extendModal').find('#platformCountry').val(obj.message.platform_country).trigger('change');
            $('#extendModal').find('#alat_type').val(obj.message.alat_type).trigger('change');
            $('#extendModal').find('#question1').val(obj.message.questions[0].answer).trigger('change');
            $('#extendModal').find('#question2').val(obj.message.questions[1].answer).trigger('change');
            $('#extendModal').find('#question3').val(obj.message.questions[2].answer).trigger('change');
            $('#extendModal').find('#question4').val(obj.message.questions[3].answer).trigger('change');
            $('#extendModal').find('#question5_1').val(obj.message.questions[4].answer).trigger('change');
            $('#extendModal').find('#question5_2').val(obj.message.questions[5].answer).trigger('change');
            $('#extendModal').find('#question6').val(obj.message.questions[6].answer).trigger('change');
            $('#extendModal').find('#question7').val(obj.message.questions[7].answer).trigger('change');
          }else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && jalat == '7'){
            $('#addtionalSection').html($('#btuDetails').html());
            // $('#extendModal').find('#platformCountry').val(obj.message.platform_country).trigger('change');
            $('#extendModal').find('#penandaanBatuUjian').val(obj.message.penandaan_batu_ujian).trigger('change');

            $('#extendModal').find('#batuUjian').on('change', function(){
              var batuUjian = $(this).val();
              if (batuUjian == 'OTHER'){
                $('#extendModal').find('#batuUjianLainDisplay').show();
                $('#extendModal').find('#batuUjianLain').val(obj.message.batu_ujian_lain);
              }else{
                $('#extendModal').find('#batuUjianLainDisplay').hide();
              }
            });

            $('#extendModal').find('#batuUjian').val(obj.message.batu_ujian).trigger('change');
          }else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && jalat == '10'){
            // $('#addtionalSection').html($('#autoPackDetails').html());
            // $('#extendModal').find('#platformCountry').val(obj.message.platform_country);
            // $('#extendModal').find('#nilai1').val(obj.message.nilais[0].nilai);
            // $('#extendModal').find('#nilai2').val(obj.message.nilais[1].nilai);
            // $('#extendModal').find('#nilai3').val(obj.message.nilais[2].nilai);
            // $('#extendModal').find('#nilai4').val(obj.message.nilais[3].nilai);
            // $('#extendModal').find('#nilai5').val(obj.message.nilais[4].nilai);
            // $('#extendModal').find('#nilai6').val(obj.message.nilais[5].nilai);
            $('#addtionalSection').html($('#autoPackDetails').html());
            // $('#extendModal').find('#platformCountry').val(obj.message.platform_country).trigger('change');
            $('#extendModal').find('#jenis_penunjuk').val(obj.message.jenis_penunjuk).trigger('change');
          }
          // else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && jalat == '17'){
          //   $('#addtionalSection').html($('#atsHDetails').html());
          //   $('#extendModal').find('#platformCountry').val(obj.message.platform_country).trigger('change');
          // }
          else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && jalat == '12'){
            $('#addtionalSection').html($('#siaDetails').html());
            // $('#extendModal').find('#platformCountry').val(obj.message.platform_country).trigger('change');

            $('#extendModal').find('#nilaiJangka').on('change', function(){
              var nilaiJangka = $(this).val();
              if (nilaiJangka == 'OTHER'){
                $('#extendModal').find('#nilaiJangkaOtherDisplay').show();
                $('#extendModal').find('#nilaiJangkaOther').val(obj.message.nilai_jangka_other);
              }else{
                $('#extendModal').find('#nilaiJangkaOtherDisplay').hide();
              }
            });

            $('#extendModal').find('#diperbuatDaripada').on('change', function(){
              var diperbuatDaripada = $(this).val();
              if (diperbuatDaripada == 'OTHER'){
                $('#extendModal').find('#diperbuatDaripadaOtherDisplay').show();
                $('#extendModal').find('#diperbuatDaripadaOther').val(obj.message.diperbuat_daripada_other);
              }else{
                $('#extendModal').find('#diperbuatDaripadaOtherDisplay').hide();
              }
            });

            $('#extendModal').find('#nilaiJangka').val(obj.message.nilai_jangka).trigger('change');
            $('#extendModal').find('#diperbuatDaripada').val(obj.message.diperbuat_daripada).trigger('change');
          }
          else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && jalat == '11'){ 
            $('#addtionalSection').html($('#bapDetails').html());
            $('#extendModal').find('#pamNo').val(obj.message.pam_no).trigger('change');            $('#extendModal').find('#kelulusanBentuk').val(obj.message.kelulusan_bentuk).trigger('change');
            $('#extendModal').find('#alatType').val(obj.message.alat_type).trigger('change');
            $('#extendModal').find('#kadarPengaliran').val(obj.message.kadar_pengaliran).trigger('change');
            $('#extendModal').find('#bentukPenunjuk').val(obj.message.bentuk_penunjuk).trigger('change');

            $('#extendModal').find('#jenama').on('change', function(){
              var jenama = $(this).val();
              if (jenama == 'OTHER'){
                $('#extendModal').find('#jenamaOtherDisplay').show();
                $('#extendModal').find('#jenamaOther').val(obj.message.jenama_other);
              }else{
                $('#extendModal').find('#jenamaOtherDisplay').hide();
              }
            });

            $('#extendModal').find('#jenama').val(obj.message.jenama).trigger('change');
          }
          else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && jalat == '13'){ 
            $('#addtionalSection').html($('#sicDetails').html());
            $('#extendModal').find('#nilaiMaksimum').val(obj.message.nilai_jangkaan_maksimum).trigger('change');

            $('#extendModal').find('#bahanPembuat').on('change', function(){
              var bahanPembuat = $(this).val();
              if (bahanPembuat == 'OTHER'){
                $('#extendModal').find('#bahanPembuatOtherDisplay').show();
                $('#extendModal').find('#bahanPembuatOther').val(obj.message.bahan_pembuat_other);
              }else{
                $('#extendModal').find('#bahanPembuatOtherDisplay').hide();
              }
            });

            $('#extendModal').find('#bahanPembuat').val(obj.message.bahan_pembuat).trigger('change');
          }
          else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && jalat == '26'){
            $('#addtionalSection').html($('#btuBoxDetails').html());
            $('#extendModal').find('#noOfBtu').val(obj.message.btu_box_info.length);

            if(obj.message.btu_box_info.length > 0){
              $("#btuTable").html('');
              btuCount = 0;

              for(var i = 0; i < obj.message.btu_box_info.length; i++){
                var item = obj.message.btu_box_info[i];
                var $addContents = $("#btuCellDetails").clone();

                $("#btuTable").append($addContents.html());

                $("#btuTable").find('.details:last').attr("id", "detail" + btuCount);
                $("#btuTable").find('.details:last').attr("data-index", btuCount);
                $("#btuTable").find('#remove:last').attr("id", "remove" + btuCount);

                $("#btuTable").find('#no:last').attr('name', 'no['+btuCount+']').attr("id", "no" + btuCount).val(item.no);
                $("#btuTable").find('#batuUjian:last').attr('name', 'batuUjian['+btuCount+']').attr("id", "batuUjian" + btuCount).val(item.batuUjian).trigger('change');
                $("#btuTable").find('#batuUjianLain:last').attr('name', 'batuUjianLain['+btuCount+']').attr("id", "batuUjianLain" + btuCount).val(item.batuUjianLain);
                $("#btuTable").find('#penandaanBatuUjian:last').attr('name', 'penandaanBatuUjian['+btuCount+']').attr("id", "penandaanBatuUjian" + btuCount).val(item.penandaanBatuUjian);

                btuCount++;
              }
            }
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
        $('#extendModal').find('#customerType').val(obj.message.customer_type).attr('disabled', true).trigger('change');
        $('#extendModal').find('#customerTypeEdit').val(obj.message.customer_type);
        $('#extendModal').find('#dealer').val(obj.message.dealer).trigger('change');
        $('#extendModal').find('#brand').val(obj.message.brand).trigger('change');
        $('#extendModal').find('#makeIn').val(obj.message.make_in).trigger('change');
        $('#extendModal').find('#validator').val(obj.message.validate_by).trigger('change');
        $('#extendModal').find('#cawangan').val(obj.message.cawangan).trigger('change');
        $('#extendModal').find('#assignTo').val(obj.message.assignTo).trigger('change');
        $('#extendModal').find('#trade').val(obj.message.trade).trigger('change');
        $('#extendModal').find('#newRenew').val(obj.message.stampType).trigger('change');
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
        $('#extendModal').find('#lastYearStampDate').val(formatDate3(obj.message.last_year_stamping_date));
        $('#extendModal').find('#address2').val(obj.message.address2);
        $('#extendModal').find('#noDaftarLama').val(obj.message.no_daftar_lama);
        $('#extendModal').find('#noDaftarBaru').val(obj.message.no_daftar_baru);
        $('#extendModal').find('#address3').val(obj.message.address3);
        $('#extendModal').find('#serial').val(obj.message.serial_no);
        $('#extendModal').find('#pinKeselamatan').val(obj.message.pin_keselamatan);
        $('#extendModal').find('#attnTo').val(obj.message.pic);
        $('#extendModal').find('#siriKeselamatan').val(obj.message.siri_keselamatan);
        $('#extendModal').find('#pic').val(obj.message.pic);
        $('#extendModal').find('#borangD').val(obj.message.borang_d);
        $('#extendModal').find('#borangE').val(obj.message.borang_e);
        $('#extendModal').find('#remark').val(obj.message.remarks);
        $('#extendModal').find('#dueDate').val(formatDate3(obj.message.due_date));
        $('#extendModal').find('#quotation').val(obj.message.quotation_no);
        
        if(obj.message.quotation_attachment){
          $('#extendModal').find('#viewQuotation').attr('href', "view_file.php?file="+obj.message.quotation_attachment).show();
          $('#extendModal').find('#quotationFilePath').val(obj.message.quotation_filepath);
        }

        if(obj.message.invoice_attachment){
          $('#extendModal').find('#InvoiceFilePath').val(obj.message.invoice_filepath);
          $('#extendModal').find('#viewInvoice').attr('href', "view_file.php?file="+obj.message.invoice_attachment).show();
        }

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
        jalat = obj.message.jenis_alat;

        $('#extendModal').on('atkLoaded', function() {
          if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && jalat == '1'){
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
          }
          // else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && jalat == '4'){
          //   $('#addtionalSection').html($('#atsDetails').html());
          //   $('#extendModal').find('#platformCountry').val(obj.message.platform_country).trigger('change');
          // }
          else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && jalat == '2'){
            $('#addtionalSection').html($('#atpDetails').html());
            // $('#extendModal').find('#platformCountry').val(obj.message.platform_country).trigger('change');
            $('#extendModal').find('#jenis_penunjuk').val(obj.message.jenis_penunjuk).trigger('change');
          }else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && jalat == '23'){
            $('#addtionalSection').html($('#atpMotorDetails').html());
            // $('#extendModal').find('#platformCountry').val(obj.message.platform_country).trigger('change');
            $('#extendModal').find('#steelyard').val(obj.message.steelyard).trigger('change');
            $('#extendModal').find('#bilanganKaunterpois').val(obj.message.bilangan_kaunterpois).trigger('change');
            $('#extendModal').find('#nilai1').val(obj.message.nilais[0].nilai);
            $('#extendModal').find('#nilai2').val(obj.message.nilais[1].nilai);
            $('#extendModal').find('#nilai3').val(obj.message.nilais[2].nilai);
            $('#extendModal').find('#nilai4').val(obj.message.nilais[3].nilai);
            $('#extendModal').find('#nilai5').val(obj.message.nilais[4].nilai);
            $('#extendModal').find('#nilai6').val(obj.message.nilais[5].nilai);
          }else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && jalat == '5'){
            $('#addtionalSection').html($('#atnDetails').html());
            // $('#extendModal').find('#platformCountry').val(obj.message.platform_country).trigger('change');
            $('#extendModal').find('#alat_type').val(obj.message.alat_type).trigger('change');
            $('#extendModal').find('#bentuk_dulang').val(obj.message.bentuk_dulang).trigger('change');
          }else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && jalat == '18'){
            $('#addtionalSection').html($('#atnDetails').html());
            // $('#extendModal').find('#platformCountry').val(obj.message.platform_country).trigger('change');
            $('#extendModal').find('#alat_type').val(obj.message.alat_type).trigger('change');
            $('#extendModal').find('#bentuk_dulang').val(obj.message.bentuk_dulang).trigger('change');
          }else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && jalat == '6'){
            $('#addtionalSection').html($('#ateDetails').html());
            // $('#extendModal').find('#platformCountry').val(obj.message.platform_country).trigger('change');
            $('#extendModal').find('#class').val(obj.message.class).trigger('change');
          }else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && jalat == '14'){
            $('#addtionalSection').html($('#sllDetails').html());
            // $('#extendModal').find('#platformCountry').val(obj.message.platform_country).trigger('change');
            $('#extendModal').find('#alat_type').val(obj.message.alat_type).trigger('change');
            $('#extendModal').find('#question1').val(obj.message.questions[0].answer).trigger('change');
            $('#extendModal').find('#question2').val(obj.message.questions[1].answer).trigger('change');
            $('#extendModal').find('#question3').val(obj.message.questions[2].answer).trigger('change');
            $('#extendModal').find('#question4').val(obj.message.questions[3].answer).trigger('change');
            $('#extendModal').find('#question5_1').val(obj.message.questions[4].answer).trigger('change');
            $('#extendModal').find('#question5_2').val(obj.message.questions[5].answer).trigger('change');
            $('#extendModal').find('#question6').val(obj.message.questions[6].answer).trigger('change');
            $('#extendModal').find('#question7').val(obj.message.questions[7].answer).trigger('change');
          }else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && jalat == '7'){
            $('#addtionalSection').html($('#btuDetails').html());
            // $('#extendModal').find('#platformCountry').val(obj.message.platform_country).trigger('change');
            $('#extendModal').find('#penandaanBatuUjian').val(obj.message.penandaan_batu_ujian).trigger('change');

            $('#extendModal').find('#batuUjian').on('change', function(){
              var batuUjian = $(this).val();
              if (batuUjian == 'OTHER'){
                $('#extendModal').find('#batuUjianLainDisplay').show(); 
                $('#extendModal').find('#batuUjianLain').val(obj.message.batu_ujian_lain);
              }else{
                $('#extendModal').find('#batuUjianLainDisplay').hide();
              }
            });

            $('#extendModal').find('#batuUjian').val(obj.message.batu_ujian).trigger('change');
          }else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && jalat == '10'){
            // $('#addtionalSection').html($('#autoPackDetails').html());
            // $('#extendModal').find('#platformCountry').val(obj.message.platform_country);
            // $('#extendModal').find('#nilai1').val(obj.message.nilais[0].nilai);
            // $('#extendModal').find('#nilai2').val(obj.message.nilais[1].nilai);
            // $('#extendModal').find('#nilai3').val(obj.message.nilais[2].nilai);
            // $('#extendModal').find('#nilai4').val(obj.message.nilais[3].nilai);
            // $('#extendModal').find('#nilai5').val(obj.message.nilais[4].nilai);
            // $('#extendModal').find('#nilai6').val(obj.message.nilais[5].nilai);
            $('#addtionalSection').html($('#autoPackDetails').html());
            // $('#extendModal').find('#platformCountry').val(obj.message.platform_country).trigger('change');
            $('#extendModal').find('#jenis_penunjuk').val(obj.message.jenis_penunjuk).trigger('change');
          }
          // else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && jalat == '17'){
          //   $('#addtionalSection').html($('#atsHDetails').html());
          //   $('#extendModal').find('#platformCountry').val(obj.message.platform_country).trigger('change');
          // }
          else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && jalat == '12'){
            $('#addtionalSection').html($('#siaDetails').html());
            // $('#extendModal').find('#platformCountry').val(obj.message.platform_country).trigger('change');

            $('#extendModal').find('#nilaiJangka').on('change', function(){
              var nilaiJangka = $(this).val();
              if (nilaiJangka == 'OTHER'){
                $('#extendModal').find('#nilaiJangkaOtherDisplay').show();
                $('#extendModal').find('#nilaiJangkaOther').val(obj.message.nilai_jangka_other);
              }else{
                $('#extendModal').find('#nilaiJangkaOtherDisplay').hide();
              }
            });

            $('#extendModal').find('#diperbuatDaripada').on('change', function(){
              var diperbuatDaripada = $(this).val();
              if (diperbuatDaripada == 'OTHER'){
                $('#extendModal').find('#diperbuatDaripadaOtherDisplay').show();
                $('#extendModal').find('#diperbuatDaripadaOther').val(obj.message.diperbuat_daripada_other);
              }else{
                $('#extendModal').find('#diperbuatDaripadaOtherDisplay').hide();
              }
            });

            $('#extendModal').find('#nilaiJangka').val(obj.message.nilai_jangka).trigger('change');
            $('#extendModal').find('#diperbuatDaripada').val(obj.message.diperbuat_daripada).trigger('change');
          }
          else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && jalat == '11'){
            $('#addtionalSection').html($('#bapDetails').html());
            $('#extendModal').find('#pamNo').val(obj.message.pam_no).trigger('change');
            $('#extendModal').find('#kelulusanBentuk').val(obj.message.kelulusan_bentuk).trigger('change');
            $('#extendModal').find('#alatType').val(obj.message.alat_type).trigger('change');
            $('#extendModal').find('#kadarPengaliran').val(obj.message.kadar_pengaliran).trigger('change');
            $('#extendModal').find('#bentukPenunjuk').val(obj.message.bentuk_penunjuk).trigger('change');

            $('#extendModal').find('#jenama').on('change', function(){
              var jenama = $(this).val();
              if (jenama == 'OTHER'){
                $('#extendModal').find('#jenamaOtherDisplay').show();
                $('#extendModal').find('#jenamaOther').val(obj.message.jenama_other);
              }else{
                $('#extendModal').find('#jenamaOtherDisplay').hide();
              }
            });

            $('#extendModal').find('#jenama').val(obj.message.jenama).trigger('change');
          }
          else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && jalat == '13'){ 
            $('#addtionalSection').html($('#sicDetails').html());
            $('#extendModal').find('#nilaiMaksimum').val(obj.message.nilai_jangkaan_maksimum).trigger('change');

            $('#extendModal').find('#bahanPembuat').on('change', function(){
              var bahanPembuat = $(this).val();
              if (bahanPembuat == 'OTHER'){
                $('#extendModal').find('#bahanPembuatOtherDisplay').show();
                $('#extendModal').find('#bahanPembuatOther').val(obj.message.bahan_pembuat_other);
              }else{
                $('#extendModal').find('#bahanPembuatOtherDisplay').hide();
              }
            });

            $('#extendModal').find('#bahanPembuat').val(obj.message.bahan_pembuat).trigger('change');
          }
          else if((obj.message.validate_by == '10' || obj.message.validate_by == '9') && jalat == '26'){
            $('#addtionalSection').html($('#btuBoxDetails').html());
            $('#extendModal').find('#noOfBtu').val(obj.message.btu_box_info.length);

            if(obj.message.btu_box_info.length > 0){
              $("#btuTable").html('');
              btuCount = 0;

              for(var i = 0; i < obj.message.btu_box_info.length; i++){
                var item = obj.message.btu_box_info[i];
                var $addContents = $("#btuCellDetails").clone();

                $("#btuTable").append($addContents.html());

                $("#btuTable").find('.details:last').attr("id", "detail" + btuCount);
                $("#btuTable").find('.details:last').attr("data-index", btuCount);
                $("#btuTable").find('#remove:last').attr("id", "remove" + btuCount);

                $("#btuTable").find('#no:last').attr('name', 'no['+btuCount+']').attr("id", "no" + btuCount).val(item.no);
                $("#btuTable").find('#batuUjian:last').attr('name', 'batuUjian['+btuCount+']').attr("id", "batuUjian" + btuCount).val(item.batuUjian).trigger('change');
                $("#btuTable").find('#batuUjianLain:last').attr('name', 'batuUjianLain['+btuCount+']').attr("id", "batuUjianLain" + btuCount).val(item.batuUjianLain);
                $("#btuTable").find('#penandaanBatuUjian:last').attr('name', 'penandaanBatuUjian['+btuCount+']').attr("id", "penandaanBatuUjian" + btuCount).val(item.penandaanBatuUjian);

                btuCount++;
              }
            }
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
  $('#spinnerLoading').hide();

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
  var optionText = $('#jenisAlat option[value="' + type + '"]').text();
  window.open('php/printBorang.php?userID='+id+'&file='+optionText+'&validator='+validate, '_blank');
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

function log(id) {
  $('#spinnerLoading').show();
  $.post('php/getLog.php', {id: id, type: 'Stamping'}, function(data){
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
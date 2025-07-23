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
  $_SESSION['page']='oldstamp';
	
	if(($row = $result->fetch_assoc()) !== null){
    $role = $row['role_code'];
  }
  $stmt->close();

  $customers = $db->query("SELECT * FROM customers WHERE customer_status = 'CUSTOMERS' AND deleted = '0'");
  $customers2 = $db->query("SELECT * FROM customers WHERE customer_status = 'CUSTOMERS' AND deleted = '0'");
  $machinetypes = $db->query("SELECT * FROM machines WHERE deleted = '0'");
  $machinetypes2 = $db->query("SELECT * FROM machines WHERE deleted = '0'");
  $brands = $db->query("SELECT * FROM brand WHERE deleted = '0'");
  $brands2 = $db->query("SELECT * FROM brand WHERE deleted = '0'");
  $models = $db->query("SELECT * FROM model WHERE deleted = '0'");
  $sizes = $db->query("SELECT * FROM size WHERE deleted = '0'");
  $capacities = $db->query("SELECT * FROM capacity WHERE deleted = '0'");
  $problems = $db->query("SELECT * FROM problem WHERE deleted = '0'");
  $users = $db->query("SELECT * FROM users WHERE deleted = '0'");
  $users2 = $db->query("SELECT * FROM users WHERE deleted = '0'");
  $validators = $db->query("SELECT * FROM validators WHERE deleted = '0'");
  $validators2 = $db->query("SELECT * FROM validators WHERE deleted = '0' AND type = 'STAMPING'");  
  $alats = $db->query("SELECT * FROM alat WHERE deleted = '0'");
  $products = $db->query("SELECT * FROM products WHERE deleted = '0'");
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
        <h1 class="m-0 text-dark">Cancelled Stamping</h1>
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
                    <input type="text" class="form-control datetimepicker-input" data-target="#fromDatePicker" id="fromDate" />
                    <div class="input-group-append" data-target="#fromDatePicker" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
                </div>

                <div class="form-group col-3">
                  <label>To Expired Date:</label>
                  <div class="input-group date" id="toDatePicker" data-target-input="nearest">
                    <input type="text" class="form-control datetimepicker-input" data-target="#toDatePicker" id="toDate" />
                    <div class="input-group-append" data-target="#toDatePicker" data-toggle="datetimepicker">
                      <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                    </div>
                  </div>
                </div>

                <div class="col-3">
                  <div class="form-group">
                    <label>Customer No:</label>
                    <select class="form-control select2" id="customerNoFilter" name="customerNoFilter">
                      <option value="" selected disabled hidden>Please Select</option>
                      <?php while ($rowCustomer2 = mysqli_fetch_assoc($customers2)) { ?>
                      <option value="<?=$rowCustomer2['id'] ?>"><?=$rowCustomer2['customer_name'] ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>

                <div class="col-3">
                  <div class="form-group">
                    <label>Description Instruments:</label>
                    <select class="form-control select2" id="machineTypeFilter" name="machineTypeFilter">
                      <option value="" selected disabled hidden>Please Select</option>
                      <?php while ($machineType2 = mysqli_fetch_assoc($machinetypes2)) { ?>
                        <option value="<?= $machineType2['id'] ?>"><?= $machineType2['machine_type'] ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>

                <div class="col-3">
                  <div class="form-group">
                    <label>Select Validators:</label>
                    <select class="form-control select2" id="validatorFilter" name="validatorFilter">
                      <option value="" selected disabled hidden>Please Select</option>
                      <?php while ($validator2 = mysqli_fetch_assoc($validators2)) { ?>
                        <option value="<?= $validator2['id'] ?>"><?= $validator2['validator'] ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>

                <div class="col-3">
                  <div class="form-group">
                    <label>Brand:</label>
                    <select class="form-control select2" id="brandFilter" name="brandFilter">
                      <option value="" selected disabled hidden>Please Select</option>
                      <?php while ($brand2 = mysqli_fetch_assoc($brands2)) { ?>
                        <option value="<?= $brand2['id'] ?>"><?= $brand2['brand'] ?></option>
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
              <div class="col-8"><p class="mb-0" style="font-size: 110%">Cancelled Stamping</p></div>
              <div class="col-2">
                <button type="button" class="btn btn-block btn-sm bg-gradient-danger" id="multiDeactivate" data-bs-toggle="tooltip" title="Delete Stampings"><i class="fa-solid fa-ban"></i> Delete Stampings</button></button>
              </div>
              <div class="col-2">
                <button type="button" class="btn btn-block btn-sm bg-gradient-success" id="exportExcel" data-bs-toggle="tooltip" title="Export Excel"><i class="fa-regular fa-file-excel"></i> Export Excel</button>
              </div>
              
              <!--div class="col-2">
                <button type="button" class="btn btn-block bg-gradient-info btn-sm" id="exportBorangs">Export Borangs</button>
              </div-->
              <!--div class="col-2">
                <a href="/template/Stamping Record Template.xlsx" download><button type="button" class="btn btn-block bg-gradient-danger btn-sm" id="downloadExccl">Download Template</button></a>
              </div-->
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
      <form role="form" id="extendForm">
        <div class="modal-header bg-gray-dark color-palette">
          <h4 class="modal-title">Add New Stamping</h4>
          <button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body" >
          <input type="hidden" class="form-control" id="id" name="id">
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
                <div class="col-4">
                  <div class="form-group">
                    <label>Address Line 1 * </label>
                    <input class="form-control" type="text" placeholder="Address Line 1" id="address1" name="address1" required>
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>Address Line 2 </label>
                    <input class="form-control" type="text" placeholder="Address Line 2" id="address2" name="address2">
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>Address Line 3 </label>
                    <input class="form-control" type="text" placeholder="Address Line 3" id="address3" name="address3">
                  </div>
                </div>
                <div class="col-4">
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
                    <label>Product *</label>
                    <select class="form-control select2" style="width: 100%;" id="product" name="product" required>
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
                    <label>Capacity * </label>
                    <select class="form-control select2" style="width: 100%;" id="capacity" name="capacity" required>
                      <option selected="selected">-</option>
                      <?php while($rowCA=mysqli_fetch_assoc($capacities)){ ?>
                        <option value="<?=$rowCA['id'] ?>"><?=$rowCA['name'] ?></option>
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
                    <label>No Daftar </label>
                    <input class="form-control" type="text" placeholder="No Daftar" id="noDaftar" name="noDaftar">
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>No PIN Pelekat Keselamatan </label>
                    <input class="form-control" type="text" placeholder="No PIN Pelekat Keselamatan" id="pinKeselamatan" name="pinKeselamatan">
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
                    <label>No. Borang D</label>
                    <input class="form-control" type="text" placeholder="No. Borang D" id="borangD" name="borangD">
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
            </div>
          </div>

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
                    <label>PO Date *</label>
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

  var fromDateValue = $('#fromDate').val();
  var toDateValue = $('#toDate').val();
  var customerNoFilter = $('#customerNoFilter').val() ? $('#customerNoFilter').val() : '';
  var machineTypeFilter = $('#machineTypeFilter').val() ? $('#machineTypeFilter').val() : '';
  var validatorFilter = $('#validatorFilter').val() ? $('#validatorFilter').val() : '';
  var brandFilter = $('#brandFilter').val() ? $('#brandFilter').val() : '';
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
    // 'columnDefs': [ { orderable: false, targets: [0] }],
    'ajax': {
      'type': 'POST',
      'url':'php/filterCancelledStamping.php',
      'data': {
        fromDate: fromDateValue,
        toDate: toDateValue,
        customer: customerNoFilter,
        machineType: machineTypeFilter,
        validator: validatorFilter,
        brand: brandFilter,
        daftarLama: daftarLamaNoFilter,
        daftarBaru: daftarBaruNoFilter,
        borang: borangNoFilter,
        serial: serialNoFilter,
        quotation: quoteNoFilter,
        status: 'Cancelled'
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

            if ('<?=$role ?>' == 'ADMIN' || '<?=$role ?>' == 'SUPER_ADMIN') {
              dropdownMenu += 
                '<a class="dropdown-item" id="revertBtn' + data + '" onclick="revert(' + data + ')"><i class="fa fa-arrow-circle-left"></i> Revert</a>'+
                '<a class="dropdown-item" id="log' + data + '" onclick="log(' + data + ')"><i class="fa fa-list" aria-hidden="true"></i> Log</a>'+
                '<a class="dropdown-item" id="delete'+data+'" onclick="deactivate(' + data + ')"><i class="fa fa-times" aria-hidden="true"></i> Delete</a>';
            }else{
              dropdownMenu += '<a class="dropdown-item" id="log' + data + '" onclick="log(' + data + ')"><i class="fa fa-list" aria-hidden="true"></i> Log</a>';
            }
            
          
          dropdownMenu += '</div></div>';

          return dropdownMenu;
        }
      },
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

  // $('#weightTable tbody').on('click', 'td.dt-control', function () {
  //   var tr = $(this).closest('tr');
  //   var row = table.row(tr);

  //   if ( row.child.isShown() ) {
  //     // This row is already open - close it
  //     row.child.hide();
  //     tr.removeClass('shown');
  //   }
  //   else {
  //     $.post('php/getStamp.php', {userID: row.data().id, format: 'EXPANDABLE'}, function (data){
  //       var obj = JSON.parse(data); 
  //       if(obj.status === 'success'){ console.log(obj.message);
  //         row.child( format(obj.message) ).show();tr.addClass("shown");
  //       }
  //     });
  //   }
  // });

  $.validator.setDefaults({
    submitHandler: function () {
      if($('#extendModal').hasClass('show')){
        $('#spinnerLoading').show();

        $.post('php/insertStamping.php', $('#extendForm').serialize(), function(data){
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
    var machineTypeFilter = $('#machineTypeFilter').val() ? $('#machineTypeFilter').val() : '';
    var validatorFilter = $('#validatorFilter').val() ? $('#validatorFilter').val() : '';
    var brandFilter = $('#brandFilter').val() ? $('#brandFilter').val() : '';
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
      // 'columnDefs': [ { orderable: false, targets: [0] }],
      'ajax': {
        'type': 'POST',
        'url':'php/filterCancelledStamping.php',
        'data': {
          fromDate: fromDateValue,
          toDate: toDateValue,
          customer: customerNoFilter,
          machineType: machineTypeFilter,
          validator: validatorFilter,
          brand: brandFilter,
          daftarLama: daftarLamaNoFilter,
          daftarBaru: daftarBaruNoFilter,
          borang: borangNoFilter,
          serial: serialNoFilter,
          quotation: quoteNoFilter,
          status: 'Cancelled'
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

              if ('<?=$role ?>' == 'ADMIN' || '<?=$role ?>' == 'SUPER_ADMIN') {
                dropdownMenu += 
                  '<a class="dropdown-item" id="revertBtn' + data + '" onclick="revert(' + data + ')"><i class="fa fa-arrow-circle-left"></i> Revert</a>'+
                  '<a class="dropdown-item" id="log' + data + '" onclick="log(' + data + ')"><i class="fa fa-list" aria-hidden="true"></i> Log</a>'+
                  '<a class="dropdown-item" id="delete'+data+'" onclick="deactivate(' + data + ')"><i class="fa fa-times" aria-hidden="true"></i> Delete</a>';
              }else{
                dropdownMenu += '<a class="dropdown-item" id="log' + data + '" onclick="log(' + data + ')"><i class="fa fa-list" aria-hidden="true"></i> Log</a>';
              }
              
            
            dropdownMenu += '</div></div>';

            return dropdownMenu;
          }
        },
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

  $('#exportExcel').on('click', function () {
    var fromDateValue = $('#fromDate').val();
    var toDateValue = $('#toDate').val();

    let selectedIds = [];
    $("#weightTable tbody input[type='checkbox']").each(function () {
      if (this.checked) {
        selectedIds.push($(this).val());
      }
    });

    window.open("php/export.php?fromDate="+fromDateValue+"&toDate="+toDateValue+
    "&stamps="+selectedIds+"&type=cancelledStamp");    
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
        if (confirm('DO YOU CONFIRMED TO DELETE THE FOLLOWING STAMPINGS?')) {
          $.post('php/deleteStamp.php', {id: selectedIds, status: 'DELETE', type: 'MULTI'}, function(data){
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
        alert("Please select at least one stamping to delete.");
        $('#spinnerLoading').hide();
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
      $('#extendModal').find('#company').parents('.form-group').find('.select2-container').hide();
      $('#extendModal').find('#companyText').show();
      $('#extendModal').find('#companyText').val('');
    }
    else{
      $('#extendModal').find('#company').html($('select#customerNoHidden').html());
      $('#extendModal').find('#company').show();
      $('#extendModal').find('#company').parents('.form-group').find('.select2-container').show();
      $('#extendModal').find('#companyText').hide();
      $('#extendModal').find('#companyText').val('');
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

  $('#extendModal').find('#product').on('change', function(){
    var price = parseFloat($(this).find(":selected").attr("data-price"));
    var machine = parseFloat($(this).find(":selected").attr("data-machine"));
    var alat = parseFloat($(this).find(":selected").attr("data-alat"));
    var capacity = parseFloat($(this).find(":selected").attr("data-capacity"));
    var validator = parseFloat($(this).find(":selected").attr("data-validator"));
    var includeCert = $('#includeCert').val();
    var certPrice = 30;
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

  $('#extendModal').find('#includeCert').on('change', function(){
    var price = parseFloat($('#product').find(":selected").attr("data-price"));
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
        <p><strong>Total Billing Price:</strong> ${row.total_charges}</p>`;

      if ('<?=$role ?>' == 'ADMIN' || '<?=$role ?>' == 'SUPER_ADMIN') {
        returnString += `<div class="row">
          <div class="col-1"><button title="Revert" type="button" id="revertBtn${row.id}" onclick="revert(${row.id})" class="btn btn-success btn-sm"><i class="fa fa-arrow-circle-left"></i></button></div>
          <div class="col-1"><button title="Log" type="button" id="log${row.id}" onclick="log(${row.id})" class="btn btn-secondary btn-sm"><i class="fa fa-list" aria-hidden="true"></i></button></div>
          <div class="col-1"><button title="Cancel" type="button" id="delete${row.id}" onclick="deactivate(${row.id})" class="btn btn-danger btn-sm"><i class="fa fa-times" aria-hidden="true"></i></button></div>
        </div>`;
      }else{
        returnString += `<div class="row">
          <div class="col-1"><button title="Log" type="button" id="log${row.id}" onclick="log(${row.id})" class="btn btn-secondary btn-sm"><i class="fa fa-list" aria-hidden="true"></i></button></div>
        </div>`;
      }

     returnString += `</div>
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
                    `;

    if (row.btu_info.length > 0){
      var batuUjianVal = '';
      returnString += `
        <table style="width: 100%;">
          <thead>
            <tr>
              <th>No.</th>
              <th>Batu Ujian</th>
              <th>Penandaan Pada Batu Ujian</th>
              <th>No Daftar Lama</th>
              <th>No Daftar Baru</th>
            </tr>
          </thead>
          <tbody>`;

          for (i = 0; i < row.btu_info.length; i++) {
            returnString += `<tr><td>${row.btu_info[i].no}</td>`;

            if (row.btu_info[i].batuUjian == 'OTHER'){
              returnString += `<td>${row.btu_info[i].batuUjianLain}</td>`;
            }else{
              if (row.btu_info[i].batuUjian == 'BESI_TUANGAN'){
                batuUjianVal = 'BESI TUANGAN';
              }
              else if (row.btu_info[i].batuUjian == 'TEMBAGA'){
                batuUjianVal = 'TEMBAGA';
              }
              else if (row.btu_info[i].batuUjian == 'NIKARAT'){
                batuUjianVal = 'NIKARAT';
              }

              returnString += `<td>${batuUjianVal}</td>`;
            }

            returnString += `
              <td>${row.btu_info[i].penandaanBatuUjian}</td>
              <td>${row.btu_info[i].batuDaftarLama}</td>
              <td>${row.btu_info[i].batuDaftarBaru}</td>
              </tr>
            `;
          }
      returnString += `</tbody>
        </table>
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
              <th>No Daftar Lama</th>
              <th>No Daftar Baru</th>
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

            returnString += `
              <td>${row.btu_box_info[i].penandaanBatuUjian}</td>
              <td>${row.btu_box_info[i].batuDaftarLama}</td>
              <td>${row.btu_box_info[i].batuDaftarBaru}</td>
              </tr>
            `;
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

function revert(id) {
  if (confirm('DO YOU CONFIRMED TO REVERT?')) {
    $('#spinnerLoading').show();
    $.post('php/recallStamp.php', {userID: id}, function(data){
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
  if (confirm('DO YOU CONFIRMED TO DELETE THE FOLLOWING DETAILS?')) {
    $('#spinnerLoading').show();
    $.post('php/deleteStamp.php', {id: id, status: 'DELETE'}, function(data){
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
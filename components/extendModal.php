<?php
?>

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
          <!--Customer Details--->
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
            <div class="col-4">
              <div class="form-group">
                <label>Notification Period (Months)</label>
                <input class="form-control" type="number" placeholder="Notification Period" id="notificationPeriod" name="notificationPeriod">
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
                    <input type="hidden" id="customerTypeEdit" name="customerTypeEdit">
                  </div>
                </div>
                <div class="col-3" id="otherCodeView" style="display: none;">
                  <div class="form-group">
                    <label>Other Code (AutoCount etc.)</label>
                    <input class="form-control" type="text" placeholder="Enter Other System Code" id="otherCode" name="otherCode">
                  </div>
                </div>
                <div class="col-3">
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
                  <div class="col-3" id="addr5" style="display: none;">
                    <div class="form-group">
                      <label>Address Line 5 </label>
                      <input class="form-control" type="text" placeholder="Address Line 5" id="address5" name="address5">
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
          <!--Customer Details--->

          <!--Machine Details--->
          <div class="card card-primary">
            <div class="card-body">
              <div class="row">
                <h4>Machine / Indicator Information</h4>
              </div>
              <div class="row">
                <div class="col-4">
                  <div class="form-group">
                    <label>Ownership Status</label>
                    <select class="form-control select2" style="width: 100%;" id="ownershipStatus" name="ownershipStatus">
                      <option value="RENT">Rental Unit</option>
                      <option value="OWN">Customer Unit</option>
                    </select>
                  </div>
                </div>
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
                        <option value="<?=$rowA['id'] ?>" data-name="<?=$rowA['alat'] ?>"><?=$rowA['alat'] ?></option>
                      <?php } ?>
                    </select>
                    <input type="hidden" id="jenisAlatName" name="jenisAlatName">
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>Machine Name</label>
                    <select class="form-control select2" style="width: 100%;" id="machineName" name="machineName" required>
                      <option selected="selected">-</option>
                      <?php while($rowMN=mysqli_fetch_assoc($machineNames)){ ?>
                        <option value="<?=$rowMN['id'] ?>"><?=$rowMN['machine_name'] ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>Machine Location</label>
                    <input type="text" class="form-control" id="machineLocation" name="machineLocation">
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>Machine Area</label>
                    <input type="text" class="form-control" id="machineArea" name="machineArea">
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>Machine Serial No.</label>
                    <input type="text" class="form-control" id="machineSerialNo" name="machineSerialNo">
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
                    <label>Assigned To Technician 1 *</label>
                    <select class="form-control select2" style="width: 100%;" id="assignTo" name="assignTo" required>
                      <?php while($technician=mysqli_fetch_assoc($technicians)){ ?>
                        <option value="<?=$technician['id'] ?>"><?=$technician['name'] ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>Assigned To Technician 2</label>
                    <select class="form-control select2" style="width: 100%;" id="assignTo2" name="assignTo2">
                      <?php while($technician=mysqli_fetch_assoc($technicians2)){ ?>
                        <option value="<?=$technician['id'] ?>"><?=$technician['name'] ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>Assigned To Technician 3</label>
                    <select class="form-control select2" style="width: 100%;" id="assignTo3" name="assignTo3">
                      <?php while($technician=mysqli_fetch_assoc($technicians3)){ ?>
                        <option value="<?=$technician['id'] ?>"><?=$technician['name'] ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="col-4" id="rentalAttachment" style="display:none">
                  <div class="form-group">
                    <label>Rental Attachment</label>
                    <div class="d-flex">
                      <div class="col-10">
                        <input type="file" class="form-control" id="uploadRentalAttachment" name="uploadRentalAttachment">
                      </div>
                      <div class="col-2 mt-1">
                        <a href="" id="viewRental" name="viewRental" target="_blank" class="btn btn-success btn-sm" role="button" style="display: none;"><i class="fa fa-file-pdf-o"></i></a>
                      </div>
                    </div>
                    <input type="text" id="rentalFilePath" name="rentalFilePath" style="display:none">           
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!--Machine Details--->
          
          <!--Stamping Details--->
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
                <div class="col-4" id="validatorLamaView" style="display:none;">
                  <div class="form-group">
                    <label>Validator (Lama)</label>
                    <select class="form-control select2" style="width: 100%;" id="validatorlama" name="validatorlama">
                      <?php while($rowVA=mysqli_fetch_assoc($validators3)){ ?>
                        <option value="<?=$rowVA['id'] ?>"><?=$rowVA['validator'] ?></option>
                      <?php } ?>
                    </select>
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>Validator (Baru) *</label>
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
                <div class="col-4" id="sealLamaView" style="display:none;">
                  <div class="form-group">
                    <label>Seal No (Lama)</label>
                    <input class="form-control" type="text" placeholder="Seal No (Lama)" id="sealNoLama" name="sealNoLama">
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>Pegawai/Contact No</label>
                    <select class="form-control select2" style="width: 100%;" id="pegawaiContact" name="pegawaiContact">
                      <option selected="selected"></option>
                      <?php while($officer=mysqli_fetch_assoc($validatorOfficers)){ ?>
                        <option value="<?=$officer['id'] ?>"><?=$officer['officer_name']?></option>
                      <?php } ?>
                    </select>
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
                    <label>Seal No (Baru)</label>
                    <input class="form-control" type="text" placeholder="Seal No (Baru)" id="sealNoBaru" name="sealNoBaru">
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
                <div class="col-4" id="borangEDateView" style="display:none;">
                  <div class="form-group">
                    <label>Borang E Date</label>
                    <div class='input-group date' id="borangEDatePicker" data-target-input="nearest">
                      <input type='text' class="form-control datetimepicker-input" data-target="#borangEDatePicker" id="borangEDate" name="borangEDate"/>
                      <div class="input-group-append" data-target="#borangEDatePicker" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
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
                <div class="col-4" id="certNoView" style="display:none">
                  <div class="form-group">
                    <label>Certificate No * </label>
                    <input class="form-control" type="text" placeholder="Certificate No" id="certNo" name="certNo">
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
          <!--Stamping Details--->

          <div id="addtionalSection"></div>

          <div class="card card-primary">
            <div class="card-body">
              <div class="row">
                <h4>Billing Information</h4>
              </div>
              <div class="row">
                <!--Quotation Details--->
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
                <!--Quotation Details--->

                <!--Billing Details--->
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
                <div class="col-4">
                  <div class="form-group">
                    <label>Invoice Payment Type</label>
                    <select class="form-control select2" id="invoicePaymentType" name="invoicePaymentType">
                      <option value="Cash">Cash</option>
                      <option value="Check">Check</option>
                      <option value="Online">Online Transfer</option>
                    </select>
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>Invoice Payment Reference</label>
                    <input class="form-control" type="text" placeholder="Invoice Payment Reference" id="invoicePayRef" name="invoicePayRef">
                  </div>
                </div>
                <!--Billing Details--->
              </div>
            </div>
          </div>

          <!--Fee Details--->
          <div class="card card-primary">
            <div class="card-body">
              <div class="row">
                <h4>Stamping Fees</h4>
              </div>
              <div class="row">
                <div class="col-4">
                  <div class="form-group">
                    <label>Validator Invoice </label>
                    <input type="text" class="form-control" id="validatorInvoice" name="validatorInvoice">
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
                    <label>Sub Total Amount With SST</label>
                    <input type="text" class="form-control" id="subAmountSst" name="subAmountSst" readonly>
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>Rebate By %</label>
                    <input type="text" class="form-control" id="rebate" name="rebate" value="0">
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>Rebate Amount</label>
                    <input type="text" class="form-control" id="rebateAmount" name="rebateAmount" readonly>
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>Sub Total Amount</label>
                    <input type="text" class="form-control" id="subAmount" name="subAmount" readonly>
                  </div>
                </div>
              </div>
              <div class="row">
                <h5 class="text-danger">Service & Labour Charges</h5>
              </div>
              <div class="row">
                <div class="col-4">
                  <div class="form-group">
                    <label>Labour Charge</label>
                    <input type="number" class="form-control" id="labourCharge" name="labourCharge">
                  </div>
                </div>
                <div class="col-4" id="cerId">
                  <div class="form-group">
                    <label>Total Stamping Fee + Labour Charge</label>
                    <input type="text" class="form-control" id="stampLabourCharge" name="stampLabourCharge" readonly>
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>Internal Round Up</label>
                    <input type="number" class="form-control" id="roundUp" name="roundUp">
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>Total Charges to Customer</label>
                    <input type="text" class="form-control" id="totalCharge" name="totalCharge" readonly>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!--Fee Details--->

          <!--Remark Details--->
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Remark</label>
                <textarea class="form-control" type="text" placeholder="Remark" id="remark" name="remark"></textarea>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <label>Internal Remark</label>
                <textarea class="form-control" type="text" placeholder="Internal Remark" id="internalRemark" name="internalRemark"></textarea>
              </div>
            </div>
          </div>
          <!--Remark Details--->
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

<script type="text/html" id="atkDetails">
  <div class="card card-primary">
    <div class="card-body">
      <div class="row">
        <h4>Additional Information (ATK)</h4>
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
        <!-- <div class="col-4">
          <div class="form-group">
            <label>No. Serial Indicator *</label>
            <input type="text" class="form-control" id="noSerialIndicator" name="noSerialIndicator">
          </div>
        </div> -->
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
            <label>Lain-lain Butiran</label>
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
        <h4>Additional Information (ATS)</h4>
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
        <h4>Additional Information (ATP)</h4>
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

<script type="text/html" id="atpMotorDetails">
  <div class="card card-primary">
    <div class="card-body">
      <div class="row">
        <h4>Additional Information (ATP - MOTORCAR)</h4>
      </div>
      <div class="row">
        <!-- <div class="form-group col-4">
          <label for="model">Platform Made In *</label>
          <select class="form-control select2" id="platformCountry" name="platformCountry" required>
            <option value="" selected disabled hidden>Please Select</option>
            <?php while($rowcountry=mysqli_fetch_assoc($countryAtpMotor)){ ?>
              <option value="<?=$rowcountry['id'] ?>"><?=$rowcountry['name'] ?></option>
            <?php } ?>
          </select>
        </div> -->
        <div class="form-group col-4">
          <label>Had Terima Steelyard (kg)*</label>
          <input type="text" class="form-control" id="steelyard" name="steelyard">
        </div>
        <div class="form-group col-4">
          <label>Bilangan Kaunterpois (biji)*</label>
          <input type="text" class="form-control" id="bilanganKaunterpois" name="bilanganKaunterpois">
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
</script>

<script type="text/html" id="atnDetails">
  <div class="card card-primary">
    <div class="card-body">
      <div class="row">
        <h4>Additional Information (ATN)</h4>
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
        <h4>Additional Information (ATE)</h4>
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
        <h4>Additional Information (SLL)</h4>
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
        <h4>Additional Information (BTU)</h4>
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
        <h4>Additional Information (ATP-Auto Machine)</h4>
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

<script type="text/html" id="atsHDetails">
  <div class="card card-primary">
    <div class="card-body">
      <div class="row">
        <h4>Additional Information (ATS - H)</h4>
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
        <h4>Additional Information (SIA)</h4>
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
        <h4>Additional Information (BAP)</h4>
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
        <h4>Additional Information (SIC)</h4>
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
        <h4>Additional Information (BTU - BOX)</h4>
      </div>
      <div class="row">
        <div class="col-4">
          <div class="form-group">
            <label>No. of BTU *</label>
            <input type="number" class="form-control" id="noOfBtu" name="noOfBtu" required min="1">
          </div>
        </div>
        <div class="col-8 d-flex justify-content-end align-items-start">
          <button style="margin-left:auto;margin-right: 25px;" type="button" class="btn btn-primary add-btu">Add BTU</button>
        </div>
        <div class="col-12">
          <table style="width: 100%;">
            <thead>
              <tr>
                <th style="width: 5%;">No.</th>
                <th>Batu Ujian</th>
                <th>Penandaan Pada Batu Ujian</th>
                <th>No Daftar Lama</th>
                <th>No Daftar Baru</th>
                <th>No Siri Pelekat Keselamatan</th>
                <th>No Borang D</th>
                <th>No Borang E</th>
                <th>Price</th>
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

<script>
function newEntry(){
  var date = new Date();
  $('#capacityHigh').hide();

  $('#extendModal').find('#id').val("");
  $('#extendModal').find('#type').val("DIRECT");
  $('#extendModal').find('#companyBranch').val("<?=$branch ?>").trigger('change');
  $('#isResseller').hide();
  $('#isResseller2').hide();
  $('#isResseller3').hide();
  $('#isResseller4').hide();
  $('#isResseller5').hide();
  $('#extendModal').find('#customerType').val("EXISTING").attr('disabled', false).trigger('change');
  $('#extendModal').find('#brand').val('').trigger('change');
  $('#extendModal').find('#validatorlama').val('').trigger('change');
  $('#extendModal').find('#validator').val('').trigger('change');
  $('#extendModal').find('#product').val('');
  $('#extendModal').find('#company').val('');
  $('#extendModal').find('#companyText').val('').trigger('change');
  $('#extendModal').find('#machineType').val('').trigger('change');
  $('#extendModal').find('#jenisAlat').val('').trigger('change');
  $('#extendModal').find('#machineName').val('').trigger('change');
  $('#extendModal').find('#machineLocation').val('');
  $('#extendModal').find('#machineArea').val('');
  $('#extendModal').find('#machineSerialNo').val('');
  $('#extendModal').find('#address1').val('');
  $('#extendModal').find('#address2').val('');
  $('#extendModal').find('#address3').val('');
  $('#extendModal').find('#address4').val('');
  $('#extendModal').find('#address5').val('');
  $('#extendModal').find('#model').val("").trigger('change');
  $('#extendModal').find('#makeIn').val("").trigger('change');
  $('#extendModal').find('#cawangan').val("").trigger('change');
  $('#extendModal').find('#stampDate').val('');
  $('#extendModal').find('#lastYearStampDate').val('');
  $('#extendModal').find('#capacity_single').val('').trigger('change');
  $('#extendModal').find('#capacity_multi').val('').trigger('change');
  $('#extendModal').find('#assignTo').val('').trigger('change');
  $('#extendModal').find('#assignTo2').val('').trigger('change');
  $('#extendModal').find('#assignTo3').val('').trigger('change');
  $('#extendModal').find('#ownershipStatus').val('OWN').trigger('change');
  $('#extendModal').find('#uploadRentalAttachment').val('');
  $('#extendModal').find('#trade').val('').trigger('change');
  $('#extendModal').find('#branch').val('').trigger('change');
  $('#extendModal').find('#noDaftarLama').val('');
  $('#extendModal').find('#noDaftarBaru').val('');
  $('#extendModal').find('#sealNoLama').val('');
  $('#extendModal').find('#sealNoBaru').val('');
  $('#extendModal').find('#pegawaiContact').val('');
  $('#extendModal').find('#newRenew').val('NEW').trigger('change');
  $('#extendModal').find('#certNo').val('');
  $('#extendModal').find('#serial').val('');
  $('#extendModal').find('#pinKeselamatan').val('');
  $('#extendModal').find('#attnTo').val('<?=$user ?>');
  $('#extendModal').find('#siriKeselamatan').val('');
  $('#extendModal').find('#pic').val("");
  $('#extendModal').find('#borangD').val("");
  $('#extendModal').find('#borangE').val("");
  $('#extendModal').find('#borangEDate').val("");
  $('#extendModal').find('#remark').val("");
  $('#extendModal').find('#internalRemark').val("");
  $('#extendModal').find('#dueDate').val('');
  $('#extendModal').find('#quotation').val("");
  $('#extendModal').find('#quotationDate').val('');
  $('#extendModal').find('#includeCert').val("NO").trigger('change');
  $('#extendModal').find('#poNo').val("");
  $('#extendModal').find('#poDate').val('');
  $('#extendModal').find('#cashBill').val("");
  $('#extendModal').find('#invoice').val('');
  $('#extendModal').find('#penentusanBaru').val('');
  $('#extendModal').find('#penentusanSemula').val('');
  $('#extendModal').find('#kelulusanMSPK').val('').trigger('change');
  $('#extendModal').find('#noMSPK').val('');
  $('#extendModal').find('#platformCountry').val('').trigger('change');
  $('#extendModal').find('#platformType').val('').trigger('change');
  $('#extendModal').find('#size').val('').trigger('change');
  $('#extendModal').find('#jenisPelantar').val('').trigger('change');
  $('#extendModal').find('#others').val('');
  $('#extendModal').find('#viewQuotation').hide();
  $('#extendModal').find('#uploadQuotationAttachment').val('');
  $('#extendModal').find('#quotationFilePath').val('');
  $('#extendModal').find('#newInvoice').show();
  $('#extendModal').find('#uploadInvoiceAttachment').val('');
  $('#extendModal').find('#notificationPeriod').val(1);
  $('#extendModal').find('#viewInvoice').hide();
  $('#extendModal').find('#InvoiceFilePath').val('');
  $('#extendModal').find('#invoicePaymentType').val('').trigger('change');
  $('#extendModal').find('#invoicePayRef').val('');
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
  $('#extendModal').find('#validatorInvoice').val('');
  $('#extendModal').find('#unitPrice').val('0.00');
  $('#extendModal').find('#certPrice').val('');
  $('#extendModal').find('#totalAmount').val("");
  $('#extendModal').find('#sst').val('');
  $('#extendModal').find('#subAmountSst').val('');
  $('#extendModal').find('#rebate').val(0);
  $('#extendModal').find('#rebateAmount').val('');
  $('#extendModal').find('#subAmount').val('');
  $('#extendModal').find('#labourCharge').val('0.00');
  $('#extendModal').find('#stampLabourCharge').val('');
  $('#extendModal').find('#roundUp').val('');
  $('#extendModal').find('#totalCharge').val('');

  $('#extendModal').find('#platformCountry').val('');
  $('#extendModal').find('#jenis_penunjuk').val('');
  $('#extendModal').find('#nilai1').val('');
  $('#extendModal').find('#nilai2').val('');
  $('#extendModal').find('#nilai3').val('');
  $('#extendModal').find('#nilai4').val('');
  $('#extendModal').find('#nilai5').val('');
  $('#extendModal').find('#nilai6').val('');

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

  $('#extendModal').find('#newRenew').on('change', function(){
    if($(this).val() == "NEW"){
      $('#validatorLamaView').hide();
      $('#daftarLamaView').hide();
      $('#sealLamaView').hide();
      $('#borangEView').hide();
      $('#borangEDateView').hide();
      $('#lastYearStampDateView').hide();
    }
    else{
      $('#validatorLamaView').show();
      $('#daftarLamaView').show();
      $('#sealLamaView').show();
      $('#borangEView').show();
      $('#borangEDateView').show();
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
            $('#reseller_branch').append('<option value="'+branchInfo.branchid+'">'+branchInfo.name+' - '+branchInfo.branch_address1+' '+branchInfo.branch_address2+' '+branchInfo.branch_address3+' '+branchInfo.branch_address4+' '+branchInfo.branch_address5+'</option>')
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
      $('#extendModal').find('#addr5').show();
      $('#extendModal').find('#contact').show();
      $('#extendModal').find('#email').show();
      $('#extendModal').find('#phone').show();
      $('#extendModal').find('#pic').show();

      $('#extendModal').find('#address1').val('');
      $('#extendModal').find('#address2').val('');
      $('#extendModal').find('#address3').val('');
      $('#extendModal').find('#address4').val('');
      $('#extendModal').find('#address5').val('');
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
      $('#extendModal').find('#addr5').hide();
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
          $('#extendModal').find('#address5').val(obj.message.address5);
          
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

        for(var i=0; i<obj.message.pricing.length; i++){
          var branchInfo = obj.message.pricing[i];
          $('#branch').append('<option value="'+branchInfo.branchid+'">'+branchInfo.name+' - '+branchInfo.address1+' '+branchInfo.address2+' '+branchInfo.address3+' '+branchInfo.address4+' '+branchInfo.address5+'</option>')
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
            $('#jenisAlat').append('<option value="'+modelInfo.id+'" data-name="'+modelInfo.jenis_alat+'">'+modelInfo.jenis_alat+'</option>');
            lastId = modelInfo.id;
          }

          $('#jenisAlat').val(lastId).trigger('change');

          // $('#extendModal').trigger('jaIsLoaded');
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
    $('#sst').val((totalAmt * 0.08).toFixed(2));
    $('#subAmount').val((totalAmt + (totalAmt * 0.08)).toFixed(2));
  });

  $('#extendModal').find('#unitPrice').on('change', function(){
    var price = parseFloat($(this).val());
    var alat = $('#jenisAlat').val();
    var includeCert = $('#includeCert').val();

    if (alat == 26){
      var certPrice = 57.0;
    }else{
      var certPrice = 28.5;
    }

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
    $('#subAmountSst').val((totalAmt + (totalAmt * 0.08)).toFixed(2));

    // Rebate calculation (enhancement)
    var rebate = parseFloat($('#rebate').val())/100 || 0;
    var subAmountSst = parseFloat($('#subAmountSst').val()) || 0;
    var rebateAmount = subAmountSst * rebate;
    $('#rebateAmount').val(rebateAmount.toFixed(2));
    var subTotalAmount = subAmountSst - rebateAmount;
    $('#subAmount').val(subTotalAmount.toFixed(2));
  });

  $('#extendModal').find('#includeCert').on('change', function(){
    var includeCert = $(this).val();

    if (includeCert == 'YES'){
      $('#certNoView').show();
    }else{
      $('#certNoView').hide();
    }

    // changed code to pull instead of taking from product field
    if($('#machineType').val() && $('#jenisAlat').val() && $('#capacity').val() && $('#validator').val()){
      $.post('php/getProductsCriteria.php', {machineType: $('#machineType').val(), jenisAlat: $('#jenisAlat').val(), capacity: $('#capacity').val(), validator: $('#validator').val()}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
          $('#unitPrice').val(obj.message.price);
          $('#unitPrice').trigger('change');

          if (!priceLoadedTriggered) {
            $('#extendModal').trigger('priceLoaded');
            priceLoadedTriggered = true;
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
    // var price = parseFloat($('#product').find(":selected").attr("data-price"));
    // var alat = $('#jenisAlat').val();
    // var includeCert = $(this).val();

    // if (alat == 26){
    //   var certPrice = 57.0;
    // }else{
    //   var certPrice = 28.5;
    // }

    // var sst = 0;
    // var totalAmt = price;

    // $('#unitPrice').val(price);

    // if(includeCert == 'YES'){
    //   $('#certPrice').val(certPrice);
    //   $('#cerId').show();
    //   totalAmt += certPrice;
    // }
    // else{
    //   $('#certPrice').val(0.00);
    //   $('#cerId').hide();
    // }

    // $('#totalAmount').val(totalAmt);
    // $('#sst').val((totalAmt * 0.08).toFixed(2));
    // $('#subAmount').val((totalAmt + (totalAmt * 0.08)).toFixed(2));
  });

  $('#extendModal').find('#rebate').on('change', function(){
    var rebate = parseFloat($(this).val())/100 || 0;
    var subAmountSst = parseFloat($('#subAmountSst').val()) || 0;
    var rebateAmount = subAmountSst * rebate;
    $('#rebateAmount').val(rebateAmount.toFixed(2));
    var subTotalAmount = subAmountSst - rebateAmount;
    $('#subAmount').val(subTotalAmount.toFixed(2));
  });

  $('#extendModal').find('#labourCharge').on('change', function(){
    var labourCharge = parseFloat($(this).val());
    var subTotalAmt = parseFloat($('#subAmount').val());
    var stampLabourCharge = labourCharge + subTotalAmt;

    $('#stampLabourCharge').val(stampLabourCharge.toFixed(2));

    if ($('#roundUp').val().trim() !== '') {
      $('#roundUp').trigger('change');
    }
  });

  $('#extendModal').find('#roundUp').on('change', function(){
    var roundUp = parseFloat($(this).val());
    var stampLabourCharge = parseFloat($('#stampLabourCharge').val());
    var totalCharges = stampLabourCharge + roundUp;

    $('#totalCharge').val(totalCharges.toFixed(2));
  });

  // $('#extendModal').find('#machineType').on('change', function(){
  //   if($('#machineType').val() && $('#jenisAlat').val() && $('#capacity').val() && $('#validator').val()){
  //     $.post('php/getProductsCriteria.php', {machineType: $('#machineType').val(), jenisAlat: $('#jenisAlat').val(), capacity: $('#capacity').val(), validator: $('#validator').val()}, function(data){
  //       var obj = JSON.parse(data);
        
  //       if(obj.status === 'success'){
  //         $('#product').val(obj.message.id);
  //         $('#unitPrice').val(obj.message.price);
  //         $('#unitPrice').trigger('change');

  //         // 🔥 Ensure `priceLoaded` is triggered only ONCE per edit session
  //         // if (!priceLoadedTriggered) {
  //         //   $('#extendModal').trigger('priceLoaded');
  //         //   priceLoadedTriggered = true; // ✅ Prevents re-triggering
  //         // }
  //       }
  //       else if(obj.status === 'failed'){
  //         toastr["error"](obj.message, "Failed:");
  //       }
  //       else{
  //         toastr["error"]("Something wrong when pull data", "Failed:");
  //       }
  //       //$('#spinnerLoading').hide();
  //     });
  //   }
  // });

  
  $('#extendModal').find('#jenisAlat').on('change', function(){
    alat = $(this).val();
    jalat = $(this).val();
    alatId = $(this).val();
    $('#jenisAlatName').val($(this).find(':selected').data('name'));
    jenisAlatName = $('#jenisAlatName').val();
    $('#addtionalSection').html('');

    if($('#machineType').val() && $('#jenisAlat').val() && $('#capacity').val() && $('#validator').val()){
      $.post('php/getProductsCriteria.php', {machineType: $('#machineType').val(), jenisAlat: $('#jenisAlat').val(), capacity: $('#capacity').val(), validator: $('#validator').val()}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
          $('#product').val(obj.message.id);
          $('#unitPrice').val(obj.message.price);
          $('#unitPrice').trigger('change');

          // 🔥 Ensure `priceLoaded` is triggered only ONCE per edit session
          //if (!priceLoadedTriggered) {
          //  $('#extendModal').trigger('priceLoaded');
          //  priceLoadedTriggered = true; // ✅ Prevents re-triggering
          // }
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

    if(($('#validator').val() == '10' || $('#validator').val() == '9') && jenisAlatName.includes("ATK")){
      $('#addtionalSection').html($('#atkDetails').html());
      loadCellCount = 0;
      $("#loadCellTable").html('');
      $('#extendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });

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

          $('#extendModal').trigger('sizeLoaded');
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
    else if(($(this).val() == '10' || $(this).val() == '9') && jenisAlatName.includes("ATP (MOTORCAR)")){
      $('#addtionalSection').html($('#atpMotorDetails').html());
      $('#extendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && jenisAlatName.includes("ATP-AUTO MACHINE")){
      $('#addtionalSection').html($('#autoPackDetails').html());
      $('#extendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && jenisAlatName.includes("ATP")){
      $('#addtionalSection').html($('#atpDetails').html());
      $('#extendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && jenisAlatName.includes("ATN")){
      $('#addtionalSection').html($('#atnDetails').html());
      $('#extendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && jenisAlatName.includes("ATE")){
      $('#addtionalSection').html($('#ateDetails').html());
      $('#extendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && jenisAlatName.includes("SLL")){
      $('#addtionalSection').html($('#sllDetails').html());
      $('#extendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && jenisAlatName.includes("BTU - (BOX)")){
      $('#addtionalSection').html($('#btuBoxDetails').html());
      btuCount = 0;
      $("#btuTable").html('');
      $('#extendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && jenisAlatName.includes("BTU")){
      $('#addtionalSection').html($('#btuDetails').html());
      $('#extendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    // else if(($('#validator').val() == '10' || $('#validator').val() == '9') && alat == '17'){
    //   $('#addtionalSection').html($('#atsHDetails').html());
    //   $('#extendModal').trigger('atkLoaded');
    // }
    else if(($(this).val() == '10' || $(this).val() == '9') && jenisAlatName.includes("SIA")){
      $('#addtionalSection').html($('#siaDetails').html());
      $('#extendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && jenisAlatName.includes("BAP")){
      $('#addtionalSection').html($('#bapDetails').html());
      $('#extendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && jenisAlatName.includes("SIC")){
      $('#addtionalSection').html($('#sicDetails').html());
      $('#extendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
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

          // 🔥 Ensure `priceLoaded` is triggered only ONCE per edit session
          if (!priceLoadedTriggered) {
            $('#extendModal').trigger('priceLoaded');
            priceLoadedTriggered = true; // ✅ Prevents re-triggering
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

  $('#extendModal').find('#validator').on('change', function(){
    var jenisAlatName = $('#jenisAlatName').val()
    if($('#machineType').val() && $('#jenisAlat').val() && $('#capacity').val() && $('#validator').val()){
      $.post('php/getProductsCriteria.php', {machineType: $('#machineType').val(), jenisAlat: $('#jenisAlat').val(), capacity: $('#capacity').val(), validator: $('#validator').val()}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
          $('#product').val(obj.message.id);
          $('#unitPrice').val(obj.message.price);
          $('#unitPrice').trigger('change');

          // 🔥 Ensure `priceLoaded` is triggered only ONCE per edit session
          // if (!priceLoadedTriggered) {
          //   $('#extendModal').trigger('priceLoaded');
          //   priceLoadedTriggered = true; // ✅ Prevents re-triggering
          // }
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

    if(($(this).val() == '10' || $(this).val() == '9') && jenisAlatName.includes("ATK")){
      var alatId = $('#jenisAlat').val();

      $('#addtionalSection').html($('#atkDetails').html());
      loadCellCount = 0;
      $("#loadCellTable").html('');
      $('#extendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });

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

          $('#extendModal').trigger('sizeLoaded');
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
    else if(($(this).val() == '10' || $(this).val() == '9') && jenisAlatName.includes("ATP (MOTORCAR)")){
      $('#addtionalSection').html($('#atpMotorDetails').html());
      $('#extendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && jenisAlatName.includes("ATP-AUTO MACHINE")){
      $('#addtionalSection').html($('#autoPackDetails').html());
      $('#extendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && jenisAlatName.includes("ATP")){
      $('#addtionalSection').html($('#atpDetails').html());
      $('#extendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && jenisAlatName.includes("ATN")){
      $('#addtionalSection').html($('#atnDetails').html());
      $('#extendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && jenisAlatName.includes("ATE")){
      $('#addtionalSection').html($('#ateDetails').html());
      $('#extendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && jenisAlatName.includes("SLL")){
      $('#addtionalSection').html($('#sllDetails').html());
      $('#extendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && jenisAlatName.includes("BTU - (BOX)")){
      $('#addtionalSection').html($('#btuBoxDetails').html());
      btuCount = 0;
      $("#btuTable").html('');
      $('#extendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && jenisAlatName.includes("BTU")){
      $('#addtionalSection').html($('#btuDetails').html());
      $('#extendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    // else if(($(this).val() == '10' || $(this).val() == '9') && $('#jenisAlat').val() == '17'){
    //   $('#addtionalSection').html($('#atsHDetails').html());
    //   $('#extendModal').trigger('atkLoaded');
    // }
    else if(($(this).val() == '10' || $(this).val() == '9') && jenisAlatName.includes("SIA")){
      $('#addtionalSection').html($('#siaDetails').html());
      $('#extendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && jenisAlatName.includes("BAP")){
      $('#addtionalSection').html($('#bapDetails').html());
      $('#extendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && jenisAlatName.includes("SIC")){
      $('#addtionalSection').html($('#sicDetails').html());
      $('#extendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else{
      $('#addtionalSection').html('');
    }
  });

  $('#extendModal').find('#ownershipStatus').on('change', function(){
    var ownershipStatus = $(this).val();

    if (ownershipStatus == 'RENT'){
      $('#extendModal').find('#rentalAttachment').show();
    }else{
      $('#extendModal').find('#rentalAttachment').hide();
    }
  });

  $('#extendModal').find('#notificationPeriod').on('change', function(){
    var notificationPeriod = $(this).val();
    if (notificationPeriod > 6){
        alert("Maximum notification period is 6.");
        $(this).val(6); // reset to 6
    }
  });
</script>
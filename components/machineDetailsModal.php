<?php
?>

<div class="modal fade" id="machineInfoExtendModal">
  <div class="modal-dialog modal-xl" style="max-width: 90%;">
    <div class="modal-content">
      <form role="form" id="machineInfoExtendForm" enctype="multipart/form-data">
        <div class="modal-header bg-gray-dark color-palette">
          <h4 class="modal-title">Stamping Forms</h4>
          <button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body" >
          <input type="hidden" class="form-control" id="id" name="id">
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
        </div>
        
        <div id="addtionalSection"></div>
        
        <div class="modal-footer justify-content-between bg-gray-dark color-palette">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="saveButton">Save</button>
        </div>
      </form>
    </div> <!-- /.modal-content -->
  </div> <!-- /.modal-dialog -->
</div> <!-- /.modal -->


<script>
function newMachineInfoEntry(id){
  var date = new Date();
  $('#capacityHigh').hide();
  $('#machineInfoExtendModal').find('#id').val(id);

  $.post('php/getStamp.php', {userID: id}, function(data){
    var obj = JSON.parse(data);
    
    if(obj.status === 'success'){
      $('#machineInfoExtendModal').find('#brand').val(obj.message.brand).select2('destroy').select2();
      $('#machineInfoExtendModal').find('#product').val(obj.message.products);
      $('#machineInfoExtendModal').find('#machineType').val(obj.message.machine_type).select2('destroy').select2();
      $('#machineInfoExtendModal').find('#jenisAlat').val(obj.message.jenis_alat).select2('destroy').select2();
      $('#machineInfoExtendModal').find('#machineLocation').val(obj.message.machine_location);

      $('#machineInfoExtendModal').find('#machineName').val(obj.message.machine_name).trigger('change');
      $('#machineInfoExtendModal').find('#machineArea').val(obj.message.machine_area);
      $('#machineInfoExtendModal').find('#machineSerialNo').val(obj.message.machine_serial_no);
      $('#machineInfoExtendModal').find('#model').val(obj.message.model).trigger('change');
      $('#machineInfoExtendModal').find('#makeIn').val(obj.message.make_in).trigger('change');

      if(obj.message.capacity_range == 'MULTI'){
          $('#machineInfoExtendModal').find('#toggleMultiRange').prop('checked', true).trigger('change');
          $('#machineInfoExtendModal').find('#capacity_multi').val(obj.message.capacity).trigger('change');
      }else{
          $('#machineInfoExtendModal').find('#toggleMultiRange').prop('checked', false).trigger('change');
          $('#machineInfoExtendModal').find('#capacity_single').val(obj.message.capacity).trigger('change');
      }
      $('#machineInfoExtendModal').find('#ownershipStatus').val(obj.message.ownership_status).trigger('change');
      $('#machineInfoExtendModal').find('#trade').val(obj.message.trade).trigger('change');
      $('#machineInfoExtendModal').find('#serial').val(obj.message.serial_no);

      $('#machineInfoExtendModal').find('#penentusanBaru').val(obj.message.penentusan_baru);
      $('#machineInfoExtendModal').find('#penentusanSemula').val(obj.message.penentusan_semula);

      $('#machineInfoExtendModal').find('#kelulusanMSPK').val(obj.message.kelulusan_mspk);
      $('#machineInfoExtendModal').find('#noMSPK').val(obj.message.no_kelulusan);
      $('#machineInfoExtendModal').find('#platformCountry').val(obj.message.platform_country);
      $('#machineInfoExtendModal').find('#platformType').val(obj.message.platform_type);
      $('#machineInfoExtendModal').find('#size').val(obj.message.size);
      $('#machineInfoExtendModal').find('#jenisPelantar').val(obj.message.jenis_pelantar);
      $('#machineInfoExtendModal').find('#others').val(obj.message.other_info);
      $('#machineInfoExtendModal').find('#platformCountry').val(obj.message.platform_country);
      $('#machineInfoExtendModal').find('#jenis_penunjuk').val(obj.message.jenis_penunjuk).trigger('change');
      $('#machineInfoExtendModal').find('#nilai1').val(obj.message.nilais[0].nilai);
      $('#machineInfoExtendModal').find('#nilai2').val(obj.message.nilais[1].nilai);
      $('#machineInfoExtendModal').find('#nilai3').val(obj.message.nilais[2].nilai);
      $('#machineInfoExtendModal').find('#nilai4').val(obj.message.nilais[3].nilai);
      $('#machineInfoExtendModal').find('#nilai5').val(obj.message.nilais[4].nilai);
      $('#machineInfoExtendModal').find('#nilai6').val(obj.message.nilais[5].nilai);
    }
    else if(obj.status === 'failed'){
      toastr["error"](obj.message, "Failed:");
    }
    else{
      toastr["error"]("Something wrong when pull data", "Failed:");
    }
    $('#spinnerLoading').hide();
  });

  $('#machineInfoExtendModal').find('#uploadRentalAttachment').val('');
 
  //Additonal field reset
  // var value = $('#machineInfoExtendModal').find('#additionalSection').find('#batuUjian').val();
  // $('#machineInfoExtendModal').find('#additionalSection').find('#jenis_penunjuk').val('').trigger('change');

  $('#machineInfoExtendModal').find('#jenisAlat').change(function() {
    if($(this).val() == 1) {
        $('#machineInfoExtendModal').find('#capacityHigh').show();
    } else {
        $('#machineInfoExtendModal').find('#capacityHigh').hide();
    }
  });

  $('#machineInfoExtendModal').on('atkLoaded', function() {
    $('#machineInfoExtendModal').find('#batuUjian').on('change', function(){
      var batuUjian = $(this).val();
      if (batuUjian == 'OTHER'){
        $('#machineInfoExtendModal').find('#batuUjianLainDisplay').show();
      }else{
        $('#machineInfoExtendModal').find('#batuUjianLainDisplay').hide();
      }
    });

    $('#machineInfoExtendModal').find('#nilaiJangka').on('change', function(){
      var nilaiJangka = $(this).val();
      if (nilaiJangka == 'OTHER'){
        $('#machineInfoExtendModal').find('#nilaiJangkaOtherDisplay').show();
      }else{
        $('#machineInfoExtendModal').find('#nilaiJangkaOtherDisplay').hide();
      }
    });

    $('#machineInfoExtendModal').find('#diperbuatDaripada').on('change', function(){
      var diperbuatDaripada = $(this).val();
      if (diperbuatDaripada == 'OTHER'){
        $('#machineInfoExtendModal').find('#diperbuatDaripadaOtherDisplay').show();
      }else{
        $('#machineInfoExtendModal').find('#diperbuatDaripadaOtherDisplay').hide();
      }
    });

    $('#machineInfoExtendModal').find('#jenama').on('change', function(){
      var jenama = $(this).val();
      if (jenama == 'OTHER'){
        $('#machineInfoExtendModal').find('#jenamaOtherDisplay').show();
      }else{
        $('#machineInfoExtendModal').find('#jenamaOtherDisplay').hide();
      }
    });
  });

  customer = 0;
  branch = 0;
  $('#pricingTable').html('');
  pricingCount = 0;

  $('#cerId').hide();

  $('#machineInfoExtendModal').modal('show');

  $('#machineInfoExtendForm').validate({
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

  $('#machineInfoExtendModal').find('#brand').on('change', function(){
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

          $('#machineInfoExtendModal').trigger('modelsLoaded');
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

  $('#machineInfoExtendModal').find('#machineType').on('change', function(){
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

          // $('#machineInfoExtendModal').trigger('jaIsLoaded');
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

  $('#machineInfoExtendModal').find('#product').on('change', function(){
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

  $('#machineInfoExtendModal').find('#jenisAlat').on('change', function(){
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
          //  $('#machineInfoExtendModal').trigger('priceLoaded');
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

    if(jenisAlatName.includes("ATK")){
      $('#addtionalSection').html($('#atkDetails').html());
      loadCellCount = 0;
      $("#loadCellTable").html('');
      $('#machineInfoExtendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });

      type = $('#machineInfoExtendModal').find('#type').val();
      if(type == 'RESELLER'){
        $('#machineInfoExtendModal').find('#penentusanSemula').attr('required', true);
      }

      $.post('php/getSizeFromJA.php', {jenisAlat: alatId}, function(data){
        var obj = JSON.parse(data);

        if(obj.status === 'success'){
          $('#size').html('');

          for(var i=0; i<obj.message.length; i++){
            var size = obj.message[i];
            $('#size').append('<option value="'+size.id+'">'+size.size+'</option>')
          }

          $('#machineInfoExtendModal').trigger('sizeLoaded');
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
    //   $('#machineInfoExtendModal').trigger('atkLoaded');
    // }
    else if(jenisAlatName.includes("ATP (MOTORCAR)")){
      $('#addtionalSection').html($('#atpMotorDetails').html());
      $('#machineInfoExtendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(jenisAlatName.includes("ATP-AUTO MACHINE")){
      $('#addtionalSection').html($('#autoPackDetails').html());
      $('#machineInfoExtendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(jenisAlatName.includes("ATP")){
      $('#addtionalSection').html($('#atpDetails').html());
      $('#machineInfoExtendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(jenisAlatName.includes("ATN")){
      $('#addtionalSection').html($('#atnDetails').html());
      $('#machineInfoExtendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(jenisAlatName.includes("ATE")){
      $('#addtionalSection').html($('#ateDetails').html());
      $('#machineInfoExtendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(jenisAlatName.includes("SLL")){
      $('#addtionalSection').html($('#sllDetails').html());
      $('#machineInfoExtendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(jenisAlatName.includes("BTU - (BOX)")){
      $('#addtionalSection').html($('#btuBoxDetails').html());
      btuCount = 0;
      $("#btuTable").html('');
      $('#machineInfoExtendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(jenisAlatName.includes("BTU")){
      $('#addtionalSection').html($('#btuDetails').html());
      $('#machineInfoExtendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    // else if(($('#validator').val() == '10' || $('#validator').val() == '9') && alat == '17'){
    //   $('#addtionalSection').html($('#atsHDetails').html());
    //   $('#machineInfoExtendModal').trigger('atkLoaded');
    // }
    else if(jenisAlatName.includes("SIA")){
      $('#addtionalSection').html($('#siaDetails').html());
      $('#machineInfoExtendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(jenisAlatName.includes("BAP")){
      $('#addtionalSection').html($('#bapDetails').html());
      $('#machineInfoExtendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(jenisAlatName.includes("SIC")){
      $('#addtionalSection').html($('#sicDetails').html());
      $('#machineInfoExtendModal').trigger('atkLoaded');
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

  $('#machineInfoExtendModal').find('#toggleMultiRange').on('change', function() {
    if ($('#machineInfoExtendModal').find('#toggleMultiRange').is(':checked')) {
      $('#machineInfoExtendModal').find('#capacityMulti').val('').show();
      $('#machineInfoExtendModal').find('#capacitySingle').val('').hide();
    }else{
      $('#machineInfoExtendModal').find('#capacityMulti').val('').hide();
      $('#machineInfoExtendModal').find('#capacitySingle').val('').show();
    }
  });

  $('#machineInfoExtendModal').find('#capacity_single').on('change', function(){
    capacityId = $(this).val();
    $('#machineInfoExtendModal').find('#capacity').val(capacityId);
  });

  $('#machineInfoExtendModal').find('#capacity_multi').on('change', function(){
    capacityId = $(this).val();
    $('#machineInfoExtendModal').find('#capacity').val(capacityId);
  });

  $('#machineInfoExtendModal').find('#capacity').on('change', function(){
    if($('#machineType').val() && $('#jenisAlat').val() && $('#capacity').val() && $('#validator').val()){
      $.post('php/getProductsCriteria.php', {machineType: $('#machineType').val(), jenisAlat: $('#jenisAlat').val(), capacity: $('#capacity').val(), validator: $('#validator').val()}, function(data){
        var obj = JSON.parse(data);

        if(obj.status === 'success'){
          $('#product').val(obj.message.id);
          $('#unitPrice').val(obj.message.price);
          $('#unitPrice').trigger('change');

          // 🔥 Ensure `priceLoaded` is triggered only ONCE per edit session
          if (!priceLoadedTriggered) {
            $('#machineInfoExtendModal').trigger('priceLoaded');
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

  $('#machineInfoExtendModal').find('#ownershipStatus').on('change', function(){
    var ownershipStatus = $(this).val();

    if (ownershipStatus == 'RENT'){
      $('#machineInfoExtendModal').find('#rentalAttachment').show();
    }else{
      $('#machineInfoExtendModal').find('#rentalAttachment').hide();
    }
  });

  // $('#machineInfoExtendModal').find('#machineType').on('change', function(){
  //   if($('#machineType').val() && $('#jenisAlat').val() && $('#capacity').val() && $('#validator').val()){
  //     $.post('php/getProductsCriteria.php', {machineType: $('#machineType').val(), jenisAlat: $('#jenisAlat').val(), capacity: $('#capacity').val(), validator: $('#validator').val()}, function(data){
  //       var obj = JSON.parse(data);
        
  //       if(obj.status === 'success'){
  //         $('#product').val(obj.message.id);
  //         $('#unitPrice').val(obj.message.price);
  //         $('#unitPrice').trigger('change');

  //         // 🔥 Ensure `priceLoaded` is triggered only ONCE per edit session
  //         // if (!priceLoadedTriggered) {
  //         //   $('#machineInfoExtendModal').trigger('priceLoaded');
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
</script>

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
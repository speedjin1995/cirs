<?php
?>

<div class="modal fade" id="stampingInfoExtendModal">
  <div class="modal-dialog modal-xl" style="max-width: 90%;">
    <div class="modal-content">
      <form role="form" id="stampingInfoExtendForm" enctype="multipart/form-data">
        <div class="modal-header bg-gray-dark color-palette">
          <h4 class="modal-title">Stamping Forms</h4>
          <button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body" >
          <input type="hidden" class="form-control" id="id" name="id">
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
                <input type="hidden" id="machine_type" name="machine_type"/>
                <input type="hidden" id="jenis_alat" name="jenis_alat"/>
                <input type="hidden" id="capacity" name="capacity"/>
              </div>
            </div>
          </div>
          <!--Stamping Details---> 
        </div>

        <div class="modal-footer justify-content-between bg-gray-dark color-palette">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="saveButton">Save</button>
        </div>
      </form>
    </div> <!-- /.modal-content -->
  </div> <!-- /.modal-dialog -->
</div> <!-- /.modal -->

<script>
function newStampingInfoEntry(id){
  var date = new Date();
  $('#capacityHigh').hide();
  $('#stampingInfoExtendModal').find('#id').val(id);

  $.post('php/getStamp.php', {userID: id}, function(data){
    var obj = JSON.parse(data);
    
    if(obj.status === 'success'){
        $('#stampingInfoExtendModal').find('#machine_type').val(obj.message.machine_type);
        $('#stampingInfoExtendModal').find('#jenis_alat').val(obj.message.jenis_alat);
        $('#stampingInfoExtendModal').find('#validator').val(obj.message.validate_by).select2('destroy').select2();
        $('#stampingInfoExtendModal').find('#capacity').val(obj.message.capacity);

        $('#stampingInfoExtendModal').find('#validatorlama').val(obj.message.validator_lama).select2('destroy').select2();
        $('#stampingInfoExtendModal').find('#noDaftarLama').val(obj.message.no_daftar_lama);
        $('#stampingInfoExtendModal').find('#noDaftarBaru').val(obj.message.no_daftar_baru);
        $('#stampingInfoExtendModal').find('#sealNoLama').val(obj.message.seal_no_lama);
        $('#stampingInfoExtendModal').find('#sealNoBaru').val(obj.message.seal_no_baru);
        $('#stampingInfoExtendModal').find('#pegawaiContact').val(obj.message.pegawai_contact);
        $('#stampingInfoExtendModal').find('#newRenew').val(obj.message.stampType).trigger('change');
        $('#stampingInfoExtendModal').find('#certNo').val(obj.message.cert_no);
        $('#stampingInfoExtendModal').find('#pinKeselamatan').val(obj.message.pin_keselamatan);
        $('#stampingInfoExtendModal').find('#siriKeselamatan').val(obj.message.siri_keselamatan);
        $('#stampingInfoExtendModal').find('#borangD').val(obj.message.borang_d);
        $('#stampingInfoExtendModal').find('#borangE').val(obj.message.borang_e);
        $('#stampingInfoExtendModal').find('#borangEDate').val(formatDate3(obj.message.borang_e_date));
        $('#stampingInfoExtendModal').find('#dueDate').val(formatDate3(obj.message.due_date));
        $('#stampingInfoExtendModal').find('#includeCert').val(obj.message.include_cert).trigger('change');

    }
    else if(obj.status === 'failed'){
      toastr["error"](obj.message, "Failed:");
    }
    else{
      toastr["error"]("Something wrong when pull data", "Failed:");
    }
    $('#spinnerLoading').hide();
  });
 
  $('#pricingTable').html('');
  pricingCount = 0;

  $('#cerId').hide();

  $('#stampingInfoExtendModal').modal('show');

  $('#stampingInfoExtendForm').validate({
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

  $('#stampingInfoExtendModal').find('#newRenew').on('change', function(){
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

  $('#stampingInfoExtendModal').find('#stampDate').on('blur', function (e) {
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
      $('#stampingInfoExtendModal').find('#dueDate').val(formatDate3(date));
    }
  });

  /*$('#stampingInfoExtendModal').find('#validator').on('change', function(){
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
          //   $('#stampingInfoExtendModal').trigger('priceLoaded');
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
      $('#stampingInfoExtendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });

      type = $('#stampingInfoExtendModal').find('#type').val();
      if(type == 'RESELLER'){
        $('#stampingInfoExtendModal').find('#penentusanSemula').attr('required', true);
      }

      $.post('php/getSizeFromJA.php', {jenisAlat: alatId}, function(data){
        var obj = JSON.parse(data);

        if(obj.status === 'success'){
          $('#size').html('');

          for(var i=0; i<obj.message.length; i++){
            var size = obj.message[i]; 
            $('#size').append('<option value="'+size.id+'">'+size.size+'</option>')
          }

          $('#stampingInfoExtendModal').trigger('sizeLoaded');
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
    //   $('#stampingInfoExtendModal').trigger('atkLoaded');
    // }
    else if(($(this).val() == '10' || $(this).val() == '9') && jenisAlatName.includes("ATP (MOTORCAR)")){
      $('#addtionalSection').html($('#atpMotorDetails').html());
      $('#stampingInfoExtendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && jenisAlatName.includes("ATP-AUTO MACHINE")){
      $('#addtionalSection').html($('#autoPackDetails').html());
      $('#stampingInfoExtendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && jenisAlatName.includes("ATP")){
      $('#addtionalSection').html($('#atpDetails').html());
      $('#stampingInfoExtendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && jenisAlatName.includes("ATN")){
      $('#addtionalSection').html($('#atnDetails').html());
      $('#stampingInfoExtendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && jenisAlatName.includes("ATE")){
      $('#addtionalSection').html($('#ateDetails').html());
      $('#stampingInfoExtendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && jenisAlatName.includes("SLL")){
      $('#addtionalSection').html($('#sllDetails').html());
      $('#stampingInfoExtendModal').trigger('atkLoaded');
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
      $('#stampingInfoExtendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && jenisAlatName.includes("BTU")){
      $('#addtionalSection').html($('#btuDetails').html());
      $('#stampingInfoExtendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    // else if(($(this).val() == '10' || $(this).val() == '9') && $('#jenisAlat').val() == '17'){
    //   $('#addtionalSection').html($('#atsHDetails').html());
    //   $('#stampingInfoExtendModal').trigger('atkLoaded');
    // }
    else if(($(this).val() == '10' || $(this).val() == '9') && jenisAlatName.includes("SIA")){
      $('#addtionalSection').html($('#siaDetails').html());
      $('#stampingInfoExtendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && jenisAlatName.includes("BAP")){
      $('#addtionalSection').html($('#bapDetails').html());
      $('#stampingInfoExtendModal').trigger('atkLoaded');
      $('#addtionalSection').find('.select2').select2({
        allowClear: true,
        placeholder: "Please Select",
        dropdownParent: $('#addtionalSection'),
        width: '100%'
      });
    }
    else if(($(this).val() == '10' || $(this).val() == '9') && jenisAlatName.includes("SIC")){
      $('#addtionalSection').html($('#sicDetails').html());
      $('#stampingInfoExtendModal').trigger('atkLoaded');
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
  });*/

</script>
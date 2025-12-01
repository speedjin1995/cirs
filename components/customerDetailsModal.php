<?php
?>

<div class="modal fade" id="customerInfoExtendModal">
  <div class="modal-dialog modal-xl" style="max-width: 90%;">
    <div class="modal-content">
      <form role="form" id="customerInfoExtendForm" enctype="multipart/form-data">
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
function newCustomerInfoEntry(id){
  var date = new Date();
  
  // Ensure id is an integer; if it's not a valid number, default to 0.
  id = parseInt(id, 10);
  if (isNaN(id)) id = 0;
  $('#capacityHigh').hide();

  if(id > 0){
    console.log("New Customer Info Entry Called", id);
    $.post('php/getStamp.php', {userID: id}, function(data){
    var obj = JSON.parse(data);
    
    if(obj.status === 'success'){
        $('#customerInfoExtendModal').find('#id').val(obj.message.id);
        $('#customerInfoExtendModal').find('#type').val(obj.message.type).trigger('change');
        $('#customerInfoExtendModal').find('#customerType').val(obj.message.customer_type).attr('disabled', false).trigger('change');
        $('#customerInfoExtendModal').find('#customerTypeEdit').val(obj.message.customer_type);
        $('#customerInfoExtendModal').find('#companyBranch').val(obj.message.company_branch).trigger('change');
        $('#customerInfoExtendModal').find('#company').val(obj.message.customers).trigger('change');
        $('#customerInfoExtendModal').find('#companyText').val('');
        $('#customerInfoExtendModal').find('#address1').val(obj.message.address1);
        $('#customerInfoExtendModal').find('#address2').val(obj.message.address2);
        $('#customerInfoExtendModal').find('#address3').val(obj.message.address3);
        $('#customerInfoExtendModal').find('#address4').val(obj.message.address4);
        $('#customerInfoExtendModal').find('#address5').val(obj.message.address5);
        $('#customerInfoExtendModal').find('#address3').val(obj.message.address3);

        setTimeout(function(){
          $('#customerInfoExtendModal').find('#branch').val(obj.message.branch).trigger('change');
        }, 500);

        $('#customerInfoExtendModal').find('#pic').val(obj.message.pic);

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
  else{
    console.log("New Customer Info Entry Called2222", id);
  $('#customerInfoExtendModal').find('#customerInfoId').val("");
  $('#customerInfoExtendModal').find('#type').val("DIRECT");
  $('#customerInfoExtendModal').find('#companyBranch').val("<?=$branch ?>").trigger('change');
  $('#customerInfoExtendModal').find('#customerType').val("EXISTING").attr('disabled', false).trigger('change');
  $('#customerInfoExtendModal').find('#company').val('');
  $('#customerInfoExtendModal').find('#companyText').val('').trigger('change');
  $('#customerInfoExtendModal').find('#address1').val('');
  $('#customerInfoExtendModal').find('#address2').val('');
  $('#customerInfoExtendModal').find('#address3').val('');
  $('#customerInfoExtendModal').find('#address4').val('');
  $('#customerInfoExtendModal').find('#address5').val('');
  $('#customerInfoExtendModal').find('#notificationPeriod').val(1);
  $('#customerInfoExtendModal').find('#branch').val('').trigger('change');
  $('#customerInfoExtendModal').find('#pic').val("");  
  }

  $('#isResseller').hide();
  $('#isResseller2').hide();
  $('#isResseller3').hide();
  $('#isResseller4').hide();
  $('#isResseller5').hide();
  
  customer = 0;
  branch = 0;
  $('#pricingTable').html('');
  pricingCount = 0;

  $('#cerId').hide();

  $('#customerInfoExtendModal').modal('show');

  $('#customerInfoExtendForm').validate({
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

  $('#customerInfoExtendModal').find('#notificationPeriod').on('change', function(){
    var notificationPeriod = $(this).val();
    if (notificationPeriod > 6){
        alert("Maximum notification period is 6.");
        $(this).val(6); // reset to 6
    }
  });

  $('#customerInfoExtendModal').find('#type').on('change', function(){
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

  $('#customerInfoExtendModal').find('#dealer').on('change', function(){
    if($('#customerInfoExtendModal').find('#type').val() != 'DIRECT'){
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
          $('#customerInfoExtendModal').modal('show');

          $('#customerInfoExtendForm').validate({
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
          $('#customerInfoExtendModal').find('#customerType').val('EXISTING');
          $('#customerInfoExtendModal').find('#company').show();
          $('#customerInfoExtendModal').find('#company').parents('.form-group').find('.select2-container').show();
          $('#customerInfoExtendModal').find('#companyText').hide();
          $('#customerInfoExtendModal').find('#companyText').val('');
          for(var i=0; i<obj.message.length; i++){
            $('#company').append('<option value="'+obj.message[i].id+'">'+obj.message[i].name+'</option>')
          }

          if(customer != 0){
            $('#customerInfoExtendModal').find('#company').val(customer).trigger('change');
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

  $('#customerInfoExtendModal').find('#customerType').on('change', function(){
    if($(this).val() == "NEW"){
      $('#customerInfoExtendModal').find('#company').hide();
      $('#customerInfoExtendModal').find('#otherCodeView').show();
      $('#customerInfoExtendModal').find('#custbranch').hide();
      
      $('#customerInfoExtendModal').find('#addr1').show();
      $('#customerInfoExtendModal').find('#addr2').show();
      $('#customerInfoExtendModal').find('#addr3').show();
      $('#customerInfoExtendModal').find('#addr4').show();
      $('#customerInfoExtendModal').find('#addr5').show();
      $('#customerInfoExtendModal').find('#contact').show();
      $('#customerInfoExtendModal').find('#email').show();
      $('#customerInfoExtendModal').find('#phone').show();
      $('#customerInfoExtendModal').find('#pic').show();

      $('#customerInfoExtendModal').find('#address1').val('');
      $('#customerInfoExtendModal').find('#address2').val('');
      $('#customerInfoExtendModal').find('#address3').val('');
      $('#customerInfoExtendModal').find('#address4').val('');
      $('#customerInfoExtendModal').find('#address5').val('');
      $('#customerInfoExtendModal').find('#contact').val('');
      $('#customerInfoExtendModal').find('#email').val('');

      $('#customerInfoExtendModal').find('#company').parents('.form-group').find('.select2-container').hide();
      $('#customerInfoExtendModal').find('#companyText').show();
      $('#customerInfoExtendModal').find('#companyText').val('');
    }
    else{
      $('#customerInfoExtendModal').find('#company').html($('select#customerNoHidden').html());
      $('#customerInfoExtendModal').find('#company').show();
      $('#customerInfoExtendModal').find('#otherCodeView').hide();
      $('#customerInfoExtendModal').find('#custbranch').show();

      $('#customerInfoExtendModal').find('#addr1').hide();
      $('#customerInfoExtendModal').find('#addr2').hide();
      $('#customerInfoExtendModal').find('#addr3').hide();
      $('#customerInfoExtendModal').find('#addr4').hide();
      $('#customerInfoExtendModal').find('#addr5').hide();
      $('#customerInfoExtendModal').find('#contact').hide();
      $('#customerInfoExtendModal').find('#email').hide();
      $('#customerInfoExtendModal').find('#phone').hide();
      $('#customerInfoExtendModal').find('#pic').hide();

      $('#customerInfoExtendModal').find('#company').parents('.form-group').find('.select2-container').show();
      $('#customerInfoExtendModal').find('#companyText').hide();
      $('#customerInfoExtendModal').find('#companyText').val('');
    }
  });

  $('#customerInfoExtendModal').find('#branch').on('change', function(){
    //$('#spinnerLoading').show();
    var id = $(this).find(":selected").val();

    if (id){
      $.post('php/getBranch.php', {userID: id}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
          $('#customerInfoExtendModal').find('#address1').val(obj.message.address1);
          $('#customerInfoExtendModal').find('#address2').val(obj.message.address2);
          $('#customerInfoExtendModal').find('#address3').val(obj.message.address3);
          $('#customerInfoExtendModal').find('#address4').val(obj.message.address4);
          $('#customerInfoExtendModal').find('#address5').val(obj.message.address5);
          
          $('#customerInfoExtendModal').modal('show');

          $('#customerInfoExtendForm').validate({
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

  $('#customerInfoExtendModal').find('#company').on('change', function(){
    //$('#spinnerLoading').show();
    var id = $(this).find(":selected").val();

    $.post('php/getCustomer.php', {userID: id}, function(data){
      var obj = JSON.parse(data);
      
      if(obj.status === 'success'){
        $('#customerInfoExtendModal').find('#contact').val(obj.message.customer_phone);
        $('#customerInfoExtendModal').find('#email').val(obj.message.customer_email);

        $('#branch').html('');

        for(var i=0; i<obj.message.pricing.length; i++){
          var branchInfo = obj.message.pricing[i];
          $('#branch').append('<option value="'+branchInfo.branchid+'">'+branchInfo.name+' - '+branchInfo.address1+' '+branchInfo.address2+' '+branchInfo.address3+' '+branchInfo.address4+' '+branchInfo.address5+'</option>')
        }

        if(branch != 0){
            $('#customerInfoExtendModal').find('#branch').val(branch).trigger('change');
          }

        $('#customerInfoExtendModal').modal('show');

        $('#customerInfoExtendForm').validate({
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

</script>
<?php
?>

<div class="modal fade" id="feesInfoExtendModal">
  <div class="modal-dialog modal-xl" style="max-width: 90%;">
    <div class="modal-content">
      <form role="form" id="feeInfoExtendForm" enctype="multipart/form-data">
        <div class="modal-header bg-gray-dark color-palette">
          <h4 class="modal-title">Stamping Forms</h4>
          <button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body" >
          <input type="hidden" class="form-control" id="id" name="id">
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
function newFeesInfoEntry(id){
  var date = new Date();
  $('#capacityHigh').hide();
  $('#feesInfoExtendModal').find('#id').val(id);

  $('#pricingTable').html('');
  pricingCount = 0;
  $('#feesInfoExtendModal').find('#validatorInvoice').val('');
  $('#feesInfoExtendModal').find('#unitPrice').val('0.00');
  $('#feesInfoExtendModal').find('#certPrice').val('');
  $('#feesInfoExtendModal').find('#totalAmount').val("");
  $('#feesInfoExtendModal').find('#sst').val('');
  $('#feesInfoExtendModal').find('#subAmountSst').val('');
  $('#feesInfoExtendModal').find('#rebate').val(0);
  $('#feesInfoExtendModal').find('#rebateAmount').val('');
  $('#feesInfoExtendModal').find('#subAmount').val('');
  $('#feesInfoExtendModal').find('#labourCharge').val('0.00');
  $('#feesInfoExtendModal').find('#stampLabourCharge').val('');
  $('#feesInfoExtendModal').find('#roundUp').val('');
  $('#feesInfoExtendModal').find('#totalCharge').val('');

  $('#cerId').hide();

  $('#feesInfoExtendModal').modal('show');

  $('#feeInfoExtendForm').validate({
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

  $('#feesInfoExtendModal').find('#unitPrice').on('change', function(){
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

  $('#feesInfoExtendModal').find('#includeCert').on('change', function(){
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
            $('#feesInfoExtendModal').trigger('priceLoaded');
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

  $('#feesInfoExtendModal').find('#rebate').on('change', function(){
    var rebate = parseFloat($(this).val())/100 || 0;
    var subAmountSst = parseFloat($('#subAmountSst').val()) || 0;
    var rebateAmount = subAmountSst * rebate;
    $('#rebateAmount').val(rebateAmount.toFixed(2));
    var subTotalAmount = subAmountSst - rebateAmount;
    $('#subAmount').val(subTotalAmount.toFixed(2));
  });

  $('#feesInfoExtendModal').find('#labourCharge').on('change', function(){
    var labourCharge = parseFloat($(this).val());
    var subTotalAmt = parseFloat($('#subAmount').val());
    var stampLabourCharge = labourCharge + subTotalAmt;

    $('#stampLabourCharge').val(stampLabourCharge.toFixed(2));

    if ($('#roundUp').val().trim() !== '') {
      $('#roundUp').trigger('change');
    }
  });

  $('#feesInfoExtendModal').find('#roundUp').on('change', function(){
    var roundUp = parseFloat($(this).val());
    var stampLabourCharge = parseFloat($('#stampLabourCharge').val());
    var totalCharges = stampLabourCharge + roundUp;

    $('#totalCharge').val(totalCharges.toFixed(2));
  });
</script>
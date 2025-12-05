<?php
?>

<div class="modal fade" id="invoiceInfoExtendModal">
  <div class="modal-dialog modal-xl" style="max-width: 90%;">
    <div class="modal-content">
      <form role="form" id="invoiceInfoExtendForm" enctype="multipart/form-data">
        <div class="modal-header bg-gray-dark color-palette">
          <h4 class="modal-title">Stamping Forms</h4>
          <button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body" >
          <input type="hidden" class="form-control" id="id" name="id">
          <div class="card card-primary">
            <div class="card-body">
              <div class="row">
                <h4>Invoice Information</h4>
              </div>
              <div class="row">
                <!--Invoice Details--->
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
                <div class="col-4">
                  <div class="form-group">
                    <label>Invoice Date</label>
                    <div class='input-group date' id="datePicker2" data-target-input="nearest">
                      <input type='text' class="form-control datetimepicker-input" data-target="#datePicker2" id="invoiceDate" name="invoiceDate"/>
                      <div class="input-group-append" data-target="#datePicker2" data-toggle="datetimepicker">
                        <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-4">
                  <div class="form-group">
                    <label>Upload PO Attachment</label>
                    <div class="d-flex">
                      <div class="col-10">
                        <input type="file" class="form-control" id="uploadPOAttachment" name="uploadPOAttachment">
                      </div>
                      <div class="col-2 mt-1">
                        <a href="" id="viewPO" name="viewPO" target="_blank" class="btn btn-success btn-sm" role="button" style="display: none;"><i class="fa fa-file-pdf-o"></i></a>
                      </div>
                    </div>
                    <input type="text" id="POFilePath" name="POFilePath" style="display:none">           
                  </div>
                </div>
                <!--Invoice Details--->
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

<script>
function newInvoiceInfoEntry(id){
  var date = new Date();
  $('#capacityHigh').hide();
  $('#invoiceInfoExtendModal').find('#id').val(id);

  $.post('php/getStamp.php', {userID: id}, function(data){
    var obj = JSON.parse(data);
    
    if(obj.status === 'success'){
      $('#invoiceInfoExtendModal').find('#invoice').val(obj.message.invoice_no);
      $('#invoiceInfoExtendModal').find('#invoicePaymentType').val(obj.message.invoice_payment_type).trigger('change');
      $('#invoiceInfoExtendModal').find('#invoicePayRef').val(obj.message.invoice_payment_ref);
      $('#invoiceInfoExtendModal').find('#invoiceDate').val(formatDate3(obj.message.due_date));
      
      if(obj.message.invoice_attachment){
          $('#invoiceInfoExtendModal').find('#InvoiceFilePath').val(obj.message.invoice_filepath);
          $('#invoiceInfoExtendModal').find('#viewInvoice').attr('href', "view_file.php?file="+obj.message.invoice_attachment).show();
      }

      if(obj.message.po_attachment){
          $('#invoiceInfoExtendModal').find('#POFilePath').val(obj.message.po_filepath);
          $('#invoiceInfoExtendModal').find('#viewPO').attr('href', "view_file.php?file="+obj.message.po_attachment).show();
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

  $('#invoiceInfoExtendModal').find('#uploadInvoiceAttachment').val('');
  $('#invoiceInfoExtendModal').find('#uploadPOAttachment').val('');

  $('#pricingTable').html('');
  pricingCount = 0;

  $('#cerId').hide();

  $('#invoiceInfoExtendModal').modal('show');

  $('#invoiceInfoExtendForm').validate({
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
</script>
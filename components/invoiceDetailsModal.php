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

  $('#invoiceInfoExtendModal').find('#invoice').val('');
  $('#invoiceInfoExtendModal').find('#uploadInvoiceAttachment').val('');
  $('#invoiceInfoExtendModal').find('#viewInvoice').hide();
  $('#invoiceInfoExtendModal').find('#InvoiceFilePath').val('');
  $('#invoiceInfoExtendModal').find('#invoicePaymentType').val('').trigger('change');
  $('#invoiceInfoExtendModal').find('#invoicePayRef').val('');

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
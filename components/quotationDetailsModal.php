<?php
?>

<div class="modal fade" id="quotationInfoExtendModal">
  <div class="modal-dialog modal-xl" style="max-width: 90%;">
    <div class="modal-content">
      <form role="form" id="quotationInfoExtendForm" enctype="multipart/form-data">
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
                <h4>Quotation Information</h4>
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
function newQuotationInfoEntry(id){
  var date = new Date();
  $('#capacityHigh').hide();
  $('#quotationInfoExtendModal').find('#id').val(id);

  $('#quotationInfoExtendModal').find('#quotation').val("");
  $('#quotationInfoExtendModal').find('#quotationDate').val('');
  $('#quotationInfoExtendModal').find('#poNo').val("");
  $('#quotationInfoExtendModal').find('#poDate').val('');
  $('#quotationInfoExtendModal').find('#viewQuotation').hide();
  $('#quotationInfoExtendModal').find('#uploadQuotationAttachment').val('');
  $('#quotationInfoExtendModal').find('#quotationFilePath').val('');

  $('#pricingTable').html('');
  pricingCount = 0;

  $('#cerId').hide();

  $('#quotationInfoExtendModal').modal('show');

  $('#quotationInfoExtendForm').validate({
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
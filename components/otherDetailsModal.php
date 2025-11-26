<?php
?>

<div class="modal fade" id="otherInfoExtendModal">
  <div class="modal-dialog modal-xl" style="max-width: 90%;">
    <div class="modal-content">
      <form role="form" id="otherInfoExtendForm" enctype="multipart/form-data">
        <div class="modal-header bg-gray-dark color-palette">
          <h4 class="modal-title">Stamping Forms</h4>
          <button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>

        <div class="modal-body" >
          <input type="hidden" class="form-control" id="id" name="id">
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

<script>
function newOtherInfoEntry(id){
  var date = new Date();
  $('#capacityHigh').hide();
  $('#otherInfoExtendModal').find('#id').val(id);

  $('#otherInfoExtendModal').find('#remark').val("");
  $('#otherInfoExtendModal').find('#internalRemark').val("");
  
  $('#pricingTable').html('');
  pricingCount = 0;

  $('#cerId').hide();

  $('#otherInfoExtendModal').modal('show');

  $('#otherInfoExtendForm').validate({
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
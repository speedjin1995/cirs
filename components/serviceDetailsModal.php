<?php
?>

<div class="modal fade" id="serviceInfoExtendModal">
  <div class="modal-dialog modal-xl" style="max-width: 90%;">
    <div class="modal-content">
      <form role="form" id="serviceInfoExtendForm" enctype="multipart/form-data">
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
                <h4>Service Information</h4>
              </div>
              <div class="row">
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
function newServiceInfoEntry(id){
  var date = new Date();
  $('#capacityHigh').hide();
  $('#serviceInfoExtendModal').find('#id').val(id);

  $('#serviceInfoExtendModal').find('#assignTo').val('').trigger('change');
  $('#serviceInfoExtendModal').find('#assignTo2').val('').trigger('change');
  $('#serviceInfoExtendModal').find('#assignTo3').val('').trigger('change');

  $('#pricingTable').html('');
  pricingCount = 0;

  $('#cerId').hide();

  $('#serviceInfoExtendModal').modal('show');

  $('#serviceInfoExtendForm').validate({
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
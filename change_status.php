<?php
?>

<div class="modal fade" id="changeStatusModal">
  <div class="modal-dialog modal-xl" style="max-width: 90%;">
    <div class="modal-content">
      <form role="form" id="changeStatusForm" enctype="multipart/form-data">
        <div class="modal-header bg-gray-dark color-palette">
          <h4 class="modal-title">Change Status</h4>
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
                <label>Status</label>
                <select class="form-control" style="width: 100%;" id="status" name="status" required>
                      <option value="Created">Created</option>
                      <option value="Pending Quotation">Pending Quotation</option>
                      <option value="Quoted">Quoted</option>
                      <option value="Servicing">Servicing</option>
                      <option value="Serviced">Serviced</option>
                      <option value="Appointed">Appointed</option>
                      <option value="Stamped">Stamped</option>
                      <option value="Invoiced">Invoiced</option>
                      <option value="Paid">Paid</option>
                </select>
              </div>
            </div>
          </div>

          <!--Customer Details--->
        </div>

        <div class="modal-footer justify-content-between bg-gray-dark color-palette">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="proceedButton">Ok</button>
        </div>
      </form>
    </div> <!-- /.modal-content -->
  </div> <!-- /.modal-dialog -->
</div> <!-- /.modal -->

<script>
function changeStatusEntry(id, rowStatus){
  var $modal = $('#changeStatusModal');
  var $select = $modal.find('#status');

  // Preserve original option list on first open so we can restore later
  if (!$modal.data('originalOptions')) {
    $modal.data('originalOptions', $select.html());
  }

  // Restore original options
  $select.html($modal.data('originalOptions'));

  // Define status order
  var order = ['Created','Pending Quotation','Quoted','Servicing','Serviced','Appointed','Stamped','Invoiced','Paid'];
  var currentIndex = order.indexOf(String(rowStatus));

  // If known status, remove all options after the current status
  if (currentIndex !== -1) {
    $select.find('option').each(function(){
      var val = $(this).val();
      var idx = order.indexOf(val);
      if (idx > currentIndex) {
        $(this).remove();
      }
    });
    // set the select to current value (or leave as empty if you prefer)
    $select.val(rowStatus);
  } else {
    // unknown status => keep all options and clear selection
    $select.val('');
  }

  

  // set hidden id and show modal
  $modal.find('#id').val(id);
  $modal.modal('show');

  $('#changeStatusForm').validate({
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
<?php
require_once 'php/db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
  echo '<script type="text/javascript">';
  echo 'window.location.href = "login.php";</script>';
}
else{
  $user = $_SESSION['userID'];
  $_SESSION['page']='machine';
  $alat = $db->query("SELECT * FROM alat WHERE deleted = '0'");

  $db->close();
}
?>

<style>
  th {
    text-align: center;
  }
</style>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0 text-dark">Machine/Scale Type</h1>
			</div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
	<div class="container-fluid">
        <div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header">
                        <div class="row">
                            <div class="col-8"></div>
                            <div class="col-2">
                                <button type="button" class="btn btn-block btn-sm bg-gradient-danger" id="multiDeactivate" data-bs-toggle="tooltip" title="Delete Price"><i class="fa-solid fa-ban"></i> Delete Machine Type</button>
                            </div>
                            <div class="col-2">
                                <button type="button" class="btn btn-block btn-sm bg-gradient-warning" id="addMachine"><i class="fa-solid fa-circle-plus"></i> Add Machine Type</button>
                            </div>
                        </div>
                    </div>
					<div class="card-body">
						<table id="machineTable" class="table table-bordered table-striped">
							<thead>
								<tr>
                                    <th><input type="checkbox" id="selectAllCheckbox" class="selectAllCheckbox"></th>
									<th>No.</th>
									<th>Machine Type</th>
                                    <th>Jenis Alat</th>
									<th>Actions</th>
								</tr>
							</thead>
						</table>
					</div><!-- /.card-body -->
				</div><!-- /.card -->
			</div><!-- /.col -->
		</div><!-- /.row -->
	</div><!-- /.container-fluid -->
</section><!-- /.content -->

<div class="modal fade" id="machineModal">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <form role="form" id="machineForm">
            <div class="modal-header">
              <h4 class="modal-title">Add Machines Type</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group">
    					<input type="hidden" class="form-control" id="id" name="id">
    				</div>
    				<div class="form-group">
    					<label for="machineTypes">Machines Type *</label>
    					<input type="text" class="form-control" name="machineTypes" id="machineTypes" placeholder="Enter Machine Type" required>
    				</div>
                    <div class="form-group">
                        <label for="code">Jenis Alat *</label>
                        <select class="form-control select2" id="alat" name="alat">
                            <option value="" selected disabled hidden>Please Select</option>
                            <?php while($rowCustomer2=mysqli_fetch_assoc($alat)){ ?>
                                <option value="<?=$rowCustomer2['id'] ?>"><?=$rowCustomer2['alat'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
    			</div>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary" name="submit" id="submitLot">Submit</button>
            </div>
        </form>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<script>
$(function () {
    $('.select2').each(function() {
        $(this).select2({
            allowClear: true,
            placeholder: "Please Select",
            // Conditionally set dropdownParent based on the element’s location
            dropdownParent: $(this).closest('.modal').length ? $(this).closest('.modal-body') : undefined
        });
    });

    $('#selectAllCheckbox').on('change', function() {
        var checkboxes = $('#machineTable tbody input[type="checkbox"]');
        checkboxes.prop('checked', $(this).prop('checked')).trigger('change');
    });

    $("#machineTable").DataTable({
        "responsive": true,
        "autoWidth": false,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'order': [[ 2, 'asc' ]],
        'columnDefs': [ { orderable: false, targets: [0] }],
        'ajax': {
            'url':'php/loadMachine.php'
        },
        'columns': [
            {
                // Add a checkbox with a unique ID for each row
                data: 'id', // Assuming 'serialNo' is a unique identifier for each row
                className: 'select-checkbox',
                orderable: false,
                render: function (data, type, row) {
                    return '<input type="checkbox" class="select-checkbox" id="checkbox_' + data + '" value="'+data+'"/>';
                }
            },
            { data: 'counter' },
            { data: 'machine_type' },
            { data: 'jenis_alat' },
            {
                data: 'id',
                render: function (data, type, row) {
                    let dropdownMenu = '<div class="dropdown">' +
                    '<button class="btn btn-secondary btn-sm" type="button" id="dropdownMenuButton' + data + '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
                    '<i class="fa-solid fa-ellipsis"></i>' +
                    '</button>' +
                    '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton' + data + '">' +
                    '<a class="dropdown-item" id="edit' + data + '" onclick="edit(' + data + ')"><i class="fas fa-pen"></i> Edit</a>' +
                    '<a class="dropdown-item" id="deactivate' + data + '" onclick="deactivate(' + data + ')"><i class="fa fa-times" aria-hidden="true"></i> Delete</a>';
                    dropdownMenu += '</div></div>';

                    return dropdownMenu;
                }
            },
            // { 
            //     data: 'id',
            //     render: function ( data, type, row ) {
            //         return '<div class="row"><div class="col-3"><button type="button" id="edit'+data+'" onclick="edit('+data+')" class="btn btn-success btn-sm"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" id="deactivate'+data+'" onclick="deactivate('+data+')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></div></div>';
            //     }
            // }
        ],
        "lengthMenu": [ [10, 25, 50, 100, 300, 600, 1000], [10, 25, 50, 100, 300, 600, 1000] ], // More show options
        "pageLength": 10, // Default rows per page
        "rowCallback": function( row, data, index ) {

            $('td', row).css('background-color', '#E6E6FA');
        },        
    });
    
    $.validator.setDefaults({
        submitHandler: function () {
            $('#spinnerLoading').show();
            $.post('php/machine.php', $('#machineForm').serialize(), function(data){
                var obj = JSON.parse(data); 
                
                if(obj.status === 'success'){
                    $('#machineModal').modal('hide');
                    toastr["success"](obj.message, "Success:");
                    $('#machineTable').DataTable().ajax.reload(null, false);
                    $('#spinnerLoading').hide();
                }
                else if(obj.status === 'failed'){
                    toastr["error"](obj.message, "Failed:");
                    $('#spinnerLoading').hide();
                }
                else{
                    toastr["error"]("Something wrong when edit", "Failed:");
                    $('#spinnerLoading').hide();
                }
            });
        }
    });

    $('#addMachine').on('click', function(){
        $('#machineModal').find('#id').val("");
        $('#machineModal').find('#machineTypes').val("").trigger('change');
        $('#machineModal').find('#alat').val("").trigger('change');
        $('#machineModal').modal('show');
        
        $('#machineForm').validate({
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
    });

    $('#multiDeactivate').on('click', function () {
        $('#spinnerLoading').show();
        var selectedIds = []; // An array to store the selected 'id' values

        $("#machineTable tbody input[type='checkbox']").each(function () {
            if (this.checked) {
                selectedIds.push($(this).val());
            }
        });

        if (selectedIds.length > 0) {
            if (confirm('Are you sure you want to cancel these items?')) {
                $.post('php/deleteMachine.php', {userID: selectedIds, type: 'MULTI'}, function(data){
                    var obj = JSON.parse(data);
                    
                    if(obj.status === 'success'){
                        toastr["success"](obj.message, "Success:");
                        $('#machineTable').DataTable().ajax.reload(null, false);
                        $('#spinnerLoading').hide();
                    }
                    else if(obj.status === 'failed'){
                        toastr["error"](obj.message, "Failed:");
                        $('#spinnerLoading').hide();
                    }
                    else{
                        toastr["error"]("Something wrong when activate", "Failed:");
                        $('#spinnerLoading').hide();
                    }
                });
            }

            $('#spinnerLoading').hide();
        } 
        else {
            // Optionally, you can display a message or take another action if no IDs are selected
            alert("Please select at least one machine type to delete.");
            $('#spinnerLoading').hide();
        }     
    });
});

function edit(id){
    $('#spinnerLoading').show();
    $.post('php/getMachine.php', {userID: id}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
            $('#machineModal').find('#id').val(obj.message.id);
            $('#machineModal').find('#machineTypes').val(obj.message.machine_type);
            $('#machineModal').find('#alat').val(obj.message.jenis_alat).trigger('change');
            $('#machineModal').modal('show');
            
            $('#machineForm').validate({
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
            toastr["error"]("Something wrong when activate", "Failed:");
        }
        $('#spinnerLoading').hide();
    });
}

function deactivate(id){
    if (confirm('Are you sure you want to cancel this item?')) {
        $('#spinnerLoading').show();
        $.post('php/deleteMachine.php', {userID: id}, function(data){
            var obj = JSON.parse(data);
            
            if(obj.status === 'success'){
                toastr["success"](obj.message, "Success:");
                $('#machineTable').DataTable().ajax.reload(null, false);
                $('#spinnerLoading').hide();
            }
            else if(obj.status === 'failed'){
                toastr["error"](obj.message, "Failed:");
                $('#spinnerLoading').hide();
            }
            else{
                toastr["error"]("Something wrong when activate", "Failed:");
                $('#spinnerLoading').hide();
            }
        });
    }
}
</script>
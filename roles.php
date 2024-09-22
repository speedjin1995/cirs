<?php
require_once 'php/db_connect.php';
//require_once 'php/treeBuilder.php';

session_start();

if(!isset($_SESSION['userID'])){
echo '<script type="text/javascript">';
  echo 'window.location.href = "login.html";</script>';
}
else{
    $user = $_SESSION['userID'];
    //$modules = $db->query("SELECT * FROM modules");
    //$moduleTree = buildModuleTree($db, '0');
}
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0 text-dark">Roles</h1>
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
                            <div class="col-9"></div>
                            <div class="col-3">
                                <button type="button" class="btn btn-block bg-gradient-warning btn-sm" id="addLots">Add Reason</button>
                            </div>
                        </div>
                    </div>
					<div class="card-body">
						<table id="lotTable" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>No.</th>
									<th>Roles</th>
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

<div class="modal fade" id="lotModal">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <form role="form" id="lotForm">
            <div class="modal-header">
              <h4 class="modal-title">Add Roles</h4>
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
    					<label for="lotsNumber">Roles Code *</label>
    					<input type="text" class="form-control" name="rolecode" id="rolecode" placeholder="Enter Roles Code" required>
    				</div>
    				<div class="form-group">
    					<label for="lotsNumber">Roles Name*</label>
    					<input type="text" class="form-control" name="roles" id="roles" placeholder="Enter Roles Name" required>
    				</div><br><br>
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
    $("#lotTable").DataTable({
        "responsive": true,
        "autoWidth": false,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'order': [[ 1, 'asc' ]],
        'columnDefs': [ { orderable: false, targets: [0] }],
        'ajax': {
            'url':'php/loadRoles.php'
        },
        'columns': [
            { data: 'counter' },
            { data: 'role_name' },
            { 
                data: 'id',
                render: function ( data, type, row ) {
                    return '<div class="row"><div class="col-3"><button type="button" id="edit'+data+'" onclick="edit('+data+')" class="btn btn-success btn-sm"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" id="deactivate'+data+'" onclick="deactivate('+data+')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></div></div>';
                }
            }
        ],
        "rowCallback": function( row, data, index ) {

            //$('td', row).css('background-color', '#E6E6FA');
        },        
    });
    
    $.validator.setDefaults({
        submitHandler: function () {
            $('#spinnerLoading').show();
            $.post('php/roles.php', $('#lotForm').serialize(), function(data){
                var obj = JSON.parse(data); 
                
                if(obj.status === 'success'){
                    $('#lotModal').modal('hide');
                    toastr["success"](obj.message, "Success:");
                    $('#lotTable').DataTable().ajax.reload();
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

    $('.level1').change(function() {
        // When a checkbox in level 1 is checked/unchecked
        var isChecked = $(this).prop('checked');
        $(this).closest('tr').find('.inner').prop('checked', isChecked);
    });

    $('.inner').on('change', function() {
        // When a checkbox in level 2 is checked/unchecked
        var isChecked = $(this).prop('checked');
        $(this).parents('.main').find('.level1').prop('checked', isChecked);
    });

    $('#addLots').on('click', function(){
        $('#lotModal').find('#id').val("");
        $('#lotModal').find('#rolecode').val("");
        $('#lotModal').find('#roles').val("");
        $('#lotModal').modal('show');
        
        $('#lotForm').validate({
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
});

function edit(id){
    $('#spinnerLoading').show();
    $.post('php/getRoles.php', {userID: id}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
            $('#lotModal').find('#id').val(obj.message.id);
            $('#lotModal').find('#rolecode').val(obj.message.role_code);
            $('#lotModal').find('#roles').val(obj.message.role_name);
            $('#lotModal').modal('show');
            
            $('#lotForm').validate({
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
    if (confirm('Are you sure you want to delete this Role?')) {
        $('#spinnerLoading').show();
        $.post('php/deleteRole.php', {userID: id}, function(data){
            var obj = JSON.parse(data);
            
            if(obj.status === 'success'){
                toastr["success"](obj.message, "Success:");
                $('#lotTable').DataTable().ajax.reload();
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
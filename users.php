<?php
require_once 'php/db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
    echo '<script type="text/javascript">';
	echo 'window.location.href = "../login.php";</script>';
}
else{
    $_SESSION['page']='users';
    $user = $_SESSION['userID'];
    $stmt = $db->prepare("SELECT * from users where id = ?");
	$stmt->bind_param('s', $user);
	$stmt->execute();
	$result = $stmt->get_result();
    if(($row = $result->fetch_assoc()) !== null){
        $role = $row['role_code'];
    }
    $stmt->close();

    $superAdminRoles = $db->query("SELECT * FROM roles WHERE role_code != 'SUPER_ADMIN' AND deleted = '0'");
    $adminRoles = $db->query("SELECT * FROM roles WHERE role_code NOT IN ('SUPER_ADMIN', 'ADMIN') AND deleted = '0'");
}
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0 text-dark">Users</h1>
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
                                <button type="button" class="btn btn-block bg-gradient-warning btn-sm" id="addMembers">Add Users</button>
                            </div>
                        </div>
                    </div>
					<div class="card-body">
						<table id="memberTable" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>Username</th>
									<th>Name</th>
                                    <th>I/C. No</th>
                                    <th>Job Position</th>
                                    <th>Contact No(H/P)</th>
									<th>System Role</th>
									<th>Status</th>
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

<div class="modal fade" id="addModal">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <form role="form" id="memberForm">
            <div class="modal-header">
              <h4 class="modal-title">Add Users</h4>
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
    					<label for="username">Username *</label>
    					<input type="text" class="form-control" name="username" id="username" placeholder="Enter Username" required>
    				</div>
                    <div class="form-group">
    					<label for="name">Staff Name *</label>
    					<input type="text" class="form-control" name="name" id="name" placeholder="Enter Full Name" required>
    				</div>
                    <div class="form-group">
    					<label for="name">Staff IC </label>
    					<input type="text" class="form-control" name="icNo" id="icNo" placeholder="Enter IC">
    				</div>
                    <div class="form-group">
                        <label>Job Position </label>
						<input type="text" class="form-control" name="position" id="position" placeholder="Enter Position">
    				</div>
                    <div class="form-group">
    					<label for="name">Staff Contact (H/P)</label>
    					<input type="text" class="form-control" name="phoneNumber" id="phoneNumber" placeholder="Enter H/P">
    				</div>
                    <div class="form-group">
						<label>System Role *</label>
						<select class="form-control" id="userRole" name="userRole" required>
						    <option select="selected" value="">Please Select</option>
						    <?php if ($role == 'SUPER_ADMIN') { ?>
                                <?php while ($row2 = mysqli_fetch_assoc($superAdminRoles)) { ?>
                                    <option value="<?= $row2['role_code'] ?>"><?= $row2['role_name'] ?></option>
                                <?php } ?>
                            <?php } else { ?>
                                <?php while ($row2 = mysqli_fetch_assoc($adminRoles)) { ?>
                                    <option value="<?= $row2['role_code'] ?>"><?= $row2['role_name'] ?></option>
                                <?php } ?>
                            <?php } ?>
						</select>
					</div>
    			</div>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary" name="submit" id="submitMember">Submit</button>
            </div>
        </form>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<script>
$(function () {
    $("#memberTable").DataTable({
        "responsive": true,
        "autoWidth": false,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
            'url':'php/loadMembers.php'
        },
        'columns': [
            { data: 'username' },
            { data: 'name' },
            { data: 'ic_number' },
            { data: 'designation' },
            { data: 'contact_number' },
            { data: 'role_name' },
            { data: 'status' },
            {
                data: 'deleted',
                render: function (data, type, row) {
                let dropdownMenu = '<div class="dropdown">' +
                    '<button class="btn btn-secondary btn-sm" type="button" id="dropdownMenuButton' + data + '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
                    '<i class="fa-solid fa-ellipsis"></i>' +
                    '</button>' +
                    '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton' + data + '">';

                    if (data == 0) {
                        dropdownMenu += 
                        '<a class="dropdown-item" id="edit' + row.id + '" onclick="edit(' + row.id + ')"><i class="fas fa-pen"></i> Edit</a>' +
                        '<a class="dropdown-item" id="delete' + row.id + '" onclick="deactivate(' + row.id + ')"><i class="fa fa-times" aria-hidden="true"></i> Delete</a>';
                        // return '<div class="row"><div class="col-3"><button type="button" id="edit' + row.id + '" onclick="edit(' + row.id + ')" class="btn btn-success btn-sm"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" id="delete' + row.id + '" onclick="deactivate(' + row.id + ')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></div></div>';
                    } 
                    else{
                        dropdownMenu += 
                        '<a class="dropdown-item" id="reactivate' + row.id + '" onclick="reactivate(' + row.id + ')"><i class="fa fa-refresh" aria-hidden="true"></i>Reactivate</a>';
                    }
                    
                dropdownMenu += '</div></div>';

                return dropdownMenu;
                }
            }
            // { 
            //     data: 'deleted',
            //     render: function (data, type, row) {
            //         if (data == 0) {
            //             return '<div class="row"><div class="col-3"><button type="button" id="edit' + row.id + '" onclick="edit(' + row.id + ')" class="btn btn-success btn-sm"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" id="delete' + row.id + '" onclick="deactivate(' + row.id + ')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></div></div>';
            //         } 
            //         else{
            //             return '<button type="button" id="reactivate' + row.id + '" onclick="reactivate(' + row.id + ')" class="btn btn-warning btn-sm">Reactivate</button>';
            //         }
            //     }
            // }
        ],
        "rowCallback": function( row, data, index ) {

            $('td', row).css('background-color', '#E6E6FA');
        },
    });
    
    $.validator.setDefaults({
        submitHandler: function () {
            $('#spinnerLoading').show();
            $.post('php/users.php', $('#memberForm').serialize(), function(data){
                var obj = JSON.parse(data); 
                
                if(obj.status === 'success'){
                    $('#addModal').modal('hide');
                    toastr["success"](obj.message, "Success:");
                    $('#memberTable').DataTable().ajax.reload();
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

    $('#addMembers').on('click', function(){
        $('#addModal').find('#id').val("");
        $('#addModal').find('#username').val("");
        $('#addModal').find('#name').val("");
        $('#addModal').find('#icNo').val("");
        $('#addModal').find('#position').val("");
        $('#addModal').find('#phoneNumber').val("");
        $('#addModal').find('#userRole').val("");
        $('#addModal').modal('show');
        
        $('#memberForm').validate({
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
    $.post('php/getUser.php', {userID: id}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
            $('#addModal').find('#id').val(obj.message.id);
            $('#addModal').find('#username').val(obj.message.username);
            $('#addModal').find('#name').val(obj.message.name);
            $('#addModal').find('#icNo').val(obj.message.ic_number);
            $('#addModal').find('#position').val(obj.message.designation);
            $('#addModal').find('#phoneNumber').val(obj.message.contact_number);
            $('#addModal').find('#userRole').val(obj.message.role_code);
            $('#addModal').modal('show');
            
            $('#memberForm').validate({
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
    $('#spinnerLoading').show();
    $.post('php/deleteUser.php', {userID: id}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
            toastr["success"](obj.message, "Success:");
            $('#memberTable').DataTable().ajax.reload();
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

function reactivate(id){
  if (confirm('Are you sure you want to reactivate this items?')) {
    $('#spinnerLoading').show();
    $.post('php/reactivateUser.php', {userID: id}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
            toastr["success"](obj.message, "Success:");
            $('#memberTable').DataTable().ajax.reload();
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
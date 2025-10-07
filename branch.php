<?php
require_once 'php/db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
  echo '<script type="text/javascript">';
  echo 'window.location.href = "login.php";</script>';
}
else{
  $user = $_SESSION['userID'];
  $_SESSION['page']='branch';
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
				<h1 class="m-0 text-dark">Company Branches</h1>
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
                            <div class="col-4"></div>
                            <div class="col-2">
                                <button type="button" class="btn btn-block btn-sm bg-gradient-danger" id="multiDeactivate" data-bs-toggle="tooltip" title="Delete Branch"><i class="fa-solid fa-ban"></i> Delete Branch</button>
                            </div>
                            <div class="col-2">
                                <a href="template/Company_Branch_Template.xlsx" download><button type="button" class="btn btn-block btn-sm bg-gradient-info" id="downloadExcel"><i class="fa-solid fa-download"></i> Download Template</button></a>
                            </div>
                            <div class="col-2">
                                <button type="button" class="btn btn-block btn-sm bg-gradient-success" id="uploadExcel"><i class="fa-regular fa-file-excel"></i> Upload Excel</button>
                            </div>
                            <div class="col-2">
                                <button type="button" class="btn btn-block btn-sm bg-gradient-warning" id="addBranch"><i class="fa-solid fa-circle-plus"></i> Add Branch</button>
                            </div>
                        </div>
                    </div>
					<div class="card-body">
						<table id="branchTable" class="table table-bordered table-striped">
							<thead>
								<tr>
                                    <th><input type="checkbox" id="selectAllCheckbox" class="selectAllCheckbox"></th>
									<th>No.</th>
									<th>Branch Code</th>
									<th>Branch Name</th>
									<th>Address</th>
									<th>Email</th>
									<th>Office No.</th>
									<th>PIC</th>
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

<div class="modal fade" id="branchModal">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <form role="form" id="branchForm">
            <div class="modal-header">
              <h4 class="modal-title">Add Branches</h4>
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
    					<label for="branchCode">Branch Code</label>
    					<input type="text" class="form-control" name="branchCode" id="branchCode" placeholder="Enter Branch Code">
    				</div>
    				<div class="form-group">
    					<label for="branchName">Branch Name *</label>
    					<input type="text" class="form-control" name="branchName" id="branchName" placeholder="Enter Branch Name" required>
    				</div>
    				<div class="form-group">
    					<label for="addressLine1">Address Line 1*</label>
                        <input type="text" class="form-control" name="addressLine1" id="addressLine1" placeholder="Enter Address Line 1" required>
    				</div>
    				<div class="form-group">
    					<label for="addressLine2">Address Line 2</label>
    					<input type="text" class="form-control" name="addressLine2" id="addressLine2" placeholder="Enter Address Line 2">
    				</div>
    				<div class="form-group">
    					<label for="addressLine3">Address Line 3</label>
    					<input type="text" class="form-control" name="addressLine3" id="addressLine3" placeholder="Enter Address Line 3">
    				</div>
    				<div class="form-group">
    					<label for="addressLine4">Address Line 4</label>
    					<input type="text" class="form-control" name="addressLine4" id="addressLine4" placeholder="Enter Address Line 4">
    				</div>
    				<div class="form-group">
    					<label for="addressLine5">Address Line 5</label>
    					<input type="text" class="form-control" name="addressLine5" id="addressLine5" placeholder="Enter Address Line 5">
    				</div>
    				<div class="form-group">
    					<label for="mapUrl">Map Url</label>
    					<input type="text" class="form-control" name="mapUrl" id="mapUrl" placeholder="Enter Map Url">
    				</div>
    				<div class="form-group">
    					<label for="pic">PIC</label>
    					<input type="text" class="form-control" name="pic" id="pic" placeholder="Enter PIC">
    				</div>
    				<div class="form-group">
    					<label for="picContact">PIC Contact</label>
    					<input type="text" class="form-control" name="picContact" id="picContact" placeholder="Enter PIC Contact">
    				</div>
    				<div class="form-group">
    					<label for="email">Email</label>
    					<input type="text" class="form-control" name="email" id="email" placeholder="Enter Email">
    				</div>
    				<div class="form-group">
    					<label for="officeNo">Office No.</label>
    					<input type="text" class="form-control" name="officeNo" id="officeNo" placeholder="Enter Office No.">
    				</div>
    			</div>
            </div>
            <div class="modal-footer justify-content-between">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary" name="submit" id="submitBranch">Submit</button>
            </div>
        </form>
      </div>
      <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="uploadModal">
  <div class="modal-dialog modal-xl" style="max-width: 90%;">
    <div class="modal-content">
      <form role="form" id="uploadForm">
        <div class="modal-header bg-gray-dark color-palette">
          <h4 class="modal-title">Upload Excel File</h4>
          <button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <input type="file" id="fileInput">
          <button type="button" id="previewButton">Preview Data</button>
          <div id="previewTable" style="overflow: auto;"></div>
        </div>
        <div class="modal-footer justify-content-between bg-gray-dark color-palette">
          <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
          <button type="submit" class="btn btn-primary" id="saveButton">Save changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<div class="modal fade" id="errorModal" style="display:none">
    <div class="modal-dialog modal-xl" style="max-width: 50%;">
        <div class="modal-content">
            <div class="modal-header bg-gray-dark color-palette">
                <h4 class="modal-title">Error Log</h4>
                <button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group">
                        <ol id="errorList" class="text-danger mt-2" style="padding-left: 20px;"></ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>  


<script>
let wasErrorModalShown = false;

$(function () {
    $('#selectAllCheckbox').on('change', function() {
        var checkboxes = $('#branchTable tbody input[type="checkbox"]');
        checkboxes.prop('checked', $(this).prop('checked')).trigger('change');
    });

    $("#branchTable").DataTable({
        "responsive": true,
        "autoWidth": false,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'order': [[ 2, 'asc' ]],
        'columnDefs': [ { orderable: false, targets: [0] }],
        'ajax': {
            'url':'php/loadBranch.php'
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
            { data: 'branch_code' },
            { data: 'branch_name' },
            { data: 'address' },
            { data: 'email' },
            { data: 'office_no' },
            { data: 'pic' },
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
            }
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
            if($('#branchModal').hasClass('show')){
                $('#spinnerLoading').show();
                $.post('php/branch.php', $('#branchForm').serialize(), function(data){
                    var obj = JSON.parse(data); 
                    
                    if(obj.status === 'success'){
                        $('#branchModal').modal('hide');
                        toastr["success"](obj.message, "Success:");
                        $('#branchTable').DataTable().ajax.reload(null, false);
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
            } else if($('#uploadModal').hasClass('show')){
                $('#spinnerLoading').show();

                // Serialize the form data into an array of objects
                var formData = $('#uploadForm').serializeArray();
                var data = [];
                var rowIndex = -1;
                formData.forEach(function(field) {
                    var match = field.name.match(/([a-zA-Z0-9]+)\[(\d+)\]/);
                    if (match) {
                        var fieldName = match[1];
                        var index = parseInt(match[2], 10);
                        if (index !== rowIndex) {
                            rowIndex = index;
                            data.push({});
                        }
                        data[index][fieldName] = field.value;
                    }
                });

                // Send the JSON array to the server
                $.ajax({
                    url: 'php/uploadBranches.php',
                    type: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify(data),
                    success: function(response) {
                        var obj = JSON.parse(response);
                        if (obj.status === 'success') {
                        $('#uploadModal').modal('hide');
                            toastr["success"](obj.message, "Success:");
                            $('#branchTable').DataTable().ajax.reload(null, false);
                        } 
                        else if (obj.status === 'failed') {
                            toastr["error"](obj.message, "Failed:");
                        } 
                        else if (obj.status === 'error') {
                            $('#uploadModal').modal('hide');;
                            $('#errorModal').find('#errorList').empty();
                            var errorMessage = obj.message;
                            for (var i = 0; i < errorMessage.length; i++) {
                                $('#errorModal').find('#errorList').append(`<li>${errorMessage[i]}</li>`);                            
                            }
                            $('#errorModal').modal('show');
                        } 
                        else {
                            toastr["error"]("Something went wrong when uploading", "Failed:");
                        }
                        
                        $('#spinnerLoading').hide();
                    }
                });
            }
        }
    });

    $('#addBranch').on('click', function(){
        $('#branchModal').find('#id').val("");
        $('#branchModal').find('#branchCode').val("");
        $('#branchModal').find('#branchName').val("");
        $('#branchModal').find('#addressLine1').val("");
        $('#branchModal').find('#addressLine2').val("");
        $('#branchModal').find('#addressLine3').val("");
        $('#branchModal').find('#addressLine4').val("");
        $('#branchModal').find('#addressLine5').val("");
        $('#branchModal').find('#mapUrl').val("");
        $('#branchModal').find('#pic').val("");
        $('#branchModal').find('#picContact').val("");
        $('#branchModal').find('#email').val("");
        $('#branchModal').find('#officeNo').val("");
        $('#branchModal').modal('show');
        
        $('#branchForm').validate({
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

        $("#branchTable tbody input[type='checkbox']").each(function () {
            if (this.checked) {
                selectedIds.push($(this).val());
            }
        });

        if (selectedIds.length > 0) {
            if (confirm('Are you sure you want to cancel these items?')) {
                $.post('php/deleteBranch.php', {userID: selectedIds, type: 'MULTI'}, function(data){
                    var obj = JSON.parse(data);
                    
                    if(obj.status === 'success'){
                        toastr["success"](obj.message, "Success:");
                        $('#branchTable').DataTable().ajax.reload(null, false);
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
            alert("Please select at least one country to delete.");
            $('#spinnerLoading').hide();
        }     
    });

    $('#uploadExcel').on('click', function(){
        $('#uploadModal').modal('show');

        $('#uploadForm').validate({
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

    $('#uploadModal').find('#previewButton').on('click', function(){
        var fileInput = document.getElementById('fileInput');
        var file = fileInput.files[0];
        var reader = new FileReader();
        
        reader.onload = function(e) {
        var data = e.target.result;
        // Process data and display preview
        displayPreview(data);
        };

        reader.readAsBinaryString(file);
    });

    $('#errorModal').on('shown.bs.modal', function () {
        wasErrorModalShown = true;
    });
    
    $('#errorModal').on('hidden.bs.modal', function () {
        if (wasErrorModalShown) {
            wasErrorModalShown = false; // Reset flag
            window.location.reload();
        }
    });

});

function edit(id){
    $('#spinnerLoading').show();
    $.post('php/getCompanyBranch.php', {userID: id}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
            $('#branchModal').find('#id').val(obj.message.id);
            $('#branchModal').find('#branchCode').val(obj.message.branch_code);
            $('#branchModal').find('#branchName').val(obj.message.branch_name);
            $('#branchModal').find('#addressLine1').val(obj.message.address_line_1);
            $('#branchModal').find('#addressLine2').val(obj.message.address_line_2);
            $('#branchModal').find('#addressLine3').val(obj.message.address_line_3);
            $('#branchModal').find('#addressLine4').val(obj.message.address_line_4);
            $('#branchModal').find('#addressLine5').val(obj.message.address_line_5);
            $('#branchModal').find('#mapUrl').val(obj.message.map_url);
            $('#branchModal').find('#pic').val(obj.message.pic);
            $('#branchModal').find('#picContact').val(obj.message.pic_contact);
            $('#branchModal').find('#officeNo').val(obj.message.office_no);
            $('#branchModal').find('#email').val(obj.message.email);
            $('#branchModal').modal('show');
            
            $('#branchForm').validate({
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
        $.post('php/deleteBranch.php', {userID: id}, function(data){
            var obj = JSON.parse(data);
            
            if(obj.status === 'success'){
                toastr["success"](obj.message, "Success:");
                $('#branchTable').DataTable().ajax.reload(null, false);
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
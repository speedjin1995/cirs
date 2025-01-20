<?php
require_once 'php/db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
  echo '<script type="text/javascript">';
  echo 'window.location.href = "login.php";</script>';
}
else{
  $user = $_SESSION['userID'];
  $_SESSION['page']='price';
  $machinetypes = $db->query("SELECT * FROM machines WHERE deleted = '0'");
  $alats = $db->query("SELECT * FROM alat WHERE deleted = '0'");
  $capacities = $db->query("SELECT * FROM capacity WHERE deleted = '0'");
  $validators = $db->query("SELECT * FROM validators WHERE deleted = '0' AND type = 'STAMPING'");
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
				<h1 class="m-0 text-dark">Products/Prices</h1>
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
                                <button type="button" class="btn btn-block btn-sm bg-gradient-danger" id="multiDeactivate" data-bs-toggle="tooltip" title="Delete Price"><i class="fa-solid fa-ban"></i> Delete Price</button>
                            </div>
                            <div class="col-2">
                                <button type="button" class="btn btn-block btn-sm bg-gradient-warning" id="addCapacity"><i class="fa-solid fa-circle-plus"></i> Add Price</button>
                            </div>
                        </div>
                    </div>
					<div class="card-body">
						<table id="capacityTable" class="table table-bordered table-striped">
							<thead>
								<tr>
                                    <th><input type="checkbox" id="selectAllCheckbox" class="selectAllCheckbox"></th>
                                    <th>No.</th>
									<!--th>Name</th-->
                                    <!--th>Machine Type</th-->
                                    <th>Jenis Alat</th>
                                    <th>Capacity</th>
                                    <th>Validator</th>
                                    <th>Price (RM)</th>
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

<div class="modal fade" id="capacityModal">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <form role="form" id="capacityForm">
            <div class="modal-header">
                <h4 class="modal-title">Add Price</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <div class="form-group">
                        <input type="hidden" class="form-control" id="id" name="id">
                    </div>
                    <!--div class="form-group">
                        <label for="capacity">Product Name *</label>
                        <input type="text" class="form-control" name="capacityName" id="capacityName" placeholder="Enter Capacity Name" required>
                    </div>
                    <div class="form-group">
                        <label>Machine Type </label>
                        <select class="form-control" style="width: 100%;" id="machineType" name="machineType">
                            <option selected="selected">-</option>
                            <?php while($rowS=mysqli_fetch_assoc($machinetypes)){ ?>
                                <option value="<?=$rowS['id'] ?>"><?=$rowS['machine_type'] ?></option>
                            <?php } ?>
                        </select>
                    </div-->
                    <div class="form-group">
                        <label>Jenis Alat </label>
                        <select class="form-control select2" style="width: 100%;" id="jenisAlat" name="jenisAlat">
                            <?php while($rowA=mysqli_fetch_assoc($alats)){ ?>
                                <option value="<?=$rowA['id'] ?>"><?=$rowA['alat'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Capacity </label>
                        <select class="form-control select2" style="width: 100%;" id="capacity" name="capacity">
                            <?php while($rowCA=mysqli_fetch_assoc($capacities)){ ?>
                                <option value="<?=$rowCA['id'] ?>"><?=$rowCA['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Validator </label>
                        <select class="form-control select2" style="width: 100%;" id="validator" name="validator">
                            <?php while($rowVA=mysqli_fetch_assoc($validators)){ ?>
                                <option value="<?=$rowVA['id'] ?>"><?=$rowVA['validator'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="capacity">Type *</label>
                        <select class="form-control select2" style="width: 100%;" id="type" name="type">
                            <option value="FIXED">FIXED</option>
                            <option value="PERCENTAGE">PERCENTAGE</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="capacity">Price *</label>
                        <input type="number" class="form-control" name="price" id="price" placeholder="Enter Price" required>
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
    $('.select2').each(function() {
        $(this).select2({
            allowClear: true,
            placeholder: "Please Select",
            // Conditionally set dropdownParent based on the element’s location
            dropdownParent: $(this).closest('.modal').length ? $(this).closest('.modal-body') : undefined
        });
    });

    $("#capacityModal").find("#price").change(function() {
        var price = $(this).val();
        var formattedPrice = parseFloat(price).toFixed(2);
        $(this).val(formattedPrice);
    });

    $('#selectAllCheckbox').on('change', function() {
        var checkboxes = $('#capacityTable tbody input[type="checkbox"]');
        checkboxes.prop('checked', $(this).prop('checked')).trigger('change');
    });

    $("#capacityTable").DataTable({
        "responsive": true,
        "autoWidth": false,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'searching': true,
        "stateSave": true,
        'order': [[ 2, 'asc' ]],
        'ajax': {
            'url':'php/loadProducts.php'
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
            //{ data: 'name' },
            //{ data: 'machine_type' },
            { data: 'jenis_alat' },
            { data: 'capacity' },
            { data: 'validator' },
            { data: 'price' },
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
        ],
        "rowCallback": function( row, data, index ) {
            $('td', row).css('background-color', '#E6E6FA');
        },        
    });
    
    $.validator.setDefaults({
        submitHandler: function () {
            $('#spinnerLoading').show();
            $.post('php/products.php', $('#capacityForm').serialize(), function(data){
                var obj = JSON.parse(data); 
                
                if(obj.status === 'success'){
                    $('#capacityModal').modal('hide');
                    toastr["success"](obj.message, "Success:");
                    $('#capacityTable').DataTable().ajax.reload(null, false);
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

    $('#addCapacity').on('click', function(){
        $('#capacityModal').find('#id').val("");
        //$('#capacityModal').find('#capacityName').val("");
        //$('#capacityModal').find('#machineType').val("");
        $('#capacityModal').find('#jenisAlat').val("").trigger('change');
        $('#capacityModal').find('#capacity').val("").trigger('change');
        $('#capacityModal').find('#validator').val("").trigger('change');
        $('#capacityModal').find('#type').val("FIXED").trigger('change');
        $('#capacityModal').find('#price').val("");
        $('#capacityModal').modal('show');
        
        $('#capacityForm').validate({
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

        $("#capacityTable tbody input[type='checkbox']").each(function () {
            if (this.checked) {
                selectedIds.push($(this).val());
            }
        });

        if (selectedIds.length > 0) {
            if (confirm('Are you sure you want to cancel these items?')) {
                $.post('php/deleteProducts.php', {userID: selectedIds, type: 'MULTI'}, function(data){
                    var obj = JSON.parse(data);
                    
                    if(obj.status === 'success'){
                        toastr["success"](obj.message, "Success:");
                        $('#capacityTable').DataTable().ajax.reload(null, false);
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
            alert("Please select at least one price to delete.");
            $('#spinnerLoading').hide();
        }     
    });
});

function edit(id){
    $('#spinnerLoading').show();
    $.post('php/getProducts.php', {userID: id}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
            $('#capacityModal').find('#id').val(obj.message.id).trigger('change');
            //$('#capacityModal').find('#capacityName').val(obj.message.name);
            //$('#capacityModal').find('#machineType').val(obj.message.machine_type);
            $('#capacityModal').find('#jenisAlat').val(obj.message.jenis_alat).trigger('change');
            $('#capacityModal').find('#capacity').val(obj.message.capacity).trigger('change');
            $('#capacityModal').find('#validator').val(obj.message.validator).trigger('change');
            $('#capacityModal').find('#type').val(obj.message.type).trigger('change');
            $('#capacityModal').find('#price').val(obj.message.price);
            $('#capacityModal').modal('show');
            
            $('#capacityForm').validate({
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
        $.post('php/deleteProducts.php', {userID: id}, function(data){
            var obj = JSON.parse(data);
            
            if(obj.status === 'success'){
                toastr["success"](obj.message, "Success:");
                $('#capacityTable').DataTable().ajax.reload(null, false);
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
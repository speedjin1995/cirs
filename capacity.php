<?php
require_once 'php/db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
  echo '<script type="text/javascript">';
  echo 'window.location.href = "login.php";</script>';
}
else{
  $user = $_SESSION['userID'];
  $_SESSION['page']='capacity';
  $units = $db->query("SELECT * FROM units WHERE deleted = '0'");
  $units2 = $db->query("SELECT * FROM units WHERE deleted = '0'");
  $units3 = $db->query("SELECT * FROM units WHERE deleted = '0'");
  $units4 = $db->query("SELECT * FROM units WHERE deleted = '0'");
}
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0 text-dark">Capacity</h1>
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
                                <button type="button" class="btn btn-block bg-gradient-warning btn-sm" id="addCapacity">Add Capacity</button>
                            </div>
                        </div>
                    </div>
					<div class="card-body">
						<table id="capacityTable" class="table table-bordered table-striped">
							<thead>
								<tr>
                                    <th>No.</th>
									<th>Name</th>
									<th>Weight Range</th>
                                    <th>Capacity</th>
                                    <th>Units</th>
                                    <th>Division</th>
                                    <th>Units</th>
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
                <h4 class="modal-title">Add Capacity</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <input type="hidden" class="form-control" id="id" name="id">
                <div class="form-group">
                    <label>Range Type *</label>
                    <select class="form-control" style="width: 100%;" id="range_type" name="range_type" required>
                        <option value="SINGLE" selected="selected">SINGLE</option>
                        <option value="MULTI">MULTI</option>
                    </select>
                </div>
                <div class="row">
                    <div class="col-6">
                        <div class="card card-primary">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="capacity">Capacity *</label>
                                    <input type="number" class="form-control" name="capacity" id="capacity" placeholder="Enter Capacity" required>
                                </div>
                                <div class="form-group">
                                    <label>Units *</label>
                                    <select class="form-control select2" style="width: 100%;" id="unit" name="unit" required>
                                        <?php while($rowVA=mysqli_fetch_assoc($units)){ ?>
                                            <option value="<?=$rowVA['id'] ?>"><?=$rowVA['units'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="division">Division *</label>
                                    <input type="number" class="form-control" name="division" id="division" placeholder="Enter Division" required>
                                </div>
                                <div class="form-group">
                                    <label>Units *</label>
                                    <select class="form-control select2" style="width: 100%;" id="unitD" name="unitD" required>
                                        <?php while($rowVA2=mysqli_fetch_assoc($units2)){ ?>
                                            <option value="<?=$rowVA2['id'] ?>"><?=$rowVA2['units'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="card card-primary" id="rangeDiv" style="display:none;">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="capacity">Capacity 2 *</label>
                                    <input type="number" class="form-control" name="capacity2" id="capacity2" placeholder="Enter Capacity" >
                                </div>
                                <div class="form-group">
                                    <label>Units 2 *</label>
                                    <select class="form-control select2" style="width: 100%;" id="unit2" name="unit2" >
                                        <?php while($rowVA3=mysqli_fetch_assoc($units3)){ ?>
                                            <option value="<?=$rowVA3['id'] ?>"><?=$rowVA3['units'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="division">Division 2 *</label>
                                    <input type="number" class="form-control" name="division2" id="division2" placeholder="Enter Division" >
                                </div>
                                <div class="form-group">
                                    <label>Units 2 *</label>
                                    <select class="form-control select2" style="width: 100%;" id="unitD2" name="unitD2" >
                                        <?php while($rowVA4=mysqli_fetch_assoc($units4)){ ?>
                                            <option value="<?=$rowVA4['id'] ?>"><?=$rowVA4['units'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
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

    $('#rangeDiv').hide();

    $("#capacityTable").DataTable({
        "responsive": true,
        "autoWidth": false,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'order': [[ 1, 'asc' ]],
        'ajax': {
            'url':'php/loadCapacity.php'
        },
        'columns': [
            { data: 'counter' },
            { data: 'name' },
            { 
                data: 'range_type',
                render: function (data, type, row) {
                    if (row.range_type === 'MULTI') {
                        return 'MULTI - RANGE'; 
                    }else if (row.range_type === 'SINGLE') {
                        return 'SINGLE - RANGE';
                    }
                }
            },
            { 
                data: 'capacity',
                render: function (data, type, row) {
                    if (row.range_type === 'MULTI') {
                        return row.capacity + '<br>' + row.capacity2;  // Display capacity and capacity2 on separate lines
                    }
                    return row.capacity;  // Default if range_type is not 'multi'
                }
            },
            { 
                data: 'units',
                render: function (data, type, row) {
                    if (row.range_type === 'MULTI') {
                        return row.units + '<br>' + row.units2;
                    }
                    return row.units;
                }
            },
            { 
                data: 'division',
                render: function (data, type, row) {
                    if (row.range_type === 'MULTI') {
                        return row.division + '<br>' + row.division2;
                    }
                    return row.division;
                }
            },
            { 
                data: 'division_unit',
                render: function (data, type, row) {
                    if (row.range_type === 'MULTI') {
                        return row.division_unit + '<br>' + row.division_unit2;
                    }
                    return row.division_unit;
                }
            },
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
        "rowCallback": function( row, data, index ) {
            $('td', row).css('background-color', '#E6E6FA');
        },        
    });
    
    $.validator.setDefaults({
        submitHandler: function () {
            $('#spinnerLoading').show();
            $.post('php/capacity.php', $('#capacityForm').serialize(), function(data){
                var obj = JSON.parse(data); 
                
                if(obj.status === 'success'){
                    $('#capacityModal').modal('hide');
                    toastr["success"](obj.message, "Success:");
                    $('#capacityTable').DataTable().ajax.reload();
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
        $('#capacityModal').find('#range_type').val("SINGLE").trigger('change');
        $('#capacityModal').find('#capacity').val("");
        $('#capacityModal').find('#unit').val("").trigger('change');
        $('#capacityModal').find('#division').val("");
        $('#capacityModal').find('#unitD').val("").trigger('change');
        $('#capacityModal').find('#capacity2').val("");
        $('#capacityModal').find('#unit2').val("").trigger('change');
        $('#capacityModal').find('#division2').val("");
        $('#capacityModal').find('#unitD2').val("").trigger('change');
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

    $('#range_type').on('change', function(){
        if($(this).val() == 'SINGLE'){
            $('#rangeDiv').hide();
        }
        else{
            $('#rangeDiv').show();
        }
    });
});

function edit(id){
    $('#spinnerLoading').show();
    $.post('php/getCapacity.php', {userID: id}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
            $('#capacityModal').find('#id').val(obj.message.id);
            $('#capacityModal').find('#range_type').val(obj.message.range_type).trigger('change');
            $('#capacityModal').find('#capacity').val(obj.message.capacity);
            $('#capacityModal').find('#unit').val(obj.message.units).trigger('change');
            $('#capacityModal').find('#division').val(obj.message.division);
            $('#capacityModal').find('#unitD').val(obj.message.division_unit).trigger('change');
            $('#capacityModal').find('#capacity2').val(obj.message.capacity2);
            $('#capacityModal').find('#unit2').val(obj.message.units2).trigger('change');
            $('#capacityModal').find('#division2').val(obj.message.division2);
            $('#capacityModal').find('#unitD2').val(obj.message.division_unit2).trigger('change');
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
        $.post('php/deleteCapacity.php', {userID: id}, function(data){
            var obj = JSON.parse(data);
            
            if(obj.status === 'success'){
                toastr["success"](obj.message, "Success:");
                $('#capacityTable').DataTable().ajax.reload();
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
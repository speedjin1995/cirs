<?php
require_once 'php/db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
  echo '<script type="text/javascript">';
  echo 'window.location.href = "login.html";</script>';
}
else{
  $user = $_SESSION['userID'];
  $_SESSION['page']='price';
  $machinetypes = $db->query("SELECT * FROM machines WHERE deleted = '0'");
  $alats = $db->query("SELECT * FROM alat WHERE deleted = '0'");
  $capacities = $db->query("SELECT * FROM capacity WHERE deleted = '0'");
  $validators = $db->query("SELECT * FROM validators WHERE deleted = '0'");
}
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0 text-dark">Products</h1>
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
                                <button type="button" class="btn btn-block bg-gradient-warning btn-sm" id="addCapacity">Add Products</button>
                            </div>
                        </div>
                    </div>
					<div class="card-body">
						<table id="capacityTable" class="table table-bordered table-striped">
							<thead>
								<tr>
                                    <th>No.</th>
									<th>Name</th>
                                    <th>Machine Type</th>
                                    <th>Jenis Alat</th>
                                    <th>Capacity</th>
                                    <th>Validator</th>
                                    <th>Price</th>
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
                <h4 class="modal-title">Add Products</h4>
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
                    </div>
                    <div class="form-group">
                        <label>Jenis Alat </label>
                        <select class="form-control" style="width: 100%;" id="jenisAlat" name="jenisAlat">
                            <option selected="selected">-</option>
                            <?php while($rowA=mysqli_fetch_assoc($alats)){ ?>
                                <option value="<?=$rowA['id'] ?>"><?=$rowA['alat'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Capacity </label>
                        <select class="form-control" style="width: 100%;" id="capacity" name="capacity">
                            <option selected="selected">-</option>
                            <?php while($rowCA=mysqli_fetch_assoc($capacities)){ ?>
                                <option value="<?=$rowCA['id'] ?>"><?=$rowCA['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Validator </label>
                        <select class="form-control" style="width: 100%;" id="validator" name="validator">
                            <option selected="selected">-</option>
                            <?php while($rowVA=mysqli_fetch_assoc($validators)){ ?>
                                <option value="<?=$rowVA['id'] ?>"><?=$rowVA['validator'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="capacity">Type *</label>
                        <select class="form-control" style="width: 100%;" id="type" name="type">
                            <option selected="selected">-</option>
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
    $("#capacityTable").DataTable({
        "responsive": true,
        "autoWidth": false,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'order': [[ 1, 'asc' ]],
        'ajax': {
            'url':'php/loadProducts.php'
        },
        'columns': [
            { data: 'counter' },
            { data: 'name' },
            { data: 'machine_type' },
            { data: 'alat' },
            { data: 'capacity' },
            { data: 'validator' },
            { data: 'price' },
            { 
                data: 'id',
                render: function ( data, type, row ) {
                    return '<div class="row"><div class="col-3"><button type="button" id="edit'+data+'" onclick="edit('+data+')" class="btn btn-success btn-sm"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" id="deactivate'+data+'" onclick="deactivate('+data+')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></div></div>';
                }
            }
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
        $('#capacityModal').find('#capacityName').val("");
        $('#capacityModal').find('#machineType').val("");
        $('#capacityModal').find('#jenisAlat').val("");
        $('#capacityModal').find('#capacity').val("");
        $('#capacityModal').find('#validator').val("");
        $('#capacityModal').find('#type').val("FIXED");
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
});

function edit(id){
    $('#spinnerLoading').show();
    $.post('php/getProducts.php', {userID: id}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
            $('#capacityModal').find('#id').val(obj.message.id);
            $('#capacityModal').find('#capacityName').val(obj.message.name);
            $('#capacityModal').find('#machineType').val(obj.message.machine_type);
            $('#capacityModal').find('#jenisAlat').val(obj.message.jenis_alat);
            $('#capacityModal').find('#capacity').val(obj.message.capacity);
            $('#capacityModal').find('#validator').val(obj.message.validator);
            $('#capacityModal').find('#type').val(obj.message.type);
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
</script>
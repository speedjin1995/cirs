<?php
require_once 'php/db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
  echo '<script type="text/javascript">';
  echo 'window.location.href = "login.html";</script>';
}
else{
  $user = $_SESSION['userID'];
  $_SESSION['page']='standards';
  $capacities = $db->query("SELECT * FROM capacity WHERE deleted = '0'");
  $units = $db->query("SELECT * FROM units WHERE deleted = '0'");
}
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0 text-dark">Value of Standards</h1>
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
                                <button type="button" class="btn btn-block bg-gradient-warning btn-sm" id="addCapacity">Add Value of Standards</button>
                            </div>
                        </div>
                    </div>
					<div class="card-body">
						<table id="capacityTable" class="table table-bordered table-striped">
							<thead>
								<tr>
                                    <th rowspan="2">Std. Avg. <br>Temperature</th>
									<th rowspan="2">Relative <br>Humidity</th>
                                    <th rowspan="2">Capacity</th>
                                    <th rowspan="2">Unit</th>
                                    <th rowspan="2">Variance</th>
                                    <th colspan="10">Tester</th>
									<th rowspan="2">Actions</th>
								</tr>
                                <tr>
                                    <th>1</th>
                                    <th>2</th>
                                    <th>3</th>
                                    <th>4</th>
                                    <th>5</th>
                                    <th>6</th>
                                    <th>7</th>
                                    <th>8</th>
                                    <th>9</th>
                                    <th>10</th>
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
                <h4 class="modal-title">Add Value of Standards</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <input type="hidden" class="form-control" id="id" name="id">

                    <div class="row">
                        <div class="form-group col-4">
                            <label for="satemperature">Standard Average Temperature *</label>
                            <input type="text" class="form-control" name="satemperature" id="satemperature" placeholder="Enter Standard Average Temperature" required>
                        </div>
                        <div class="form-group col-4">
                            <label for="capacity">Relative Humidity</label>
                            <input type="text" class="form-control" name="relHumidity" id="relHumidity" placeholder="Enter Relative Humidity">
                        </div>
                        <div class="form-group col-4">
                            <label>Capacity *</label>
                            <select class="form-control" style="width: 100%;" id="capacity" name="capacity" required>
                                <option selected="selected">-</option>
                                <?php while($rowCA=mysqli_fetch_assoc($capacities)){ ?>
                                    <option value="<?=$rowCA['id'] ?>"><?=$rowCA['name'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group col-4">
                            <label>Units *</label>
                            <select class="form-control" style="width: 100%;" id="units" name="units" required>
                                <option selected="selected">-</option>
                                <?php while($rowVA2=mysqli_fetch_assoc($units)){ ?>
                                    <option value="<?=$rowVA2['id'] ?>"><?=$rowVA2['units'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group col-4">
                            <label for="variance">Variance +/- *</label>
                            <input type="number" class="form-control" name="variance" id="variance" placeholder="Enter Relative Humidity" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-1"></div>
                        <div class="form-group col-2">
                            <label for="tester1">Tester/Time 1 *</label>
                            <input type="number" class="form-control" name="tester1" id="tester1" placeholder="Tester/Time 1" required>
                        </div>
                        <div class="form-group col-2">
                            <label for="tester2">Tester/Time 2 *</label>
                            <input type="number" class="form-control" name="tester2" id="tester2" placeholder="Tester/Time 2" required>
                        </div>
                        <div class="form-group col-2">
                            <label for="tester3">Tester/Time 3 *</label>
                            <input type="number" class="form-control" name="tester3" id="tester3" placeholder="Tester/Time 3" required>
                        </div>
                        <div class="form-group col-2">
                            <label for="tester4">Tester/Time 4 *</label>
                            <input type="number" class="form-control" name="tester4" id="tester4" placeholder="Tester/Time 4" required>
                        </div>
                        <div class="form-group col-2">
                            <label for="tester5">Tester/Time 5 *</label>
                            <input type="number" class="form-control" name="tester5" id="tester5" placeholder="Tester/Time 5" required>
                        </div>
                        <div class="col-1"></div>
                    </div>
                    <div class="row">
                        <div class="col-1"></div>
                        <div class="form-group col-2">
                            <label for="tester6">Tester/Time 6 *</label>
                            <input type="number" class="form-control" name="tester6" id="tester6" placeholder="Tester/Time 6" required>
                        </div>
                        <div class="form-group col-2">
                            <label for="tester7">Tester/Time 7 *</label>
                            <input type="number" class="form-control" name="tester7" id="tester7" placeholder="Tester/Time 7" required>
                        </div>
                        <div class="form-group col-2">
                            <label for="tester8">Tester/Time 8 *</label>
                            <input type="number" class="form-control" name="tester8" id="tester8" placeholder="Tester/Time 8" required>
                        </div>
                        <div class="form-group col-2">
                            <label for="tester9">Tester/Time 9 *</label>
                            <input type="number" class="form-control" name="tester9" id="tester9" placeholder="Tester/Time 9" required>
                        </div>
                        <div class="form-group col-2">
                            <label for="tester10">Tester/Time 10 *</label>
                            <input type="number" class="form-control" name="tester10" id="tester10" placeholder="Tester/Time 10" required>
                        </div>
                        <div class="col-1"></div>
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
        'order': [[ 0, 'asc' ]],
        'ajax': {
            'url':'php/loadStandards.php'
        },
        'columns': [
            { data: 'standard_avg_temp' },
            { data: 'relative_humidity' },
            { data: 'name' },
            { data: 'units' },
            { data: 'variance' },
            { data: 'test_1' },
            { data: 'test_2' },
            { data: 'test_3' },
            { data: 'test_4' },
            { data: 'test_5' },
            { data: 'test_6' },
            { data: 'test_7' },
            { data: 'test_8' },
            { data: 'test_9' },
            { data: 'test_10' },
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
            $.post('php/standards.php', $('#capacityForm').serialize(), function(data){
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
        $('#capacityModal').find('#satemperature').val("");
        $('#capacityModal').find('#relHumidity').val("");
        $('#capacityModal').find('#capacity').val("");
        $('#capacityModal').find('#units').val("");
        $('#capacityModal').find('#variance').val("");
        $('#capacityModal').find('#tester1').val("");
        $('#capacityModal').find('#tester2').val("");
        $('#capacityModal').find('#tester3').val("");
        $('#capacityModal').find('#tester4').val("");
        $('#capacityModal').find('#tester5').val("");
        $('#capacityModal').find('#tester6').val("");
        $('#capacityModal').find('#tester7').val("");
        $('#capacityModal').find('#tester8').val("");
        $('#capacityModal').find('#tester9').val("");
        $('#capacityModal').find('#tester10').val("");
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
    $.post('php/getStandards.php', {userID: id}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
            $('#capacityModal').find('#id').val(obj.message.id);
            $('#capacityModal').find('#satemperature').val(obj.message.standard_avg_temp);
            $('#capacityModal').find('#relHumidity').val(obj.message.relative_humidity);
            $('#capacityModal').find('#capacity').val(obj.message.capacity);
            $('#capacityModal').find('#units').val(obj.message.unit);
            $('#capacityModal').find('#variance').val(obj.message.variance);
            $('#capacityModal').find('#tester1').val(obj.message.test_1);
            $('#capacityModal').find('#tester2').val(obj.message.test_2);
            $('#capacityModal').find('#tester3').val(obj.message.test_3);
            $('#capacityModal').find('#tester4').val(obj.message.test_4);
            $('#capacityModal').find('#tester5').val(obj.message.test_5);
            $('#capacityModal').find('#tester6').val(obj.message.test_6);
            $('#capacityModal').find('#tester7').val(obj.message.test_7);
            $('#capacityModal').find('#tester8').val(obj.message.test_8);
            $('#capacityModal').find('#tester9').val(obj.message.test_9);
            $('#capacityModal').find('#tester10').val(obj.message.test_10);
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
    $.post('php/deleteStandards.php', {userID: id}, function(data){
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
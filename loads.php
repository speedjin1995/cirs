<?php
require_once 'php/db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
  echo '<script type="text/javascript">';
  echo 'window.location.href = "login.html";</script>';
}
else{
  $user = $_SESSION['userID'];
  $_SESSION['page']='loads';
  $machinetypes = $db->query("SELECT * FROM machines WHERE deleted = '0'");
  $brands = $db->query("SELECT * FROM brand WHERE deleted = '0'");
  $models = $db->query("SELECT * FROM model WHERE deleted = '0'");
  $alats = $db->query("SELECT * FROM alat WHERE deleted = '0'");
  $country = $db->query("SELECT * FROM country");

  $machinetypes2 = $db->query("SELECT * FROM machines WHERE deleted = '0'");
  $brands2 = $db->query("SELECT * FROM brand WHERE deleted = '0'");
  $models2 = $db->query("SELECT * FROM model WHERE deleted = '0'");
  $alats2 = $db->query("SELECT * FROM alat WHERE deleted = '0'");
  $country2 = $db->query("SELECT * FROM country");
  $capacity2 = $db->query("SELECT * FROM capacity");
}
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0 text-dark">Loads Cell Type</h1>
			</div>
        </div>
    </div>
</div>
<!-- /.content-header -->

<!-- Main content -->
<section class="content">
	<div class="container-fluid">
        <!-- <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-4">
                                <label>Machine/Scale Type</label>
                                <select class="form-control select2" id="machineTypeFilter" name="machineTypeFilter">
                                    <option value="" selected disabled hidden>Please Select</option>
                                    <?php while($rowM=mysqli_fetch_assoc($machinetypes)){ ?>
                                        <option value="<?=$rowM['id'] ?>"><?=$rowM['machine_type'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group col-4">
                                <label>Brand</label>
                                <select class="form-control select2" id="brandFilter" name="brandFilter">
                                    <option value="" selected disabled hidden>Please Select</option>
                                    <?php while($rowB=mysqli_fetch_assoc($brands)){ ?>
                                        <option value="<?=$rowB['id'] ?>"><?=$rowB['brand'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group col-4">
                                <label>Model</label>
                                <select class="form-control select2" id="modelFilter" name="modelFilter">
                                    <option value="" selected disabled hidden>Please Select</option>
                                    <?php while($rowM=mysqli_fetch_assoc($models)){ ?>
                                        <option value="<?=$rowM['id'] ?>"><?=$rowM['model'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group col-4">
                                <label>Jenis Alat</label>
                                <select class="form-control select2" id="jenisAlatFilter" name="jenisAlatFilter">
                                    <option value="" selected disabled hidden>Please Select</option>
                                    <?php while($rowA=mysqli_fetch_assoc($alats)){ ?>
                                        <option value="<?=$rowA['id'] ?>"><?=$rowA['alat'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group col-4">
                                <label>Made In</label>
                                <select class="form-control select2" id="madeInFilter" name="madeInFilter">
                                    <option value="" selected disabled hidden>Please Select</option>
                                    <?php while($rowC=mysqli_fetch_assoc($country)){ ?>
                                        <option value="<?=$rowC['id'] ?>"><?=$rowC['name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="form-group col-4">
                                <label>SIRIM Pattern Approval No.</label>
                                <input class="form-control" type="text" placeholder="SIRIM Pattern Approval No." id="patternNoFilter" name="patternNoFilter" />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-9"></div>
                            <div class="col-3">
                                <button type="button" class="btn btn-block bg-gradient-warning btn-sm"  id="filterSearch">
                                    <i class="fas fa-search"></i>
                                    Search
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div> -->
        <div class="row">
			<div class="col-12">
				<div class="card mt-4">
					<div class="card-header">
                        <div class="row">
                            <div class="col-9">
                                <p>Loads Cell Type</p>
                            </div>
                            <div class="col-3">
                                <button type="button" class="btn btn-block bg-gradient-warning btn-sm" id="addBrand">Add New</button>
                            </div>
                        </div>
                    </div>
					<div class="card-body">
                        <table id="brandTable" class="table table-bordered table-striped">
							<thead>
								<tr>
									<th>No.</th>
									<!--th>Part No.</th-->
									<th>Load Cell Type</th>
									<th>Load Cell Brand</th>
                                    <th>Load Cell Model</th>
                                    <th>Load Cell Capacity</th>
                                    <th>Load Cell Made In</th>
                                    <th>Class</th>
                                    <th>SIRIM Pattern <br>Approval No.</th>
                                    <th>SIRIM Pattern <br>Approval Date</th>
                                    <th>SIRIM Pattern <br>Expiry Date</th>
                                    <th>Attach <br>Certificate</th>
                                    <th>Action</th>
								</tr>
							</thead>
						</table>
					</div><!-- /.card-body -->
				</div><!-- /.card -->
			</div><!-- /.col -->
		</div><!-- /.row -->
	</div><!-- /.container-fluid -->
</section><!-- /.content -->

<div class="modal fade" id="brandModal">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <form role="form" id="brandForm">
            <div class="modal-header">
              <h4 class="modal-title">Add Load Cells</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
                <div class="card-body">
                    <input type="hidden" class="form-control" id="id" name="id">
                    <div class="row">
                        <div class="form-group col-8">
                            <label for="brand">Load Cell Type *</label>
                            <input type="text" class="form-control" name="loadCell" id="loadCell" placeholder="Enter Load Cell Type" required>
                        </div>
                        <!--div class="form-group col-4">
                            <label for="brand">Part No. *</label>
                            <input type="text" class="form-control" name="partNo" id="partNo" placeholder="Enter Part No" required>
                        </div-->
                    </div>
    				
                    <div class="row">
                        <div class="form-group col-4">
                            <label>Load Cell Model *</label>
                            <select class="form-control select2" id="model" name="model" required>
                                <option value="" selected disabled hidden>Please Select</option>
                                <?php while($rowM2=mysqli_fetch_assoc($models2)){ ?>
                                    <option value="<?=$rowM2['id'] ?>"><?=$rowM2['model'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group col-4">
                            <label>Load Cell Capacity *</label>
                            <input class="form-control" type="text" placeholder="capacity" id="capacity" name="capacity" required/>
                        </div>
                        <div class="form-group col-4">
                            <label>Load Cell Made In *</label>
                            <select class="form-control select2" id="madeIn" name="madeIn" required>
                                <option value="" selected disabled hidden>Please Select</option>
                                <?php while($rowC2=mysqli_fetch_assoc($country2)){ ?>
                                    <option value="<?=$rowC2['id'] ?>"><?=$rowC2['name'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-4">
                            <label>Load Cell Brand *</label>
                            <select class="form-control select2" id="brand" name="brand" required>
                                <option value="" selected disabled hidden>Please Select</option>
                                <?php while($rowB2=mysqli_fetch_assoc($brands2)){ ?>
                                    <option value="<?=$rowB2['id'] ?>"><?=$rowB2['brand'] ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group col-4">
                            <label>Load Cell Class *</label>
                            <input class="form-control" type="text" placeholder="Class" id="class" name="class" required/>
                        </div>

                        <div class="form-group col-4">
                            <label>OIML Approval *</label>
                            <select class="form-control select2" id="oimlApproval" name="oimlApproval" required>
                                <option value="" selected disabled hidden>Please Select</option>
                                <option value="Y">YES</option>
                                <option value="N">NO</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="form-group col-4">
                            <label>SIRIM Pattern Approval No. *</label>
                            <input class="form-control" type="text" placeholder="SIRIM Pattern Approval No." id="patternNo" name="patternNo" required/>
                        </div>
                        <div class="form-group col-4">
                            <label>SIRIM Pattern Approval Date *</label>
                            <div class="input-group date" id="approvalDate" data-target-input="nearest">
                                <input type="text" class="form-control datetimepicker-input" data-target="#approvalDate" id="approval_date" name="approvalDate" required/>
                                <div class="input-group-append" data-target="#approvalDate" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-4">
                            <label>SIRIM Pattern Expiry Date *</label>
                            <div class="input-group date" id="expiryDate" data-target-input="nearest">
                                <input type="text" class="form-control datetimepicker-input" data-target="#expiryDate" id="expiry_date" name="expiryDate" required/>
                                <div class="input-group-append" data-target="#expiryDate" data-toggle="datetimepicker">
                                    <div class="input-group-text"><i class="fa fa-calendar"></i></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="form-group col-12">
                            <label>Attach SIRIM Pattern Certificate *</label>
                            <input class="form-control" type="file" placeholder="Attach SIRIM Pattern Certificate" id="certificate" name="certificate" required/>
                        </div>
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
    const today = new Date();
    const tomorrow = new Date(today);
    const yesterday = new Date(today);
    tomorrow.setDate(tomorrow.getDate() + 1);
    yesterday.setDate(tomorrow.getDate() - 7);

    var machineTypeFilter = $('#machineTypeFilter').val() ? $('#machineTypeFilter').val() : '';
    var brandFilter = $('#brandFilter').val() ? $('#brandFilter').val() : '';
    var modelFilter = $('#modelFilter').val() ? $('#modelFilter').val() : '';
    var jenisAlatFilter = $('#jenisAlatFilter').val() ? $('#jenisAlatFilter').val() : '';
    var madeInFilter = $('#madeInFilter').val() ? $('#madeInFilter').val() : '';
    var patternNoFilter = $('#patternNoFilter').val() ? $('#patternNoFilter').val() : '';

    var table = $("#brandTable").DataTable({
        "responsive": true,
        "autoWidth": false,
        'processing': true,
        'serverSide': true,
        'searching': true,
        'serverMethod': 'post',
        'order': [[ 1, 'asc' ]],
        'columnDefs': [ { orderable: false, targets: [0] }],
        'ajax': {
            'type': 'POST',
            'url':'php/filterLoadCells.php',
            'data': {
                // machineType: machineTypeFilter,
                // brand: brandFilter,
                // model: modelFilter,
                // jenisAlat: jenisAlatFilter,
                // madeIn: madeInFilter,
                // patternNo: patternNoFilter,
            } 
        },
        'columns': [
            { data: 'no' },
            //{ data: 'part_no' },
            { data: 'load_cell' },
            { data: 'brand_name' },
            { data: 'model_name' },
            { data: 'capacity' },
            { data: 'made_in' },
            { data: 'class' },
            { data: 'pattern_no' },
            { data: 'pattern_datetime' },
            { data: 'pattern_expiry' },
            {
				data: 'certificate',    // Attach PDF
				render: function (data, type, row) {
					return data; // Render the HTML as is
				}
			},
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

    // Bind form submission handler once
	$('#brandForm').off('submit').on('submit', function(e) {
        $('#spinnerLoading').show();
		e.preventDefault(); 
		var formData = new FormData(this); 
		$.ajax({
			url: 'php/loads.php',
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			success: function(data) {
				var obj = JSON.parse(data); 
				if (obj.status === 'success') {
					$('#brandModal').modal('hide');
					toastr["success"](obj.message, "Success:");
					location.reload(); // Reload the page
				} else {
					toastr["error"](obj.message, "Failed:");
				}
				$('#spinnerLoading').hide();
				// isModalOpen = false; // Set flag to false on error as well
			},
			error: function(xhr, status, error) {
				console.error("AJAX request failed:", status, error);
				toastr["error"]("An error occurred while processing the request.", "Failed:");
				$('#spinnerLoading').hide();
				// isModalOpen = false; // Set flag to false on error as well
			}
		});
	});
    
    // $.validator.setDefaults({
    //     submitHandler: function () {
    //         $('#spinnerLoading').show();
    //         $.post('php/loads.php', $('#brandForm').serialize(), function(data){
    //             var obj = JSON.parse(data); 
                
    //             if(obj.status === 'success'){
    //                 $('#brandModal').modal('hide');
    //                 toastr["success"](obj.message, "Success:");
    //                 $('#brandTable').DataTable().ajax.reload();
    //                 $('#spinnerLoading').hide();
    //             }
    //             else if(obj.status === 'failed'){
    //                 toastr["error"](obj.message, "Failed:");
    //                 $('#spinnerLoading').hide();
    //             }
    //             else{
    //                 toastr["error"]("Something wrong when edit", "Failed:");
    //                 $('#spinnerLoading').hide();
    //             }
    //         });
    //     }
    // });

    $('#filterSearch').on('click', function(){
        var machineTypeFilter = $('#machineTypeFilter').val() ? $('#machineTypeFilter').val() : '';
        var brandFilter = $('#brandFilter').val() ? $('#brandFilter').val() : '';
        var modelFilter = $('#modelFilter').val() ? $('#modelFilter').val() : '';
        var jenisAlatFilter = $('#jenisAlatFilter').val() ? $('#jenisAlatFilter').val() : '';
        var madeInFilter = $('#madeInFilter').val() ? $('#madeInFilter').val() : '';
        var patternNoFilter = $('#patternNoFilter').val() ? $('#patternNoFilter').val() : '';

        //Destroy the old Datatable
        $("#weightTable").DataTable().clear().destroy();

        //Create new Datatable
        table = $("#brandTable").DataTable({
            "responsive": true,
            "autoWidth": false,
            'processing': true,
            'serverSide': true,
            'searching': false,
            'serverMethod': 'post',
            'order': [[ 1, 'asc' ]],
            'columnDefs': [ { orderable: false, targets: [0] }],
            'ajax': {
                'type': 'POST',
                'url':'php/filterLoadCells.php',
                'data': {
                    machineType: machineTypeFilter,
                    brand: brandFilter,
                    model: modelFilter,
                    jenisAlat: jenisAlatFilter,
                    madeIn: madeInFilter,
                    patternNo: patternNoFilter,
                } 
            },
            'columns': [
                { data: 'no' },
                { data: 'load_cell' },
                { data: 'brand_name' },
                { data: 'model_name' },
                { data: 'alat' },
                { data: 'nicename' },
                { data: 'class' },
                { data: 'pattern_no' },
                { data: 'pattern_datetime' },
                { data: 'pattern_expiry' },
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
    });

    $('#approvalDate').datetimepicker({
        icons: { time: 'far fa-calendar' },
        format: 'DD/MM/YYYY',
        defaultDate: today
    });

    $('#expiryDate').datetimepicker({
        icons: { time: 'far fa-calendar' },
        format: 'DD/MM/YYYY',
        defaultDate: today
    });

    $('#addBrand').on('click', function(){
        $('#brandModal').find('#id').val("");
        $('#brandModal').find('#machineType').val('').trigger('change');
        $('#brandModal').find('#brand').val("").trigger('change');
        $('#brandModal').find('#model').val('').trigger('change');
        $('#brandModal').find('#jenisAlat').val('').trigger('change');
        $('#brandModal').find('#madeIn').val('').trigger('change');
        $('#brandModal').find('#patternNo').val('');
        $('#brandModal').find('#class').val('');
        $('#brandModal').find('#approval_date').val(formatDate3(today));
        $('#brandModal').find('#expiry_date').val(formatDate3(today));
        $('#brandModal').modal('show');
        
        $('#brandForm').validate({
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
    $.post('php/getLoadCell.php', {userID: id}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
            $('#brandModal').find('#id').val(obj.message.id);
            $('#brandModal').find('#loadCell').val(obj.message.load_cell);
            $('#brandModal').find('#partNo').val(obj.message.part_no);
            $('#brandModal').find('#model').val(obj.message.model).trigger('change');
            $('#brandModal').find('#capacity').val(obj.message.capacity).trigger('change');
            $('#brandModal').find('#madeIn').val(obj.message.made_in).trigger('change');
            $('#brandModal').find('#brand').val(obj.message.brand).trigger('change');
            $('#brandModal').find('#class').val(obj.message.class);
            $('#brandModal').find('#oimlApproval').val(obj.message.oiml_approval);
            $('#brandModal').find('#patternNo').val(obj.message.pattern_no);
            $('#brandModal').find('#approval_date').val(formatDate3(obj.message.pattern_datetime));
            $('#brandModal').find('#expiry_date').val(formatDate3(obj.message.pattern_expiry));
            $('#brandModal').find('#certificate').removeAttr('required');

            $('#brandModal').modal('show');
            
            $('#brandForm').validate({
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
        $.post('php/deleteLoadCell.php', {userID: id}, function(data){
            var obj = JSON.parse(data);
            
            if(obj.status === 'success'){
                toastr["success"](obj.message, "Success:");
                $('#brandTable').DataTable().ajax.reload();
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
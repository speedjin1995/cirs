<?php
require_once 'php/db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
    echo '<script type="text/javascript">';
    echo 'window.location.href = "login.php";</script>';
}
else{
    $id = '1';
    $_SESSION['page']='company';
    $stmt = $db->prepare("SELECT * from companies where id = ?");
	$stmt->bind_param('s', $id);
	$stmt->execute();
	$result = $stmt->get_result();
    $name = '';
	$address = '';
	$phone = '';
	$email = '';
	
	if(($row = $result->fetch_assoc()) !== null){
        $data = $row;
        // $name = $row['name'];
        // $address = $row['address'];
        // $phone = $row['phone'];
        // $email = $row['email'];
		$filepath = $row['signature'];
		$inhouseFilePath = $row['inhouse'];
    }
}
?>

<section class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0 text-dark"><b>Company Profile</b></h1>
			</div>
		</div>
	</div>
</section>

<section class="content" style="min-height:700px;">
	<div class="card">
		<form role="form" id="profileForm" novalidate="novalidate">
			<div class="card-body">
				<div class="row">
					<div class="form-group col-6">
						<label for="new_roc">Company NEW. ROC. *</label>
						<input type="text" class="form-control" id="new_roc" name="new_roc" value="<?=$data['new_roc'] ?>" placeholder="Enter Company NEW. ROC." required="">
					</div>
					
					<div class="form-group col-6">
						<label for="old_roc">Company OLD. ROC.</label>
						<input type="text" class="form-control" id="old_roc" name="old_roc" value="<?=$data['old_roc'] ?>" placeholder="Enter Company OLD. ROC.">
					</div>

					<div class="form`-group col-6">
						<label for="name">Company Name *</label>
						<input type="text" class="form-control" id="name" name="name" value="<?=$data['name'] ?>" placeholder="Enter Company Name" required="">
					</div>
					
					<div class="form-group col-6">
						<label for="address">Company Address *</label>
						<textarea class="form-control" name="address" id="address" rows="3" placeholder="Enter Address" required=""><?=$data['address'] ?></textarea>
					</div>

					<div class="form-group col-6">
						<label for="phone">Company Phone </label>
						<input type="text" class="form-control" id="phone" name="phone" value="<?=$data['phone'] ?>" placeholder="Enter Phone">
					</div>

					<div class="form-group col-6">
						<label for="fax">Company Fax </label>
						<input type="text" class="form-control" id="fax" name="fax" value="<?=$data['fax'] ?>" placeholder="Enter Fax">
					</div>

					<div class="form-group col-6">
						<label for="person_incharge">Person Incharge </label>
						<input type="text" class="form-control" id="person_incharge" name="person_incharge" value="<?=$data['person_incharge'] ?>" placeholder="Enter Person Incharge">
					</div>

					<div class="form-group col-6">
						<label for="contact_no">Contact No </label>
						<input type="text" class="form-control" id="contact_no" name="contact_no" value="<?=$data['contact_no'] ?>" placeholder="Enter Contact No">
					</div>

					<div class="form-group col-6">
						<label for="email">Email address</label>
						<input type="email" class="form-control" id="email" name="email" value="<?=$data['email'] ?>" placeholder="Enter Email">
					</div>

					<div class="form-group col-6">
						<label for="lesen_type">Lesen Type </label>
						<select class="form-control select2" name="lesen_type" id="lesen_type">
							<option value="" selected disabled hidden>Please Select</option>
							<option value="membuat/membaiki/menjual" <?php if ($data['lesen_type'] == 'membuat/membaiki/menjual') echo 'selected'; ?>>Membuat/Membaiki/Menjual</option>
							<option value="membaiki/menjual" <?php if ($data['lesen_type'] == 'membaiki/menjual') echo 'selected'; ?>>Membaiki/Menjual</option>
							<option value="membuat" <?php if ($data['lesen_type'] == 'membuat') echo 'selected'; ?>>Membuat</option>
							<option value="membaiki" <?php if ($data['lesen_type'] == 'membaiki') echo 'selected'; ?>>Membaiki</option>
						</select>
					</div>

					<div class="form-group col-6">
						<label for="certno_lesen">Certificate No.Lesen </label>
						<input type="text" class="form-control" id="certno_lesen" name="certno_lesen" value="<?=$data['certno_lesen'] ?>" placeholder="Enter Certificate No.Lesen">
					</div>

					<div class="form-group col-6">
						<label for="certats_serialno">Certificate ATS Serial No. </label>
						<input type="text" class="form-control" id="certats_serialno" name="certats_serialno" value="<?=$data['certats_serialno'] ?>" placeholder="Enter Certificate ATS Serial No.">
					</div>

					<div class="form-group col-6">
						<label for="failno">No.Fail </label>
						<input type="text" class="form-control" id="failno" name="failno" value="<?=$data['failno'] ?>" placeholder="Enter No.Fail">
					</div>

					<div class="form-group col-6">
						<label for="bless_serahanno">No. Serahan BLESS </label>
						<input type="text" class="form-control" id="bless_serahanno" name="bless_serahanno" value="<?=$data['bless_serahanno'] ?>" placeholder="Enter No. Serahan BLESS">
					</div>

					<div class="form-group col-6">
						<label for="resitno">No. Resit </label>
						<input type="text" class="form-control" id="resitno" name="resitno" value="<?=$data['resitno'] ?>" placeholder="Enter No. Resit">
					</div>

					<div class="form-group col-6">
						<label for="tarikh_kuatkuasa">Tarikh Kuatkuasa </label>
						<input type="date" class="form-control" id="tarikh_kuatkuasa" name="tarikh_kuatkuasa" value="<?=$data['tarikh_kuatkuasa'] ?>">
					</div>

					<div class="form-group col-6">
						<label for="tarikh_luput">Tarikh Luput </label>
						<input type="date" class="form-control" id="tarikh_luput" name="tarikh_luput" value="<?=$data['tarikh_luput'] ?>">
					</div>

					<div class="form-group col-6">
						<label for="tarikh_dikeluarkan">Tarikh Dikeluarkan </label>
						<input type="date" class="form-control" id="tarikh_dikeluarkan" name="tarikh_dikeluarkan" value="<?=$data['tarikh_dikeluarkan'] ?>">
					</div>

					<div class="form-group col-6">
						<label for="signature">Signature </label>
						<div class="d-flex">
							<div class="col-11">
								<input type="file" class="form-control" id="uploadAttachment" name="uploadAttachment" accept="image/*">
							</div>
							<div class="col-1 mt-1">
								<?php 
									if (isset($data['signature']) && !empty($data['signature'])) {
										echo '<a href="view_file.php?file=' . htmlspecialchars($data['signature'], ENT_QUOTES, 'UTF-8') . '" id="viewSignPdf" name="viewSignPdf" target="_blank" class="btn btn-success btn-sm" role="button"><i class="fa fa-file-pdf-o"></i></a>';
									}else{
										echo '<a href="" id="viewSignPdf" name="viewSignPdf" target="_blank" class="btn btn-success btn-sm" role="button" style="display: none;"><i class="fa fa-file-pdf-o"></i></a>';
									}
								?>
								
							</div>
						</div>
						<input type="text" id="signFilePath" name="signFilePath" value="<?php echo htmlspecialchars($filepath, ENT_QUOTES, 'UTF-8'); ?>" style="display:none;">
					</div>

					<div class="form-group col-6">
						<label for="inhouseHead">Inhouse Letter Head </label>
						<div class="d-flex">
							<div class="col-11">
								<input type="file" class="form-control" id="uploadInhouseAttachment" name="uploadInhouseAttachment" accept="image/*">
							</div>
							<div class="col-1 mt-1">
								<?php 
									if (isset($data['inhouse']) && !empty($data['inhouse'])) {
										echo '<a href="view_file.php?file=' . htmlspecialchars($data['inhouse'], ENT_QUOTES, 'UTF-8') . '" id="viewInhousePdf" name="viewInhousePdf" target="_blank" class="btn btn-success btn-sm" role="button"><i class="fa fa-file-pdf-o"></i></a>';
									}else{
										echo '<a href="" id="viewInhousePdf" name="viewInhousePdf" target="_blank" class="btn btn-success btn-sm" role="button" style="display: none;"><i class="fa fa-file-pdf-o"></i></a>';
									}
								?>
								
							</div>
						</div>
						<input type="text" id="inhouseFilePath" name="inhouseFilePath" value="<?php echo htmlspecialchars($inhouseFilePath, ENT_QUOTES, 'UTF-8'); ?>" style="display:none;">
					</div>
				</div>
			</div>
			
			<div class="card-footer">
				<button class="btn btn-success" id="saveProfile"><i class="fas fa-save"></i> Save</button>
			</div>
		</form>
	</div>

	<div class="row">
      <div class="col-lg-12">
        <div class="card card-primary">
          <div class="card-header">
            <div class="row">
              <div class="col-10"><p><b>Certificate No.Lesen</b></p></div>
              <div class="col-2">
                <button type="button" class="btn btn-block btn-sm" style="background-color: #f4c200; border-color: black;" id="addLesenCert" onclick="newLesenCert('<?php echo $id; ?>');"><b>ADD Details</b></button>
              </div>
            </div>
          </div>

          <div class="card-body">
            <table id="lesenCertTable" class="table table-bordered table-striped display">
              <thead>
                <tr>
					<th>No</th>
					<th>Details</th>
					<th>Serial No</th>
					<th>Approval Date</th>
					<th>Expire Date</th>
					<th>Attach PDF</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>

	<div class="modal fade" id="addLesenCertModal">
		<div class="modal-dialog modal-xl" style="max-width: 40%;">
			<div class="modal-content">
				<form role="form" id="addLesenCertForm" enctype="multipart/form-data">
					<div class="modal-header bg-gray-dark color-palette">
						<h4 class="modal-title"><b>Add Certificate No.Lesen</b></h4>
						<button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">
						<input type="hidden" class="form-control" id="id" name="id">
						<div class="row">
							<div class="col-12">
								<div class="form-group">
									<label>Details *</label>
									<input type="text" class="form-control" id="lesenCertDetail" name="lesenCertDetail" required>
								</div>
							</div>
						</div>  
						<div class="row">
							<div class="col-12">
								<div class="form-group">
									<label>Serial No *</label>
									<input type="text" class="form-control" id="lesenCertSerialNo" name="lesenCertSerialNo" required>
								</div>
							</div>
						</div>  
						<div class="row">
							<div class="col-12">
								<div class="form-group">
									<label>Approval Date *</label>
									<input type="date" class="form-control" id="lesenCertApprDt" name="lesenCertApprDt" required>
								</div>
							</div>
						</div>  
						<div class="row">
							<div class="col-12">
								<div class="form-group">
									<label>Expire Date *</label>
									<input type="date" class="form-control" id="lesenCertExpDt" name="lesenCertExpDt" required>
								</div>
							</div>
						</div>  
						<div class="row">
							<div class="col-12">
								<div class="form-group">
									<label>Upload PDF *</label>
									<input type="file" class="form-control" id="lesenCertPdf" name="lesenCertPdf" required>
								</div>
							</div>
						</div>
					</div>

					<div class="modal-footer justify-content-between bg-gray-dark color-palette">
						<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-primary" id="saveButton">Save changes</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="modal fade" id="editLesenCertModal">
		<div class="modal-dialog modal-xl" style="max-width: 40%;">
			<div class="modal-content">
				<form role="form" id="editLesenCertForm" enctype="multipart/form-data">
					<div class="modal-header bg-gray-dark color-palette">
						<h4 class="modal-title"><b>Add Certificate No.Lesen</b></h4>
						<button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">
						<input type="hidden" class="form-control" id="id" name="id">
						<input type="hidden" class="form-control" id="lesenCertId" name="lesenCertId">
						<input type="hidden" class="form-control" id="lesenCertFilePath" name="lesenCertFilePath">

						<div class="row">
							<div class="col-12">
								<div class="form-group">
									<label>Details *</label>
									<input type="text" class="form-control" id="lesenCertDetail" name="lesenCertDetail" required>
								</div>
							</div>
						</div>  
						<div class="row">
							<div class="col-12">
								<div class="form-group">
									<label>Serial No *</label>
									<input type="text" class="form-control" id="lesenCertSerialNo" name="lesenCertSerialNo" required>
								</div>
							</div>
						</div>  
						<div class="row">
							<div class="col-12">
								<div class="form-group">
									<label>Approval Date *</label>
									<input type="date" class="form-control" id="lesenCertApprDt" name="lesenCertApprDt" required>
								</div>
							</div>
						</div>  
						<div class="row">
							<div class="col-12">
								<div class="form-group">
									<label>Expire Date *</label>
									<input type="date" class="form-control" id="lesenCertExpDt" name="lesenCertExpDt" required>
								</div>
							</div>
						</div>  
						<div class="row">
							<div class="col-12">
								<div class="form-group">
									<label>Upload PDF *</label>
									<input type="file" class="form-control" id="lesenCertPdf" name="lesenCertPdf">
								</div>
							</div>
						</div>
					</div>

					<div class="modal-footer justify-content-between bg-gray-dark color-palette">
						<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-primary" id="saveButton">Save changes</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="row">
      <div class="col-lg-12">
        <div class="card card-primary">
          <div class="card-header"  style="background-color: green;">
            <div class="row">
              <div class="col-10"><p><b>NMIM Pattern Approval Detail / Certificate</b></p></div>
              <div class="col-2">
                <button type="button" class="btn btn-block btn-sm" style="background-color: #f4c200; border-color: black;" id="addNmim" onclick="addNmim('<?php echo $id; ?>');"><b>ADD Details</b></button>
              </div>
            </div>
          </div>

          <div class="card-body">
            <table id="nmimTable" class="table table-bordered table-striped display">
              <thead>
                <tr>
					<th>No</th>
					<th>Details</th>
					<th>NMIM Approval No</th>
					<th>Approval Date</th>
					<th>Expire Date</th>
					<th>Attach PDF</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>

	<div class="modal fade" id="addNmimModal">
		<div class="modal-dialog modal-xl" style="max-width: 40%;">
			<div class="modal-content">
				<form role="form" id="addNmimForm" enctype="multipart/form-data">
					<div class="modal-header bg-gray-dark color-palette">
						<h4 class="modal-title"><b>Add NMIM Pattern Approaval Detail / Certificate</b></h4>
						<button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">
						<input type="hidden" class="form-control" id="id" name="id">
						<div class="row">
							<div class="col-12">
								<div class="form-group">
									<label>Details *</label>
									<input type="text" class="form-control" id="nmimDetail" name="nmimDetail" required>
								</div>
							</div>
						</div>  
						<div class="row">
							<div class="col-12">
								<div class="form-group">
									<label>NMIM Approval No *</label>
									<input type="text" class="form-control" id="nmimApprNo" name="nmimApprNo" required>
								</div>
							</div>
						</div>  
						<div class="row">
							<div class="col-12">
								<div class="form-group">
									<label>Approval Date *</label>
									<input type="date" class="form-control" id="nmimApprDt" name="nmimApprDt" required>
								</div>
							</div>
						</div>  
						<div class="row">
							<div class="col-12">
								<div class="form-group">
									<label>Expire Date *</label>
									<input type="date" class="form-control" id="nmimExpDt" name="nmimExpDt" required>
								</div>
							</div>
						</div>  
						<div class="row">
							<div class="col-12">
								<div class="form-group">
									<label>Upload PDF *</label>
									<input type="file" class="form-control" id="nmimPdf" name="nmimPdf" required>
								</div>
							</div>
						</div>
					</div>

					<div class="modal-footer justify-content-between bg-gray-dark color-palette">
						<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-primary" id="saveButton">Save changes</button>
					</div>
				</form>
			</div>
		</div>
	</div>

	<div class="modal fade" id="editNmimModal">
		<div class="modal-dialog modal-xl" style="max-width: 40%;">
			<div class="modal-content">
				<form role="form" id="editNmimForm" enctype="multipart/form-data">
					<div class="modal-header bg-gray-dark color-palette">
						<h4 class="modal-title"><b>Add NMIM Pattern Approaval Detail / Certificate</b></h4>
						<button type="button" class="close bg-gray-dark color-palette" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">
						<input type="hidden" class="form-control" id="id" name="id">
						<input type="hidden" class="form-control" id="nmimId" name="nmimId">
						<input type="hidden" class="form-control" id="nmimFilePath" name="nmimFilePath">
						<div class="row">
							<div class="col-12">
								<div class="form-group">
									<label>Details *</label>
									<input type="text" class="form-control" id="nmimDetail" name="nmimDetail" required>
								</div>
							</div>
						</div>  
						<div class="row">
							<div class="col-12">
								<div class="form-group">
									<label>NMIM Approval No *</label>
									<input type="text" class="form-control" id="nmimApprNo" name="nmimApprNo" required>
								</div>
							</div>
						</div>  
						<div class="row">
							<div class="col-12">
								<div class="form-group">
									<label>Approval Date *</label>
									<input type="date" class="form-control" id="nmimApprDt" name="nmimApprDt" required>
								</div>
							</div>
						</div>  
						<div class="row">
							<div class="col-12">
								<div class="form-group">
									<label>Expire Date *</label>
									<input type="date" class="form-control" id="nmimExpDt" name="nmimExpDt" required>
								</div>
							</div>
						</div>  
						<div class="row">
							<div class="col-12">
								<div class="form-group">
									<label>Upload PDF</label>
									<input type="file" class="form-control" id="nmimPdf" name="nmimPdf">
								</div>
							</div>
						</div>
					</div>

					<div class="modal-footer justify-content-between bg-gray-dark color-palette">
						<button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
						<button type="submit" class="btn btn-primary" id="saveButton">Save changes</button>
					</div>
				</form>
			</div>
		</div>
	</div>

</section>

<script>
$(function () {
	var isModalOpen = false; // Flag to track modal visibility
    var id = '<?php echo $id; ?>';

	var table = $("#lesenCertTable").DataTable({
		"responsive": true,
		"autoWidth": false,
		'processing': true,
		'serverSide': true,
		'serverMethod': 'post',
		'searching': false,
		'order': [[ 1, 'asc' ]],
		'columnDefs': [ { orderable: false, targets: [0] }],
		'ajax': {
			'type': 'POST',
			'url':'php/getLesenCert.php',
			'data': {
				companyId: id,
			}, 
			'dataSrc': function (json) {
				// console.log(json); // Debugging: Check JSON response
				if (json.status === "success") {
					return json.message;
				} else {
					return [];
				}
			}
		},
		'columns': [
			{ 
				data: null,  
				render: function (data, type, row, meta) {
					return meta.row + 1;  
				}
			},
			{ data: 0 },  
            { data: 1 }, 
            { data: 2 }, 
            { data: 3 },
            {
				data: 4,    // Attach PDF
				render: function (data, type, row) {
					return data; // Render the HTML as is
				}
			},
			{ 
				data: 5,
				visible: false,
				render: function (data, type, row) {
					return data; 
				}
			}
		],
	});

	// Bind form submission handler once
	$('#addLesenCertForm').off('submit').on('submit', function(e) {
		e.preventDefault(); 
		var formData = new FormData(this);
		$.ajax({
			url: 'php/addLesenCert.php',
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			success: function(data) {
				var obj = JSON.parse(data); 
				if (obj.status === 'success') {
					$('#addLesenCertModal').modal('hide');
					toastr["success"](obj.message, "Success:");
					// $('#lesenCertTable').DataTable().ajax.reload();
					location.reload(); // Reload the page
				} else {
					toastr["error"](obj.message, "Failed:");
				}
				$('#spinnerLoading').hide();
				isModalOpen = false; // Set flag to false on error as well
			},
			error: function(xhr, status, error) {
				console.error("AJAX request failed:", status, error);
				toastr["error"]("An error occurred while processing the request.", "Failed:");
				$('#spinnerLoading').hide();
				isModalOpen = false; // Set flag to false on error as well
			}
		});
	});

	// Bind form submission handler once
	$('#editLesenCertForm').off('submit').on('submit', function(e) {
		e.preventDefault(); 
		var formData = new FormData(this);
		$.ajax({
			url: 'php/editLesenCert.php',
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			success: function(data) {
				var obj = JSON.parse(data); 
				if (obj.status === 'success') {
					$('#editLesenCertModal').modal('hide');
					toastr["success"](obj.message, "Success:");
					// $('#nmimTable').DataTable().ajax.reload();
					location.reload(); // Reload the page
				} else {
					toastr["error"](obj.message, "Failed:");
				}
				$('#spinnerLoading').hide();
				isModalOpen = false; // Set flag to false on error as well
			},
			error: function(xhr, status, error) {
				console.error("AJAX request failed:", status, error);
				toastr["error"]("An error occurred while processing the request.", "Failed:");
				$('#spinnerLoading').hide();
				isModalOpen = false; // Set flag to false on error as well
			}
		});
	});

	var table = $("#nmimTable").DataTable({
		"responsive": true,
		"autoWidth": false,
		'processing': true,
		'serverSide': true,
		'serverMethod': 'post',
		'searching': false,
		'order': [[ 1, 'asc' ]],
		'columnDefs': [ { orderable: false, targets: [0] }],
		'ajax': {
			'type': 'POST',
			'url':'php/getNmim.php',
			'data': {
				companyId: id,
			}, 
			'dataSrc': function (json) {
				// console.log(json); // Debugging: Check JSON response
				if (json.status === "success") {
					return json.message;
				} else {
					return [];
				}
			}
		},
		'columns': [
			{ 
				data: null,  
				render: function (data, type, row, meta) {
					return meta.row + 1;  
				}
			},
			{ data: 0 },  
            { data: 1 }, 
            { data: 2 }, 
            { data: 3 },
            {
				data: 4,    // Attach PDF
				render: function (data, type, row) {
					return data; // Render the HTML as is
				}
			},
			{ 
				data: 5,
				visible: false,
				render: function (data, type, row) {
					return data; 
				}
			}
		],
	});

	// Bind form submission handler once
	$('#addNmimForm').off('submit').on('submit', function(e) {
		e.preventDefault(); 
		var formData = new FormData(this);
		$.ajax({
			url: 'php/addNmim.php',
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			success: function(data) {
				var obj = JSON.parse(data); 
				if (obj.status === 'success') {
					$('#addNmimModal').modal('hide');
					toastr["success"](obj.message, "Success:");
					// $('#nmimTable').DataTable().ajax.reload();
					location.reload(); // Reload the page
				} else {
					toastr["error"](obj.message, "Failed:");
				}
				$('#spinnerLoading').hide();
				isModalOpen = false; // Set flag to false on error as well
			},
			error: function(xhr, status, error) {
				console.error("AJAX request failed:", status, error);
				toastr["error"]("An error occurred while processing the request.", "Failed:");
				$('#spinnerLoading').hide();
				isModalOpen = false; // Set flag to false on error as well
			}
		});
	});

	// Bind form submission handler once
	$('#editNmimForm').off('submit').on('submit', function(e) {
		e.preventDefault(); 
		var formData = new FormData(this);
		$.ajax({
			url: 'php/editNmim.php',
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			success: function(data) {
				var obj = JSON.parse(data); 
				if (obj.status === 'success') {
					$('#editNmimModal').modal('hide');
					toastr["success"](obj.message, "Success:");
					// $('#nmimTable').DataTable().ajax.reload();
					location.reload(); // Reload the page
				} else {
					toastr["error"](obj.message, "Failed:");
				}
				$('#spinnerLoading').hide();
				isModalOpen = false; // Set flag to false on error as well
			},
			error: function(xhr, status, error) {
				console.error("AJAX request failed:", status, error);
				toastr["error"]("An error occurred while processing the request.", "Failed:");
				$('#spinnerLoading').hide();
				isModalOpen = false; // Set flag to false on error as well
			}
		});
	});

	// Bind form submission handler once
	$('#profileForm').off('submit').on('submit', function(e) {
		e.preventDefault(); 
		var formData = new FormData(this);
		$.ajax({
			url: 'php/updateCompany.php',
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			success: function(data) {
				var obj = JSON.parse(data); 
				if (obj.status === 'success') {
					toastr["success"](obj.message, "Success:");
					location.reload(); // Reload the page
				} else {
					toastr["error"](obj.message, "Failed:");
				}
				$('#spinnerLoading').hide();
			},
			error: function(xhr, status, error) {
				console.error("AJAX request failed:", status, error);
				toastr["error"]("An error occurred while processing the request.", "Failed:");
				$('#spinnerLoading').hide();
			}
		});
	});

    // $.validator.setDefaults({
    //     submitHandler: function () {
    //         $('#spinnerLoading').show();
	// 		if (!isModalOpen) {
	// 			$.post('php/updateCompany.php', $('#profileForm').serialize(), function(data){
	// 				var obj = JSON.parse(data); 
					
	// 				if(obj.status === 'success'){
	// 					toastr["success"](obj.message, "Success:");
						
	// 					$.get('company.php', function(data) {
	// 						$('#mainContents').html(data);
	// 						$('#spinnerLoading').hide();
	// 					});
	// 				}
	// 				else if(obj.status === 'failed'){
	// 					toastr["error"](obj.message, "Failed:");
	// 					$('#spinnerLoading').hide();
	// 				}
	// 				else{
	// 					toastr["error"]("Failed to update profile", "Failed:");
	// 					$('#spinnerLoading').hide();
	// 				}
	// 			});
	// 		}
    //     }
    // });
    
    $('#profileForm').validate({
        rules: {
            text: {
                required: true
            }
        },
        messages: {
            text: {
                required: "Please fill in this field"
            }
        },
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

function newLesenCert(id){
	$('#spinnerLoading').show();
	$('#addLesenCertModal').find('#id').val(id);
	$('#addLesenCertModal').modal('show');
	isModalOpen = true; // Set flag to true when modal is shown
	// $('#addLesenCertForm').validate({
	// 	errorElement: 'span',
	// 	errorPlacement: function (error, element) {
	// 	error.addClass('invalid-feedback');
	// 	element.closest('.form-group').append(error);
	// 	},
	// 	highlight: function (element, errorClass, validClass) {
	// 	$(element).addClass('is-invalid');
	// 	},
	// 	unhighlight: function (element, errorClass, validClass) {
	// 	$(element).removeClass('is-invalid');
	// 	}
	// });
	$('#spinnerLoading').hide();
}

function editLesenCert(companyid, lesencertid){
	$('#spinnerLoading').show();
	$.post('php/getSingleLesenCert.php', {companyId: companyid, lesenCertId: lesencertid}, function(data){
        var obj = JSON.parse(data);
		console.log(obj);
        
        if(obj.status === 'success'){
			$('#editLesenCertModal').find('#id').val(companyid);
			$('#editLesenCertModal').find('#lesenCertId').val(lesencertid);
			$('#editLesenCertModal').find('#lesenCertDetail').val(obj.message.lesenCertDetail);
			$('#editLesenCertModal').find('#lesenCertSerialNo').val(obj.message.lesenCertSerialNo);
			$('#editLesenCertModal').find('#lesenCertApprDt').val(obj.message.lesenCertApprDt);
			$('#editLesenCertModal').find('#lesenCertExpDt').val(obj.message.lesenCertExpDt);
			$('#editLesenCertModal').find('#lesenCertFilePath').val(obj.message.lesenCertFilePath);
			$('#editLesenCertModal').modal('show');
			isModalOpen = true; // Set flag to true when modal is shown
            
            // $('#customerForm').validate({
            //     errorElement: 'span',
            //     errorPlacement: function (error, element) {
            //         error.addClass('invalid-feedback');
            //         element.closest('.form-group').append(error);
            //     },
            //     highlight: function (element, errorClass, validClass) {
            //         $(element).addClass('is-invalid');
            //     },
            //     unhighlight: function (element, errorClass, validClass) {
            //         $(element).removeClass('is-invalid');
            //     }
            // });
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

function addNmim(id){
	$('#spinnerLoading').show();
	$('#addNmimModal').find('#id').val(id);
	$('#addNmimModal').modal('show');
	isModalOpen = true; // Set flag to true when modal is shown
	// $('#addLesenCertForm').validate({
	// 	errorElement: 'span',
	// 	errorPlacement: function (error, element) {
	// 	error.addClass('invalid-feedback');
	// 	element.closest('.form-group').append(error);
	// 	},
	// 	highlight: function (element, errorClass, validClass) {
	// 	$(element).addClass('is-invalid');
	// 	},
	// 	unhighlight: function (element, errorClass, validClass) {
	// 	$(element).removeClass('is-invalid');
	// 	}
	// });
	$('#spinnerLoading').hide();
}

function editNmim(companyid, nmimid){
	$('#spinnerLoading').show();
	$.post('php/getSingleNmim.php', {companyId: companyid, nmimId: nmimid}, function(data){
        var obj = JSON.parse(data);
		console.log(obj);
        
        if(obj.status === 'success'){
			$('#editNmimModal').find('#id').val(companyid);
			$('#editNmimModal').find('#nmimId').val(nmimid);
			$('#editNmimModal').find('#nmimDetail').val(obj.message.nmimDetail);
			$('#editNmimModal').find('#nmimApprNo').val(obj.message.nmimApprNo);
			$('#editNmimModal').find('#nmimApprDt').val(obj.message.nmimApprDt);
			$('#editNmimModal').find('#nmimExpDt').val(obj.message.nmimExpDt);
			$('#editNmimModal').find('#nmimFilePath').val(obj.message.nmimFilePath);
			$('#editNmimModal').modal('show');
			isModalOpen = true; // Set flag to true when modal is shown
            
            // $('#customerForm').validate({
            //     errorElement: 'span',
            //     errorPlacement: function (error, element) {
            //         error.addClass('invalid-feedback');
            //         element.closest('.form-group').append(error);
            //     },
            //     highlight: function (element, errorClass, validClass) {
            //         $(element).addClass('is-invalid');
            //     },
            //     unhighlight: function (element, errorClass, validClass) {
            //         $(element).removeClass('is-invalid');
            //     }
            // });
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

function deleteLesenCert(companyid, lesencertid) {
  if (confirm('Are you sure you want to cancel this items?')) {
    $('#spinnerLoading').show();
    $.post('php/deleteLesenCert.php', {companyId: companyid, lesenCertId: lesencertid}, function(data){
      var obj = JSON.parse(data);

      if(obj.status === 'success'){
        toastr["success"](obj.message, "Success:");
        $('#lesenCertTable').DataTable().ajax.reload();
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
}

function deleteNmim(companyid, nmimid) {
  if (confirm('Are you sure you want to cancel this items?')) {
    $('#spinnerLoading').show();
    $.post('php/deleteNmim.php', {companyId: companyid, nmimId: nmimid}, function(data){
      var obj = JSON.parse(data);

      if(obj.status === 'success'){
        toastr["success"](obj.message, "Success:");
        $('#nmimTable').DataTable().ajax.reload();
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
}

</script>
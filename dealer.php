<?php
require_once 'php/db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
  echo '<script type="text/javascript">';
  echo 'window.location.href = "login.html";</script>';
}
else{
  $user = $_SESSION['userID'];
  $_SESSION['page']='dealer';
}
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0 text-dark">Reseller</h1>
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
                                <button type="button" class="btn btn-block bg-gradient-warning btn-sm" id="addCustomers">Add Reseller</button>
                            </div>
                        </div>
                    </div>
					<div class="card-body">
						<table id="customerTable" class="table table-bordered table-striped">
							<thead>
								<tr>
                  <th>Reseller Code</th>
                  <th>Other Code</th>
									<th>Name</th>
									<th>Address</th>
									<th>Phone</th>
									<th>Email</th>
									<th>Actions</th>
									<th>Branches</th>
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
    <div class="modal-dialog modal-xl" style="max-width:90%;">
      <div class="modal-content">
        <form role="form" id="customerForm">
            <div class="modal-header">
              <h4 class="modal-title">Add Resellers</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="card-body">
                <input type="hidden" class="form-control" id="id" name="id">
                <div class="row">
                  <div class="form-group col-3">
                    <label for="code">Reseller Code *</label>
                    <input type="text" class="form-control" name="code" id="code" placeholder="Enter Reseller Code" readonly>
                  </div>
                  <div class="form-group col-3">
                    <label for="otherCode">Other Code (AutoCount etc.)</label>
                    <input type="text" class="form-control" name="otherCode" id="otherCode" placeholder="Enter Other System Code">
                  </div>
                  <div class="form-group col-6">
                    <label for="name">Reseller Name *</label>
                    <input type="text" class="form-control" name="name" id="name" placeholder="Enter Reseller Name" required>
                  </div>
                  <div class="form-group col-3"> 
                    <label for="address">Address *</label>
                    <input class="form-control" id="address" name="address" placeholder="Enter your address 1" required>
                  </div>
                  <div class="form-group col-3"> 
                    <label for="address2">Address 2 *</label>
                    <input class="form-control" id="address2" name="address2" placeholder="Enter your address 2" required>
                  </div>
                  <div class="form-group col-3"> 
                    <label for="address3">Address 3</label>
                    <input class="form-control" id="address3" name="address3" placeholder="Enter your address 3">
                  </div>
                  <div class="form-group col-3"> 
                    <label for="address4">Address 4</label>
                    <input class="form-control" id="address4" name="address4" placeholder="Enter your address 4">
                  </div>
                  <div class="form-group col-3"> 
                    <label for="reseller_map_url">Map Url</label>
                    <input type="text" class="form-control" id="reseller_map_url" name="reseller_map_url" placeholder="Enter Reseller Map Url">
                  </div>
                  <div class="form-group col-3">
                    <label for="phone">Office Phone *</label>
                    <input type="text" class="form-control" name="phone" id="phone" placeholder="01x-xxxxxxx" required>
                  </div>
                  <div class="form-group col-3">
                    <label for="pic">PIC *</label>
                    <input type="text" class="form-control" name="pic" id="pic" placeholder="Enter PIC" required>
                  </div>
                  <div class="form-group col-3"> 
                    <label for="picContact">PIC Contact *</label>
                    <input type="text" class="form-control" id="picContact" name="picContact" placeholder="Enter PIC Contact" required>
                  </div>
                  <div class="form-group col-3"> 
                    <label for="email">Email *</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter Reseller Email" required>
                  </div>
                </div>

                <section class="mt-5 mb-5">
                  <div class="row mb-3">
                    <h4>Reseller Branches & Address</h4>
                    <button style="margin-left:auto;margin-right: 25px;" type="button" class="btn btn-primary add-branch">Add Branch</button>
                  </div><hr>
                  <div id="branchTable"></div>
                </section>  
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

<script type="text/html" id="branchDetails">
  <div class="details">
    <div class="row">
      <input type="hidden" class="form-control" id="branch_id">
      <div class="form-group col-2"> 
        <label for="branch_code">Branch Code </label>
        <input class="form-control" id="branch_code" placeholder="Enter your branch code">
      </div>
      <div class="form-group col-2"> 
        <label for="branch_name">Branch Name *</label>
        <input class="form-control" id="branch_name" placeholder="Enter your branch name" required>
      </div>
      <div class="form-group col-2"> 
        <label for="branch_address1">Address 1 *</label>
        <input class="form-control" id="branch_address1" placeholder="Enter your address" required>
      </div>
      <div class="form-group col-2"> 
        <label for="branch_address2">Address 2 *</label>
        <input class="form-control" id="branch_address2" placeholder="Enter your address 2" required>
      </div>
      <div class="form-group col-2"> 
        <label for="branch_address3">Address 3 *</label>
        <input class="form-control" id="branch_address3" placeholder="Enter your address 3" required>
      </div>
      <div class="form-group col-2"> 
        <label for="branch_address4">Address 4 *</label>
        <input class="form-control" id="branch_address4" placeholder="Enter your address 4" required>
      </div>
      <div class="form-group col-2"> 
        <label for="map_url">Map URL</label>
        <input class="form-control" id="map_url" placeholder="Enter your map url">
      </div>
      <div class="form-group col-2"> 
        <label for="branchPhone">Office Phone</label>
        <input class="form-control" id="branchPhone" placeholder="Enter your phone">
      </div>
      <div class="form-group col-2"> 
        <label for="branchEmail">Email</label>
        <input class="form-control" id="branchEmail" placeholder="Enter your email">
      </div>
      <div class="form-group col-2"> 
        <label for="branchPic">PIC</label>
        <input class="form-control" id="branchPic" placeholder="Enter your PIC">
      </div>
      <div class="form-group col-2"> 
        <label for="branchPicContact">PIC Contact</label>
        <input class="form-control" id="branchPicContact" placeholder="Enter your PIC Contact">
      </div>
      <div class="form-group col-2"></div>
      <div class="form-group col-2"> 
        <button class="btn btn-danger btn-sm" id="remove"><i class="fa fa-times"></i></button>
      </div>
    </div><hr>
  </div>
</script>

<script>
var branchCount = $("#branchTable").find(".details").length;

$(function () {
    var table = $("#customerTable").DataTable({
        "responsive": true,
        "autoWidth": false,
        'processing': true,
        'serverSide': true,
        'serverMethod': 'post',
        'ajax': {
            'url':'php/loadDealers.php'
        },
        'columns': [
            { data: 'customer_code' },
            { data: 'other_code' },
            { data: 'customer_name' },
            { data: 'customer_address' },
            { data: 'customer_phone' },
            { data: 'customer_email' },
            { 
                data: 'id',
                render: function ( data, type, row ) {
                    return '<div class="row"><div class="col-3"><button type="button" id="edit'+data+'" onclick="edit('+data+')" class="btn btn-success btn-sm"><i class="fas fa-pen"></i></button></div><div class="col-3"><button type="button" id="deactivate'+data+'" onclick="deactivate('+data+')" class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button></div></div>';
                }
            },
            { 
            className: 'dt-control',
            orderable: false,
            data: null,
            render: function ( data, type, row ) {
                return '<td class="table-elipse" data-toggle="collapse" data-target="#demo'+row.id+'"><i class="fas fa-angle-down"></i></td>';
            }
            }
        ],
        "rowCallback": function( row, data, index ) {

            $('td', row).css('background-color', '#E6E6FA');
        },        
    });

    // Add event listener for opening and closing details
    $('#customerTable tbody').on('click', 'td.dt-control', function () {
        var tr = $(this).closest('tr');
        var row = table.row( tr );

        if ( row.child.isShown() ) {
            // This row is already open - close it
            row.child.hide();
            tr.removeClass('shown');
        }
        else {
            row.child( format(row.data()) ).show();tr.addClass("shown");
        }
    });
    
    $.validator.setDefaults({
        submitHandler: function () {
            $('#spinnerLoading').show();
            $.post('php/dealer.php', $('#customerForm').serialize(), function(data){
                var obj = JSON.parse(data); 
                
                if(obj.status === 'success'){
                    $('#addModal').modal('hide');
                    toastr["success"](obj.message, "Success:");
                    $('#customerTable').DataTable().ajax.reload();
                    $('#spinnerLoading').hide();
                    location.reload();
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

    $('#addCustomers').on('click', function(){
        $('#addModal').find('#id').val("");
        $('#addModal').find('#code').val("");
        $('#addModal').find('#otherCode').val("");
        $('#addModal').find('#name').val("");
        $('#addModal').find('#address').val("");
        $('#addModal').find('#address2').val("");
        $('#addModal').find('#address3').val("");
        $('#addModal').find('#address4').val("");
        $('#addModal').find('#reseller_map_url').val("");
        $('#addModal').find('#phone').val("");
        $('#addModal').find('#pic').val("");
        $('#addModal').find('#picContact').val("");
        $('#addModal').find('#email').val("");
        branchCount = 0;
        $('#addModal').modal('show');
        
        $('#customerForm').validate({
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

    $(".add-branch").click(function(){
        var $addContents = $("#branchDetails").clone();
        $("#branchTable").append($addContents.html());

        $("#branchTable").find('.details:last').attr("id", "detail" + branchCount);
        $("#branchTable").find('.details:last').attr("data-index", branchCount);
        $("#branchTable").find('#remove:last').attr("id", "remove" + branchCount);

        $("#branchTable").find('#branch_code:last').attr('name', 'branch_code['+branchCount+']').attr("id", "branch_code" + branchCount);
        $("#branchTable").find('#branch_name:last').attr('name', 'branch_name['+branchCount+']').attr("id", "branch_name" + branchCount);
        $("#branchTable").find('#branch_address1:last').attr('name', 'branch_address1['+branchCount+']').attr("id", "branch_address1" + branchCount);
        $("#branchTable").find('#branch_address2:last').attr('name', 'branch_address2['+branchCount+']').attr("id", "branch_address2" + branchCount);
        $("#branchTable").find('#branch_address3:last').attr('name', 'branch_address3['+branchCount+']').attr("id", "branch_address3" + branchCount);
        $("#branchTable").find('#branch_address4:last').attr('name', 'branch_address4['+branchCount+']').attr("id", "branch_address4" + branchCount);
        $("#branchTable").find('#map_url:last').attr('name', 'map_url['+branchCount+']').attr("id", "map_url" + branchCount);
        $("#branchTable").find('#branch_id:last').attr('name', 'branch_id['+branchCount+']').attr("id", "branch_id" + branchCount);
        $("#branchTable").find('#branchPhone:last').attr('name', 'branchPhone['+branchCount+']').attr("id", "branchPhone" + branchCount);
        $("#branchTable").find('#branchEmail:last').attr('name', 'branchEmail['+branchCount+']').attr("id", "branchEmail" + branchCount);
        $("#branchTable").find('#branchPic:last').attr('name', 'branchPic['+branchCount+']').attr("id", "branchPic" + branchCount);
        $("#branchTable").find('#branchPicContact:last').attr('name', 'branchPicContact['+branchCount+']').attr("id", "branchPicContact" + branchCount);

        branchCount++;
    });

    $("#branchTable").on('click', 'button[id^="remove"]', function () {
    var index = $(this).parents('.details').attr('data-index');
    var branchId = $(this).parents('.details').find('input[id^="branch_id"]').val();
    $("#branchTable").append('<input type="hidden" name="deletedShip[]" value="'+index+'"/>');
    //pricingCount--;
    $(this).parents('.details').remove();
  });
});

function format(row){
    var returnString = "";

    if (row.log.length > 0) {
        returnString += '<h4>Branches</h4><table style="width: 100%;"><thead><tr><th width="5%">No.</th><th width="10%">Branch Code</th><th width="10%">Branch Name</th><th width="20%">Address</th><th width="20%">Address 2</th><th width="20%">Address 3</th><th width="20%">Address 4</th></tr></thead><tbody>'
        
        for (var i = 0; i < row.log.length; i++) {
        var item = row.log[i];

          // Check if mapurl is not null
          var branchNameWithMapIcon = item.mapurl 
            ? '<a href="' + item.mapurl + '" target="_blank">' + item.branchname + ' <i class="fa fa-map-marker"></i></a>' 
            : item.branchname;

          returnString += '<tr><td>' + (i + 1) + '</td><td>' + item.branchcode + '</td><td>' + branchNameWithMapIcon + '</td><td>' + item.branch_address1 + '</td><td>' + item.branch_address2 + '</td><td>' + item.branch_address3 + '</td><td>' + item.branch_address4 + '</td></tr>';
        }

        returnString += '</tbody></table>';
    }

    return returnString;
}

function edit(id){
    $('#spinnerLoading').show();
    $.post('php/getDealer.php', {userID: id}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
            $('#addModal').find('#id').val(obj.message.id);
            $('#addModal').find('#code').val(obj.message.customer_code);
            $('#addModal').find('#otherCode').val(obj.message.other_code);
            $('#addModal').find('#name').val(obj.message.customer_name);
            $('#addModal').find('#address').val(obj.message.customer_address);
            $('#addModal').find('#address2').val(obj.message.address2);
            $('#addModal').find('#address3').val(obj.message.address3);
            $('#addModal').find('#address4').val(obj.message.address4);
            $('#addModal').find('#reseller_map_url').val(obj.message.resellerMapUrl);
            $('#addModal').find('#phone').val(obj.message.customer_phone);
            $('#addModal').find('#email').val(obj.message.customer_email);
            $('#addModal').find('#pic').val(obj.message.customer_email);
            $('#addModal').find('#picContact').val(obj.message.customer_email);
            branchCount = 0;

            var weightData = obj.message.branches;
            for(var i=0; i<weightData.length; i++){
              var $addContents = $("#branchDetails").clone();
              $("#branchTable").append($addContents.html());

              $("#branchTable").find('.details:last').attr("id", "detail" + branchCount);
              $("#branchTable").find('.details:last').attr("data-index", branchCount);
              $("#branchTable").find('#remove:last').attr("id", "remove" + branchCount);

              $("#branchTable").find('#branch_id:last').attr('name', 'branch_id['+branchCount+']').attr("id", "branch_id" + branchCount).val(weightData[i].branchid);
              $("#branchTable").find('#branch_code:last').attr('name', 'branch_code['+branchCount+']').attr("id", "branch_code" + branchCount).val(weightData[i].code);
              $("#branchTable").find('#branch_name:last').attr('name', 'branch_name['+branchCount+']').attr("id", "branch_name" + branchCount).val(weightData[i].name);
              $("#branchTable").find('#branch_address1:last').attr('name', 'branch_address1['+branchCount+']').attr("id", "branch_address1" + branchCount).val(weightData[i].branch_address1);
              $("#branchTable").find('#branch_address2:last').attr('name', 'branch_address2['+branchCount+']').attr("id", "branch_address2" + branchCount).val(weightData[i].branch_address2);
              $("#branchTable").find('#branch_address3:last').attr('name', 'branch_address3['+branchCount+']').attr("id", "branch_address3" + branchCount).val(weightData[i].branch_address3);
              $("#branchTable").find('#branch_address4:last').attr('name', 'branch_address4['+branchCount+']').attr("id", "branch_address4" + branchCount).val(weightData[i].branch_address4);
              $("#branchTable").find('#map_url:last').attr('name', 'map_url['+branchCount+']').attr("id", "map_url" + branchCount).val(weightData[i].map_url);
              $("#branchTable").find('#branchPhone:last').attr('name', 'branchPhone['+branchCount+']').attr("id", "branchPhone" + branchCount).val(weightData[i].office_no);
              $("#branchTable").find('#branchEmail:last').attr('name', 'branchEmail['+branchCount+']').attr("id", "branchEmail" + branchCount).val(weightData[i].email);
              $("#branchTable").find('#branchPic:last').attr('name', 'branchPic['+branchCount+']').attr("id", "branchPic" + branchCount).val(weightData[i].pic);
              $("#branchTable").find('#branchPicContact:last').attr('name', 'branchPicContact['+branchCount+']').attr("id", "branchPicContact" + branchCount).val(weightData[i].pic_contact);
              branchCount++;
            }

            $('#addModal').modal('show');
            
            $('#customerForm').validate({
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
    $.post('php/deleteDealer.php', {userID: id}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
            toastr["success"](obj.message, "Success:");
            $('#customerTable').DataTable().ajax.reload();
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
<?php
require_once 'php/db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
  echo '<script type="text/javascript">';
  echo 'window.location.href = "login.html";</script>';
}
else{
  $user = $_SESSION['userID'];
  $_SESSION['page']='customers';
  $dealer = $db->query("SELECT * FROM dealer WHERE deleted = '0'");
}
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0 text-dark">Customers</h1>
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
                      <button type="button" class="btn btn-block bg-gradient-warning btn-sm" id="addCustomers">Add Customers</button>
                  </div>
              </div>
          </div>
					<div class="card-body">
						<table id="customerTable" class="table table-bordered table-striped">
							<thead>
								<tr>
                  <th>Customer Code</th>
                  <th>Other Code</th>
									<th>Name</th>
                  <th>Address</th>
									<th>Phone</th>
									<th>Email</th>
                  <th>Action</th>
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
              <h4 class="modal-title">Add Customers</h4>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="card-body">
                <input type="hidden" class="form-control" id="id" name="id">
                <div class="row">
                  <div class="form-group col-3">
                    <label for="code">Customer Code *</label>
                    <input type="text" class="form-control" name="code" id="code" placeholder="Enter Customer Code" readonly>
                  </div>
                  <div class="form-group col-3">
                    <label for="otherCode">Other Code (AutoCount etc.)</label>
                    <input type="text" class="form-control" name="otherCode" id="otherCode" placeholder="Enter Other System Code">
                  </div>
                  <div class="form-group col-3">
                    <label for="name">Customer Name *</label>
                    <input type="text" class="form-control" name="name" id="name" placeholder="Enter Customer Name" required>
                  </div>
                  <div class="form-group col-3">
                    <label for="code">Dealer</label>
                    <select class="form-control select2" id="dealer" name="dealer">
                      <option value="" selected disabled hidden>Please Select</option>
                      <?php while($rowCustomer2=mysqli_fetch_assoc($dealer)){ ?>
                        <option value="<?=$rowCustomer2['id'] ?>"><?=$rowCustomer2['customer_name'] ?></option>
                      <?php } ?>
                    </select>
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
                    <label for="phone">Phone </label>
                    <input type="text" class="form-control" name="phone" id="phone" placeholder="01x-xxxxxxx">
                  </div>
                  <div class="form-group col-3">
                    <label for="pic">PIC </label>
                    <input type="text" class="form-control" name="pic" id="pic" placeholder="Enter PIC">
                  </div>
                  <div class="form-group col-3"> 
                    <label for="picContact">PIC Contact</label>
                    <input type="text" class="form-control" id="picContact" name="picContact" placeholder="Enter PIC Contact" >
                  </div>
                  <div class="form-group col-3"> 
                    <label for="email">Email </label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your Email">
                  </div>
                </div><hr>
                <div class="row">
                  <h4>Customer Branches & Address</h4>
                  <button style="margin-left:auto;margin-right: 25px;" type="button" class="btn btn-primary add-price">Add Branch</button>
                </div><hr>
                <div id="pricingTable"></div>
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

<script type="text/html" id="pricingDetails">
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
var contentIndex = 0;
var pricingCount = $("#pricingTable").find(".details").length;

$(function () {
  var table = $("#customerTable").DataTable({
      "responsive": true,
      "autoWidth": false,
      'processing': true,
      'serverSide': true,
      'serverMethod': 'post',
      'ajax': {
          'url':'php/loadCustomers.php'
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
      if($("#pricingTable").find(".details").length > 0){
        $('#spinnerLoading').show();
        $.post('php/customers.php', $('#customerForm').serialize(), function(data){
            var obj = JSON.parse(data); 
            
            if(obj.status === 'success'){
                $('#addModal').modal('hide');
                toastr["success"](obj.message, "Success:");
                $('#customerTable').DataTable().ajax.reload();
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
      else{
        alert("Please enter at least an address to procedd");
      }
    }
  });

  $('#addCustomers').on('click', function(){
    $('#addModal').find('#id').val("");
    $('#addModal').find('#dealer').val("");
    $('#addModal').find('#otherCode').val("");
    $('#addModal').find('#code').val("");
    $('#addModal').find('#name').val("");
    $('#addModal').find('#address').val("");
    $('#addModal').find('#address2').val("");
    $('#addModal').find('#address3').val("");
    $('#addModal').find('#address4').val("");
    $('#addModal').find('#phone').val("");
    $('#addModal').find('#pic').val("");
    $('#addModal').find('#picContact').val("");
    $('#addModal').find('#email').val("");
    $('#addModal').find('#pricingTable').html('');
    pricingCount = 0;
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

  $(".add-price").click(function(){
    var $addContents = $("#pricingDetails").clone();
    $("#pricingTable").append($addContents.html());

    $("#pricingTable").find('.details:last').attr("id", "detail" + pricingCount);
    $("#pricingTable").find('.details:last').attr("data-index", pricingCount);
    $("#pricingTable").find('#remove:last').attr("id", "remove" + pricingCount);

    $("#pricingTable").find('#branch_code:last').attr('name', 'branch_code['+pricingCount+']').attr("id", "branch_code" + pricingCount);
    $("#pricingTable").find('#branch_name:last').attr('name', 'branch_name['+pricingCount+']').attr("id", "branch_name" + pricingCount);
    $("#pricingTable").find('#branch_address1:last').attr('name', 'branch_address1['+pricingCount+']').attr("id", "branch_address1" + pricingCount);
    $("#pricingTable").find('#branch_address2:last').attr('name', 'branch_address2['+pricingCount+']').attr("id", "branch_address2" + pricingCount);
    $("#pricingTable").find('#branch_address3:last').attr('name', 'branch_address3['+pricingCount+']').attr("id", "branch_address3" + pricingCount);
    $("#pricingTable").find('#branch_address4:last').attr('name', 'branch_address4['+pricingCount+']').attr("id", "branch_address4" + pricingCount);
    $("#pricingTable").find('#map_url:last').attr('name', 'map_url['+pricingCount+']').attr("id", "map_url" + pricingCount);
    $("#pricingTable").find('#branch_id:last').attr('name', 'branch_id['+pricingCount+']').attr("id", "branch_id" + pricingCount);
    $("#pricingTable").find('#branchPhone:last').attr('name', 'branchPhone['+pricingCount+']').attr("id", "branchPhone" + pricingCount);
    $("#pricingTable").find('#branchEmail:last').attr('name', 'branchEmail['+pricingCount+']').attr("id", "branchEmail" + pricingCount);
    $("#pricingTable").find('#branchPic:last').attr('name', 'branchPic['+pricingCount+']').attr("id", "branchPic" + pricingCount);
    $("#pricingTable").find('#branchPicContact:last').attr('name', 'branchPicContact['+pricingCount+']').attr("id", "branchPicContact" + pricingCount);

    pricingCount++;
  });

  $("#pricingTable").on('click', 'button[id^="remove"]', function () {
    var index = $(this).parents('.details').attr('data-index');
    var branchId = $(this).parents('.details').find('input[id^="branch_id"]').val();
    $("#pricingTable").append('<input type="hidden" name="deletedShip[]" value="'+index+'"/>');
    //pricingCount--;
    $(this).parents('.details').remove();
  });
});

function format(row){
  var returnString = "";
  console.log(row);
  if (row.log.length > 0) {
    returnString += '<h4>Branches</h4><table style="width: 100%;"><thead><tr><th width="5%">No.</th><th width="10%">Branch Code</th><th width="10%">Branch Name</th><th width="20%">Address</th><th width="20%">Address 2</th><th width="20%">Address 3</th><th width="20%">Address 4</th></tr></thead><tbody>'
    
    for (var i = 0; i < row.log.length; i++) {
      var item = row.log[i];

      // Check if mapurl is not null
      var branchNameWithMapIcon = item.mapurl 
        ? '<a href="' + item.mapurl + '">' + item.branchname + ' <i class="fa fa-map-marker"></i></a>' 
        : item.branchname;

      returnString += '<tr><td>' + (i + 1) + '</td><td>' + item.branchcode + '</td><td>' + branchNameWithMapIcon + '</td><td>' + item.address1 + '</td><td>' + item.address2 + '</td><td>' + item.address3 + '</td><td>' + item.address4 + '</td></tr>';
    }

    returnString += '</tbody></table>';
  }

  return returnString;
}

function edit(id){
    $('#spinnerLoading').show();
    $.post('php/getCustomer.php', {userID: id}, function(data){
        var obj = JSON.parse(data);
        
        if(obj.status === 'success'){
            $('#addModal').find('#id').val(obj.message.id);
            $('#addModal').find('#dealer').val(obj.message.dealer);
            $('#addModal').find('#code').val(obj.message.customer_code);
            $('#addModal').find('#otherCode').val(obj.message.other_code);
            $('#addModal').find('#name').val(obj.message.customer_name);
            $('#addModal').find('#address').val(obj.message.customer_address);
            $('#addModal').find('#address2').val(obj.message.address2);
            $('#addModal').find('#address3').val(obj.message.address3);
            $('#addModal').find('#address4').val(obj.message.address4);
            $('#addModal').find('#phone').val(obj.message.customer_phone);
            $('#addModal').find('#email').val(obj.message.customer_email);
            $('#addModal').find('#pic').val(obj.message.customer_email);
            $('#addModal').find('#picContact').val(obj.message.customer_email);

            $('#addModal').find('#pricingTable').html('');
            pricingCount = 0;

            var weightData = obj.message.pricing;

            for(var i=0; i<weightData.length; i++){
              var $addContents = $("#pricingDetails").clone();
              $("#pricingTable").append($addContents.html());

              $("#pricingTable").find('.details:last').attr("id", "detail" + pricingCount);
              $("#pricingTable").find('.details:last').attr("data-index", pricingCount);
              $("#pricingTable").find('#remove:last').attr("id", "remove" + pricingCount);

              $("#pricingTable").find('#branch_id:last').attr('name', 'branch_id['+pricingCount+']').attr("id", "branch_id" + pricingCount).val(weightData[i].branchid);
              $("#pricingTable").find('#branch_code:last').attr('name', 'branch_code['+pricingCount+']').attr("id", "branch_code" + pricingCount).val(weightData[i].code);
              $("#pricingTable").find('#branch_name:last').attr('name', 'branch_name['+pricingCount+']').attr("id", "branch_name" + pricingCount).val(weightData[i].name);
              $("#pricingTable").find('#branch_address1:last').attr('name', 'branch_address1['+pricingCount+']').attr("id", "branch_address1" + pricingCount).val(weightData[i].branch_address1);
              $("#pricingTable").find('#branch_address2:last').attr('name', 'branch_address2['+pricingCount+']').attr("id", "branch_address2" + pricingCount).val(weightData[i].branch_address2);
              $("#pricingTable").find('#branch_address3:last').attr('name', 'branch_address3['+pricingCount+']').attr("id", "branch_address3" + pricingCount).val(weightData[i].branch_address3);
              $("#pricingTable").find('#branch_address4:last').attr('name', 'branch_address4['+pricingCount+']').attr("id", "branch_address4" + pricingCount).val(weightData[i].branch_address4);
              $("#pricingTable").find('#map_url:last').attr('name', 'map_url['+pricingCount+']').attr("id", "map_url" + pricingCount).val(weightData[i].map_url);
              $("#pricingTable").find('#branchPhone:last').attr('name', 'branchPhone['+pricingCount+']').attr("id", "branchPhone" + pricingCount).val(weightData[i].office_no);
              $("#pricingTable").find('#branchEmail:last').attr('name', 'branchEmail['+pricingCount+']').attr("id", "branchEmail" + pricingCount).val(weightData[i].email);
              $("#pricingTable").find('#branchPic:last').attr('name', 'branchPic['+pricingCount+']').attr("id", "branchPic" + pricingCount).val(weightData[i].pic);
              $("#pricingTable").find('#branchPicContact:last').attr('name', 'branchPicContact['+pricingCount+']').attr("id", "branchPicContact" + pricingCount).val(weightData[i].pic_contact);

              pricingCount++;
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
  $.post('php/deleteCustomer.php', {userID: id}, function(data){
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
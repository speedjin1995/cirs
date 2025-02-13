<?php
require_once 'php/db_connect.php';

session_start();

if(!isset($_SESSION['userID'])){
    echo '<script type="text/javascript">';
    echo 'window.location.href = "login.php";</script>';
}
else{
    $_SESSION['page']='otheremailsetup';
    // $stmt = $db->prepare("SELECT * from email_setup where type = ?");
	// $stmt->bind_param('s', $type);
	// $stmt->execute();
	// $result = $stmt->get_result();
    
    // $emailCC = '';
    // $emailTitle = '';
    // $emailBody = '';
	
	// if(($row = $result->fetch_assoc()) !== null){
    //     $emailCC = $row['email_cc'];
    //     $emailTitle = $row['email_title'];
    //     $emailBody = $row['email_body'];
    // }
}
?>

<section class="content-header">
	<div class="container-fluid">
		<div class="row mb-2">
			<div class="col-sm-6">
				<h1 class="m-0 text-dark">Other Validation Email Setup</h1>
			</div>
		</div>
	</div>
</section>

<section class="content" style="min-height:700px;">
	<div class="card">
		<form role="form" id="emailForm">
            <input type="text" id="type" name="type" hidden>
			<div class="card-body">
				<div class="form-group">
					<label for="name">Email Recipient (CC)</label>
					<input type="text" class="form-control" id="emailCC" name="emailCC" placeholder="Enter Email Recipient">
				</div>
				
				<div class="form-group">
					<label for="name">Email Title *</label>
					<input type="text" class="form-control" id="emailTitle" name="emailTitle" placeholder="Enter Email Title" required>
				</div>

				<div class="form-group">
					<label for="name">Email Body *</label>
                    <textarea class="form-control" name="emailBody" id="emailBody" required></textarea>
				</div>
			</div>
			
			<div class="card-footer">
				<button class="btn btn-success" id="saveEmail"><i class="fas fa-save"></i> Save</button>
			</div>
		</form>
	</div>
</section>

<script>
$(function () {
    var type = "other";
    $('#emailForm').find('#type').val(type);

    $('#emailBody').summernote({
        placeholder: 'Enter Email Body',
        tabsize: 2,
        height: 100
    });

    $.post( "php/getEmail.php", {id: type}, function( data ) {
        var decode = JSON.parse(data)
        
        if(decode.status === 'success'){
            $('#emailForm').find('#emailCC').val(decode.message.emailCC);
            $('#emailForm').find('#emailTitle').val(decode.message.emailTitle);
            $('#emailForm').find('#emailBody').summernote("code", decode.message.emailBody);
            
            $('#emailForm').validate({
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
        }
    });

    $.validator.setDefaults({
        submitHandler: function () {
            $('#spinnerLoading').show();
            $.post('php/updateEmail.php', $('#emailForm').serialize(), function(data){
                var obj = JSON.parse(data); 
                
                if(obj.status === 'success'){
                    toastr["success"](obj.message, "Success:");
                    location.reload(); // Reload the page
        		}
        		else if(obj.status === 'failed'){
        		    toastr["error"](obj.message, "Failed:");
                    $('#spinnerLoading').hide();
                }
        		else{
        			toastr["error"]("Failed to update email", "Failed:");
                    $('#spinnerLoading').hide();
        		}
            });
        }
    });
});
</script>
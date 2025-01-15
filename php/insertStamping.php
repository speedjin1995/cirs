<?php
require_once 'db_connect.php';
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

session_start();

$uid = $_SESSION['userID'];

if(isset($_POST['customerType'])){
	$customerType = $_POST['customerType'];
}else{
	$customerType = $_POST['customerTypeEdit'];
}

if(isset($_POST['type'], $customerType, $_POST['newRenew'], $_POST['brand'], $_POST['validator'], $_POST['machineType'], $_POST['jenisAlat']
, $_POST['model'], $_POST['makeIn'], $_POST['capacity'], $_POST['serial'], $_POST['cawangan'], $_POST['trade'], $_POST['assignTo'])){
	$type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
	$customerType = filter_input(INPUT_POST, 'customerType', FILTER_SANITIZE_STRING);
	$brand = filter_input(INPUT_POST, 'brand', FILTER_SANITIZE_STRING);
	$makeIn = filter_input(INPUT_POST, 'makeIn', FILTER_SANITIZE_STRING);
	$validator = filter_input(INPUT_POST, 'validator', FILTER_SANITIZE_STRING);
	$newRenew = filter_input(INPUT_POST, 'newRenew', FILTER_SANITIZE_STRING);
	$machineType = filter_input(INPUT_POST, 'machineType', FILTER_SANITIZE_STRING);
	$jenisAlat = filter_input(INPUT_POST, 'jenisAlat', FILTER_SANITIZE_STRING);
	$model = filter_input(INPUT_POST, 'model', FILTER_SANITIZE_STRING);
	$capacity = filter_input(INPUT_POST, 'capacity', FILTER_SANITIZE_STRING);
	$serial = filter_input(INPUT_POST, 'serial', FILTER_SANITIZE_STRING);
	$cawangan = filter_input(INPUT_POST, 'cawangan', FILTER_SANITIZE_STRING);
	$trade = filter_input(INPUT_POST, 'trade', FILTER_SANITIZE_STRING);
	$assignTo = filter_input(INPUT_POST, 'assignTo', FILTER_SANITIZE_STRING);

	$product = null;
	$dealer = null;
	$reseller_branch = null;
	$company = null;
	$customerText = null;
	$otherCode = null;
	$branch = null;
	$address1 = null;
	$address2 = null;
	$address3 = null;
	$address4 = null;
	$phone = null;
	$email = null;
	$pic = null;
	$contact = null;
	$dueDate = null;
	$stamping = null;
	$stampDate = null;
	$lastYearStampDate = null;
	$noDaftar = null;
	$pinKeselamatan = null;
	$attnTo = null;
	$siriKeselamatan = null;
	$borangD = null;
	$borangE = null;
	$cashBill = null;
	$invoice = null;
	$pic = null;
	$followUpDate = null;
	$quotation = null;
	$quotationDate = null;
	$remark = null;
	$customer = "";
	$includeCert = "NO";
	$poNo = null;
	$poDate = null;
	$unitPrice = '0.00';
	$certPrice = '0.00';
	$totalPrice = '0.00';
	$sst = '0.00';
	$subtoalPrice = '0.00';

	$logs = array();

	if(isset($_POST['product']) && $_POST['product']!=null && $_POST['product']!=""){
		$product = filter_input(INPUT_POST, 'product', FILTER_SANITIZE_STRING);
	}
	else{
		if ($select_stmtP = $db->prepare("SELECT id FROM products WHERE machine_type=? AND jenis_alat=? AND capacity=? AND validator=?")) {
			$select_stmtP->bind_param('ssss', $machineType, $jenisAlat, $capacity, $validator);
			$select_stmtP->execute();
			$resultP = $select_stmtP->get_result();
        
			if ($rowP = $resultP->fetch_assoc()) {
				$product = $rowP['id'];
			} 
			else {
				if(isset($_POST['unitPrice']) && $_POST['unitPrice']!=null && $_POST['unitPrice']!="" && $_POST['unitPrice']!="0.00"){
					// Customer does not exist, create a new customer
					if ($insert_stmtP = $db->prepare("INSERT INTO products (name, machine_type, jenis_alat, capacity, validator, price) VALUES (?, ?, ?, ?, ?, ?)")) {
						$pname = 'product'.$machineType.$jenisAlat.$capacity.$validator;
						$insert_stmtP->bind_param('ssssss', $pname , $machineType, $jenisAlat, $capacity, $validator, $_POST['unitPrice']);
						
						if ($insert_stmtP->execute()) {
							$product = $insert_stmtP->insert_id;
						} 
					}
				}
			}
		}
	}

	if(isset($_POST['dealer']) && $_POST['dealer']!=null && $_POST['dealer']!=""){
		$dealer = $_POST['dealer'];
	}

	if(isset($_POST['reseller_branch']) && $_POST['reseller_branch']!=null && $_POST['reseller_branch']!=""){
		$reseller_branch = $_POST['reseller_branch'];
	}

	if(isset($_POST['company']) && $_POST['company']!=null && $_POST['company']!=""){
		$company = $_POST['company'];
	}

	if(isset($_POST['companyText']) && $_POST['companyText']!=null && $_POST['companyText']!=""){
		$companyText = $_POST['companyText'];
	}

	if(isset($_POST['otherCode']) && $_POST['otherCode']!=null && $_POST['otherCode']!=""){
		$otherCode = $_POST['otherCode'];
	}

	if(isset($_POST['address1']) && $_POST['address1']!=null && $_POST['address1']!=""){
		$address1 = $_POST['address1'];
	}

	if(isset($_POST['address2']) && $_POST['address2']!=null && $_POST['address2']!=""){
		$address2 = $_POST['address2'];
	}

	if(isset($_POST['address3']) && $_POST['address3']!=null && $_POST['address3']!=""){
		$address3 = $_POST['address3'];
	}

	if(isset($_POST['address4']) && $_POST['address4']!=null && $_POST['address4']!=""){
		$address4 = $_POST['address4'];
	}

	if(isset($_POST['branch']) && $_POST['branch']!=null && $_POST['branch']!=""){
		$branch = $_POST['branch'];
	}

	if(isset($_POST['phone']) && $_POST['phone']!=null && $_POST['phone']!=""){
		$phone = $_POST['phone'];
	}

	if(isset($_POST['email']) && $_POST['email']!=null && $_POST['email']!=""){
		$email = $_POST['email'];
	}

	if(isset($_POST['pic']) && $_POST['pic']!=null && $_POST['pic']!=""){
		$pic = $_POST['pic'];
	}

	if(isset($_POST['contact']) && $_POST['contact']!=null && $_POST['contact']!=""){
		$contact = $_POST['contact'];
	}

	if($customerType == "NEW"){
		if ($select_stmt = $db->prepare("SELECT id FROM customers WHERE customer_name=? and deleted = '0'")) {
			$select_stmt->bind_param('s', $_POST['companyText']);
			$select_stmt->execute();
			$result = $select_stmt->get_result();
        
			if ($row = $result->fetch_assoc()) {
				$customer = $row['id'];
				$customerType = 'EXISTING';
			} 
			else {
				$dealer = null;
				$branchName = '';
				$mapUrl = '';

				if(isset($_POST['dealer'] ) && $_POST['dealer'] != null && $_POST['dealer'] != "" && $type == 'DEALER'){
					$dealer = filter_input(INPUT_POST, 'dealer', FILTER_SANITIZE_STRING);
				}

				$custNameFirstLetter = substr($_POST['companyText'], 0, 1);
				$firstChar = $custNameFirstLetter;
				$code = 'C-'.strtoupper($custNameFirstLetter);

				$customerQuery = "SELECT * FROM customers WHERE customer_code LIKE '%$code%' ORDER BY customer_code DESC";
				$customerDetail = mysqli_query($db, $customerQuery);
				$customerRow = mysqli_fetch_assoc($customerDetail);
		
				$customerCode = null;
				$codeSeq = null;
				$count = '';
		
				if(!empty($customerRow)){
					$customerCode = $customerRow['customer_code'];
					preg_match('/\d+/', $customerCode, $matches);
					$codeSeq = (int)$matches[0]; 
					$nextSeq = $codeSeq+1;
					$count = str_pad($nextSeq, 4, '0', STR_PAD_LEFT); 
					$code.=$count;
				}
				else{
					$nextSeq = 1;
					$count = str_pad($nextSeq, 4, '0', STR_PAD_LEFT); 
					$code.=$count;
				} 
		
				// Customer does not exist, create a new customer
				if ($insert_stmt = $db->prepare("INSERT INTO customers (customer_name, customer_code, customer_address, address2, address3, address4, customer_phone, customer_email, customer_status, pic, pic_contact, other_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
					$customer_status = 'CUSTOMERS';
					$insert_stmt->bind_param('ssssssssssss', $_POST['companyText'], $code, $address1, $address2, $address3, $address4, $phone, $email, $customer_status, $pic, $contact, $otherCode);
					
					if ($insert_stmt->execute()) {
						$customer = $insert_stmt->insert_id;
						$customerType = 'EXISTING';

						if ($insert_stmt2 = $db->prepare("INSERT INTO branches (customer_id, address, address2, address3, address4, branch_name, map_url, pic, pic_contact) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
							$insert_stmt2->bind_param('sssssssss', $customer, $address1, $address2, $address3, $address4, $branchName, $mapUrl, $pic, $contact);
							$insert_stmt2->execute();
							$branch = $insert_stmt2->insert_id;
							$insert_stmt2->close();
						} 
					} else {
						echo json_encode(
							array(
								"status"=> "failed", 
								"message"=> $insert_stmt->error
							)
						);
					}
				}
			}
		}
	}
	else{
		$customer = $_POST['company'];
		$customerType = 'EXISTING';
	}

	if(isset($_POST['stamping']) && $_POST['stamping']!=null && $_POST['stamping']!=""){
		$stamping = $_POST['stamping'];
	}

	if(isset($_POST['stampDate']) && $_POST['stampDate']!=null && $_POST['stampDate']!=""){
		$stampDate = $_POST['stampDate'];
		$stampDate = DateTime::createFromFormat('d/m/Y', $stampDate)->format('Y-m-d H:i:s');
	}

	if(isset($_POST['lastYearStampDate']) && $_POST['lastYearStampDate']!=null && $_POST['lastYearStampDate']!=""){
		$lastYearStampDate = $_POST['lastYearStampDate'];
		$lastYearStampDate = DateTime::createFromFormat('d/m/Y', $lastYearStampDate)->format('Y-m-d H:i:s');
	}
	
	if(isset($_POST['noDaftarLama']) && $_POST['noDaftarLama']!=null && $_POST['noDaftarLama']!=""){
		$noDaftarLama = $_POST['noDaftarLama'];
	}
	
	if(isset($_POST['noDaftarBaru']) && $_POST['noDaftarBaru']!=null && $_POST['noDaftarBaru']!=""){
		$noDaftarBaru = $_POST['noDaftarBaru'];
	}

	if(isset($_POST['pinKeselamatan']) && $_POST['pinKeselamatan']!=null && $_POST['pinKeselamatan']!=""){
		$pinKeselamatan = $_POST['pinKeselamatan'];
	}

	if(isset($_POST['attnTo']) && $_POST['attnTo']!=null && $_POST['attnTo']!=""){
		$attnTo = $_POST['attnTo'];
	}

	if(isset($_POST['siriKeselamatan']) && $_POST['siriKeselamatan']!=null && $_POST['siriKeselamatan']!=""){
		$siriKeselamatan = $_POST['siriKeselamatan'];
	}
	
	if(isset($_POST['pic']) && $_POST['pic']!=null && $_POST['pic']!=""){
		$pic = $_POST['pic'];
	}

	if(isset($_POST['borangD']) && $_POST['borangD']!=null && $_POST['borangD']!=""){
		$borangD = $_POST['borangD'];
	}

	if(isset($_POST['borangE']) && $_POST['borangE']!=null && $_POST['borangE']!=""){
		$borangE = $_POST['borangE'];
	}

	if(isset($_POST['remark']) && $_POST['remark']!=null && $_POST['remark']!=""){
		$remark = $_POST['remark'];
	}

	if(isset($_POST['dueDate']) && $_POST['dueDate']!=null && $_POST['dueDate']!=""){
		$dueDate = $_POST['dueDate'];
		$dueDate = DateTime::createFromFormat('d/m/Y', $dueDate)->format('Y-m-d H:i:s');
	}

	if(isset($_POST['quotation']) && $_POST['quotation']!=null && $_POST['quotation']!=""){
		$quotation = $_POST['quotation'];
	}

	if(isset($_POST['quotationDate']) && $_POST['quotationDate']!=null && $_POST['quotationDate']!=""){
		$quotationDate = $_POST['quotationDate'];
		$quotationDate = DateTime::createFromFormat('d/m/Y', $quotationDate)->format('Y-m-d H:i:s');
	}

	if(isset($_POST['includeCert']) && $_POST['includeCert']!=null && $_POST['includeCert']!=""){
		$includeCert = $_POST['includeCert'];
	}

	if(isset($_POST['poNo']) && $_POST['poNo']!=null && $_POST['poNo']!=""){
		$poNo = $_POST['poNo'];
	}

	if(isset($_POST['poDate']) && $_POST['poDate']!=null && $_POST['poDate']!=""){
		$poDate = $_POST['poDate'];
		$poDate = DateTime::createFromFormat('d/m/Y', $poDate)->format('Y-m-d H:i:s');
	}

	if(isset($_POST['cashBill']) && $_POST['cashBill']!=null && $_POST['cashBill']!=""){
		$cashBill = $_POST['cashBill'];
	}

	if(isset($_POST['invoice']) && $_POST['invoice']!=null && $_POST['invoice']!=""){
		$invoice = $_POST['invoice'];
	}

	if(isset($_POST['unitPrice']) && $_POST['unitPrice']!=null && $_POST['unitPrice']!=""){
		$unitPrice = $_POST['unitPrice'];
	}

	if(isset($_POST['certPrice']) && $_POST['certPrice']!=null && $_POST['certPrice']!=""){
		$certPrice = $_POST['certPrice'];
	}

	if(isset($_POST['totalAmount']) && $_POST['totalAmount']!=null && $_POST['totalAmount']!=""){
		$totalPrice = $_POST['totalAmount'];
	}

	if(isset($_POST['sst']) && $_POST['sst']!=null && $_POST['sst']!=""){
		$sst = $_POST['sst'];
	}

	if(isset($_POST['subAmount']) && $_POST['subAmount']!=null && $_POST['subAmount']!=""){
		$subtotalPrice = $_POST['subAmount'];
	}

	if(isset($_POST['id']) && $_POST['id'] != null && $_POST['id'] != ''){
		//Updated datetime
		$currentDateTime = date('Y-m-d H:i:s');

		if ($update_stmt = $db->prepare("UPDATE stamping SET type=?, dealer=?, dealer_branch=?, customers=?, brand=?, machine_type=?, model=?, make_in=?, capacity=?, serial_no=?, assignTo=?, validate_by=?, cawangan=?, jenis_alat=?, trade=?, no_daftar_lama=?, no_daftar_baru=?, pin_keselamatan=?, siri_keselamatan=?, include_cert=?, borang_d=?
		, borang_e=?, invoice_no=?, cash_bill=?, stamping_date=?, last_year_stamping_date=?, due_date=?, pic=?, customer_pic=?, quotation_no=?, quotation_date=?, purchase_no=?, purchase_date=?
		, remarks=?, unit_price=?, cert_price=?, total_amount=?, sst=?, subtotal_amount=?, log=?, products=?, stamping_type=?, updated_datetime=?, branch=? WHERE id=?")){
			$data = json_encode($logs);
			$update_stmt->bind_param('sssssssssssssssssssssssssssssssssssssssssssss', $type, $dealer, $reseller_branch, $customer, $brand, $machineType, $model, $makeIn, $capacity, $serial, $assignTo, 
			$validator, $cawangan, $jenisAlat, $trade, $noDaftarLama,$noDaftarBaru, $pinKeselamatan, $siriKeselamatan, $includeCert, $borangD, $borangE, $invoice, $cashBill, $stampDate, $lastYearStampDate, $dueDate, $uid, $pic, 
			$quotation, $quotationDate, $poNo, $poDate, $remark, $unitPrice, $certPrice, $totalPrice, $sst, $subtotalPrice, $data, $product, $newRenew, $currentDateTime, $branch, $_POST['id']);
		
			// Execute the prepared query.
			if (! $update_stmt->execute()){
				echo json_encode(
					array(
						"status"=> "failed", 
						"message"=> $update_stmt->error
					)
				);
			} 
			else{
				$stampingId = $_POST['id'];
				$stampExtQuery = "SELECT * FROM stamping_ext WHERE stamp_id = $stampingId";
                $stampExtDetail = mysqli_query($db, $stampExtQuery);
                $stampExtRow = mysqli_fetch_assoc($stampExtDetail);

				if($stampExtRow == NULL){
					if ($insert_stmt = $db->prepare("INSERT INTO stamping_ext (stamp_id) 
					VALUES (?)")){
						$insert_stmt->bind_param('s', $stampingId);
						$insert_stmt->execute();
						$insert_stmt->close();
					}

				}

				// For ATK Additional fields
				if(($validator == '10' || $validator == '9') && $jenisAlat == '1'){
					$penentusan_baru = null;
					$penentusan_semula = null;
					$kelulusan_mspk = null;
					$no_kelulusan = null;
					$indicator_serial = null;
					$platform_country = null;
					$platform_type = null;
					$size = null;
					$jenis_pelantar = null;
					$others = null;
					$load_cell_country = null;
					$load_cell_no = null;
					$load_cells_info = [];

					$no = $_POST['no'];
					$loadCells = $_POST['loadCells'];
					$loadCellBrand = $_POST['loadCellBrand'];
					$loadCellModel = $_POST['loadCellModel'];
					$loadCellCapacity = $_POST['loadCellCapacity'];
					$loadCellSerial = $_POST['loadCellSerial'];

					if(isset($no) && $no != null && count($no) > 0){
						for($i=0; $i<count($no); $i++){
							$load_cells_info[] = array(
								"no" => $no[$i],
								"loadCells" => $loadCells[$i],
								"loadCellBrand" => $loadCellBrand[$i],
								"loadCellModel" => $loadCellModel[$i],
								"loadCellCapacity" => $loadCellCapacity[$i],
								"loadCellSerial" => $loadCellSerial[$i]
							);
						}
					}

					if(isset($_POST['penentusanBaru']) && $_POST['penentusanBaru']!=null && $_POST['penentusanBaru']!=""){
						$penentusan_baru = $_POST['penentusanBaru'];
					}
				
					if(isset($_POST['penentusanSemula']) && $_POST['penentusanSemula']!=null && $_POST['penentusanSemula']!=""){
						$penentusan_semula = $_POST['penentusanSemula'];
					}
				
					if(isset($_POST['kelulusanMSPK']) && $_POST['kelulusanMSPK']!=null && $_POST['kelulusanMSPK']!=""){
						$kelulusan_mspk = $_POST['kelulusanMSPK'];
					}
				
					if(isset($_POST['noMSPK']) && $_POST['noMSPK']!=null && $_POST['noMSPK']!=""){
						$no_kelulusan = $_POST['noMSPK'];
					}
				
					if(isset($_POST['noSerialIndicator']) && $_POST['noSerialIndicator']!=null && $_POST['noSerialIndicator']!=""){
						$indicator_serial = $_POST['noSerialIndicator'];
					}

					if(isset($_POST['platformCountry']) && $_POST['platformCountry']!=null && $_POST['platformCountry']!=""){
						$platform_country = $_POST['platformCountry'];
					}
				
					if(isset($_POST['platformType']) && $_POST['platformType']!=null && $_POST['platformType']!=""){
						$platform_type = $_POST['platformType'];
					}
				
					if(isset($_POST['size']) && $_POST['size']!=null && $_POST['size']!=""){
						$size = $_POST['size'];
					}

					if(isset($_POST['jenisPelantar']) && $_POST['jenisPelantar']!=null && $_POST['jenisPelantar']!=""){
						$jenis_pelantar = $_POST['jenisPelantar'];
					}
				
					if(isset($_POST['others']) && $_POST['others']!=null && $_POST['others']!=""){
						$others = $_POST['others'];
					}
				
					if(isset($_POST['loadCellCountry']) && $_POST['loadCellCountry']!=null && $_POST['loadCellCountry']!=""){
						$load_cell_country = $_POST['loadCellCountry'];
					}

					if(isset($_POST['noOfLoadCell']) && $_POST['noOfLoadCell']!=null && $_POST['noOfLoadCell']!=""){
						$load_cell_no = $_POST['noOfLoadCell'];
					}

					if ($insert_stmt2 = $db->prepare("UPDATE stamping_ext SET penentusan_baru = ?, penentusan_semula = ?, kelulusan_mspk = ?, no_kelulusan = ?, indicator_serial = ?, platform_country = ?, platform_type = ?, 
					size = ?, jenis_pelantar = ?, other_info = ?, load_cell_country = ?, load_cell_no = ?, load_cells_info = ? WHERE stamp_id = ?")){
						$data = json_encode($load_cells_info);
						$insert_stmt2->bind_param('ssssssssssssss', $penentusan_baru, $penentusan_semula, $kelulusan_mspk, $no_kelulusan, $indicator_serial, $platform_country, $platform_type, 
						$size, $jenis_pelantar, $others, $load_cell_country, $load_cell_no, $data, $_POST['id']);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}
				}

				// For ATS Additional fields
				if(($validator == '10' || $validator == '9') && $jenisAlat == '4'){
					$platform_country = null;

					if(isset($_POST['platformCountry']) && $_POST['platformCountry']!=null && $_POST['platformCountry']!=""){
						$platform_country = $_POST['platformCountry'];
					}

					if ($insert_stmt2 = $db->prepare("UPDATE stamping_ext SET platform_country = ? WHERE stamp_id = ?")){
						$insert_stmt2->bind_param('ss', $platform_country, $_POST['id']);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}
				}

				// For ATP Additional fields
				if(($validator == '10' || $validator == '9') && $jenisAlat == '2'){
					$platform_country = null;
					$jenis_penunjuk = null;

					if(isset($_POST['platformCountry']) && $_POST['platformCountry']!=null && $_POST['platformCountry']!=""){
						$platform_country = $_POST['platformCountry'];
					}

					if(isset($_POST['jenis_penunjuk']) && $_POST['jenis_penunjuk']!=null && $_POST['jenis_penunjuk']!=""){
						$jenis_penunjuk = $_POST['jenis_penunjuk'];
					}

					if ($insert_stmt2 = $db->prepare("UPDATE stamping_ext SET platform_country = ?, jenis_penunjuk=? WHERE stamp_id = ?")){
						$insert_stmt2->bind_param('sss', $platform_country, $jenis_penunjuk, $_POST['id']);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}
				}

				// For ATP (MOTORCAR) Additional fields
				if(($validator == '10' || $validator == '9') && $jenisAlat == '23'){
					$platform_country = null;
					$jenis_penunjuk = null;

					if(isset($_POST['platformCountry']) && $_POST['platformCountry']!=null && $_POST['platformCountry']!=""){
						$platform_country = $_POST['platformCountry'];
					}

					if(isset($_POST['steelyard']) && $_POST['steelyard']!=null && $_POST['steelyard']!=""){
						$steelyard = $_POST['steelyard'];
					}

					if(isset($_POST['bilanganKaunterpois']) && $_POST['bilanganKaunterpois']!=null && $_POST['bilanganKaunterpois']!=""){
						$bilanganKaunterpois = $_POST['bilanganKaunterpois'];
					}

					$nilais = [
						[
							"no" => 1,
							"nilai" => $_POST['nilai1'] ?? null,
						],
						[
							"no" => 2,
							"nilai" => $_POST['nilai2'] ?? null,
						],
						[
							"no" => 3,
							"nilai" => $_POST['nilai3'] ?? null,
						],
						[
							"no" => 4,
							"nilai" => $_POST['nilai4'] ?? null,
						],
						[
							"no" => 5,
							"nilai" => $_POST['nilai5'] ?? null,
						],
						[
							"no" => 6,
							"nilai" => $_POST['nilai6'] ?? null,
						]
					];

					$nilaiString = json_encode($nilais, JSON_PRETTY_PRINT);

					if ($insert_stmt2 = $db->prepare("UPDATE stamping_ext SET platform_country = ?, steelyard = ?, bilangan_kaunterpois = ?, nilais = ? WHERE stamp_id = ?")){
						$insert_stmt2->bind_param('sssss', $platform_country, $steelyard, $bilanganKaunterpois, $nilaiString, $_POST['id']);
						$insert_stmt2->execute(); 
						$insert_stmt2->close();
					}
				}
				
				// For ATN Additional fields
				if(($validator == '10' || $validator == '9') && ($jenisAlat == '5' || $jenisAlat == '18')){
					$platform_country = null;
					$alat_type = null;
					$bentuk_dulang = null;

					if(isset($_POST['platformCountry']) && $_POST['platformCountry']!=null && $_POST['platformCountry']!=""){
						$platform_country = $_POST['platformCountry'];
					}

					if(isset($_POST['alat_type']) && $_POST['alat_type']!=null && $_POST['alat_type']!=""){
						$alat_type = $_POST['alat_type'];
					}

					if(isset($_POST['bentuk_dulang']) && $_POST['bentuk_dulang']!=null && $_POST['bentuk_dulang']!=""){
						$bentuk_dulang = $_POST['bentuk_dulang'];
					}

					if ($insert_stmt2 = $db->prepare("UPDATE stamping_ext SET platform_country = ?, alat_type=?, bentuk_dulang=? WHERE stamp_id = ?")){
						$insert_stmt2->bind_param('ssss', $platform_country, $alat_type, $bentuk_dulang, $_POST['id']);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}
				}
				
				// For ATE Additional fields
				if(($validator == '10' || $validator == '9') && $jenisAlat == '6'){
					$platform_country = null;
					$class = null;
					$bentuk_dulang = null;

					if(isset($_POST['platformCountry']) && $_POST['platformCountry']!=null && $_POST['platformCountry']!=""){
						$platform_country = $_POST['platformCountry'];
					}

					if(isset($_POST['class']) && $_POST['class']!=null && $_POST['class']!=""){
						$class = $_POST['class'];
					}

					if ($insert_stmt2 = $db->prepare("UPDATE stamping_ext SET platform_country = ?, class=? WHERE stamp_id = ?")){
						$insert_stmt2->bind_param('sss', $platform_country, $class, $_POST['id']);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}
				}
				
				// For SLL Additional fields
				if(($validator == '10' || $validator == '9') && $jenisAlat == '14'){
					$platform_country = null;
					$alat_type = null;		
					
					if(isset($_POST['platformCountry']) && $_POST['platformCountry']!=null && $_POST['platformCountry']!=""){
						$platform_country = $_POST['platformCountry'];
					}

					if(isset($_POST['alat_type']) && $_POST['alat_type']!=null && $_POST['alat_type']!=""){
						$alat_type = $_POST['alat_type'];
					}

					$questions = [
						[
							"no" => 1,
							"answer" => $_POST['question1'] ?? null,
						],
						[
							"no" => 2,
							"answer" => $_POST['question2'] ?? null,
						],
						[
							"no" => 3,
							"answer" => $_POST['question3'] ?? null,
						],
						[
							"no" => 4,
							"answer" => $_POST['question4'] ?? null,
						],
						[
							"no" => 5.1,
							"answer" => $_POST['question5_1'] ?? null,
						],
						[
							"no" => 5.2,
							"answer" => $_POST['question5_2'] ?? null,
						],
						[
							"no" => 6,
							"answer" => $_POST['question6'] ?? null,
						],
						[
							"no" => 7,
							"answer" => $_POST['question7'] ?? null,
						],
					];

					$questionString = json_encode($questions, JSON_PRETTY_PRINT);

					if ($insert_stmt2 = $db->prepare("UPDATE stamping_ext SET platform_country=?, alat_type=?, questions=? WHERE stamp_id = ?")){
						$insert_stmt2->bind_param('ssss', $platform_country, $alat_type, $questionString, $_POST['id']);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}
				}

				// For BTU Additional fields	
				if(($validator == '10' || $validator == '9') && $jenisAlat == '7'){
					$platform_country = null;
					$batuUjian = null;
					$batuUjianLain = null;

					if(isset($_POST['platformCountry']) && $_POST['platformCountry']!=null && $_POST['platformCountry']!=""){
						$platform_country = $_POST['platformCountry'];
					}

					if(isset($_POST['batuUjian']) && $_POST['batuUjian']!=null && $_POST['batuUjian']!=""){
						$batuUjian = $_POST['batuUjian'];
					}

					if(isset($_POST['batuUjianLain']) && $_POST['batuUjianLain']!=null && $_POST['batuUjianLain']!=""){
						$batuUjianLain = $_POST['batuUjianLain'];
					}

					if ($insert_stmt2 = $db->prepare("UPDATE stamping_ext SET platform_country = ?, batu_ujian = ?, batu_ujian_lain=? WHERE stamp_id = ?")){
						$insert_stmt2->bind_param('ssss', $platform_country, $batuUjian, $batuUjianLain, $_POST['id']);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}
				}
				
				// For AUTO_PACKER Additional fields
				if(($validator == '10' || $validator == '9') && $jenisAlat == '10'){
					$platform_country = null;
					$jenis_penunjuk = null;

					if(isset($_POST['platformCountry']) && $_POST['platformCountry']!=null && $_POST['platformCountry']!=""){
						$platform_country = $_POST['platformCountry'];
					}

					if(isset($_POST['jenis_penunjuk']) && $_POST['jenis_penunjuk']!=null && $_POST['jenis_penunjuk']!=""){
						$jenis_penunjuk = $_POST['jenis_penunjuk'];
					}

					if ($insert_stmt2 = $db->prepare("UPDATE stamping_ext SET platform_country = ?, jenis_penunjuk=? WHERE stamp_id = ?")){
						$insert_stmt2->bind_param('sss', $platform_country, $jenis_penunjuk, $_POST['id']);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}

					// $platform_country = null;
					
					// if(isset($_POST['platformCountry']) && $_POST['platformCountry']!=null && $_POST['platformCountry']!=""){
					// 	$platform_country = $_POST['platformCountry'];
					// }

					// $nilais = [
					// 	[
					// 		"no" => 1,
					// 		"nilai" => $_POST['nilai1'] ?? null,
					// 	],
					// 	[
					// 		"no" => 2,
					// 		"nilai" => $_POST['nilai2'] ?? null,
					// 	],
					// 	[
					// 		"no" => 3,
					// 		"nilai" => $_POST['nilai3'] ?? null,
					// 	],
					// 	[
					// 		"no" => 4,
					// 		"nilai" => $_POST['nilai4'] ?? null,
					// 	],
					// 	[
					// 		"no" => 5,
					// 		"nilai" => $_POST['nilai5'] ?? null,
					// 	],
					// 	[
					// 		"no" => 6,
					// 		"nilai" => $_POST['nilai6'] ?? null,
					// 	]
					// ];

					// $nilaiString = json_encode($nilais, JSON_PRETTY_PRINT);

					// if ($insert_stmt2 = $db->prepare("UPDATE stamping_ext SET platform_country=?, nilais=? WHERE stamp_id = ?")){
					// 	$insert_stmt2->bind_param('sss', $platform_country, $nilaiString, $_POST['id']);
					// 	$insert_stmt2->execute();
					// 	$insert_stmt2->close();
					// }
				}

				// For ATS (H)  Additional fields
				if(($validator == '10' || $validator == '9') && $jenisAlat == '17'){
					$platform_country = null;

					if(isset($_POST['platformCountry']) && $_POST['platformCountry']!=null && $_POST['platformCountry']!=""){
						$platform_country = $_POST['platformCountry'];
					}

					if ($insert_stmt2 = $db->prepare("UPDATE stamping_ext SET platform_country = ? WHERE stamp_id = ?")){
						$insert_stmt2->bind_param('ss', $platform_country, $_POST['id']);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}
				}

				// For SIA Additional fields	
				if(($validator == '10' || $validator == '9') && $jenisAlat == '12'){
					$platform_country = null;
					$nilaiJangka = null;
					$nilaiJangkaOther = null;
					$diperbuatDaripada = null;
					$diperbuatDaripadaOther = null;

					if(isset($_POST['platformCountry']) && $_POST['platformCountry']!=null && $_POST['platformCountry']!=""){
						$platform_country = $_POST['platformCountry'];
					}

					if(isset($_POST['nilaiJangka']) && $_POST['nilaiJangka']!=null && $_POST['nilaiJangka']!=""){
						$nilaiJangka = $_POST['nilaiJangka'];
					}

					if(isset($_POST['nilaiJangkaOther']) && $_POST['nilaiJangkaOther']!=null && $_POST['nilaiJangkaOther']!=""){
						$nilaiJangkaOther = $_POST['nilaiJangkaOther'];
					}

					if(isset($_POST['diperbuatDaripada']) && $_POST['diperbuatDaripada']!=null && $_POST['diperbuatDaripada']!=""){
						$diperbuatDaripada = $_POST['diperbuatDaripada'];
					}

					if(isset($_POST['diperbuatDaripadaOther']) && $_POST['diperbuatDaripadaOther']!=null && $_POST['diperbuatDaripadaOther']!=""){
						$diperbuatDaripadaOther = $_POST['diperbuatDaripadaOther'];
					}

					if ($insert_stmt2 = $db->prepare("UPDATE stamping_ext SET platform_country = ?, nilai_jangka = ?, nilai_jangka_other=?, diperbuat_daripada=?, diperbuat_daripada_other=? WHERE stamp_id = ?")){
						$insert_stmt2->bind_param('ssssss', $platform_country, $nilaiJangka, $nilaiJangkaOther, $diperbuatDaripada, $diperbuatDaripadaOther, $_POST['id']);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}
				}

				// UPDATE Stamping System Log
				if ($insert_stmt3 = $db->prepare("INSERT INTO stamping_log (action, user_id, item_id) 
				VALUES (?, ?, ?)")){
					$action = "UPDATE";
					$insert_stmt3->bind_param('sss', $action, $uid, $_POST['id']);
					$insert_stmt3->execute();
					$insert_stmt3->close();
				}

				$update_stmt->close();
				$db->close();
				
				echo json_encode(
					array(
						"status"=> "success", 
						"message"=> "Updated Successfully!!" 
					)
				);
			}
		}
		else{
			echo json_encode(
				array(
					"status"=> "failed", 
					"message"=> "Error when creating query"
				)
			);
		}
	}
	else{
		if ($insert_stmt = $db->prepare("INSERT INTO stamping (type, dealer, dealer_branch, customer_type, customers, brand, machine_type, model, make_in, capacity, serial_no, assignTo,
		validate_by, cawangan, jenis_alat, trade, no_daftar_lama, no_daftar_baru, pin_keselamatan, siri_keselamatan, include_cert, borang_d, borang_e, invoice_no, cash_bill, stamping_date, last_year_stamping_date, due_date, pic, customer_pic, 
		quotation_no, quotation_date, purchase_no, purchase_date, remarks, unit_price, cert_price, total_amount, sst, subtotal_amount, log, products, stamping_type, branch) 
		VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")){
			$data = json_encode($logs);
			$insert_stmt->bind_param('ssssssssssssssssssssssssssssssssssssssssssss', $type, $dealer, $reseller_branch, $customerType, $customer, $brand, $machineType, $model, $makeIn, $capacity, $serial, $assignTo,
			$validator, $cawangan, $jenisAlat, $trade, $noDaftarLama, $noDaftarBaru, $pinKeselamatan, $siriKeselamatan, $includeCert, $borangD, $borangE, $invoice, $cashBill, $stampDate, $lastYearStampDate, $dueDate, $uid, $pic, 
			$quotation, $quotationDate, $poNo, $poDate, $remark, $unitPrice, $certPrice, $totalPrice, $sst, $subtotalPrice, $data, $product, $newRenew, $branch);
			
			// Execute the prepared query.
			if (! $insert_stmt->execute()){
				echo json_encode(
					array(
						"status"=> "failed", 
						"message"=> $insert_stmt->error
					)
				);
			} 
			else{
				$stamp_id = $insert_stmt->insert_id;

				// For ATK Additional fields
				if(($validator == '10' || $validator == '9') && $jenisAlat == '1'){
					$penentusan_baru = null;
					$penentusan_semula = null;
					$kelulusan_mspk = null;
					$no_kelulusan = null;
					$indicator_serial = null;
					$platform_country = null;
					$platform_type = null;
					$size = null;
					$jenis_pelantar = null;
					$others = null;
					$load_cell_country = null;
					$load_cell_no = null;
					$load_cells_info = [];

					$no = $_POST['no'];
					$loadCells = $_POST['loadCells'];
					$loadCellBrand = $_POST['loadCellBrand'];
					$loadCellModel = $_POST['loadCellModel'];
					$loadCellCapacity = $_POST['loadCellCapacity'];
					$loadCellSerial = $_POST['loadCellSerial'];

					if(isset($no) && $no != null && count($no) > 0){
						for($i=0; $i<count($no); $i++){
							$load_cells_info[] = array(
								"no" => $no[$i],
								"loadCells" => $loadCells[$i],
								"loadCellBrand" => $loadCellBrand[$i],
								"loadCellModel" => $loadCellModel[$i],
								"loadCellCapacity" => $loadCellCapacity[$i],
								"loadCellSerial" => $loadCellSerial[$i]
							);
						}
					}

					if(isset($_POST['penentusanBaru']) && $_POST['penentusanBaru']!=null && $_POST['penentusanBaru']!=""){
						$penentusan_baru = $_POST['penentusanBaru'];
					}
				
					if(isset($_POST['penentusanSemula']) && $_POST['penentusanSemula']!=null && $_POST['penentusanSemula']!=""){
						$penentusan_semula = $_POST['penentusanSemula'];
					}
				
					if(isset($_POST['kelulusanMSPK']) && $_POST['kelulusanMSPK']!=null && $_POST['kelulusanMSPK']!=""){
						$kelulusan_mspk = $_POST['kelulusanMSPK'];
					}
				
					if(isset($_POST['noMSPK']) && $_POST['noMSPK']!=null && $_POST['noMSPK']!=""){
						$no_kelulusan = $_POST['noMSPK'];
					}
				
					if(isset($_POST['noSerialIndicator']) && $_POST['noSerialIndicator']!=null && $_POST['noSerialIndicator']!=""){
						$indicator_serial = $_POST['noSerialIndicator'];
					}

					if(isset($_POST['platformCountry']) && $_POST['platformCountry']!=null && $_POST['platformCountry']!=""){
						$platform_country = $_POST['platformCountry'];
					}
				
					if(isset($_POST['platformType']) && $_POST['platformType']!=null && $_POST['platformType']!=""){
						$platform_type = $_POST['platformType'];
					}
				
					if(isset($_POST['size']) && $_POST['size']!=null && $_POST['size']!=""){
						$size = $_POST['size'];
					}

					if(isset($_POST['jenisPelantar']) && $_POST['jenisPelantar']!=null && $_POST['jenisPelantar']!=""){
						$jenis_pelantar = $_POST['jenisPelantar'];
					}
				
					if(isset($_POST['others']) && $_POST['others']!=null && $_POST['others']!=""){
						$others = $_POST['others'];
					}
				
					if(isset($_POST['loadCellCountry']) && $_POST['loadCellCountry']!=null && $_POST['loadCellCountry']!=""){
						$load_cell_country = $_POST['loadCellCountry'];
					}

					if(isset($_POST['noOfLoadCell']) && $_POST['noOfLoadCell']!=null && $_POST['noOfLoadCell']!=""){
						$load_cell_no = $_POST['noOfLoadCell'];
					}

					if ($insert_stmt2 = $db->prepare("INSERT INTO stamping_ext (stamp_id, penentusan_baru, penentusan_semula, kelulusan_mspk, no_kelulusan, indicator_serial, platform_country, 
						platform_type, size, jenis_pelantar, other_info, load_cell_country, load_cell_no, load_cells_info) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")){
						$data = json_encode($load_cells_info);
						$insert_stmt2->bind_param('ssssssssssssss', $stamp_id, $penentusan_baru, $penentusan_semula, $kelulusan_mspk, $no_kelulusan, $indicator_serial, $platform_country, $platform_type, 
						$size, $jenis_pelantar, $others, $load_cell_country, $load_cell_no, $data);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}
				}

				// For ATS Additional fields
				if(($validator == '10' || $validator == '9') && $jenisAlat == '4'){
					$platform_country = null;

					if(isset($_POST['platformCountry']) && $_POST['platformCountry']!=null && $_POST['platformCountry']!=""){
						$platform_country = $_POST['platformCountry'];
					}

					if ($insert_stmt2 = $db->prepare("INSERT INTO stamping_ext (stamp_id, platform_country) 
					VALUES (?, ?)")){
						$insert_stmt2->bind_param('ss', $stamp_id, $platform_country);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}
				}

				// For ATP Additional fields
				if(($validator == '10' || $validator == '9') && $jenisAlat == '2'){
					$platform_country = null;
					$jenis_penunjuk = null;

					if(isset($_POST['platformCountry']) && $_POST['platformCountry']!=null && $_POST['platformCountry']!=""){
						$platform_country = $_POST['platformCountry'];
					}

					if(isset($_POST['jenis_penunjuk']) && $_POST['jenis_penunjuk']!=null && $_POST['jenis_penunjuk']!=""){
						$jenis_penunjuk = $_POST['jenis_penunjuk'];
					}

					if ($insert_stmt2 = $db->prepare("INSERT INTO stamping_ext (stamp_id, platform_country, jenis_penunjuk) 
					VALUES (?, ?, ?)")){
						$insert_stmt2->bind_param('sss', $stamp_id, $platform_country, $jenis_penunjuk);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}
				}

				// For ATP (MOTORCAR) Additional fields
				if(($validator == '10' || $validator == '9') && $jenisAlat == '23'){
					$platform_country = null;
					$jenis_penunjuk = null;

					if(isset($_POST['platformCountry']) && $_POST['platformCountry']!=null && $_POST['platformCountry']!=""){
						$platform_country = $_POST['platformCountry'];
					}

					if(isset($_POST['steelyard']) && $_POST['steelyard']!=null && $_POST['steelyard']!=""){
						$steelyard = $_POST['steelyard'];
					}
				
					if(isset($_POST['bilanganKaunterpois']) && $_POST['bilanganKaunterpois']!=null && $_POST['bilanganKaunterpois']!=""){
						$bilanganKaunterpois = $_POST['bilanganKaunterpois'];
					}

					$nilais = [
						[
							"no" => 1,
							"nilai" => $_POST['nilai1'] ?? null,
						],
						[
							"no" => 2,
							"nilai" => $_POST['nilai2'] ?? null,
						],
						[
							"no" => 3,
							"nilai" => $_POST['nilai3'] ?? null,
						],
						[
							"no" => 4,
							"nilai" => $_POST['nilai4'] ?? null,
						],
						[
							"no" => 5,
							"nilai" => $_POST['nilai5'] ?? null,
						],
						[
							"no" => 6,
							"nilai" => $_POST['nilai6'] ?? null,
						]
					];

					$nilaiString = json_encode($nilais, JSON_PRETTY_PRINT);

					if ($insert_stmt2 = $db->prepare("INSERT INTO stamping_ext (stamp_id, platform_country, steelyard, bilangan_kaunterpois, nilais) 
					VALUES (?, ?, ?, ?, ?)")){
						$insert_stmt2->bind_param('sssss', $stamp_id, $platform_country, $steelyard, $bilanganKaunterpois, $nilaiString);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}
				}

				// For ATN Additional fields
				if(($validator == '10' || $validator == '9') && ($jenisAlat == '5' || $jenisAlat == '18')){
					$platform_country = null;
					$alat_type = null;
					$bentuk_dulang = null;

					if(isset($_POST['platformCountry']) && $_POST['platformCountry']!=null && $_POST['platformCountry']!=""){
						$platform_country = $_POST['platformCountry'];
					}

					if(isset($_POST['alat_type']) && $_POST['alat_type']!=null && $_POST['alat_type']!=""){
						$alat_type = $_POST['alat_type'];
					}

					if(isset($_POST['bentuk_dulang']) && $_POST['bentuk_dulang']!=null && $_POST['bentuk_dulang']!=""){
						$bentuk_dulang = $_POST['bentuk_dulang'];
					}

					if ($insert_stmt2 = $db->prepare("INSERT INTO stamping_ext (stamp_id, platform_country, alat_type, bentuk_dulang) 
					VALUES (?, ?, ?, ?)")){
						$insert_stmt2->bind_param('ssss', $stamp_id, $platform_country, $alat_type, $bentuk_dulang);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}
				}

				// For ATE Additional fields
				if(($validator == '10' || $validator == '9') && $jenisAlat == '6'){
					$platform_country = null;
					$class = null;

					if(isset($_POST['platformCountry']) && $_POST['platformCountry']!=null && $_POST['platformCountry']!=""){
						$platform_country = $_POST['platformCountry'];
					}

					if(isset($_POST['class']) && $_POST['class']!=null && $_POST['class']!=""){
						$class = $_POST['class'];
					}

					if ($insert_stmt2 = $db->prepare("INSERT INTO stamping_ext (stamp_id, platform_country, class) 
					VALUES (?, ?, ?)")){
						$insert_stmt2->bind_param('sss', $stamp_id, $platform_country, $class);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}
				}

				// For SLL Additional fields
				if(($validator == '10' || $validator == '9') && $jenisAlat == '14'){
					$platform_country = null;
					$alat_type = null;		
					
					if(isset($_POST['platformCountry']) && $_POST['platformCountry']!=null && $_POST['platformCountry']!=""){
						$platform_country = $_POST['platformCountry'];
					}

					if(isset($_POST['alat_type']) && $_POST['alat_type']!=null && $_POST['alat_type']!=""){
						$alat_type = $_POST['alat_type'];
					}

					$questions = [
						[
							"no" => 1,
							"answer" => $_POST['question1'] ?? null,
						],
						[
							"no" => 2,
							"answer" => $_POST['question2'] ?? null,
						],
						[
							"no" => 3,
							"answer" => $_POST['question3'] ?? null,
						],
						[
							"no" => 4,
							"answer" => $_POST['question4'] ?? null,
						],
						[
							"no" => 5.1,
							"answer" => $_POST['question5_1'] ?? null,
						],
						[
							"no" => 5.2,
							"answer" => $_POST['question5_2'] ?? null,
						],
						[
							"no" => 6,
							"answer" => $_POST['question6'] ?? null,
						],
						[
							"no" => 7,
							"answer" => $_POST['question7'] ?? null,
						],
					];

					$questionString = json_encode($questions, JSON_PRETTY_PRINT);

					if ($insert_stmt2 = $db->prepare("INSERT INTO stamping_ext (stamp_id, platform_country, alat_type, questions) 
					VALUES (?, ?, ?, ?)")){
						$insert_stmt2->bind_param('ssss', $stamp_id, $platform_country, $alat_type, $questionString);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}
				}

				// For BTU Additional fields
				if(($validator == '10' || $validator == '9') && $jenisAlat == '7'){
					$platform_country = null;
					$batuUjian = null;
					$batuUjianLain = null;

					if(isset($_POST['platformCountry']) && $_POST['platformCountry']!=null && $_POST['platformCountry']!=""){
						$platform_country = $_POST['platformCountry'];
					}

					if(isset($_POST['batuUjian']) && $_POST['batuUjian']!=null && $_POST['batuUjian']!=""){
						$batuUjian = $_POST['batuUjian'];
					}

					if(isset($_POST['batuUjianLain']) && $_POST['batuUjianLain']!=null && $_POST['batuUjianLain']!=""){
						$batuUjianLain = $_POST['batuUjianLain'];
					}

					if ($insert_stmt2 = $db->prepare("INSERT INTO stamping_ext (stamp_id, platform_country, batu_ujian, batu_ujian_lain) 
					VALUES (?, ?, ?, ?)")){
						$insert_stmt2->bind_param('ssss', $stamp_id, $platform_country, $batuUjian, $batuUjianLain);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}

				}

				// For AUTO_PACKER Additional fields
				if(($validator == '10' || $validator == '9') && $jenisAlat == '10'){
					$platform_country = null;
					$jenis_penunjuk = null;

					if(isset($_POST['platformCountry']) && $_POST['platformCountry']!=null && $_POST['platformCountry']!=""){
						$platform_country = $_POST['platformCountry'];
					}

					if(isset($_POST['jenis_penunjuk']) && $_POST['jenis_penunjuk']!=null && $_POST['jenis_penunjuk']!=""){
						$jenis_penunjuk = $_POST['jenis_penunjuk'];
					}

					if ($insert_stmt2 = $db->prepare("INSERT INTO stamping_ext (stamp_id, platform_country, jenis_penunjuk) 
					VALUES (?, ?, ?)")){
						$insert_stmt2->bind_param('sss', $stamp_id, $platform_country, $jenis_penunjuk);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}
					// $platform_country = null;
					
					// if(isset($_POST['platformCountry']) && $_POST['platformCountry']!=null && $_POST['platformCountry']!=""){
					// 	$platform_country = $_POST['platformCountry'];
					// }

					// $nilais = [
					// 	[
					// 		"no" => 1,
					// 		"nilai" => $_POST['nilai1'] ?? null,
					// 	],
					// 	[
					// 		"no" => 2,
					// 		"nilai" => $_POST['nilai2'] ?? null,
					// 	],
					// 	[
					// 		"no" => 3,
					// 		"nilai" => $_POST['nilai3'] ?? null,
					// 	],
					// 	[
					// 		"no" => 4,
					// 		"nilai" => $_POST['nilai4'] ?? null,
					// 	],
					// 	[
					// 		"no" => 5,
					// 		"nilai" => $_POST['nilai5'] ?? null,
					// 	],
					// 	[
					// 		"no" => 6,
					// 		"nilai" => $_POST['nilai6'] ?? null,
					// 	]
					// ];

					// $nilaistring = json_encode($nilais, JSON_PRETTY_PRINT);

					// if ($insert_stmt2 = $db->prepare("INSERT INTO stamping_ext (stamp_id, platform_country, nilais) 
					// VALUES (?, ?, ?)")){
					// 	$insert_stmt2->bind_param('sss', $stamp_id, $platform_country, $nilaistring);
					// 	$insert_stmt2->execute();
					// 	$insert_stmt2->close();
					// }
				}

				// For ATS - H Additional fields
				if(($validator == '10' || $validator == '9') && $jenisAlat == '17'){
					$platform_country = null;

					if(isset($_POST['platformCountry']) && $_POST['platformCountry']!=null && $_POST['platformCountry']!=""){
						$platform_country = $_POST['platformCountry'];
					}

					if ($insert_stmt2 = $db->prepare("INSERT INTO stamping_ext (stamp_id, platform_country) 
					VALUES (?, ?)")){
						$insert_stmt2->bind_param('ss', $stamp_id, $platform_country);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}
				}

				// For SIA Additional fields
				if(($validator == '10' || $validator == '9') && $jenisAlat == '12'){
					$platform_country = null;
					$nilaiJangka = null;
					$nilaiJangkaOther = null;
					$diperbuatDaripada = null;
					$diperbuatDaripadaOther = null;

					if(isset($_POST['platformCountry']) && $_POST['platformCountry']!=null && $_POST['platformCountry']!=""){
						$platform_country = $_POST['platformCountry'];
					}

					if(isset($_POST['nilaiJangka']) && $_POST['nilaiJangka']!=null && $_POST['nilaiJangka']!=""){
						$nilaiJangka = $_POST['nilaiJangka'];
					}

					if(isset($_POST['nilaiJangkaOther']) && $_POST['nilaiJangkaOther']!=null && $_POST['nilaiJangkaOther']!=""){
						$nilaiJangkaOther = $_POST['nilaiJangkaOther'];
					}

					if(isset($_POST['diperbuatDaripada']) && $_POST['diperbuatDaripada']!=null && $_POST['diperbuatDaripada']!=""){
						$diperbuatDaripada = $_POST['diperbuatDaripada'];
					}

					if(isset($_POST['diperbuatDaripadaOther']) && $_POST['diperbuatDaripadaOther']!=null && $_POST['diperbuatDaripadaOther']!=""){
						$diperbuatDaripadaOther = $_POST['diperbuatDaripadaOther'];
					}

					if ($insert_stmt2 = $db->prepare("INSERT INTO stamping_ext (stamp_id, platform_country, nilai_jangka, nilai_jangka_other, diperbuat_daripada,diperbuat_daripada_other ) 
					VALUES (?, ?, ?, ?, ?, ?)")){
						$insert_stmt2->bind_param('ssssss', $stamp_id, $platform_country, $nilaiJangka, $nilaiJangkaOther, $diperbuatDaripada, $diperbuatDaripadaOther);
						$insert_stmt2->execute();
						$insert_stmt2->close();
					}

				}
				
				// Insert Stamping System Log
				if ($insert_stmt3 = $db->prepare("INSERT INTO stamping_log (action, user_id, item_id) 
				VALUES (?, ?, ?)")){
					$action = "INSERT";
					$insert_stmt3->bind_param('sss', $action, $uid, $stamp_id);
					$insert_stmt3->execute();
					$insert_stmt3->close();
				}


				$insert_stmt->close();
				$db->close();
				
				echo json_encode(
					array(
						"status"=> "success", 
						"message"=> "Added Successfully!!" 
					)
				);
			}
		}
		else{
			echo json_encode(
				array(
					"status"=> "failed", 
					"message"=> "Error when creating query"
				)
			); 
		}
	}
} 
else{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );     
}

?>
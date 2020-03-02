<?php
	
	include('pdu_script.php'); 

	$phone = $_GET['phone'];
	$data = $_GET['at'];

	$fg = str_replace(chr(32),'',$data);
	$sep = chr(13).chr(10);
	$exp = explode($sep, $fg);
	
	$name = explode(':',$exp[1])[0];
	$param1 = explode(',',explode(':',$exp[1])[1])[0];
	$param2 = explode(',',explode(':',$exp[1])[1])[1];
	$at_data = $exp[2];
	
	
	$fp = fopen('log.txt','w');
	
	if($_GET['com']=='at'){
		
		fwrite($fp, "com=at\n");
		
		if($_GET['connect']=='1'){
		
			echo"connected";
			
		
		}else{
			
				
			$conn = new mysqli('localhost', 'root', 'password', 'smsreceive');
					
			$conn->query('set names utf8');
			$conn->query("set lc_time_names='ru_RU'");
				
			if($conn->connect_error){
				die("Error ". $conn->connect_error);
			}

				if($name=='CMT'){
						
					fwrite($fp, "CMT=OK\n");
						
					$pdu_decode = getPDUMetaInfo($at_data);
					$exp = explode(':|:',$pdu_decode);
					
					$smsc = explode('[SMSC]#',$exp[0])[1];
					$sender = explode('[Sender]:',$exp[1])[1];
					$timestamp = explode('[TimeStamp]:',$exp[2])[1];
					$TP_PID = explode('[TP_PID]:',$exp[3])[1];
					$TP_DCS = explode('[TP_DCS]:',$exp[4])[1];
					$TP_DCS_popis = explode('[TP_DCS-popis]:',$exp[5])[1];
					$class = explode('[class]:',$exp[6])[1];
					$alphabet = explode('[Alphabet]:',$exp[7])[1];
					$sms = explode('[SMS]:',$exp[8])[1];
					$length = explode('[Length]:',$exp[9])[1];
					

					if($conn->query("INSERT INTO t$phone(smsc,sender,timestamp,tp_pid,tp_dsc,tp_dsc_popis,class,alphabet,sms,length,source) 
						VALUES('$smsc','$sender','$timestamp','$TP_PID','$TP_DCS','$TP_DCS_popis','$class','$alphabet','$sms','$length','$data')")===TRUE){	

						fwrite($fp, "sql OK\n");
						echo"success";
					
					}else{
						fwrite($fp, "sql ERROR:".$conn->error."\n");
						echo"error";
					}
							
				}else{
					
					fwrite($fp, "sql UNCNOWN COMMAND\n");
					echo"uncnown command";
					
				}
	 
	 
														
				if($conn->query("UPDATE parameters SET value='".time()."' WHERE name='online'")==TRUE){
					fwrite($fp, "update=ok\n");
				}else{
					fwrite($fp, "update=error".$conn->error ."\n");
				}	
					 
			$conn->close();			
			
		}

		
		
	}
	

	fclose($fp);
?>

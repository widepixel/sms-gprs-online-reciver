<?php


	$sevenbitdefault = Array('@', '£', '$', '¥', 'è', 'é', 'ù', 'ì', 'ò', 'Ç', '\n', 'Ø', 'ø', '\r','Å', 'å','\u0394', '_', '\u03a6', '\u0393', '\u039b', '\u03a9', '\u03a0','\u03a8', '\u03a3', '\u0398', '\u039e','&#8364;', 'Æ', 'æ', 'ß', 'É', ' ', '!', '"', '#', '¤', '%', '&', '\'', '(', ')','*', '+', ',', '-', '.', '/', '0', '1', '2', '3', '4', '5', '6', '7','8', '9', ':', ';', '<', '=', '>', '?', '¡', 'A', 'B', 'C', 'D', 'E','F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S','T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'Ä', 'Ö', 'Ñ', 'Ü', '§', '¿', 'a','b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o','p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', 'ä', 'ö', 'ñ','ü', 'à');


	$calculation = "";

	$maxkeys = 160;
	$alerted = false;

// function te convert a bit string into a integer
function binToInt($x)//sp
{
	$total = 0;
	$len = strlen($x);
	$power = intval($len)-1;

	for($i=0;$i<$len;$i++)
	{
		if(substr($x,$i,1) == '1')
		{
		 $total = $total + pow(2,$power);
		}
		$power --;
	}
	return $total;
}


// function to convert a integer into a bit String
function intToBin($x,$size) //sp
{
	$base = 2;
	$num = intval($x);
	$bin = base_convert($num, 10, $base);
	$len = strlen($bin);
	for($i=$len;$i<$size;$i++)
	{
		$bin = "0".$bin;
	}
	return $bin;
}


// function to convert a Hexnumber into a 10base number
	
function HexToNum($numberS)
{
	$tens = MakeNum(substring($numberS,0,1));

	$ones = 0;
	$len = strlen($numberS);
	if($len > 1) // means two characters entered
		$ones = MakeNum(substring($numberS,1,2));
	if($ones == 'X')
	{
		return "00";
	}
	return  ($tens * 16) + ($ones * 1);
}


// helper function for HexToNum
function MakeNum($str)
{
	if(($str >= 0) && ($str <= 9) && (is_numeric($str)))
		return $str;
	switch(strtoupper($str))
	{
		case "A": return 10;
		case "B": return 11;
		case "C": return 12;
		case "D": return 13;
		case "E": return 14;
		case "F": return 15;
		default: return 'X';
		//default: return 'Only insert Hex values!!';
   	}
}



//function to convert integer to Hex

function intToHex($i) //sp
 {
  $sHex = "0123456789ABCDEF";
  $h = "";
  $i = intval($i);
  for($j = 0; $j <= 3; $j++)
  {
    $h .= substr($sHex,($i >> ($j * 8 + 4)) & 0x0F, 1) . substr($sHex,($i >> ($j * 8)) & 0x0F, 1);
  }
  return substr($h,0,2);
}


function ToHex($i)
{
	$sHex = "0123456789ABCDEF";
	$Out = "";

	$Out = substr($sHex, ($i&0xf), 1);
	$i>>=4;
	$Out = substr($sHex, ($i&0xf), 1) . $Out;

	return $Out;
}

function getSevenBit($character) //sp
{
	global $sevenbitdefault;

	$count = count($sevenbitdefault);
	for($i=0;$i<$count;$i++)
	{
		if($sevenbitdefault[$i] == $character)
		{
			return $i;
		}
	}
	
	//alert("No 7 bit char!");
	return 0;
}


function getEightBit($character)
{
	return $character;
}

function get16Bit($character)
{
	return $character;
}

// function to convert semioctets to a string
function semiOctetToString($inp) //sp
{
	for($i=0;$i<strlen($inp);$i=$i+2)
	{
	  	$temp = substr($inp,$i,$i+2);
		$out .= substr($temp,1,1) . substr($temp,0,1);
	}

	return $out;
}



//Main function to translate the input to a "human readable" string


function getUserMessage($input,$truelength) // Add truelength AJA
{
	global $sevenbitdefault;
	
	$s=1;
	$count = 0;
	$matchcount = 0; // AJA

	for($i=0;$i<strlen($input);$i=$i+2)
	{
		$hex = substring($input,$i,$i+2);
		$byteString = $byteString.intToBin(HexToNum($hex),8);
	}

	for($i=0;$i<strlen($byteString);$i=$i+8)
	{
		$octetArray[$count] = substring($byteString,$i,$i+8);
		$restArray[$count] = substring($octetArray[$count],0,($s%8));
		$septetsArray[$count] = substring($octetArray[$count],($s%8),8);

		$s++;
        	$count++;
		if($s == 8)
		{
			$s = 1;
		}
	}

	for($i=0;$i<count($restArray);$i++)
	{

		if($i%7 == 0)
		{
			if($i != 0)
			{
				$smsMessage = $smsMessage.$sevenbitdefault[binToInt($restArray[$i-1])];
				$matchcount ++; // AJA
			}
			$smsMessage = $smsMessage.$sevenbitdefault[binToInt($septetsArray[$i])];
			$matchcount ++; // AJA
		}
		else
		{
			$smsMessage = $smsMessage.$sevenbitdefault[binToInt($septetsArray[$i].$restArray[$i-1])];
			$matchcount ++; // AJA
		}

	}
	if($matchcount != $truelength) // Don't forget trailing characters!! AJA
	{
		$smsMessage = $smsMessage.$sevenbitdefault[binToInt($restArray[$i-1])];
	}
	else // Blank Filler
	{}


	return $smsMessage;
}

	


function getUserMessage16($input,$truelength)
{
	// Cut the input string into pieces of 4

	for($i=0;$i<strlen($input);$i=$i+4)
	{

		$hex1 = substring($input,$i,$i+2);
		$hex2 = substring($input,$i+2,$i+4);
		$smsMessage .= "".mb_convert_encoding('&#'.intval(HexToNum($hex1)*256+HexToNum($hex2)) . ';', 'UTF-8', 'HTML-ENTITIES');

	}

	return $smsMessage;
}
	

function getUserMessage8($input,$truelength)
{
	$smsMessage = "";
	$calculation = "Not implemented";

	// Cut the input string into pieces of 2 (just get the hex octets)
	for($i=0;$i<strlen($input);$i=$i+2)
	{
		$hex = substring($input,$i,$i+2);
		$smsMessage .= "".mb_convert_encoding('&#'.intval(HexToNum($hex)) . ';', 'UTF-8', 'HTML-ENTITIES');
	}

	return $smsMessage;
}



function substring($string, $from, $to){
    return substr($string, $from, $to - $from);
}
	

// Function to get SMSmeta info information from PDU String
function getPDUMetaInfo($inp)
{
	$PDUString = $inp;
	$start = 0;

	$SMSC_lengthInfo = HexToNum(substring($PDUString,0,2));
	$SMSC_info = substring($PDUString,2,2+($SMSC_lengthInfo*2));
	$SMSC_TypeOfAddress = substring($SMSC_info,0,2);
	$SMSC_Number = substring($SMSC_info,2,2+($SMSC_lengthInfo*2));

	if ($SMSC_lengthInfo != 0)
	{
		$SMSC_Number = semiOctetToString($SMSC_Number);

		// if the length is odd remove the trailing  F
		if((substr($SMSC_Number,strlen($SMSC_Number)-1,1) == 'F') || (substr($SMSC_Number, strlen($SMSC_Number)-1,1) == 'f'))
		{
			$SMSC_Number = substring($SMSC_Number,0,strlen($SMSC_Number)-1);
		}
		if ($SMSC_TypeOfAddress == 91)
		{
			$SMSC_Number = "+" . $SMSC_Number;
		}
	}

	$start_SMSDeleivery = ($SMSC_lengthInfo*2)+2;

	$start = $start_SMSDeleivery;
	$firstOctet_SMSDeliver = substr($PDUString,$start,2);
	$start = $start + 2;

	if ((HexToNum($firstOctet_SMSDeliver) & 0x03) == 1) // Transmit Message
	{
		$MessageReference = HexToNum(substr($PDUString,$start,2));
		$start = $start + 2;

		// length in decimals
		$sender_addressLength = HexToNum(substr($PDUString,$start,2));
		if($sender_addressLength%2 != 0)
		{
			$sender_addressLength +=1;
		}
		$start = $start + 2;

		$sender_typeOfAddress = substr($PDUString,$start,2);
		$start = $start + 2;

		$senderValue = substring($PDUString,$start,$start+$sender_addressLength);

		
		if((stristr($senderValue,'a')) || (stristr($senderValue,'b')) || (stristr($senderValue,'c')) || (stristr($senderValue,'d')) || (stristr($senderValue,'e'))){

			$sender_number = decodePDU($senderValue);
		
		}else{
			
			$sender_number = semiOctetToString($senderValue);
			
			if((substr($sender_number,strlen($sender_number)-1,1) == 'F') || (substr($sender_number,strlen($sender_number)-1,1) == 'f' ))
			{
				$sender_number =	substring($sender_number,0,strlen($sender_number)-1);
			}
			if ($sender_typeOfAddress == 91)
			{
				$sender_number = "+" . $sender_number;
			}
						
		}		

		
		
		
		
		
	        $start +=$sender_addressLength;

		$tp_PID = substr($PDUString,$start,2);
	        $start +=2;

	        $tp_DCS = substr($PDUString,$start,2);
	        $tp_DCS_desc = tpDCSMeaning($tp_DCS);
	        $start +=2;

		$ValidityPeriod = HexToNum(substr($PDUString,$start,2));
	        $start +=2;

// Commonish...
		$messageLength = HexToNum(substr($PDUString,$start,2));

	        $start += 2;

		$bitSize = DCS_Bits($tp_DCS);
	    	$userData = "Undefined format";
		if ($bitSize==7)
		{
			$userData = getUserMessage(substr($PDUString,$start,strlen($PDUString)-$start),$messageLength);
		}
		else if ($bitSize==8)
		{
			$userData = getUserMessage8(substr($PDUString,$start,strlen($PDUString)-$start),$messageLength);
		}
		else if ($bitSize==16)
		{
			$userData = getUserMessage16(substr($PDUString,$start,strlen($PDUString)-$start),$messageLength);
		}

		$userData = substr($userData,0,$messageLength);
		if ($bitSize==16)
		{
			$messageLength/=2;
		}

		$out =  "[SMSC]#".$SMSC_Number.":|:[Sender]:".$sender_number.":|:[TP_PID]:".$tp_PID.":|:[TP_DCS]:".$tp_DCS.":|:[TP_DCS-popis]:".$tp_DCS_desc.":|:[SMS]:".$userData.":|:[Length]:".$messageLength;
	}
	else // Receive Message
	if ((HexToNum($firstOctet_SMSDeliver) & 0x03) == 0) // Receive Message
	{
		// length in decimals
		$sender_addressLength = HexToNum(substr($PDUString,$start,2));
		if($sender_addressLength%2 != 0)
		{
			$sender_addressLength +=1;
		}
		$start = $start + 2;

		$sender_typeOfAddress = substr($PDUString,$start,2);
		$start = $start + 2;

		
		$senderValue = substring($PDUString,$start,$start+$sender_addressLength);
		
		if((stristr($senderValue,'a')) || (stristr($senderValue,'b')) || (stristr($senderValue,'c')) || (stristr($senderValue,'d')) || (stristr($senderValue,'e'))){
			
			$sender_number = decodePDU($senderValue);
			
		}else{
			
			$sender_number = semiOctetToString($senderValue);
			
			if((substr($sender_number,strlen($sender_number)-1,1) == 'F') || (substr($sender_number,strlen($sender_number)-1,1) == 'f' ))
			{
				$sender_number =	substring($sender_number,0,strlen($sender_number)-1);
			}
			if ($sender_typeOfAddress == 91)
			{
				$sender_number = "+" . $sender_number;
			}
			
			
		}







		 $start +=$sender_addressLength;

		$tp_PID = substr($PDUString,$start,2);
	        $start +=2;

	        $tp_DCS = substr($PDUString,$start,2);
	        $tp_DCS_desc = tpDCSMeaning($tp_DCS);
	        $start +=2;

		$timeStamp = semiOctetToString(substr($PDUString,$start,14));

		// get date
		$year = substring($timeStamp,0,2);
		$month = substring($timeStamp,2,4);
		$day = substring($timeStamp,4,6);
		$hours = substring($timeStamp,6,8);
		$minutes = substring($timeStamp,8,10);
		$seconds = substring($timeStamp,10,12);

		$timeStamp = $day."/".$month."/".$year." ".$hours.":".$minutes.":".$seconds;
		$start +=14;

// Commonish...
		$messageLength = HexToNum(substr($PDUString,$start,2));
	        $start += 2;

		$bitSize = DCS_Bits($tp_DCS);
	    	$userData = "Undefined format";
		if ($bitSize==7)
		{
			$userData = getUserMessage(substr($PDUString,$start,strlen($PDUString)-$start),$messageLength);
		}
		else if ($bitSize==8)
		{
			$userData = getUserMessage8(substr($PDUString,$start,strlen($PDUString)-$start),$messageLength);
		}
		else if ($bitSize==16)
		{
			$userData = getUserMessage16(substr($PDUString,$start,strlen($PDUString)-$start),$messageLength);
		}

		$userData = substr($userData,0,$messageLength);

		if ($bitSize==16)
		{
			$messageLength/=2;
		}

		$out =  "[SMSC]#".$SMSC_Number.":|:[Sender]:".$sender_number.":|:[TimeStamp]:".$timeStamp.":|:[TP_PID]:".$tp_PID.":|:[TP_DCS]:".$tp_DCS.":|:[TP_DCS-popis]:".$tp_DCS_desc.":|:[SMS]:".$userData.":|:[Length]:".$messageLength;
	}
	else
	{
		$out =  "Unhandled message";
	}

	return $out;
}







function tpDCSMeaning($tp_DCS)
{
	$tp_DCS_desc=$tp_DCS;
        $pomDCS = HexToNum($tp_DCS);
        switch($pomDCS & 192)
	{
		case 0: if($pomDCS & 32)
			{
				$tp_DCS_desc="Compressed Text:|:";
			}
			else
			{
				$tp_DCS_desc="Uncompressed Text:|:";
			}
			if($pomDCS & 16)
			{
				$tp_DCS_desc.="No class:|:";
			}
			else
			{
			  	$tp_DCS_desc.="[class]:";

		  		switch($pomDCS & 3)
				{
					case 0:
						$tp_DCS_desc.="0:|:";
						break;
					case 1:
						$tp_DCS_desc.="1:|:";
						break;
					case 2:
						$tp_DCS_desc.="2:|:";
						break;
					case 3:
						$tp_DCS_desc.="3:|:";
						break;
				}
			}
                        $tp_DCS_desc.="[Alphabet]:";
			switch($pomDCS & 12)
			{
				case 0:
					$tp_DCS_desc.="Default";
					break;
				case 4:
					$tp_DCS_desc.="8bit";
					break;
				case 8:
					$tp_DCS_desc.="UCS2(16)bit";
					break;
				case 12:
					$tp_DCS_desc.="Reserved";
					break;
			}
			break;
                case 64:
                case 128:
			$tp_DCS_desc ="Reserved coding group";
			break;
		case 192:
			switch($pomDCS & 0x30)
			{
				case 0:
					$tp_DCS_desc ="Message waiting group";
					$tp_DCS_desc.="Discard";
					break;
				case 0x10:
					$tp_DCS_desc ="Message waiting group";
					$tp_DCS_desc.="Store Message. Default Alphabet";
					break;
				case 0x20:
					$tp_DCS_desc ="Message waiting group";
					$tp_DCS_desc.="Store Message. UCS2 Alphabet";
					break;
				case 0x30:
					$tp_DCS_desc ="Data coding message class";
					if ($pomDCS & 0x4)
					{
						$tp_DCS_desc.="Default Alphabet";
					}
					else
					{
						$tp_DCS_desc.="8 bit Alphabet";
					}
					break;
			}
			break;

	}

	//alert(tp_DCS.valueOf());

        return($tp_DCS_desc);
}
	
	
	
	
	
	
	

function DCS_Bits($tp_DCS)
{
	$AlphabetSize=7; // Set Default
//alert(tp_DCS);
        $pomDCS = HexToNum($tp_DCS);
//alert(pomDCS);
        switch($pomDCS & 192)
	{
		case 0: if($pomDCS & 32)
			{
				// tp_DCS_desc="Compressed Text\n";
			}
			else
			{
				// tp_DCS_desc="Uncompressed Text\n";
			}
			switch($pomDCS & 12)
			{
				case 4:
					$AlphabetSize=8;
					break;
				case 8:
					$AlphabetSize=16;
					break;
			}
			break;
		case 192:
			switch($pomDCS & 0x30)
			{
				case 0x20:
					$AlphabetSize=16;
					break;
				case 0x30:
					if ($pomDCS & 0x4)
					{
						;
					}
					else
					{
						$AlphabetSize=8;
					}
					break;
			}
			break;

	}

        return($AlphabetSize);
}

	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	

function decodePDU($in) {
 $b = 0; $d = 0;
 $out = "";
 foreach (str_split($in, 2) as $ss) {
  $byte = hexdec($ss);
  $c = (($byte & ((1 << 7-$d)-1)) << $d) | $b;
  $b = $byte >> (7-$d);
  $out .= chr($c);
  $d++;
  if ($d == 7) {
   $out .= chr($b);
   $d = 0; $b = 0;
  }
 }
 return $out;
}

function encodePDU($in) {
 $out = "";
 for ($i = 0; $i < strlen($in); $i++) {
  $t = $i%8+1;
  if ($t == 8) 
   continue;
  $c = ord($in[$i])>>($i%8);
  $oc = $c;
  $b = ord($in[$i+1]) & ((1 << $t)-1);
  $c = ($b << (8-$t)) | $c;
  $out .= strtoupper(str_pad(dechex($c), 2, '0', STR_PAD_LEFT));
 }
 return $out;
}

?>

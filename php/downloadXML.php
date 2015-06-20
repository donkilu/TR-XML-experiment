<html>
<?php
	// first check whether today's xml is downloaded
	date_default_timezone_set("Asia/Taipei");
	$today_str = date(Ymd);
	$xml_dir   = "../XML/";
	$local_xml = $xml_dir . $today_str . ".xml";

	// delete all the old files in ../XML/
	foreach(glob($xml_dir."*.*") as $filename){
		if(is_file($filename)){
			unlink($filename);
		}
	}		
	// load php zip extension if not loaded
	if (!extension_loaded('zip')) {  
		dl('zip.so');  
	}
	$target_url = "http://163.29.3.98/XML/" . $today_str . ".zip";
	//$target_url = "http://163.29.3.98/XML/20150620.zip";	
	$userAgent = 'Donki Works (http://donki.comuv.com)';  
	$file_zip = $xml_dir . $today_str . ".zip";  
	echo "<br>Starting<br>Target_url: $target_url";  
	echo "<br>Headers stripped out";  
	// make the cURL request to $target_url  
	$ch = curl_init();  
	$fp = fopen("$file_zip", "w");  
	curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);  
	curl_setopt($ch, CURLOPT_URL,$target_url);  
	curl_setopt($ch, CURLOPT_FAILONERROR, true);  
	curl_setopt($ch, CURLOPT_HEADER,0);  
	//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);  
	curl_setopt($ch, CURLOPT_AUTOREFERER, true);  
	curl_setopt($ch, CURLOPT_BINARYTRANSFER,true);  
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);  
	curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);  
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);   
	curl_setopt($ch, CURLOPT_FILE, $fp);  
	$page = curl_exec($ch);  
	if (!$page) {  
		echo "<br />cURL error number:" .curl_errno($ch);  
		echo "<br />cURL error:" . curl_error($ch);  
		exit;  
	}  
	curl_close($ch);  
	echo "<br>Downloaded file: $target_url";  
	echo "<br>Saved as file: $file_zip";  
	echo "<br>About to unzip ...";  
	// Un zip the file  
	$zip = new ZipArchive;  
	if (! $zip) {  
		echo "<br>Could not make ZipArchive object.";  
		exit;  
	}  
	if($zip->open("$file_zip") != "true") {  
	   echo "<br>Could not open $file_zip";  
	}  
	$zip->extractTo("$xml_dir");  
	$zip->close();  
	echo "<br>Unzipped file to: $xml_dir<br><br>";
	Header("Location:'analyzeXML.php'");
?>
</html>
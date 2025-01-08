<html>
<?php
    // first check whether today's xml is downloaded
    date_default_timezone_set("Asia/Taipei");
    $today_str = date("Ymd");
    $xml_dir   = "../XML/";
    $local_xml = $xml_dir . $today_str . ".xml";

    // delete all the old files in ../XML/
    foreach(glob($xml_dir."*.*") as $filename)
    {
        if(is_file($filename))
        {
            unlink($filename);
        }
    }

    $curl = curl_init();
    $tra_xml_list_url = 'https://ods.railway.gov.tw/tra-ods-web/ods/download/dataResource/railway_schedule/XML/list';
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => $tra_xml_list_url,
        CURLOPT_USERAGENT => 'donkilu'
    ));
    $resp = curl_exec($curl);
    $pattern = '/.+ href="(.+)"\>' . $today_str . '\.xml<\/a><\/td>/';
    preg_match_all($pattern, $resp, $matches);
    $actual_xml_url = "https://ods.railway.gov.tw" . $matches[1][0];
    echo $actual_xml_url;

    // Download actual file
    $fp = fopen("$local_xml", "w");
    curl_setopt_array($curl, array(
        CURLOPT_URL => $actual_xml_url,
        CURLOPT_TIMEOUT => 60,
        CURLOPT_SSL_VERIFYHOST => 0,
        CURLOPT_SSL_VERIFYPEER => 0,
        CURLOPT_FILE => $fp,
        CURLOPT_USERAGENT => 'donkilu'
    ));
    $xml_resp = curl_exec($curl);
    if (!$xml_resp) {
        echo "<br />cURL error number:" .curl_errno($curl);
        echo "<br />cURL error:" . curl_error($curl);
        exit;
    }
    curl_close($curl);

    // Go back to analyzeXML
    Header("Location:analyzeXML.php");
?>
</html>
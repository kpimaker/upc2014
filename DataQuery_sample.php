<?php

$username=""; //enter your login to AT Internet
$password=""; //your password
$siteID=""; //xtsite parameter value in AT counter code

//Parameter siteID already entered in this line
//Attention! If you use other URL you should replace "$" signs with "\$" in it
$url="https://apirest.atinternet-solutions.com/data/v2/json/getData?&columns={d_time_date,m_visits}&sort={d_time_date}&space={s:".$siteID."}&period={R:{D:{start:'-7',end:'-1'}}}&max-results=10000";

//Setting options for Data Query API request
$options = array(
  CURLOPT_URL => $url,
  CURLOPT_USERPWD => "$username:$password",
  CURLOPT_RETURNTRANSFER => TRUE
);

$ch = curl_init(); //initializing request
curl_setopt_array($ch, $options); //setting options
$data = curl_exec($ch); //performing request to Data Query API, writing response to $data parameter

curl_close($ch); //closing request

//echo $data; //if you want to see full API response in JSON format uncomment this line

$data = json_decode($data, true); //converting full API response from JSON to array

//To see JSON converted to array uncomment next 3 lines. It's easy to work with array when you see it by hierarchy
//echo "<pre>";
//print_r($data);
//echo "</pre>";

echo "Number of visits for yesterday ".$data[DataFeed][0][Rows][6][d_time_date]." - ".$data[DataFeed][0][Rows][6][m_visits];

echo "<br /> <br />";
echo "Full table of visits for last 7 days: <br />";

//Writing visits from array to browser and to file "Last week visits.txt"
for ($i = 0; $i < count($data[DataFeed][0][Rows]); ++$i) {
  echo $data[DataFeed][0][Rows][$i][d_time_date]." - ".$data[DataFeed][0][Rows][$i][m_visits]."<br />";
  file_put_contents("Last week visits.txt", $data[DataFeed][0][Rows][$i][d_time_date]." - ".$data[DataFeed][0][Rows][$i][m_visits]."\n", FILE_APPEND);
}

//Sending file by mail
$file = "Last week visits.txt"; // file name
$mailTo = "mymail@mysite.com"; // to - your email
$from = "analytics@mysite.com"; // from - you can choose any address (sometimes mail drops to spam folder)
$subject = "Daily visits"; // subject
$message = ""; // mail body
$r = sendMailAttachment($mailTo, $from, $subject, $message, $file); // sending mail

function sendMailAttachment($mailTo, $from, $subject, $message, $file = false){
    $separator = "---"; // mail separator
    // Mail headers
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "From: $from\nReply-To: $from\n"; // from options
    $headers .= "Content-Type: multipart/mixed; boundary=\"$separator\""; // separator in title
    // sending attachment
    if($file){
        $bodyMail = "--$separator\n"; // writing separator
        $bodyMail .= "Content-type: text/html; charset='utf-8'\n"; // mail coding
        $bodyMail .= "Content-Transfer-Encoding: quoted-printable"; // mail encoding
        $bodyMail .= "Content-Disposition: attachment; filename==?utf-8?B?".base64_encode(basename($file))."?=\n\n"; // file name
        $bodyMail .= $message."\n"; // adding mail text
        $bodyMail .= "--$separator\n";
        $fileRead = fopen($file, "r"); // opening file
        $contentFile = fread($fileRead, filesize($file)); // reading file
        fclose($fileRead); // closing file
        $bodyMail .= "Content-Type: application/octet-stream; name==?utf-8?B?".base64_encode(basename($file))."?=\n"; 
        $bodyMail .= "Content-Transfer-Encoding: base64\n"; // file encoding
        $bodyMail .= "Content-Disposition: attachment; filename==?utf-8?B?".base64_encode(basename($file))."?=\n\n";
        $bodyMail .= chunk_split(base64_encode($contentFile))."\n"; // coding and attaching file
        $bodyMail .= "--".$separator ."--\n";
    // if you have no attachments
    }else{
        $bodyMail = $message;
    }
    $result = mail($mailTo, $subject, $bodyMail, $headers); // sending mail
    return $result;
}
?>

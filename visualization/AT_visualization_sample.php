<?php

$username=""; //enter your login to AT Internet
$password=""; //your password
$siteID=""; //xtsite parameter value in AT counter code

//Parameter siteID already entered in this line
//Attention! If you use other URL you should replace "$" signs with "\$"
$url="https://apirest.atinternet-solutions.com/data/v2/json/getData?&columns={d_time_date,m_visitors,m_page_loads}&sort={d_time_date}&space={s:".$siteID."}&period={R:{D:{start:'-7',end:'-1'}}}&max-results=1000";

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

$fp = fopen("Visitors views ratio.txt", "w");

//Writing page views / visitors ratio to browser and to file "Visitors views ratio.txt"
for ($i = 0; $i < count($data[DataFeed][0][Rows]); ++$i) {
  //echo round($data[DataFeed][0][Rows][$i][m_page_loads] / $data[DataFeed][0][Rows][$i][m_visitors], 2)."<br />";
  file_put_contents("Visitors views ratio.txt", round($data[DataFeed][0][Rows][$i][m_page_loads] / $data[DataFeed][0][Rows][$i][m_visitors], 2)."\n", FILE_APPEND);
  file_put_contents("Visitors views ratio.txt", $data[DataFeed][0][Rows][$i][d_time_date]."\n", FILE_APPEND);
}

?>

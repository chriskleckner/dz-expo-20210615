<?php

add_shortcode('olo_locations','shortcode_get_olo_locations');
function shortcode_get_olo_locations(){

	if( isset($_GET['sandbox'])!=false ){
		$api_key = '9zpN7F7sK8Q1iq2qoDCWaV9Gzn6Jpspw';
		$url = 'https://ordering.api.olosandbox.com/v1.1/restaurants?key='.$api_key;
	} else {
		$api_key = 'jeydI8ODC9SKR2KSQCsBMJPdbbb3r2so';
		$url = 'https://ordering.api.olo.com/v1.1/restaurants?key='.$api_key;
	}
	
	$curl = curl_init();

	curl_setopt_array($curl, array(
		CURLOPT_URL => $url,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_ENCODING => "",
		CURLOPT_MAXREDIRS => 10,
		CURLOPT_TIMEOUT => 0,
		CURLOPT_FOLLOWLOCATION => true,
		CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		CURLOPT_CUSTOMREQUEST => "GET",
	));

	$response = curl_exec($curl);
	
	curl_close($curl);

	$restaurants = json_decode(json_encode(json_decode($response,true)))->restaurants;

	array_multisort( 
		array_column($restaurants, 'state'), SORT_ASC,
		array_column($restaurants, 'city'), SORT_ASC,
		array_column($restaurants, 'name'), SORT_ASC,
		array_column($restaurants, 'isavailable'), SORT_ASC,
		$restaurants
		);

	$restaurants = filterArrayByKeyValue($restaurants,'isavailable',true);
	
	ob_start();

?>
	<div id="locations-list">
		<?php
	
		$states = array_unique(array_column($restaurants, 'state'));
		foreach($states as $state) {
			echo '<ul>';
			echo '<li><h3>'.get_state_name($state).'</h3>';
			echo '<ul>';
			$stores = filterArrayByKeyValue($restaurants,'state',$state);
			$city = '';
			foreach($stores as $store){
				if( $store->city != $city )
					echo '<li>'.$store->city.'</li>';
				$city = $store->city;
			}
			echo '</li></ul>';
			echo '</ul>';
		}

		?>
	</div>
<?php
	return ob_get_clean();
}

// this should live in a file with other common functions accessible from mutiple places. 
function get_state_name($abbr) {
	$state_name = '';
	switch( strtolower($abbr) ) {
		case 'al': $state_name = 'Alabama'; break;
		case 'ak': $state_name = 'Alaska'; break;
		case 'az': $state_name = 'Arizona'; break;
		case 'ar': $state_name = 'Arkansas'; break;
		case 'ca': $state_name = 'California'; break;
		case 'co': $state_name = 'Colorado'; break;
		case 'ct': $state_name = 'Connecticut'; break;
		case 'dc': $state_name = 'District of Columbia'; break;
		case 'de': $state_name = 'Delaware'; break;
		case 'fl': $state_name = 'Florida'; break;
		case 'ga': $state_name = 'Georgia'; break;
		case 'hi': $state_name = 'Hawaii'; break;
		case 'id': $state_name = 'Idaho'; break;
		case 'il': $state_name = 'Illinois'; break;
		case 'in': $state_name = 'Indiana'; break;
		case 'ia': $state_name = 'Iowa'; break;
		case 'ks': $state_name = 'Kansas'; break;
		case 'ky': $state_name = 'Kentucky'; break;
		case 'la': $state_name = 'Louisiana'; break;
		case 'me': $state_name = 'Maine'; break;
		case 'md': $state_name = 'Maryland'; break;
		case 'ma': $state_name = 'Massachusetts'; break;
		case 'mi': $state_name = 'Michigan'; break;
		case 'mn': $state_name = 'Minnesota'; break;
		case 'ms': $state_name = 'Mississippi'; break;
		case 'mo': $state_name = 'Missouri'; break;
		case 'mt': $state_name = 'Montana'; break;
		case 'ne': $state_name = 'Nebraska'; break;
		case 'nv': $state_name = 'Nevada'; break;
		case 'nh': $state_name = 'New Hampshire'; break;
		case 'nj': $state_name = 'New Jersey'; break;
		case 'nm': $state_name = 'New Mexico'; break;
		case 'ny': $state_name = 'New York'; break;
		case 'nc': $state_name = 'North Carolina'; break;
		case 'nd': $state_name = 'North Dakota'; break;
		case 'oh': $state_name = 'Ohio'; break;
		case 'ok': $state_name = 'Oklahoma'; break;
		case 'or': $state_name = 'Oregon'; break;
		case 'pa': $state_name = 'Pennsylvania'; break;
		case 'ri': $state_name = 'Rhode Island'; break;
		case 'sc': $state_name = 'South Carolina'; break;
		case 'sd': $state_name = 'South Dakota'; break;
		case 'tn': $state_name = 'Tennessee'; break;
		case 'tx': $state_name = 'Texas'; break;
		case 'ut': $state_name = 'Utah'; break;
		case 'vt': $state_name = 'Vermont'; break;
		case 'va': $state_name = 'Virginia'; break;
		case 'wa': $state_name = 'Washington'; break;
		case 'wv': $state_name = 'West Virginia'; break;
		case 'wi': $state_name = 'Wisconsin'; break;
		case 'wy': $state_name = 'Wyoming'; break;
		default: $state_name = $abbr; break;
	}
	return $state_name;
}


function filterArrayByKeyValue($array, $key, $keyValue) {
    return array_filter($array, function($value) use ($key, $keyValue) {
       return $value->$key == $keyValue; 
    });
}
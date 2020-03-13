<?php
    //get key để tạo link api get link video
	function getKey($url){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL,$url); 
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_BINARYTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
		'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_1 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/10.0 Mobile/14E304 Safari/602.1)'
		));
		$response = curl_exec($curl);
		if ($response === FALSE) {
			echo 'An error has occurred: ' . curl_error($curl) . PHP_EOL;
		}
		else {
			$response = htmlspecialchars($response,ENT_NOQUOTES,"UTF-8",true);
		}
		curl_close($curl);

		preg_match_all('/(?<=dytk: ")[^"]+/', $response, $key);
		return $key[0][0];
	}
	
    //get id video để tạo link api get link video
	function getIDVideo($url){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL,$url); 
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_1 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/10.0 Mobile/14E304 Safari/602.1)'
		));
		$response = curl_exec($curl);
		$last_url = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
		curl_close($curl);
		preg_match('/\d+/', $last_url, $id);
		return $id[0];
	}
	
	//api lấy link để get link video
	function connectApi($id,$key){
		$url = "https://www.iesdouyin.com/web/api/v2/aweme/iteminfo/?item_ids={$id}&dytk={$key}";
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL,$url); 
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		$response = curl_exec($curl);
		curl_close($curl);
		$data = json_decode($response);
		return $data->item_list[0]->video->play_addr->url_list[0];
	}
	
    //Lấy link video
	function getRealDownloadLink($url){
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL,$url); 
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array(
			'User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_1 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/10.0 Mobile/14E304 Safari/602.1)'
		));
		$response = curl_exec($curl);
		$last_url = curl_getinfo($curl, CURLINFO_EFFECTIVE_URL);
		curl_close($curl);
		return $last_url;
	}
	
	if(isset($_POST['url'])){
		$url = $_POST['url'];
		if(strpos($url, 'douyin') !== false)
			$id = getIDVideo($url);
		else if(strpos($url,'iesdouyin.com/share/video') !==false){
			$id = preg_match('/\d+/', $last_url, $id)[0];
		}
		$key = getKey($url);
		$apiLinkVideo = connectApi($id,$key);
		echo getRealDownloadLink($apiLinkVideo);
	}else{
		echo "Bạn cần điền link";
	}
?>
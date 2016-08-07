<?php
/////////////////////////////////////////////////////////
//用户信息
$UserID = "";
$UserName = "";
$SSOToken = "";
$SSO_session = "";
/////////////////////////////////////////////////////////
$Cookie = "moegirlSSOUserID=".$UserID."; moegirlSSOUserName=".$UserName."; moegirlSSOToken=".$SSOToken."; moegirlSSO_session=".$SSO_session.";";
function processContent($content){
	//return $content."xxtestEdit";
	return str_replace(
		//将这里的内容
		array("{| class=\"wikitable\" style=\"margin: 1em auto 1em auto;width: 400px;\""),
		//替换成
		array("{| class=\"wikitable\" style=\"margin: 1em auto 1em auto;max-width: 400px;\""),
		////////////////////////
		$content
	);
}

//这里填上需要修改的页面
$editpages = array(
"神乐丽",
"秋月凉",
"卯月卷绪"
);

//请详细填写好修改的理由
$reason = "偶像大师SideM 表格宽度适配";
/////////////////////////////////////////////////////////




/////////////////////////////////////////////////////////
function getToken($title){
	$url = 'https://zh.moegirl.org/api.php';

	$post = array(
			"action" => "query",
			"meta" => "tokens",
			"titles" => $title,
			"format" => "json"
		);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Moegirl iOS Login Proxy');
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));	
	curl_setopt($ch, CURLOPT_TIMEOUT, 20);
	curl_setopt($ch, CURLOPT_COOKIE, $Cookie);
	
	$content = curl_exec($ch);
	$http_status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if ($http_status_code == 200) {
		$data = json_decode($content);
		//print_r($data);
		return $data->query->tokens->csrftoken;
	}else{
		return;
	}
}

function submitChanges($title,$tokens,$content,$summary){
	$url = 'https://zh.moegirl.org/api.php';

	$post = array(
			"action" => "edit",
			"text" => $content,
			"title" => $title,
			"summary" => $summary,
			"token" => $tokens,
			"format" => "json"
		);
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded; charset=UTF-8'));
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Moegirl iOS Login Proxy');
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));	
	curl_setopt($ch, CURLOPT_TIMEOUT, 20);
	curl_setopt($ch, CURLOPT_COOKIE, $Cookie);
	
	$content = curl_exec($ch);
	$http_status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	$data = json_decode($content);
	//print_r($data);
	echo "\t\t".$http_status_code."\t\t".$data->edit->result;
}


foreach ($editpages as $key => $value) {
	echo $key."\t".$value;

	//获取内容
	$title = $value;
	$url = 'https://zh.moegirl.org/index.php?title='.urlencode($title)."&action=raw";
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Moegirl iOS Login Proxy');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	curl_setopt($ch, CURLOPT_TIMEOUT, 20);
	curl_setopt($ch, CURLOPT_COOKIE, $Cookie);
	
	$content = curl_exec($ch);
	$http_status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if ($http_status_code == 200) {
		
		$token = getToken($title);
		echo "\t\t".$token;
		if (!empty($token)) {
			submitChanges($title,$token,processContent($content),$reason);	
		}

	}else{
		echo $http_status_code;
	}

	//结束
	echo "\n";
}

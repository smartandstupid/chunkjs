<?php
define('php-steamlogin', true);
//require_once '../general/ActiveRecord.php';
require_once 'main.php';
require_once 'Unirest.php';
require_once 'simple_html_dom.php';
/**
* Steam Trade PHP Class
* Based on node.js version by Alex7Kom https://github.com/Alex7Kom/node-steam-tradeoffers
*
*
*
* https://github.com/halipso/php-steam-tradeoffers
*/
class SteamTrade
{
	public $webCookies = '';
	public $sessionId = '';
	private $apiKey = '';

	function __construct()
	{
        $SteamLogin = new SteamLogin(array(
            'username' => '',
            'password' => '',
            'datapath' => dirname(__FILE__) //path to saving cache files
        ));
        if($SteamLogin->success) {
            $logindata = $SteamLogin->login();
            $this->webCookies = $logindata[cookies];
            $this->sessionId = $logindata[sessionId];
            $this->getApiKey();

            if($SteamLogin->error != '') echo $SteamLogin->error;
        } else {
            echo $SteamLogin->error;
        }
	}

	public function setup($sessionId, $webCookies) {
		$this->webCookies = $webCookies;
		$this->sessionId = $sessionId;
		$this->getApiKey();
	}

	public function getApiKey() {
		if($this->apiKey) {
			return;
		}
		$headers = array('Cookie' => $this->webCookies,'Timeout'=> Unirest\Request::timeout(5));
		try {
			$response = Unirest\Request::get('https://steamcommunity.com/dev/apikey',$headers);
		} catch (Exception $e) {
			echo 'Error: '.$e->getMessage(); #TODO: show url in error
			return;
		}

		if($response->code != 200) {
			die("Error getting apiKey. Code:".$response->code);
		}

		$parse = str_get_html($response->body);

		if($parse->find('#mainContents',0)->find('h2',0)->plaintext == 'Access Denied') {
			die('Error: Access Denied!');
		}

		if($parse->find('#bodyContents_ex',0)->find('h2',0)->plaintext == 'Your Steam Web API Key') {
			$key = explode(' ',$parse->find('#bodyContents_ex',0)->find('p',0)->plaintext)[1];
			$this->apiKey = $key;
			return;
		}

		$headers = array('Cookie' => $this->webCookies);
		$body = array('domain' => 'localhost', 'agreeToTerms' => 'agreed', 'sessionid' => $this->sessionId, 'submit' => 'Register');
		$response = Unirest\Request::post('https://steamcommunity.com/dev/registerkey', $headers, $body);
		$this->getApiKey();
	}

	public function loadMyInventory($options) {
		$query = array();

		if($options['language']) {
			$query['l'] = $options['language'];
		}

		if($options['tradableOnly']) {
			$query['trading'] = 1;
		}

		$uri = 'https://steamcommunity.com/my/inventory/json/'.$options['appId'].'/' .$options['contextId'].'/?'.http_build_query($query);
		return $this->_loadInventory(array(), $uri, array('json' => TRUE), $options['contextId'], null);
	}

	private function _loadInventory($inventory, $uri, $options, $contextid, $start = null) {
		$options['uri'] = $uri;

		if($start) {
			$options['uri'] = $options['uri'] + '&' + http_build_query(array('start'=>'start'));
		}

		$headers = array('Cookie' => $this->webCookies,'Timeout'=> Unirest\Request::timeout(5));
		if($options['headers']) {
			foreach ($options['headers'] as $key => $value) {
				$headers[$key] = $value;
			}
		}

		try {
			$response = Unirest\Request::get($uri,$headers);
		} catch (Exception $e) {
			echo 'Error: '.$e->getMessage(); #TODO: show url in error
			return;
		}

		if($response->code != 200) {
			die("Error loading inventory. Code:".$response->code);
		}

		$response = $response->body;

		if(!$response || !$response->rgInventory || !$response->rgDescriptions ) { #TODO: Check rgCurrency
			die('Invalid Response');
		}

		$inventory = array_merge($inventory,array_merge($this->mergeWithDescriptions($response->rgInventory, $response->rgDescriptions, $contextid),$this->mergeWithDescriptions($response->rgCurrency, $response->rgDescriptions, $contextid)));
		if($response->more) {
			return $this->_loadInventory($inventory, $uri, $options, $contextid, $response->more_start);
		} else {
			return $inventory;
		}
	}

	private function mergeWithDescriptions($items, $descriptions, $contextid) {
		$descriptions = (array) $descriptions;
		$n_items = array();
		foreach ($items as $key => $item) {
			$description = (array) $descriptions[$item->classid.'_'.($item->instanceid ? $item->instanceid : 0)];
			$item = (array) $item;
			foreach ($description as $k => $v) {
				$item[$k] = $description[$k];
			}
			// add contextid because Steam is retarded
			$item['contextid'] = $contextid;
			$n_items[] = $item;
		}
		return $n_items;
	}

	private function toAccountID($id) {
	    if (preg_match('/^STEAM_/', $id)) {
	        $split = explode(':', $id);
	        return $split[2] * 2 + $split[1];
	    } elseif (preg_match('/^765/', $id) && strlen($id) > 15) {
	        return bcsub($id, '76561197960265728');
	    } else {
	        return $id;
	    }
	}

	private function toSteamID($id) {
	    if (preg_match('/^STEAM_/', $id)) {
	        $parts = explode(':', $id);
	        return bcadd(bcadd(bcmul($parts[2], '2'), '76561197960265728'), $parts[1]);
	    } elseif (is_numeric($id) && strlen($id) < 16) {
	        return bcadd($id, '76561197960265728');
	    } else {
	        return $id;
	    }
	}

    static function getPriceItems()
    {
        $data730 = file_get_contents('http://sknx.ru/api/730?cur=usd');
        $data730 = json_decode($data730);
        if($data730->success && isset($data730->items)) {
            file_put_contents('prices_730_usd.json', json_encode($data730->items));
        } else if(isset($data730->message)){
            $retMass = [
                'Error' =>
                    ['730_usd' => json_encode($data730->message)]
            ];
            return $retMass;
        }
        sleep(90);
        $data730 = file_get_contents('http://sknx.ru/api/730?cur=rub');
        $data730 = json_decode($data730);
        if($data730->success && isset($data730->items)) {
            file_put_contents('prices_730_rub.json', json_encode($data730->items));
        } else if(isset($data730->message)){
            $retMass = [
                'Error' =>
                    ['730_rub' => json_encode($data730->message)]
            ];
            return $retMass;
        }
        sleep(90);
        $data570 = file_get_contents('http://sknx.ru/api/570?cur=usd');
        $data570 = json_decode($data570);
        if($data570->success && isset($data570->items)){
            file_put_contents('prices_570_usd.json', json_encode($data570->items));
        } else if(isset($data570->message)){
            $retMass = [
                'Error->message' =>
                    ['570_usd' => json_encode($data570->message)]
            ];
            return $retMass;
        }
        sleep(90);
        $data570 = file_get_contents('http://sknx.ru/api/570?cur=rub');
        $data570 = json_decode($data570);
        if($data570->success && isset($data570->items)){
            file_put_contents('prices_570_rub.json', json_encode($data570->items));
        } else if(isset($data570->message)){
            $retMass = [
                'Error->message' =>
                    ['570_rub' => json_encode($data570->message)]
            ];
            return $retMass;
        }


        if($data570->success && isset($data570->items) && $data730->success && isset($data730->items)) {
            return true;
        } else {
            $retMass = [
                'Error->data' =>
                    ['Data570' => json_encode($data570), 'Data730' => json_encode($data730)]
            ];
            return $retMass;
        }
    }

    public function initializePrice($cur) {
        $this->items[570] = json_decode(file_get_contents(__DIR__.'/prices/prices_570_'.$cur.'.json'));
        $this->items[730] = json_decode(file_get_contents(__DIR__.'/prices/prices_730_'.$cur.'.json'));
    }

    public function getItemPrice($market_hash_name, $game) {
        try {
            if($this->items[$game]->$market_hash_name == 'undefined'){
                $median = $this->loadPrice([appid => $game, market_hash_name => $market_hash_name]);
                if(is_string($median)) {
                    return "Error : $median";
                } else if(is_float($median)) {
                    return $median;
                }
            } else{
                return (float)$this->items[$game]->$market_hash_name->price;
            }
        } catch(Exception $e){
            return "Error : $e";
        }
    }

    public function loadPrice($options) {
        $headers = array('Cookie' => $this->webCookies, 'Timeout' => Unirest\Request::timeout(5));
        $uri = 'http://steamcommunity.com/market/priceoverview/?';
        $get = array(
            'currency' => '5',
            'appid' => $options[appid],
            'market_hash_name' => $options[market_hash_name]
        );
        $uri .= http_build_query($get);
        try {
            $response = Unirest\Request::get($uri, $headers);
        } catch (Exception $e) {
            return 'Error: ' . $e->getMessage(); #TODO: show url in error
            exit;
        }
//        $lowest_price = explode(' ', $response->body->lowest_price);
//        $lowest_price = explode(',', $lowest_price[0]);
//        $lowest_price = (float)($lowest_price[0].'.'.$lowest_price[1]);
//
//        $volume = $response->body->volume;

        $median_price = explode(' ', $response->body->median_price);
        $median_price = explode(',', $median_price[0]);
        $median_price = (float)($median_price[0].'.'.$median_price[1]);

        return $median_price;
//        return [
//            'response' => $response->body->lowest_price,
//            'lowest_price' => $lowest_price,
//            'volume' => $volume,
//            'median_price' => $median_price
//        ];
    }

    public function syncInventoryDB($appId, $language) {
        $this->initializePrice($language == 'russian' ? 'rub' : 'usd');
        $appName = $appId == 730 ? 'csgo' : 'dota';
        $itemsInventory = $this->loadMyInventory([
            'language' => $language,
            'tradableOnly' => 1,
            'appId' => $appId,
            'contextId' => '2'
        ]);

        foreach ($itemsInventory as $key => $val) {
            $conn = new ActiveRecord('market_items_'.$appName);
            $resObj = $conn->query(
                ['amount'],
                [
                    'assetid' => $val['id'],
                    'lang' => substr($language, 0, 2),
                    'author' => 0
                ]
            );
            if($resObj['amount'] != '') {
                $upPrice = $conn->update(
                    ['value' => $this->getItemPrice($val['market_hash_name'], $appId)],
                    ['assetid' => $val['id']]
                );
                if($upPrice == false) return('Error update price');
                if($resObj['amount'] != $val['amount']) {
                    $saveUp = $conn->update(
                        ['amount' => $val['amount']],
                        ['assetid' => $val['id']]
                    );
                    if($saveUp == false) return('Error update amount');
                }
            } else {
                $descItem = [
                    'amount' => $val['amount'],
                    'value' => $this->getItemPrice($val['market_hash_name'], $appId),
                    'name' => $val['name'],
                    'assetid' => $val['id'],
                    'classid' => $val['classid'],
                    'icon' => 'http://steamcommunity-a.akamaihd.net/economy/image/'.$val['icon_url'],
                    'lang' => substr($language, 0, 2),
                    'author' => 0
                ];
                $extra = $this->getDesc($val['tags'], $language, $appName);
                foreach ($extra as $key => $name) {
                    if($key == 'Качество' || $key == 'Quality') {
                        if($appName == 'csgo') {
                            $descItem['rarity'] = $name;
                        } else {
                            $descItem['quality'] = $name;
                        }
                    } else if($key == 'Ячейка' || $key == 'Slot') {
                        $descItem['cell'] = $name;
                    } else if($key == 'Тип' || $key == 'Type') {
                        $descItem['type'] = $name;
                    } else if($key == 'Категория' || $key == 'Category') {
                        $descItem['category'] = $name;
                    } else if($key == 'Герой' || $key == 'Hero') {
                        $descItem['hero'] = $name;
                    } else if($key == 'Оформление' || $key == 'Exterior') {
                        $descItem['exterior'] = $name;
                    } else if($key == 'Набор' || $key == 'Collection') {
                        $descItem['collection'] = $name;
                    } else if($key == 'Оружие' || $key == 'Weapon') {
                        $descItem['weapon'] = $name;
                    } else if($key == 'Редкость' || $key == 'Rarity') {
                        $descItem['rarity'] = $name;
                    } else if($key == 'Цвет граффити' || $key == 'Graffiti Color') {
                        $descItem['graffiti'] = $name;
                    } else if($key == 'color') {
                        $descItem['color'] = $name;
                    }
                }
                $saveRes =  $conn->save($descItem);
                if($saveRes == false) exit('Error save item '.$appName);
            }
        }
    }

    public function getDesc($arr, $language, $app) {
        $mass = [];
        foreach ($arr as $key => $val) {
            $conn = new ActiveRecord('market_desc_'.$app);
            $resObj = $conn->query(['name'], ['category_name' => $val->category_name, 'name' => $val->name]);
            if($resObj['name'] == '') {
                $resObj = $conn->save(['category_name' => $val->category_name, 'name' => $val->name, 'lang' => substr($language, 0, 2)]);
                if($resObj === false) return('Error save db');
            }
            $mass[$val->category_name] = $val->name;
            if($val->category_name == 'Редкость' || ($val->category_name == 'Quality' && $app == 'csgo') || $val->category_name == 'Rarity') {
                $mass['color'] = $val->color;
            }
        }
        return $mass;
    }

	public function loadPartnerInventory($options) {

		$form = array(
		    'sessionid' => $this->sessionId,
		    'partner' => $options['partnerSteamId'] ? $options['partnerSteamId'] : $this->toSteamID($options['partnerAccountId']), //$_SESSION['steamid'], //
		    'appid' => $options['appId'],
		    'contextid' => $options['contextId']
	 	);

	 	if($options['language']) {
			$form['l'] = $options['language'];
		}

		$offer = 'new';
		if($options['tradeOfferId']) {
			$offer = $options->tradeOfferId;
		}

		$uri = 'https://steamcommunity.com/tradeoffer/'.$offer.'/partnerinventory/?'.http_build_query($form);

        $itemMass = $this->_loadInventory(array(), $uri, array(
		    'json' => TRUE,
		    'headers' => array(
		      'referer' => 'https://steamcommunity.com/tradeoffer/'.$offer.'/?partner='.$this->toAccountID($options['partnerSteamId'])
		    ) , $options['contextId'], null), 2);

        foreach ($itemMass as $massKey => $massVal ) {
            $itemMass[$massKey][lowprice] = $this->loadPrice(['appid' => $options['appId'], 'market_hash_name' => $massVal[market_hash_name]]);
        }

        return $itemMass;
	}

	public function makeOffer($options) {

		$tradeoffer = array(
		    'newversion' => TRUE,
		    'version' => 2,
		    'me' => array('assets' => $options['itemsFromMe'], 'currency' => array(), 'ready' => FALSE ),
		    'them' => array('assets' => $options['itemsFromThem'], 'currency' => array(), 'ready' => FALSE )
	  	);

	  	$formFields = array(
		    'serverid' => 1,
		    'sessionid' => $this->sessionId,
		    'partner' => $options['partnerSteamId'] ? $options['partnerSteamId'] : $this->toSteamID($options['partnerAccountId']),
		    'tradeoffermessage' => $options['message'] ? $options['message'] : '',
		    'json_tradeoffer' => json_encode($tradeoffer)
	  	);

	  	$query = array(
		    'partner' => $options['partnerAccountId'] ? $options['partnerAccountId'] : $this->toAccountID($options['partnerSteamId'])
		);

	  	if($options['accessToken']) {
	  		$formFields['trade_offer_create_params'] = http_build_query(array('trade_offer_access_token'=>$options['accessToken']));
	  		$query['token'] = $options['accessToken'];
	  	}

	  	$referer = '';
	  	if($options['counteredTradeOffer']) {
	  		$formFields['tradeofferid_countered'] = $options['counteredTradeOffer'];
	  		$referer = 'https://steamcommunity.com/tradeoffer/'.$options['counteredTradeOffer'].'/';
	  	} else {
	  		$referer = 'https://steamcommunity.com/tradeoffer/new/?'.http_build_query($query);
	  	}

	  	$headers = array('Cookie' => $this->webCookies,'Timeout'=> Unirest\Request::timeout(5));
	  	$headers['referer'] = $referer;
	  	$response = Unirest\Request::post('https://steamcommunity.com/tradeoffer/new/send', $headers, $formFields);

	  	if($response->code != 200) {
	  		die('Error making offer! Server response code: '.$response->code);
	  	}

	  	$body = $response->body;

	  	if($body && $body->strError) {
	  		die('Error making offer: '.$body->strError);
	  	}

	  	return $body;
	}

    public function makeOffer2($sessionId, $cookies, $partner, $message = '', $token, $assetid) {
        $type = 'POST';
        $url = 'https://steamcommunity.com/tradeoffer/new/send';

        $steamid = $this->toSteamID($partner);

        $tradeoffer = array(
            'newversion' => TRUE,
            'version' => 2,
            'me' => array('assets' => [], 'currency' => array(), 'ready' => FALSE ),
            'them' => array('assets' => $assetid, 'currency' => array(), 'ready' => FALSE )
        );

        $data= array (
            'sessionid' => $sessionId,
            'serverid' => '1',
            'partner' => $steamid,
            'tradeoffermessage' => $message,
            'trade_offer_create_params' => '{"trade_offer_access_token": "'.$token.'"}',
            'json_tradeoffer' => json_encode($tradeoffer)
        );

        $c = curl_init();
        curl_setopt($c, CURLOPT_HEADER, false);
        curl_setopt($c, CURLOPT_NOBODY, false);
        curl_setopt($c, CURLOPT_URL, $url);
        curl_setopt($c, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($c, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)");
        curl_setopt($c, CURLOPT_COOKIE, $cookies);
        curl_setopt($c, CURLOPT_POST, 1);
        curl_setopt($c, CURLOPT_POSTFIELDS, http_build_query($data));
        curl_setopt($c, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($c, CURLOPT_HTTPHEADER, array('Referer: https://steamcommunity.com/tradeoffer/new/?partner='.$partner.'&token='.$token));
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($c, CURLOPT_CUSTOMREQUEST, strtoupper($type));
        $return = curl_exec($c);
        curl_close($c);

        return $return;
    }

	public function getOffers($options) {
		$offers = $this->doAPICall(
			array(
				'method' => 'GetTradeOffers/v1',
				'params' => $options
			)
		);

		$offers = json_decode(mb_convert_encoding($offers, 'UTF-8', 'UTF-8'),1);

		if($offers['response']['trade_offers_received']) {
			foreach ($offers['response']['trade_offers_received'] as $key => $value) {
				$offers['response']['trade_offers_received'][$key]['steamid_other'] = $this->toSteamID($value['accountid_other']);
			}
		}

		if($offers['response']['trade_offers_sent']) {
			foreach ($offers['response']['trade_offers_sent'] as $key => $value) {
				$offers['response']['trade_offers_sent'][$key]['steamid_other'] = $this->toSteamID($value['accountid_other']);
			}
		}

		return $offers;
	}

	public function getOffer($options) {
		$offer = $this->doAPICall(
			array(
                'method' => 'GetTradeOffer/v1',
                'params' => $options
			)
		);

		$offer = json_decode(mb_convert_encoding($offer, 'UTF-8', 'UTF-8'),1);

		if ($offer['response']['offer']) {
	    	$offer['response']['offer']['steamid_other'] = $this->toSteamId($offer['response']['offer']['accountid_other']);
	    }

		return $offer;
	}

	function getOffer2() {
        $headers = array('Cookie' => $this->webCookies, 'Timeout' => Unirest\Request::timeout(5));
	    $params = [
            'key' => $this->apiKey,
            'tradeofferid' => 2643290970,
            'language' => 'russian'
        ];
        $uri = 'https://api.steampowered.com/IEconService/GetTradeStatus/v1/?'.http_build_query($params);
        try {
            $response = Unirest\Request::get($uri, $headers);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage(); #TODO: show url in error
            exit;
        }

        return $response->body;
    }

    public function getItemInformation($options) {
        $headers = array('Cookie' => $this->webCookies, 'Timeout' => Unirest\Request::timeout(5));
        $uri = 'https://api.steampowered.com/ISteamEconomy/GetAssetClassInfo/v1/?';
        $get = array(
            'key' => $this->apiKey,
            'format' => 'json',
            'appid' => $options['appid'],
            'language' => $options['lang'],
            'class_count' => 1,
            'classid0' => $options['classid'] // class_id
        );
        $uri .= http_build_query($get);
        try {
            $response = Unirest\Request::get($uri, $headers);
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage(); #TODO: show url in error
            exit;
        }
        return $response->body;
    }

	private function doAPICall($options) {
		$uri = 'https://api.steampowered.com/IEconService/'.$options['method'].'/?key='.$this->apiKey.($options['post'] ? '' : ('&'.http_build_query($options['params'])));

		$body = null;
		if($options['post']) {
			$body = $options['params'];
		}

		$response = ($options['post'] ? Unirest\Request::post($uri, null, $body) : Unirest\Request::get($uri));

		if($response->code != 200) {
			die('Error doing API call. Server response code: '.$response->code);
		}

		if(!$response->raw_body) {
			die('Error doing API call. Invalid response.');
		}

		return $response->raw_body;
	}

	public function declineOffer($options) {
		return $this->doAPICall(
			array(
				'method' => 'DeclineTradeOffer/v1',
				'params' => array('tradeofferid' => $options['tradeOfferId']),
				'post' => 1
			)
		);
	}

	public function cancelOffer($options) {
		return $this->doAPICall(
			array(
				'method' => 'CancelTradeOffer/v1',
				'params' => array('tradeofferid' => $options['tradeOfferId']),
				'post' => 1
			)
		);
	}

	public function acceptOffer($options) {

	  	if(!$options['tradeOfferId']) {
	  		die('No options');
	  	}

	  	$form = array(
	  		'sessionid' => $this->sessionId,
	  		'serverid' => 1,
	  		'tradeofferid' => $options['tradeOfferId']
	  		);

	  	$referer = 'https://steamcommunity.com/tradeoffer/'.$options['tradeOfferId'].'/';

	  	$headers = array('Cookie' => $this->webCookies,'Timeout'=> Unirest\Request::timeout(5));
	  	$headers['referer'] = $referer;
	  	$response = Unirest\Request::post('https://steamcommunity.com/tradeoffer/'.$options['tradeOfferId'].'/accept', $headers, $form);

	  	if($response->code != 200) {
	  		die('Error accepting offer. Server response code: '.$response->code);
	  	}

	  	$body = $response->body;

	  	if($body && $body->strError) {
	  		die('Error accepting offer: '.$body->strError);
	  	}

	  	return $body;
	}

	public function getOfferToken() {

		$headers = array('Cookie' => $this->webCookies,'Timeout'=> Unirest\Request::timeout(5));
		$response = Unirest\Request::get('https://steamcommunity.com/my/tradeoffers/privacy', $headers);

		if($response->code != 200) {
			die('Error retrieving offer token. Server response code: '.$response->code);
		}

		$body = str_get_html($response->body);

		if(!$body) {
			die('Error retrieving offer token. Invalid response.');
		}

    	$offerUrl = $body->find('#trade_offer_access_url',0)->value;
    	return explode('=',$offerUrl)[2];
	}

	public function getItems($options) {
		$headers = array('Cookie' => $this->webCookies,'Timeout'=> Unirest\Request::timeout(5));
		$response = Unirest\Request::get('https://steamcommunity.com/trade/'.$options['tradeId'].'/receipt/', $headers);

		if($response->code != 200) {
			die('Error get items. Server response code: '.$response->code);
		}

		$body = $response->body;

		preg_match('/(var oItem;[\s\S]*)<\/script>/', $body, $matches);

		if(!$matches) {
			die('Error get items: no session');
		}

		$temp = str_replace(array("\r", "\n"), "", $matches[1]);

		$items = array();

		preg_match_all('/oItem = {(.*?)};/', $temp, $matches);
		foreach ($matches[0] as $key => $value) {
			$value = rtrim(str_replace('oItem = ', '', $value),';');
			$items[] = json_decode($value,1);
		}

		return $items;
	}

}
?>

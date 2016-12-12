<?
	class Helper {
		
		static function GET(){
			$b = explode("/",rtrim($_GET[tag],"/"));
			if($b[0] == null){ $b[0] = 'collections'; }
			return $b;	
		}
		static function getArrayValueByMatch($data, $prefix){
			
			$return = array();
			foreach($data as $dk => $dv){
				if(strpos($dk,$prefix) === 0){
					$return[str_replace($prefix,'',$dk)] = $dv;
				}
			}
			
			return $return;
		}
		static function currentPageNum(){
		  $num 	= 0;
		  $last = self::getLastParam();
		  
		  $num 	= (is_numeric($last)) ? $last : 1;
		  
		  return $num;	
		}
		
		static function getCookieFilter($filter_prefix = '', $removeKeys = array()){
			if($filter_prefix == '') return false;
			$back = array();
				foreach($_COOKIE as $ck => $cv){
					if(strpos($ck,$filter_prefix) !== false){
						$key = str_replace($filter_prefix.'_','',$ck);
						if(!in_array($key,$removeKeys))
						$back[$key] = $cv;	
					}
				}
			return $back;
		}
		
		static function getParam($arg = array()){
			$get = self::GET();
									
			if(!empty($arg)){
				$pos = 2;
				foreach($arg as $ar){
					if($get[$pos] != null){
						$param[$ar] = $get[$pos];
						$pos++;
					}else{ break; }
				}
			}else{
				$pos = 0;
				foreach($get as $g){
					if($pos > 1){
						$param[] = $g;
					}
					$pos++;
				}	
			}
			
			return $param;
		}
		
		static function getLastParam(){
			$p = self::GET();
			$p = array_reverse($p);
			return $p[0];
		}

		static function cashFormat($cash){
			$cash = number_format($cash,0,""," ");
			return $cash;	
		}
		
		static function makeSafeUrl($str,$after = ''){
			$f 		= array(' ',',','á','Á','é','É','í','Í','ú','Ú','ü','Ü','ű','Ű','ö','Ö','ő','Ő','ó','Ó','(',')','\'','"',"=","/","\\","?","&","!");
			$t 		= array('-','','a','a','e','e','i','i','u','u','u','u','u','u','o','o','o','o','o','o','','','','','','','','','','');
			$str 	= str_replace($f,$t,$str);
			$str 	= strtolower($str);
			
			$ret = $str . $after;
			return $ret;			
		}

		static function getFileRoot($file){
			$ct 	= explode("/",$file);
			$max 	= count($ct);
			$im 	= $ct[$max-1];
			$root 	= str_replace($im,"",$file);
			return $root;
		}

		public static function setMashineID(){
			if(self::getMachineID() == ""){
				setcookie('__mid',mt_rand(),time() + 60*60*24*365*2,"/");
				
				if($_COOKIE['__mid'] != ""){
					header('Location: ');
				}
			}
		}
		
		public static function getMachineID(){
			return $_COOKIE['__mid'];
		}

		static function softDate($d){
			if($d == '0000-00-00 00:00:00' || is_null($d)){ return 'n.a.'; }
			return str_replace("-","/",substr($d,0,-3));
		}
		
		static function emailPatern($str){
			$patern = '
			<html>
				<head>
					<title>{TEMA_NEV}</title>
				</head>
				<body style="margin:0; padding:0;" bgcolor="#f7f7f7">
					<div class="mail" style="margin:35px; background-color:#ffffff; border:2px solid #d7d7d7; padding:0; width:800px; color:#4f565a;">
						<table class="mail" width="800" cellspacing="0" cellpadding="5">
							<tr style="color:#5c5c5c;">
								<td align="left"><a title="'.TITLE.'" href="'.DOMAIN.'"><img src="'.DOMAIN.'images/default_logo.png" height="80" alt="'.DOMAIN.'" /></a></td>
								<td align="right">
									<div style="font-size:20px; font-weight:bold; color:#222;">{TEMA_NEV}</div>
								</td>
							</tr>
							<tr>
								<td colspan="2" align="left">
									<div style="padding:10px; color:#403d3d;">
									{UZENET}
									</div>
								</td>
							</tr>
							<tr bgcolor="#e2e2e2">
								<td align="left">
									<div style="font-size:10px; color:#818181;">{ALAIRAS}</div>
								</td>
								<td align="right">
									<div style="font-size:14px; font-weight:bold; color:#222222;">'.MDOMAIN.'</div>
								</td>
							</tr>
						</table>
					</div>';
					if($str['NEWS']){
						/*$patern .= '<div align="justify" style="color:#9c9c9c; font-size:10px; margin:35px; width:800px;">Nagyon köszönjük, hogy megtisztelt levelünk végigolvasásával! A gazdasági reklámtevékenység alapvető feltételeiről és egyes korlátairól szóló 2008. évi XLVIII. törvény 6. §-ának maximális figyelembevételével, abban reménykedve, hogy Magyarországon egyedülálló szolgáltatást nyujtva, hozzá tudunk járulni az Ön sikereihez. A kapcsolati adataikat Cégnyilvántartásból származó, szabadon elérhető nyilvános cégadatok, cégnév, székhely, ügyvezető, e-mail cím felhasználásával nyertük ki. Nem kívánjuk munkáját rendszeres e-mailekkel zavarni, levelünk célja csupán a lehetőségekre hívja fel a figyelmet, amennyiben nem járul hozzá, hogy a jövőben esetleg levelet küldjünk Önnek, <a href="'.DOMAIN.'maillist/usm/'.$str['EMAIL'].'">kattintson ide</a> és Mi természetesen tiszteletben fogjuk tartani döntését.</div>';*/
					}
					$patern .= '</body>
			</html>
			';
			
			$filledData = str_replace(
				array('{UZENET}','{ALAIRAS}','{TEMA_NEV}'),
				array($str['UZENET'],$str['ALAIRAS'],$str['TEMA_NEV']),
				$patern
			);
			
			return $filledData;
		}

		static function makeAlertMsg($type, $str){
            /*
             * Required: Bootstrap 3 (http://getbootstrap.com/components/#alerts)
             * */
            switch($type){
                case 'pWarning':
                    return '
                    <div class="alert alert-warning alert-dismissable">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                      <strong>'.__('Atention!').'</strong><br/>
                      '.$str.'
                    </div>';
                break;
                case 'pError':
                    return '
                    <div class="alert alert-danger alert-dismissable">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                      <strong>'.__('Error!').'</strong><br/>
                      '.$str.'
                    </div>';
                    break;
                case 'pInfo':
                    return '
                    <div class="alert alert-info alert-dismissable">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                      <strong>'.__('Information:').'</strong><br/>
                      '.$str.'
                    </div>';
                    break;
                case 'pSuccess':
                    return '
                    <div class="alert alert-success alert-dismissable">
                      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                      '.$str.'
                    </div>';
                    break;
                default:
                    return '<div class="'.$type.'">'.$str.'</div>';
                break;
            }
		}

		static function distanceDate($date = NOW){
			if($date == '0000-00-00 00:00:00'){ return 'sose'; }
			$now 		= strtotime(NOW);
			$date 		= strtotime($date);
			$mode 		= 'past'; 
			if($date < $now){
				$dif_sec =  $now - $date ;
			}else{
				$mode = 'future';
				$dif_sec =  $date - $now ;
			}
		
			$ret 		= '';
			///////////////////////////////
			$perc 	= 60;
			$ora 	= $perc * 60;
			$nap 	= $ora * 24;
			$honap 	= $nap * 30;
			$ev 	= $honap * 12;
			///////////////////////////////
				switch($mode){
					case 'past':
						if($dif_sec <= $perc){ // Másodperc
							$ret = $dif_sec.' '. __('másodperce');
						}else if($dif_sec > $perc && $dif_sec <= $ora){ // Perc
							$ret = floor($dif_sec / $perc).' '.__('perce');
						}else if($dif_sec > $ora && $dif_sec <= $nap){ // Óra
							$ret = floor($dif_sec / $ora).' '.__('órája');
						}else if($dif_sec > $nap && $dif_sec <= $honap){ // Nap
							$np = floor($dif_sec / $nap);
							if($np == 1){
								$ret = __('tegnap');
							}else 
								$ret = $np.' '.__('napja');
						}else if($dif_sec > $honap && $dif_sec <= $ev){ // Hónap
							$ret = floor($dif_sec / $honap).' '.__('hónapja');
						}else{ // Év
							$ret = floor($dif_sec / $ev).' '.__('éve');
						}
					break;
					case 'future':
						if($dif_sec <= $perc){ // Másodperc
							$ret = $dif_sec.' '. __('másodperc');
						}else if($dif_sec > $perc && $dif_sec <= $ora){ // Perc
							$ret = floor($dif_sec / $perc).' '.__('perc');
						}else if($dif_sec > $ora && $dif_sec <= $nap){ // Óra
							$ret = floor($dif_sec / $ora).' '.__('óra');
						}else if($dif_sec > $nap && $dif_sec <= $honap){ // Nap
							$np = floor($dif_sec / $nap);
							$ret = $np.' '.__('nap');
						}else if($dif_sec > $honap && $dif_sec <= $ev){ // Hónap
							$ret = floor($dif_sec / $honap).' '.__('hónap');
						}else{ // Év
							$ret = floor($dif_sec / $ev).' '.__('év');
						}
					break;
				}
				
			
			return $ret;
		}	
		
		static function getMonthByNum($mnum){
			$re = $mnum;
				switch($mnum){
					case 1:
						$re = __('január');
					break;
					case 2:
						$re = __('február');
					break;
					case 3:
						$re = __('március');
					break;
					case 4:
						$re = __('április');
					break;
					case 5:
						$re = __('május');
					break;
					case 6:
						$re = __('junius');
					break;
					case 7:
						$re = __('július');
					break;
					case 8:
						$re = __('augusztus');
					break;
					case 9:
						$re = __('szeptember');
					break;
					case 10:
						$re = __('október');
					break;
					case 11:
						$re = __('november');
					break;
					case 12:
						$re = __('december');
					break;

				}
			return $re;
		}
		
		static function get_extension($file_name){
			$ext = explode('.', $file_name);
			$ext = array_pop($ext);
			return strtolower($ext);
		}
		
		static function smtpMail($arg = array()){
			if(is_array($arg[recepiens]) && count($arg[recepiens]) > 0){
				
				date_default_timezone_set('Europe/Budapest');
				
				$mail = new PHPMailer;
				$news = ($arg[news]) ? true : false;
				$from = ($arg[from]) ? $arg[from] : EMAIL;
				$fromName = ($arg[fromName]) ? $arg[fromName] : TITLE;
				
				$mail->isSMTP();                    // Set mailer to use SMTP
				//$mail->Host 		= '';
				$mail->SMTPDebug 	= ($arg[debug]) ? $arg[debug] : 0;
				
				
				$mail->SMTPAuth 	= true;         // Enable SMTP authentication
				$mail->SMTPSecure 	= ($arg[smtp_mode])?$arg[smtp_mode]:SMTP_MODE;    // Enable encryption, 'ssl' also accepted
				$mail->Host 		= SMTP_HOST;  	// Specify main and backup server
				$mail->Port 		= ($arg[smtp_port])?$arg[smtp_port]:SMTP_PORT;
				$mail->Username 	= SMTP_USER;    // SMTP username
				$mail->Password 	= SMTP_PW;      // SMTP password
				
				//echo $mail->Username.':'.$mail->Password.' @ '.$mail->Host;
								
				$mail->From 		= $from;
				$mail->FromName 	= $fromName;
				$mail->addReplyTo($from, $fromName);
				$inserted 			= array();
				$err 				= array();
				$ret 				= array(); 
				
				foreach($arg[recepiens] as $r){
					$mail->addAddress($r);  	
					$mail->WordWrap = 150;                                 // Set word wrap to 50 characters
					$mail->isHTML(true);                                  // Set email format to HTML
					
					$msg = Helper::emailPatern(array(
						'UZENET' 	=> $arg[msg],
						'ALAIRAS' 	=> $arg[alairas],
						'TEMA_NEV' 	=> $arg[tema],
						'NEWS' 		=> $news,
						'EMAIL' 	=> $r
					));
						
					$mail->Subject = $arg[sub];
					$mail->Body    = $msg;
					$mail->AltBody = $mail->html2text($msg);
					
					if (!$mail->send()) {
				       	$emsg 	=  "Kiküldés sikertelen: (" . str_replace("@", "&#64;", $r) . ') ' . $mail->ErrorInfo . '<br />';
						$err[] 	= array('mail' => $r, 'msg' => $emsg);
				        break;
				    }else{
				        $inserted[] = $r;
				    }		
					
					$mail->clearAddresses();
   					$mail->clearAttachments();
				}
				$ret[failed] 	= $err;
				$ret[success] 	= $inserted; 
				return $ret;
			}else return false;
		}
		
		static function sendMail($arg = array()){
			if(is_array($arg[recepiens]) && count($arg[recepiens]) > 0){
				date_default_timezone_set('Europe/Budapest');
				
				$mail = new PHPMailer;
				$news = ($arg[news]) ? true : false;
				$from = ($arg[from]) ? $arg[from] : EMAIL;
				$fromName = ($arg[fromName]) ? $arg[fromName] : TITLE;
							
				$mail->From 		= $from;
				$mail->FromName 	= $fromName;
				$mail->addReplyTo($from, $fromName);
				$inserted 			= array();
				$err 				= array();
				$ret 				= array(); 
				
				foreach($arg[recepiens] as $r){
					$mail->addAddress($r);  	
					$mail->WordWrap = 150;                                 // Set word wrap to 50 characters
					$mail->isHTML(true);                                  // Set email format to HTML
					
					$msg = Helper::emailPatern(array(
						'UZENET' 	=> $arg[msg],
						'ALAIRAS' 	=> $arg[alairas],
						'TEMA_NEV' 	=> $arg[tema],
						'NEWS' 		=> $news,
						'EMAIL' 	=> $r
					));
						
					$mail->Subject = $arg[sub];
					$mail->Body    = $msg;
					$mail->AltBody = $mail->html2text($msg);
					
					if (!$mail->send()) {
				       	$emsg 	=  "Kiküldés sikertelen: (" . str_replace("@", "&#64;", $r) . ') ' . $mail->ErrorInfo . '<br />';
						$err[] 	= array('mail' => $r, 'msg' => $emsg);
				        break;
				    }else{
				        $inserted[] = $r;
				    }		
					
					$mail->clearAddresses();
   					$mail->clearAttachments();
				}
				$ret[failed] 	= $err;
				$ret[success] 	= $inserted; 
				return $ret;
			}else return false;
		}
		
		static function sortAssocArray(&$array, $key, $type = 'ASC') {
		    $sorter=array();
			$ret=array();
				reset($array);
			foreach ($array as $ii => $va) {
				$sorter[$ii]=$va[$key];
			}
			asort($sorter);
			foreach ($sorter as $ii => $va) {
				$ret[$ii]=$array[$ii];
			}
			$array=$ret;
			if($type == "DESC"){
				$array = array_reverse($array);	
			}
		}
	
		static function reload($to = ''){
			$to = ($to == '') ? $_SERVER['HTTP_REFERER'] : $to;
			header('Location: '.$to); exit;
		}
		
		static function getIDFromString($string){
			$x = strpos($string, '_-');
			
			return (int)substr($string,$x+strlen('_-'));
		}
		
	}
?>

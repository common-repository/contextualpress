<?php 
/*
Plugin Name: ContextualPress
Plugin URI: http://www.contextualpress.com/
Description: Special contextual advertising management on your blog by using links to assigned keyword terms in post content
Author: Burak Sırrı Vanlı
Author URI: http://www.burakvanli.com/
Version: 1.0
*/
if(WPLANG=='tr_TR'){include('lang/tr_TR.php');}else{include('lang/en_EN.php');}


function myplugin_install(){
	global $wpdb;
	$table_name = $wpdb->prefix . "contextualpress";
   
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE " . $table_name . " (
			id INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
			word text NOT NULL ,
			value text NOT NULL ,
			des text NOT NULL ,
			tip INT NOT NULL ,
			dtime text NOT NULL,
			hit INT NOT NULL ,
			click INT NOT NULL
			) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_general_ci ;";
		
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}
	
	$upload_dir = wp_upload_dir();
	$upload_path = $upload_dir['basedir'].'/cp';
	@mkdir($upload_path);
	chmod($upload_path.'',0777);

	
	}
	
register_activation_hook(WP_PLUGIN_DIR.'/contextualpress/contextualpress.php','myplugin_install');
	add_action('admin_menu', 'adminoptions');

function adminoptions() {
	
	add_menu_page('<font size=1 family=verdana>ContextualPress</font>', '<font size=1 family=verdana>ContextualPress</font>', 10,  'chart', 'chart','http://www.burakvanli.com/favicon.png');
	add_submenu_page('chart', 'Görsel Reklam Ekle', MENU1, 10,  'visualform', 'visualform');
	add_submenu_page('chart', 'Text Reklam Ekle', MENU2, 10,  'textform', 'textform');
	add_submenu_page('chart', 'Görsel - Text Ekle', MENU3, 10,  'visualtextform', 'visualtextform');
	add_submenu_page('chart', 'Link olarak Ekle', MENU4, 10,  'linkform', 'linkform');
 
}


function visualform(){
	
	echo '<div class="wrap">';
	echo '<h2>'.GORSEL_REKLAM_EKLE.'</h2>';
	
	if ('POST' == $_SERVER['REQUEST_METHOD']) {
		check_admin_referer('update');
		update();
	}
	
	echo '<form method="post" enctype="multipart/form-data"  >';
	
	if ( function_exists('wp_nonce_field') )
        	wp_nonce_field('update');
			echo '
	<INPUT type="hidden" name="process" value="gorselekle">
	<DIV class="form-field form-required">
	<LABEL for="tag-name">'.REKLAM_KELIMESI.'</LABEL>
	<INPUT name="word" type="text" value="" size="40">
	<P>'.REKLAM1.'</P>
	</DIV>
	<DIV class="form-field">
	<LABEL for="tag-slug">'.SITE_LINK.'</LABEL>
	<INPUT name="link"  type="text" value="" size="155">
	<P>'.LINK_ACIKLA.' </P>
	</DIV>
	<DIV class="form-field">
	<LABEL for="tag-description">'.RESIM_SECIN.' '.GORSELBOYUT.'</LABEL>
	<input type="file" name="file0" >
	<P>'.TOOLTIP.'</P>
	</DIV>

	<P class="submit"><input type="submit" class="button"  value="'.KAYDET.'"></P>
	</form>';
	
	echo '</div>';
	
	
	
	}
	
	
function textform(){
	
	echo '<div class="wrap">';
	echo '<h2>'.TEXT_REKLAM_EKLE.'</h2>';
	if ('POST' == $_SERVER['REQUEST_METHOD']) {
		check_admin_referer('update');
		update();
	}
	
	echo '<form method="post"  >';
	
	if ( function_exists('wp_nonce_field') )
        	wp_nonce_field('update');
			echo '
	<INPUT type="hidden" name="process" value="textekle">
	<DIV class="form-field form-required">
	<LABEL for="tag-name">'.REKLAM_KELIMESI.'</LABEL>
	<INPUT name="word" type="text" value="" size="40">
	<P>'.REKLAM1.'</P>
	</DIV>
	<DIV class="form-field">
	<LABEL for="tag-slug">'.SITE_LINK.'</LABEL>
	<INPUT name="link"  type="text" value="" size="155">
	<P>'.LINK_ACIKLA.'</P>
	</DIV>
	<DIV class="form-field">
	<LABEL for="tag-description">'.KISA_REKLAM_METNI.'</LABEL>
	<TEXTAREA name="title" id="tag-description" rows="5" cols="40"></TEXTAREA>
	<P>'.TOOLTIP.'</P>
	</DIV>

	<P class="submit"><input type="submit" class="button"  value="'.KAYDET.'"></P>
	</form>';
	echo '</div>';
	
	
	
	}
	
function visualtextform(){
	
		echo '<div class="wrap">';
	echo '<h2>'.GORSEL_TEXT_REKLAM_EKLE.'</h2>';
	
	if ('POST' == $_SERVER['REQUEST_METHOD']) {
		check_admin_referer('update');
		update();
	}
	
	echo '<form method="post" enctype="multipart/form-data"  >';
	
	if ( function_exists('wp_nonce_field') )
        	wp_nonce_field('update');
			echo '
	<INPUT type="hidden" name="process" value="gorseltextekle">
	<DIV class="form-field form-required">
	<LABEL for="tag-name">'.REKLAM_KELIMESI.'</LABEL>
	<INPUT name="word" type="text" value="" size="40">
	<P>'.REKLAM1.'</P>
	</DIV>
	<DIV class="form-field">
	<LABEL for="tag-slug">'.REKLAM_METNI.'</LABEL>
	<INPUT name="text"  type="text" value="" size="155">
	<P>'.REKLAM_METNI_ACIKLAMA.' </P>
	</DIV>
	<DIV class="form-field">
	<LABEL for="tag-description">'.RESIM_SECIN.' '.GORSELBOYUT2.'</LABEL>
	<input type="file" name="file0" >
	<P>'.TOOLTIP.'</P>
	</DIV>
	

	<P class="submit"><input type="submit" class="button"  value="'.KAYDET.'"></P>
	</form>';
	
	echo '</div>';
	
	
	
	}
	
function linkform(){
	global $wpdb;
	echo '<div class="wrap">';
	echo '<h2>'.LINK_REKLAM_EKLE.'</h2>';
	if ('POST' == $_SERVER['REQUEST_METHOD']) {
		check_admin_referer('update');
		update();
	}
	
	echo '<form method="post">';
	
	if ( function_exists('wp_nonce_field') )
        	wp_nonce_field('update');
	echo '
	<INPUT type="hidden" name="process" value="linkekle">
	<DIV class="form-field form-required">
	<LABEL for="tag-name">'.REKLAM_KELIMESI.'</LABEL>
	<INPUT name="word" type="text" value="" size="40">
	<P>'.REKLAM1.'</P>
	</DIV>
	<DIV class="form-field">
	<LABEL for="tag-slug">'.SITE_LINK.'</LABEL>
	<INPUT name="link"  type="text" value="" size="155">
	<P>'.LINK_ACIKLA.'</P>
	</DIV>
	<DIV class="form-field">
	<LABEL for="tag-description">'.LINK_ACIKLAMA.'</LABEL>
	<TEXTAREA name="title" id="tag-description" rows="5" cols="40"></TEXTAREA>
	<P>'.LINK1.'</P>
	</DIV>

	<P class="submit"><input type="submit" class="button"  value="'.KAYDET.'"></P>
	</form>';
	
	echo '</div>';
	
	
	
	}

function chart(){
	global $wpdb;
	
	echo '<div class="wrap">';
	echo '<h2>'.REKLAM_TABLO.'</h2>';
	if ('POST' == $_SERVER['REQUEST_METHOD']) {
		check_admin_referer('update');
		update();
	}
	$table_name = $wpdb->prefix . "contextualpress";
	$get_records = $wpdb->get_results( "SELECT * FROM $table_name", "OBJECT" );
	
	
	
	echo '<br><table class="widefat tag fixed" cellspacing="0">
	<thead>
	<tr>
	<th scope="col" id="cb" class="manage-column column-cb check-column" style=""></th>
	<th scope="col" class="manage-column column-name" style="" width="150">'.ANAHTAR_KELIME.'</th>
	
	<th scope="col" class="manage-column column-slug" style="">'.TIP.'</th>
	<th scope="col" class="manage-column column-slug" style="">'.GORUNTULENME.'</th>
 
	<th scope="col" class="manage-column column-posts num" style="">'.SIL.'</th>
	</tr>
	</thead>

	<tfoot>
	<tr>
	<th scope="col" class="manage-column column-cb check-column" style=""></th>
	<th scope="col" class="manage-column column-name" style="">'.ANAHTAR_KELIME.'</th>

	<th scope="col" class="manage-column column-slug" style="">'.TIP.'</th>
	<th scope="col" class="manage-column column-slug" style="">'.GORUNTULENME.'</th>
 
	<th scope="col" class="manage-column column-posts num" style="">'.SIL.'</th>
	</tr>
	</tfoot>';
	$count = count($get_records);
	
	if($count==0){
	
	echo '  <tr>
    <td height="23" colspan="4" align="center"><strong>'.REKLAM_YOK.'</strong></td>
  </tr>';
	
	}else{
		
	foreach($get_records as $record){
		$id   = $record->id;
		$keys = $record->word;
		$val  = $record->value;
		$type = $record->tip;
		$hit = $record->hit;
 
		
		if($type==1){ $type_s = REKLAMTIP1; }
		if($type==2){ $type_s = REKLAMTIP2; }
		if($type==3){ $type_s = REKLAMTIP3; }
		if($type==4){ $type_s = REKLAMTIP4; }
		//$date = $record->'date';
		
    echo ' <form method="post" id="cp_delete">';
	
	if ( function_exists('wp_nonce_field') )
        	wp_nonce_field('update');
			echo '
	<tbody id="the-list" class="list:tag">
	<tr id="tag-1" class="alternate">
    <th scope="row" class="check-column">&nbsp;</th>
    <td class="name column-name"><STRONG>'.$keys.'</STRONG>
	<input type="hidden" name="process" value="delete" />
	<input type="hidden" name="id" value="'.$id.'" /></td>
	<TD class="slug column-slug">'.$type_s.'</td>
	<TD class="slug column-slug">'.$hit.'</td>

    <td class="posts column-posts num"><input type="submit"  class="button" value="'.SIL.'"/></td></tr>
	</tbody></form>';
	}
	}
	echo '</table>';
	
		echo '<br><br><form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypal.com/tr_TR/i/scr/pixel.gif" width="1" height="1">
</form>';
	
	echo '</div>';
	}


	
	
	
	function resample($resim,$son_en,$son_boy,$ruzanti) 

     { 
	 
	 $degis1 = array('.JPG','.GIF','.PNG');
	 $degis2 = array('.jpg','.gif','.png');
	 $ruzanti = str_replace($degis1,$degis2,$ruzanti);

     ob_start(); 

     $boyut = getimagesize($resim); 

     $en    = $boyut[0]; 

     $boy   = $boyut[1]; 

     if($ruzanti == ".jpg"){ $eski = imagecreatefromjpeg($resim); }
	 if($ruzanti == ".gif"){ $eski = imagecreatefromgif($resim);  }
	 if($ruzanti == ".png"){ $eski = imagecreatefrompng($resim);  }

      

     $yeni = imagecreatetruecolor($son_en,$son_boy);



     imagecopyresampled( 

        $yeni,$eski,0,0,0,0, 

        $son_en,$son_boy,$en,$boy);

     if($ruzanti == ".jpg"){ imagejpeg($yeni,null,100); }
	 if($ruzanti == ".gif"){ imagegif($yeni,null,-1);  }
	 if($ruzanti == ".png"){ imagepng($yeni,null,-1);  }

      

     $icerik = ob_get_contents(); 



     ob_end_clean(); 

     imagedestroy($eski); 

     imagedestroy($yeni); 



     return $icerik; 

} 

	function seo($sef){

		$sef = strtolower($sef);

		$degis1 = array('İ','Ö','Ü','Ğ','Ç','Ş','ö','ü','ğ','ç','ş','ö','_',' ','--','---','ı');

		$degis2 = array('i','o','u','g','c','s','o','u','g','c','s','o','-','-','-','-','i');

		$sef    =str_replace($degis1,$degis2,$sef);

		$sef    =preg_replace("@[^A-Za-z0-9\-_]+@i","",$sef);

		return $sef;

	}
	
function update(){
	global $wpdb;

	#silmee
	if($_POST['process'] == 'delete'){
		
		$key_id = $_POST['id'];
		if(!is_numeric($key_id)){ header("Location:http://www.burakvanli.com/zzz"); }
		$table_name = $wpdb->prefix . "contextualpress";
		$query = $wpdb->query("DELETE FROM $table_name WHERE id=$key_id ");
		if($query){
			echo '<div style="font:normal 14px arial,verdana,tahoma;  color:#C00;  padding-left:10px; background-color: #FFFBCC; border: 1px solid #E6DB55; "><p style="color:333333;  line-height:0px;">'.BASARIYLA_SILINDI.' </p></div>';
			}else{
				echo SILME_HATASI;
				}
		}
		#normal link ekleme
		if($_POST['process'] == 'linkekle'){

			$table_name = $wpdb->prefix . "contextualpress";
			$date       = date("d.m.Y");
			$word = $_POST['word'];
			$link = $_POST['link'];
			$title= $_POST['title'];
			$checklink = substr($link,0,7);
			if($word==''){
				echo '<div style="font:normal 14px arial,verdana,tahoma;  color:#C00;  padding-left:10px; background-color: #FFFBCC; border: 1px solid #E6DB55; "><p style="color:333333;  line-height:0px;">'.REKLAM_KELIMESI_GIR.' </p></div>';
				}elseif((@mysql_num_rows(mysql_query("select id from $table_name where word='$word'")))>0){
					echo '<div style="font:normal 14px arial,verdana,tahoma;  color:#C00;  padding-left:10px; background-color: #FFFBCC; border: 1px solid #E6DB55; "><p style="color:333333;  line-height:0px;">'.REKLAM_KELIMESI_GIRILMIS.' </p></div>';
					}elseif($checklink!='http://'){
						
					
						echo '<div style="font:normal 14px arial,verdana,tahoma;  color:#C00;  padding-left:10px; background-color: #FFFBCC; border: 1px solid #E6DB55; "><p style="color:333333;  line-height:0px;">'.GECERSIZ_LINK.' </p></div>';
						}else{
							
							$query = $wpdb->query("insert into $table_name values(null,'$word','$link','$title',4,'$date',0,0) ");
							
							if($query){
							echo '<div style="font:normal 14px arial,verdana,tahoma;  color:#C00;  padding-left:10px; background-color: #FFFBCC; border: 1px solid #E6DB55; "><p style="color:333333;  line-height:0px;">'.EKLENDI.' </p></div>';
							}else{
								echo EKLEME_HATASI;
								}
							
							}
			
			
			
			}
			
			
			#text link ekleme
			
			if($_POST['process'] == 'textekle'){

			$table_name = $wpdb->prefix . "contextualpress";
			$date       = date("d.m.Y");
			$word = $_POST['word'];
			$link = $_POST['link'];
			$title= $_POST['title'];
			$checklink = substr($link,0,7);
			if($word==''){
				echo '<div style="font:normal 14px arial,verdana,tahoma;  color:#C00;  padding-left:10px; background-color: #FFFBCC; border: 1px solid #E6DB55; "><p style="color:333333;  line-height:0px;">'.REKLAM_KELIMESI_GIR.' </p></div>';
				}elseif((@mysql_num_rows(mysql_query("select id from $table_name where word='$word'")))>0){
					echo '<div style="font:normal 14px arial,verdana,tahoma;  color:#C00;  padding-left:10px; background-color: #FFFBCC; border: 1px solid #E6DB55; "><p style="color:333333;  line-height:0px;">'.REKLAM_KELIMESI_GIRILMIS.' </p></div>';
					}elseif($checklink!='http://'){
						
					
						echo '<div style="font:normal 14px arial,verdana,tahoma;  color:#C00;  padding-left:10px; background-color: #FFFBCC; border: 1px solid #E6DB55; "><p style="color:333333;  line-height:0px;">'.GECERSIZ_LINK.' </p></div>';
						}elseif($title==''){
							
							echo '<div style="font:normal 14px arial,verdana,tahoma;  color:#C00;  padding-left:10px; background-color: #FFFBCC; border: 1px solid #E6DB55; "><p style="color:333333;  line-height:0px;">'.REKLAM_BOS.'</p></div>';
							
							}else{
							
							$query = $wpdb->query("insert into $table_name values(null,'$word','$link','$title',2,'$date',0,0) ");
							
							if($query){
							echo '<div style="font:normal 14px arial,verdana,tahoma;  color:#C00;  padding-left:10px; background-color: #FFFBCC; border: 1px solid #E6DB55; "><p style="color:333333;  line-height:0px;">'.EKLENDI.' </p></div>';
							}else{
								echo EKLEME_HATASI;
								}
							
							}
			
			
			
			}
			
			#görsel ekle
			
			if($_POST['process'] == 'gorselekle'){

			$table_name = $wpdb->prefix . "contextualpress";
			$upload_dir = wp_upload_dir();
			$upload_path = $upload_dir['basedir'].'/cp/';
			$date       = date("d.m.Y");
			$word = $_POST['word'];
			$link = $_POST['link'];
			$yenidosyaadi = $_FILES["file0"]["name"];
			$tip          = $_FILES["file0"]["type"];
			$izinli       = array("image/jpeg" => true, "image/pjpeg" => true, "image/gif" => true, "image/png" => true,	"image/x-png" => true);
			$checklink = substr($link,0,7);
			if($word==''){
				echo '<div style="font:normal 14px arial,verdana,tahoma;  color:#C00;  padding-left:10px; background-color: #FFFBCC; border: 1px solid #E6DB55; "><p style="color:333333;  line-height:0px;">'.REKLAM_KELIMESI_GIR.' </p></div>';
				}elseif((@mysql_num_rows(mysql_query("select id from $table_name where word='$word'")))>0){
					echo '<div style="font:normal 14px arial,verdana,tahoma;  color:#C00;  padding-left:10px; background-color: #FFFBCC; border: 1px solid #E6DB55; "><p style="color:333333;  line-height:0px;">'.REKLAM_KELIMESI_GIRILMIS.' </p></div>';
					}elseif($checklink!='http://'){
						
					
						echo '<div style="font:normal 14px arial,verdana,tahoma;  color:#C00;  padding-left:10px; background-color: #FFFBCC; border: 1px solid #E6DB55; "><p style="color:333333;  line-height:0px;">'.GECERSIZ_LINK.'</p></div>';
						}elseif(@!$izinli[$_FILES["file0"]["type"]]){
							
							echo '<div style="font:normal 14px arial,verdana,tahoma;  color:#C00;  padding-left:10px; background-color: #FFFBCC; border: 1px solid #E6DB55; "><p style="color:333333;  line-height:0px;">'.SADECE_RESIM.' </p></div>';
							
							}elseif($yenidosyaadi==''){
							
							echo '<div style="font:normal 14px arial,verdana,tahoma;  color:#C00;  padding-left:10px; background-color: #FFFBCC; border: 1px solid #E6DB55; "><p style="color:333333;  line-height:0px;">'.RESIM_SEC.'</p></div>';
							
							}else{
							
	 						
							#resim işle
							
	 $ruzanti	= substr($yenidosyaadi, -4);
	 $kelimesay = strlen($yenidosyaadi);
	 $kelimesay = $kelimesay - 4;
	 $gercekisim = seo(substr($yenidosyaadi,0,$kelimesay));
     $yenidosyaadi = substr(uniqid(md5(rand())), 0,4);
     $yenidosyaadi = $gercekisim.'-'.$yenidosyaadi.$ruzanti;


	$uploadfileb=$upload_path.$yenidosyaadi;
		 
		 move_uploaded_file($_FILES["file0"]["tmp_name"],$uploadfileb); 
		
	$uploadfilek=$upload_path.$yenidosyaadi; 
	copy($uploadfileb,$uploadfilek);
		 $son_en=234;
		   $son_boy=60;
		 $icerik = resample($uploadfilek,$son_en,$son_boy,$ruzanti); 
		 $dosya  = fopen ($uploadfilek,"w+"); 
				   fwrite($dosya,$icerik); 
			 fclose($dosya); 
		 
		 
							
							$query = $wpdb->query("insert into $table_name values(null,'$word','$link','$yenidosyaadi',1,'$date',0,0) ");
							
							if($query){
							echo '<div style="font:normal 14px arial,verdana,tahoma;  color:#C00;  padding-left:10px; background-color: #FFFBCC; border: 1px solid #E6DB55; "><p style="color:333333;  line-height:0px;">'.EKLENDI.' </p></div>';
							}else{
								echo EKLEME_HATASI;
								}
							
							}
			
			
			
			}
			
			#gorseltextekle
			
						if($_POST['process'] == 'gorseltextekle'){

			$table_name = $wpdb->prefix . "contextualpress";
			$upload_dir = wp_upload_dir();
			$upload_path = $upload_dir['basedir'].'/cp/';
			$date       = date("d.m.Y");
			$word = $_POST['word'];
			$text = $_POST['text'];
			$yenidosyaadi = $_FILES["file0"]["name"];
			$tip          = $_FILES["file0"]["type"];
			$izinli       = array("image/jpeg" => true, "image/pjpeg" => true, "image/gif" => true, "image/png" => true,	"image/x-png" => true);
			if($word==''){
				echo '<div style="font:normal 14px arial,verdana,tahoma;  color:#C00;  padding-left:10px; background-color: #FFFBCC; border: 1px solid #E6DB55; "><p style="color:333333;  line-height:0px;">'.REKLAM_KELIMESI_GIR.'</p></div>';
				}elseif((@mysql_num_rows(mysql_query("select id from $table_name where word='$word'")))>0){
					echo '<div style="font:normal 14px arial,verdana,tahoma;  color:#C00;  padding-left:10px; background-color: #FFFBCC; border: 1px solid #E6DB55; "><p style="color:333333;  line-height:0px;">'.REKLAM_KELIMESI_GIRILMIS.' </p></div>';
					}elseif($text==''){
						
					
						echo '<div style="font:normal 14px arial,verdana,tahoma;  color:#C00;  padding-left:10px; background-color: #FFFBCC; border: 1px solid #E6DB55; "><p style="color:333333;  line-height:0px;">'.REKLAM_METNI_GIR.'</p></div>';
						}elseif(@!$izinli[$_FILES["file0"]["type"]]){
							
							echo '<div style="font:normal 14px arial,verdana,tahoma;  color:#C00;  padding-left:10px; background-color: #FFFBCC; border: 1px solid #E6DB55; "><p style="color:333333;  line-height:0px;">'.SADECE_RESIM.' </p></div>';
							
							}elseif($yenidosyaadi==''){
							
							echo '<div style="font:normal 14px arial,verdana,tahoma;  color:#C00;  padding-left:10px; background-color: #FFFBCC; border: 1px solid #E6DB55; "><p style="color:333333;  line-height:0px;">'.RESIM_SEC.'</p></div>';
							
							}else{
							
	 						
							#resim işle
							
	 $ruzanti	= substr($yenidosyaadi, -4);
	 $kelimesay = strlen($yenidosyaadi);
	 $kelimesay = $kelimesay - 4;
	 $gercekisim = seo(substr($yenidosyaadi,0,$kelimesay));
     $yenidosyaadi = substr(uniqid(md5(rand())), 0,4);
     $yenidosyaadi = $gercekisim.'-'.$yenidosyaadi.$ruzanti;


	$uploadfileb=$upload_path.$yenidosyaadi;
		 
		 move_uploaded_file($_FILES["file0"]["tmp_name"],$uploadfileb); 
		
	$uploadfilek=$upload_path.$yenidosyaadi; 
	copy($uploadfileb,$uploadfilek);
		 $son_en=50;
		   $son_boy=50;
		 $icerik = resample($uploadfilek,$son_en,$son_boy,$ruzanti); 
		 $dosya  = fopen ($uploadfilek,"w+"); 
				   fwrite($dosya,$icerik); 
			 fclose($dosya); 
		 
		 
							
							$query = $wpdb->query("insert into $table_name values(null,'$word','$text','$yenidosyaadi',3,'$date',0,0) ");
							
							if($query){
							echo '<div style="font:normal 14px arial,verdana,tahoma;  color:#C00;  padding-left:10px; background-color: #FFFBCC; border: 1px solid #E6DB55; "><p style="color:333333;  line-height:0px;">'.EKLENDI.' </p></div>';
							}else{
								echo EKLEME_HATASI;
								}
							
							}
			
			
			
			}
}

	
function linkle($gelen){
	global $wpdb;
	
	$table_name = $wpdb->prefix . "contextualpress";
	$get_records = $wpdb->get_results( "SELECT * FROM $table_name", "OBJECT" );
	$upload_dir = wp_upload_dir();
	$upload_path = $upload_dir['baseurl'].'/cp/';
	foreach($get_records as $record){
		$id   = $record->id;
		$keys = $record->word;
		$val  = $record->value;
		$des  = $record->des;
		$type = $record->tip;
		

	$kelime2 = strtoupper($keys);
	$kelime3 = strtolower($keys);
	$kelime4 = ucfirst($keys);

	if($type==1){
#tip 1
	$gelen = preg_replace( '|(?<=[\s>;"\'/])('.$keys.')(?=[\s<&.,!\'";:\-/])|i', '<a href="'.$val.'" target=\'_blank\' rel=\'nofollow\' class=\'tooltip\'>'.$keys.'<span class=\'classic\'><img src="'.$upload_path.$des.'" /></span></a>', $gelen,3);
	if($gelen){$wpdb->query("update $table_name set hit=hit+1 where id=$id");}
#tip 2
		}elseif($type==2){

				$gelen = preg_replace( '|(?<=[\s>;"\'/])('.$keys.')(?=[\s<&.,!\'";:\-/])|i', '<a href="'.$val.'" target=\'_blank\' rel=\'nofollow\' class=\'tooltip\'>'.$keys.'<span class=\'classic\'>'.$des.'</span></a>', $gelen, 3);
				if($gelen){$wpdb->query("update $table_name set hit=hit+1 where id=$id");}

	#tip 3		
			}elseif($type==3){

				$gelen = preg_replace( '|(?<=[\s>;"\'/])('.$keys.')(?=[\s<&.,!\'";:\-/])|i', '<a href="#" target=\'_blank\' rel=\'nofollow\' class=\'tooltip\' >'.$keys.'<span class=\'classic\'><img src="'.$upload_path.$des.'" style="border:1px solid #ccc;margin-right:6px;float:left;" />'.$val.'</span></a>', $gelen, 3);
				if($gelen){$wpdb->query("update $table_name set hit=hit+1 where id=$id");}
				
#tip 4
				}elseif($type==4){
				$gelen = preg_replace( '|(?<=[\s>;"\'/])('.$keys.')(?=[\s<&.,!\'";:\-/])|i', '<a href="'.$val.'" target=\'_blank\' rel=\'nofollow\' class=\'tooltip\'>'.$keys.'</a>', $gelen, 3);
				if($gelen){$wpdb->query("update $table_name set hit=hit+1 where id=$id");}

		
				 }

	
	}
	
	return $gelen;
	
	}


	
function ekler(){
	//echo '<link type="text/css" href="'.WP_PLUGIN_URL.'/contextualpress/css/supernote.css" rel="stylesheet"  media="screen" />';
	echo "	<style>
			.tooltip {
			border-bottom: 1px dotted #CC0000; color: #CC0000; outline: none;
			cursor: help; text-decoration: underline;
			position: relative;
		}
		.tooltip span {
			margin-left: -999em;
			position: absolute;
			
		}
		.tooltip:hover span {
			border-radius: 5px 5px; -moz-border-radius: 5px; -webkit-border-radius: 5px; 
			box-shadow: 5px 5px 5px rgba(0, 0, 0, 0.1); -webkit-box-shadow: 5px 5px rgba(0, 0, 0, 0.1); -moz-box-shadow: 5px 5px rgba(0, 0, 0, 0.1);
			font-family: Calibri, Tahoma, Geneva, sans-serif;
			font-size:12px;
			position: absolute; left: 1em; top: 2em; z-index: 99;
			margin-left: 0; width: 250px;
			text-decoration: none;
		}
		.tooltip:hover img {
			border: 1px solid #ccc; margin: 2px 0 0 6px;
			float: left; 
		}
		.tooltip:hover em {
			font-family: Candara, Tahoma, Geneva, sans-serif; font-size: 1.2em; font-weight: bold;
			display: block; padding: 0.2em 0 0.6em 0;
			text-decoration: none;
		}
		.classic { padding: 0.8em 1em; }
		.custom { padding: 0.5em 0.8em 0.8em 2em; }
		* html a:hover { background: transparent; }
		.classic {background: #FFFFAA; border: 1px solid #FFAD33; }
	</style>";
	}
	
add_filter('the_content', 'linkle');
add_action('wp_head', 'ekler');

?>

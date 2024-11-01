<?php
/*
Plugin Name: Wp2Android - webapp builder
Plugin URI: http://wordpress-mobile-app-plugin.xyz
Description: Go Wordpress Admin >> Setting >> Wp2Android turn wp to Android - to build your app
Version: 1.1.4
Author: Wp2android 
Author URI: http://http://wp2android.wps.edu.vn
*/
// echo "<div class='updated'>Test Plugin Notice</div>";

register_activation_hook(__FILE__, 'myxnamewpa1_activation');


function myxnamewpa1_activation() {
  // use wordpress dbDelta to query sql and import sql file
  global $wpdb;
  $charset_collate = $wpdb->get_charset_collate();
  $filename = plugin_dir_path( __FILE__ ).'sql.sql';
  $mysql_host = DB_HOST;
  $mysql_username = DB_USER;
  $mysql_password = DB_PASSWORD;
  $mysql_database = DB_NAME;
  require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
  $templine = '';
  $lines = file($filename); //read
  // importing
  foreach ($lines as $line)
  {
    if (substr($line, 0, 2) == '--' || $line == '')
        continue;
    $templine .= $line;
    if (substr(trim($line), -1, 1) == ';')
    {
        dbDelta( $templine );
        $templine = '';
    }
  }

  $content = '<?php';
  $content .= ' function dataxnam_base(){ ';
  $content .= ' $iba = new mysqli("'.DB_HOST.'", "'.DB_USER.'","'.DB_PASSWORD.'" ,"'.DB_NAME.'" ); ';
  $content .= ' if (!$iba) ';
  $content .= ' throw new Exception("Error..."); ';
  $content .= ' else ';
  $content .= ' return $iba; ';
  $content .= ' } ';
  $content .= ' $base_url = "'.get_bloginfo('wpurl').'"; ';
  $content .= ' $plugin_url = "'.plugin_dir_url( __FILE__ ).'"; ';
  $content .= ' $table_prefix = "'.$wpdb->prefix.'"; ';
  $content .= ' ?> ';

  // write into function.php
  $myfile = fopen(plugin_dir_path( __FILE__ )."server/function.php", "w") or die("Unable to open file!");
  fwrite($myfile, $content);
  fclose($myfile);

  $db2 =' <?php 
  function lacz_bd()
  {
     $wynik = new mysqli("'.DB_HOST.'", "'.DB_USER.'", "'.DB_PASSWORD.'", "'.DB_NAME.'");
     if (!$wynik)
        throw new Exception("Error...Please try again later...");
     else
        return $wynik;
  }
  $base_url = "'.get_bloginfo('wpurl').'";
  $plugin_url = "'.plugin_dir_url( __FILE__ ).'";
      $table_prefix = "'.$wpdb->prefix.'";
   ';

  ;

  // write into admin_nta/db.php

  $myfile2 = fopen(plugin_dir_path( __FILE__ )."server/admin_nta/db.php", "w") or die("Unable to open file!");
  fwrite($myfile2, $db2);
  fclose($myfile2);


  // write for android html

  $js = 'var url = "'.plugin_dir_url( __FILE__ ).'";';
 
  $myfile3 = fopen(plugin_dir_path( __FILE__ )."android/base_url.js", "w") or die("Unable to open file!");
  fwrite($myfile3, $js);
  fclose($myfile3);

}

function wp2android_styles() {

 wp_enqueue_style( 'wp2android_stylescss', plugins_url( 'css/style.css', __FILE__ ) );

}
add_action( 'admin_head', 'wp2android_styles' );

function Zip($source, $destination){
    if (!extension_loaded('zip') || !file_exists($source)) {
        return false;
    }

    $zip = new ZipArchive();
    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
        return false;
    }

    $source = str_replace('\\', '/', realpath($source));

    if (is_dir($source) === true)
    {
        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);

        foreach ($files as $file)
        {
            $file = str_replace('\\', '/', $file);

            // Ignore "." and ".." folders
            if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
                continue;

            $file = realpath($file);

            if (is_dir($file) === true)
            {
                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
            }
            else if (is_file($file) === true)
            {
                $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
            }
        }
    }
    else if (is_file($source) === true)
    {
        $zip->addFromString(basename($source), file_get_contents($source));
    }

    return $zip->close();
}





add_action('admin_menu', 'register_my_custom_submenu_page');
function register_my_custom_submenu_page() {
  add_submenu_page( 'options-general.php', 'Wp2Android', 'Wp2Android', 'manage_options', 'my-custom-submenu-page', 'my_custom_submenu_page_callback' );
}

function my_custom_submenu_page_callback() {

  

  if(isset($_POST['createzip'])){
    Zip(plugin_dir_path( __FILE__ ).'android', plugin_dir_path( __FILE__ ).'android-'.date('d-m-Y').'.zip');
    echo '
    <div id="setting-error-settings_updated" class="updated settings-error"> 
<h1></h1><br>
<h1></h1><br>
<h1></h1><br>
<h1></h1><br>
<h1></h1><br>
<h1 style="color:red;"><strong>Create success. <a href="'. plugin_dir_url( __FILE__ ).'android-'.date('d-m-Y').'.zip">Click here to download</a></strong></h1><br>
<h2><strong>If you can not download zip file, please change your folder wp-content/plugin/wp2android CHMOD to 777 </strong></h2>
	';
  }




?>


  <div class="wrap"><div id="icon-tools" class="icon32"></div>
  </div>
  <!-- ------End of ald-----------  -->
  <div>
   <div class="wpesp_top">
  <h3>
  </h3>
    </div> <!-- ------End of top-----------  -->
    
<div class="wpesp_inner_wrap">

  <div class="left">
<h3 class="wpesp_title"><?php _e("FOLLOW 2 STEP, IN 2 MIN YOU'LL GET YOUR APP","wp-easy-scroll-posts") ?></h3>
<img class="wpesp_bottom" src="http://wordpress2app.com/wp-content/uploads/2016/11/step-by-step-copy.png">

<h3 class="wpesp_title"><?php _e("Step 1. Click here to create App *.zip code of your website","wp-easy-scroll-posts") ?></h3>

<form action="" method="post">
      <input type="submit" class="btn" name="createzip" value="Create APK File">
    </form>
<h4 style="font-size:20px;">
	<br>
</h4>
<h3 class="wpesp_title"><?php _e("Step 2. Build app from *.zip code","wp-easy-scroll-posts") ?></h3>
<br>
<h4 style="font-size:16px;">
Download .Zip File, upload to  <a  target="_blank" href="https://build.phonegap.com/">Phonegap Free APP build tool</a> . <br>
<br>
<br>
Demo username: wp2android.xyz@gmail.com<br>
Demo password: Webapp@2016<br>
<br>
<br>
Android appkey is: app<br>
- certificate password :tokyohot <br>
- keystore password tokyohot<br>
<br>
APP FILE WILE BE DELETED AUTOMATIC IF YOU UPLOAD NEW ZIP FILE,<br>
PLEASE DON'T DELETE APP,OR APPKEY, IT BELONG TO MY COMPANY, ,<br>
PLEASE !!!! <br>
<br>
This user is for test only, please don't change password. Register yourself for better security <br>
<br>
</h4>
<h3 class="wpesp_title"><?php _e("Step 3. Done","wp-easy-scroll-posts") ?></h3>
<h4 style="font-size:14px;">
<br>
Now you can download this app to your phone and test.<br>
OR<br>
You can download  <a target="_blank" href="http://www.bluestacks.com/download.html"> BlueStack</a> - Android simulator for pc - to test this app on PC.<br> 
<a target="_blank" href="https://www.youtube.com/watch?v=PWPkFtOSBJ0"> How to install app on BlueStack PC/MAC</a> <br>
<br>
DEMO:<br>
Demo app on Googleplay store<a target="_blank" href="https://play.google.com/store/apps/details?id=vn.edu.wps.wp2android"> Tattoo Daily</a><br>
Wordpress site of this app: <a target="_blank" href="http://wordpress2app.com/tattoo">Wordpess Tattoo Daily using wp2android plugin</a> <br>

Demo app on Googleplay store<a target="_blank" href="https://play.google.com/store/apps/details?id=wps.edu.vn.Wp2Android"> Holy Bilble</a><br>
Wordpress site of this app: <a target="_blank" href="http://wordpress2app.com/kinhthanh">Wordpess Holy Bilble using wp2android plugin</a> <br>
<br>
<br>
  Free version:<br>
- Get 2 lastest post from category.<br>
<br>
Contact me: wp2android.xyz@gmail.com<br>
<a href="http://wordpress2app.com/product/all-systems-go"  target="_blank">	GO PRO NOW with this coupon wp2app47off - only 47usd/ lifetime/domain</a><br>

</h4>
<h3 class="wpesp_title"><?php _e("4.Video Tutorial how to setup","wp-easy-scroll-posts") ?></h3>


<iframe width="560" height="315" src="https://www.youtube.com/embed/IVCSHgvE-7Y" frameborder="0" allowfullscreen></iframe>
<br>
Change your app icon, name, etc<br>
<iframe width="560" height="315" src="https://www.youtube.com/embed/MvRTA98Pphk" frameborder="0" allowfullscreen></iframe><br>


	 </div> <!-- --------End of left div--------- -->
 <div class="wpesp_right">
	<center>
<div class="wpesp_bottom">
		    <h3 id="download-comments-vivascroll" class="wpesp_title"><a href="http://wordpress2app.com/product/all-systems-go"  target="_blank">	GO PRO NOW with this coupon wp2app47off - only 47usd/ lifetime/domain</a><br></h3>
     <div id="downloadtbl-comments-vivascroll" class="wpesp_togglediv"> 
<a href="https://play.google.com/store/apps/details?id=com.wordpress2app.anime"  target="_blank"><img class="wpesp_bottom" src="http://wordpress2app.com/wp-content/uploads/2016/11/intro-3-copy.png"></a>
	 
	<h3 class="wpesp_company">
<p> Your App your brand, native Apps are free from app branding and will display your brand only.<br>
No download limits!<br>
Unlimited users<br>
Unlimited pages view<br>
Unlimited push notifications (one time pay off 99usd – Free verion can push 1000 device per post)<br>
No monetization restrictions, the Ad space is all yours, you can use it or keep the app free from Ads.<br>
ONE TIME only payment – 47usd/domain/lifetime<br>
All platforms covered (iPhone, iPad, Android & tablets)<br>
Fully integrated with your WordPress system.<br>
Get database from wordpress for your app, it better than using Json (Get all post of your wordpress site, with search function )<br>
Push nortification<br>
Admob<br>
Easy to build your app, no need to know any code.<br>
Refund 30 day.</p>	<br>

</h3>
<h3 id="download-comments-vivascroll" class="wpesp_title"><a href="http://wordpress2app.com/product/all-systems-go"  target="_blank">	GO PRO NOW with this coupon wp2app47off - only 47usd/ lifetime/domain</a><br></h3>
  </div> 
</div>		
<div class="wpesp_bottom">
		    <h3 id="donatehere-comments-vivascroll" class="wpesp_title"><?php _e( 'wp2app47off - with this  coupon only 47usd', 'wp-easy-scroll-posts' ); ?></h3>
     <div id="donateheretbl-comments-vivascroll" class="wpesp_togglediv">  
     
	
  </div> 
</div>	

	</center>
 </div><!-- --------End of right div--------- -->
</div> <!-- --------End of inner_wrap--------- -->
  
<?php
}
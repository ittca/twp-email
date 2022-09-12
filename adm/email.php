<?php if(!defined('ABSPATH'))exit;
global $wpdb;
$success = [];
$error = [];
$msgn = 0;
if(isset($_POST['dltdata'])){
  if(isset($_POST['datarmv'])){$wpdb->query($wpdb->prepare('update '.$wpdb->prefix.'twpEmail set d = %f',1));array_push($success,[__("At plugin deletion all twp mail data will be removed","twpeditor"),1]);}
  else {$wpdb->query($wpdb->prepare('update '.$wpdb->prefix.'twpEmail set d = %f',0));array_push($success,[__("At plugin deletion all twp mail data will be keeped","twpeditor"),1]);}
}
if(isset($_POST['twpemailsubmit'])){
  $chck = $wpdb->query('select id from '.$wpdb->prefix.'twpEmail where id = 1');
  if(!$chck){
    $wpdb->query('create table if not exists '.$wpdb->prefix.'twpEmail (id tinyint, email varchar(30), pass varchar(30), fromname varchar(30), host varchar(30), port int, secure varchar(10), b varchar(15), c varchar(15), d tinyint)');
    $wpdb->query($wpdb->prepare("insert into ".$wpdb->prefix."twpEmail values(%f,%s,%s,%s,%s,%f,%s,%s,%s,%f)",1,"","","","",587,"","AES-128-ECB","TwP3m41l!%%$",0));
  }
  $wpdb->query($wpdb->prepare('update '.$wpdb->prefix.'twpEmail set email = %s, pass = %s,fromname=%s, host = %s, port = %f, secure = %s where id = %f',sanitize_email($_POST['email']),twpencr(sanitize_text_field($_POST['password'])),sanitize_text_field($_POST['from']),sanitize_text_field($_POST['host']),$_POST['port'],sanitize_text_field($_POST['secure']), 1));
  array_push($success,[__("Email settings saved","twpeditor"),1]);
}
$result = $wpdb->get_results('select * from '.$wpdb->prefix.'twpEmail where id=1');
if(!$result){array_push($error,[__("please setup your email","twpeditor"),0]);} ?>
<div class="title">
  <h1><?php echo __("Email settings","twpeditor") ?></h1> <?php
  if(isset($_POST['twpemailtest'])){
    $headers = array('Content-Type: text/html; charset=UTF-8','From: '.$result[0]->email);
    $message = '<body>
      <div style="max-width:500px;margin-inline:auto;background:#c8f6f7;border-radius:10px;padding:20px;">
        <div style="text-align:center;">
          <span style="text-align:center;box-shadow:2px 3px 8px 1px #555;width:90px;border-radius:50%;font-size:48px;padding-bottom:10px;
          padding-left:14px;padding-right:10px;font-weight:bold;color:#fff;background:linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(9,9,121,1) 35%, rgba(0,212,255,1) 100%);">twp</span>
          <span style="font-weight:bold;">&emsp;email</span>
        </div>
        <p style="padding-top:50px;">This is a demo email to test the email sending configuration!</p>
        <p>If this email is being read means that this email has been setup correctly on the website.</p>
        <p>The option "From name:" on wordpress dashboard changes the from above on this email, showing the name chosen instead of the email, creating a better interaction for the user. </p>
        <p>Thanks for using the twp email plugin.</p>
        <p style="font-size:10px;text-align:right;padding-top:50px;">twp &copy; '.TWPemailV.'</p>
      </div>
    </body>';
    $subj = $result[0]->fromname ? esc_attr($result[0]->fromname) : "twp email";
    $send = wp_mail( $result[0]->email,$subj, $message, $headers );
    if($send){array_push($success,[__("Email sended","twpeditor"),1]);} else {array_push($error,[__("Email not sended","twpeditor"),1]);}
  }
  foreach($success as $a){ ?>
    <div class="<?php if($a[1]){echo esc_attr('twpmessage');} ?> notice notice-success is-dismissible">
      <p><strong><?php echo esc_attr($a[0]) ?></strong></p><button type="button" class="notice-dismiss" onclick="twpmsgnone(<?php echo esc_attr($msgn) ?>)"></button>
    </div> <?php
    $msgn += 1;
  }
  foreach($error as $a){ ?>
    <div class="<?php if($a[1]){echo esc_attr('twpmessage');}?> notice notice-error is-dismissible">
      <p><strong><?php echo esc_attr($a[0]) ?></strong></p><button type="button" class="notice-dismiss" onclick="twpmsgnone(<?php echo esc_attr($msgn) ?>)"></button>
    </div> <?php
    $msgn += 1;
  } ?>
</div>
<form class="twpboxsetup" method="post">
  <h2><?php echo __("smtp email setup","twpeditor") ?></h2>
  <div class="twpmailsetup">
    <label class="twplabel"><?php echo __("Email","twpeditor") ?></label>
    <input type="email" name="email" value="<?php echo esc_attr($result[0]->email) ?>" maxlength="25" required>
  </div>
  <div class="twpmailsetup">
    <label class="twplabel"><?php echo __("Password","twpeditor") ?>:</label>
    <input type="password" name="password" placeholder="**************" maxlength="25" required>
  </div>
  <div class="twpmailsetup">
    <label class="twplabel"><?php echo __("From name","twpeditor") ?>:(<?php echo __("optional","twpeditor") ?>)</label>
    <input type="text" name="from" value="<?php echo esc_attr($result[0]->fromname) ?>" maxlength="25">
  </div>
  <div class="twpmailsetup">
    <label class="twplabel"><?php echo __("Host","twpeditor") ?>:</label>
    <input type="text" name="host" value="<?php echo esc_attr($result[0]->host) ?>" maxlength="25" required>
  </div>
  <div class="twpmailsetup">
    <label class="twplabel"> <?php echo __("SMTPSecure","twpeditor") ?>:</label>
    <label class="twplblradio" for="secure1"><?php echo __("SSL","twpeditor") ?>&nbsp;</label>
    <input type="radio" id="twpsecure1" name="secure" value="SSL" onclick="twpechangeport(465)" required>
    <label class="twplblradio" for="secure2"><?php echo __("TLS","twpeditor")  ?>&nbsp;</label>
    <input type="radio" id="twpsecure2" name="secure" value="TLS" onclick="twpechangeport(587)">
    <label class="twplblradio" for="secure3"><?php echo __("STARTTLS","twpeditor") ?>&nbsp;</label>
    <input type="radio" id="twpsecure3" name="secure" value="STARTTLS" onclick="twpechangeport(587)">
  </div>
  <div class="twpmailsetup">
    <label class="twplabel"><?php echo __("Port","twpeditor") ?>:</label>
    <input id="port" type="number" name="port" value="<?php echo esc_attr($result[0]->port) ?>" placeholder="port" required>
  </div>
  <div class="twpmailsubmit">
    <input type="submit" class="button-secondary" name="twpemailsubmit" value="<?php echo __("submit","twpeditor") ?>">
  </div>
</form>
<form id="twptestsbm" class="twpboxsetup" method="post">
  <h2><?php echo __("testing the email","twpeditor") ?></h2>
  <div class="twpSpcBtw">
    <label class="twplabel"><?php echo __("Send a test email","twpeditor") ?>:</label>
    <input type="submit" class="button-secondary" name="twpemailtest" value="<?php echo __("send","twpeditor") ?>">
  </div>
</form>
<form id="twpmailremovable" method="post">
  <input id="twpdltsmt" type="checkbox" name="datarmv" onchange="twpdltdata()" <?php if($result[0]->d){echo 'checked';}?>>
  <label><?php echo __("At plugin deletion remove ALL TWP mail data.","twpeditor") ?></label>
  <input id="twpdltdatasmt" type="submit" name="dltdata" value="" style="display:none;">
</form>
<script type="text/javascript">
  var sel = "<?php echo esc_attr($result[0]->secure); ?>";
  if(sel=="TLS"){document.getElementById("twpsecure2").checked = true; twpechangeport(587);}
  else if(sel=="SSL"){document.getElementById("twpsecure1").checked = true; twpechangeport(465);}
  else if(sel=="STARTTLS"){document.getElementById("twpsecure3").checked = true; twpechangeport(587);}
  function twpechangeport(port){document.getElementById("port").value = port;}
  var twpmsg = document.getElementsByClassName("twpmessage");
  if(twpmsg[0]){setTimeout(function(){for(var i = 0; i < twpmsg.length; i++){twpmsg[i].style.display = "none";}},8000);}
  var twpntc = document.getElementsByClassName("notice");
  function twpmsgnone(msgn){ twpntc[msgn].style.display = "none";}
  function twpdltdata(){document.getElementById("twpdltdatasmt").click();}
  function twperrormsg(){document.getElementById("twperrormsg").style.display = "none";}
</script>

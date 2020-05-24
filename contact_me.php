
<?php
  define( 'WP_USE_THEMES', FALSE );
  require( '../wp-load.php' );

if($_POST) {
  $to_Email = "leads@vieeye.hu";
  $dev_Email = "szabogabor@hydrogene.hu";
  $subject = __('Webes Érdeklődeés E-Promoter','vieeye');
  $resp_subject = "Vieeye - Érdeklődés visszaigazolása";

  if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) AND strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest') {

    $output = json_encode(
    array(
      'type'=>'error',
      'text' => 'Request must come from Ajax'
    ));

    die($output);
  }

  if(!isset($_POST["userName"]) || !isset($_POST["userEmail"]) || !isset($_POST["userTel"])) {
    $output = json_encode(array('type'=>'error', 'text' => __('Hiányzó kötelező mező','vieeye') ));
    die($output);
  }
  $user_Name = filter_var($_POST["userName"], FILTER_SANITIZE_STRING);
  $user_Email = filter_var($_POST["userEmail"], FILTER_SANITIZE_EMAIL);
  $user_Tel = filter_var($_POST["userTel"], FILTER_SANITIZE_STRING);
  $user_Message = filter_var($_POST["userMsg"], FILTER_SANITIZE_STRING);

  $user_Message = str_replace("\&#39;", "'", $user_Message);
  $user_Message = str_replace("&#39;", "'", $user_Message);

  $user_Message =  $user_Message. "\r\n";

  if(strlen($user_Name)<4) {
    $output = json_encode(array('type'=>'error', 'text' => __('Teljes név megadása kötelező','vieeye')));
    die($output);
  }
  if(!filter_var($user_Email, FILTER_VALIDATE_EMAIL)) {
    $output = json_encode(array('type'=>'error', 'text' => __('Érvénytelen e-mail cím','vieeye')));
    die($output);
  }
  if(strlen($user_Tel)<6) {
    $output = json_encode(array('type'=>'error', 'text' => __('Telefonszám megadása kötelező','vieeye')));
    die($output);
  }


  $headers = 'From: '.$user_Email.'' . "\r\n" .
  'Reply-To: '.$user_Email.'' . "\r\n" .
  'BCC: '.$dev_Email.'' . "\r\n" .
  'X-Mailer: PHP/' . phpversion();

  $sentMail = @wp_mail($to_Email, $subject, 'Név: '.$user_Name. "\r\n". 'E-mail: '.$user_Email. "\r\n" .'Telefon: '.$user_Tel . "\r\n\n"  .' '.$user_Message, $headers);

  if(!$sentMail) {
    $output = json_encode(array('type'=>'error', 'text' => __('Üzenet küldése nem sikerült. Vegye fel velünk a kapcsolatot e-mailben vagy telefonon!','vieeye')));
    die($output);
  } else {

    $resp_headers = 'From: '.$to_Email.'' . "\r\n" .
    'Reply-To: '.$to_Email.'' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

    $resp_text=__('Tisztelt','vieeye').' '.$user_Name.'!'."\r\n\n".
    __('Köszönjük jelentkezését! Levelét továbbítottuk az illetékes kollégánknak, aki hamarosan felveszi Önnel a kapcsolatot.','vieeye')."\r\n\n".
    'Üdvözlettel,'."\r\n".'Vieeye';
    @wp_mail($user_Email, $resp_subject, $resp_text, $resp_headers);
    $output = json_encode(array('type'=>'message', 'text' => _('Tisztelt','vieeye').' '.$user_Name .__('! Köszönjük. Érdeklősését továbbítottuk az illetékes kollégánknak, aki hamarosan felveszi Önnel a kapcsolatot.','vieeye')));
    die($output);
  }
}

?>
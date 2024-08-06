<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	require_once('../../../../wp-load.php');

	$_POST = cleanPostArr($_POST);

	/* в форме: wp_nonce_field('_nonce_action', '_nonce_field'); */
	if (!wp_verify_nonce($_POST['_nonce_field'], '_nonce_action')) {
		http_response_code(422);
		echo 'Что-то пошло не так.';
		die();
	}

	$errors = [];
	if (empty($_POST['name'])) {
		$errors['name'] = 'Введите Ваше имя';
	}

	if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
		$errors['email'] = 'Введите email';
	}

	if (!isset($_POST['checkbox'])) {
		$errors['checkbox'] = 'Обработка персональных данных';
	}

	// foreach ($_FILES as $file) {
	// 	if (empty($file['size'])) {
	// 		continue;
	// 	}
	// 	if ($file['type'] != 'application/pdf' && $file['type'] != 'application/msword' && $file['type'] != 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
	// 		http_response_code(422);
	// 		$errors['myFile'] = 'Неверный формат';
	// 	}
	// }

	if (!empty($errors)) {
		$error_output = '';
		$error_output  .= '<ul class="errors-list">';
		foreach ($errors as $key => $value) {
			$error_output .= '<li data-error="' . $key . '"></li>';
		}
		$error_output .= '<li class="errors-list__item">Заполните обязательные поля!</li>';
		$error_output .= '</ul>';
		http_response_code(422);
		echo $error_output;
		die();

		// http_response_code(422);
		// echo json_encode($errors, JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
		// die();
	}

	// $captcha = getCaptcha($_POST['g-recaptcha-response-form-callback']);

	// if ($captcha->success == true && $captcha->score >= 0.4) {

	$name = $_POST['name'];
	$email = $_POST['email'];

	// $file_tmp  = $_FILES['myFile']['tmp_name'];
	// $file_name = $_FILES['myFile']['name'];

	$message = "ФИО - " . $name . "\nEmail - " . $email;
	// $messageHtml = "
	// <h2>Новое сообщение</h2>
	// <b>Имя:</b> $name<br>
	// <b>Email:</b> $email<br>
	// <p>текст блабла</p>
	// ";

	// require 'phpmailer/PHPMailer.php';
	// require 'phpmailer/SMTP.php';
	// require 'phpmailer/Exception.php';

	// $mail = new PHPMailer\PHPMailer\PHPMailer();
	// $mail->isSMTP();
	// $mail->CharSet = "UTF-8";
	// $mail->SMTPAuth   = true;
	// $mail->SMTPDebug = 0;
	// $mail->Host = 'ssl://smtp.mail.ru';
	// $mail->Port = 465;
	// $mail->Username = 'noreply@domain.ru';
	// $mail->Password = 'pass';
	// // $mail->setFrom('noreply@domain.ru', 'domain.ru');
	// $mail->From = 'noreply@domain.ru';
	// $mail->FromName = 'domain.ru';
	// $mail->Sender = 'noreply@domain.ru';
	// // $mail->addAddress('sendto');
	// $mail->addAddress('sendto');
	// $mail->Subject = 'Письмо с сайта domain.ru';
	// // $mail->isHTML(true);
	// // $mail->Body = $messageHtml;
	// $mail->Body = $message;
	// $mail->AddAttachment($file_tmp, $file_name);

	// if ($mail->send()) {
	// 	$ok = true;
	// }


	$headers = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/plain; charset=utf-8";
	if (mail("sendto@sendto.ru", "Письмо с сайта domain.ru", $message, $headers)) {
		$ok = true;
	}


	// $tokenTG = "tokenTG";
	// $chatIdTG = "chatIdTG";
	// $arrTG = [
	// 	'Имя:' => $name ? $name : '—',
	// 	'Телефон:' => $phone ? $phone : '—',
	// ];

	// $txtTG = "Сообщение с сайта domain.ru. \r\n";
	// foreach($arrTG as $key => $value) {
	// 	$txtTG .= "<b>".$key."</b> ".$value."\r\n";
	// };

	$sendToTelegram = file_get_contents("https://api.telegram.org/bot" . $tokenTG . "/sendMessage?chat_id=" . $chatIdTG . "&parse_mode=html&text=" . urlencode($txtTG));

	if ($ok) {
		http_response_code(200);
		echo '<div class="success"><p>Ваше сообщение успешно отправлено!</p></div>';
	} else {
		http_response_code(422);
		echo '<ul class="errors-list"><li class="errors-list__item">Что-то пошло не так!</li></ul>';
	}

	// if ($ok) {
	// 	http_response_code(200);
	// 	echo json_encode(['result' => 'ok'], JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
	// } else {
	// 	http_response_code(422);
	// 	echo json_encode(['result' => 'false', 'error' => 'Что-то пошло не так!'], JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
	// }

	// } else {
	// 	http_response_code(422);
	// 	echo '<ul class="errors-list"><li class="errors-list__item">Попробуйте позже!</li></ul>';
	// }
} else {
	header('Location: /404/');
	die();
}

<?php

// define('TOKEN_BOT_TG', 'token');
// define('CHAT_ID_TG', 'chat_id');


add_action('wp_ajax_modal', 'modal_action');
add_action('wp_ajax_nopriv_modal', 'modal_action');

function modal_action() {
	$_POST = cleanPostArr($_POST);

	/* в форме: wp_nonce_field('modal_nonce_action', 'modal_nonce_field'); */
	if (!wp_verify_nonce($_POST['modal_nonce_field'], 'modal_nonce_action')) {
		http_response_code(422);
		echo json_encode(['result' => 'false', 'message' => 'Что-то пошло не так!']);
		die();
	}

	$errors = [];
	if (empty($_POST['name'])) {
		$errors['name'] = 'Введите имя';
	}
	if (empty($_POST['phone'])) {
		$errors['phone'] = 'Введите телефон';
	}
	if (!isset($_POST['privacy'])) {
		$errors['privacy'] = 'Обработка персональных данных';
	}

	// if (empty($_POST['form_email']) || !filter_var($_POST['form_email'], FILTER_VALIDATE_EMAIL)) {
	// $errors['form_email'] = 'Введите email';
	// }

	if (!empty($errors)) {
		http_response_code(422);
		echo json_encode(['result' => 'false', 'errors' => $errors, 'message' => 'Заполните обязательные поля!']);
		die();
	}

	// $captcha = getCaptcha($_POST['g-recaptcha-response-modal']);

	// if ($captcha->success == true && $captcha->score >= 0.2) {
	$name = $_POST['name'];
	$email = $_POST['email'];

	$arrMessage = [
		'Имя: ' => $name ? $name : '—',
		'Почта: ' => $email ? $email : '—',
	];

	$messageTitle = "Сообщение с формы — SiteName.";
	$message = '';
	// $messageHtml = '';
	foreach ($arrMessage as $key => $value) {
		$message .= $key . $value . "\r\n";
		// $messageHtml .= "<b>" . $key . "</b>" . $value . "\r\n";
	};

	// $messageHtml = "
	// <h2>Новое сообщение</h2>
	// <b>Имя:</b> $name<br>
	// <b>Email:</b> $email<br>
	// <p>текст блабла</p>
	// ";


	$headers = "MIME-Version: 1.0\r\n";
	$headers .= "Content-type: text/plain; charset=utf-8";
	if (mail("sendto@sendto.ru", $messageTitle, $message, $headers)) {
		$ok = true;
	}

	// $headers = "MIME-Version: 1.0\r\n";
	// $headers .= "Content-Type: text/html; charset=UTF-8"; // html письмо
	// if (mail("sendto@sendto.ru", $messageTitle, $messageHtml, $headers)) {
	// 	$ok = true;
	// }

	// $sendToTelegram = file_get_contents("https://api.telegram.org/bot" . TOKEN_BOT_TG . "/sendMessage?chat_id=" . CHAT_ID_TG . "&parse_mode=html&text=" . urlencode($messageTitle . "\r\n" . $message));


	if ($ok) {
		http_response_code(200);
		echo json_encode(['result' => 'ok', 'message' => 'Сообщение успешно отправлено!']);
	} else {
		http_response_code(422);
		echo json_encode(['result' => 'false', 'message' => 'Что-то пошло не так!']);
	}
	// } else {
	// 	http_response_code(422);
	// 	echo json_encode(['result' => 'false', 'message' => 'Попробуйте позже!']);
	// }

	die();
}

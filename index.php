<?php
	// Composer�ŃC���X�g�[���������C�u�������ꊇ�ǂݍ���
	require_once __DIR__ . '/vendor/autoload.php';

	// �A�N�Z�X�g�[�N�����g��CurlHTTPClient���C���X�^���X��
	$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(getenv('CHANNEL_ACCESS_TOKEN'));

	// CurlHTTPClient�ƃV�[�N���b�g���g��LINEBot���C���X�^���X��
	$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => getenv('CHANNEL_SECRET')]);

	// LINE Messaging API�����N�G�X�g�ɕt�^�����������擾
	$signature = $_SERVER['HTTP_' . \LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];

	// �������������`�F�b�N�B�����ł���΃��N�G�X�g���p�[�X���z���
	$events = $bot->parseEventRequest(file_get_contents('PHP://input'), $signature);

	// �z��Ɋi�[���ꂽ�e�C�x���g�����[�v�ŏ���
	foreach($events as $event) {
		// �e�L�X�g��ԐM
		//$bot->replyText($event->getReplyToken(), 'TextMessage');
		replyTextMessage($bot, $event->getReplyToken(), 'SendTextMessage');
	}

	// �e�L�X�g�𑗐M�B������LINEBot�A�ԐM��A�e�L�X�g
	function replyTextMessage($bot, $replyToken, $text) {
		// �ԐM���s�����X�|���X���擾
		// TextMessageBuiler�̈����̓e�L�X�g
		$response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($text));

		// ���X�|���X���ُ�ȏꍇ
		if (!$response->isSuccessed()){
			//�G���[���e���o��
			error_log('Failed! '. $response->getHTTPStatus . ' '. $response->getRawBody());
		}
	}

	// �摜�𑗐M�B������LINEBot�A�ԐM��A�摜URL�A�T���l�C��URL
	function replyImageMessage($bot, $replyToken, $originalImageUrl, $previewImageUrl){
		// ImageMessageBuilder�̈����͉摜URL�A�T���l�C��URL
		$response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\ImageMessageBuiler(
						$originalImageUrl, $previewImageUrl));
		if (!$response->isSuccessed()){
			error_log('Failed!'. $response->getHTTPStatus . ' ' . $response->getRawBody());
		}
	}
?>
<?php
	use \LINE\LINEBot\MessageBuilder\TextMessageBuilder;
	use \LINE\LINEBot\MessageBuilder\ImageMessageBuilder;
	use \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder;
	use \LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder;
	use \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder;
	
	// Composerでインストールしたライブラリを一括読み込み
	require_once __DIR__ . '/vendor/autoload.php';

	// アクセストークンを使いCurlHTTPClientをインスタンス化
	$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(getenv('CHANNEL_ACCESS_TOKEN'));

	// CurlHTTPClientとシークレットを使いLINEBotをインスタンス化
	$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => getenv('CHANNEL_SECRET')]);

	// LINE Messaging APIがリクエストに付与した署名を取得
	$signature = $_SERVER['HTTP_' . \LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];

	// 署名が正当かチェック。正当であればリクエストをパースし配列へ
	$events = $bot->parseEventRequest(file_get_contents('PHP://input'), $signature);

	// 配列に格納された各イベントをループで処理
	foreach($events as $event) {
		error_log('type:' . $event->getType());

		switch($event->getType()){
			case 'follow':
				joinFriend($bot, $event);
				break;
			case 'message':
				replyTextMessage($bot, $event->getReplyToken(), 'Message Recieve OK!');
				//replyConfirmTemplateMessage($bot, $event->getReplyToken(), 'どうする？');
				break;
		}
	}

	// 友達追加時のイベント
	function joinFriend($bot, $event) {
		//$returnStr = 'userId:' . $event->getUserId();
		$msg = '友達追加して頂きありがとうございます。アンケートに答えて頂くとお得なプレゼントを進呈します。アンケートに回答しますか？';
		//replyTextMessage($bot, $event->getReplyToken(), $msg);

		//$msg = 'アンケートに回答しますか？'; 
		replyConfirmTemplateMessage($bot, $event->getReplyToken(), $msg);
	}

	// テキストを送信。引数はLINEBot、返信先、テキスト
	function replyTextMessage($bot, $replyToken, $text) {
		// 返信を行いレスポンスを取得
		// TextMessageBuilerの引数はテキスト
		$response = $bot->replyMessage($replyToken, new TextMessageBuilder($text));
	}

	// 画像を送信。引数はLINEBot、返信先、画像URL、サムネイルURL
	function replyImageMessage($bot, $replyToken, $originalImageUrl, $previewImageUrl){
		// ImageMessageBuilderの引数は画像URL、サムネイルURL
		$response = $bot->replyMessage($replyToken, new ImageMessageBuiler(
						$originalImageUrl, $previewImageUrl));
		if (!$response->isSuccessed()){
			error_log('Failed!'. $response->getHTTPStatus . ' ' . $response->getRawBody());
		}
	}

	function replyConfirmTemplateMessage($bot, $replyToken, $msg){
		$bot->replyMessage(
			$replyToken,
			new TemplateMessageBuilder(
				'Confirm alt text',
				new ConfirmTemplateBuilder($msg, [
					new MessageTemplateActionBuilder('Yes', 'はい'),
					new MessageTemplateActionBuilder('No', 'いいえ'),
				])
			)
		);
	}
?>
<?php
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
		// テキストを返信
		//$bot->replyText($event->getReplyToken(), 'TextMessage');
		replyTextMessage($bot, $event->getReplyToken(), 'SendTextMessage');
	}

	// テキストを送信。引数はLINEBot、返信先、テキスト
	function replyTextMessage($bot, $replyToken, $text) {
		// 返信を行いレスポンスを取得
		// TextMessageBuilerの引数はテキスト
		$response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($text));

		// レスポンスが異常な場合
		if (!$response->isSuccessed()){
			//エラー内容を出力
			error_log('Failed! '. $response->getHTTPStatus . ' '. $response->getRawBody());
		}
	}

	// 画像を送信。引数はLINEBot、返信先、画像URL、サムネイルURL
	function replyImageMessage($bot, $replyToken, $originalImageUrl, $previewImageUrl){
		// ImageMessageBuilderの引数は画像URL、サムネイルURL
		$response = $bot->replyMessage($replyToken, new \LINE\LINEBot\MessageBuilder\ImageMessageBuiler(
						$originalImageUrl, $previewImageUrl));
		if (!$response->isSuccessed()){
			error_log('Failed!'. $response->getHTTPStatus . ' ' . $response->getRawBody());
		}
	}
?>
<?php
namespace App\Http\Controllers\CommunalData;

use App\Http\Controllers\Controller;
use App\Mail\Contact;
use App\Services\Communal\Comfort\ComfortManager;
use App\Services\Communal\Gazprom\GazpromManager;
use App\Services\Communal\Parser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;


class CommunalController extends Controller
{
    public function index($value = '', ComfortManager $comManager, GazpromManager $gasManager)
    {
    	//$param = 'GAS#12, COLD#10, HOT#5';
		//dd($messages);
    	$token = env('BOT_TOKEN');
		$bot = new \TelegramBot\Api\Client($token);
		// команда для start
		$bot->command('start', function ($message) use ($bot) {
		    $answer = 'Добро пожаловать!';
		    $bot->sendMessage($message->getChat()->getId(), $answer);
		});
		// команда для помощи
		$bot->command('help', function ($message) use ($bot) {
		    $answer = 'Команды:
		/help - вывод справки';
		    $bot->sendMessage($message->getChat()->getId(), $answer);
		});


		$bot->command('push', function ($message) use ($bot, $comManager, $gasManager) {
		    $text = $message->getText();
		    $param = str_replace('/push ', '', $text);
		    $answer = 'Неизвестная команда';
		    if (!empty($param))
		    {
		    	$parser = new Parser($param);
				$messages = $parser->parse();

				// в массиве $messages содержится элементы

				$comfort = [];
				$gazprom = null;

				foreach ($messages as $mes) {
					if ($mes->type == "COL" || "HOT") {
						$comfort[] = $mes;
					}

					if ($mes->type == "GAS") {
						$gazprom = $mes->text;
					}
				}

				$comManager->make()->write($comfort);
				$gasManager->make()->write($gazprom);
		    	$answer = 'Ok';
		    }
		    $bot->sendMessage($message->getChat()->getId(), $answer);
		});


		$bot->run();

		//Log::warning('бот запускается');
		/*
		$token = env('BOT_TOKEN');
		$chatId = env('CHAT_ID');
		$messageText = 'Передайте показания';

		$bot = new \TelegramBot\Api\BotApi($token);

		$bot->sendMessage($chatId, $messageText);*/
		//$bot->run();
    }
}
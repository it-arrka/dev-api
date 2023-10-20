<?php
//In this file, there is list of all important and common modules will be loaded.

//1. vendor autoload 
require_once dirname(__DIR__) . '/vendor/autoload.php'; // Include Composer's autoloader

//2. Load .env file
require_once dirname(__DIR__)."/config/load_env.php";

//3. Error handling File
require_once dirname(__DIR__)."/config/error/error.php";

//4. Error handling File
require_once dirname(__DIR__)."/config/success/success.php";

//5. DB Config file
require_once dirname(__DIR__)."/db/config.php";

//6. log handler file
require_once dirname(__DIR__)."/config/log_handler.php";

//7. token handling api
require_once dirname(__DIR__)."/token/token_generation.php";


?>
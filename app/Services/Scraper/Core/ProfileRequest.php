<?php
namespace App\Services\Scraper\Core;

use Exception;
use GuzzleHttp\Psr7\Response;

interface ProfileRequest
{
    public function processOnRequestIsFulfilled(Response $response);

    public function processOnRequestIsFailed(Exception $exception);
}
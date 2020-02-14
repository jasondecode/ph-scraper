<?php
namespace App\Services\Scraper\Core;

interface ProfileRequest
{
    public function processOnRequestIsFulfilled();

    public function processOnRequestIsFailed();
}
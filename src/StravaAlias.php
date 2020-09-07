<?php


namespace FkrCode\Strava;

use Illuminate\Support\Facades\Facade;

class StravaAlias extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'StravaFkr';
    }

}
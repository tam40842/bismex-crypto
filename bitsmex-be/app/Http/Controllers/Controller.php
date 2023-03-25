<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Controllers\Vuta\Vuta;
use App\Http\Controllers\Vuta\CryptoMap;
use App\Http\Controllers\Vuta\Status;

class Controller extends BaseController
{
    use Vuta, CryptoMap, Status, AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}

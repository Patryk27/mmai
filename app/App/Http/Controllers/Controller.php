<?php

namespace App\App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

/**
 * This is the base controller - all application's controller should inherit
 * from it.
 */
abstract class Controller extends BaseController
{

    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

}
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EstablecimientoController;

Route::get('/marcadores', [EstablecimientoController::class, 'apiMarcadores']);

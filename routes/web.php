<?php

use App\Http\Controllers\CvController;
use App\Livewire\Portfolio\PortfolioPage;
use Illuminate\Support\Facades\Route;

Route::get('/', PortfolioPage::class);

Route::get('/cv', CvController::class)->name('cv');

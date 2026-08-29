<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Support\CvFormatter;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CvController
{
    public function __invoke(): StreamedResponse
    {
        $user = User::with([
            'links',
            'projects',
            'experiences',
            'skills',
            'educations',
            'languages',
            'courses',
        ])->first();

        abort_unless($user instanceof User, 404);

        $pdf = Pdf::loadView('cv', [
            'user' => $user,
            'lastUpdated' => CvFormatter::lastUpdated($user),
        ]);

        return response()->streamDownload(
            static function () use ($pdf): void {
                echo $pdf->output();
            },
            'CV_DenisPina_FullStack.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }
}

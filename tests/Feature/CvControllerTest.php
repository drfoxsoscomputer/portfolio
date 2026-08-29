<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesPortfolioUser;
use Tests\TestCase;

class CvControllerTest extends TestCase
{
    use CreatesPortfolioUser, RefreshDatabase;

    public function test_cv_route_returns_a_valid_pdf_download(): void
    {
        $this->createUserWithRelations();

        $response = $this->get('/cv');

        $response
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf')
            ->assertDownload('CV_DenisPina_FullStack.pdf');

        $this->assertStringStartsWith('%PDF', $response->streamedContent());
    }

    public function test_cv_route_returns_404_without_a_portfolio_owner(): void
    {
        $this->get('/cv')->assertNotFound();
    }
}

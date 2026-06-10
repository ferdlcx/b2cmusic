<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiLogisticsTest extends TestCase
{
    /** @test */
    public function biteship_search_area_endpoint_returns_json()
    {
        $response = $this->get('/api/biteship/search-area?q=setiabudi');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([]);
    }

    /** @test */
    public function biteship_rates_endpoint_requires_destination_and_weight()
    {
        $response = $this->post('/api/biteship/rates', []);
        
        // It should return 302 redirecting back with validation errors
        // because it expects 'destination_area_id' and 'weight'
        $response->assertStatus(302);
        $response->assertSessionHasErrors(['destination_area_id', 'weight']);
    }
}

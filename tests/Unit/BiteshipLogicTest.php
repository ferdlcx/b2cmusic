<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Http\Controllers\BiteshipController;
use Illuminate\Http\Request;

class BiteshipLogicTest extends TestCase
{
    /** @test */
    public function it_can_validate_biteship_api_key_existence()
    {
        $controller = new BiteshipController();
        
        // Use reflection to access private apiKey property
        $reflection = new \ReflectionClass($controller);
        $property = $reflection->getProperty('apiKey');
        $property->setAccessible(true);
        $apiKey = $property->getValue($controller);
        
        $this->assertNotEmpty($apiKey, 'Biteship API Key should not be empty');
    }
}

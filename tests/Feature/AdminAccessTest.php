<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected User $admin;
    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        // Create standard test users
        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@test.com',
            'password' => bcrypt('password'),
            'role' => 'super_admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->admin = User::create([
            'name' => 'Staff Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);

        $this->customer = User::create([
            'name' => 'Customer',
            'email' => 'customer@test.com',
            'password' => bcrypt('password'),
            'role' => 'customer',
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
    }

    /**
     * Test that AdminIpWhitelistMiddleware blocks non-mobile, non-whitelisted IP.
     */
    public function test_ip_whitelist_blocks_unauthorized_desktop_ip(): void
    {
        // Set allowed IP to a specific IP
        putenv('ADMIN_ALLOWED_IPS=192.168.1.100');

        $response = $this->actingAs($this->admin)
            ->withServerVariables(['REMOTE_ADDR' => '10.0.0.1'])
            ->get('/admin/dashboard');

        $response->assertStatus(403);
        $response->assertSee('Akses Ditolak');
    }

    /**
     * Test that AdminIpWhitelistMiddleware allows mobile User-Agent regardless of IP.
     */
    public function test_ip_whitelist_bypasses_for_mobile_user_agent(): void
    {
        // Set allowed IP to a specific IP, which REMOTE_ADDR won't match
        putenv('ADMIN_ALLOWED_IPS=192.168.1.100');

        // Request as Mobile User-Agent
        $response = $this->actingAs($this->admin)
            ->withHeaders([
                'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1'
            ])
            ->withServerVariables(['REMOTE_ADDR' => '10.0.0.1'])
            ->get('/admin/dashboard');

        $response->assertStatus(200);
    }

    /**
     * Test that SuperAdminMiddleware blocks regular admin role from accessing super admin endpoints.
     */
    public function test_super_admin_middleware_blocks_regular_admin(): void
    {
        // Set ALLOWED_IPS to * to bypass IP check
        putenv('ADMIN_ALLOWED_IPS=*');

        $response = $this->actingAs($this->admin)
            ->get('/admin/users');

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('error', 'Akses Ditolak: Hanya pemilik toko (Super Admin) yang diizinkan untuk mengakses fitur ini.');
    }

    /**
     * Test that SuperAdminMiddleware allows super admin role to access super admin endpoints.
     */
    public function test_super_admin_middleware_allows_super_admin(): void
    {
        // Set ALLOWED_IPS to * to bypass IP check
        putenv('ADMIN_ALLOWED_IPS=*');

        $response = $this->actingAs($this->superAdmin)
            ->get('/admin/users');

        $response->assertStatus(200);
    }

    protected function tearDown(): void
    {
        // Clean up environment variable
        putenv('ADMIN_ALLOWED_IPS');
        parent::tearDown();
    }
}

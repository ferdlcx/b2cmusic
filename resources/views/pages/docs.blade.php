@extends('layouts.app')
@section('title', 'Developer Documentation - DjudasMS')

@section('content')
<div class="max-w-[1440px] mx-auto px-4 py-12" x-data="{ activeSection: 'architecture' }">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar Navigation -->
        <aside class="w-full md:w-64 shrink-0">
            <div class="bg-white border border-walnut-800/10 shadow-sm rounded-2xl p-5 sticky top-24">
                <h3 class="font-display font-black text-lg uppercase tracking-tight text-walnut-950 border-b border-walnut-800/10 pb-3 mb-4">
                    Developer Docs
                </h3>
                <nav class="space-y-2">
                    @php
                        $navItems = [
                            'architecture' => ['icon' => 'layers', 'label' => 'System Architecture'],
                            'database' => ['icon' => 'database', 'label' => 'Database Overview'],
                            'api' => ['icon' => 'plug', 'label' => 'API Integrations'],
                            'routes' => ['icon' => 'route', 'label' => 'Routes'],
                            'webhook' => ['icon' => 'webhook', 'label' => 'Webhook Flow'],
                            'services' => ['icon' => 'server', 'label' => 'Internal Services']
                        ];
                    @endphp
                    @foreach($navItems as $key => $item)
                        <button @click="activeSection = '{{ $key }}'" 
                                :class="activeSection === '{{ $key }}' ? 'bg-walnut-900 text-gold-500' : 'text-walnut-600 hover:bg-cream-100 hover:text-walnut-900'"
                                class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-widest transition text-left">
                            <i data-lucide="{{ $item['icon'] }}" class="w-4 h-4"></i>
                            {{ $item['label'] }}
                        </button>
                    @endforeach
                </nav>
            </div>
        </aside>

        <!-- Content Area -->
        <div class="flex-1">
            <div class="bg-cream-50 border border-walnut-800/10 shadow-sm rounded-2xl p-8 md:p-12 min-h-[600px]">
                
                <!-- System Architecture -->
                <div x-show="activeSection === 'architecture'" x-transition.opacity>
                    <div class="mb-8 border-b border-walnut-800/10 pb-4">
                        <span class="text-[0.65rem] uppercase tracking-[0.45em] text-gold-600 font-bold block mb-1">Architecture</span>
                        <h2 class="font-display text-3xl font-black uppercase tracking-tight text-walnut-950">System Architecture</h2>
                    </div>
                    <div class="space-y-6">
                        <p class="text-sm text-muted leading-relaxed font-medium">DjudasMS is a modern B2C e-commerce platform built on the Laravel framework. It employs a monolithic architecture with distinct layers for routing, controllers, models, and views, enhanced by Alpine.js for lightweight frontend reactivity.</p>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="bg-white p-5 rounded-xl border border-walnut-800/10 shadow-sm">
                                <h4 class="text-xs font-bold uppercase tracking-widest text-gold-600 mb-2">Backend (Core)</h4>
                                <ul class="text-xs text-walnut-800 space-y-1.5 list-disc list-inside">
                                    <li><strong>Framework:</strong> Laravel 11 (PHP 8.3)</li>
                                    <li><strong>Design Pattern:</strong> MVC (Model-View-Controller)</li>
                                    <li><strong>ORM:</strong> Eloquent</li>
                                </ul>
                            </div>
                            <div class="bg-white p-5 rounded-xl border border-walnut-800/10 shadow-sm">
                                <h4 class="text-xs font-bold uppercase tracking-widest text-gold-600 mb-2">Frontend (UI)</h4>
                                <ul class="text-xs text-walnut-800 space-y-1.5 list-disc list-inside">
                                    <li><strong>Templating:</strong> Blade</li>
                                    <li><strong>Styling:</strong> Tailwind CSS v4</li>
                                    <li><strong>Interactivity:</strong> Alpine.js & Lucide Icons</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Database Overview -->
                <div x-show="activeSection === 'database'" x-transition.opacity style="display: none;">
                    <div class="mb-8 border-b border-walnut-800/10 pb-4">
                        <span class="text-[0.65rem] uppercase tracking-[0.45em] text-gold-600 font-bold block mb-1">Data Layer</span>
                        <h2 class="font-display text-3xl font-black uppercase tracking-tight text-walnut-950">Database Overview</h2>
                    </div>
                    <div class="space-y-4">
                        <p class="text-sm text-muted leading-relaxed font-medium mb-6">The system uses a MySQL 8 relational database hosted on Aiven Cloud. The schema is highly normalized to support e-commerce operations.</p>
                        <div class="overflow-x-auto rounded-xl border border-walnut-800/10">
                            <table class="w-full text-left text-sm whitespace-nowrap bg-white">
                                <thead class="bg-walnut-900 text-gold-500 text-[0.65rem] uppercase tracking-widest font-bold">
                                    <tr>
                                        <th class="px-6 py-4">Table Group</th>
                                        <th class="px-6 py-4">Description</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-walnut-800/5 text-xs text-walnut-900 font-medium">
                                    <tr><td class="px-6 py-3 font-bold">users, addresses</td><td class="px-6 py-3 text-muted">Authentication, roles (customer/admin), and shipping addresses.</td></tr>
                                    <tr><td class="px-6 py-3 font-bold">products, categories, brands</td><td class="px-6 py-3 text-muted">Catalog management, stock, hierarchical categories.</td></tr>
                                    <tr><td class="px-6 py-3 font-bold">orders, order_items, shipments</td><td class="px-6 py-3 text-muted">Order lifecycle tracking, biteship correlation, and tracking history.</td></tr>
                                    <tr><td class="px-6 py-3 font-bold">carts, wishlists</td><td class="px-6 py-3 text-muted">Customer intent and pending transactions.</td></tr>
                                    <tr><td class="px-6 py-3 font-bold">payments, coupons, flash_sales</td><td class="px-6 py-3 text-muted">Midtrans transaction references and promotional features.</td></tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- API Integrations -->
                <div x-show="activeSection === 'api'" x-transition.opacity style="display: none;">
                    <div class="mb-8 border-b border-walnut-800/10 pb-4">
                        <span class="text-[0.65rem] uppercase tracking-[0.45em] text-gold-600 font-bold block mb-1">External Systems</span>
                        <h2 class="font-display text-3xl font-black uppercase tracking-tight text-walnut-950">API Integrations</h2>
                    </div>
                    <div class="space-y-6">
                        <div class="bg-white border-l-4 border-gold-500 p-5 rounded-r-xl shadow-sm">
                            <h4 class="font-bold text-sm text-walnut-950 mb-1">Midtrans</h4>
                            <p class="text-xs text-muted mb-2">Payment gateway using Snap popup for checkout and webhooks for asynchronous status updates.</p>
                        </div>
                        <div class="bg-white border-l-4 border-gold-500 p-5 rounded-r-xl shadow-sm">
                            <h4 class="font-bold text-sm text-walnut-950 mb-1">Biteship</h4>
                            <p class="text-xs text-muted mb-2">Courier integration for AWB generation and real-time tracking webhooks.</p>
                        </div>
                        <div class="bg-white border-l-4 border-gold-500 p-5 rounded-r-xl shadow-sm">
                            <h4 class="font-bold text-sm text-walnut-950 mb-1">RajaOngkir (Komerce)</h4>
                            <p class="text-xs text-muted mb-2">Domestic shipping rate calculation based on precise subdistrict origins and destinations.</p>
                        </div>
                        <div class="bg-white border-l-4 border-gold-500 p-5 rounded-r-xl shadow-sm">
                            <h4 class="font-bold text-sm text-walnut-950 mb-1">MailerSend</h4>
                            <p class="text-xs text-muted mb-2">Transactional email service for OTPs, order confirmations, and reset password links.</p>
                        </div>
                    </div>
                </div>

                <!-- Routes -->
                <div x-show="activeSection === 'routes'" x-transition.opacity style="display: none;">
                    <div class="mb-8 border-b border-walnut-800/10 pb-4">
                        <span class="text-[0.65rem] uppercase tracking-[0.45em] text-gold-600 font-bold block mb-1">Navigation</span>
                        <h2 class="font-display text-3xl font-black uppercase tracking-tight text-walnut-950">Routes Overview</h2>
                    </div>
                    <div class="space-y-4 text-xs font-medium text-walnut-800 leading-relaxed">
                        <p>Routes are logically grouped in <code>routes/web.php</code> using Laravel's middleware groups:</p>
                        <ul class="list-disc list-inside space-y-2 mt-4 bg-white p-5 rounded-xl border border-walnut-800/10 shadow-sm">
                            <li><strong class="text-gold-600">Public:</strong> <code>/</code>, <code>/catalog</code>, <code>/product/{slug}</code>, <code>/docs</code></li>
                            <li><strong class="text-gold-600">Auth & Guest:</strong> <code>/login</code>, <code>/register</code>, <code>/password/reset</code></li>
                            <li><strong class="text-gold-600">Customer (Verified):</strong> <code>/dashboard</code>, <code>/cart</code>, <code>/checkout</code>, <code>/orders</code></li>
                            <li><strong class="text-gold-600">Admin Panel:</strong> Prefix <code>/admin</code> protected by <code>admin</code> middleware.</li>
                            <li><strong class="text-gold-600">Webhooks:</strong> <code>/midtrans/webhook</code>, <code>/api/biteship/webhook</code></li>
                        </ul>
                    </div>
                </div>

                <!-- Webhook Flow -->
                <div x-show="activeSection === 'webhook'" x-transition.opacity style="display: none;">
                    <div class="mb-8 border-b border-walnut-800/10 pb-4">
                        <span class="text-[0.65rem] uppercase tracking-[0.45em] text-gold-600 font-bold block mb-1">Data Pipeline</span>
                        <h2 class="font-display text-3xl font-black uppercase tracking-tight text-walnut-950">Webhook Flow</h2>
                    </div>
                    <div class="space-y-6">
                        <p class="text-sm text-muted font-medium">Webhooks form the asynchronous backbone of DjudasMS's transaction processing.</p>
                        
                        <div class="space-y-4">
                            <div class="bg-white p-5 rounded-xl border border-walnut-800/10 shadow-sm">
                                <h4 class="text-sm font-bold text-walnut-950 mb-2">1. Payment (Midtrans)</h4>
                                <p class="text-xs text-muted mb-2">When a payment state changes (e.g., from pending to <code>settlement</code>), Midtrans posts to <code>/midtrans/webhook</code>. The system validates the signature, updates the <code>payments</code> table, and alters the main <code>orders</code> status to <strong>Paid</strong>.</p>
                            </div>
                            
                            <div class="bg-white p-5 rounded-xl border border-walnut-800/10 shadow-sm">
                                <h4 class="text-sm font-bold text-walnut-950 mb-2">2. Shipping (Biteship)</h4>
                                <p class="text-xs text-muted mb-2">When a courier updates a package's location or state, Biteship POSTs to <code>/api/biteship/webhook</code>. DjudasMS uses the <code>order_id</code> to find the internal shipment record and updates its <code>status</code>. If the status is <strong>delivered</strong>, an email is dispatched and the order is completed.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Internal Services -->
                <div x-show="activeSection === 'services'" x-transition.opacity style="display: none;">
                    <div class="mb-8 border-b border-walnut-800/10 pb-4">
                        <span class="text-[0.65rem] uppercase tracking-[0.45em] text-gold-600 font-bold block mb-1">Operations</span>
                        <h2 class="font-display text-3xl font-black uppercase tracking-tight text-walnut-950">Internal Services</h2>
                    </div>
                    <div class="space-y-4">
                        <p class="text-sm text-muted font-medium mb-4">Core business logic is encapsulated in dedicated controllers and service classes to ensure maintainability.</p>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div class="bg-white p-5 rounded-xl border border-walnut-800/10 shadow-sm space-y-2">
                                <h4 class="text-xs font-bold uppercase tracking-widest text-gold-600">CheckoutService</h4>
                                <p class="text-[0.65rem] text-muted">Handles cart conversion, total calculation, shipping cost integration, and Midtrans Snap token generation in a single atomic transaction.</p>
                            </div>
                            <div class="bg-white p-5 rounded-xl border border-walnut-800/10 shadow-sm space-y-2">
                                <h4 class="text-xs font-bold uppercase tracking-widest text-gold-600">TrackingController</h4>
                                <p class="text-[0.65rem] text-muted">Manages the Biteship webhook simulator (<code>/simulasi</code>) and interpolates tracking coordinates for customer order pages.</p>
                            </div>
                            <div class="bg-white p-5 rounded-xl border border-walnut-800/10 shadow-sm space-y-2">
                                <h4 class="text-xs font-bold uppercase tracking-widest text-gold-600">RajaOngkirController</h4>
                                <p class="text-[0.65rem] text-muted">Acts as a proxy cache for RajaOngkir API calls, reducing rate limits for area searches and tariff lookups.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

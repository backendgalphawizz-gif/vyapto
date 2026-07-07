<?php

namespace Database\Seeders;

use App\Models\WebsiteBlog;
use App\Models\WebsiteCareerItem;
use App\Models\WebsitePageSection;
use App\Models\WebsiteProduct;
use App\Models\WebsiteService;
use Illuminate\Database\Seeder;

class WebsiteSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedPageSections();
        $this->seedServices();
        $this->seedProducts();
        $this->seedCareerItems();
        $this->seedBlogs();
    }

    private function seedPageSections(): void
    {
        $sections = [
            ['page' => 'home', 'section_key' => 'hero', 'title' => 'Smart Delivery', 'subtitle' => 'Workforce Platform', 'content' => 'Manage attendance, shipments, salary tracking and field operations from one secure platform.', 'icon' => 'fa-user-group', 'sort_order' => 1],
            ['page' => 'home', 'section_key' => 'hero_badge', 'title' => 'Employee Portal', 'icon' => 'fa-user-group', 'sort_order' => 2],
            ['page' => 'home', 'section_key' => 'hero_bg', 'title' => 'Hero Background', 'sort_order' => 3, 'extra' => ['default_image' => 'images/web-auth-bg.png']],
            ['page' => 'home', 'section_key' => 'hero_image', 'title' => 'Hero Phone', 'sort_order' => 4, 'extra' => ['default_image' => 'images/app-screen-2.png']],
            ['page' => 'home', 'section_key' => 'about', 'title' => "Built for Smarter\nDelivery Operations", 'content' => 'We empower delivery teams and operations managers with the tools they need to work smarter and deliver better.', 'sort_order' => 50],
            ['page' => 'home', 'section_key' => 'promise_card', 'title' => 'Our Promise', 'content' => 'To provide a secure, reliable and user-friendly platform that helps every delivery associate succeed.', 'icon' => 'fa-shield-halved', 'sort_order' => 51],
            ['page' => 'home', 'section_key' => 'cta', 'title' => 'Ready To Get Started?', 'content' => 'Join thousands of delivery associates using Vyapto.', 'sort_order' => 90],
            ['page' => 'home', 'section_key' => 'cta_bg', 'title' => 'CTA Background', 'sort_order' => 91, 'extra' => ['default_image' => 'images/foot-card-bg.png']],
            ['page' => 'services', 'section_key' => 'hero', 'title' => 'Our Services', 'content' => 'End-to-end logistics and workforce solutions tailored for growing businesses.', 'sort_order' => 1],
            ['page' => 'products', 'section_key' => 'hero', 'title' => 'Our Products', 'content' => 'Innovative platforms and products powering the Vyapto ecosystem.', 'sort_order' => 1],
            ['page' => 'careers', 'section_key' => 'hero', 'title' => 'Careers & Highlights', 'content' => 'Build your career with Vyapto — where logistics meets innovation.', 'sort_order' => 1],
            ['page' => 'blogs', 'section_key' => 'hero', 'title' => 'Blogs', 'content' => 'Insights, updates, and stories from the Vyapto team.', 'sort_order' => 1],
            ['page' => 'contact', 'section_key' => 'hero', 'title' => 'Contact Us', 'content' => 'Have a question or want to work with us? Reach out and our team will get back to you.', 'sort_order' => 1],
            ['page' => 'global', 'section_key' => 'site_logo_desktop', 'title' => 'Desktop Logo', 'sort_order' => 1, 'extra' => ['default_image' => 'images/nav-logo.png']],
            ['page' => 'global', 'section_key' => 'site_logo_mobile', 'title' => 'Mobile Logo', 'sort_order' => 2, 'extra' => ['default_image' => 'images/nav-logo-mobile.png']],
            ['page' => 'global', 'section_key' => 'site_logo_footer', 'title' => 'Footer Logo', 'sort_order' => 3, 'extra' => ['default_image' => 'images/nav-logo.png']],
        ];

        foreach ($sections as $section) {
            WebsitePageSection::updateOrCreate(
                ['page' => $section['page'], 'section_key' => $section['section_key']],
                array_merge(['status' => true], $section)
            );
        }

        $heroFeatures = [
            ['title' => 'Secure Access', 'subtitle' => 'OTP Based Login', 'icon' => 'fa-shield-halved'],
            ['title' => 'GPS Attendance', 'subtitle' => 'Live Tracking', 'icon' => 'fa-location-dot'],
            ['title' => 'Shipment Tracking', 'subtitle' => 'Real Time Updates', 'icon' => 'fa-box'],
            ['title' => 'Salary Reports', 'subtitle' => 'Work Reports', 'icon' => 'fa-file-lines'],
        ];

        foreach ($heroFeatures as $i => $feature) {
            WebsitePageSection::updateOrCreate(
                ['page' => 'home', 'section_key' => 'feature_' . ($i + 1)],
                array_merge($feature, ['page' => 'home', 'sort_order' => 10 + $i, 'status' => true])
            );
        }

        $stats = [
            ['title' => '1000+', 'subtitle' => 'Active Employees', 'icon' => 'fa-users'],
            ['title' => '25K+', 'subtitle' => 'Shipments Delivered', 'icon' => 'fa-box'],
            ['title' => '50+', 'subtitle' => 'Locations', 'icon' => 'fa-location-dot'],
            ['title' => '99.9%', 'subtitle' => 'Secure Platform', 'icon' => 'fa-shield-halved'],
        ];

        foreach ($stats as $i => $stat) {
            WebsitePageSection::updateOrCreate(
                ['page' => 'home', 'section_key' => 'stat_' . ($i + 1)],
                array_merge($stat, ['page' => 'home', 'sort_order' => 20 + $i, 'status' => true])
            );
        }

        WebsitePageSection::updateOrCreate(
            ['page' => 'home', 'section_key' => 'features_header'],
            [
                'page' => 'home',
                'title' => 'Everything You Need, In One Platform',
                'subtitle' => 'FEATURES',
                'content' => 'Built for delivery associates to simplify daily operations and maximize efficiency.',
                'icon' => 'fa-layer-group',
                'sort_order' => 30,
                'status' => true,
            ]
        );

        $platformFeatures = [
            ['title' => 'Easy Attendance', 'content' => 'Punch in/out with GPS location and selfie verification for accurate attendance.', 'default_image' => 'images/feature-icon1.png'],
            ['title' => 'Manage Deliveries', 'content' => 'Get assigned shipments and update delivery status in real time.', 'default_image' => 'images/feature-icon2.png'],
            ['title' => 'Real-Time Tracking', 'content' => 'Live tracking of deliveries and routes to ensure complete visibility.', 'default_image' => 'images/feature-icon3.png'],
            ['title' => 'Salary on Track', 'content' => 'Access your salary slips, earnings and work reports anytime.', 'default_image' => 'images/feature-icon4.png'],
            ['title' => 'Performance Insights', 'content' => 'Track your performance and delivery statistics with detailed insights.', 'default_image' => 'images/feature-icon5.png'],
            ['title' => 'Secure & Trusted', 'content' => 'Your data is protected with industry-standard security and privacy.', 'default_image' => 'images/feature-icon6.png'],
        ];

        foreach ($platformFeatures as $i => $feature) {
            WebsitePageSection::updateOrCreate(
                ['page' => 'home', 'section_key' => 'platform_feature_' . ($i + 1)],
                [
                    'page' => 'home',
                    'title' => $feature['title'],
                    'content' => $feature['content'],
                    'extra' => ['default_image' => $feature['default_image']],
                    'sort_order' => 31 + $i,
                    'status' => true,
                ]
            );
        }

        WebsitePageSection::updateOrCreate(
            ['page' => 'home', 'section_key' => 'mobile_app'],
            [
                'page' => 'home',
                'title' => "Your Work,\nOn The Go",
                'subtitle' => 'MOBILE APP',
                'content' => 'Our Android app helps you stay connected, manage deliveries, mark attendance and track earnings — anytime, anywhere.',
                'icon' => 'fa-mobile-screen',
                'sort_order' => 40,
                'status' => true,
            ]
        );

        WebsitePageSection::updateOrCreate(
            ['page' => 'home', 'section_key' => 'mobile_phone_left'],
            ['page' => 'home', 'title' => 'Left Phone', 'sort_order' => 41, 'status' => true, 'extra' => ['default_image' => 'images/app-screen-1.png']]
        );

        WebsitePageSection::updateOrCreate(
            ['page' => 'home', 'section_key' => 'mobile_phone_right'],
            ['page' => 'home', 'title' => 'Right Phone', 'sort_order' => 42, 'status' => true, 'extra' => ['default_image' => 'images/app-screen-2.png']]
        );

        WebsitePageSection::updateOrCreate(
            ['page' => 'home', 'section_key' => 'play_store'],
            ['page' => 'home', 'title' => 'Play Store Badge', 'link' => '#', 'sort_order' => 43, 'status' => true, 'extra' => ['default_image' => 'images/play-store.png']]
        );

        WebsitePageSection::updateOrCreate(
            ['page' => 'home', 'section_key' => 'testimonials_header'],
            [
                'page' => 'home',
                'title' => 'Loved by Delivery Partners',
                'subtitle' => 'TESTIMONIALS',
                'content' => 'See what our employees have to say about Vyapto.',
                'icon' => 'fa-user-group',
                'sort_order' => 60,
                'status' => true,
            ]
        );

        $testimonials = [
            ['content' => 'Vyapto app makes my work so easy. Punch in, get deliveries and track earnings — everything in one place.', 'title' => 'Aman Kumar', 'subtitle' => 'Delivery Associate', 'default_image' => 'https://i.pravatar.cc/60?img=12'],
            ['content' => 'The GPS attendance is accurate and the app is very simple to use.', 'title' => 'Rohit Paswan', 'subtitle' => 'Delivery Associate', 'default_image' => 'https://i.pravatar.cc/60?img=15'],
            ['content' => 'I can track my salary and download payslips anytime. Very helpful!', 'title' => 'Vivek Singh', 'subtitle' => 'Delivery Associate', 'default_image' => 'https://i.pravatar.cc/60?img=18'],
        ];

        foreach ($testimonials as $i => $testimonial) {
            WebsitePageSection::updateOrCreate(
                ['page' => 'home', 'section_key' => 'testimonial_' . ($i + 1)],
                [
                    'page' => 'home',
                    'title' => $testimonial['title'],
                    'subtitle' => $testimonial['subtitle'],
                    'content' => $testimonial['content'],
                    'extra' => ['default_image' => $testimonial['default_image']],
                    'sort_order' => 61 + $i,
                    'status' => true,
                ]
            );
        }
    }

    private function seedServices(): void
    {
        $services = [
            ['title' => 'Transportation & Logistics', 'slug' => 'transportation-logistics', 'description' => 'Reliable freight and fleet management for seamless supply chain operations.', 'icon' => 'fa-truck', 'sort_order' => 1],
            ['title' => 'Last-Mile Delivery Solutions', 'slug' => 'last-mile-delivery', 'description' => 'Optimized last-mile delivery with real-time tracking and route management.', 'icon' => 'fa-route', 'sort_order' => 2],
            ['title' => 'Manpower Solutions', 'slug' => 'manpower-solutions', 'description' => 'Skilled workforce deployment and management for logistics operations.', 'icon' => 'fa-users', 'sort_order' => 3],
            ['title' => 'Franchise Operations', 'slug' => 'franchise-operations', 'description' => 'Scalable franchise models with centralized control and local execution.', 'icon' => 'fa-store', 'sort_order' => 4],
            ['title' => 'Consumer Products', 'slug' => 'consumer-products', 'description' => 'Quality consumer goods delivered through our extensive distribution network.', 'icon' => 'fa-basket-shopping', 'sort_order' => 5],
        ];

        foreach ($services as $service) {
            WebsiteService::updateOrCreate(['slug' => $service['slug']], array_merge($service, ['status' => true]));
        }
    }

    private function seedProducts(): void
    {
        $products = [
            ['title' => 'Vyapto VMS', 'slug' => 'vyapto-vms', 'description' => 'Vehicle Management System for fleet tracking, maintenance, and utilization analytics.', 'sort_order' => 1],
            ['title' => 'Vyapto Foods', 'slug' => 'vyapto-foods', 'description' => 'Fresh food delivery platform connecting consumers with quality products.', 'sort_order' => 2],
        ];

        foreach ($products as $product) {
            WebsiteProduct::updateOrCreate(['slug' => $product['slug']], array_merge($product, ['status' => true]));
        }
    }

    private function seedCareerItems(): void
    {
        WebsiteCareerItem::updateOrCreate(
            ['slug' => 'life-at-vyapto'],
            [
                'category' => WebsiteCareerItem::CATEGORY_LIFE,
                'title' => 'Life at Vyapto',
                'excerpt' => 'A culture of growth, innovation, and teamwork.',
                'content' => '<p>At Vyapto, we believe in empowering every team member. From field associates to corporate staff, everyone plays a vital role in our mission to revolutionize logistics.</p>',
                'sort_order' => 1,
                'status' => true,
            ]
        );

        WebsiteCareerItem::updateOrCreate(
            ['slug' => 'join-delivery-partner'],
            [
                'category' => WebsiteCareerItem::CATEGORY_DELIVERY_PARTNER,
                'title' => 'Join as Delivery Partner',
                'excerpt' => 'Flexible earnings, your schedule, our support.',
                'content' => '<p>Become a Vyapto delivery partner and earn on your own terms. We provide training, equipment support, and a steady flow of deliveries.</p>',
                'link' => '/portal/register',
                'sort_order' => 1,
                'status' => true,
            ]
        );

        WebsiteCareerItem::updateOrCreate(
            ['slug' => 'delivery-associate-delhi'],
            [
                'category' => WebsiteCareerItem::CATEGORY_JOB_OPENING,
                'title' => 'Delivery Associate — Delhi NCR',
                'excerpt' => 'Full-time delivery role with competitive pay.',
                'content' => '<p>We are hiring delivery associates for Delhi NCR region. Experience in last-mile delivery is a plus.</p>',
                'department' => 'Operations',
                'location' => 'Delhi NCR',
                'sort_order' => 1,
                'status' => true,
            ]
        );

        WebsiteCareerItem::updateOrCreate(
            ['slug' => 'vyapto-expands-operations'],
            [
                'category' => WebsiteCareerItem::CATEGORY_NEWS,
                'title' => 'Vyapto Expands Operations to New Cities',
                'excerpt' => 'Growing our footprint across India.',
                'content' => '<p>Vyapto is proud to announce expansion into three new metropolitan areas, bringing our delivery excellence to more customers.</p>',
                'published_at' => now()->subDays(7),
                'sort_order' => 1,
                'status' => true,
            ]
        );
    }

    private function seedBlogs(): void
    {
        WebsiteBlog::updateOrCreate(
            ['slug' => 'future-of-last-mile-delivery'],
            [
                'title' => 'The Future of Last-Mile Delivery',
                'excerpt' => 'How technology is reshaping the final leg of logistics.',
                'content' => '<p>Last-mile delivery remains the most critical and costly segment of the supply chain. At Vyapto, we are investing in AI-driven route optimization, real-time tracking, and workforce management to make deliveries faster and more reliable.</p>',
                'author' => 'Vyapto Team',
                'published_at' => now()->subDays(14),
                'status' => true,
            ]
        );
    }
}

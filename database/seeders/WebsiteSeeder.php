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
            ['page' => 'home', 'section_key' => 'hero', 'title' => 'Complete Logistics Support for', 'subtitle' => 'Your Business', 'content' => 'Vyapto empowers businesses with expert logistics, accounting, IT support, and HR solutions — all from a single trusted partner.', 'sort_order' => 1],
            ['page' => 'home', 'section_key' => 'hero_badge', 'title' => 'Trusted by 500+ Businesses', 'sort_order' => 2],
            ['page' => 'home', 'section_key' => 'hero_bg', 'title' => 'Hero Background', 'sort_order' => 3, 'extra' => ['default_image' => 'images/web-auth-bg.png']],
            ['page' => 'home', 'section_key' => 'hero_image', 'title' => 'Freight & Logistics Solutions', 'sort_order' => 4, 'extra' => ['default_image' => 'https://images.unsplash.com/photo-1601584115917-0f970f2f0e6b?w=800&h=900&fit=crop']],
            ['page' => 'home', 'section_key' => 'services_header', 'title' => 'Comprehensive Solutions Built for Growth', 'content' => 'Our integrated platform streamlines every aspect of your operations, from logistics to workforce management.', 'sort_order' => 25],
            ['page' => 'home', 'section_key' => 'why_header', 'title' => 'Why Partner With Us?', 'content' => 'We deliver strategic advantages that directly impact your bottom line.', 'sort_order' => 30],
            ['page' => 'home', 'section_key' => 'process_header', 'title' => 'Our Streamlined Process', 'content' => 'From initial consultation to ongoing support, we\'ve optimized every step for maximum efficiency.', 'sort_order' => 40],
            ['page' => 'home', 'section_key' => 'impact_header', 'title' => 'Proven Impact, Measurable Results', 'content' => 'Join hundreds of companies that have transformed their operations with our solutions.', 'sort_order' => 50],
            ['page' => 'home', 'section_key' => 'gallery_header', 'title' => 'Operations in Motion', 'content' => 'Real logistics operations and networks powering supply chains.', 'sort_order' => 70],
            ['page' => 'home', 'section_key' => 'faq_header', 'title' => 'Frequently Asked Questions', 'content' => 'Find answers to common questions about our services and solutions.', 'sort_order' => 80],
            ['page' => 'home', 'section_key' => 'cta', 'title' => 'Ready to Transform Your Operations?', 'content' => 'Partner with us today and experience the difference.', 'sort_order' => 90],
            ['page' => 'about', 'section_key' => 'hero', 'title' => 'About Vyapto', 'content' => 'Leading the future of logistics support, business services & operational excellence.', 'sort_order' => 1],
            ['page' => 'about', 'section_key' => 'overview', 'title' => 'Powering Smarter Logistics & Driving Real Growth', 'content' => 'We go beyond basics by helping our clients manage operations, optimizing processes, and staying ahead in a highly competitive industry.', 'sort_order' => 2],
            ['page' => 'about', 'section_key' => 'who_we_are', 'title' => 'Who We Are', 'content' => 'Vyapto provides logistics support services with structured operations and standardized processes for day-to-day consistency and efficiency.', 'sort_order' => 3],
            ['page' => 'about', 'section_key' => 'why_choose', 'title' => 'Why Choose Vyapto?', 'content' => 'There are several reasons why you should choose us.', 'sort_order' => 20],
            ['page' => 'about', 'section_key' => 'cta', 'title' => "Let's Simplify Logistics Together!", 'content' => 'Partner with us today and develop smarter logistics operations.', 'sort_order' => 90],
            ['page' => 'faq', 'section_key' => 'hero', 'title' => 'Frequently Asked Questions', 'content' => 'Find answers to common questions about our services and solutions.', 'sort_order' => 1],
            ['page' => 'services', 'section_key' => 'hero', 'title' => 'Our Services', 'content' => 'End-to-end logistics and workforce solutions tailored for growing businesses.', 'sort_order' => 1],
            ['page' => 'products', 'section_key' => 'hero', 'title' => 'Our Products', 'content' => 'Innovative platforms and products powering the Vyapto ecosystem.', 'sort_order' => 1],
            ['page' => 'careers', 'section_key' => 'hero', 'title' => 'Careers & Highlights', 'content' => 'Build your career with Vyapto — where logistics meets innovation.', 'sort_order' => 1],
            ['page' => 'blogs', 'section_key' => 'hero', 'title' => 'Blogs', 'content' => 'Insights, updates, and stories from the Vyapto team.', 'sort_order' => 1],
            ['page' => 'contact', 'section_key' => 'hero', 'title' => 'Contact Us', 'content' => 'Have a question or want to work with us? Reach out and our team will get back to you.', 'sort_order' => 1],
            ['page' => 'global', 'section_key' => 'site_logo_desktop', 'title' => 'Desktop Logo', 'sort_order' => 1, 'extra' => ['default_image' => 'images/nav-logo.png']],
            ['page' => 'global', 'section_key' => 'site_logo_mobile', 'title' => 'Mobile Logo', 'sort_order' => 2, 'extra' => ['default_image' => 'images/nav-logo-mobile.png']],
            ['page' => 'global', 'section_key' => 'site_logo_footer', 'title' => 'Footer Logo', 'sort_order' => 3, 'extra' => ['default_image' => 'images/nav-logo.png']],
            ['page' => 'global', 'section_key' => 'footer_tagline', 'title' => 'Footer Tagline', 'content' => 'Professional logistics and workforce solutions for businesses across the globe.', 'sort_order' => 4],
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
            ['title' => '1,000+', 'subtitle' => 'Professionals Supporting Operations'],
            ['title' => '24/7', 'subtitle' => 'Dedicated Business Support'],
            ['title' => '50+', 'subtitle' => 'Operational Specialists'],
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
                'title' => 'Voices from Our Team',
                'content' => 'Hear what our team members say about working with Vyapto.',
                'sort_order' => 60,
                'status' => true,
            ]
        );

        $whyPartners = [
            ['icon' => '⚡', 'title' => 'Industry Expertise', 'content' => 'Years of experience serving the logistics industry with deep operational knowledge and proven best practices.'],
            ['icon' => '🌎', 'title' => 'Nationwide Coverage', 'content' => 'Supporting businesses across all regions with scalable logistics solutions and a strong network.'],
            ['icon' => '✅', 'title' => 'Proven Results', 'content' => 'Trusted by hundreds of companies who have improved efficiency, reduced costs, and scaled operations.'],
        ];
        foreach ($whyPartners as $i => $card) {
            WebsitePageSection::updateOrCreate(
                ['page' => 'home', 'section_key' => 'why_partner_' . ($i + 1)],
                array_merge($card, ['page' => 'home', 'sort_order' => 31 + $i, 'status' => true])
            );
        }

        $processSteps = [
            ['title' => 'Consultation', 'content' => 'We understand your business needs and operational challenges.'],
            ['title' => 'Planning', 'content' => 'Custom strategy tailored to your logistics requirements.'],
            ['title' => 'Implementation', 'content' => 'Seamless deployment with dedicated support teams.'],
            ['title' => 'Ongoing Support', 'content' => '24/7 assistance to ensure continuity and growth.'],
        ];
        foreach ($processSteps as $i => $step) {
            WebsitePageSection::updateOrCreate(
                ['page' => 'home', 'section_key' => 'process_step_' . ($i + 1)],
                array_merge($step, ['page' => 'home', 'sort_order' => 41 + $i, 'status' => true])
            );
        }

        $impacts = [
            ['title' => '500+', 'subtitle' => 'Companies Served'],
            ['title' => '24/7', 'subtitle' => 'Business Support'],
            ['title' => '50+', 'subtitle' => 'Specialists'],
            ['title' => '99%', 'subtitle' => 'Client Satisfaction'],
        ];
        foreach ($impacts as $i => $impact) {
            WebsitePageSection::updateOrCreate(
                ['page' => 'home', 'section_key' => 'impact_' . ($i + 1)],
                array_merge($impact, ['page' => 'home', 'sort_order' => 51 + $i, 'status' => true])
            );
        }

        $milestones = [
            ['subtitle' => '2021', 'title' => 'The Foundation', 'content' => 'Vyapto started with a skilled team focused on providing reliable logistics support services.'],
            ['subtitle' => '2022', 'title' => 'Establishing The Brand', 'content' => 'Operations were unified under Vyapto, solidifying and launching the brand.'],
            ['subtitle' => '2023', 'title' => 'Expanding Reach', 'content' => 'Our team grew and we opened more offices, enabling 24x7 support to clients.'],
            ['subtitle' => '2025', 'title' => 'Strengthening Capabilities', 'content' => 'We added integrated logistics support services and upgraded customer success systems.'],
        ];
        foreach ($milestones as $i => $m) {
            WebsitePageSection::updateOrCreate(
                ['page' => 'about', 'section_key' => 'milestone_' . ($i + 1)],
                array_merge($m, ['page' => 'about', 'sort_order' => 10 + $i, 'status' => true])
            );
        }

        $locations = [
            ['title' => 'Mohali', 'subtitle' => 'Head Office'],
            ['title' => 'Gurgaon', 'subtitle' => 'Sub-Branch'],
            ['title' => 'Noida', 'subtitle' => 'Sub-Branch'],
            ['title' => 'Delhi', 'subtitle' => 'Sub-Branch'],
        ];
        foreach ($locations as $i => $loc) {
            WebsitePageSection::updateOrCreate(
                ['page' => 'about', 'section_key' => 'location_' . ($i + 1)],
                array_merge($loc, ['page' => 'about', 'sort_order' => 15 + $i, 'status' => true])
            );
        }

        $testimonials = [
            ['content' => 'Vyapto is a great place to work, known for its positive work culture. They truly appreciate their employees.', 'title' => 'Ayesha Amaan', 'subtitle' => 'Customer Success Team'],
            ['content' => 'The platform makes daily operations so much easier. Everything is in one place.', 'title' => 'Rohit Sharma', 'subtitle' => 'Operations Manager'],
            ['content' => 'Excellent support team and reliable systems. Highly recommended!', 'title' => 'Vivek Singh', 'subtitle' => 'Delivery Partner'],
        ];

        foreach ($testimonials as $i => $testimonial) {
            WebsitePageSection::updateOrCreate(
                ['page' => 'home', 'section_key' => 'testimonial_' . ($i + 1)],
                [
                    'page' => 'home',
                    'title' => $testimonial['title'],
                    'subtitle' => $testimonial['subtitle'],
                    'content' => $testimonial['content'],
                    'sort_order' => 61 + $i,
                    'status' => true,
                ]
            );
        }

        $galleryImages = [
            'https://images.unsplash.com/photo-1566576912321-d58ddd7a6088?w=560&h=400&fit=crop',
            'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=560&h=400&fit=crop',
            'https://images.unsplash.com/photo-1494414623144-080708c2043b?w=560&h=400&fit=crop',
            'https://images.unsplash.com/photo-1519003722464-d8e2f013f3cb?w=560&h=400&fit=crop',
            'https://images.unsplash.com/photo-1601584115917-0f970f2f0e6b?w=560&h=400&fit=crop',
            'https://images.unsplash.com/photo-1580674685258-234b35eb6d6d?w=560&h=400&fit=crop',
            'https://images.unsplash.com/photo-1513828583688-c52646db42ef?w=560&h=400&fit=crop',
            'https://images.unsplash.com/photo-1544626977-9e4c4d0d0c0c?w=560&h=400&fit=crop',
        ];
        foreach ($galleryImages as $i => $url) {
            WebsitePageSection::updateOrCreate(
                ['page' => 'home', 'section_key' => 'gallery_' . ($i + 1)],
                [
                    'page' => 'home',
                    'title' => 'Gallery Image ' . ($i + 1),
                    'sort_order' => 71 + $i,
                    'status' => true,
                    'extra' => ['default_image' => $url],
                ]
            );
        }

        WebsitePageSection::updateOrCreate(
            ['page' => 'about', 'section_key' => 'overview'],
            [
                'page' => 'about',
                'title' => 'Powering Smarter Logistics & Driving Real Growth',
                'content' => 'We go beyond basics by helping our clients manage operations, optimizing processes, and staying ahead in a highly competitive industry.',
                'sort_order' => 2,
                'status' => true,
                'extra' => ['default_image' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=600&h=400&fit=crop'],
            ]
        );
    }

    private function seedServices(): void
    {
        $services = [
            ['title' => 'Freight Brokerage Solutions', 'slug' => 'freight-brokerage', 'description' => 'Load matching with our extensive carrier network. Connect operations seamlessly.', 'icon' => 'fa-truck', 'sort_order' => 1],
            ['title' => 'Accounting Services', 'slug' => 'accounting-services', 'description' => 'Bookkeeping, tax prep, and compliance designed specifically for logistics businesses.', 'icon' => 'fa-calculator', 'sort_order' => 2],
            ['title' => 'IT & Administration Support', 'slug' => 'it-administration', 'description' => 'Modern operational systems and technical support to streamline your operations.', 'icon' => 'fa-laptop', 'sort_order' => 3],
            ['title' => 'HR & Payroll Management', 'slug' => 'hr-payroll', 'description' => 'Payroll processing, employee onboarding, and compliance management.', 'icon' => 'fa-users', 'sort_order' => 4],
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

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
            ['page' => 'home', 'section_key' => 'hero_image', 'title' => 'Freight & Logistics Solutions', 'sort_order' => 4, 'extra' => ['default_image' => 'images/6slider.avif']],
            ['page' => 'home', 'section_key' => 'services_header', 'title' => 'Comprehensive Solutions Built for Growth', 'content' => 'Our integrated platform streamlines every aspect of your operations, from logistics to workforce management.', 'sort_order' => 25],
            ['page' => 'home', 'section_key' => 'why_header', 'title' => 'Why Partner With Us?', 'content' => 'We deliver strategic advantages that directly impact your bottom line.', 'sort_order' => 30],
            ['page' => 'home', 'section_key' => 'process_header', 'title' => 'Our Streamlined Process', 'content' => 'From initial consultation to ongoing support, we\'ve optimized every step for maximum efficiency.', 'sort_order' => 40],
            ['page' => 'home', 'section_key' => 'impact_header', 'title' => 'Proven Impact, Measurable Results', 'content' => 'Join hundreds of companies that have transformed their operations with our solutions.', 'sort_order' => 50],
            ['page' => 'home', 'section_key' => 'gallery_header', 'title' => 'Operations in Motion', 'content' => 'Real logistics operations and networks powering supply chains.', 'sort_order' => 70],
            ['page' => 'home', 'section_key' => 'faq_header', 'title' => 'Frequently Asked Questions', 'content' => 'Find answers to common questions about our services and solutions.', 'sort_order' => 80],
            ['page' => 'home', 'section_key' => 'cta', 'title' => 'Ready to Transform Your Operations?', 'content' => 'Partner with us today and experience the difference.', 'sort_order' => 90],
            ['page' => 'about', 'section_key' => 'hero', 'title' => "Every load has\nan origin.", 'subtitle' => 'ABOUT VYAPTO', 'content' => 'Vyapto Commerce Pvt. Ltd. is a fast-growing company based in Bihar, India, moving goods, people and opportunity across the country — one consignment at a time.', 'sort_order' => 1],
            ['page' => 'faq', 'section_key' => 'hero', 'title' => 'Frequently Asked Questions', 'content' => 'Find answers to common questions about our services and solutions.', 'sort_order' => 1],
            ['page' => 'services', 'section_key' => 'hero', 'title' => 'Solving real challenges.', 'subtitle' => 'Delivering real results.', 'content' => 'We understand the challenges businesses face every day. Vyapto is built to solve them with efficient solutions, transparency, and an unwavering commitment to quality service.', 'sort_order' => 1, 'extra' => ['default_image' => 'images/vyapto-warehouse-bg.png']],
            ['page' => 'products', 'section_key' => 'hero', 'title' => 'Our Products', 'content' => "Built on the trust we've earned through our services — now bringing quality directly to you.", 'sort_order' => 1, 'extra' => ['default_image' => 'images/vyapto-warehouse-bg.png']],
            ['page' => 'careers', 'section_key' => 'hero', 'title' => "Ride with a\ncompany that moves.", 'subtitle' => 'Crew Manifest · Now Boarding', 'content' => "150+ people strong and growing — on the road, in the hubs, behind the CRM, and on the franchise floor. If you like solving real problems for real businesses, there's a seat for you.", 'sort_order' => 1, 'extra' => [
                'default_image' => 'images/vyapto-warehouse-bg.png',
                'meta' => [
                    ['label' => 'Open Categories', 'value' => '6'],
                    ['label' => 'Locations', 'value' => 'Bihar + Pan-India'],
                    ['label' => 'Team Size', 'value' => '150+'],
                ],
            ]],
            ['page' => 'careers', 'section_key' => 'culture', 'title' => "Work that shows up\non the road, not just a slide.", 'content' => "You'll see the outcome of your work the same day — a route run on time, a client onboarded, a hub cleared before the evening rush.", 'sort_order' => 10, 'extra' => [
                'num' => 'Culture',
                'category' => 'Why Vyapto',
                'default_image' => 'images/3slider.avif',
            ]],
            ['page' => 'careers', 'section_key' => 'value_1', 'title' => 'Integrity', 'content' => 'Honest, transparent, ethical, always.', 'icon' => 'fa-shield-halved', 'sort_order' => 11],
            ['page' => 'careers', 'section_key' => 'value_2', 'title' => 'Efficiency', 'content' => 'We optimize, not just work harder.', 'icon' => 'fa-gears', 'sort_order' => 12],
            ['page' => 'careers', 'section_key' => 'value_3', 'title' => 'Empowerment', 'content' => 'Opportunity for people and partners.', 'icon' => 'fa-handshake', 'sort_order' => 13],
            ['page' => 'careers', 'section_key' => 'value_4', 'title' => 'Innovation', 'content' => 'Smarter tools, smarter routes.', 'icon' => 'fa-lightbulb', 'sort_order' => 14],
            ['page' => 'careers', 'section_key' => 'value_5', 'title' => 'Commitment', 'content' => 'Excellence in every service.', 'icon' => 'fa-bullseye', 'sort_order' => 15],
            ['page' => 'careers', 'section_key' => 'roles_header', 'title' => "Where you could\njoin the route.", 'content' => "Hiring across every vertical we operate — reach out even if your exact role isn't listed.", 'sort_order' => 20, 'extra' => ['num' => 'Positions', 'category' => 'Open Categories']],
            ['page' => 'careers', 'section_key' => 'apply', 'title' => "Send us your\ndetails. We'll route it.", 'content' => 'Drop your details below and our workforce team will match you to the right category — no long forms, no waiting for a portal login.', 'sort_order' => 30, 'extra' => ['num' => 'Join', 'category' => 'Application Note']],
            ['page' => 'blogs', 'section_key' => 'hero', 'title' => 'Stories that move with us', 'content' => 'Insights, updates, and stories from the Vyapto team — logistics, workforce, and the road ahead.', 'sort_order' => 1, 'extra' => ['default_image' => 'images/4slider.avif']],
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
            ['title' => 'Freight Brokerage Solutions', 'content' => 'Connect with our extensive carrier network to find the perfect load matching solution. We streamline load matching to maximize your efficiency.'],
            ['title' => 'US Accounting Services for Trucking Companies', 'content' => 'Comprehensive bookkeeping, tax preparation, and financial reporting designed specifically for the trucking industry.'],
            ['title' => 'IT & Administration Support', 'content' => 'Modern operational systems, CRM management, and technical support to keep your operations running smoothly.'],
            ['title' => 'HR & Payroll Management for Logistics Firms', 'content' => 'Streamlined payroll processing, employee onboarding, and DOT compliance management for your workforce.'],
        ];
        foreach ($processSteps as $i => $step) {
            WebsitePageSection::updateOrCreate(
                ['page' => 'home', 'section_key' => 'process_step_' . ($i + 1)],
                array_merge($step, ['page' => 'home', 'sort_order' => 41 + $i, 'status' => true])
            );
        }

        $processImages = [
            'images/4slider.avif',
            'images/5slider.avif',
            'images/3slider.avif',
        ];
        foreach ($processImages as $i => $img) {
            WebsitePageSection::updateOrCreate(
                ['page' => 'home', 'section_key' => 'process_image_' . ($i + 1)],
                [
                    'page' => 'home',
                    'title' => 'Process Image ' . ($i + 1),
                    'sort_order' => 40 + $i,
                    'status' => true,
                    'extra' => ['default_image' => $img],
                ]
            );
        }

        $heroSlides = [
            'images/4slider.avif',
            'images/5slider.avif',
            'images/3slider.avif',
        ];
        foreach ($heroSlides as $i => $img) {
            WebsitePageSection::updateOrCreate(
                ['page' => 'home', 'section_key' => 'hero_slide_' . ($i + 1)],
                [
                    'page' => 'home',
                    'title' => 'Hero Slide ' . ($i + 1),
                    'sort_order' => 5 + $i,
                    'status' => true,
                    'extra' => ['default_image' => $img],
                ]
            );
        }

        $heroMiniStats = [
            ['title' => '1,000+', 'subtitle' => 'Professionals Supporting Operations'],
            ['title' => '24/7', 'subtitle' => 'Dedicated Business Support'],
            ['title' => '50+', 'subtitle' => 'Operational Specialists Across Departments'],
        ];
        foreach ($heroMiniStats as $i => $stat) {
            WebsitePageSection::updateOrCreate(
                ['page' => 'home', 'section_key' => 'stat_' . ($i + 1)],
                array_merge($stat, ['page' => 'home', 'sort_order' => 20 + $i, 'status' => true])
            );
        }

        $galleryImages = [
            'images/1slider.avif',
            'images/2slider.avif',
            'images/6slider.avif',
            'images/7slider.avif',
            'images/8slider.avif',
            'images/9slider.avif',
            'images/3slider.avif',
            'images/4slider.avif',
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

        $testimonials = [
            ['content' => 'Vyapto is a great place to work, known for its positive work culture. They truly appreciate their employees.', 'title' => 'Ayesha Amaan', 'subtitle' => 'Customer Success Team'],
            ['content' => 'The platform makes daily operations so much easier. Everything is in one place.', 'title' => 'Rohit Sharma', 'subtitle' => 'Operations Manager'],
            ['content' => 'Excellent support team and reliable systems. Highly recommended!', 'title' => 'Vivek Singh', 'subtitle' => 'Delivery Partner'],
        ];
        foreach ($testimonials as $i => $testimonial) {
            WebsitePageSection::updateOrCreate(
                ['page' => 'home', 'section_key' => 'testimonial_' . ($i + 1)],
                array_merge($testimonial, ['page' => 'home', 'sort_order' => 61 + $i, 'status' => true])
            );
        }

        $this->seedAboutPage();
    }

    private function seedAboutPage(): void
    {
        WebsitePageSection::updateOrCreate(
            ['page' => 'about', 'section_key' => 'hero_bg'],
            ['page' => 'about', 'title' => 'Hero Background', 'sort_order' => 2, 'status' => true, 'extra' => ['default_image' => 'https://images.unsplash.com/photo-1601584115917-0f970f2f0e6b?auto=format&fit=crop&w=1600&q=80']]
        );

        $metas = [
            ['title' => 'ORIGIN', 'subtitle' => 'Bihar, India'],
            ['title' => 'ROUTE', 'subtitle' => 'Pan-India'],
            ['title' => 'VERTICALS', 'subtitle' => '04'],
            ['title' => 'STATUS', 'subtitle' => 'In Transit — Growing'],
        ];
        foreach ($metas as $i => $meta) {
            WebsitePageSection::updateOrCreate(
                ['page' => 'about', 'section_key' => 'hero_meta_' . ($i + 1)],
                array_merge($meta, ['page' => 'about', 'sort_order' => 3 + $i, 'status' => true])
            );
        }

        WebsitePageSection::updateOrCreate(
            ['page' => 'about', 'section_key' => 'story_header'],
            [
                'page' => 'about',
                'title' => "Built on the road,\nnot in a boardroom.",
                'subtitle' => 'OUR COMPANY STORY',
                'sort_order' => 10,
                'status' => true,
            ]
        );

        WebsitePageSection::updateOrCreate(
            ['page' => 'about', 'section_key' => 'story'],
            [
                'page' => 'about',
                'title' => 'Company Story',
                'content' => "Vyapto started with a simple observation: businesses across India were losing time and money to unreliable transport, unverified manpower, and disconnected operations.\nWhat began as a transportation and logistics operation in Bihar has grown into an integrated services company — spanning Transportation & Logistics, Manpower Solutions, Franchise Operations and Consumer Products. Every vertical we've added exists because a partner asked us to solve one more problem for them.\nToday, that same approach — solve the problem in front of us, do it with integrity, and stay close to the ground — still runs every route we plan and every hire we place.",
                'sort_order' => 11,
                'status' => true,
            ]
        );

        WebsitePageSection::updateOrCreate(
            ['page' => 'about', 'section_key' => 'story_image'],
            [
                'page' => 'about',
                'title' => 'Distribution Hub',
                'sort_order' => 12,
                'status' => true,
                'extra' => ['default_image' => 'https://images.unsplash.com/photo-1566576912321-d58ddd7a6088?auto=format&fit=crop&w=800&q=85'],
            ]
        );

        $stats = [
            ['title' => '100+', 'subtitle' => 'Business Clients Served'],
            ['title' => '1500+', 'subtitle' => 'Deliveries Daily'],
            ['title' => '150+', 'subtitle' => 'Workforce Managed'],
            ['title' => '200+', 'subtitle' => 'Pincodes Covered'],
        ];
        foreach ($stats as $i => $stat) {
            WebsitePageSection::updateOrCreate(
                ['page' => 'about', 'section_key' => 'about_stat_' . ($i + 1)],
                array_merge($stat, ['page' => 'about', 'sort_order' => 20 + $i, 'status' => true])
            );
        }

        WebsitePageSection::updateOrCreate(
            ['page' => 'about', 'section_key' => 'vision_header'],
            ['page' => 'about', 'title' => 'Our Vision', 'subtitle' => 'OUR VISION', 'sort_order' => 30, 'status' => true]
        );

        WebsitePageSection::updateOrCreate(
            ['page' => 'about', 'section_key' => 'vision_quote'],
            [
                'page' => 'about',
                'title' => 'Vision Quote',
                'content' => 'To become a trusted growth partner for businesses by delivering dependable logistics, workforce, franchise, and distribution solutions that create sustainable value.',
                'sort_order' => 31,
                'status' => true,
            ]
        );

        $visionPoints = [
            ['title' => 'Innovate & Excel', 'content' => 'Constantly improving our services through technology, optimization, and on-time execution.'],
            ['title' => 'Empower & Impact', 'content' => 'Enabling businesses to scale and creating long-term positive economic impact.'],
        ];
        foreach ($visionPoints as $i => $point) {
            WebsitePageSection::updateOrCreate(
                ['page' => 'about', 'section_key' => 'vision_point_' . ($i + 1)],
                array_merge($point, ['page' => 'about', 'sort_order' => 32 + $i, 'status' => true])
            );
        }

        WebsitePageSection::updateOrCreate(
            ['page' => 'about', 'section_key' => 'vision_image'],
            [
                'page' => 'about',
                'title' => 'Vision Photo',
                'sort_order' => 34,
                'status' => true,
                'extra' => ['default_image' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?auto=format&fit=crop&w=800&q=85'],
            ]
        );

        WebsitePageSection::updateOrCreate(
            ['page' => 'about', 'section_key' => 'why_header'],
            [
                'page' => 'about',
                'title' => "We solve the problem\nbefore it costs you a client.",
                'subtitle' => 'Operational Strength',
                'content' => 'Every challenge businesses face has a Vyapto answer built directly against it.',
                'sort_order' => 40,
                'status' => true,
            ]
        );

        $issues = [
            ['title' => 'Delayed deliveries', 'content' => 'Unreliable transport partners and poor route planning.', 'subtitle' => 'Reliable logistics network with real-time tracking.'],
            ['title' => 'Workforce shortages', 'content' => 'Skilled, dependable manpower is hard to find fast.', 'subtitle' => 'Ready-to-deploy, verified workforce pool.'],
            ['title' => 'High operational costs', 'content' => 'Inefficient processes inflate business expenses.', 'subtitle' => 'Smart planning, transparent cost-efficient pricing.'],
            ['title' => 'Inconsistent supply', 'content' => 'Irregular supply chains hurt customer trust.', 'subtitle' => 'Quality-assured, hygiene-checked distribution.'],
        ];
        foreach ($issues as $i => $issue) {
            WebsitePageSection::updateOrCreate(
                ['page' => 'about', 'section_key' => 'issue_' . ($i + 1)],
                array_merge($issue, ['page' => 'about', 'sort_order' => 41 + $i, 'status' => true])
            );
        }

        WebsitePageSection::updateOrCreate(
            ['page' => 'about', 'section_key' => 'advantage_header'],
            ['page' => 'about', 'title' => 'THE VYAPTO ADVANTAGE', 'sort_order' => 50, 'status' => true]
        );

        $advantages = [
            ['title' => 'Integrated Solutions', 'content' => 'One-stop solutions across multiple business needs.', 'icon' => 'fa-layer-group'],
            ['title' => 'Pan-India Network', 'content' => 'Strong presence and operations across India.', 'icon' => 'fa-map'],
            ['title' => 'Experienced Team', 'content' => 'Skilled professionals with domain expertise.', 'icon' => 'fa-users'],
            ['title' => 'Quality Assured', 'content' => 'Consistent, high standards in everything we do.', 'icon' => 'fa-clipboard-check'],
            ['title' => 'Ethical & Transparent', 'content' => 'Complete transparency, always.', 'icon' => 'fa-shield-halved'],
            ['title' => 'Growth-Oriented', 'content' => 'Reducing cost, increasing profitability, together.', 'icon' => 'fa-chart-line'],
        ];
        foreach ($advantages as $i => $adv) {
            WebsitePageSection::updateOrCreate(
                ['page' => 'about', 'section_key' => 'advantage_' . ($i + 1)],
                array_merge($adv, ['page' => 'about', 'sort_order' => 51 + $i, 'status' => true])
            );
        }

        WebsitePageSection::updateOrCreate(
            ['page' => 'about', 'section_key' => 'team_header'],
            [
                'page' => 'about',
                'title' => "The people behind\nevery delivery.",
                'subtitle' => 'Our Force',
                'content' => '150+ trained professionals — drivers, dispatchers, franchise managers and support staff — working as one crew across Bihar and beyond.',
                'sort_order' => 60,
                'status' => true,
            ]
        );

        $team = [
            [
                'title' => 'Rahul Sharma',
                'subtitle' => 'Leadership',
                'content' => 'Head of Operations — oversees logistics, route planning and hub management across all Vyapto locations.',
                'icon' => 'orange',
                'extra' => ['default_image' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=400&q=80'],
            ],
            [
                'title' => 'Priya Verma',
                'subtitle' => 'Workforce',
                'content' => 'Workforce Manager — sources, verifies and deploys skilled and semi-skilled personnel for clients across the country.',
                'icon' => 'orange',
                'extra' => ['default_image' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=400&q=80'],
            ],
            [
                'title' => 'Amit Singh',
                'subtitle' => 'Franchise',
                'content' => 'Franchise Director — builds and grows Vyapto\'s franchise partner network from onboarding to operational excellence.',
                'icon' => 'blue',
                'extra' => ['default_image' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=400&q=80'],
            ],
            [
                'title' => 'Neha Jha',
                'subtitle' => 'Finance',
                'content' => 'Finance & Compliance — manages bookkeeping, tax compliance and financial controls for all Vyapto verticals.',
                'icon' => 'blue',
                'extra' => ['default_image' => 'https://images.unsplash.com/photo-1607746882042-944635dfe10e?auto=format&fit=crop&w=400&q=80'],
            ],
        ];
        foreach ($team as $i => $member) {
            WebsitePageSection::updateOrCreate(
                ['page' => 'about', 'section_key' => 'team_' . ($i + 1)],
                array_merge($member, ['page' => 'about', 'sort_order' => 61 + $i, 'status' => true])
            );
        }

        WebsitePageSection::updateOrCreate(
            ['page' => 'about', 'section_key' => 'team_cta'],
            [
                'page' => 'about',
                'title' => 'Join the crew',
                'link' => '/careers',
                'sort_order' => 70,
                'status' => true,
            ]
        );
    }

    private function seedServices(): void
    {
        $services = [
            [
                'title' => 'Freight Brokerage Solutions',
                'slug' => 'freight-brokerage',
                'category' => 'Logistics Network',
                'subtitle' => 'Reliable Logistics Network',
                'description' => 'Connect with our extensive carrier network to find the perfect load-matching solution for your business. We streamline load matching and dispatch to maximize your efficiency, using optimized routes, real-time tracking, and an extensive pan-India network to ensure on-time delivery — every time.',
                'icon' => 'fa-truck',
                'image' => 'images/6slider.avif',
                'sort_order' => 1,
                'features' => [
                    'Load matching and carrier sourcing',
                    'Dispatch coordination',
                    'Route optimization and real-time tracking',
                    'Pan-India carrier network access',
                ],
            ],
            [
                'title' => 'Accounting Services',
                'slug' => 'accounting-services',
                'category' => 'Finance Support',
                'subtitle' => 'Logistics-Ready Accounting',
                'description' => 'Bookkeeping, tax preparation, and compliance designed specifically for logistics businesses. Stay audit-ready with clear reporting, accurate ledgers, and processes built around freight and fleet operations.',
                'icon' => 'fa-calculator',
                'image' => 'images/7slider.avif',
                'sort_order' => 2,
                'features' => [
                    'Bookkeeping and ledger management',
                    'Tax preparation and filings',
                    'Compliance and audit support',
                    'Financial reporting for logistics',
                ],
            ],
            [
                'title' => 'IT & Administration Support',
                'slug' => 'it-administration',
                'category' => 'Technology',
                'subtitle' => 'Modern Operational Systems',
                'description' => 'Modern operational systems and technical support to streamline your day-to-day work. From tools and access control to process automation, we keep your teams connected and productive.',
                'icon' => 'fa-laptop',
                'image' => 'images/8slider.avif',
                'sort_order' => 3,
                'features' => [
                    'Systems setup and administration',
                    'Technical support and troubleshooting',
                    'Process automation tools',
                    'Secure access and documentation',
                ],
            ],
            [
                'title' => 'HR & Payroll Management',
                'slug' => 'hr-payroll',
                'category' => 'People Operations',
                'subtitle' => 'Workforce Management',
                'description' => 'Payroll processing, employee onboarding, and compliance management so your people operations stay accurate, timely, and ready to scale with your logistics business.',
                'icon' => 'fa-users',
                'image' => 'images/9slider.avif',
                'sort_order' => 4,
                'features' => [
                    'Payroll processing',
                    'Employee onboarding',
                    'Attendance and compliance',
                    'HR documentation support',
                ],
            ],
        ];

        foreach ($services as $service) {
            WebsiteService::updateOrCreate(
                ['slug' => $service['slug']],
                array_merge($service, ['status' => true])
            );
        }
    }

    private function seedProducts(): void
    {
        $products = [
            [
                'title' => 'Vyapto Foods',
                'slug' => 'vyapto-foods',
                'category' => 'Vyapto Foods',
                'subtitle' => 'Authentic. Pure. Wholesome. Made for Every Home.',
                'description' => "Building on the trust we've earned through our logistics and business services, Vyapto Foods brings you authentic, high-quality, value-for-money makhana (fox nuts) for everyday snacking. Every pack is roasted, light, crunchy, and made with love in Bihar.",
                'icon' => 'fa-basket-shopping',
                'image' => 'images/6slider.avif',
                'link' => 'https://vyaptofoods.com/',
                'sort_order' => 1,
                'features' => [
                    '100% Natural — No artificial additives',
                    'High in Protein — Perfect for healthy snacking',
                    'Light & Crunchy — Roasted, not fried',
                    'Gluten Free — Safe and healthy',
                    'Proudly from Bihar — Harvested with care',
                ],
                'extra' => [
                    'gallery_header' => [
                        'num' => 'Makhana',
                        'category' => 'Our First Range',
                        'title' => 'Six flavors, one honest snack.',
                        'subtitle' => 'Roasted · Light · Nutritious — Made with love in Bihar.',
                    ],
                    'gallery' => [
                        [
                            'image' => 'images/1slider.avif',
                            'meta' => 'Vyapto Foods · Jar Range',
                            'title' => 'Makhana Premium Jars',
                            'desc' => 'Same 6 honest flavors — now in resealable premium jars for freshness and convenience.',
                        ],
                        [
                            'image' => 'images/2slider.avif',
                            'meta' => 'Vyapto Foods · Pouch Range',
                            'title' => 'Makhana Flavour Range',
                            'desc' => '6 bold flavors — Pudina · Cheese · Peri Peri · Cream & Onion · Barbeque · Pink Salt',
                        ],
                    ],
                    'badges' => [
                        ['icon' => 'fa-leaf', 'label' => '100% Natural'],
                        ['icon' => 'fa-bolt', 'label' => 'High in Protein'],
                        ['icon' => 'fa-star', 'label' => 'Light & Crunchy'],
                        ['icon' => 'fa-shield-halved', 'label' => 'Gluten Free'],
                        ['icon' => 'fa-location-dot', 'label' => 'Proudly from Bihar'],
                    ],
                    'vision_header' => [
                        'num' => 'Vision',
                        'category' => 'Purity & Impact',
                        'title' => 'Our Wider Vision',
                        'subtitle' => 'To create a brand that stands for purity, trust, and wellness — while empowering local communities and contributing to a healthier tomorrow.',
                    ],
                    'vision_pillars' => [
                        ['icon' => 'fa-certificate', 'title' => 'Authentic Quality', 'desc' => 'Carefully sourced ingredients with strict quality checks.'],
                        ['icon' => 'fa-heart', 'title' => 'Health & Wellness', 'desc' => 'Nutritious, wholesome products for a better lifestyle.'],
                        ['icon' => 'fa-handshake', 'title' => 'Trust & Transparency', 'desc' => 'Honest processes and clear commitment to our customers.'],
                        ['icon' => 'fa-users', 'title' => 'Community Impact', 'desc' => 'Supporting local farmers and creating sustainable opportunities.'],
                        ['icon' => 'fa-chart-line', 'title' => 'Sustainable Growth', 'desc' => 'Building a future-ready brand that grows with responsibility.'],
                    ],
                    'expectation' => 'A wide range of quality consumer products, affordable pricing with premium quality, and consistent availability with timely delivery.',
                ],
            ],
            [
                'title' => 'Vyapto VMS',
                'slug' => 'vyapto-vms',
                'category' => 'Vyapto VMS',
                'subtitle' => 'Smart. Transparent. Connected.',
                'description' => 'Vyapto VMS (Vendor Management System) simplifies vendor onboarding, compliance, performance tracking, and operational visibility through a single powerful digital platform — built for businesses that manage large vendor and workforce networks.',
                'icon' => 'fa-laptop-code',
                'image' => 'images/app-screen-1.png',
                'sort_order' => 2,
                'features' => [],
                'extra' => [
                    'chips' => [
                        'Logistics companies',
                        'Staffing agencies',
                        'Manufacturing units',
                        'E-commerce operations',
                        'Multi-location businesses',
                    ],
                    'vision_label' => 'Our Vision:',
                    'vision_title' => 'One Platform. Complete Control.',
                    'vision_text' => 'Building a smarter ecosystem where businesses, vendors, and operations work together seamlessly through technology.',
                    'capabilities_header' => [
                        'num' => 'Capabilities',
                        'category' => 'System Architecture',
                        'title' => 'Core Benefits & Key Capabilities',
                    ],
                    'capabilities' => [
                        ['icon' => 'fa-user-plus', 'title' => 'Simplify Vendor Onboarding', 'desc' => 'Quick, centralized registration and system onboarding for vendors.'],
                        ['icon' => 'fa-file-circle-check', 'title' => 'Ensure Compliance', 'desc' => 'Digital document management, secure archival, and automatic verification flows.'],
                        ['icon' => 'fa-chart-column', 'title' => 'Track Performance', 'desc' => 'Live scorecards, SLA monitoring, and structured KPI analytics.'],
                        ['icon' => 'fa-eye', 'title' => 'Real-Time Visibility', 'desc' => 'Actionable insights, analytics dashboards, and interactive reporting panels.'],
                        ['icon' => 'fa-bell', 'title' => 'Smart Notifications', 'desc' => 'Real-time alerts, email warnings, and automated milestone updates.'],
                        ['icon' => 'fa-diagram-project', 'title' => 'Operations Management', 'desc' => 'Seamless workflow automation connecting operational units directly.'],
                    ],
                ],
            ],
        ];

        foreach ($products as $product) {
            WebsiteProduct::updateOrCreate(
                ['slug' => $product['slug']],
                array_merge($product, ['status' => true])
            );
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

        $openings = [
            [
                'slug' => 'logistics-fleet-operations',
                'title' => 'Logistics & Fleet Operations',
                'excerpt' => 'Drivers, dispatchers, route planners',
                'location' => 'Pan-India',
                'department' => 'Full-time',
                'sort_order' => 1,
            ],
            [
                'slug' => 'warehouse-hub-management',
                'title' => 'Warehouse & Hub Management',
                'excerpt' => 'Hub supervisors, sorting & inventory crew',
                'location' => '9+ Hubs',
                'department' => 'Full-time',
                'sort_order' => 2,
            ],
            [
                'slug' => 'last-mile-delivery-partners',
                'title' => 'Last-Mile Delivery Partners',
                'excerpt' => 'Riders and delivery executives',
                'location' => '2,000+ Pincodes',
                'department' => 'Full & Part-time',
                'sort_order' => 3,
            ],
            [
                'slug' => 'manpower-workforce-deployment',
                'title' => 'Manpower & Workforce Deployment',
                'excerpt' => 'Recruiters, verification & onboarding staff',
                'location' => 'Bihar HQ',
                'department' => 'Full-time',
                'sort_order' => 4,
            ],
            [
                'slug' => 'accounting-compliance',
                'title' => 'Accounting & Compliance',
                'excerpt' => 'Bookkeeping, tax and IFTA compliance specialists',
                'location' => 'Remote-friendly',
                'department' => 'Full-time',
                'sort_order' => 5,
            ],
            [
                'slug' => 'it-crm-franchise-support',
                'title' => 'IT, CRM & Franchise Support',
                'excerpt' => 'Helpdesk, systems admin, franchise onboarding leads',
                'location' => 'Bihar HQ',
                'department' => 'Full-time',
                'sort_order' => 6,
            ],
        ];

        foreach ($openings as $job) {
            WebsiteCareerItem::updateOrCreate(
                ['slug' => $job['slug']],
                array_merge($job, [
                    'category' => WebsiteCareerItem::CATEGORY_JOB_OPENING,
                    'content' => '<p>'.$job['excerpt'].'</p>',
                    'status' => true,
                ])
            );
        }

        // Keep older sample opening inactive so the new categories list stays clean.
        WebsiteCareerItem::where('slug', 'delivery-associate-delhi')->update(['status' => false]);

        WebsiteCareerItem::updateOrCreate(
            ['slug' => 'join-delivery-partner'],
            [
                'category' => WebsiteCareerItem::CATEGORY_DELIVERY_PARTNER,
                'title' => 'Join as Delivery Partner',
                'excerpt' => 'Flexible earnings, your schedule, our support.',
                'content' => '<p>Become a Vyapto delivery partner and earn on your own terms.</p>',
                'link' => '/portal/register',
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
                'content' => '<p>Vyapto is proud to announce expansion into three new metropolitan areas.</p>',
                'published_at' => now()->subDays(7),
                'sort_order' => 1,
                'status' => true,
            ]
        );
    }

    private function seedBlogs(): void
    {
        $posts = [
            [
                'slug' => 'future-of-last-mile-delivery',
                'title' => 'The Future of Last-Mile Delivery',
                'excerpt' => 'How technology is reshaping the final leg of logistics — and what it means for Indian supply chains.',
                'content' => '<p>Last-mile delivery remains the most critical and costly segment of the supply chain. At Vyapto, we are investing in AI-driven route optimization, real-time tracking, and workforce management to make deliveries faster and more reliable.</p><p>From hub sequencing to rider allocation, every decision on the road compounds into customer trust. The winners will be teams that treat data as an operating system — not a report.</p>',
                'author' => 'Vyapto Team',
                'image' => 'images/7slider.avif',
                'published_at' => now()->subDays(14),
                'sort_order' => 1,
            ],
            [
                'slug' => 'building-reliable-hub-networks',
                'title' => 'Building Reliable Hub Networks',
                'excerpt' => 'Why consistent hub operations beat one-off speed — and how we design for evening peaks.',
                'content' => '<p>A strong hub network is the backbone of on-time delivery. Clear sorting rules, trained crews, and predictable cut-off times keep packages moving even when volumes spike.</p>',
                'author' => 'Operations',
                'image' => 'images/8slider.avif',
                'published_at' => now()->subDays(10),
                'sort_order' => 2,
            ],
            [
                'slug' => 'workforce-that-shows-up',
                'title' => 'Workforce That Shows Up',
                'excerpt' => 'How structured manpower deployment keeps logistics moving when demand flexes overnight.',
                'content' => '<p>Logistics is a people business. Verification, onboarding, and deployment workflows are what turn headcount into reliable capacity on the floor and on the road.</p>',
                'author' => 'People Ops',
                'image' => 'images/3slider.avif',
                'published_at' => now()->subDays(7),
                'sort_order' => 3,
            ],
            [
                'slug' => 'why-visibility-wins-freight',
                'title' => 'Why Visibility Wins in Freight',
                'excerpt' => 'Transparent tracking and clear SLAs turn freight brokerage into a trust product.',
                'content' => '<p>Shippers do not just buy capacity — they buy confidence. Live status, proactive updates, and accountable partners change the entire experience of moving freight.</p>',
                'author' => 'Freight Desk',
                'image' => 'images/9slider.avif',
                'published_at' => now()->subDays(4),
                'sort_order' => 4,
            ],
        ];

        foreach ($posts as $post) {
            WebsiteBlog::updateOrCreate(
                ['slug' => $post['slug']],
                array_merge($post, ['status' => true])
            );
        }
    }
}

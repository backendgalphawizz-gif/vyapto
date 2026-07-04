<?php

namespace Database\Seeders;

use App\Models\StaticPage;
use Illuminate\Database\Seeder;

class StaticPageSeeder extends Seeder
{
    public function run(): void
    {
        $pages = [
            [
                'key' => 'privacy_policy',
                'title' => 'Privacy Policy',
                'content' => '<p>This Privacy Policy explains how VYAPTO collects, uses, and protects employee and operational data within the platform.</p><p>We collect information such as name, contact details, attendance records, location data during punch in/out, and employment-related documents required for payroll and compliance.</p><p>Data is used only for legitimate business purposes including attendance tracking, salary processing, shipment assignment, and communication with employees.</p><p>We do not sell personal data to third parties. Access is restricted to authorized personnel only.</p><p>For privacy-related questions, contact your company administrator.</p>',
            ],
            [
                'key' => 'terms_conditions',
                'title' => 'Terms & Conditions',
                'content' => '<p>By using the VYAPTO employee application and portal, you agree to follow company policies, provide accurate information, and use the system responsibly.</p><p>Employees must mark attendance honestly, complete assigned tasks on time, and protect login credentials. Misuse of the system may result in disciplinary action.</p><p>Location verification may be required for punch in/out. Failure to comply with attendance or delivery procedures can affect payroll and performance records.</p><p>These terms may be updated by the company from time to time. Continued use of the platform indicates acceptance of the updated terms.</p>',
            ],
            [
                'key' => 'about_us',
                'title' => 'About Us',
                'content' => '<p>VYAPTO is a field operations and workforce management platform designed to help teams manage attendance, deliveries, vehicles, payroll, and daily employee activities in one place.</p><p>Our goal is to simplify work for field staff and give administrators clear visibility into operations, compliance, and performance.</p><p>Through the mobile app and employee portal, team members can punch in/out, track shipments, view salary information, and access company policies anytime.</p>',
            ],
        ];

        foreach ($pages as $page) {
            StaticPage::updateOrCreate(
                ['key' => $page['key']],
                [
                    'title' => $page['title'],
                    'content' => $page['content'],
                    'status' => 1,
                ]
            );
        }

        $this->command?->info('Static pages seeded: Privacy Policy, Terms & Conditions, About Us.');
    }
}

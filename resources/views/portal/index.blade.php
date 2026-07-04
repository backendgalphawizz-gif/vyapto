<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Vyapto | Vyapto Management System</title>
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: "Poppins", sans-serif;
            /* background: #f8faf9; */
            background: #0d1b3e;
            color: #1f2937;
            padding-top: 76px;
        }

        .container {
            width: 90%;
            max-width: 1400px;
            margin: auto;
        }

        header {
            height: 76px;
            max-height: 76px;
            /* background: rgba(255, 255, 255, 0.2); */
            background: #fff;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-bottom: 1px solid #eef2f7;
            position: fixed;
            top: 0px;
            left: 0;
            width: 100%;
            z-index: 9999;
        }

        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 0;
        }

        .logo {
            font-size: 28px;
            font-weight: 800;
            color: #0f172a;
        }

        .logo span {
            color: #16a34a;
        }

        .auth-hero-logo {
            height: 50px;
            width: auto;
            max-height: 50px;
        }

        .auth-foot-logo {
            height: 55px;
            width: auto;
            max-height: 55px;
            background: #fff;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .nav-links {
            display: flex;
            gap: 35px;
        }

        .nav-links a {
            text-decoration: none;
            color: #334155;
            font-weight: 600;
        }

        .login-btn {
            /* background: #ffe8cf; */
            color: #FF6002;
            border: 1px solid #FF6002;

            padding: 12px 22px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
        }

        .hero-bg {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 90dvh;
            object-fit: cover;
            z-index: -1;
        }

        .home-wrapper {
            padding-top: 90px;
            height: 100dvh;
            min-height: 100vh;
            /* display: grid; */
            /* background: url(/images/web-auth-bg.png) no-repeat; */
            background-position: bottom;
            background-size: cover;
        }

        .hero-wrapper>div:first-child {
            flex: 1.3;
        }

        .hero-wrapper>div:last-child {
            flex: 1;
        }


        .hero {
            /* padding: 80px 0; */
        }

        .hero-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 50px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #ffe8cf;
            color: #FF6002;
            padding: 10px 18px;
            border-radius: 999px;
            font-weight: 600;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .hero h1 {
            font-size: 48px;
            line-height: 1.1;
            margin-bottom: 30px;
            font-weight: 800;
        }

        .hero h1 span {
            background: #FF8A08;
            background: linear-gradient(0deg, rgba(255, 138, 8, 1) 0%, rgba(255, 96, 2, 1) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: block;
        }

        .hero p {
            color: #8697af;
            font-size: 18px;
            line-height: 1.4;
            margin-bottom: 35px;
            width: 76%;
        }

        .feature-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 35px;
        }

        .feature-box {
            flex: 1 1 calc(25% - 12px);
        }

        .feature-box {
            background: #fff;
            border-radius: 18px;
            padding: 18px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .05);
        }

        .feature-box i {
            width: 55px;
            height: 55px;
            background: #ffe8cf;
            color: #FF6002;
            border-radius: 50%;
            line-height: 55px;
            font-size: 22px;
            margin-bottom: 10px;
        }

        .feature-box h5 {
            font-size: 15px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .feature-box p {
            font-size: 12px;
            margin: 0;
            width: 100%;
        }

        .cta-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: linear-gradient(0deg, rgba(255, 138, 8, 1) 0%, rgba(255, 96, 2, 1) 100%);

            color: #fff;
            text-decoration: none;
            padding: 16px 28px;
            border-radius: 12px;
            font-weight: 700;
        }

        .hero-image {
            position: relative;
        }

        .hero-image img {
            width: 100%;
        }

        .stats {
            margin-top: 40px;
        }

        .stats-box {
            background: #1d2c51;
            border-radius: 24px;
            padding: 30px;
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, .08);
        }

        .stat-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 25px;
        }

        .stat-wrapper {
            flex: 1 1 calc(25% - 15px);
        }

        .stat-wrapper i {
            font-size: 26px;
            background: #ffe8cf;
            color: #FF6002;
            padding: 15px;
            width: 65px;
            height: 65px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 999px;
        }

        .stat-text {
            /* width: 100%; */
            display: flex;
            justify-content: start;
            align-items: center;
        }

        .stat {
            text-align: center;
            /* width: 100%; */
        }


        .stat h3 {
            color: #FF6002;
            font-size: 32px;
            margin-bottom: 8px;
            text-align: left;
        }

        .stat p {
            color: #fff;
            /* color: #8697af; */
            width: 100%;
            margin-bottom: 0;
        }

        .section {
            padding: 90px 0;
        }

        /* 
        .section-title {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-title span {
            color: #16a34a;
            font-weight: 700;
        }

        .section-title h2 {
            font-size: 42px;
            margin-top: 10px;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 25px;
        }

        .card {
            background: #fff;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, .05);
        }

        .card i {
            font-size: 40px;
            color: #16a34a;
            margin-bottom: 20px;
        }

        .card h4 {
            margin-bottom: 15px;
        }

        .card p {
            color: #8697af;
            line-height: 1.7;
        } */


        .features-section {
            padding: 90px 0;
            background: #0d1b3e;
        }

        .section-title {
            text-align: center;
            margin-bottom: 45px;
        }

        .section-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #ffe8cf;
            color: #FF6002;
            font-size: 11px;
            font-weight: 700;
            padding: 6px 12px;
            border-radius: 20px;
            margin-bottom: 15px;
        }

        .section-title h2 {
            font-size: 38px;
            font-weight: 800;
            background: linear-gradient(0deg, rgba(255, 138, 8, 1) 0%, rgba(255, 96, 2, 1) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }

        .section-title p {
            font-size: 15px;
            color: #8697af;
        }

        .features-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 18px;
        }

        .feature-card {
            flex: 1 1 calc(33.333% - 12px);
        }


        .feature-card {
            background: #dee8ff;
            border: 1px solid #0d1b3e;
            border-radius: 14px;
            padding: 22px;
            display: flex;
            align-items: flex-start;
            gap: 18px;
            min-height: 125px;
            transition: .3s;
        }

        .feature-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, .05);
        }

        .feature-icon {
            width: 75px;
            height: 75px;
            min-width: 75px;
            border-radius: 12px;
            /* background: #f2fbf5; */
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .feature-icon i {
            font-size: 24px;
            color: #16a34a;
        }

        .feature-content h4 {
            font-size: 16px;
            font-weight: 700;
            color: #0f172a;
            margin-bottom: 8px;
        }

        .feature-content p {
            font-size: 13px;
            line-height: 1.6;
            color: #8697af;
            margin: 0;
        }


        .why-vyapto {
            position: relative;
            min-height: 500px;
            overflow: hidden;
            /* background-image:
                linear-gradient(90deg,
                    rgba(255, 255, 255, .98) 0%,
                    rgba(255, 255, 255, .95) 35%,
                    rgba(255, 255, 255, .65) 50%,
                    rgba(255, 255, 255, 0) 70%),
                url("images/vyapto-warehouse-bg.png"); */

            background-image: linear-gradient(90deg, rgb(255 255 255 / 0%) 0%, rgb(255 255 255 / 62%) 35%, rgb(255 255 255 / 19%) 50%, rgba(255, 255, 255, 0) 70%), url(images/vyapto-warehouse-bg.png);

            background-size: cover;
            background-position: center;
            margin-bottom: 65px;
        }

        .why-vyapto .container {
            position: relative;
            z-index: 2;
            padding: 60px;
        }

        .content {
            max-width: 420px;
        }

        .content h2 {
            font-size: 42px;
            line-height: 1.1;
            color: #0f172a;
            margin: 20px 0;
        }

        .content p {
            color: #8697af;
            line-height: 1.8;
        }

        .content ul {
            margin-top: 30px;
        }

        .content li {
            display: flex;
            gap: 12px;
            margin-bottom: 18px;
            color: #334155;
        }

        .content li i {
            color: #0BAA67;
        }

        .promise-card {
            position: absolute;
            left: 40%;
            bottom: 40px;

            width: 420px;
            background: #fff;
            border-radius: 20px;
            padding: 24px;

            display: flex;
            gap: 18px;

            box-shadow:
                0 15px 40px rgba(0, 0, 0, .08);
        }



        .testimonials-section {
            /* padding: 100px 0; */
            background: #0d1b3e;
        }

        .section-heading {
            text-align: center;
            margin-bottom: 50px;
        }

        .section-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 14px;
            border-radius: 20px;
            background: #ffe8cf;
            color: #FF6002;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .section-heading h2 {
            font-size: 42px;
            font-weight: 800;
            background: linear-gradient(0deg, rgba(255, 138, 8, 1) 0%, rgba(255, 96, 2, 1) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 10px;
        }

        .section-heading p {
            color: #8697af;
            font-size: 16px;
        }

        .testimonial-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 24px;
        }

        .testimonial-card {
            flex: 1 1 calc(33.333% - 16px);
        }

        .testimonial-card {
            background: #fff;
            border: 1px solid #edf2f7;
            border-radius: 18px;
            padding: 28px;
            min-height: 220px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: .3s;
        }

        .testimonial-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 30px rgba(0, 0, 0, .06);
        }

        .rating {
            color: #fbbf24;
            font-size: 18px;
            letter-spacing: 2px;
            margin-bottom: 15px;
        }

        .testimonial-text {
            color: #475569;
            font-size: 15px;
            line-height: 1.8;
            margin-bottom: 25px;
        }

        .testimonial-user {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .testimonial-user img {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            object-fit: cover;
        }

        .testimonial-user h5 {
            margin: 0;
            color: #0f172a;
            font-size: 15px;
            font-weight: 700;
        }

        .testimonial-user span {
            font-size: 13px;
            color: #94a3b8;
        }

        .testimonial-dots {
            display: none !important;
            margin-top: 35px;
            display: flex;
            justify-content: center;
            gap: 8px;
        }

        .testimonial-dots span {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #d1d5db;
        }

        .testimonial-dots span.active {
            background: #16a34a;
            width: 10px;
            height: 10px;
        }



        .cta-banner {
            margin: 100px auto;
            background-image: url("images/foot-card-bg.png");
            background-size: cover;
            border-radius: 25px;
            padding: 50px;
            color: #fff;
            display: flex;
            justify-content: end;
            align-items: center;

        }

        .cta-banner>div {
            margin-right: 55px;
        }

        .cta-banner h2 {
            font-size: 42px;
        }

        .cta-banner a.banner-btn {
            background: #ffe8cf;
            color: #FF6002;
            text-decoration: none;
            padding: 16px 30px;
            border-radius: 12px;
            font-weight: 700;
        }

        footer {
            background: #1d2c51;
            color: #fff;
            padding: 60px 0;
            padding-bottom: 16px;
        }

        .footer-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 40px;
        }

        .footer-grid>div:first-child {
            flex: 2;
        }

        .footer-grid>div:not(:first-child) {
            flex: 1;
        }

        .footer-grid h4 {
            margin-bottom: 20px;
        }

        .footer-grid ul {
            list-style: none;
        }

        .footer-grid ul li {
            margin-bottom: 12px;
        }

        .footer-grid ul li a {
            color: #d1d5db;
            text-decoration: none;
        }

        .copyright {
            border-top: 1px solid rgba(255, 255, 255, .1);
            margin-top: 40px;
            padding-top: 25px;
            text-align: center;
        }


        /* Hamburger */

        .menu-toggle {
            display: none;
            width: 45px;
            height: 45px;
            border: none;
            background: #16a34a;
            color: #fff;
            border-radius: 10px;
            cursor: pointer;
            font-size: 20px;
        }

        /* Mobile Menu */

        .mobile-menu {
            position: fixed;
            top: -500px;
            left: 0;
            width: 100%;
            background: #fff;
            z-index: 998;
            padding: 90px 25px 30px;
            display: flex;
            flex-direction: column;
            gap: 18px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, .08);
            transition: .4s ease;
        }

        .mobile-menu.active {
            top: 0;
        }

        .mobile-menu a {
            text-decoration: none;
            color: #0f172a;
            font-weight: 600;
            padding: 12px 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .mobile-login-btn {
            background: #16a34a;
            color: #fff !important;
            text-align: center;
            border-radius: 10px;
            padding: 14px !important;
            border: none !important;
        }

        .auth-hero-logo.mobile-logo {
            display: none;
        }

        .auth-hero-logo.desktop-logo {
            display: block !important;
        }

        .mobile-login-btn {
            width: fit-content;
        }

        section,
        footer {
            scroll-margin-top: 100px;
        }


        #home,
        #feature,
        #employees,
        #about,
        #contact {
            scroll-margin-top: 100px;
        }





        .mobile-app-section Specificity: (0, 1, 0) {
            position: relative;
            overflow: hidden;
            padding: 30px 0;
            background: radial-gradient(circle at left center, rgb(225 96 2 / 18%) 0%, transparent 30%), radial-gradient(circle at right center, rgb(225 96 2 / 18%) 0%, transparent 30%), #0d1b3e;
        }

        /* background like screenshot */
        .mobile-app-section::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 15% 50%, rgba(22, 163, 74, .04), transparent 25%),
                radial-gradient(circle at 80% 30%, rgba(22, 163, 74, .04), transparent 25%);
            pointer-events: none;
        }

        .mobile-app-wrapper {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 80px;
        }

        .mobile-app-content {
            width: 35%;
        }

        .mobile-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 7px 14px;
            border-radius: 30px;
            background: #ffe8cf;
            color: #FF6002;
            font-size: 12px;
            font-weight: 700;
            margin-bottom: 20px;
        }

        .mobile-app-content h2 {
            font-size: 48px;
            line-height: 1.15;
            background: linear-gradient(0deg, rgba(255, 138, 8, 1) 0%, rgba(255, 96, 2, 1) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
            margin-bottom: 20px;
        }

        .mobile-app-content p {
            max-width: 420px;
            color: #8697af;
            font-size: 18px;
            line-height: 1.9;
            margin-bottom: 25px;
        }

        .divider {
            width: 80px;
            height: 2px;
            background: #cbd5e1;
            margin-bottom: 35px;
        }

        .playstore-btn img {
            width: 180px;
            display: block;
        }

        .mobile-app-image {
            width: 65%;
            position: relative;
            display: flex;
            justify-content: end;
            align-items: center;
            min-height: 650px;
            gap: 35px;
        }

        .dot-pattern {
            position: absolute;
            width: 520px;
            height: 420px;
            background-image: radial-gradient(#d8f2df 1.5px, transparent 1.5px);
            background-size: 10px 10px;
            opacity: .8;
        }

        .phone {
            position: relative;
            width: 270px;
            max-width: 100%;
            z-index: 2;
            filter: drop-shadow(0 25px 35px rgba(0, 0, 0, .12));
        }

        .phone.phone-left {
            width: 250px;
        }

        .bg-gradient {
            position: absolute;
            width: 800px;
            height: 500px;
            border-radius: 50%;
            /* background: radial-gradient(circle, rgb(255 195 151 / 44%) 0%, rgb(97 45 7 / 29%) 30%, rgb(225 96 2 / 0%) 60%, #0d1b3e 100%); */
            background: radial-gradient(circle, rgb(179 76 0 / 31%) 0%, rgb(141 99 69 / 12%) 30%, rgb(225 96 2 / 0%) 60%, #0d1b3e 100%);
            z-index: 1;
        }

        /* dotted patterns */
        .dot-pattern {
            position: absolute;
            width: 260px;
            height: 260px;
            z-index: 1;

            background-image: radial-gradient(rgb(225 96 2 / 12%) 1.7px, transparent 1.5px);

            background-size: 8px 8px;
        }

        .dot-left {
            left: 120px;
            top: 110px;
        }

        .dot-right {
            right: 120px;
            top: 120px;
        }





        /* =========================================
   RESPONSIVE DESIGN
========================================= */

        /* Large Laptop */
        @media (max-width: 1400px) {

            .container {
                width: 95%;
            }

            .hero h1 {
                font-size: 42px;
            }

            .hero p {
                width: 100%;
            }

            .promise-card {
                left: auto;
                right: 40px;
            }
        }


        /* Laptop */
        @media (max-width: 1200px) {

            .hero-wrapper {
                gap: 30px;
            }

            .hero h1 {
                font-size: 38px;
            }

            .content h2 {
                font-size: 36px;
            }

            .cta-banner h2 {
                font-size: 34px;
            }

            .mobile-app-content h2 {
                font-size: 46px;
            }

            .phone {
                width: 240px;
            }

            .feature-box,
            .stat-wrapper {
                flex: 1 1 calc(50% - 25px);
            }

            .feature-card,
            .testimonial-card {
                flex: 1 1 calc(50% - 15px);
            }
        }



        @media(min-width:992px) {

            .mobile-menu {
                display: none;
            }


        }

        /* Tablet */
        @media (max-width: 991px) {
            .auth-hero-logo.desktop-logo {
                display: none !important;
            }

            .auth-hero-logo.mobile-logo {
                display: block !important;
            }

            .auth-hero-logo.desktop-logo {
                display: none !important;
            }

            .auth-hero-logo.mobile-logo {
                display: block !important;
            }

            .home-wrapper {
                height: auto;
                min-height: auto;
                padding-top: 0;
            }

            .hero {
                padding: 50px 0;
            }

            .hero-image {
                max-width: 600px;
                margin: auto;
            }

            .navbar {
                flex-wrap: wrap;
                gap: 15px;
            }

            .login-btn {
                display: none;
            }

            .menu-toggle {
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .navbar {
                position: relative;
            }


            .section-heading h2 {
                font-size: 32px;
            }


            .hero h1 {
                font-size: 42px;
            }



            .nav-links {
                display: none;
            }

            .hero-wrapper {
                flex-direction: column;
                text-align: center;
            }

            .hero h1 {
                font-size: 38px;
            }

            .hero p {
                width: 100%;
                /* margin: auto auto 30px; */
                margin-bottom: 0;
            }


            .hero-image {
                max-width: 600px;
                margin: auto;
            }

            .why-vyapto {
                background-position: center;
                min-height: auto;
                padding-bottom: 180px;
            }

            .why-vyapto .container {
                padding: 40px;
            }

            .promise-card {
                position: relative;
                width: 100%;
                max-width: 100%;
                left: auto;
                right: auto;
                bottom: auto;
                margin-top: 40px;
            }

            .content {
                max-width: 100%;
            }

            .cta-banner {
                flex-direction: column;
                text-align: center;
                gap: 25px;
            }

            .feature-box,
            .stat-wrapper,
            .feature-card {
                flex: 1 1 calc(50% - 25px);
            }

            .testimonial-card {
                flex: 1 1 100%;
            }

            .footer-grid>div {
                flex: 1 1 calc(50% - 20px);
            }

            .mobile-app-wrapper {
                flex-direction: column;
                gap: 60px;
                text-align: center;
            }

            .mobile-app-content,
            .mobile-app-image {
                width: 100%;
            }

            .mobile-app-content p {
                margin-left: auto;
                margin-right: auto;
            }

            .divider {
                margin-left: auto;
                margin-right: auto;
            }

            .mobile-app-image {
                min-height: auto;
                gap: 20px;
            }

            .phone,
            .phone.phone-left {
                width: 42%;
            }

            .mobile-app-image {
                justify-content: center;
            }

            .playstore-btn {
                margin: 0 auto !important;
                display: block;
                width: fit-content;
            }

            .phone-left,
            .phone-right {
                margin: 0;
            }

            .dot-pattern {
                width: 100%;
                height: 100%;
            }

        }


        /* Mobile */
        @media (max-width: 768px) {

            .hero-bg {
                height: 100%;
                object-position: center;
                opacity: 0.3;
            }

            .navbar {
                justify-content: space-between;
                text-align: center;
            }

            .logo {
                width: fit-content;
                text-align: center;
            }

            .login-btn {
                width: 100%;
                text-align: center;
            }

            .hero h1 {
                font-size: 32px;
                line-height: 1.2;
            }

            .hero p {
                font-size: 16px;
            }

            .stats-box {
                padding: 20px;
                gap: 25px;
            }

            .stat-wrapper {
                justify-content: start;
                gap: 25px;
            }

            .stat h3 {
                text-align: left;
            }

            .stats {
                margin-top: 0;
            }

            .feature-card {
                flex: 1 1 calc(50% - 18px);
                min-height: auto;
                padding: 18px;
            }

            .section-title h2,
            .section-heading h2,
            .content h2 {
                font-size: 28px;
                line-height: 1.3;
            }

            .why-vyapto {
                background-image:
                    linear-gradient(rgba(255, 255, 255, .95),
                        rgba(255, 255, 255, .95)),
                    url("images/vyapto-warehouse-bg.png");
                padding-bottom: 60px;
                margin-top: 30px;
            }

            .why-vyapto .container {
                padding: 25px;
            }

            .promise-card {
                flex-direction: column;
                text-align: center;
            }

            .testimonial-card {
                padding: 22px;
            }

            .cta-banner {
                padding: 30px 20px;
            }

            .cta-banner h2 {
                font-size: 28px;
            }

            .footer-grid {
                text-align: center;
                padding: 0 15px;
            }

            footer {
                padding: 50px 0;
            }

            .feature-row,
            .stats-box,
            .features-grid,
            .testimonial-grid,
            {
            flex-direction: column;
        }

        .feature-row {
            margin: 30px 0
        }

        .feature-box {
            flex: 1 1 calc(50% - 15px);
        }

        .stat-wrapper {
            flex: 1 1 calc(50% - 25px);

        }

        .testimonial-card,
        .footer-grid>div {
            flex: 1 1 100%;
        }

        .cta-banner {
            flex-direction: column;
            gap: 25px;
            text-align: center;
        }

        .section-title h2 {
            font-size: 30px;
        }

        .features-section {
            padding: 30px 0;
        }

        .footer-grid>div:first-child {
            flex: 1 1 100%;
        }

        .footer-grid>div:not(:first-child) {
            text-align: left;
        }

        }


        /* Small Mobile */
        @media (max-width: 576px) {

            .container {
                width: 92%;
            }

            .hero {
                padding: 40px 0;
            }

            .badge,
            .section-badge,
            .section-tag {
                font-size: 11px;
                padding: 8px 12px;
            }

            .hero h1 {
                font-size: 28px;
            }

            .hero p {
                font-size: 15px;
                /* margin-bottom: 30px; */
            }

            .cta-btn {
                width: 100%;
                justify-content: center;
                text-align: center;
            }

            .login-btn {
                padding: 12px;
                width: 100%;
            }

            .feature-row {
                display: none;
            }

            .feature-box {
                padding: 15px;
            }

            .feature-box i {
                width: 50px;
                height: 50px;
                line-height: 50px;
                font-size: 20px;
            }

            .stat h3 {
                font-size: 26px;
                text-align: left;
            }

            .section {
                padding: 60px 0;
            }

            .features-section {
                padding: 60px 0;
            }

            .testimonial-user {
                flex-direction: column;
                text-align: center;
            }

            .cta-banner h2 {
                font-size: 24px;
            }

            .footer-grid>div:not(:first-child) {
                flex: calc(50% - 40px);
                /* max-width: calc(50% - 40px); */
                text-align: left;
            }

            .footer-grid>div:first-child {
                flex: 1 1 100%;
            }

            .why-vyapto .container {
                padding: 0 10px;
            }

            .stat-wrapper {
                gap: 35px;
            }

            .stat-text {
                width: 100%;
            }

            .mobile-app-section {
                padding: 20px 0;
            }

            .mobile-app-content h2 {
                font-size: 36px;
            }

            .mobile-app-content p {
                font-size: 16px;
            }

            .phone,
            .phone.phone-left {
                width: 46%;
            }

            .playstore-btn img {
                width: 150px;
                margin: auto;
            }

            .bg-gradient {
                height: 400px;
            }

        }
    </style>
</head>

<body>
    <header>
        <div class="container">
            <div class="navbar">

                <div class="logo">
                    <img src="images/nav-logo.png" alt="VYAPTO" class="auth-hero-logo desktop-logo">
                    <img src="images/nav-logo-mobile.png" alt="VYAPTO" class="auth-hero-logo mobile-logo">
                </div>

                <!-- <nav class="nav-links">
                        <a href="#">Home</a>
                        <a href="#feature">Features</a>
                        <a href="#">For Employees</a>
                        <a href="#">About Us</a>
                        <a href="#">Contact</a>
                    </nav> -->
                <nav class="nav-links">
                    <a href="javascript:void(0)" onclick="scrollToSection('home')">Home</a>
                    <a href="javascript:void(0)" onclick="scrollToSection('feature')">Features</a>
                    <a href="javascript:void(0)" onclick="scrollToSection('employees')">For Employees</a>
                    <a href="javascript:void(0)" onclick="scrollToSection('about')">About Us</a>
                    <a href="javascript:void(0)" onclick="scrollToSection('contact')">Contact</a>
                </nav>

                <button class="menu-toggle" id="menuToggle">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <a href="/portal/login" class="login-btn">
                    <i class="fa-regular fa-user"></i>
                    Employee Login
                </a>

            </div>
        </div>
    </header>

    <div class="home-wrapper">

        <img src="images/web-auth-bg.png" alt="VYAPTO" class="hero-bg">



        <div class="mobile-menu" id="mobileMenu">
            <a href="javascript:void(0)" onclick="scrollToSection('home')">Home</a>
            <a href="javascript:void(0)" onclick="scrollToSection('feature')">Features</a>
            <a href="javascript:void(0)" onclick="scrollToSection('employees')">For Employees</a>
            <a href="javascript:void(0)" onclick="scrollToSection('about')">About Us</a>
            <a href="javascript:void(0)" onclick="scrollToSection('contact')">Contact</a>

            <a href="/portal/login" class="mobile-login-btn">
                Employee Login
            </a>
        </div>

        <section class="hero" id="home">
            <div class="container">

                <div class="hero-wrapper">

                    <div>

                        <div class="badge">
                            <i class="fa-solid fa-user-group"></i>
                            Employee Portal
                        </div>

                        <h1>
                            Smart Delivery
                            <span>Workforce Platform</span>
                        </h1>

                        <p style="margin-bottom: 30px;">
                            Manage attendance, shipments, salary tracking and field operations from one secure platform.
                        </p>

                        <div class="feature-row">

                            <div class="feature-box">
                                <i class="fa-solid fa-shield-halved"></i>
                                <h5>Secure Access</h5>
                                <p>OTP Based Login</p>
                            </div>

                            <div class="feature-box">
                                <i class="fa-solid fa-location-dot"></i>
                                <h5>GPS Attendance</h5>
                                <p>Live Tracking</p>
                            </div>

                            <div class="feature-box">
                                <i class="fa-solid fa-box"></i>
                                <h5>Shipment Tracking</h5>
                                <p>Real Time Updates</p>
                            </div>

                            <div class="feature-box">
                                <i class="fa-solid fa-file-lines"></i>
                                <h5>Salary Reports</h5>
                                <p>Work Reports</p>
                            </div>

                        </div>

                        <a href="{BASE_URL}/portal/login" class="cta-btn" id="employees">
                            <i class="fa-regular fa-user"></i>
                            Employee Login
                            <i class="fa-solid fa-arrow-right"></i>
                        </a>

                    </div>

                    <div class="hero-image">
                        <img src="images/hero-delivery.png" alt="">
                    </div>

                </div>

                <div class="stats">

                    <div class="stats-box">

                        <div class="stat-wrapper">
                            <div>
                                <i class="fa-solid fa-users"></i>
                            </div>
                            <div class="stat-text">
                                <div class="stat">
                                    <h3>1000+</h3>
                                    <p>Active Employees</p>
                                </div>
                            </div>
                        </div>
                        <div class="stat-wrapper">
                            <div>
                                <i class="fa-solid fa-users"></i>
                            </div>
                            <div class="stat-text">
                                <div class="stat">
                                    <h3>25K+</h3>
                                    <p>Shipments Delivered</p>
                                </div>
                            </div>
                        </div>
                        <div class="stat-wrapper">
                            <div>
                                <i class="fa-solid fa-users"></i>
                            </div>
                            <div class="stat-text">
                                <div class="stat">
                                    <h3>50+</h3>
                                    <p>Locations</p>
                                </div>
                            </div>
                        </div>

                        <div class="stat-wrapper">
                            <div>
                                <i class="fa-solid fa-users"></i>
                            </div>
                            <div class="stat-text">
                                <div class="stat">
                                    <h3>99.9%</h3>
                                    <p>Secure Platform</p>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </section>
    </div>

    <section id="feature" class="features-section">
        <div class="container">

            <div class="section-title">
                <span class="section-badge">
                    <i class="fa-solid fa-layer-group"></i>
                    FEATURES
                </span>

                <h2>Everything You Need, In One Platform</h2>

                <p>
                    Built for delivery associates to simplify daily operations
                    and maximize efficiency.
                </p>
            </div>

            <div class="features-grid">

                <div class="feature-card">
                    <div class="feature-icon">
                        <img src="images/feature-icon1.png" alt="VYAPTO" class="feature-icons">
                    </div>
                    <div class="feature-content">
                        <h4>Easy Attendance</h4>
                        <p>Punch in/out with GPS location and selfie verification for accurate attendance.</p>
                    </div>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <img src="images/feature-icon2.png" alt="VYAPTO" class="feature-icons">
                    </div>
                    <div class="feature-content">
                        <h4>Manage Deliveries</h4>
                        <p>Get assigned shipments and update delivery status in real time.</p>
                    </div>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <img src="images/feature-icon3.png" alt="VYAPTO" class="feature-icons">
                    </div>
                    <div class="feature-content">
                        <h4>Real-Time Tracking</h4>
                        <p>Live tracking of deliveries and routes to ensure complete visibility.</p>
                    </div>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <img src="images/feature-icon4.png" alt="VYAPTO" class="feature-icons">
                    </div>
                    <div class="feature-content">
                        <h4>Salary on Track</h4>
                        <p>Access your salary slips, earnings and work reports anytime.</p>
                    </div>
                </div>

                <div class="feature-card">
                    <div class="feature-icon">
                        <img src="images/feature-icon5.png" alt="VYAPTO" class="feature-icons">
                    </div>
                    <div class="feature-content">
                        <h4>Performance Insights</h4>
                        <p>Track your performance and delivery statistics with detailed insights.</p>
                    </div>

                </div>
                <div class="feature-card">
                    <div class="feature-icon">
                        <img src="images/feature-icon6.png" alt="VYAPTO" class="feature-icons">
                    </div>
                    <div class="feature-content">
                        <h4>Secure & Trusted</h4>
                        <p>Your data is protected with industry-standard security and privacy.</p>
                    </div>
                </div>

            </div>



        </div>

        </div>
    </section>

    <section class="mobile-app-section">

        <div class="container">

            <div class="mobile-app-wrapper">


                <div class="mobile-app-content">

                    <span class="mobile-badge">
                        <i class="fa-solid fa-mobile-screen"></i>
                        MOBILE APP
                    </span>

                    <h2>Your Work, <br> On The Go</h2>

                    <p>
                        Our Android app helps you stay connected, manage deliveries,
                        mark attendance and track earnings — anytime, anywhere.
                    </p>

                    <div class="divider"></div>

                    <a href="#" class="playstore-btn">
                        <img src="images/play-store.png" alt="">
                    </a>

                </div>

                <div class="mobile-app-image">

                    <div class="bg-gradient"></div>

                    <div class="dot-pattern dot-left"></div>
                    <div class="dot-pattern dot-right"></div>

                    <img src="images/app-screen-1.png" alt="" class="phone phone-left">
                    <img src="images/app-screen-2.png" alt="" class="phone phone-right">

                </div>

            </div>

        </div>

    </section>

    <section class="why-vyapto" id="about">
        <div class="container">
            <div class="content">
                <span class="badge">
                    <i class="fa-solid fa-users"></i>
                    WHY CHOOSE VYAPTO
                </span>

                <h2>Built for Smarter<br>Delivery Operations</h2>

                <p>
                    We empower delivery teams and operations managers
                    with the tools they need to work smarter and deliver better.
                </p>

                <ul>
                    <li>
                        <i class="fa-solid fa-circle-check"></i>
                        Dedicated support for employees and delivery partners
                    </li>

                    <li>
                        <i class="fa-solid fa-circle-check"></i>
                        Reliable & accurate tracking for all operations
                    </li>

                    <li>
                        <i class="fa-solid fa-circle-check"></i>
                        Designed for speed, simplicity and productivity
                    </li>
                </ul>
            </div>

            <div class="promise-card">
                <div class="icon">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>

                <div>
                    <h4>Our Promise</h4>
                    <p>
                        To provide a secure, reliable and user-friendly
                        platform that helps every delivery associate succeed.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="testimonials-section">
        <div class="container">

            <div class="section-heading">
                <span class="section-tag">
                    <i class="fa-solid fa-user-group"></i>
                    TESTIMONIALS
                </span>

                <h2>Loved by Delivery Partners</h2>

                <p>
                    See what our employees have to say about Vyapto.
                </p>
            </div>

            <div class="testimonial-grid">

                <div class="testimonial-card">
                    <div class="rating">
                        ★★★★★
                    </div>

                    <p class="testimonial-text">
                        "Vyapto app makes my work so easy. Punch in, get deliveries
                        and track earnings — everything in one place."
                    </p>

                    <div class="testimonial-user">
                        <img src="https://i.pravatar.cc/60?img=12" alt="">
                        <div>
                            <h5>Aman Kumar</h5>
                            <span>Delivery Associate</span>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="rating">
                        ★★★★★
                    </div>

                    <p class="testimonial-text">
                        "The GPS attendance is accurate and the app is very simple to use."
                    </p>

                    <div class="testimonial-user">
                        <img src="https://i.pravatar.cc/60?img=15" alt="">
                        <div>
                            <h5>Rohit Paswan</h5>
                            <span>Delivery Associate</span>
                        </div>
                    </div>
                </div>

                <div class="testimonial-card">
                    <div class="rating">
                        ★★★★★
                    </div>

                    <p class="testimonial-text">
                        "I can track my salary and download payslips anytime. Very helpful!"
                    </p>

                    <div class="testimonial-user">
                        <img src="https://i.pravatar.cc/60?img=18" alt="">
                        <div>
                            <h5>Vivek Singh</h5>
                            <span>Delivery Associate</span>
                        </div>
                    </div>
                </div>

            </div>

            <div class="testimonial-dots">
                <span class="active"></span>
                <span></span>
                <span></span>
            </div>

        </div>
    </section>

    <div class="container">
        <div class="cta-banner">
            <div>
                <h2>Ready To Get Started?</h2>
                <p>Join thousands of delivery associates using Vyapto.</p>
            </div>

            <a class="banner-btn" href="login.php">Employee Login</a>
        </div>
    </div>



    <footer id="contact">
        <div class="container">

            <div class="footer-grid">

                <div>
                    <img src="images/nav-logo.png" alt="VYAPTO" class="auth-foot-logo ">
                    <p>Smart Delivery Workforce Platform for attendance, shipment tracking and operations.</p>
                </div>

                <div>
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="#">Home</a></li>
                        <li><a href="#">Features</a></li>
                        <li><a href="#">About</a></li>
                    </ul>
                </div>

                <div>
                    <h4>Employees</h4>
                    <ul>
                        <li><a href="#">Login</a></li>
                        <li><a href="#">Support</a></li>
                    </ul>
                </div>

                <div>
                    <h4>Contact</h4>
                    <ul>
                        <li>support@vyapto.com</li>
                        <li>+91 99999 99999</li>
                    </ul>
                </div>

            </div>

            <div class="copyright">
                © <?php echo date('Y'); ?> Vyapto. All Rights Reserved.
            </div>

        </div>
    </footer>

    <script>
        const menuToggle = document.getElementById('menuToggle');
        const mobileMenu = document.getElementById('mobileMenu');

        menuToggle.addEventListener('click', () => {

            mobileMenu.classList.toggle('active');

            if (mobileMenu.classList.contains('active')) {
                menuToggle.innerHTML = '<i class="fa-solid fa-xmark"></i>';
            } else {
                menuToggle.innerHTML = '<i class="fa-solid fa-bars"></i>';
            }
        });

        function scrollToSection(id) {
            const element = document.getElementById(id);

            if (element) {
                element.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    </script>



</body>

</html>
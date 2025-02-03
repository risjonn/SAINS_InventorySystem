<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SAINS</title>

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <style>
        /* Smooth Page Load Animation */
        body {
            font-family: 'Nunito', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            opacity: 0;
            animation: fadeIn 1.5s ease-in-out forwards;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
            padding: 20px;
        }

        /* Logo Animation */
        .logo {
    width: 150px;
    height: auto;
    max-width: 100%;
    max-height: 100px;
    margin: 0 auto 20px; /* Centers the logo and adds space below */
    display: block;
}


        .logo:hover {
            transform: rotate(360deg);
        }

        .content {
            background-color: white;
            border-radius: 10px;
            padding: 40px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 1000px;
            margin-top: 30px;
        }

        h1 {
            font-size: 36px;
            color: #333;
            margin-bottom: 20px;
            text-align: center;
        }

        p {
            font-size: 16px;
            color: #555;
            line-height: 1.6;
            margin-bottom: 20px;
            text-align: center;
        }

        /* Call to Action Buttons */
        .cta-buttons {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-bottom: 30px;
        }

        .cta-buttons a {
            background-color: #EF3B2D;
            color: white;
            padding: 12px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: all 0.3s ease-in-out;
        }

        .cta-buttons a:hover {
            background-color: #D6321D;
            transform: scale(1.05);
        }

        /* Card Container */
        .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        /* Card Styling */
        .card {
            background-color: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            width: 45%;
            margin: 10px 0;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .card-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }

        .card-description {
            font-size: 14px;
            color: #777;
        }

        /* Footer */
        .footer {
            font-size: 14px;
            color: #888;
            margin-top: 30px;
            text-align: center;
            padding: 20px;
        }

        .footer a {
            color: #EF3B2D;
            text-decoration: none;
        }

        /* Navigation Bar */
        .nav {
            display: flex;
            justify-content: space-between;
            width: 100%;
            max-width: 1000px;
            align-items: center;
            padding: 10px 20px;
            transition: padding 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }

        .nav a {
            text-decoration: none;
            color: #333;
            font-size: 16px;
            font-weight: 600;
            margin-left: 20px;
        }

        /* Navbar Scroll Effect */
        .scrolled {
            padding: 5px 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .card-container {
                flex-direction: column;
                align-items: center;
            }

            .card {
                width: 90%;
            }

            .nav {
                flex-direction: column;
                align-items: center;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="nav" id="navbar">
            <img src="/image/SAINS.jpeg" alt="SAINS Logo" class="logo">
            <div>
                <a href="{{ route('login') }}">Log in</a>
                <a href="{{ route('register') }}">Register</a>
            </div>
        </div>

        <div class="content">
    <!-- Add the logo above the heading -->
    <img src="/image/SAINS.jpeg" alt="SAINS Logo" class="logo">
    
    <h1>Welcome to SAINS</h1>
    <p>We are dedicated to supporting the Sarawak Government in its drive to push Sarawak forward with innovative and sustainable digital solutions.</p>
    
    <div class="cta-buttons">
        <a href="{{ route('login') }}">Log In</a>
        <a href="{{ route('register') }}">Register</a>
    </div>


            <div class="card-container">
                <div class="card">
                    <div class="card-title">Documentation</div>
                    <div class="card-description">
                        SAINS offers comprehensive documentation covering all aspects of our services and technology.
                    </div>
                </div>

                <div class="card">
                    <div class="card-title">Training</div>
                    <div class="card-description">
                        SAINS offers a variety of training programs for professionals looking to enhance their skills in technology.
                    </div>
                </div>

                <div class="card">
                    <div class="card-title">Solutions</div>
                    <div class="card-description">
                        SAINS provides tailored digital solutions to help governmental organizations achieve their objectives efficiently.
                    </div>
                </div>

                <div class="card">
                    <div class="card-title">Vibrant Ecosystem</div>
                    <div class="card-description">
                        SAINS provides a robust ecosystem of tools, services, and platforms that enable digital transformation.
                    </div>
                </div>
            </div>
        </div>

        <div class="footer">
            <p>Visit us at <a href="https://www.sains.com.my">www.sains.com</a> for more information.</p>
        </div>
    </div>

    <!-- JavaScript for Navbar Scroll Effect -->
    <script>
        window.addEventListener("scroll", function() {
            let navbar = document.getElementById("navbar");
            if (window.scrollY > 50) {
                navbar.classList.add("scrolled");
            } else {
                navbar.classList.remove("scrolled");
            }
        });
    </script>
</body>
</html>

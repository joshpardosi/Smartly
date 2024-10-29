<?php
include('service/database.php');
$stmt = "SELECT * FROM login where role='guru'";
$result = mysqli_query($db, $stmt);

$jumlahGuru = mysqli_num_rows($result);

$stmt = "SELECT * FROM login where role='siswa'";
$result = mysqli_query($db, $stmt);

$jumlahSiswa = mysqli_num_rows($result);

$stmt = "SELECT * FROM courses";
$result = mysqli_query($db, $stmt);

$jumlahCourse = mysqli_num_rows($result);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smartly - Online Learning Platform</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        .navbar {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        /* Hero Section Styles */
        .hero-section {
            background: black;
            padding: 3rem 5rem 1rem 0rem;

        }

        .hero-content {
            text-align: left;
        }

        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .hero-content p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }

        .hero-buttons {
            margin-top: 2rem;
        }

        .hero-buttons .btn {
            font-size: 1.2rem;
            padding: 0.5rem 1.5rem;
            border-radius: 25px;
        }

        .stats-counter {
            margin-top: 3rem;
        }

        .stats-counter h2 {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .stats-counter p {
            font-size: 1rem;
            margin-bottom: 0;
        }

        #heroCarousel {
            position: relative;
            cursor: pointer;
        }

        .carousel-inner {
            position: relative;
            overflow: hidden;
            width: 100%;
        }

        /* Animasi transisi */
        .carousel-item {
            transition: transform .6s ease-in-out;
        }

        .carousel-item img {
            width: 100%;
            height: auto;
        }

        .carousel-caption {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.5);
            color: #fff;
            text-align: center;
        }

        .carousel-caption h3 {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .carousel-caption p {
            font-size: 1rem;
            margin-bottom: 0;
        }

        /* Gradient Text */
        .text-gradient {
            background: linear-gradient(45deg, #fff, #f0f0f0);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        /* Tambahkan atau update style berikut di bagian <style> */
        /* Styling untuk Feature Cards */
        .feature-card {
            padding: 2rem;
            margin: 1rem;
            height: 300px;
            /* Mengatur tinggi tetap */
            background: white;
            transition: all 0.3s ease;
            border: 1px solid #e0e0e0;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .feature-card:hover {
            transform: translateY(-10px);
            border-color: #6366F1;
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.2);
        }

        .feature-card i {
            font-size: 3rem;
            color: #6366F1;
            margin-bottom: 1.5rem;
        }

        .feature-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #333;
        }

        .feature-card p {
            color: #666;
            font-size: 1rem;
            line-height: 1.6;
        }

        /* Styling untuk Contact Cards */
        .contact-card {
            padding: 1.5rem;
            margin: 0.5rem;
            height: 250px;
            /* Mengatur tinggi tetap */
            background: white;
            transition: all 0.3s ease;
            border: 1px solid #e0e0e0;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .contact-card:hover {
            transform: translateY(-10px);
            border-color: #8B5CF6;
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.2);
        }

        .contact-card i {
            font-size: 2.5rem;
            color: #8B5CF6;
            margin-bottom: 1.5rem;
        }

        .contact-card h3 {
            font-size: 1.3rem;
            margin-bottom: 1rem;
            color: #333;
        }

        .contact-card p {
            color: #666;
            font-size: 0.9rem;
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .contact-card a {
            padding: 0.5rem 1.5rem;
            background: linear-gradient(135deg, #6366F1 0%, #8B5CF6 100%);
            color: white;
            text-decoration: none;
            border-radius: 25px;
            transition: all 0.3s ease;
        }

        .contact-card a:hover {
            background: linear-gradient(135deg, #8B5CF6 0%, #6366F1 100%);
            transform: scale(1.05);
        }

        /* Styling untuk sections */
        .features-section,
        .contact-section {
            padding: 5rem 0;
            background: #f8f9fa;
        }

        .features-section h2,
        .contact-section h2 {
            margin-bottom: 3rem;
            color: #333;
            font-weight: bold;
            text-align: center;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {

            .feature-card,
            .contact-card {
                height: auto;
                min-height: 250px;
                margin: 1rem 0;
            }
        }

        .stats-section {
            background: linear-gradient(135deg, #6366F1 0%, #8B5CF6 100%);
            color: white;
        }

        .typing-animation {
            /* font-family: monospace;
      white-space: nowrap; */
            overflow: hidden;
            border-right: 2px solid;
            width: 0;
            animation: typing 2s steps(60, end) forwards;
        }

        @keyframes typing {
            from {
                width: 0;
            }

            to {
                width: 100%;
            }
        }

        #loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: white;
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #007bff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>

<body>
    <div id="loader">
        <div class="spinner"></div>
    </div>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">
                <i class="fas fa-graduation-cap me-2"></i>Smartly
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#faq">FAQ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#testimonials">Testimoni</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#features">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">Contact</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-primary ms-2" href="login.php">Login</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <!-- Hero Section dengan Carousel -->
    <section class="hero-section" id="heroCarousel" class="carousel slide carousel-slide" data-bs-ride="carousel" data-bs-interval="1000">
        <div class="container-fluid p-0">
            <div class="row g-0 align-items-center">
                <!-- Left Content -->
                <div class="col-lg-6 p-5" data-aos="fade-right">
                    <div class="hero-content">
                        <h2 class="typing-animation display-4 fw-bold mb-4 text-gradient">
                            Empowering Education for Everyone
                        </h2>
                        <p class="typing-animation lead mb-4 text-gradient fw-bold">
                            Join our community and start your learning journey today!
                        </p>
                        <div class="hero-buttons">
                            <a href="regis.php" class="btn btn-primary btn-lg me-3">
                                Get Started
                                <i class="fas fa-arrow-right ms-2"></i>
                            </a>
                            <a href="#about" class="btn btn-outline-light btn-lg">
                                Learn More
                                <i class="fas fa-info-circle ms-2"></i>
                            </a>
                        </div>

                    </div>
                </div>

                <!-- Right Carousel -->
                <div class="col-lg-6" data-aos="fade-left">
                    <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-wrap="true">
                        <div class="carousel-inner mt-3">

                            <div class="carousel-item active">
                                <div class="overlay"></div>
                                <img src="img7.png" class="d-block w-100" alt="Education">
                                <div class="carousel-caption">
                                    <h3>Interactive Learning</h3>
                                    <p>Engage in dynamic online learning experiences</p>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="overlay"></div>
                                <img src="img9.png" class="d-block w-100" alt="Students">
                                <div class="carousel-caption">
                                    <h3>Expert Teachers</h3>
                                    <p>Learn from industry professionals</p>
                                </div>
                            </div>
                            <div class="carousel-item">
                                <div class="overlay"></div>
                                <img src="img3.png" class="d-block w-100" alt="Campus">
                                <div class="carousel-caption">
                                    <h3>Flexible Schedule</h3>
                                    <p>Study at your own pace</p>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

        <!-- About Section -->
    <section class="about-section py-5"  id="about">
        <div class="container">
        <div class="about-us">
    <h2 class="text-center">About Us</h2>
    <p class="text-center">
    Welcome to Smartly, your go-to destination for comprehensive online learning. Since our founding in 2024, we’ve been at the forefront of innovative education, bridging the gap between traditional and modern learning. Our platform combines expert-led courses, personalized learning paths, and interactive content, designed to help students achieve their full potential.
    </p>

    <div class="vision-mission text-center">
        <div class="vision">
            <h3>Our Vision</h3>
            <p>To revolutionize education through technology, making quality learning accessible to everyone, everywhere, and creating a world where continuous learning is both engaging and empowering.</p>
        </div>
        
        <div class="mission">
            <h3>Our Mission</h3>
            <p>To provide innovative, accessible, and high-quality educational experiences that inspire lifelong learning and enable individuals to reach their full potential in an ever-evolving global landscape.</p>
        </div>
    </div>
</div>
    </section>

    <!-- Features Section -->
    <section class="features-section py-5" id="features">
        <div class="container">
            <h2 class="text-center">Features</h2>
            <div class="row justify-content-center">
                <div class="col-lg-3 col-md-6" data-aos="fade-up">
                    <div class="feature-card">
                        <i class="fas fa-book-open fa-2x"></i>
                        <h3>Modules Learning</h3>
                        <p>Engaging learning modules that keep you focused.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up">
                    <div class="feature-card">
                        <i class="fas fa-tasks fa-2x"></i>
                        <h3>Assignments & Quizzes</h3>
                        <p>Test your knowledge with various assignments.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up">
                    <div class="feature-card">
                        <i class="fas fa-trophy fa-2x"></i>
                        <h3>Quiz Leaderboard</h3>
                        <p>Race to the Top: See Who’s Winning!</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6" data-aos="fade-up">
                    <div class="feature-card">
                        <i class="fas fa-chart-line fa-2x"></i>
                        <h3>Progress Tracking</h3>
                        <p>Track your Courses and Quizzes progress.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section class="contact-section py-5" id="contact">
        <div class="container">
            <h2 class="text-center">Contact Us</h2>
            <div class="row justify-content-center">
                <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up">
                    <div class="contact-card">
                        <i class="fas fa-phone-alt fa-2x"></i>
                        <h3>Phone Support</h3>
                        <p>Call us at +6289626825501 for any inquiries.</p>
                        <a href="https://wa.me/+6289626825501" target="_blank">Click Here</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up">
                    <div class="contact-card">
                        <i class="fas fa-envelope fa-2x"></i>
                        <h3>Email Support</h3>
                        <p>Reach out via email: Smartly@gmail.com</p>
                        <a href="mailto:Smartly@gmail.com" target="_blank">Click Here</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up">
                    <div class="contact-card">
                        <i class="fas fa-map-marker-alt fa-2x"></i>
                        <h3>Our Location</h3>
                        <p>Visit us at USU, Medan, North Sumatra, Indonesia.</p>
                        <a href="https://maps.app.goo.gl/6ksWx797fuVFypRp7" target="_blank">Click Here</a>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up">
                    <div class="contact-card">
                        <i class="fas fa-comments fa-2x"></i>
                        <h3>Live Chat</h3>
                        <p>Chat with our support team 24/7 for immediate assistance.</p>
                        <a href="https://wa.me/+6289626825501" target="_blank">Click Here</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonial Section -->
    <section class="testimonials-section py-5" data-aos="fade-up" id="testimonials">
        <div class="container">
            <h2 class="text-center">Testimonials</h2>
            <p class="text-center">What Our Learners Have To Say About Us</p>
            <div class="testimonials-slider text-center">
                <div class="testimonials-slider-item">
                    <div class="testimonials-slider-item-content">
                        <i class="fas fa-quote-left fa-2x"></i>
                        <h6>
                            "Tenaga Pendidik dan Praktisi yang berpengalaman dalam bidang ini sangat memudahkan para pelajar dalam belajar."
                        </h6>
                        <h5>Azlin</h5>
                    </div>
                </div>
                <div class="testimonials-slider-item">
                    <div class="testimonials-slider-item-content">
                        <i class="fas fa-quote-left fa-2x"></i>
                        <h6>
                            "Saya sangat senang telah mengikuti pelatihan ini. Terima kasih Smartly."
                            </h>
                            <h5>Josh</h5>
                    </div>
                </div>
                <div class="testimonials-slider-item">
                    <div class="testimonials-slider-item-content">
                        <i class="fas fa-quote-left fa-2x"></i>
                        <h6>
                            "Sejauh ini saya sangat merekomendasikan Smartly untuk pelajar pemula."
                        </h6>
                        <h5>Divay</h5>
                    </div>
                </div>
                <div class="testimonials-slider-item">
                    <div class="testimonials-slider-item-content">
                        <i class="fas fa-quote-left fa-2x"></i>
                        <h6>
                            "Pembelajaran ini sangat menyenangkan. Terima kasih Smartly."
                        </h6>
                        <h5>Shafda</h5>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="faq-section py-5" data-aos="fade-up" id="faq">
        <div class="container">
            <h2 class="text-center">Frequently Asked Questions (FAQ)</h2>
            <p class="text-center">Explore our frequently asked questions to learn more about our services and features.</p>
            <div class="row">
                <div class="">
                    <div class="accordion" id="accordionExample">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                    What is Smartly?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    Smartly is an online learning platform that offers a wide range of courses and resources to help learners improve their skills and knowledge.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    How can I use Smartly?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    You can use Smartly by simply logging in to your account and exploring our courses. You can also enroll in our courses and access our resources for free.
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    How can I enroll in Smartly?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                                data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    You can enroll in our courses by simply logging in to your account and exploring our courses. You can also enroll in our courses and access our resources for free.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Footer -->
    <footer class="footer py-5">
        <div class="container">
            <p class="text-center">Copyright &copy; 2024 Smartly. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init();
        document.addEventListener('DOMContentLoaded', function() {
            const carousel = document.getElementById('heroCarousel');
            const carouselInstance = new bootstrap.Carousel(carousel, {
                interval: 5000, // Durasi auto-slide
                wrap: true // Untuk membuat carousel sirkular
            });

            // Tambahkan area klik
            const nextArea = document.createElement('div');
            nextArea.className = 'carousel-next-area';
            const prevArea = document.createElement('div');
            prevArea.className = 'carousel-prev-area';

            carousel.appendChild(nextArea);
            carousel.appendChild(prevArea);

            // Event listener untuk next/previous
            nextArea.addEventListener('click', function() {
                carouselInstance.next();
            });

            prevArea.addEventListener('click', function() {
                carouselInstance.prev();
            });

        });

        window.addEventListener('load', function() {
            document.getElementById('loader').style.display = 'none';
        });
    </script>
</body>

</html>
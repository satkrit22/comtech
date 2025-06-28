<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Get Your CompTIA A+ Certificate | Comtech Group</title>
    <link href="/comtech/assets/img/favicon.png" rel="icon">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #1e40af;
            --primary-dark: #1e3a8a;
            --secondary-color: #f8fafc;
            --accent-color: #059669;
            --gold-color: #f59e0b;
            --text-primary: #1f2937;
            --text-secondary: #6b7280;
            --border-color: #e5e7eb;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
            color: var(--text-primary);
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        /* Popup Styles */
        .popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 99999;
            opacity: 1;
            transition: opacity 0.5s ease;
            backdrop-filter: blur(5px);
        }

        .popup-content {
            position: relative;
            max-width: 90%;
            max-height: 90%;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-xl);
            animation: popupSlideIn 0.5s ease-out;
        }

        .popup img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 20px;
        }

        .close-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 45px;
            height: 45px;
            font-size: 24px;
            font-weight: bold;
            color: white;
            background: rgba(0, 0, 0, 0.7);
            border: none;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .close-btn:hover {
            background: rgba(239, 68, 68, 0.9);
            transform: scale(1.1);
        }

        @keyframes popupSlideIn {
            from {
                opacity: 0;
                transform: scale(0.8) translateY(-50px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Enhanced Header */
        .header {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            padding: 25px 0;
            box-shadow: var(--shadow-md);
            position: sticky;
            top: 0;
            z-index: 1000;
            border-bottom: 1px solid var(--border-color);
        }

        .header h1 {
            text-align: center;
            font-size: 3rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--gold-color), #ea580c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -1px;
        }

        .header .subtitle {
            text-align: center;
            color: var(--text-secondary);
            font-size: 1.2rem;
            margin-top: 10px;
            font-weight: 500;
        }

        .nav-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 20px;
        }

        .nav-links a {
            color: var(--text-primary);
            text-decoration: none;
            font-weight: 600;
            padding: 12px 24px;
            border-radius: 50px;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.8);
            box-shadow: var(--shadow-sm);
        }

        .nav-links a:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        /* Main Content */
        .main-content {
            padding: 60px 0;
        }

        .hero-section {
            text-align: center;
            margin-bottom: 60px;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            color: white;
            margin-bottom: 20px;
            text-shadow: 0 4px 8px rgba(0,0,0,0.3);
            letter-spacing: -1px;
        }

        .hero-subtitle {
            font-size: 1.3rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 40px;
            font-weight: 500;
        }

        .certificate-info {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            padding: 50px;
            border-radius: 24px;
            box-shadow: var(--shadow-xl);
            margin-bottom: 40px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .certificate-info h2 {
            color: var(--primary-color);
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 30px;
            text-align: center;
            letter-spacing: -0.5px;
        }

        .benefits-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin: 40px 0;
        }

        .benefit-card {
            background: rgba(255, 255, 255, 0.8);
            padding: 30px;
            border-radius: 16px;
            box-shadow: var(--shadow-md);
            text-align: center;
            transition: all 0.3s ease;
        }

        .benefit-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-xl);
        }

        .benefit-icon {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .benefit-card h3 {
            color: var(--primary-color);
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .benefit-card p {
            color: var(--text-secondary);
            line-height: 1.6;
        }

        /* Certificate Form */
        .certificate-form {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            padding: 60px;
            border-radius: 28px;
            box-shadow: var(--shadow-xl);
            margin: 50px 0;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .certificate-form h2 {
            text-align: center;
            color: var(--primary-color);
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 40px;
            letter-spacing: -0.5px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 40px;
        }

        .form-group {
            margin-bottom: 30px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: var(--text-primary);
            font-size: 1rem;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
            font-family: inherit;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(30, 64, 175, 0.1);
            transform: translateY(-1px);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .submit-btn {
            background: linear-gradient(135deg, var(--gold-color), #ea580c);
            color: white;
            padding: 18px 50px;
            border: none;
            border-radius: 12px;
            font-size: 1.2rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 30px;
            letter-spacing: 0.5px;
        }

        .submit-btn:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-xl);
        }

        /* Alert Messages */
        .alert {
            padding: 20px 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            font-weight: 600;
            font-size: 1rem;
        }

        .alert-success {
            background: #d1fae5;
            color: #065f46;
            border: 2px solid #a7f3d0;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 2px solid #fecaca;
        }

        /* Footer */
        .footer {
            background: rgba(17, 24, 39, 0.98);
            backdrop-filter: blur(20px);
            color: white;
            text-align: center;
            padding: 40px 0;
            margin-top: 80px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 0 15px;
            }
            
            .header h1 {
                font-size: 2.2rem;
            }
            
            .hero-title {
                font-size: 2.5rem;
            }
            
            .certificate-form {
                padding: 40px 30px;
            }

            .nav-links {
                flex-direction: column;
                align-items: center;
                gap: 15px;
            }
        }
    </style>
</head>

<body>
    

    <header class="header">
        <div class="container">
            <h1>🏆 Get Your Certificate</h1>
            <p class="subtitle">Official CompTIA A+ Certification from Comtech Group</p>
            <div class="nav-links">
                <a href="comptia.php">Training</a>
                <a href="#benefits">Benefits</a>
                <a href="#apply">Apply Now</a>
                <a href="/comtech/home.html">Home</a>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <div class="hero-section">
                <h1 class="hero-title">Advance Your IT Career</h1>
                <p class="hero-subtitle">Get your official CompTIA A+ certificate and unlock new opportunities in the IT industry</p>
            </div>

            <div class="certificate-info" id="benefits">
                <h2>Why Get Your CompTIA A+ Certificate?</h2>
                <div class="benefits-grid">
                    <div class="benefit-card">
                        <div class="benefit-icon">💼</div>
                        <h3>Career Advancement</h3>
                        <p>Open doors to higher-paying IT positions and demonstrate your technical expertise to employers worldwide.</p>
                    </div>
                    <div class="benefit-card">
                        <div class="benefit-icon">🌟</div>
                        <h3>Industry Recognition</h3>
                        <p>CompTIA A+ is globally recognized and appears in more IT job listings than any other certification.</p>
                    </div>
                    <div class="benefit-card">
                        <div class="benefit-icon">📈</div>
                        <h3>Salary Increase</h3>
                        <p>Certified professionals earn 15-20% more than their non-certified counterparts in similar roles.</p>
                    </div>
                    <div class="benefit-card">
                        <div class="benefit-icon">🎯</div>
                        <h3>Skill Validation</h3>
                        <p>Prove your hands-on skills in hardware, software, networking, and security troubleshooting.</p>
                    </div>
                    <div class="benefit-card">
                        <div class="benefit-icon">🔒</div>
                        <h3>Job Security</h3>
                        <p>Stay relevant in the ever-evolving IT landscape with industry-standard certification.</p>
                    </div>
                    <div class="benefit-card">
                        <div class="benefit-icon">🌐</div>
                        <h3>Global Opportunities</h3>
                        <p>Access job opportunities worldwide with this internationally recognized certification.</p>
                    </div>
                </div>
            </div>

            <!-- Certificate Application Form -->
            <div class="certificate-form" id="apply">
                <h2>Apply for Your Certificate</h2>

                <!-- Success Message -->
                <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
                    <div class="alert alert-success">
                        <strong>Application Submitted!</strong> Thank you for applying for your CompTIA A+ certificate. We'll review your application and contact you within 2-3 business days with next steps.
                    </div>
                <?php endif; ?>

                <!-- Form -->
                <form method="POST" action="https://formspree.io/f/myzkdrnk" onsubmit="return validateCertificateForm()">
                    <div class="form-grid">
                        <div>
                            <div class="form-group">
                                <label for="full_name">Full Name (as it should appear on certificate) *</label>
                                <input type="text" id="full_name" name="full_name" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="phone">Phone Number *</label>
                                <input type="tel" id="phone" name="phone" required pattern="^(98|97|96|99|98[0-9]{1})[0-9]{7}$" placeholder="Enter Nepalese phone number">
                            </div>

                            <div class="form-group">
                                <label for="date_of_birth">Date of Birth *</label>
                                <input type="date" id="date_of_birth" name="date_of_birth" required>
                            </div>

                            <div class="form-group">
                                <label for="training_completion_date">Training Completion Date *</label>
                                <input type="date" id="training_completion_date" name="training_completion_date" required>
                            </div>
                        </div>
                        
                        <div>
                            <div class="form-group">
                                <label for="address">Complete Address *</label>
                                <textarea id="address" name="address" required placeholder="Street, City, District, Province"></textarea>
                            </div>

                            <div class="form-group">
                                <label for="training_type">Training Type *</label>
                                <select id="training_type" name="training_type" required>
                                    <option value="">Select training type</option>
                                    <option value="comptia_a_plus">CompTIA A+ Core 1 & 2</option>
                                    <option value="hardware_networking">Hardware & Networking</option>
                                    <option value="chip_level">Chip Level Repair</option>
                                    <option value="custom">Custom Training Program</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label for="instructor_name">Instructor Name</label>
                                <input type="text" id="instructor_name" name="instructor_name" placeholder="Name of your training instructor">
                            </div>

                            <div class="form-group">
                                <label for="additional_info">Additional Information</label>
                                <textarea id="additional_info" name="additional_info" placeholder="Any additional information or special requests..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" required style="margin-right: 10px;">
                            I confirm that all information provided is accurate and I have completed the required training program at Comtech Group Co. Pvt. Ltd.
                        </label>
                    </div>
                    
                    <button type="submit" class="submit-btn">🏆 Apply for Certificate</button>
                </form>
            </div>
        </div>
    </main>

    <script>
        // Popup functionality
        function closePopup() {
            const popup = document.getElementById("popup");
            popup.style.opacity = 0;
            setTimeout(function() {
                popup.style.display = "none";
                document.body.style.overflow = "auto";
            }, 500);
        }

        // Auto close popup after 8 seconds
        setTimeout(closePopup, 8000);

        // Close popup when clicking outside the image
        document.getElementById("popup").addEventListener("click", function(e) {
            if (e.target === this) {
                closePopup();
            }
        });

        // Form validation
        function validatePhoneNumber(phone) {
            var phonePattern = /^(98|97|96|99|98[0-9]{1})[0-9]{7}$/; 
            return phonePattern.test(phone);
        }

        function validateEmail(email) {
            var emailPattern = /^[a-zA-Z][a-zA-Z0-9._%+-]*@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            return emailPattern.test(email);
        }

        function validateCertificateForm() {
            var email = document.getElementById("email").value;
            var phone = document.getElementById("phone").value;
            var dateOfBirth = new Date(document.getElementById("date_of_birth").value);
            var completionDate = new Date(document.getElementById("training_completion_date").value);
            var today = new Date();
            
            if (!validateEmail(email)) {
                alert("Please enter a valid email address.");
                return false;
            }
            
            if (!validatePhoneNumber(phone)) {
                alert("Please enter a valid Nepalese phone number.");
                return false;
            }

            if (dateOfBirth >= today) {
                alert("Please enter a valid date of birth.");
                return false;
            }

            if (completionDate > today) {
                alert("Training completion date cannot be in the future.");
                return false;
            }

            return true;
        }

        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>

    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Comtech Group Co. Pvt. Ltd. All rights reserved. | Official CompTIA Training Partner</p>
        </div>
    </footer>
</body>
</html>
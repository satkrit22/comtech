<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CompTIA A+ Professional Training | IT Certification Course</title>
    <link href="/comtech/assets/img/favicon.png" rel="icon">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="comptia.css">
</head>

<body>
    <!-- Popup Ad Section -->
    <div id="popup" class="popup">
        <div class="popup-content">
            <button class="close-btn" onclick="closePopup()" aria-label="Close popup">×</button>
            <img src="/comtech/assets/img/gallery/gallery-2.jpg" alt="Special CompTIA Training Offer">
        </div>
    </div>

    <header class="header">
        <div class="container">
            <h1>CompTIA A+</h1>
            <p class="subtitle">Professional IT Certification Training</p>
            <div class="nav-links">
                <a href="#about">About</a>
                <a href="#skills">Skills</a>
                <a href="#jobs">Careers</a>
                <a href="#contact">Contact</a>
                <a href="get-certificate.php">Get Certificate</a>
            </div>
        </div>
    </header>

    <main class="main-content">
        <div class="container">
            <div class="section" id="about">
                <h2>About CompTIA A+</h2>
                <p>CompTIA A+ is the industry standard for establishing a career in IT. CompTIA A+ certified professionals are proven problem solvers. They support today's core technologies from security to networking to virtualization and more. CompTIA A+ is the industry standard for launching IT careers into today's digital world.</p>
                
                <p>CompTIA A+ is the only industry recognized credential with performance testing to prove pros can think on their feet to perform critical IT support tasks. It is trusted by employers around the world to identify the go-to person in endpoint management & technical support roles. CompTIA A+ appears in more tech support job listings than any other IT credential.</p>
                
                <p>The CompTIA A+ Core Series requires candidates to pass two exams: Core 1 (220-1101) and Core 2 (220-1102) covering the following new content, emphasizing the technologies and skills IT pros need to support a hybrid workforce.</p>
                
                <ul>
                    <li>Increased reliance on SaaS applications for remote work</li>
                    <li>More on troubleshooting and how to remotely diagnose and correct common software, hardware, or connectivity problems</li>
                    <li>Changing core technologies from cloud virtualization and IoT device security to data management and scripting</li>
                    <li>Multiple operating systems now encountered by technicians on a regular basis, including the major systems, their use cases, and how to keep them running properly</li>
                    <li>Reflects the changing nature of the job role, where many tasks are sent to specialized providers as certified personnel need to assess whether it's best to fix something on site, or to save time and money by sending proprietary technologies directly to vendors</li>
                </ul>
            </div>

            <h1 class="skills-title" id="skills">What Skills Will You Learn?</h1>
            <div class="skills-section">
                <div class="skill">
                    <div class="skill-image-container">
                        <img src="/comtech/assets/img/comptia/2662398.png" alt="Hardware">
                    </div>
                    <h3>Hardware</h3>
                    <p>Identifying, using, and connecting hardware components and devices, including the broad knowledge about different devices that is now necessary to support the remote workforce</p>
                </div>
                <div class="skill">
                    <div class="skill-image-container">
                        <img src="/comtech/assets/img/comptia/windows-os-logo-top-operating-system-signs-free-png.webp" alt="Operating Systems">
                    </div>
                    <h3>Operating Systems</h3>
                    <p>Install and support Windows OS including command line and client support, system configuration imaging and troubleshooting for Mac OS, Chrome OS, Android and Linux OS</p>
                </div>
                <div class="skill">
                    <div class="skill-image-container">
                        <img src="/comtech/assets/img/comptia/download-software-logo-material-design-png_87546.jpg" alt="Software Troubleshooting">
                    </div>
                    <h3>Software Troubleshooting</h3>
                    <p>Troubleshoot PC and mobile device issues including common OS, malware and security issues</p>
                </div>
                <div class="skill">
                    <div class="skill-image-container">
                        <img src="/comtech/assets/img/comptia/8bf6a97f8e6d3f239057d0882e7b7837403b1f7da9c95c6123b5c22e43bb22ac.jpg" alt="Networking">
                    </div>
                    <h3>Networking</h3>
                    <p>Explain types of networks and connections including TCP/IP, WIFI and SOHO</p>
                </div>
                <div class="skill">
                    <div class="skill-image-container">
                        <img src="/comtech/assets/img/comptia/png-clipart-repair-logo-computer-icons-troubleshooting-icon-design-tuning-miscellaneous-black.png" alt="Troubleshooting">
                    </div>
                    <h3>Troubleshooting</h3>
                    <p>Troubleshoot real-world device and network issues quickly and efficiently</p>
                </div>
                <div class="skill">
                    <div class="skill-image-container">
                        <img src="/comtech/assets/img/comptia/pngtree-creative-abstract-open-lock-logo.-security-logo-concept.-png-image_3628086.jpg" alt="Security">
                    </div>
                    <h3>Security</h3>
                    <p>Identify and protect against security vulnerabilities for devices and their network connections</p>
                </div>
                <div class="skill">
                    <div class="skill-image-container contain">
                        <img src="/comtech/assets/img/comptia/iStock-981901868.jpg" alt="Virtualization and Cloud Computing">
                    </div>
                    <h3>Virtualization and Cloud Computing</h3>
                    <p>Compare and contrast cloud computing concepts and set up client-side virtualization</p>
                </div>
            </div>

            <h1 class="jobs-title" id="jobs">Jobs You Can Land With CompTIA A+</h1>
            <div class="jobs-section">
                <div class="job">Help Desk Technician</div>
                <div class="job">IT Specialist</div>
                <div class="job">Field Service Technician</div>
                <div class="job">IT Support Manager</div>
                <div class="job">Business Analyst</div>
                <div class="job">Data Support Technician</div>
                <div class="job">Technical Support Specialist</div>
                <div class="job">MSP Personnel</div>
                <div class="job">IT Server Technician</div>
            </div>

            <!-- Contact Form Section -->
            <div class="contact-section" id="contact">
                <h2>Start Your CompTIA A+ Journey Today</h2>

                <!-- Success Message -->
                <?php if (isset($_POST['name']) && isset($_POST['email'])): ?>
                    <div class="alert alert-success">
                        <strong>Thank you!</strong> Your inquiry has been submitted successfully. We'll contact you within 24 hours to discuss your CompTIA A+ training options.
                    </div>
                <?php endif; ?>

                <!-- Error Message -->
                <?php if (isset($error_message) && $error_message): ?>
                    <div class="alert alert-error">
                        <strong>Error:</strong> <?php echo $error_message; ?>
                    </div>
                <?php endif; ?>

                <!-- Form -->
                <form method="POST" action="https://formspree.io/f/myzkdrnk" onsubmit="return validateForm()">
                    <div class="form-grid">
                        <div>
                            <div class="form-group">
                                <label for="name">Full Name *</label>
                                <input type="text" id="name" name="name" required value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" name="email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            </div>
                            
                            <div class="form-group">
                                <label for="phone">Phone Number *</label>
                                <input type="tel" id="phone" name="phone" required value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" pattern="^(98|97|96|99|98[0-9]{1})[0-9]{7}$" placeholder="Enter a valid Nepalese phone number (e.g., 9801234567)">
                            </div>
                            
                            <div class="form-group">
                                <label for="experience">IT Experience Level</label>
                                <select id="experience" name="experience">
                                    <option value="">Select your experience level</option>
                                    <option value="beginner" <?php echo (isset($_POST['experience']) && $_POST['experience'] == 'beginner') ? 'selected' : ''; ?>>Beginner (0-1 years)</option>
                                    <option value="intermediate" <?php echo (isset($_POST['experience']) && $_POST['experience'] == 'intermediate') ? 'selected' : ''; ?>>Intermediate (1-3 years)</option>
                                    <option value="advanced" <?php echo (isset($_POST['experience']) && $_POST['experience'] == 'advanced') ? 'selected' : ''; ?>>Advanced (3+ years)</option>
                                </select>
                            </div>
                        </div>
                        
                        <div>
                            <div class="form-group">
                                <label for="message">Tell us about your goals and any questions you have</label>
                                <textarea id="message" name="message" placeholder="I'm interested in CompTIA A+ training because..."><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="submit-btn">Get Started with CompTIA A+ Training</button>
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

        function validateForm() {
            var email = document.getElementById("email").value;
            var phone = document.getElementById("phone").value;
            
            if (!validateEmail(email)) {
                alert("Please enter a valid email address that does not start with a number.");
                return false;
            }
            
            if (!validatePhoneNumber(phone)) {
                alert("Please enter a valid Nepalese phone number.");
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
            <p>&copy; 2024 CompTIA A+ Training Center. All rights reserved. | Professional IT Certification Training</p>
        </div>
    </footer>
</body>
</html>
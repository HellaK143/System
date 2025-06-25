<?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success text-center">Your application was submitted successfully!</div>
<?php endif; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UMU Innovation Office - Application Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="styles.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="logo.png">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css" />
    <style>
        .form-step { display: none; }
        .form-step.active { display: block; }
    </style>
</head>
<body class="application-page">
    <!-- Navigation -->
    <nav class="navbar navbar-expand-xl navbar-dark">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.html">
                <img src="logo.png" alt="UMU Logo" class="logo me-2">
                <div class="brand-text">
                    <span class="main-brand">Uganda Martyrs University</span>
                    <span class="sub-brand">Innovation Office</span>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="programsDropdown" role="button" data-bs-toggle="dropdown">Programs</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">TAG DEV 2.0</a></li>
                            <li><a class="dropdown-item" href="#">SUESCA</a></li>
                            <li><a class="dropdown-item" href="#">AI Innovation Challenge</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="#">Partnerships</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Mentorship</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Incubation</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">News</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Gallery</a></li>
                    <li class="nav-item"><a class="nav-link entrepreneur-portal" href="login.php">Entrepreneur Portal</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <!-- Application Form Section -->
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="text-center mb-4">Entrepreneur Application Form</h2>
                        <form id="applicationForm" action="dist/submit_application.php" method="POST" enctype="multipart/form-data" novalidate>
                            <!-- Step 1: Basic Info -->
                            <div class="form-step active" id="step1">
                                <div class="mb-3">
                                    <label for="full_name" class="form-label">Full Name*</label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" required>
                                    <div class="invalid-feedback">Please enter your full name.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="category" class="form-label">Category*</label>
                                    <select class="form-select" id="category" name="category" required>
                                        <option value="">Select</option>
                                        <option value="student">Student</option>
                                        <option value="staff">Staff</option>
                                        <option value="alumni">Alumni</option>
                                        <option value="community">Community Member</option>
                                    </select>
                                    <div class="invalid-feedback">Please select a category.</div>
                                </div>
                                <div class="text-end">
                                    <button type="button" class="btn btn-primary next-btn">Next</button>
                                </div>
                            </div>
                            <!-- Step 2: Category Info -->
                            <div class="form-step" id="step2">
                                <!-- Student Fields -->
                                <div class="category-fields" id="studentFields" style="display:none;">
                                    <h5>Student Information</h5>
                                    <div class="mb-3">
                                        <label for="campus" class="form-label">Campus*</label>
                                        <select class="form-select" id="campus" name="campus">
                                            <option value="">Select Campus</option>
                                            <option value="main">Main Campus - Nkozi</option>
                                            <option value="kampala">Kampala Campus</option>
                                            <option value="mbale">Mbale Campus</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="student_number" class="form-label">Student Number*</label>
                                        <input type="text" class="form-control" id="student_number" name="student_number">
                                    </div>
                                    <div class="mb-3">
                                        <label for="course" class="form-label">Course*</label>
                                        <input type="text" class="form-control" id="course" name="course">
                                    </div>
                                    <div class="mb-3">
                                        <label for="year_of_study" class="form-label">Year of Study*</label>
                                        <select class="form-select" id="year_of_study" name="year_of_study">
                                            <option value="">Select Year</option>
                                            <option value="1">Year 1</option>
                                            <option value="2">Year 2</option>
                                            <option value="3">Year 3</option>
                                            <option value="4">Year 4</option>
                                            <option value="5">Year 5</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- Staff Fields -->
                                <div class="category-fields" id="staffFields" style="display:none;">
                                    <h5>Staff Information</h5>
                                    <div class="mb-3">
                                        <label for="staff_number" class="form-label">Staff Number*</label>
                                        <input type="text" class="form-control" id="staff_number" name="staff_number">
                                    </div>
                                    <div class="mb-3">
                                        <label for="faculty" class="form-label">Faculty*</label>
                                        <input type="text" class="form-control" id="faculty" name="faculty">
                                    </div>
                                    <div class="mb-3">
                                        <label for="years_at_umu" class="form-label">Years at UMU*</label>
                                        <input type="number" class="form-control" id="years_at_umu" name="years_at_umu">
                                    </div>
                                </div>
                                <!-- Alumni Fields -->
                                <div class="category-fields" id="alumniFields" style="display:none;">
                                    <h5>Alumni Information</h5>
                                    <div class="mb-3">
                                        <label for="graduation_year" class="form-label">Year of Graduation*</label>
                                        <select class="form-select" id="graduation_year" name="graduation_year"></select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="current_job" class="form-label">Current Job*</label>
                                        <input type="text" class="form-control" id="current_job" name="current_job">
                                    </div>
                                    <div class="mb-3">
                                        <label for="employer" class="form-label">Employer*</label>
                                        <input type="text" class="form-control" id="employer" name="employer">
                                    </div>
                                </div>
                                <!-- Community Fields -->
                                <div class="category-fields" id="communityFields" style="display:none;">
                                    <h5>Community Member Information</h5>
                                    <div class="mb-3">
                                        <label for="national_id" class="form-label">National ID*</label>
                                        <input type="text" class="form-control" id="national_id" name="national_id">
                                    </div>
                                    <div class="mb-3">
                                        <label for="occupation" class="form-label">Occupation*</label>
                                        <input type="text" class="form-control" id="occupation" name="occupation">
                                    </div>
                                    <div class="mb-3">
                                        <label for="marital_status" class="form-label">Marital Status*</label>
                                        <select class="form-select" id="marital_status" name="marital_status">
                                            <option value="">Select Status</option>
                                            <option value="single">Single</option>
                                            <option value="married">Married</option>
                                            <option value="divorced">Divorced</option>
                                            <option value="widowed">Widowed</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="num_beneficiaries" class="form-label">Number of Beneficiaries*</label>
                                        <input type="number" class="form-control" id="num_beneficiaries" name="num_beneficiaries">
                                    </div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-secondary prev-btn">Previous</button>
                                    <button type="button" class="btn btn-primary next-btn">Next</button>
                                </div>
                            </div>
                            <!-- Step 3: Personal Info -->
                            <div class="form-step" id="step3">
                                <div class="mb-3">
                                    <label for="nationality" class="form-label">Nationality*</label>
                                    <input type="text" class="form-control" id="nationality" name="nationality" required>
                                </div>
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Telephone Contact*</label>
                                    <input type="tel" class="form-control" id="phone" name="phone" required pattern="[0-9]{10,15}">
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address*</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                                <div class="mb-3">
                                    <label for="street" class="form-label">Street*</label>
                                    <input type="text" class="form-control" id="street" name="street" required>
                                </div>
                                <div class="mb-3">
                                    <label for="village" class="form-label">Village*</label>
                                    <input type="text" class="form-control" id="village" name="village" required>
                                </div>
                                <div class="mb-3">
                                    <label for="subcounty" class="form-label">Subcounty*</label>
                                    <input type="text" class="form-control" id="subcounty" name="subcounty" required>
                                </div>
                                <div class="mb-3">
                                    <label for="country" class="form-label">Country*</label>
                                    <select class="form-select" id="country" name="country" required>
                                        <option value="">Select Country</option>
                                        <option value="Uganda">Uganda</option>
                                        <option value="Kenya">Kenya</option>
                                        <option value="Tanzania">Tanzania</option>
                                        <option value="Rwanda">Rwanda</option>
                                        <option value="Burundi">Burundi</option>
                                        <option value="South Sudan">South Sudan</option>
                                        <option value="Nigeria">Nigeria</option>
                                        <option value="Ghana">Ghana</option>
                                        <option value="South Africa">South Africa</option>
                                        <option value="United States">United States</option>
                                        <option value="United Kingdom">United Kingdom</option>
                                        <!-- Add more countries as needed -->
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="district" class="form-label">District*</label>
                                    <select class="form-select" id="district" name="district" required disabled>
                                        <option value="">Select District</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Refugee Status*</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="refugee" id="refugeeYes" value="yes" required>
                                        <label class="form-check-label" for="refugeeYes">Yes</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="refugee" id="refugeeNo" value="no">
                                        <label class="form-check-label" for="refugeeNo">No</label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="age_range" class="form-label">Age Range*</label>
                                    <select class="form-select" id="age_range" name="age_range" required>
                                        <option value="">Select Age Range</option>
                                        <option value="below15">Below 15</option>
                                        <option value="15-18">15-18</option>
                                        <option value="19-25">19-25</option>
                                        <option value="26-30">26-30</option>
                                        <option value="31-35">31-35</option>
                                        <option value="36-40">36-40</option>
                                        <option value="above40">Above 40</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Disability*</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="disability" id="disabilityYes" value="yes" required>
                                        <label class="form-check-label" for="disabilityYes">Yes</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="disability" id="disabilityNo" value="no">
                                        <label class="form-check-label" for="disabilityNo">No</label>
                                    </div>
                                </div>
                                <div class="mb-3" id="disabilityDescription" style="display: none;">
                                    <label for="disability_text" class="form-label">Please describe your disability</label>
                                    <textarea class="form-control" id="disability_text" maxlength="300" rows="3" name="disability_text"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="gender" class="form-label">Gender*</label>
                                    <select class="form-select" id="gender" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                    </select>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-secondary prev-btn">Previous</button>
                                    <button type="button" class="btn btn-primary next-btn">Next</button>
                                </div>
                            </div>
                            <!-- Step 4: Business Info -->
                            <div class="form-step" id="step4">
                                <div class="mb-3">
                                    <label for="business_idea_name" class="form-label">Business/Idea Name*</label>
                                    <input type="text" class="form-control" id="business_idea_name" name="business_idea_name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="sector" class="form-label">Sector*</label>
                                    <select class="form-select" id="sector" name="sector" required>
                                        <option value="">Select Sector</option>
                                        <option value="agriculture">Agriculture</option>
                                        <option value="climate">Climate Action</option>
                                        <option value="financial">Financial Services</option>
                                        <option value="ict">Information Computer Technology</option>
                                        <option value="transportation">Transportation</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="program_attended" class="form-label">Entrepreneurship Program Attended</label>
                                    <select class="form-select" id="program_attended" name="program_attended">
                                        <option value="">Select Program</option>
                                        <option value="tagdev">TAG DEV 2.0</option>
                                        <option value="suesca">SUESCA</option>
                                        <option value="ai">AI Innovation Challenge</option>
                                        <option value="unesco">UNESCO Africa Engineering Week</option>
                                        <option value="none">None of the above</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="initial_capital" class="form-label">Initial Capital (UGX/USD)*</label>
                                    <input type="number" class="form-control" id="initial_capital" name="initial_capital" required min="0">
                                </div>
                                <div class="mb-3">
                                    <label for="cohort" class="form-label">Cohort</label>
                                    <input type="text" class="form-control" id="cohort" name="cohort">
                                </div>
                                <div class="mb-3">
                                    <label for="year_of_inception" class="form-label">Year of Inception*</label>
                                    <select class="form-select" id="year_of_inception" name="year_of_inception" required></select>
                                </div>
                                <div class="mb-3">
                                    <label for="interested_in" class="form-label">Interested in*</label>
                                    <select class="form-select" id="interested_in" name="interested_in" required>
                                        <option value="">Select Interest</option>
                                        <option value="funding">Accessing Funding</option>
                                        <option value="mentorship">Mentorship</option>
                                        <option value="business">Business Development Services</option>
                                        <option value="acceleration">Acceleration</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="attachments" class="form-label">Attachments (optional)</label>
                                    <input type="file" class="form-control" id="attachments" name="attachments[]" multiple accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.mp4,.avi,.mov,.txt,.zip,.rar,.csv">
                                    <div class="form-text">You can upload multiple files. Allowed types: pdf, doc, docx, ppt, pptx, xls, xlsx, jpg, jpeg, png, gif, mp4, avi, mov, txt, zip, rar, csv.</div>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <button type="button" class="btn btn-secondary prev-btn">Previous</button>
                                    <button type="submit" class="btn btn-success">Submit Application</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4 mb-lg-0">
                    <div class="footer-info">
                        <p>Fostering innovation, entrepreneurship, and technological advancement to drive economic growth and development across Uganda and Africa.</p>
                        <div class="mission-box">
                            <h5>Our Mission</h5>
                            <p>Empowering the next generation of innovators through cutting-edge programs and mentorship.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-2 col-md-6 mb-4 mb-md-0">
                    <h5>Quick Links</h5>
                    <ul class="footer-links">
                        <li><a href="#">Programs</a></li>
                        <li><a href="#">Partnerships</a></li>
                        <li><a href="#">Mentorship</a></li>
                        <li><a href="#">Incubation</a></li>
                        <li><a href="#">News</a></li>
                        <li><a href="#">Gallery</a></li>
                    </ul>
                </div>
                <div class="col-lg-3 col-md-6 mb-4 mb-md-0">
                    <h5>Featured Programs</h5>
                    <ul class="footer-links">
                        <li><a href="#">TAG DEV 2.0</a></li>
                        <li><a href="#">SUESCA</a></li>
                        <li><a href="#">AI Innovation Challenge</a></li>
                        <li><a href="#">UNESCO Africa Engineering</a></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h5>Contact Us</h5>
                    <ul class="footer-contact">
                        <li><i class="fas fa-envelope"></i> innovation@umu.ac.ug</li>
                        <li><i class="fas fa-phone"></i> +256 XXX XXX XXX</li>
                        <li><i class="fas fa-map-marker-alt"></i> Nkozi, Uganda</li>
                    </ul>
                    <div class="social-links mt-3">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom text-center mt-4">
                <p>&copy; 2025 Uganda Martyrs University - Innovation Office. All rights reserved.</p>
            </div>
        </div>
    </footer>
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script>
    // Multi-step wizard logic
    const steps = Array.from(document.querySelectorAll('.form-step'));
    let currentStep = 0;
    function showStep(idx) {
        steps.forEach((step, i) => step.classList.toggle('active', i === idx));
    }
    function validateStep(idx) {
        let valid = true;
        const inputs = steps[idx].querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            if (input.hasAttribute('required') && !input.value) {
                input.classList.add('is-invalid');
                valid = false;
            } else {
                input.classList.remove('is-invalid');
            }
        });
        return valid;
    }
    document.querySelectorAll('.next-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            if (validateStep(currentStep)) {
                if (currentStep === 0) {
                    // Show only the selected category fields
                    const cat = document.getElementById('category').value;
                    ['student','staff','alumni','community'].forEach(c => {
                        document.getElementById(c+'Fields').style.display = (c === cat) ? 'block' : 'none';
                    });
                }
                currentStep++;
                showStep(currentStep);
            }
        });
    });
    document.querySelectorAll('.prev-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            currentStep--;
            showStep(currentStep);
        });
    });
    document.getElementById('applicationForm').addEventListener('submit', function(e) {
        if (!validateStep(currentStep)) {
            e.preventDefault();
        }
    });
    showStep(currentStep);
    // Graduation year and year of inception
    (function() {
        const grad = document.getElementById('graduation_year');
        const now = new Date().getFullYear();
        if (grad) {
            for (let y = now; y >= now - 50; y--) {
                const opt = document.createElement('option');
                opt.value = y;
                opt.textContent = y;
                grad.appendChild(opt);
            }
        }
        const inception = document.getElementById('year_of_inception');
        if (inception) {
            for (let y = now; y >= now - 20; y--) {
                const opt = document.createElement('option');
                opt.value = y;
                opt.textContent = y;
                inception.appendChild(opt);
            }
        }
    })();
    // Disability description toggle
    (function() {
        const yes = document.getElementById('disabilityYes');
        const no = document.getElementById('disabilityNo');
        const desc = document.getElementById('disabilityDescription');
        if (yes && no && desc) {
            yes.addEventListener('change', () => desc.style.display = 'block');
            no.addEventListener('change', () => desc.style.display = 'none');
        }
    })();
    // Dynamic Ugandan districts
    (function() {
        const country = document.getElementById('country');
        const district = document.getElementById('district');
        const ugandanDistricts = [
            "Abim", "Adjumani", "Agago", "Alebtong", "Amolatar", "Amudat", "Amuria", "Amuru", "Apac", "Arua",
            "Budaka", "Bududa", "Bugiri", "Bugweri", "Buhweju", "Buikwe", "Bukedea", "Bukomansimbi", "Bukwo", "Bulambuli",
            "Buliisa", "Bundibugyo", "Bunyangabu", "Bushenyi", "Busia", "Butaleja", "Butambala", "Butebo", "Buvuma", "Buyende",
            "Dokolo", "Gomba", "Gulu", "Hoima", "Ibanda", "Iganga", "Isingiro", "Jinja", "Kaabong", "Kabale",
            "Kabarole", "Kaberamaido", "Kagadi", "Kakumiro", "Kalangala", "Kaliro", "Kalungu", "Kampala", "Kamuli", "Kamwenge",
            "Kanungu", "Kapchorwa", "Kapelebyong", "Karenga", "Kasese", "Katakwi", "Kayunga", "Kazo", "Kibaale", "Kiboga",
            "Kibuku", "Kikuube", "Kiruhura", "Kiryandongo", "Kisoro", "Kitagwenda", "Kitgum", "Koboko", "Kole", "Kotido",
            "Kumi", "Kwania", "Kween", "Kyankwanzi", "Kyegegwa", "Kyenjojo", "Kyotera", "Lamwo", "Lira", "Luuka",
            "Luwero", "Lwengo", "Lyantonde", "Manafwa", "Maracha", "Masaka", "Masindi", "Mayuge", "Mbale", "Mbarara",
            "Mitooma", "Mityana", "Moroto", "Moyo", "Mpigi", "Mubende", "Mukono", "Nabilatuk", "Nakapiripirit", "Nakaseke",
            "Nakasongola", "Namayingo", "Namisindwa", "Namutumba", "Napak", "Nebbi", "Ngora", "Ntoroko", "Ntungamo", "Nwoya",
            "Omoro", "Otuke", "Oyam", "Pader", "Pakwach", "Pallisa", "Rakai", "Rubanda", "Rubirizi", "Rukiga",
            "Rukungiri", "Rwampara", "Sembabule", "Serere", "Sheema", "Sironko", "Soroti", "Tororo", "Wakiso", "Yumbe", "Zombo"
        ];
        if (country && district) {
            country.addEventListener('change', function() {
                district.innerHTML = '<option value="">Select District</option>';
                if (this.value === 'Uganda') {
                    district.removeAttribute('disabled');
                    ugandanDistricts.forEach(function(d) {
                        const opt = document.createElement('option');
                        opt.value = d;
                        opt.textContent = d;
                        district.appendChild(opt);
                    });
                } else {
                    district.setAttribute('disabled', 'disabled');
                }
            });
        }
    })();
    </script>
</body>
</html> 
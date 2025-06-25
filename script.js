function initializeSwiper() {
    const swiper = new Swiper('.hero-slider', {
        direction: 'horizontal',
        loop: true,
        autoplay: {
            delay: 5000,
            disableOnInteraction: false,
        },
        effect: 'fade',
        fadeEffect: {
            crossFade: true
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        pagination: {
            el: '.swiper-button-pagination',
            clickable: true,
        },
        keyboard: {
            enabled: true,
        },
    });
}

function initializeSmoothScrolling() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });
}

function initializeResponsiveNavigation() {
    const navbar = document.querySelector('.navbar');
    let lastScroll = 0;

    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;

        if (currentScroll > lastScroll && currentScroll > 100) {
            navbar.style.transform = 'translateY(-100%)';
        } else {
            navbar.style.transform = 'translateY(0)';
        }

        lastScroll = currentScroll;
    });
}

function initializeForm() {
    const form = document.getElementById('entrepreneurForm');
    const sections = form.querySelectorAll('.form-section');
    const categorySelect = document.getElementById('category');

    sections.forEach(section => {
        section.style.display = 'none';
    });
    sections[0].style.display = 'block';

    const graduationYearSelect = document.getElementById('graduationYear');
    const currentYear = new Date().getFullYear();
    for (let year = currentYear; year >= currentYear - 50; year--) {
        const option = document.createElement('option');
        option.value = year;
        option.textContent = year;
        graduationYearSelect.appendChild(option);
    }

    const yearInceptionSelect = document.getElementById('yearInception');
    for (let year = currentYear; year >= currentYear - 20; year--) {
        const option = document.createElement('option');
        option.value = year;
        option.textContent = year;
        yearInceptionSelect.appendChild(option);
    }

    categorySelect.addEventListener('change', function () {
        const selectedCategory = this.value;
        const studentFields = document.getElementById('studentFields');
        const staffFields = document.getElementById('staffFields');
        const alumniFields = document.getElementById('alumniFields');
        const communityFields = document.getElementById('communityFields');

        [studentFields, staffFields, alumniFields, communityFields].forEach(section => {
            section.style.display = 'none';
        });

        if (selectedCategory) {
            const selectedSection = document.getElementById(`${selectedCategory}Fields`);
            if (selectedSection) {
                selectedSection.style.display = 'block';
            }
        }
    });

    form.querySelectorAll('.next-btn').forEach(button => {
        button.addEventListener('click', function () {
            const currentSection = this.closest('.form-section');
            const currentCategory = categorySelect.value;

            if (currentSection.id === 'basicInfoSection' && currentCategory) {
                const categorySection = document.getElementById(`${currentCategory}Fields`);
                if (categorySection) {
                    currentSection.style.display = 'none';
                    categorySection.style.display = 'block';
                    return;
                }
            }

            if (currentSection.id.endsWith('Fields')) {
                const personalInfoSection = document.getElementById('personalInfoSection');
                if (personalInfoSection) {
                    currentSection.style.display = 'none';
                    personalInfoSection.style.display = 'block';
                    return;
                }
            }

            const nextSection = currentSection.nextElementSibling;
            if (nextSection && nextSection.classList.contains('form-section')) {
                currentSection.style.display = 'none';
                nextSection.style.display = 'block';
            }
        });
    });

    form.querySelectorAll('.prev-btn').forEach(button => {
        button.addEventListener('click', function () {
            const currentSection = this.closest('.form-section');

            if (currentSection.id === 'personalInfoSection') {
                const categorySection = document.getElementById(`${categorySelect.value}Fields`);
                if (categorySection) {
                    currentSection.style.display = 'none';
                    categorySection.style.display = 'block';
                    return;
                }
            }

            if (currentSection.id.endsWith('Fields')) {
                const basicInfoSection = document.getElementById('basicInfoSection');
                if (basicInfoSection) {
                    currentSection.style.display = 'none';
                    basicInfoSection.style.display = 'block';
                    return;
                }
            }

            const prevSection = currentSection.previousElementSibling;
            if (prevSection && prevSection.classList.contains('form-section')) {
                currentSection.style.display = 'none';
                prevSection.style.display = 'block';
            }
        });
    });

    const disabilityRadios = document.querySelectorAll('input[name="disability"]');
    const disabilityDescription = document.getElementById('disabilityDescription');

    disabilityRadios.forEach(radio => {
        radio.addEventListener('change', function () {
            disabilityDescription.style.display = this.value === 'yes' ? 'block' : 'none';
        });
    });

    form.addEventListener('submit', function (event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
            form.classList.add('was-validated');
            return;
        }
        // If valid, allow normal submission
    });
}

async function populateCountries() {
    const countrySelect = document.getElementById('country');
    if (!countrySelect) {
        console.error('Country select element not found');
        return;
    }

    try {
        console.log('Fetching countries...');
        const response = await fetch('https://countriesnow.space/api/v0.1/countries', {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        if (!data.data || !Array.isArray(data.data)) {
            throw new Error('Invalid response format');
        }
        
        const countries = data.data.map(country => country.country);
        console.log(`Successfully fetched ${countries.length} countries`);
        console.log('First few countries:', countries.slice(0, 3));

        // Clear existing options except the first one
        while (countrySelect.options.length > 1) {
            countrySelect.remove(1);
        }

        const sortedCountries = countries.sort((a, b) => a.localeCompare(b));

        sortedCountries.forEach(country => {
            const option = document.createElement('option');
            option.value = country;
            option.textContent = country;
            countrySelect.appendChild(option);
        });
        console.log('Countries populated successfully');
    } catch (error) {
        console.error('Error loading countries:', error);
        console.error('Error details:', {
            message: error.message,
            stack: error.stack
        });
        
        // Add a fallback option in case of error
        const option = document.createElement('option');
        option.value = 'Uganda';
        option.textContent = 'Uganda';
        countrySelect.appendChild(option);
    }
}

function initializeCountrySelection() {
    const countrySelect = document.getElementById('country');
    const districtSelect = document.getElementById('district');

    // List of Ugandan districts
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

    countrySelect.addEventListener('change', function () {
        // Clear existing district options
        districtSelect.innerHTML = '<option value="">Select District</option>';
        
        if (this.value === 'Uganda') {
            districtSelect.removeAttribute('disabled');
            
            // Sort districts alphabetically
            const sortedDistricts = ugandanDistricts.sort();
            
            // Add district options
            sortedDistricts.forEach(district => {
                const option = document.createElement('option');
                option.value = district;
                option.textContent = district;
                districtSelect.appendChild(option);
            });
        } else {
            districtSelect.setAttribute('disabled', 'disabled');
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    initializeSwiper();
    initializeSmoothScrolling();
    initializeResponsiveNavigation();
    initializeForm();
    populateCountries();
    initializeCountrySelection();

    const urlParams = new URLSearchParams(window.location.search);
    const program = urlParams.get('program');
    if (program) {
        const programInput = document.getElementById('program');
        if (programInput) programInput.value = program;
    }
});
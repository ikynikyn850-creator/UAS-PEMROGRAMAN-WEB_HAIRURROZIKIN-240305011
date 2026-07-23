// ===== NAVBAR SCROLL EFFECT =====
const navbar = document.getElementById('navbar');
const scrollTopBtn = document.getElementById('scrollTopBtn');

// ===== GALERI FOTO KEGIATAN =====
const uploadedGallery = document.getElementById('uploadedGallery');

if (uploadedGallery) {
    const galleryRequest = fetch('../galeri_portfolio.php', { credentials: 'same-origin' });
    const galleryTimeout = new Promise((_, reject) => {
        setTimeout(() => reject(new Error('Waktu permintaan habis.')), 8000);
    });

    Promise.race([galleryRequest, galleryTimeout])
        .then(response => {
            if (response.status === 401) {
                throw new Error('Silakan login terlebih dahulu untuk melihat galeri kegiatan.');
            }
            if (!response.ok) {
                throw new Error('Galeri tidak dapat dimuat.');
            }
            return response.json();
        })
        .then(photos => {
            if (!photos.length) {
                uploadedGallery.innerHTML = '<div class="col-12"><p class="text-center text-muted py-4">Belum ada foto kegiatan yang diunggah.</p></div>';
                return;
            }

            uploadedGallery.innerHTML = photos.map(photo => `
                <div class="portfolio-item">
                    <div class="portfolio-card uploaded-photo-card">
                        <div class="portfolio-image">
                            <img src="../uploads/poto/${encodeURIComponent(photo.nama_file)}" alt="${escapeHtml(photo.nama_kegiatan)}">
                        </div>
                        <div class="portfolio-content">
                            <h5 class="fw-bold mb-2">${escapeHtml(photo.nama_kegiatan)}</h5>
                            <p class="text-muted mb-2">${escapeHtml(photo.deskripsi || 'Tanpa deskripsi')}</p>
                            <small class="text-secondary"><i class="fas fa-calendar me-1"></i>${formatUploadDate(photo.tanggal_upload)}</small>
                        </div>
                    </div>
                </div>
            `).join('');

            uploadedGallery.querySelectorAll('.portfolio-card').forEach(card => observer.observe(card));
        })
        .catch(error => {
            uploadedGallery.innerHTML = `<div class="col-12"><p class="text-center text-muted py-4">${escapeHtml(error.message)}</p></div>`;
        });
}

function escapeHtml(value) {
    return String(value).replace(/[&<>'"]/g, character => ({
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        "'": '&#039;',
        '"': '&quot;'
    }[character]));
}

function formatUploadDate(value) {
    const date = new Date(value.replace(' ', 'T'));
    return Number.isNaN(date.getTime()) ? value : date.toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'short',
        year: 'numeric'
    });
}

window.addEventListener('scroll', () => {
    // Navbar scroll effect
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }

    // Show/Hide scroll to top button
    if (window.scrollY > 300) {
        scrollTopBtn.classList.add('show');
    } else {
        scrollTopBtn.classList.remove('show');
    }

    // Update active nav link
    updateActiveNavLink();
});

// ===== SCROLL TO TOP =====
scrollTopBtn.addEventListener('click', () => {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
});

// ===== SMOOTH SCROLL FOR NAV LINKS =====
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        const href = this.getAttribute('href');
        if (href !== '#') {
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }
    });
});

// ===== UPDATE ACTIVE NAV LINK =====
function updateActiveNavLink() {
    const sections = document.querySelectorAll('section');
    const navLinks = document.querySelectorAll('.nav-link');

    let current = '';
    sections.forEach(section => {
        const sectionTop = section.offsetTop;
        if (window.pageYOffset >= sectionTop - 200) {
            current = section.getAttribute('id');
        }
    });

    navLinks.forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === `#${current}`) {
            link.classList.add('active');
        }
    });
}

// ===== CONTACT FORM SUBMISSION =====
const contactForm = document.getElementById('contactForm');

if (contactForm) {
    contactForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const subject = document.getElementById('subject').value;
        const message = document.getElementById('message').value;

        // Validasi form
        if (!name || !email || !subject || !message) {
            showAlert('Silakan isi semua field!', 'warning');
            return;
        }

        // Validasi email
        if (!isValidEmail(email)) {
            showAlert('Email tidak valid!', 'warning');
            return;
        }

        // Simulasi pengiriman form
        const submitBtn = contactForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';

        // Simulasi delay
        setTimeout(() => {
            showAlert('Pesan Anda berhasil dikirim! Terima kasih telah menghubungi saya.', 'success');
            contactForm.reset();
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }, 1500);
    });
}

// ===== EMAIL VALIDATION =====
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// ===== SHOW ALERT =====
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show fixed-top mt-3`;
    alertDiv.style.zIndex = '10000';
    alertDiv.style.marginLeft = '1rem';
    alertDiv.style.marginRight = '1rem';
    alertDiv.style.maxWidth = '400px';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;

    document.body.appendChild(alertDiv);

    // Auto dismiss after 5 seconds
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

// ===== INTERSECTION OBSERVER FOR ANIMATIONS =====
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -100px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('fade-in');
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

// Observe skill cards
document.querySelectorAll('.skill-card').forEach(card => {
    observer.observe(card);
});

// Observe portfolio cards
document.querySelectorAll('.portfolio-card').forEach(card => {
    observer.observe(card);
});

// Observe sections
document.querySelectorAll('.section-title').forEach(title => {
    observer.observe(title);
});

// ===== PROGRESS BAR ANIMATION =====
function animateProgressBars() {
    const progressBars = document.querySelectorAll('.progress-bar');
    progressBars.forEach(bar => {
        const width = bar.style.width;
        bar.style.width = '0';
        setTimeout(() => {
            bar.style.width = width;
        }, 100);
    });
}

// Animate progress bars when skills section is visible
const skillsSection = document.getElementById('skills');
if (skillsSection) {
    const skillsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateProgressBars();
                skillsObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    skillsObserver.observe(skillsSection);
}

// ===== CLOSE MOBILE MENU AFTER CLICK =====
const navbarCollapse = document.querySelector('.navbar-collapse');
const navbarToggler = document.querySelector('.navbar-toggler');

document.querySelectorAll('.nav-link').forEach(link => {
    link.addEventListener('click', () => {
        if (window.innerWidth < 992) {
            navbarToggler.click();
        }
    });
});

// ===== RIPPLE EFFECT ON BUTTONS =====
document.querySelectorAll('.btn').forEach(button => {
    button.addEventListener('click', function(e) {
        const ripple = document.createElement('span');
        const rect = this.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;

        ripple.style.width = ripple.style.height = size + 'px';
        ripple.style.left = x + 'px';
        ripple.style.top = y + 'px';
        ripple.classList.add('ripple');

        this.appendChild(ripple);

        setTimeout(() => {
            ripple.remove();
        }, 600);
    });
});

// ===== INITIALIZE AOS-LIKE ANIMATIONS =====
function initAnimations() {
    const elements = document.querySelectorAll('[class*="slide-in"], [class*="fade-in"]');
    
    elements.forEach((el, index) => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            el.style.transition = 'all 0.6s ease-out';
            el.style.opacity = '1';
            el.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

// Initialize on page load
window.addEventListener('load', () => {
    initAnimations();
    updateActiveNavLink();
});

// ===== TYPEWRITER EFFECT =====
function typeWriter(element, text, speed = 50) {
    let i = 0;
    element.textContent = '';
    
    function type() {
        if (i < text.length) {
            element.textContent += text.charAt(i);
            i++;
            setTimeout(type, speed);
        }
    }
    
    type();
}

// ===== COUNTER ANIMATION =====
function animateCounter(element, target, duration = 2000) {
    let current = 0;
    const increment = target / (duration / 16);
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            element.textContent = target;
            clearInterval(timer);
        } else {
            element.textContent = Math.floor(current);
        }
    }, 16);
}

// ===== PARALLAX EFFECT =====
window.addEventListener('scroll', () => {
    const parallaxElements = document.querySelectorAll('[data-parallax]');
    parallaxElements.forEach(element => {
        const scrollPosition = window.scrollY;
        const speed = element.dataset.parallax || 0.5;
        element.style.transform = `translateY(${scrollPosition * speed}px)`;
    });
});

// ===== KEYBOARD SHORTCUTS =====
document.addEventListener('keydown', (e) => {
    // Press 'h' to go home
    if (e.key === 'h' || e.key === 'H') {
        document.querySelector('#home').scrollIntoView({ behavior: 'smooth' });
    }
    // Press 'c' to go to contact
    if (e.key === 'c' || e.key === 'C') {
        document.querySelector('#contact').scrollIntoView({ behavior: 'smooth' });
    }
});

// ===== CONSOLE MESSAGE =====
console.log('%cWelcome to Ikin Portfolio!', 'color: #0d6efd; font-size: 24px; font-weight: bold;');
console.log('%cMade with ❤️ using HTML, CSS & JavaScript', 'color: #ff006e; font-size: 14px;');
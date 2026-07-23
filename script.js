document.addEventListener('DOMContentLoaded', function () {
    // Sidebar Toggle dengan animasi smooth
    var sidebarToggle = document.getElementById('sidebarToggle');
    var sidebar = document.querySelector('.sidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('show');
        });
    }

    // Efek Cahaya Klik (Click Glow Effect) - Enhanced
    document.addEventListener('click', function(e) {
        // Cek apakah yang diklik adalah button, link, atau elemen interaktif
        if (e.target.closest('.btn') || e.target.closest('.nav-link') || e.target.closest('a') || e.target.closest('.card')) {
            createClickGlow(e.clientX, e.clientY);
        }
    });

    function createClickGlow(x, y) {
        const glow = document.createElement('div');
        glow.style.position = 'fixed';
        glow.style.width = '0px';
        glow.style.height = '0px';
        glow.style.borderRadius = '50%';
        glow.style.pointerEvents = 'none';
        glow.style.left = x + 'px';
        glow.style.top = y + 'px';
        glow.style.transform = 'translate(-50%, -50%)';
        glow.style.boxShadow = '0 0 0 0 rgba(139, 92, 246, 0.9)';
        glow.style.zIndex = '9999';
        
        document.body.appendChild(glow);

        // Animate glow dengan kurva yang lebih smooth
        let size = 0;
        let opacity = 0.9;
        const maxSize = 200;
        const duration = 700;
        const startTime = performance.now();

        function animateGlow(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = elapsed / duration;
            const easeOut = 1 - Math.pow(1 - progress, 3);

            if (progress < 1) {
                size = easeOut * maxSize;
                opacity = 0.9 * (1 - progress);
                glow.style.boxShadow = `0 0 20px ${size}px rgba(139, 92, 246, ${opacity}), 0 0 40px ${size * 0.5}px rgba(168, 85, 247, ${opacity * 0.5})`;
                requestAnimationFrame(animateGlow);
            } else {
                glow.remove();
            }
        }

        requestAnimationFrame(animateGlow);
    }

    // Smooth Scroll untuk anchor links
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

    // Tambah animasi ke card saat loading
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.animation = `fadeInUp 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) ${index * 0.1}s both`;
    });

    // Enhanced hover effect ke table rows
    const tableRows = document.querySelectorAll('.table-hover tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.background = 'rgba(139, 92, 246, 0.15)';
            this.style.boxShadow = '0 4px 12px rgba(139, 92, 246, 0.1)';
        });
        row.addEventListener('mouseleave', function() {
            this.style.background = '';
            this.style.boxShadow = '';
        });
    });

    // Alert auto dismiss dengan animasi
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert) {
                alert.style.animation = 'slideInDown 0.5s ease-out forwards reverse';
                setTimeout(() => alert.remove(), 500);
            }
        }, 5000);
    });

    // Efek ripple pada tombol
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
            ripple.className = 'ripple';
            
            this.appendChild(ripple);

            setTimeout(() => ripple.remove(), 600);
        });
    });

    // Parallax effect untuk card pada scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.animation = 'scaleIn 0.5s cubic-bezier(0.34, 1.56, 0.64, 1) forwards';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.card').forEach(card => {
        observer.observe(card);
    });
});

// Animasi CSS
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
        }
    }

    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
`;
document.head.appendChild(style);

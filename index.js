document.addEventListener('DOMContentLoaded', function() {
    // Mobile menu toggle
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const navLinks = document.querySelector('.nav-links');
    
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            navLinks.classList.toggle('active');
            this.classList.toggle('active');
        });
    }
    
    // Testimonial slider
    const dots = document.querySelectorAll('.dot');
    const testimonials = document.querySelectorAll('.testimonial');
    
    if (dots.length && testimonials.length) {
        dots.forEach((dot, index) => {
            dot.addEventListener('click', function() {
                // Hide all testimonials
                testimonials.forEach(testimonial => {
                    testimonial.style.display = 'none';
                });
                
                // Remove active class from all dots
                dots.forEach(d => {
                    d.classList.remove('active');
                });
                
                // Show current testimonial and activate dot
                testimonials[index].style.display = 'block';
                dot.classList.add('active');
            });
        });
        
        // Auto-slide testimonials every 5 seconds
        let currentSlide = 0;
        
        function showNextSlide() {
            testimonials.forEach(testimonial => {
                testimonial.style.display = 'none';
            });
            
            dots.forEach(dot => {
                dot.classList.remove('active');
            });
            
            currentSlide = (currentSlide + 1) % testimonials.length;
            testimonials[currentSlide].style.display = 'block';
            dots[currentSlide].classList.add('active');
        }
        
        // Initially show first testimonial
        testimonials[0].style.display = 'block';
        dots[0].classList.add('active');
        
        // Start auto-slide
        setInterval(showNextSlide, 5000);
    }
    
    // Sticky navbar
    const navbar = document.querySelector('.nav-bar');
    
    if (navbar) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });
    }
    
    // Animation on scroll
    const animateElements = document.querySelectorAll('.feature-card, .step, .gallery-item');
    
    function checkIfInView() {
        animateElements.forEach(element => {
            const elementTop = element.getBoundingClientRect().top;
            const elementVisible = 150;
            
            if (elementTop < window.innerHeight - elementVisible) {
                element.classList.add('animate');
            }
        });
    }
    
    window.addEventListener('scroll', checkIfInView);
    checkIfInView();
});

/* Animation styles */
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

.feature-card, .step, .gallery-item {
    opacity: 0;
    transform: translateY(20px);
    transition: var(--transition);
}

.feature-card.animate, .step.animate, .gallery-item.animate {
    opacity: 1;
    transform: translateY(0);
}

.nav-bar.scrolled {
    padding: 0.5rem 0;
    background-color: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
}

.nav-links.active {
    display: flex;
    flex-direction: column;
    position: absolute;
    top: 70px;
    left: 0;
    right: 0;
    background: white;
    padding: 20px;
    box-shadow: 0 10px 15px rgba(0,0,0,0.1);
    animation: fadeInDown 0.5s ease;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.mobile-menu-toggle.active span:nth-child(1) {
    transform: rotate(45deg) translate(5px, 6px);
}

.mobile-menu-toggle.active span:nth-child(2) {
    opacity: 0;
}

.mobile-menu-toggle.active span:nth-child(3) {
    transform: rotate(-45deg) translate(5px, -6px);
}
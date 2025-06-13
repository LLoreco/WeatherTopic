document.addEventListener('DOMContentLoaded', () => {
    // Check if user is already logged in
    const token = localStorage.getItem('token');
    if (token) {
        window.location.href = 'weather.html';
    }

    // Tab switching
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const tabId = btn.dataset.tab;
            
            // Update active button
            tabBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            // Show selected content
            tabContents.forEach(content => {
                content.classList.add('hidden');
                if (content.id === tabId) {
                    content.classList.remove('hidden');
                }
            });
        });
    });

    // Login form handling
    const loginForm = document.getElementById('loginForm');
    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const email = document.getElementById('loginEmail').value;
        const password = document.getElementById('loginPassword').value;

        try {
            const response = await fetch('/api/auth/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email, password })
            });

            const data = await response.json();

            if (response.ok) {
                localStorage.setItem('token', data.token);
                window.location.href = 'weather.html';
            } else {
                alert(data.message || 'Login failed');
            }
        } catch (error) {
            alert('An error occurred. Please try again.');
        }
    });

    // Register form handling
    const registerForm = document.getElementById('registerForm');
    registerForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const name = document.getElementById('registerName').value;
        const email = document.getElementById('registerEmail').value;
        const password = document.getElementById('registerPassword').value;

        try {
            const response = await fetch('/api/auth/register', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ name, email, password })
            });

            const data = await response.json();

            if (response.ok) {
                alert('Registration successful! Please login.');
                // Switch to login tab
                document.querySelector('[data-tab="login"]').click();
            } else {
                alert(data.message || 'Registration failed');
            }
        } catch (error) {
            alert('An error occurred. Please try again.');
        }
    });
}); 
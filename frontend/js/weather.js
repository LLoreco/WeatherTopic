document.addEventListener('DOMContentLoaded', () => {
    // Check if user is logged in
    const token = localStorage.getItem('token');
    if (!token) {
        window.location.href = 'index.html';
        return;
    }

    // Logout handling
    const logoutBtn = document.getElementById('logoutBtn');
    logoutBtn.addEventListener('click', () => {
        localStorage.removeItem('token');
        window.location.href = 'index.html';
    });

    // Weather form handling
    const weatherForm = document.getElementById('weatherForm');
    const weatherInfo = document.getElementById('weatherInfo');
    const errorMessage = document.getElementById('errorMessage');

    weatherForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const city = document.getElementById('cityInput').value;
        errorMessage.classList.add('hidden');
        weatherInfo.classList.add('hidden');

        try {
            const response = await fetch(`/api/weather?city=${encodeURIComponent(city)}`, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            const data = await response.json();

            if (response.ok) {
                // Update weather information
                document.getElementById('cityName').textContent = data.city;
                document.getElementById('temperature').textContent = data.temperature;
                document.getElementById('description').textContent = data.description;
                document.getElementById('weatherIcon').src = data.icon;
                document.getElementById('humidity').textContent = `${data.humidity}%`;
                document.getElementById('windSpeed').textContent = `${data.windSpeed} m/s`;

                weatherInfo.classList.remove('hidden');
            } else {
                errorMessage.textContent = data.message || 'Failed to fetch weather data';
                errorMessage.classList.remove('hidden');
            }
        } catch (error) {
            errorMessage.textContent = 'An error occurred. Please try again.';
            errorMessage.classList.remove('hidden');
        }
    });
}); 
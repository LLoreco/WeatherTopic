document.addEventListener('DOMContentLoaded', () => {
    const token = localStorage.getItem('token');
    if (!token) {
        window.location.href = 'index.html';
        return;
    }
    console.log('Token:', token);

    const logoutBtn = document.getElementById('logoutBtn');
    logoutBtn.addEventListener('click', () => {
        localStorage.removeItem('token');
        window.location.href = 'index.html';
    });

    const weatherForm = document.getElementById('weatherForm');
    const weatherInfo = document.getElementById('weatherInfo');
    const errorMessage = document.getElementById('errorMessage');

    weatherForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const city = document.getElementById('cityInput').value;
        errorMessage.classList.add('hidden');
        weatherInfo.classList.add('hidden');

        try {
            const response = await fetch(`http://localhost:3000/backend/public/weather.php?city=${encodeURIComponent(city)}`, {
                headers: {
                    'Authorization': `Bearer ${token}`
                }
            });

            const text = await response.text();
            console.log('Raw response:', text);
            const data = JSON.parse(text);

            if (response.ok) {
                document.getElementById('cityName').textContent = data.data.city;
                document.getElementById('temperature').textContent = data.data.temperature;
                document.getElementById('description').textContent = data.data.description;
                document.getElementById('weatherIcon').src = `http:${data.data.icon}`;
                document.getElementById('humidity').textContent = `${data.data.humidity}%`;
                document.getElementById('windSpeed').textContent = `${data.data.windSpeed} m/s`;

                weatherInfo.classList.remove('hidden');
            } else {
                errorMessage.textContent = data.message || 'Failed to fetch weather data';
                errorMessage.classList.remove('hidden');
            }
        } catch (error) {
            errorMessage.textContent = 'An error occurred. Please try again.';
            errorMessage.classList.remove('hidden');
            console.error('Weather fetch error:', error, error.stack);
        }
    });
});
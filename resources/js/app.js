document.addEventListener('livewire:init', () => {
    const getLocation = () => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    Livewire.first().dispatch('set-location', {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    });
                },
                (error) => {
                    console.error('GPS Error:', error.message);
                },
                { enableHighAccuracy: true }
            );
        }
    };

    getLocation();
    Livewire.on('get-location', () => {
        getLocation();
    });
});
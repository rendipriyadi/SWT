document.addEventListener('DOMContentLoaded', function() {
    console.log('area-station.js loaded');

    const areaSelect = document.getElementById('area_id');
    const stationSelect = document.getElementById('penanggung_jawab_id');
    const supervisorInput = document.getElementById('supervisor');

    // Fetch-based population from backend
    let fetchedStations = [];
    async function fetchStations(areaId) {
        try {
            const response = await fetch(`/get-penanggung-jawab/${areaId}`);
            if (!response.ok) throw new Error('Failed to fetch stations');
            return await response.json();
        } catch (e) {
            console.error(e);
            return { stations: [], group_members: [] };
        }
    }

    // Update station dropdown berdasarkan area yang dipilih
    async function updateStations(areaId) {
        // Reset station dropdown
        stationSelect.innerHTML = '<option value="">Select Station</option>';
        
        // Jika tidak ada area yang dipilih
        if (!areaId) {
            supervisorInput.value = '';
            return;
        }
        const data = await fetchStations(areaId);
        supervisorInput.value = (data.group_members || []).join(', ');
        fetchedStations = data.stations || [];
        fetchedStations.forEach(station => {
            const option = document.createElement('option');
            option.value = station.id;
            option.textContent = station.station;
            stationSelect.appendChild(option);
        });
        
        console.log(`Stations updated for area ID: ${areaId}`);
        
        // Jika ini adalah form edit, coba pilih station yang tersimpan
        const selectedStation = stationSelect.getAttribute('data-selected');
        if (selectedStation) {
            const options = stationSelect.querySelectorAll('option');
            for (let option of options) {
                if (option.value === selectedStation) {
                    option.selected = true;
                    updateSupervisor(selectedStation, areaId);
                    break;
                }
            }
        }
    }

    // Update supervisor berdasarkan station yang dipilih
    function updateSupervisor(stationId, areaId) {
        if (!stationId || stationId === '') {
            supervisorInput.value = supervisorInput.value; // keep group list
            return;
        }
        const match = fetchedStations.find(s => String(s.id) === String(stationId));
        if (match) {
            supervisorInput.value = match.name || '';
        }
    }

    // Event listeners
    if (areaSelect) {
        areaSelect.addEventListener('change', function() {
            const areaId = this.value;
            updateStations(areaId);
        });
    }
    
    if (stationSelect) {
        stationSelect.addEventListener('change', function() {
            const stationId = this.value;
            const areaId = areaSelect.value;
            updateSupervisor(stationId, areaId);
        });
    }
    
    // On submit, if station is not chosen, avoid sending an empty value
    const form = document.getElementById('reportForm');
    if (form && stationSelect) {
        form.addEventListener('submit', function() {
            if (!stationSelect.value) {
                stationSelect.removeAttribute('name');
            }
        });
    }
    
    // Initialize form if values exist (important for edit form and validation errors)
    if (areaSelect && areaSelect.value) {
        updateStations(areaSelect.value);
        
        // Delay to ensure DOM is fully loaded
        setTimeout(() => {
            // If form has station pre-selected (from old() or edit form)
            const selectedStation = stationSelect.getAttribute('data-selected');
            if (selectedStation) {
                const options = stationSelect.querySelectorAll('option');
                for (let option of options) {
                    if (option.value === selectedStation) {
                        option.selected = true;
                        updateSupervisor(selectedStation, areaSelect.value);
                        break;
                    }
                }
            }
        }, 200); // Increased delay for better reliability
    }
});
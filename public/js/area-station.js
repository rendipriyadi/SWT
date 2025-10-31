document.addEventListener('DOMContentLoaded', function() {

    const areaSelect = document.getElementById('area_id');
    const stationSelect = document.getElementById('penanggung_jawab_id');
    const supervisorInput = document.getElementById('supervisor');

    // Cache for API responses
    const stationCache = new Map();
    let fetchedStations = [];
    let currentRequest = null; // Track ongoing request

    // Fetch-based population from backend using POST (secure + optimized)
    async function fetchStations(areaId) {
        // Check cache first
        if (stationCache.has(areaId)) {
            return stationCache.get(areaId);
        }

        // Cancel previous request if still pending
        if (currentRequest) {
            currentRequest.abort();
        }

        try {
            // Create AbortController for timeout
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), 10000); // 10s timeout
            currentRequest = controller;

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            const response = await fetch('/api/stations', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ area_id: areaId }),
                signal: controller.signal
            });

            clearTimeout(timeoutId);
            currentRequest = null;

            if (!response.ok) throw new Error("Failed to fetch stations");
            
            const data = await response.json();
            
            // Cache the response
            stationCache.set(areaId, data);
            
            return data;
        } catch (e) {
            return { stations: [], group_members: [] };
        }
    }

    // Update station dropdown berdasarkan area yang dipilih
    async function updateStations(areaId) {
        // Jika tidak ada area yang dipilih
        if (!areaId) {
            stationSelect.innerHTML = '<option value="">Select Station</option>';
            supervisorInput.value = '';
            stationSelect.disabled = false;
            return;
        }

        // Show loading state
        stationSelect.innerHTML = '<option value="">Loading stations...</option>';
        stationSelect.disabled = true;
        
        const data = await fetchStations(areaId);
        
        // Reset and populate
        stationSelect.innerHTML = '<option value="">Select Station</option>';
        stationSelect.disabled = false;
        
        supervisorInput.value = (data.group_members || []).join(', ');
        fetchedStations = data.stations || [];
        
        // Use DocumentFragment for better performance
        const fragment = document.createDocumentFragment();
        fetchedStations.forEach(station => {
            const option = document.createElement('option');
            option.value = station.id;
            option.textContent = station.station;
            fragment.appendChild(option);
        });
        stationSelect.appendChild(fragment);
        
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
    async function updateSupervisor(stationId, areaId) {
        if (!stationId || stationId === '') {
            // Ketika "Select Station" dipilih, tampilkan semua PIC dari area
            if (areaId) {
                const data = await fetchStations(areaId);
                supervisorInput.value = (data.group_members || []).join(', ');
            } else {
                supervisorInput.value = '';
            }
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
    
    // Station field is nullable in validation, no need to remove name attribute
    // const form = document.getElementById('reportForm');
    // if (form && stationSelect) {
    //     form.addEventListener('submit', function() {
    //         if (!stationSelect.value) {
    //             stationSelect.removeAttribute('name');
    //         }
    //     });
    // }
    
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
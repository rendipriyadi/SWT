document.addEventListener('DOMContentLoaded', function() {

    const areaSelect = document.getElementById('area_id');
    const stationSelect = document.getElementById('penanggung_jawab_id');
    const supervisorInput = document.getElementById('supervisor');
    const supervisorBadges = document.getElementById('supervisor-badges');

    // Cache for API responses
    const stationCache = new Map();
    let fetchedStations = [];
    let currentRequest = null; // Track ongoing request
    
    // Helper function to update supervisor display (input or badges)
    function updateSupervisorDisplay(names) {
        if (supervisorInput) {
            // For input field (create report, etc)
            supervisorInput.value = names.join(', ');
        }
        if (supervisorBadges) {
            // For badge display (edit report)
            if (names.length === 0) {
                supervisorBadges.innerHTML = '<span class="text-muted small">No person in charge assigned</span>';
            } else {
                supervisorBadges.innerHTML = names.map(name => 
                    `<span class="badge bg-secondary fs-6 py-2 px-3">
                        <i class="fas fa-user me-1"></i>${name}
                    </span>`
                ).join('');
            }
        }
    }

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
            
            // Use global route configuration
            const url = window.routes?.stations || '/api/stations';
            
            const response = await fetch(url, {
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
            console.error('Error fetching stations for area:', areaId, e);
            return { stations: [], group_members: [] };
        }
    }

    // Update station dropdown berdasarkan area yang dipilih
    async function updateStations(areaId) {
        // Jika tidak ada area yang dipilih
        if (!areaId) {
            stationSelect.innerHTML = '<option value="">Select Station</option>';
            updateSupervisorDisplay([]);
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
        
        // Area IDs yang harus mengecualikan station "General"
        // 1 = Manufacture, 2 = Quality Control, 3 = Warehouse
        const excludeGeneralAreaIds = ['1', '2', '3'];
        const shouldExcludeGeneral = excludeGeneralAreaIds.includes(String(areaId));
        
        // Filter stations dan group_members untuk mengecualikan "General"
        fetchedStations = data.stations || [];
        let filteredStations = fetchedStations;
        let filteredGroupMembers = data.group_members || [];
        
        if (shouldExcludeGeneral) {
            // Filter stations untuk exclude "General"
            filteredStations = fetchedStations.filter(station => 
                !(station.station && station.station.toLowerCase() === 'general')
            );
            
            // Filter group_members untuk exclude nama dari station "General"
            const generalStationNames = fetchedStations
                .filter(station => station.station && station.station.toLowerCase() === 'general')
                .map(station => station.name);
            
            filteredGroupMembers = filteredGroupMembers.filter(name => 
                !generalStationNames.includes(name)
            );
        }
        
        updateSupervisorDisplay(filteredGroupMembers);
        
        // Use DocumentFragment for better performance
        const fragment = document.createDocumentFragment();
        filteredStations.forEach(station => {
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
                
                // Area IDs yang harus mengecualikan station "General"
                const excludeGeneralAreaIds = ['1', '2', '3'];
                const shouldExcludeGeneral = excludeGeneralAreaIds.includes(String(areaId));
                
                let groupMembers = data.group_members || [];
                
                if (shouldExcludeGeneral) {
                    // Filter group_members untuk exclude nama dari station "General"
                    const allStations = data.stations || [];
                    const generalStationNames = allStations
                        .filter(station => station.station && station.station.toLowerCase() === 'general')
                        .map(station => station.name);
                    
                    groupMembers = groupMembers.filter(name => 
                        !generalStationNames.includes(name)
                    );
                }
                
                updateSupervisorDisplay(groupMembers);
            } else {
                updateSupervisorDisplay([]);
            }
            return;
        }
        const match = fetchedStations.find(s => String(s.id) === String(stationId));
        if (match) {
            updateSupervisorDisplay([match.name || '']);
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
document.addEventListener('DOMContentLoaded', function() {
    console.log('area-station.js loaded');

    const areaSelect = document.getElementById('area_id');
    const stationSelect = document.getElementById('penanggung_jawab_id');
    const supervisorInput = document.getElementById('supervisor');

    // Data mapping area ke stations dan penanggung jawab
    const areaStationMap = {
        // Manufaktur (ID: 1)
        '1': {
            allSupervisors: 'Aris Setiawan, Rachmad Haryono, Hadi Djohansyah, Helmy Sundani, Sarifudin Raysan, Bayu Putra Trianto, Joni Rahman, Tri Widardi, Asept Surachaman',
            stations: [
                { id: 1, name: 'LV Assembly', supervisor: 'Aris Setiawan' },
                { id: 2, name: 'LV Box', supervisor: 'Rachmad Haryono' },
                { id: 3, name: 'LV Module', supervisor: 'Hadi Djohansyah' },
                { id: 4, name: 'MV Assembly', supervisor: 'Helmy Sundani' },
                { id: 5, name: 'Prefabrication', supervisor: 'Sarifudin Raysan' },
                { id: 6, name: 'Packing', supervisor: 'Bayu Putra Trianto' },
                { id: 7, name: 'Tool Store', supervisor: 'Joni Rahman' },
                { id: 8, name: 'General', supervisor: 'Tri Widardi' },
                { id: 9, name: 'General', supervisor: 'Asept Surachaman' }
            ]
        },
        // QC (ID: 2)
        '2': {
            allSupervisors: 'Ishak Marthen, Sirad Nova Mihardi, Arif Hadi Rizali',
            stations: [
                { id: 10, name: 'QC LV', supervisor: 'Ishak Marthen' },
                { id: 11, name: 'QC MV', supervisor: 'Sirad Nova Mihardi' },
                { id: 12, name: 'IQC', supervisor: 'Abduh Al Agani' },
                { id: 13, name: 'General', supervisor: 'Arif Hadi Rizali' }
            ]
        },
        // Warehouse (ID: 3)
        '3': {
            allSupervisors: 'Suhendra, Wahyu Wahyudin',
            stations: [
                { id: 14, name: 'Warehouse', supervisor: 'Suhendra' },
                { id: 15, name: 'Warehouse', supervisor: 'Wahyu Wahyudin' }
            ]
        }
    };

    // Update station dropdown berdasarkan area yang dipilih
    function updateStations(areaId) {
        // Reset station dropdown
        stationSelect.innerHTML = '<option value="">Pilih Station</option>';
        
        // Jika tidak ada area yang dipilih
        if (!areaId) {
            supervisorInput.value = '';
            return;
        }
        
        // Tampilkan semua penanggung jawab area
        supervisorInput.value = areaStationMap[areaId].allSupervisors;
        
        // Isi dropdown stations
        const stations = areaStationMap[areaId].stations;
        stations.forEach(station => {
            const option = document.createElement('option');
            option.value = station.id;
            option.textContent = station.name;
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
            // Jika station tidak dipilih, tampilkan semua supervisor dari area
            if (areaId) {
                supervisorInput.value = areaStationMap[areaId].allSupervisors;
            } else {
                supervisorInput.value = '';
            }
            return;
        }
        
        // Temukan supervisor untuk station yang dipilih
        const area = areaStationMap[areaId];
        if (!area) return;
        
        const station = area.stations.find(s => s.id == stationId);
        if (station) {
            supervisorInput.value = station.supervisor;
            console.log(`Supervisor updated for station ID: ${stationId} - ${station.supervisor}`);
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
    
    // Initialize form if values exist (important for edit form)
    if (areaSelect && areaSelect.value) {
        updateStations(areaSelect.value);
        
        // Delay to ensure DOM is fully loaded
        setTimeout(() => {
            // If edit form has station pre-selected
            if (stationSelect.getAttribute('data-selected')) {
                const selectedStation = stationSelect.getAttribute('data-selected');
                const options = stationSelect.querySelectorAll('option');
                for (let option of options) {
                    if (option.value === selectedStation) {
                        option.selected = true;
                        updateSupervisor(selectedStation, areaSelect.value);
                        break;
                    }
                }
            }
        }, 100);
    }
});
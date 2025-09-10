document.addEventListener('DOMContentLoaded', function() {
    console.log('filter-area-station.js loaded');

    const areaSelect = document.getElementById('area_id');
    const stationSelect = document.getElementById('penanggung_jawab_id');

    if (!areaSelect || !stationSelect) return;

    // Data mapping area ke stations dan penanggung jawab
    const areaStationMap = {
        // Manufaktur (ID: 1)
        '1': {
            stations: [
                { id: 1, name: 'LV Assembly' },
                { id: 2, name: 'LV Box' },
                { id: 3, name: 'LV Module' },
                { id: 4, name: 'MV Assembly' },
                { id: 5, name: 'Prefabrication' },
                { id: 6, name: 'Packing' },
                { id: 7, name: 'Tool Store' },
                { id: 8, name: 'General' },
                { id: 9, name: 'General' }
            ]
        },
        // QC (ID: 2)
        '2': {
            stations: [
                { id: 10, name: 'QC LV' },
                { id: 11, name: 'QC MV' },
                { id: 12, name: 'IQC' },
                { id: 13, name: 'General' }
            ]
        },
        // Warehouse (ID: 3)
        '3': {
            stations: [
                { id: 14, name: 'Warehouse' },
                { id: 15, name: 'Warehouse' }
            ]
        }
    };

    // Update station dropdown berdasarkan area yang dipilih
    function updateStations(areaId) {
        // Reset station dropdown
        stationSelect.innerHTML = '<option value="">Semua Station</option>';
        
        // Jika tidak ada area yang dipilih
        if (!areaId) {
            return;
        }
        
        // Isi dropdown stations
        const area = areaStationMap[areaId];
        if (area && area.stations) {
            area.stations.forEach(station => {
                const option = document.createElement('option');
                option.value = station.id;
                option.textContent = station.name;
                stationSelect.appendChild(option);
            });
        }
        
        console.log(`Stations updated for area ID: ${areaId}`);
    }

    // Event listeners
    areaSelect.addEventListener('change', function() {
        const areaId = this.value;
        updateStations(areaId);
    });
});
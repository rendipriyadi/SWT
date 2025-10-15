document.addEventListener('DOMContentLoaded', function() {
    const departemenSelect = document.getElementById('departemen');
    const supervisorInput = document.getElementById('supervisor');

    // Function to update supervisor
    function updateSupervisor(departemenId) {
        if (!departemenId) {
            supervisorInput.value = '';
            return;
        }

        // Menggunakan Ziggy route helper
        fetch(route('get.supervisor', departemenId))
            .then(response => response.json())
            .then(data => {
                // Set supervisor value from response
                if (data.is_group && Array.isArray(data.group_members) && data.group_members.length > 0) {
                    // For group departments, show the list of members
                    supervisorInput.value = data.group_members.join(', ');
                } else {
                    // For individual departments, show the supervisor name
                    supervisorInput.value = data.supervisor || '';
                }
            })
            .catch(error => {
                console.error('Error fetching supervisor data:', error);
                supervisorInput.value = '';
            });
    }

    // Event listener for departemen change
    if (departemenSelect && supervisorInput) {
        // Initial load if value exists
        if (departemenSelect.value) {
            updateSupervisor(departemenSelect.value);
        }

        // On change event
        departemenSelect.addEventListener('change', function() {
            updateSupervisor(this.value);
        });
    }
});
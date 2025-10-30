document.addEventListener('DOMContentLoaded', function() {
    const departemenSelect = document.getElementById('departemen');
    const supervisorInput = document.getElementById('supervisor');

    /**
     * Update supervisor field based on selected department
     * @param {string} departemenId - The department ID
     */
    function updateSupervisor(departemenId) {
        if (!departemenId) {
            supervisorInput.value = '';
            return;
        }

        // Use global route configuration
        const url = window.routes.supervisor.replace(':id', departemenId);

        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to fetch supervisor data');
                }
                return response.json();
            })
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
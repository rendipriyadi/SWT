// Penyelesaian Modal Handler
document.addEventListener('DOMContentLoaded', function() {
    // Modal dinamis untuk melihat penyelesaian
    $(document).on('click', '.lihat-penyelesaian-btn', function() {
        var id = $(this).data('id');
        var modalBody = $('#modalPenyelesaianBody');
        
        modalBody.html('<div class="text-center my-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Memuat data...</p></div>');
        
        $.get('/laporan/penyelesaian/' + id, function(res) {
            console.log('Response:', res); // Debug response
            
            if (res.success) {
                var html = '';
                
                // Bagian Tanggal
                if (res.Tanggal) {
                    html += `<div class="mb-3">
                        <h6 class="fw-bold">Tanggal Penyelesaian:</h6>
                        <p>${res.Tanggal}</p>
                    </div>`;
                }
                
                // Bagian Foto - Ubah menjadi carousel jika ada foto
                html += '<div class="mb-3">';
                html += '<h6 class="fw-bold">Foto Penyelesaian:</h6>';
                
                if (res.Foto && Array.isArray(res.Foto) && res.Foto.length > 0) {
                    html += '<div id="penyelesaianPhotoCarousel" class="carousel slide" data-bs-ride="carousel">';
                    html += '<div class="carousel-inner">';
                    
                    res.Foto.forEach((foto, index) => {
                        html += `<div class="carousel-item ${index === 0 ? 'active' : ''}">
                            <img src="${foto}" class="d-block w-100 img-fluid rounded" alt="Foto Penyelesaian ${index+1}">
                        </div>`;
                    });
                    
                    html += '</div>';
                    
                    // Tombol navigasi carousel jika lebih dari 1 foto
                    if (res.Foto.length > 1) {
                        html += `<button class="carousel-control-prev" type="button" data-bs-target="#penyelesaianPhotoCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#penyelesaianPhotoCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>`;
                    }
                    
                    html += '</div>'; // end carousel
                } else {
                    html += '<p class="text-muted">Tidak ada foto penyelesaian.</p>';
                }
                
                html += '</div>'; // end div.mb-3 for photos
                
                // Bagian Deskripsi
                if (res.deskripsi_penyelesaian) {
                    html += `<div class="mb-3">
                        <h6 class="fw-bold">Deskripsi Penyelesaian:</h6>
                        <p style="white-space: pre-line;">${res.deskripsi_penyelesaian}</p>
                    </div>`;
                }
                
                modalBody.html(html);
                
                // Inisialisasi carousel secara manual
                if (res.Foto && res.Foto.length > 0) {
                    new bootstrap.Carousel(document.getElementById('penyelesaianPhotoCarousel'), {
                        interval: 5000
                    });
                }
            } else {
                modalBody.html('<div class="alert alert-danger mb-0">Data penyelesaian tidak ditemukan.</div>');
            }
        }).fail(function(xhr, status, error) {
            console.error('Error:', error);
            modalBody.html('<div class="alert alert-danger mb-0">Gagal mengambil data penyelesaian. Silakan coba lagi.</div>');
        });
    });
});
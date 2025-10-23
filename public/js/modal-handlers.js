/**
 * Modal Handlers
 * Centralized modal management for description, photos, and completion details
 * Prevents duplicate handlers and manages modal backdrop cleanup
 */
document.addEventListener('DOMContentLoaded', function() {
    
    /**
     * Centralized modal management to prevent duplicate handlers
     * Tracks active modals and handles cleanup
     */
    const modalHandlers = {
        activeModals: [],
        
        /**
         * Register a modal as active
         * @param {string} modalId - The ID of the modal to register
         */
        register: function(modalId) {
            if (!this.activeModals.includes(modalId)) {
                this.activeModals.push(modalId);
            }
        },
        
        /**
         * Unregister a modal and cleanup if it's the last one
         * @param {string} modalId - The ID of the modal to unregister
         */
        unregister: function(modalId) {
            this.activeModals = this.activeModals.filter(id => id !== modalId);
            
            // Clean up backdrop if this was the last modal
            if (this.activeModals.length === 0) {
                $('.modal-backdrop').remove();
                $('body').removeClass('modal-open');
                $('body').css('padding-right', '');
            }
        }
    };

    /**
     * Fix modal backdrop issues
     * Monitors body class changes and cleans up orphaned backdrops
     */
    function fixBackdropIssue() {
        // Observer to monitor body class changes
        const bodyObserver = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    // Cek jika body memiliki class modal-open tapi tidak ada modal yang terlihat
                    if (document.body.classList.contains('modal-open') && document.querySelectorAll('.modal.show').length === 0) {
                        document.body.classList.remove('modal-open');
                        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                        document.body.style.paddingRight = '';
                        document.body.style.overflow = '';
                    }
                }
            });
        });
        
        bodyObserver.observe(document.body, { attributes: true });
        
        // Tambahkan event handler untuk tombol close dan klik backdrop
        document.addEventListener('click', function(e) {
            // Guard against null targets or nodes without classList
            const tgt = e && e.target ? e.target : null;
            if (!tgt) return;
            const hasClassList = !!(tgt.classList && typeof tgt.classList.contains === 'function');
            const isBackdrop = hasClassList && tgt.classList.contains('modal');
            const isCloseBtn = hasClassList && tgt.classList.contains('btn-close');
            const isDismissAttr = typeof tgt.getAttribute === 'function' && tgt.getAttribute('data-bs-dismiss') === 'modal';

            if (isBackdrop || isCloseBtn || isDismissAttr) {
                setTimeout(function() {
                    if (document.querySelectorAll('.modal.show').length === 0) {
                        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                        document.body.classList.remove('modal-open');
                        document.body.style.paddingRight = '';
                        document.body.style.overflow = '';
                    }
                }, 300); // Delay untuk memungkinkan animasi modal selesai
            }
        });
        
        // Force cleanup setiap kali modal ditutup
        document.body.addEventListener('hidden.bs.modal', function(e) {
            setTimeout(function() {
                if (document.querySelectorAll('.modal.show').length === 0) {
                    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                    document.body.classList.remove('modal-open');
                    document.body.style.paddingRight = '';
                    document.body.style.overflow = '';
                }
            }, 50);
        }, true);
    }

    /**
     * Ensure modal has proper z-index
     * @param {HTMLElement} modalEl - The modal element
     */
    function ensureModalZIndex(modalEl) {
        if (!modalEl) return;
        
        const allModals = document.querySelectorAll('.modal.show');
        const baseZIndex = 1050;
        
        allModals.forEach((modal, index) => {
            modal.style.zIndex = baseZIndex + (index * 10);
            const backdrop = document.querySelector(`.modal-backdrop:nth-of-type(${index + 1})`);
            if (backdrop) {
                backdrop.style.zIndex = baseZIndex + (index * 10) - 1;
            }
        });
    }

    /**
     * Event listener for all opened modals
     * Moves modal to body and ensures proper z-index
     */
    document.addEventListener('show.bs.modal', function(e) {
        const modalEl = e.target;
        // Move modal to <body> to avoid stacking context issues
        if (modalEl && modalEl.parentElement !== document.body) {
            document.body.appendChild(modalEl);
        }
        setTimeout(() => {
            ensureModalZIndex(modalEl);
        }, 10);
    }, true);
    
    // Call backdrop fix function
    fixBackdropIssue();

    /**
     * Description modal handler
     * Displays full description text in a modal
     */
    $(document).on('click', '.view-description', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Get description from data attribute
        const description = $(this).data('description');
        
        // Find modal element
        const modal = document.getElementById('descriptionModal');
        if (!modal) {
            console.error('Modal element not found!');
            alert('Description detail modal not found!');
            return;
        }
        
        // Find modal body
        const modalBody = document.getElementById('descriptionModalBody');
        if (!modalBody) {
            console.error('Modal body element not found!');
            alert('Modal body element not found!');
            return;
        }
        
        // Set content with proper formatting
        if (description) {
            // Preserve line breaks and format text
            const formattedDescription = description
                .replace(/\n/g, '<br>')
                .replace(/\r/g, '')
                .replace(/  /g, '&nbsp;&nbsp;');
            
            modalBody.innerHTML = `
                <div class="description-content">
                    <div class="description-text">${formattedDescription}</div>
                </div>
            `;
        } else {
            modalBody.innerHTML = '<div class="text-muted">No description available</div>';
        }
        
        // Open modal using Bootstrap's API
        // Pastikan modal menjadi anak langsung dari <body> sebelum ditampilkan
        if (modal.parentElement !== document.body) {
            document.body.appendChild(modal);
        }
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
        
        // Register with our handler
        modalHandlers.register('descriptionModal');
    });

    /**
     * Photo modal handler
     * Displays photos in a carousel modal
     */
    $(document).on('click', 'img[data-bs-toggle="modal"][data-bs-target="#modalFotoFull"]', function() {
        const photosData = $(this).data('photos');
        
        const carouselInner = $('#photoCarousel .carousel-inner');
        carouselInner.empty(); // Clear old photos
        
        const modal = document.getElementById('modalFotoFull');
        modalHandlers.register('modalFotoFull');

        if (photosData && Array.isArray(photosData) && photosData.length > 0) {
            photosData.forEach((photoUrl, index) => {
                const activeClass = index === 0 ? 'active' : '';
                carouselInner.append(`
                    <div class="carousel-item ${activeClass}">
                        <img src="${photoUrl}" class="d-block w-100 img-fluid rounded" alt="Foto ${index+1}">
                    </div>
                `);
            });
        } else {
            // Fallback if no photos
            const noPhotoUrl = window.location.origin + '/images/static/nophoto.jpg';
            carouselInner.append(`
                <div class="carousel-item active">
                    <img src="${noPhotoUrl}" class="d-block w-100 img-fluid rounded" alt="No photo available">
                </div>
            `);
        }
        
        // Show/hide carousel navigation buttons
        $('#photoCarousel .carousel-control-prev, #photoCarousel .carousel-control-next').toggle(photosData && photosData.length > 1);
    });

    /**
     * Solution details modal handler
     * Displays completion details with photo carousel
     */
    $(document).on('click', '.lihat-penyelesaian-btn', function() {
        const encryptedId = $(this).data('encrypted-id');
        const modalBody = $('#modalPenyelesaianBody');
        
        // Show loading spinner
        modalBody.html('<div class="text-center my-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading data...</p></div>');
        modalHandlers.register('modalPenyelesaian');
        
        // Use global route configuration
        const url = window.routes.penyelesaian.replace(':encryptedId', encryptedId);
        
        $.get(url, function(res) {
            if (res.success) {
                var html = '';
                
                // Date section
                if (res.Tanggal) {
                    html += `<div class="mb-3">
                        <h6 class="fw-bold">Completion Date:</h6>
                        <p>${res.Tanggal}</p>
                    </div>`;
                }
                
                // Photo section - convert to carousel if multiple photos
                html += '<div class="mb-3">';
                html += '<h6 class="fw-bold">Completion Photos:</h6>';

                if (res.Foto && Array.isArray(res.Foto) && res.Foto.length > 0) {
                    html += '<div id="penyelesaianPhotoCarousel" class="carousel slide position-relative" style="background-color: #f8f9fa; padding: 20px; border-radius: 8px;">';
                    html += '<div class="carousel-inner">';
                    
                    res.Foto.forEach((foto, index) => {
                        html += `<div class="carousel-item ${index === 0 ? 'active' : ''}">
                            <img src="${foto}" class="d-block img-fluid rounded mx-auto" style="max-height: 400px; max-width: 100%; object-fit: contain;" alt="Foto Penyelesaian ${index+1}">
                        </div>`;
                    });
                    
                    html += '</div>';
                    
                    // Carousel navigation buttons if more than 1 photo with better visibility
                    if (res.Foto.length > 1) {
                        html += `<button class="carousel-control-prev" type="button" data-bs-target="#penyelesaianPhotoCarousel" data-bs-slide="prev" style="width: 50px;">
                            <span class="carousel-control-prev-icon" aria-hidden="true" style="background-color: rgba(0,0,0,0.7); padding: 20px; border-radius: 50%;"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#penyelesaianPhotoCarousel" data-bs-slide="next" style="width: 50px;">
                            <span class="carousel-control-next-icon" aria-hidden="true" style="background-color: rgba(0,0,0,0.7); padding: 20px; border-radius: 50%;"></span>
                            <span class="visually-hidden">Next</span>
                        </button>`;
                        
                        // Add thumbnail previews below carousel
                        html += '<div class="d-flex justify-content-center gap-2 mt-3 flex-wrap">';
                        res.Foto.forEach((foto, index) => {
                            html += `<img src="${foto}" 
                                class="img-thumbnail completion-thumbnail ${index === 0 ? 'active-thumbnail' : ''}" 
                                data-bs-target="#penyelesaianPhotoCarousel" 
                                data-bs-slide-to="${index}"
                                style="width: 80px; height: 80px; object-fit: cover; cursor: pointer; border: 3px solid ${index === 0 ? '#0d6efd' : 'transparent'};"
                                alt="Thumbnail ${index+1}">`;
                        });
                        html += '</div>';
                    }
                    
                    html += '</div>'; // end carousel
                } else {
                    html += '<p class="text-muted">Tidak ada foto penyelesaian.</p>';
                }
                
                html += '</div>'; // end div.mb-3 for photos
                
                // Description section
                if (res.deskripsi_penyelesaian) {
                    html += `<div class="mb-3">
                        <h6 class="fw-bold">Description of Completion:</h6>
                        <p style="white-space: pre-line;">${res.deskripsi_penyelesaian}</p>
                    </div>`;
                }
                
                modalBody.html(html);
                
                // Initialize carousel manually
                if (res.Foto && res.Foto.length > 0) {
                    const carouselEl = document.getElementById('penyelesaianPhotoCarousel');
                    const carousel = new bootstrap.Carousel(carouselEl, {
                        interval: false // Disable auto-slide
                    });
                    
                    // Handle thumbnail clicks
                    if (res.Foto.length > 1) {
                        const thumbnails = document.querySelectorAll('.completion-thumbnail');
                        thumbnails.forEach((thumb, index) => {
                            thumb.addEventListener('click', function() {
                                carousel.to(index);
                                // Update active thumbnail border
                                thumbnails.forEach(t => t.style.border = '3px solid transparent');
                                this.style.border = '3px solid #0d6efd';
                            });
                        });
                        
                        // Update thumbnail border on carousel slide
                        carouselEl.addEventListener('slid.bs.carousel', function(e) {
                            const activeIndex = e.to;
                            thumbnails.forEach((t, i) => {
                                t.style.border = i === activeIndex ? '3px solid #0d6efd' : '3px solid transparent';
                            });
                        });
                    }
                }
            } else {
                modalBody.html('<div class="alert alert-danger mb-0">Completion data not found.</div>');
            }
        }).fail(function(xhr, status, error) {
            console.error('Error:', error);
            modalBody.html('<div class="alert alert-danger mb-0">Failed to retrieve completion data. Please try again.</div>');
        });
    });
    
    /**
     * General modal cleanup on close
     * Unregisters modal from tracking when hidden
     */
    $(document).on('hidden.bs.modal', '.modal', function(e) {
        const modalId = this.id;
        
        // Unregister this modal
        modalHandlers.unregister(modalId);
    });
});
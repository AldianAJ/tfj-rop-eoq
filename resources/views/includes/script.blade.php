<script src="{{ asset('assets/libs/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/libs/metismenu/metisMenu.min.js') }}"></script>
<script src="{{ asset('assets/libs/simplebar/simplebar.min.js') }}"></script>
<script src="{{ asset('assets/libs/node-waves/waves.min.js') }}"></script>
<script src="{{ asset('assets/js/app.js') }}"></script>
<script src="{{ asset('assets/libs/sweetalert2/sweetalert2.min.js') }}"></script>
<script src="{{ asset('assets/js/pages/sweet-alerts.init.js') }}"></script>
<script>
    @if ($user->role == 'gudang')
        var checkROP = function() {
            $.ajax({
                type: "GET",
                url: "{{ route('barang.checkROP') }}",
                success: function(response) {
                    $("#count-notif").text(response.jumlah);
                    let html = '';
                    response.barangs.forEach(element => {
                        html +=
                            " <a href='{{ route('pemesanan.create') }}' class='text-reset notification-item'>" +
                            "<div class='d-flex'>" +
                            " <div class='avatar-xs me-3'>" +
                            " <span class='avatar-title bg-primary rounded-circle font-size-16'>" +
                            "<i class='bx bx-cart'></i>" +
                            "</span>" +
                            "  </div>" +
                            "<div class='flex-grow-1'>" +
                            " <h6 class='mb-1' key='t-your-order'>Barang yang harus dipesan</h6>" +
                            "   <div class='font-size-12 text-muted'>" +
                            "  <p class='mb-1' key='t-grammer'>" + element.nama_barang +
                            "    </p>" +
                            " </div>" +
                            "</div>" +
                            "</div>" +
                            "</a>"
                    });
                    $(".parentNotif").remove();
                    $(".out-simple").after(
                        '<div data-simplebar style="max-height: 230px;" class="parentNotif">' +
                        html +
                        '</div>'
                    );
                }
            });
            setTimeout(() => {
                checkROP()
            }, 10000);
        }

        checkROP();
    @endif
</script>

<script>
    @if ($user->role == 'counter')
        var checkMinta = function() {
            $.ajax({
                type: "GET",
                url: "{{ route('permintaan-counter.checkMinta') }}",
                success: function(response) {
                    var $badgeCount = response.checkMinta.length; // Tentukan jumlah notifikasi

                    // Menghapus semua elemen dari .out-simple
                    $(".out-simple").empty();

                    if ($badgeCount > 0) {
                        response.checkMinta.forEach(element => {
                            let html = '';
                            if (element.status === "Processing") {
                                html +=
                                    "<a href='{{ route('permintaan-counter') }}' class='text-reset notification-item'>" +
                                    "<div data-simplebar style='max-height: 230px;' class='processingNotif'>" +
                                    "<div class='d-flex'>" +
                                    "<div class='avatar-xs me-3 mt-1'>" +
                                    "<span class='avatar-title bg-primary rounded-circle font-size-16 mt-1'>" +
                                    "<i class='bx bx-cart'></i>" +
                                    "</span>" +
                                    "</div>" +
                                    "<div class='flex-grow-1'>" +
                                    "<h6 class='mb-1' key='t-grammer'>" + element.status +
                                    "</h6>" +
                                    "<div class='font-size-12 text-muted'>" +
                                    "<p class='mb-1' key='t-your-order'>Status permintaan masih Pending. Silahkan menunggu.</p>" +
                                    "</div>" +
                                    "</div>" +
                                    "</div>" +
                                    "</div>" +
                                    "</a>";
                            } else {
                                html +=
                                    "<a href='{{ route('permintaan-counter') }}' class='text-reset notification-item'>" +
                                    "<div data-simplebar style='max-height: 230px;' class='sendNotif'>" +
                                    "<div class='d-flex'>" +
                                    "<div class='avatar-xs me-3 mt-1'>" +
                                    "<span class='avatar-title bg-primary rounded-circle font-size-16 m-1'>" +
                                    "<i class='bx bx-cart'></i>" +
                                    "</span>" +
                                    "</div>" +
                                    "<div class='flex-grow-1'>" +
                                    "<h6 class='mb-1' key='t-grammer'>" + element.status +
                                    "</h6>" +
                                    "<div class='font-size-12 text-muted'>" +
                                    "<p class='mb-1' key='t-your-order'>Permintaan barang telah dikirim. Silakan cek.</p>" +
                                    "</div>" +
                                    "</div>" +
                                    "</div>" +
                                    "</div>" +
                                    "</a>";
                            }

                            $(".out-simple").append(html); // Menambahkan elemen ke .out-simple
                        });
                    } else {
                        // Menampilkan .out-simple tetapi menonaktifkan elemen-elemennya
                        $(".out-simple").append(
                            "<div data-simplebar style='max-height: 230px;'>" +
                            "<div class='d-flex'>" +
                            "<div class='avatar-xs ms-3 me-3 mb-3 mt-3'>" +
                            "<span class='avatar-title bg-primary rounded-circle font-size-16'>" +
                            "<i class='bx bx-cart'></i>" +
                            "</span>" +
                            "</div>" +
                            "<div class='flex-grow-1'>" +
                            "<h6 class='mb-1 mt-3' key='t-grammer'>Kosong</h6>" +
                            "<div class='font-size-12 text-muted'>" +
                            "<p class='mb-3' key='t-your-order'>Permintaan ke gudang tidak ada.</p>" +
                            "</div>" +
                            "</div>" +
                            "</div>" +
                            "</div>"
                        );
                    }

                    // Update badge dengan jumlah notifikasi
                    $("#minta-notif").text($badgeCount);

                    // Menampilkan atau menyembunyikan elemen berdasarkan kondisi
                    if ($(".processingNotif").length > 0) {
                        $(".sendNotif").remove();
                        $(".out-simple").remove();
                        $(".processingNotif").after();
                    } else if ($(".sendNotif").length > 0) {
                        $(".processingNotif").remove();
                        $(".sendNotif").after();
                    } else {
                        $(".processingNotif").remove();
                        $(".sendNotif").remove();
                        $(".out-simple").after();
                    }

                }
            });
        };
        checkMinta();
    @endif
</script>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const darkModeCheckbox = document.getElementById('darkMode');
        const logoDarkMode = document.getElementById('logoDarkMode');
        const logoLightMode = document.getElementById('logoLightMode');
        const layoutModeAttribute = document.querySelector('[data-layout-mode]');
        const sidebarAttribute = document.querySelector('[data-sidebar]');

        // Check local storage for the user's preference
        const isDarkMode = localStorage.getItem('darkMode') === 'true';

        // Function to update UI based on dark mode preference
        function updateDarkModeUI(isDark) {
            if (isDark) {
                logoDarkMode.style.display = 'none';
                logoLightMode.style.display = 'block';
                logoLightMode.removeAttribute('data-sidebar');
                logoLightMode.removeAttribute('data-layout-mode');
                logoDarkMode.setAttribute('data-sidebar', 'dark');
                logoDarkMode.setAttribute('data-layout-mode', 'dark');
                sidebarAttribute.setAttribute('data-sidebar', 'dark');
                layoutModeAttribute.setAttribute('data-layout-mode', 'dark');
            } else {
                logoDarkMode.style.display = 'block';
                logoLightMode.style.display = 'none';
                logoDarkMode.removeAttribute('data-sidebar');
                logoDarkMode.removeAttribute('data-layout-mode');
                logoLightMode.setAttribute('data-sidebar', 'light');
                logoLightMode.setAttribute('data-layout-mode', 'light');
                sidebarAttribute.setAttribute('data-sidebar', 'light');
                layoutModeAttribute.setAttribute('data-layout-mode', 'light');
            }
        }

        // Initial UI update based on local storage preference
        updateDarkModeUI(isDarkMode);

        // Update UI and local storage when dark mode checkbox is changed
        darkModeCheckbox.addEventListener('change', function() {
            const isDark = this.checked;
            updateDarkModeUI(isDark);
            localStorage.setItem('darkMode', isDark);
        });
    });
</script>

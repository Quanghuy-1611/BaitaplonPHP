/**
 * App JS - Các chức năng JavaScript chung
 */

document.addEventListener('DOMContentLoaded', function () {

    // ===== Auto dismiss alerts after 4 seconds =====
    document.querySelectorAll('.alert-dismissible').forEach(function (alert) {
        setTimeout(function () {
            var bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        }, 4000);
    });

    // ===== Confirm delete =====
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            var msg = this.getAttribute('data-confirm') || 'Bạn có chắc chắn muốn xóa?';
            if (!confirm(msg)) {
                e.preventDefault();
                return false;
            }
        });
    });

    // ===== Format money inputs =====
    document.querySelectorAll('input[data-money]').forEach(function (input) {
        input.addEventListener('input', function () {
            var value = this.value.replace(/[^\d]/g, '');
            if (value) {
                this.value = parseInt(value).toLocaleString('vi-VN');
            }
        });
    });

    // ===== Parse money before form submit =====
    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function () {
            this.querySelectorAll('input[data-money]').forEach(function (input) {
                input.value = input.value.replace(/[^\d]/g, '');
            });
        });
    });

    // ===== Auto-fill salary from position select =====
    var positionSelect = document.getElementById('position_id');
    var salaryInput = document.getElementById('base_salary');
    if (positionSelect && salaryInput) {
        positionSelect.addEventListener('change', function () {
            var selected = this.options[this.selectedIndex];
            var baseSalary = selected.getAttribute('data-salary');
            if (baseSalary && baseSalary > 0) {
                salaryInput.value = parseInt(baseSalary).toLocaleString('vi-VN');
            }
        });
    }

    // ===== Sidebar active link highlight =====
    var currentPath = window.location.pathname;
    document.querySelectorAll('.sidebar-nav a').forEach(function (link) {
        var href = link.getAttribute('href');
        if (href && currentPath.indexOf(href) !== -1 && href !== '/BaitaplonPHP/') {
            link.classList.add('active');
        }
    });

    // ===== Mobile sidebar toggle =====
    var sidebarToggle = document.getElementById('sidebarToggle');
    var sidebar = document.querySelector('.sidebar');
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function () {
            sidebar.classList.toggle('show');
        });

        // Close sidebar on outside click (mobile)
        document.addEventListener('click', function (e) {
            if (window.innerWidth <= 768 && sidebar.classList.contains('show')) {
                if (!sidebar.contains(e.target) && e.target !== sidebarToggle) {
                    sidebar.classList.remove('show');
                }
            }
        });
    }

    // ===== Calculate leave days between dates =====
    var startDate = document.getElementById('start_date');
    var endDate = document.getElementById('end_date');
    var daysInput = document.getElementById('days');
    if (startDate && endDate && daysInput) {
        function calcDays() {
            if (startDate.value && endDate.value) {
                var start = new Date(startDate.value);
                var end = new Date(endDate.value);
                var diff = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
                if (diff > 0) {
                    daysInput.value = diff;
                }
            }
        }
        startDate.addEventListener('change', calcDays);
        endDate.addEventListener('change', calcDays);
    }

    // ===== Tooltip initialization =====
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (el) {
        new bootstrap.Tooltip(el);
    });

    // ===== Print payslip =====
    var printBtn = document.getElementById('btnPrint');
    if (printBtn) {
        printBtn.addEventListener('click', function () {
            window.print();
        });
    }
});

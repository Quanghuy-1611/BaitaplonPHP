<?php

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'ql_nhansu_luong';

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $conn = new mysqli($db_host, $db_user, $db_pass);
        if ($conn->connect_error) {
            throw new Exception('Không thể kết nối MySQL: ' . $conn->connect_error);
        }
        $conn->set_charset('utf8mb4');

        // Xóa database cũ nếu chọn reset
        if (!empty($_POST['reset'])) {
            $conn->query("DROP DATABASE IF EXISTS `$db_name`");
        }

        // Tạo database
        $conn->query("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $conn->select_db($db_name);

        // ==================== TẠO BẢNG ====================

        $conn->query("CREATE TABLE IF NOT EXISTS `users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `username` VARCHAR(50) NOT NULL UNIQUE,
            `password` VARCHAR(255) NOT NULL,
            `full_name` VARCHAR(100) NOT NULL,
            `email` VARCHAR(100) DEFAULT NULL,
            `role` ENUM('admin','hr','accountant','employee') NOT NULL DEFAULT 'hr',
            `employee_id` INT DEFAULT NULL,
            `status` TINYINT(1) NOT NULL DEFAULT 1,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $conn->query("CREATE TABLE IF NOT EXISTS `departments` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL,
            `manager_name` VARCHAR(100) DEFAULT NULL,
            `phone` VARCHAR(20) DEFAULT NULL,
            `description` TEXT DEFAULT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $conn->query("CREATE TABLE IF NOT EXISTS `positions` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL,
            `department_id` INT DEFAULT NULL,
            `base_salary` DECIMAL(15,0) DEFAULT 0,
            `description` TEXT DEFAULT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`department_id`) REFERENCES `departments`(`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $conn->query("CREATE TABLE IF NOT EXISTS `employees` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `employee_code` VARCHAR(20) NOT NULL UNIQUE,
            `full_name` VARCHAR(100) NOT NULL,
            `gender` VARCHAR(10) DEFAULT 'Nam',
            `birth_date` DATE DEFAULT NULL,
            `id_card` VARCHAR(20) DEFAULT NULL,
            `phone` VARCHAR(20) DEFAULT NULL,
            `email` VARCHAR(100) DEFAULT NULL,
            `address` TEXT DEFAULT NULL,
            `department_id` INT DEFAULT NULL,
            `position_id` INT DEFAULT NULL,
            `hire_date` DATE DEFAULT NULL,
            `contract_type` VARCHAR(50) DEFAULT NULL,
            `base_salary` DECIMAL(15,0) DEFAULT 0,
            `bank_account` VARCHAR(30) DEFAULT NULL,
            `bank_name` VARCHAR(100) DEFAULT NULL,
            `status` VARCHAR(20) DEFAULT 'Đang làm',
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (`department_id`) REFERENCES `departments`(`id`) ON DELETE SET NULL,
            FOREIGN KEY (`position_id`) REFERENCES `positions`(`id`) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $conn->query("CREATE TABLE IF NOT EXISTS `attendance` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `employee_id` INT NOT NULL,
            `work_date` DATE NOT NULL,
            `status` VARCHAR(20) DEFAULT 'Đi làm',
            `check_in` TIME DEFAULT NULL,
            `check_out` TIME DEFAULT NULL,
            `overtime_hours` DECIMAL(5,1) DEFAULT 0,
            `note` TEXT DEFAULT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY `uk_emp_date` (`employee_id`, `work_date`),
            FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $conn->query("CREATE TABLE IF NOT EXISTS `allowances` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL,
            `default_amount` DECIMAL(15,0) DEFAULT 0,
            `description` TEXT DEFAULT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $conn->query("CREATE TABLE IF NOT EXISTS `employee_allowances` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `employee_id` INT NOT NULL,
            `allowance_id` INT NOT NULL,
            `amount` DECIMAL(15,0) DEFAULT 0,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY `uk_emp_allow` (`employee_id`, `allowance_id`),
            FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE CASCADE,
            FOREIGN KEY (`allowance_id`) REFERENCES `allowances`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $conn->query("CREATE TABLE IF NOT EXISTS `rewards` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `employee_id` INT NOT NULL,
            `type` ENUM('Khen thưởng','Kỷ luật') NOT NULL,
            `reason` TEXT NOT NULL,
            `amount` DECIMAL(15,0) DEFAULT 0,
            `date` DATE DEFAULT NULL,
            `decision_number` VARCHAR(50) DEFAULT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $conn->query("CREATE TABLE IF NOT EXISTS `salary` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `employee_id` INT NOT NULL,
            `month` INT NOT NULL,
            `year` INT NOT NULL,
            `working_days` INT DEFAULT 0,
            `actual_working_days` INT DEFAULT 0,
            `base_salary` DECIMAL(15,0) DEFAULT 0,
            `total_allowance` DECIMAL(15,0) DEFAULT 0,
            `overtime_hours` DECIMAL(5,1) DEFAULT 0,
            `overtime_pay` DECIMAL(15,0) DEFAULT 0,
            `total_reward` DECIMAL(15,0) DEFAULT 0,
            `total_discipline` DECIMAL(15,0) DEFAULT 0,
            `gross_salary` DECIMAL(15,0) DEFAULT 0,
            `bhxh` DECIMAL(15,0) DEFAULT 0,
            `bhyt` DECIMAL(15,0) DEFAULT 0,
            `bhtn` DECIMAL(15,0) DEFAULT 0,
            `tax` DECIMAL(15,0) DEFAULT 0,
            `advance_salary` DECIMAL(15,0) DEFAULT 0,
            `other_deduction` DECIMAL(15,0) DEFAULT 0,
            `net_salary` DECIMAL(15,0) DEFAULT 0,
            `status` VARCHAR(20) DEFAULT 'Chờ duyệt',
            `approved_by` INT DEFAULT NULL,
            `approved_at` DATETIME DEFAULT NULL,
            `paid_at` DATETIME DEFAULT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            UNIQUE KEY `uk_emp_month` (`employee_id`, `month`, `year`),
            FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $conn->query("CREATE TABLE IF NOT EXISTS `salary_advance` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `employee_id` INT NOT NULL,
            `amount` DECIMAL(15,0) NOT NULL,
            `reason` TEXT DEFAULT NULL,
            `month` INT NOT NULL,
            `year` INT NOT NULL,
            `status` VARCHAR(20) DEFAULT 'Chờ duyệt',
            `approved_by` INT DEFAULT NULL,
            `approved_at` DATETIME DEFAULT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $conn->query("CREATE TABLE IF NOT EXISTS `leaves` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `employee_id` INT NOT NULL,
            `leave_type` VARCHAR(50) NOT NULL DEFAULT 'Nghỉ phép năm',
            `start_date` DATE NOT NULL,
            `end_date` DATE NOT NULL,
            `days` INT NOT NULL DEFAULT 1,
            `reason` TEXT DEFAULT NULL,
            `status` VARCHAR(20) DEFAULT 'Chờ duyệt',
            `approved_by` INT DEFAULT NULL,
            `approved_at` DATETIME DEFAULT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $conn->query("CREATE TABLE IF NOT EXISTS `contracts` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `employee_id` INT NOT NULL,
            `contract_number` VARCHAR(50) DEFAULT NULL,
            `contract_type` VARCHAR(50) NOT NULL,
            `start_date` DATE NOT NULL,
            `end_date` DATE DEFAULT NULL,
            `base_salary` DECIMAL(15,0) DEFAULT 0,
            `note` TEXT DEFAULT NULL,
            `status` VARCHAR(20) DEFAULT 'Hiệu lực',
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updated_at` DATETIME DEFAULT NULL,
            FOREIGN KEY (`employee_id`) REFERENCES `employees`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $conn->query("CREATE TABLE IF NOT EXISTS `activity_log` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `user_id` INT NOT NULL,
            `action` VARCHAR(255) NOT NULL,
            `module` VARCHAR(50) NOT NULL,
            `detail` TEXT DEFAULT NULL,
            `ip_address` VARCHAR(45) DEFAULT NULL,
            `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // ==================== DỮ LIỆU MẪU ====================

        $check = $conn->query("SELECT COUNT(*) as cnt FROM users");
        $row = $check->fetch_assoc();

        if ($row['cnt'] == 0) {
            // --- Users ---
            $adminPass = password_hash('admin123', PASSWORD_DEFAULT);
            $hrPass = password_hash('hr123', PASSWORD_DEFAULT);
            $accPass = password_hash('ketoan123', PASSWORD_DEFAULT);
            $empPass = password_hash('nhanvien123', PASSWORD_DEFAULT);

            $conn->query("INSERT INTO users (username, password, full_name, email, role, employee_id) VALUES
                ('admin', '$adminPass', 'Nguyễn Văn Admin', 'admin@company.com', 'admin', NULL),
                ('hr01', '$hrPass', 'Trần Thị Nhân Sự', 'hr@company.com', 'hr', NULL),
                ('ketoan01', '$accPass', 'Lê Văn Kế Toán', 'ketoan@company.com', 'accountant', NULL),
                ('nhanvien01', '$empPass', 'Nguyễn Văn An', 'an.nv@company.com', 'employee', 1)
            ");

            // --- Departments ---
            $conn->query("INSERT INTO departments (name, manager_name, phone) VALUES
                ('Ban Giám đốc', 'Nguyễn Văn An', '0901000001'),
                ('Phòng Nhân sự', 'Trần Thị Bích', '0901000002'),
                ('Phòng Kế toán', 'Lê Hoàng Cường', '0901000003'),
                ('Phòng Kinh doanh', 'Phạm Minh Dũng', '0901000004'),
                ('Phòng Kỹ thuật', 'Hoàng Thị Mai', '0901000005'),
                ('Phòng Marketing', 'Bùi Thị Lan', '0901000006'),
                ('Phòng Hành chính', 'Phan Đình Sơn', '0901000007')
            ");

            // --- Positions ---
            $conn->query("INSERT INTO positions (name, department_id, base_salary) VALUES
                ('Giám đốc', 1, 50000000),
                ('Phó Giám đốc', 1, 40000000),
                ('Trưởng phòng Nhân sự', 2, 25000000),
                ('Nhân viên Nhân sự', 2, 12000000),
                ('Trưởng phòng Kế toán', 3, 25000000),
                ('Kế toán viên', 3, 13000000),
                ('Trưởng phòng Kinh doanh', 4, 22000000),
                ('Nhân viên Kinh doanh', 4, 10000000),
                ('Trưởng phòng Kỹ thuật', 5, 28000000),
                ('Kỹ sư phần mềm', 5, 18000000),
                ('Lập trình viên', 5, 15000000),
                ('Trưởng phòng Marketing', 6, 22000000),
                ('Nhân viên Marketing', 6, 11000000),
                ('Trưởng phòng Hành chính', 7, 18000000),
                ('Nhân viên Hành chính', 7, 9000000)
            ");

            // --- Employees (15 nhân viên) ---
            $conn->query("INSERT INTO employees (employee_code, full_name, gender, birth_date, id_card, phone, email, address, department_id, position_id, hire_date, contract_type, base_salary, bank_account, bank_name, status) VALUES
                ('NV0001', 'Nguyễn Văn An', 'Nam', '1985-03-15', '001085012345', '0912345001', 'an.nv@company.com', '123 Nguyễn Huệ, Q.1, TP.HCM', 1, 1, '2018-01-15', 'Không xác định thời hạn', 50000000, '1234567890', 'Vietcombank', 'Đang làm'),
                ('NV0002', 'Trần Thị Bích', 'Nữ', '1990-07-20', '001090034567', '0912345002', 'bich.tt@company.com', '456 Lê Lợi, Q.1, TP.HCM', 2, 3, '2019-03-01', 'Không xác định thời hạn', 25000000, '2345678901', 'Techcombank', 'Đang làm'),
                ('NV0003', 'Lê Hoàng Cường', 'Nam', '1992-11-05', '001092056789', '0912345003', 'cuong.lh@company.com', '789 Hai Bà Trưng, Q.3, TP.HCM', 3, 5, '2019-06-15', 'Không xác định thời hạn', 25000000, '3456789012', 'BIDV', 'Đang làm'),
                ('NV0004', 'Phạm Minh Dũng', 'Nam', '1993-05-12', '001093078901', '0912345004', 'dung.pm@company.com', '321 Võ Văn Tần, Q.3, TP.HCM', 4, 7, '2020-01-10', 'Xác định thời hạn', 22000000, '4567890123', 'Agribank', 'Đang làm'),
                ('NV0005', 'Hoàng Thị Mai', 'Nữ', '1995-09-25', '001095090123', '0912345005', 'mai.ht@company.com', '654 Pasteur, Q.1, TP.HCM', 5, 9, '2020-04-01', 'Không xác định thời hạn', 28000000, '5678901234', 'MB Bank', 'Đang làm'),
                ('NV0006', 'Vũ Đức Hùng', 'Nam', '1994-02-18', '001094012346', '0912345006', 'hung.vd@company.com', '987 Nam Kỳ Khởi Nghĩa, Q.1, TP.HCM', 5, 10, '2021-02-15', 'Xác định thời hạn', 18000000, '6789012345', 'Vietcombank', 'Đang làm'),
                ('NV0007', 'Đỗ Thanh Hương', 'Nữ', '1996-06-30', '001096034568', '0912345007', 'huong.dt@company.com', '147 Điện Biên Phủ, Q.Bình Thạnh, TP.HCM', 5, 11, '2021-06-01', 'Xác định thời hạn', 15000000, '7890123456', 'Sacombank', 'Đang làm'),
                ('NV0008', 'Ngô Quang Khải', 'Nam', '1991-12-08', '001091056790', '0912345008', 'khai.nq@company.com', '258 Cách Mạng Tháng 8, Q.10, TP.HCM', 4, 8, '2020-09-01', 'Xác định thời hạn', 10000000, '8901234567', 'Techcombank', 'Đang làm'),
                ('NV0009', 'Bùi Thị Lan', 'Nữ', '1997-04-14', '001097078902', '0912345009', 'lan.bt@company.com', '369 Trường Chinh, Q.Tân Bình, TP.HCM', 6, 12, '2022-01-10', 'Xác định thời hạn', 22000000, '9012345678', 'ACB', 'Đang làm'),
                ('NV0010', 'Đinh Văn Long', 'Nam', '1998-08-22', '001098090124', '0912345010', 'long.dv@company.com', '741 Lý Thường Kiệt, Q.Tân Bình, TP.HCM', 6, 13, '2022-03-15', 'Xác định thời hạn', 11000000, '0123456789', 'VPBank', 'Đang làm'),
                ('NV0011', 'Trương Thị Ngọc', 'Nữ', '1993-10-03', '001093012347', '0912345011', 'ngoc.tt@company.com', '852 Nguyễn Thị Minh Khai, Q.3, TP.HCM', 2, 4, '2021-08-01', 'Xác định thời hạn', 12000000, '1122334455', 'BIDV', 'Đang làm'),
                ('NV0012', 'Lý Văn Phúc', 'Nam', '1989-01-28', '001089034569', '0912345012', 'phuc.lv@company.com', '963 Nguyễn Đình Chiểu, Q.3, TP.HCM', 3, 6, '2020-11-15', 'Không xác định thời hạn', 13000000, '2233445566', 'Vietinbank', 'Đang làm'),
                ('NV0013', 'Cao Thị Quỳnh', 'Nữ', '1996-03-17', '001096056791', '0912345013', 'quynh.ct@company.com', '159 Phan Xích Long, Q.Phú Nhuận, TP.HCM', 4, 8, '2022-06-01', 'Xác định thời hạn', 10000000, '3344556677', 'MB Bank', 'Đang làm'),
                ('NV0014', 'Phan Đình Sơn', 'Nam', '1994-07-09', '001094078903', '0912345014', 'son.pd@company.com', '753 Hoàng Văn Thụ, Q.Tân Bình, TP.HCM', 7, 14, '2021-04-15', 'Không xác định thời hạn', 18000000, '4455667788', 'Agribank', 'Đang làm'),
                ('NV0015', 'Mai Thị Tuyết', 'Nữ', '1999-11-21', '001099090125', '0912345015', 'tuyet.mt@company.com', '357 Bà Huyện Thanh Quan, Q.3, TP.HCM', 7, 15, '2023-01-09', 'Xác định thời hạn', 9000000, '5566778899', 'Sacombank', 'Đang làm')
            ");

            // --- Allowances ---
            $conn->query("INSERT INTO allowances (name, default_amount, description) VALUES
                ('Phụ cấp ăn trưa', 800000, 'Phụ cấp tiền ăn trưa hàng tháng'),
                ('Phụ cấp xăng xe', 500000, 'Phụ cấp đi lại hàng tháng'),
                ('Phụ cấp điện thoại', 300000, 'Phụ cấp sử dụng điện thoại'),
                ('Phụ cấp trách nhiệm', 2000000, 'Phụ cấp cho vị trí quản lý'),
                ('Phụ cấp chuyên môn', 1500000, 'Phụ cấp cho vị trí chuyên môn cao'),
                ('Phụ cấp nhà ở', 1000000, 'Hỗ trợ tiền thuê nhà')
            ");

            // --- Employee Allowances ---
            $conn->query("INSERT INTO employee_allowances (employee_id, allowance_id, amount) VALUES
                (1,1,800000),(1,2,500000),(1,3,300000),(1,4,2000000),
                (2,1,800000),(2,2,500000),(2,3,300000),(2,4,2000000),
                (3,1,800000),(3,2,500000),(3,3,300000),(3,4,2000000),
                (4,1,800000),(4,2,500000),(4,4,2000000),
                (5,1,800000),(5,2,500000),(5,3,300000),(5,4,2000000),(5,5,1500000),
                (6,1,800000),(6,2,500000),(6,5,1500000),
                (7,1,800000),(7,2,500000),
                (8,1,800000),(8,2,500000),
                (9,1,800000),(9,2,500000),(9,4,2000000),
                (10,1,800000),(10,2,500000),
                (11,1,800000),(11,2,500000),
                (12,1,800000),(12,2,500000),(12,3,300000),
                (13,1,800000),(13,2,500000),
                (14,1,800000),(14,2,500000),(14,4,2000000),
                (15,1,800000),(15,2,500000)
            ");

            // --- Rewards ---
            $conn->query("INSERT INTO rewards (employee_id, type, reason, amount, date) VALUES
                (1, 'Khen thưởng', 'Hoàn thành xuất sắc nhiệm vụ Q4/2025', 5000000, '2026-01-15'),
                (5, 'Khen thưởng', 'Dự án phần mềm hoàn thành đúng tiến độ', 3000000, '2026-01-20'),
                (6, 'Khen thưởng', 'Nhân viên xuất sắc tháng 12/2025', 2000000, '2026-01-10'),
                (8, 'Kỷ luật', 'Vi phạm nội quy lao động', 500000, '2026-01-25'),
                (4, 'Khen thưởng', 'Đạt doanh số cao nhất phòng Kinh doanh', 4000000, '2026-02-01'),
                (9, 'Khen thưởng', 'Chiến dịch marketing Q1 thành công', 2500000, '2026-02-10')
            ");

            // --- Attendance (Tháng 2/2026) ---
            $attValues = [];
            for ($empId = 1; $empId <= 15; $empId++) {
                for ($day = 1; $day <= 28; $day++) {
                    $date = sprintf('2026-02-%02d', $day);
                    $dayOfWeek = date('N', strtotime($date));
                    if ($dayOfWeek >= 6) continue;

                    $rand = rand(1, 100);
                    if ($rand <= 85) {
                        $st = 'Đi làm';
                    } elseif ($rand <= 92) {
                        $st = 'Nghỉ phép';
                    } elseif ($rand <= 96) {
                        $st = 'Đi muộn';
                    } else {
                        $st = 'Vắng';
                    }

                    if ($st === 'Nghỉ phép' || $st === 'Vắng') {
                        $attValues[] = "($empId, '$date', '$st', NULL, NULL)";
                    } else {
                        $ci = ($st === 'Đi muộn') ? '08:' . rand(15, 45) . ':00' : '08:00:00';
                        $co = '17:' . str_pad(rand(0, 30), 2, '0', STR_PAD_LEFT) . ':00';
                        $attValues[] = "($empId, '$date', '$st', '$ci', '$co')";
                    }
                }
            }
            if (!empty($attValues)) {
                $chunks = array_chunk($attValues, 50);
                foreach ($chunks as $chunk) {
                    $conn->query("INSERT INTO attendance (employee_id, work_date, status, check_in, check_out) VALUES " . implode(',', $chunk));
                }
            }

            // --- Leaves ---
            $conn->query("INSERT INTO leaves (employee_id, leave_type, start_date, end_date, days, reason, status, approved_by) VALUES
                (3, 'Nghỉ phép năm', '2026-02-10', '2026-02-11', 2, 'Việc gia đình', 'Đã duyệt', 1),
                (7, 'Nghỉ ốm', '2026-02-05', '2026-02-06', 2, 'Sốt cao', 'Đã duyệt', 1),
                (10, 'Nghỉ phép năm', '2026-02-20', '2026-02-21', 2, 'Du lịch', 'Đã duyệt', 1),
                (11, 'Nghỉ phép năm', '2026-03-05', '2026-03-07', 3, 'Về quê thăm gia đình', 'Chờ duyệt', NULL),
                (4, 'Nghỉ việc riêng', '2026-03-10', '2026-03-10', 1, 'Làm giấy tờ', 'Chờ duyệt', NULL),
                (6, 'Nghỉ phép năm', '2026-03-15', '2026-03-17', 3, 'Nghỉ mát', 'Chờ duyệt', NULL),
                (13, 'Nghỉ ốm', '2026-02-15', '2026-02-15', 1, 'Đau bụng', 'Đã duyệt', 1),
                (1, 'Nghỉ phép năm', '2026-01-20', '2026-01-22', 3, 'Họp gia đình', 'Đã duyệt', 1)
            ");

            // --- Contracts ---
            $conn->query("INSERT INTO contracts (employee_id, contract_number, contract_type, start_date, end_date, base_salary, note, status) VALUES
                (1, 'HD-2018-001', 'Không xác định thời hạn', '2018-01-15', NULL, 50000000, 'Hợp đồng không xác định thời hạn', 'Hiệu lực'),
                (2, 'HD-2019-002', 'Không xác định thời hạn', '2019-03-01', NULL, 25000000, NULL, 'Hiệu lực'),
                (3, 'HD-2019-003', 'Không xác định thời hạn', '2019-06-15', NULL, 25000000, NULL, 'Hiệu lực'),
                (4, 'HD-2026-004', 'Xác định thời hạn', '2026-01-11', '2028-01-10', 23000000, 'Hợp đồng gia hạn', 'Hiệu lực'),
                (5, 'HD-2020-005', 'Không xác định thời hạn', '2020-04-01', NULL, 28000000, NULL, 'Hiệu lực'),
                (6, 'HD-2023-006', 'Xác định thời hạn', '2023-02-15', '2026-02-14', 18000000, 'Sắp hết hạn', 'Hiệu lực'),
                (7, 'HD-2023-007', 'Xác định thời hạn', '2023-06-01', '2026-05-31', 15000000, NULL, 'Hiệu lực'),
                (8, 'HD-2024-008', 'Xác định thời hạn', '2024-09-01', '2026-08-31', 10000000, NULL, 'Hiệu lực'),
                (9, 'HD-2024-009', 'Xác định thời hạn', '2024-01-10', '2026-01-10', 22000000, NULL, 'Hết hạn'),
                (10, 'HD-2024-010', 'Xác định thời hạn', '2024-03-15', '2026-03-14', 11000000, 'Sắp hết hạn', 'Hiệu lực'),
                (11, 'HD-2023-011', 'Xác định thời hạn', '2023-08-01', '2026-07-31', 12000000, NULL, 'Hiệu lực'),
                (12, 'HD-2020-012', 'Không xác định thời hạn', '2020-11-15', NULL, 13000000, NULL, 'Hiệu lực'),
                (13, 'HD-2024-013', 'Xác định thời hạn', '2024-06-01', '2026-05-31', 10000000, NULL, 'Hiệu lực'),
                (14, 'HD-2021-014', 'Không xác định thời hạn', '2021-04-15', NULL, 18000000, NULL, 'Hiệu lực'),
                (15, 'HD-2023-015', 'Xác định thời hạn', '2023-01-09', '2026-01-08', 9000000, NULL, 'Hết hạn')
            ");

            // --- Salary (Tháng 1/2026) ---
            $conn->query("INSERT INTO salary (employee_id, month, year, working_days, actual_working_days, base_salary, total_allowance, overtime_hours, overtime_pay, total_reward, total_discipline, gross_salary, bhxh, bhyt, bhtn, tax, advance_salary, other_deduction, net_salary, status, paid_at) VALUES
                (1,  1, 2026, 22, 22, 50000000, 3600000, 0, 0, 5000000, 0, 58600000, 4000000, 750000, 500000, 5115000, 0, 0, 48235000, 'Đã thanh toán', '2026-02-05 10:00:00'),
                (2,  1, 2026, 22, 22, 25000000, 3600000, 0, 0, 0, 0, 28600000, 2000000, 375000, 250000, 1215000, 0, 0, 24760000, 'Đã thanh toán', '2026-02-05 10:00:00'),
                (3,  1, 2026, 22, 22, 25000000, 3600000, 0, 0, 0, 0, 28600000, 2000000, 375000, 250000, 1215000, 0, 0, 24760000, 'Đã thanh toán', '2026-02-05 10:00:00'),
                (4,  1, 2026, 22, 22, 22000000, 3300000, 0, 0, 4000000, 0, 29300000, 1760000, 330000, 220000, 1285000, 0, 0, 25705000, 'Đã thanh toán', '2026-02-05 10:00:00'),
                (5,  1, 2026, 22, 22, 28000000, 5100000, 0, 0, 3000000, 0, 36100000, 2240000, 420000, 280000, 2305000, 0, 0, 30855000, 'Đã thanh toán', '2026-02-05 10:00:00'),
                (6,  1, 2026, 22, 22, 18000000, 2800000, 0, 0, 2000000, 0, 22800000, 1440000, 270000, 180000, 465000, 0, 0, 20445000, 'Đã thanh toán', '2026-02-05 10:00:00'),
                (7,  1, 2026, 22, 22, 15000000, 1300000, 0, 0, 0, 0, 16300000, 1200000, 225000, 150000, 0, 0, 0, 14725000, 'Đã thanh toán', '2026-02-05 10:00:00'),
                (8,  1, 2026, 22, 21, 10000000, 1300000, 0, 0, 0, 500000, 10800000, 800000, 150000, 100000, 0, 0, 0, 9750000, 'Đã thanh toán', '2026-02-05 10:00:00'),
                (9,  1, 2026, 22, 22, 22000000, 3300000, 0, 0, 2500000, 0, 27800000, 1760000, 330000, 220000, 1095000, 0, 0, 24395000, 'Đã thanh toán', '2026-02-05 10:00:00'),
                (10, 1, 2026, 22, 22, 11000000, 1300000, 0, 0, 0, 0, 12300000, 880000, 165000, 110000, 0, 0, 0, 11145000, 'Đã thanh toán', '2026-02-05 10:00:00'),
                (11, 1, 2026, 22, 22, 12000000, 1300000, 0, 0, 0, 0, 13300000, 960000, 180000, 120000, 0, 0, 0, 12040000, 'Đã thanh toán', '2026-02-05 10:00:00'),
                (12, 1, 2026, 22, 22, 13000000, 1600000, 0, 0, 0, 0, 14600000, 1040000, 195000, 130000, 0, 0, 0, 13235000, 'Đã thanh toán', '2026-02-05 10:00:00'),
                (13, 1, 2026, 22, 22, 10000000, 1300000, 0, 0, 0, 0, 11300000, 800000, 150000, 100000, 0, 0, 0, 10250000, 'Đã thanh toán', '2026-02-05 10:00:00'),
                (14, 1, 2026, 22, 22, 18000000, 3300000, 0, 0, 0, 0, 21300000, 1440000, 270000, 180000, 240000, 0, 0, 19170000, 'Đã thanh toán', '2026-02-05 10:00:00'),
                (15, 1, 2026, 22, 22, 9000000, 1300000, 0, 0, 0, 0, 10300000, 720000, 135000, 90000, 0, 0, 0, 9355000, 'Đã thanh toán', '2026-02-05 10:00:00')
            ");

            // --- Salary Advances ---
            $conn->query("INSERT INTO salary_advance (employee_id, amount, reason, month, year, status, approved_by) VALUES
                (7, 5000000, 'Ứng trước lương tháng 3', 3, 2026, 'Đã duyệt', 1),
                (8, 3000000, 'Chi phí cá nhân khẩn cấp', 3, 2026, 'Đã duyệt', 1),
                (13, 2000000, 'Ứng lương tháng 3', 3, 2026, 'Chờ duyệt', NULL)
            ");

            // --- Activity Log ---
            $conn->query("INSERT INTO activity_log (user_id, action, module, ip_address, created_at) VALUES
                (1, 'Đăng nhập hệ thống', 'auth', '127.0.0.1', '2026-03-01 08:00:00'),
                (1, 'Tính lương tháng 1/2026', 'salary', '127.0.0.1', '2026-02-05 09:00:00'),
                (1, 'Duyệt lương tháng 1/2026 cho toàn bộ NV', 'salary', '127.0.0.1', '2026-02-05 09:30:00'),
                (2, 'Đăng nhập hệ thống', 'auth', '127.0.0.1', '2026-03-01 08:15:00'),
                (2, 'Thêm nhân viên NV0015 - Mai Thị Tuyết', 'employees', '127.0.0.1', '2026-01-09 10:00:00'),
                (2, 'Duyệt đơn nghỉ phép - Lê Hoàng Cường', 'leaves', '127.0.0.1', '2026-02-08 14:00:00'),
                (3, 'Đăng nhập hệ thống', 'auth', '127.0.0.1', '2026-03-01 08:30:00'),
                (1, 'Duyệt tạm ứng - Đỗ Thanh Hương', 'salary', '127.0.0.1', '2026-02-28 15:00:00'),
                (2, 'Cập nhật chấm công tháng 2/2026', 'attendance', '127.0.0.1', '2026-02-28 16:00:00'),
                (1, 'Xuất báo cáo lương tháng 1/2026', 'reports', '127.0.0.1', '2026-02-10 11:00:00')
            ");
        }

        $success = true;
        $message = 'Cài đặt cơ sở dữ liệu thành công!';

    } catch (Exception $ex) {
        $message = 'Lỗi: ' . $ex->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cài đặt - Hệ thống Quản lý Nhân sự</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f0f2f5; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .setup-card { max-width: 650px; width: 100%; }
        .setup-header { background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: #fff; border-radius: 12px 12px 0 0; padding: 32px; text-align: center; }
        .setup-header h2 { margin: 0 0 8px; font-weight: 700; }
        .setup-header p { margin: 0; opacity: 0.9; }
        .info-table td { padding: 6px 12px; font-size: 14px; }
        .info-table td:first-child { font-weight: 600; width: 180px; }
    </style>
</head>
<body>
    <div class="setup-card">
        <div class="setup-header">
            <h2><i class="fas fa-cogs me-2"></i>Cài đặt hệ thống</h2>
            <p>Hệ thống Quản lý Nhân sự - Tiền lương</p>
        </div>
        <div class="card border-0 shadow" style="border-radius: 0 0 12px 12px;">
            <div class="card-body p-4">
                <?php if ($message): ?>
                    <div class="alert alert-<?= $success ? 'success' : 'danger' ?> mb-3">
                        <i class="fas fa-<?= $success ? 'check-circle' : 'exclamation-circle' ?> me-1"></i>
                        <?= $message ?>
                    </div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="text-center mb-3">
                        <i class="fas fa-check-circle text-success" style="font-size: 48px;"></i>
                        <h5 class="mt-3">Cài đặt hoàn tất!</h5>
                    </div>

                    <h6 class="mb-2"><i class="fas fa-users me-1"></i> Tài khoản đăng nhập</h6>
                    <table class="table table-sm table-bordered info-table mb-3">
                        <thead class="table-light">
                            <tr><th>Vai trò</th><th>Username</th><th>Mật khẩu</th></tr>
                        </thead>
                        <tbody>
                            <tr><td><span class="badge bg-danger">Admin</span></td><td>admin</td><td>admin123</td></tr>
                            <tr><td><span class="badge bg-primary">Nhân sự (HR)</span></td><td>hr01</td><td>hr123</td></tr>
                            <tr><td><span class="badge bg-success">Kế toán</span></td><td>ketoan01</td><td>ketoan123</td></tr>
                        </tbody>
                    </table>

                    <h6 class="mb-2"><i class="fas fa-database me-1"></i> Dữ liệu đã tạo</h6>
                    <table class="table table-sm table-bordered info-table mb-3">
                        <tbody>
                            <tr><td>Phòng ban</td><td>7 phòng ban</td></tr>
                            <tr><td>Chức vụ</td><td>15 chức vụ</td></tr>
                            <tr><td>Nhân viên</td><td>15 nhân viên</td></tr>
                            <tr><td>Phụ cấp</td><td>6 loại phụ cấp</td></tr>
                            <tr><td>Bảng lương</td><td>Tháng 1/2026 (15 bảng)</td></tr>
                            <tr><td>Chấm công</td><td>Tháng 2/2026</td></tr>
                            <tr><td>Nghỉ phép</td><td>8 đơn nghỉ phép</td></tr>
                            <tr><td>Hợp đồng</td><td>15 hợp đồng</td></tr>
                            <tr><td>Khen thưởng</td><td>6 bản ghi</td></tr>
                        </tbody>
                    </table>

                    <a href="/BaitaplonPHP/login" class="btn btn-primary w-100">
                        <i class="fas fa-sign-in-alt me-1"></i>Đăng nhập hệ thống
                    </a>
                <?php else: ?>
                    <div class="mb-3">
                        <h6><i class="fas fa-info-circle me-1 text-primary"></i> Thông tin kết nối</h6>
                        <table class="table table-sm info-table">
                            <tr><td>MySQL Host</td><td><?= $db_host ?></td></tr>
                            <tr><td>MySQL User</td><td><?= $db_user ?></td></tr>
                            <tr><td>Database</td><td><?= $db_name ?></td></tr>
                        </table>
                    </div>

                    <div class="alert alert-warning small">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Hệ thống sẽ tạo database <strong><?= $db_name ?></strong>, tất cả 13 bảng và dữ liệu mẫu.
                    </div>

                    <form method="POST">
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="reset" value="1" id="resetDb">
                            <label class="form-check-label text-danger small" for="resetDb">
                                <strong>Xóa toàn bộ database cũ</strong> (nếu đã tồn tại) và tạo lại từ đầu
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 btn-lg">
                            <i class="fas fa-play me-2"></i>Bắt đầu cài đặt
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>

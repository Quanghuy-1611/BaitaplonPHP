# TÀI LIỆU THIẾT KẾ HỆ THỐNG
# HỆ THỐNG QUẢN LÝ NHÂN SỰ - TIỀN LƯƠNG

> Hướng dẫn: Copy từng block code PlantUML vào https://www.plantuml.com/plantuml/uml/ để xuất hình ảnh.

---

## 2.1. BIỂU ĐỒ PHÂN CẤP CHỨC NĂNG HỆ THỐNG

```plantuml
@startmindmap
* Hệ thống Quản lý\nNhân sự - Tiền lương
** 1. Quản lý đăng nhập
*** 1.1 Đăng nhập
*** 1.2 Đăng xuất
*** 1.3 Phân quyền (RBAC)
** 2. Quản lý nhân viên
*** 2.1 Thêm nhân viên
*** 2.2 Sửa thông tin NV
*** 2.3 Xóa nhân viên
*** 2.4 Xem danh sách NV
*** 2.5 Tìm kiếm NV
*** 2.6 Xem chi tiết NV
** 3. Quản lý phòng ban
*** 3.1 Thêm phòng ban
*** 3.2 Sửa phòng ban
*** 3.3 Xóa phòng ban
*** 3.4 Xem danh sách PB
** 4. Quản lý chức vụ
*** 4.1 Thêm chức vụ
*** 4.2 Sửa chức vụ
*** 4.3 Xóa chức vụ
*** 4.4 Xem danh sách CV
** 5. Quản lý chấm công
*** 5.1 Chấm công đơn lẻ
*** 5.2 Chấm công hàng loạt
*** 5.3 Xem bảng chấm công
*** 5.4 Xóa bản ghi chấm công
** 6. Quản lý nghỉ phép
*** 6.1 Tạo đơn nghỉ phép
*** 6.2 Duyệt đơn nghỉ phép
*** 6.3 Từ chối đơn nghỉ phép
*** 6.4 Xem danh sách đơn
*** 6.5 Xem phép còn lại
** 7. Quản lý phụ cấp
*** 7.1 Thêm loại phụ cấp
*** 7.2 Sửa phụ cấp
*** 7.3 Xóa phụ cấp
*** 7.4 Gán phụ cấp cho NV
*** 7.5 Gỡ phụ cấp khỏi NV
** 8. Quản lý khen thưởng\n/ kỷ luật
*** 8.1 Thêm khen thưởng
*** 8.2 Thêm kỷ luật
*** 8.3 Sửa bản ghi
*** 8.4 Xóa bản ghi
** 9. Quản lý tiền lương
*** 9.1 Tính lương tháng
*** 9.2 Duyệt bảng lương
*** 9.3 Thanh toán lương
*** 9.4 In phiếu lương
*** 9.5 Xuất CSV
*** 9.6 Tạm ứng lương
**** 9.6.1 Tạo phiếu tạm ứng
**** 9.6.2 Duyệt tạm ứng
**** 9.6.3 Từ chối tạm ứng
** 10. Quản lý hợp đồng
*** 10.1 Thêm hợp đồng
*** 10.2 Sửa hợp đồng
*** 10.3 Xóa hợp đồng
*** 10.4 Xem DS hợp đồng
*** 10.5 Cảnh báo hết hạn
** 11. Dashboard
*** 11.1 Tổng quan (Admin/HR)
*** 11.2 Trang cá nhân (NV)
** 12. Nhật ký hoạt động
*** 12.1 Xem lịch sử thao tác
@endmindmap
```

---

## 2.2. BIỂU ĐỒ USE CASE

### 2.2.1. Use Case tổng quát

```plantuml
@startuml
left to right direction
skinparam actorStyle awesome
skinparam packageStyle rectangle

actor "Quản trị viên\n(Admin)" as Admin
actor "Nhân sự\n(HR)" as HR
actor "Kế toán\n(Accountant)" as Acc
actor "Nhân viên\n(Employee)" as Emp

rectangle "Hệ thống Quản lý Nhân sự - Tiền lương" {
    usecase "UC01: Đăng nhập / Đăng xuất" as UC01
    usecase "UC02: Quản lý nhân viên" as UC02
    usecase "UC03: Quản lý phòng ban" as UC03
    usecase "UC04: Quản lý chức vụ" as UC04
    usecase "UC05: Quản lý chấm công" as UC05
    usecase "UC06: Quản lý nghỉ phép" as UC06
    usecase "UC07: Quản lý phụ cấp" as UC07
    usecase "UC08: Quản lý khen thưởng\n/ kỷ luật" as UC08
    usecase "UC09: Quản lý tiền lương" as UC09
    usecase "UC10: Quản lý hợp đồng" as UC10
    usecase "UC11: Xem Dashboard" as UC11
    usecase "UC12: Xem nhật ký\nhoạt động" as UC12
    usecase "UC13: Quản lý tài khoản" as UC13
    usecase "UC14: Xem phiếu lương\ncá nhân" as UC14
    usecase "UC15: Tạo đơn nghỉ phép" as UC15
    usecase "UC16: Xem chấm công\ncá nhân" as UC16
}

Admin --> UC01
Admin --> UC02
Admin --> UC03
Admin --> UC04
Admin --> UC05
Admin --> UC06
Admin --> UC07
Admin --> UC08
Admin --> UC09
Admin --> UC10
Admin --> UC11
Admin --> UC12
Admin --> UC13

HR --> UC01
HR --> UC02
HR --> UC03
HR --> UC04
HR --> UC05
HR --> UC06
HR --> UC07
HR --> UC08
HR --> UC10
HR --> UC11

Acc --> UC01
Acc --> UC09
Acc --> UC11

Emp --> UC01
Emp --> UC11
Emp --> UC14
Emp --> UC15
Emp --> UC16
@enduml
```

### 2.2.2. Phân rã Use Case - Quản lý nhân viên (UC02)

```plantuml
@startuml
left to right direction
skinparam actorStyle awesome

actor "Quản trị viên" as Admin
actor "Nhân sự" as HR

rectangle "UC02: Quản lý nhân viên" {
    usecase "UC02.1: Xem danh sách\nnhân viên" as UC021
    usecase "UC02.2: Tìm kiếm\nnhân viên" as UC022
    usecase "UC02.3: Thêm nhân viên" as UC023
    usecase "UC02.4: Sửa thông tin\nnhân viên" as UC024
    usecase "UC02.5: Xóa nhân viên" as UC025
    usecase "UC02.6: Xem chi tiết\nhồ sơ nhân viên" as UC026

    UC021 ..> UC022 : <<include>>
    UC023 ..> UC021 : <<extend>>
}

Admin --> UC021
Admin --> UC023
Admin --> UC024
Admin --> UC025
Admin --> UC026

HR --> UC021
HR --> UC023
HR --> UC024
HR --> UC025
HR --> UC026
@enduml
```

### 2.2.3. Phân rã Use Case - Quản lý tiền lương (UC09)

```plantuml
@startuml
left to right direction
skinparam actorStyle awesome

actor "Quản trị viên" as Admin
actor "Kế toán" as Acc

rectangle "UC09: Quản lý tiền lương" {
    usecase "UC09.1: Tính lương\ntháng" as UC091
    usecase "UC09.2: Xem bảng lương" as UC092
    usecase "UC09.3: Duyệt\nbảng lương" as UC093
    usecase "UC09.4: Đánh dấu\nthanh toán" as UC094
    usecase "UC09.5: In phiếu lương" as UC095
    usecase "UC09.6: Xuất CSV" as UC096
    usecase "UC09.7: Tạm ứng lương" as UC097

    UC091 ..> UC092 : <<extend>>
    UC093 ..> UC092 : <<include>>
    UC094 ..> UC093 : <<include>>
}

Admin --> UC091
Admin --> UC092
Admin --> UC093
Admin --> UC094
Admin --> UC095
Admin --> UC096
Admin --> UC097

Acc --> UC091
Acc --> UC092
Acc --> UC093
Acc --> UC094
Acc --> UC095
Acc --> UC096
Acc --> UC097
@enduml
```

### 2.2.4. Phân rã Use Case - Quản lý nghỉ phép (UC06)

```plantuml
@startuml
left to right direction
skinparam actorStyle awesome

actor "Quản trị viên" as Admin
actor "Nhân sự" as HR
actor "Nhân viên" as Emp

rectangle "UC06: Quản lý nghỉ phép" {
    usecase "UC06.1: Xem danh sách\nđơn nghỉ phép" as UC061
    usecase "UC06.2: Tạo đơn\nnghỉ phép" as UC062
    usecase "UC06.3: Duyệt đơn\nnghỉ phép" as UC063
    usecase "UC06.4: Từ chối đơn\nnghỉ phép" as UC064
    usecase "UC06.5: Xem phép\ncòn lại" as UC065
}

Admin --> UC061
Admin --> UC062
Admin --> UC063
Admin --> UC064

HR --> UC061
HR --> UC062
HR --> UC063
HR --> UC064

Emp --> UC061
Emp --> UC062
Emp --> UC065
@enduml
```

### 2.2.5. Phân rã Use Case - Quản lý chấm công (UC05)

```plantuml
@startuml
left to right direction
skinparam actorStyle awesome

actor "Quản trị viên" as Admin
actor "Nhân sự" as HR
actor "Nhân viên" as Emp

rectangle "UC05: Quản lý chấm công" {
    usecase "UC05.1: Xem bảng\nchấm công" as UC051
    usecase "UC05.2: Chấm công\nđơn lẻ" as UC052
    usecase "UC05.3: Chấm công\nhàng loạt" as UC053
    usecase "UC05.4: Xóa bản ghi\nchấm công" as UC054
    usecase "UC05.5: Xem chấm công\ncá nhân" as UC055

    UC051 ..> UC055 : <<extend>>
}

Admin --> UC051
Admin --> UC052
Admin --> UC053
Admin --> UC054

HR --> UC051
HR --> UC052
HR --> UC053
HR --> UC054

Emp --> UC055
@enduml
```

---

## 2.3. BIỂU ĐỒ TUẦN TỰ (SEQUENCE DIAGRAM)

### 2.3.1. Biểu đồ tuần tự - Đăng nhập hệ thống

```plantuml
@startuml
skinparam sequenceMessageAlign center
actor "Người dùng" as User
participant "AuthController" as Auth
participant "UserModel" as UM
database "Database" as DB

User -> Auth : Nhập username, password
Auth -> UM : findByUsername(username)
UM -> DB : SELECT * FROM users\nWHERE username = ?
DB --> UM : Trả về thông tin user
UM --> Auth : Dữ liệu user

alt Đăng nhập thành công
    Auth -> Auth : password_verify(password, hash)
    Auth -> Auth : Lưu session:\nuser_id, username,\nfull_name, role, employee_id
    Auth -> Auth : logActivity("Đăng nhập")
    Auth --> User : Redirect → Dashboard
else Đăng nhập thất bại
    Auth --> User : Hiển thị lỗi:\n"Tên đăng nhập hoặc\nmật khẩu không đúng"
end
@enduml
```

### 2.3.2. Biểu đồ tuần tự - Tính lương tháng

```plantuml
@startuml
skinparam sequenceMessageAlign center
actor "Kế toán / Admin" as User
participant "SalaryController" as SC
participant "EmployeeModel" as EM
participant "AttendanceModel" as AM
participant "AllowanceModel" as ALM
participant "RewardModel" as RM
participant "SalaryModel" as SM
database "Database" as DB

User -> SC : Chọn tháng/năm,\nbấm "Tính lương"
SC -> SC : verifyCsrf()
SC -> EM : getActiveEmployees()
EM -> DB : SELECT * FROM employees\nWHERE status = 'Đang làm'
DB --> EM : Danh sách nhân viên
EM --> SC : employees

loop Cho mỗi nhân viên
    SC -> AM : getEmployeeStats(empId, month, year)
    AM -> DB : SELECT COUNT(ngày công),\nSUM(overtime)
    DB --> AM : attStats
    AM --> SC : {ngay_cong, overtime}

    SC -> ALM : getTotalAllowance(empId)
    ALM -> DB : SELECT SUM(amount)\nFROM employee_allowances
    DB --> ALM : totalAllowance
    ALM --> SC : totalAllowance

    SC -> RM : getEmployeeTotals(empId, month, year)
    RM -> DB : SELECT SUM(khen thưởng),\nSUM(kỷ luật)
    DB --> RM : {reward, discipline}
    RM --> SC : {totalReward, totalDiscipline}

    SC -> SM : getApprovedAdvance(empId, month, year)
    SM -> DB : SELECT SUM(amount)\nFROM salary_advance
    DB --> SM : advanceTotal
    SM --> SC : advanceSalary

    SC -> SC : Tính toán:\n- Lương theo ngày công\n- Tăng ca (1.5x)\n- Gross = lương + phụ cấp\n  + tăng ca + thưởng - phạt\n- BHXH(8%) + BHYT(1.5%)\n  + BHTN(1%)\n- Thuế TNCN lũy tiến\n- Net = Gross - BH - Thuế\n  - Tạm ứng

    SC -> SM : calculate(empId, month, year, data)
    SM -> DB : INSERT/UPDATE salary
    DB --> SM : OK
end

SC --> User : Thông báo:\n"Đã tính lương cho N nhân viên"
@enduml
```

### 2.3.3. Biểu đồ tuần tự - Xin nghỉ phép (Nhân viên)

```plantuml
@startuml
skinparam sequenceMessageAlign center
actor "Nhân viên" as Emp
participant "LeaveController" as LC
participant "LeaveModel" as LM
database "Database" as DB
actor "Nhân sự (HR)" as HR

== Nhân viên tạo đơn ==
Emp -> LC : Tạo đơn nghỉ phép\n(loại, từ ngày, đến ngày, lý do)
LC -> LC : verifyCsrf()
LC -> LC : employee_id = \nSESSION['employee_id']
LC -> LC : Tính số ngày nghỉ
LC -> LM : create(data)
LM -> DB : INSERT INTO leaves\n(employee_id, leave_type,\nstart_date, end_date, days,\nreason, status='Chờ duyệt')
DB --> LM : OK
LM --> LC : true
LC -> LC : logActivity("Tạo đơn nghỉ phép")
LC --> Emp : Thông báo:\n"Tạo đơn thành công"

== HR duyệt đơn ==
HR -> LC : Xem danh sách đơn\nchờ duyệt
LC -> LM : getList(filters)
LM -> DB : SELECT leaves + employees
DB --> LM : Danh sách đơn
LM --> LC : leaves
LC --> HR : Hiển thị DS đơn

HR -> LC : Bấm "Duyệt" (id)
LC -> LC : requireRole('leaves')
LC -> LM : approve(id, approvedBy)
LM -> DB : UPDATE leaves\nSET status='Đã duyệt',\napproved_by=?, approved_at=NOW()
DB --> LM : OK
LM --> LC : true
LC --> HR : Thông báo:\n"Đã duyệt đơn nghỉ phép"
@enduml
```

### 2.3.4. Biểu đồ tuần tự - Thêm nhân viên

```plantuml
@startuml
skinparam sequenceMessageAlign center
actor "HR / Admin" as User
participant "EmployeeController" as EC
participant "EmployeeModel" as EM
participant "DepartmentModel" as DM
participant "PositionModel" as PM
database "Database" as DB

User -> EC : Truy cập trang\nthêm nhân viên
EC -> DM : getSelectList()
DM -> DB : SELECT id, name\nFROM departments
DB --> DM : departments
EC -> PM : getSelectList()
PM -> DB : SELECT id, name, base_salary\nFROM positions
DB --> PM : positions
EC -> EM : generateNextCode()
EM -> DB : SELECT MAX(employee_code)\nFROM employees
DB --> EM : "NV0015"
EM --> EC : "NV0016"
EC --> User : Hiển thị form\nvới mã NV tự sinh

User -> EC : Nhập thông tin,\nbấm "Lưu"
EC -> EC : verifyCsrf()
EC -> EM : codeExists(code)
EM -> DB : SELECT id FROM employees\nWHERE employee_code = ?
DB --> EM : null (chưa tồn tại)
EM --> EC : false

EC -> EM : create(data)
EM -> DB : INSERT INTO employees\n(employee_code, full_name,\ngender, birth_date, ...)
DB --> EM : lastInsertId
EM --> EC : newId

EC -> EC : logActivity("Thêm NV")
EC --> User : Redirect → Danh sách NV\n+ thông báo thành công
@enduml
```

### 2.3.5. Biểu đồ tuần tự - Chấm công hàng loạt

```plantuml
@startuml
skinparam sequenceMessageAlign center
actor "HR / Admin" as User
participant "AttendanceController" as AC
participant "AttendanceModel" as AM
database "Database" as DB

User -> AC : Chọn ngày, trạng thái,\ngiờ vào/ra, danh sách NV
AC -> AC : verifyCsrf()
AC -> AM : addBulk(date, employeeIds,\nstatus, checkIn, checkOut)

loop Cho mỗi nhân viên được chọn
    AM -> AM : addSingle(data)
    AM -> DB : SELECT id FROM attendance\nWHERE employee_id=?\nAND work_date=?
    alt Đã tồn tại
        AM -> DB : UPDATE attendance\nSET status=?, check_in=?,\ncheck_out=?
    else Chưa tồn tại
        AM -> DB : INSERT INTO attendance\n(employee_id, work_date,\nstatus, check_in, check_out)
    end
    DB --> AM : OK
end

AM --> AC : count (số NV đã chấm)
AC -> AC : logActivity("Chấm công hàng loạt")
AC --> User : Thông báo:\n"Đã chấm công cho N nhân viên"
@enduml
```

---

## 2.4. BIỂU ĐỒ LỚP (CLASS DIAGRAM)

```plantuml
@startuml
skinparam classAttributeIconSize 0

' ==================== CORE ====================
abstract class Controller {
    # conn : mysqli
    # currentUser : array
    # rolePermissions : array
    --
    + __construct()
    # view(viewPath, data)
    # viewOnly(viewPath, data)
    # redirect(path)
    # requireAuth()
    # requireRole(permission)
    # hasPermission(permission) : bool
    # isPost() : bool
    # post(key, default) : string
    # get(key, default) : string
    # setFlash(type, message)
    # verifyCsrf()
    # generateCsrf() : string
    # logActivity(action, module, detail)
    # model(modelName) : Model
}

abstract class Model {
    # conn : mysqli
    # table : string
    --
    + __construct()
    # query(sql, params, types) : mysqli_result
    # queryOne(sql, params, types) : array|null
    # execute(sql, params, types) : bool
    # insert(sql, params, types) : int
    # findAll(orderBy) : mysqli_result
    # findById(id) : array|null
    # count(where) : int
    # delete(id) : bool
    # emptyResult() : mysqli_result
}

class App {
    # controller : string
    # method : string
    # params : array
    --
    + __construct()
    - parseUrl() : array
}

' ==================== CONTROLLERS ====================
class AuthController {
    + login()
    + logout()
    + notFound()
}

class DashboardController {
    + index()
    - employeeDashboard()
}

class EmployeeController {
    + index()
    + create()
    + store()
    + edit(id)
    + update(id)
    + show(id)
    + delete(id)
}

class DepartmentController {
    + index()
}

class PositionController {
    + index()
}

class AttendanceController {
    + index()
    + store()
    + bulkStore()
    + delete(id)
}

class LeaveController {
    + index()
    - createLeave(model, isEmployee)
    - approveLeave(model)
    - rejectLeave(model)
}

class AllowanceController {
    + index()
    + assign()
    + remove()
}

class RewardController {
    + index()
}

class SalaryController {
    + index()
    + calculate()
    + detail(id)
    + advance()
    + payslip(id)
    + export()
}

class ContractController {
    + index()
    - createContract(model)
    - updateContract(model)
    - deleteContract(model)
}

' ==================== MODELS ====================
class UserModel {
    # table = "users"
    --
    + findByUsername(username) : array
    + getAllUsers() : mysqli_result
    + create(data) : int
    + update(id, data) : bool
    + updateProfile(id, data) : bool
    + changePassword(id, newPassword) : bool
    + usernameExists(username, excludeId) : bool
}

class EmployeeModel {
    # table = "employees"
    --
    + getList(filters, page, perPage) : array
    + getDetail(id) : array
    + create(data) : int
    + update(id, data) : bool
    + codeExists(code, excludeId) : bool
    + getActiveEmployees() : mysqli_result
    + getActiveCount() : int
    + getNewThisMonth() : int
    + getByDepartmentStats() : mysqli_result
    + getRecentEmployees(limit) : mysqli_result
    + generateNextCode() : string
}

class DepartmentModel {
    # table = "departments"
    --
    + getAllWithCount() : mysqli_result
    + create(data) : int
    + update(id, data) : bool
    + hasEmployees(id) : bool
    + getSelectList() : mysqli_result
}

class PositionModel {
    # table = "positions"
    --
    + getAllWithCount() : mysqli_result
    + create(data) : int
    + update(id, data) : bool
    + hasEmployees(id) : bool
    + getSelectList() : mysqli_result
    + getByDepartment(deptId) : mysqli_result
}

class AttendanceModel {
    # table = "attendance"
    --
    + getByMonth(month, year, deptId) : mysqli_result
    + getStats(month, year) : array
    + addSingle(data) : bool
    + addBulk(date, empIds, status, in, out) : int
    + getEmployeeAttendance(empId, m, y) : mysqli_result
    + getEmployeeStats(empId, m, y) : array
    + deleteRecord(id) : bool
}

class LeaveModel {
    # table = "leaves"
    --
    + getList(filters) : mysqli_result
    + create(data) : int
    + approve(id, approvedBy) : bool
    + reject(id, approvedBy) : bool
    + getEmployeeLeaves(empId, year) : mysqli_result
    + getLeaveBalance(empId, year) : array
    + getPendingCount() : int
    + getStats(month, year) : array
}

class AllowanceModel {
    # table = "allowances"
    --
    + getAllWithCount() : mysqli_result
    + create(data) : int
    + update(id, data) : bool
    + hasEmployees(allowanceId) : bool
    + isAssigned(empId, allowanceId) : bool
    + getAllAssignments() : mysqli_result
    + getEmployeeAllowances(empId) : mysqli_result
    + assignToEmployee(empId, allowId, amount) : bool
    + removeFromEmployee(id) : bool
    + getTotalAllowance(empId) : float
}

class RewardModel {
    # table = "rewards"
    --
    + getList(filters) : mysqli_result
    + create(data) : int
    + update(id, data) : bool
    + getByEmployee(empId, m, y) : mysqli_result
    + getStats(month, year) : array
    + getEmployeeTotals(empId, m, y) : array
}

class SalaryModel {
    # table = "salary"
    --
    + getByMonth(month, year, status) : mysqli_result
    + getDetail(id) : array
    + getTotals(month, year) : array
    + calculate(empId, month, year, data) : bool
    + approve(id) : bool
    + approveAll(month, year) : bool
    + markPaid(id) : bool
    + getByEmployeeId(empId, m, y) : array
    + getEmployeeSalaryHistory(empId) : mysqli_result
    + getMonthlyTrend(months) : mysqli_result
    + getAdvances(month, year, status) : mysqli_result
    + createAdvance(data) : int
    + approveAdvance(id) : bool
    + rejectAdvance(id) : bool
    + getApprovedAdvance(empId, m, y) : float
}

class ContractModel {
    # table = "contracts"
    --
    + getList(filters) : mysqli_result
    + create(data) : int
    + update(id, data) : bool
    + getByEmployee(empId) : mysqli_result
    + getExpiringContracts(days) : mysqli_result
    + getStats() : array
}

' ==================== RELATIONSHIPS ====================
Controller <|-- AuthController
Controller <|-- DashboardController
Controller <|-- EmployeeController
Controller <|-- DepartmentController
Controller <|-- PositionController
Controller <|-- AttendanceController
Controller <|-- LeaveController
Controller <|-- AllowanceController
Controller <|-- RewardController
Controller <|-- SalaryController
Controller <|-- ContractController

Model <|-- UserModel
Model <|-- EmployeeModel
Model <|-- DepartmentModel
Model <|-- PositionModel
Model <|-- AttendanceModel
Model <|-- LeaveModel
Model <|-- AllowanceModel
Model <|-- RewardModel
Model <|-- SalaryModel
Model <|-- ContractModel

App --> Controller : khởi tạo
Controller --> Model : sử dụng

EmployeeController ..> EmployeeModel
EmployeeController ..> DepartmentModel
EmployeeController ..> PositionModel
DashboardController ..> EmployeeModel
DashboardController ..> SalaryModel
DashboardController ..> LeaveModel
DashboardController ..> ContractModel
DashboardController ..> AttendanceModel
SalaryController ..> SalaryModel
SalaryController ..> EmployeeModel
SalaryController ..> AttendanceModel
SalaryController ..> AllowanceModel
SalaryController ..> RewardModel
@enduml
```

---

## 2.6. THIẾT KẾ DATABASE

### Mô tả tổng quan

- **DBMS:** MySQL (InnoDB)
- **Character Set:** utf8mb4 (hỗ trợ tiếng Việt đầy đủ)
- **Collation:** utf8mb4_unicode_ci
- **Database name:** ql_nhansu_luong
- **Tổng số bảng:** 13 bảng

### Danh sách các bảng

| STT | Tên bảng | Mô tả | Số trường |
|-----|----------|-------|-----------|
| 1 | users | Tài khoản người dùng | 10 |
| 2 | departments | Phòng ban | 5 |
| 3 | positions | Chức vụ | 6 |
| 4 | employees | Nhân viên | 19 |
| 5 | attendance | Chấm công | 9 |
| 6 | allowances | Loại phụ cấp | 4 |
| 7 | employee_allowances | Gán phụ cấp cho NV | 5 |
| 8 | rewards | Khen thưởng / Kỷ luật | 7 |
| 9 | salary | Bảng lương | 22 |
| 10 | salary_advance | Tạm ứng lương | 9 |
| 11 | leaves | Nghỉ phép | 10 |
| 12 | contracts | Hợp đồng lao động | 10 |
| 13 | activity_log | Nhật ký hoạt động | 7 |

---

## 3.1. XÂY DỰNG DATABASE TRÊN MYSQL

### SQL tạo database

```sql
CREATE DATABASE IF NOT EXISTS `ql_nhansu_luong`
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE `ql_nhansu_luong`;
```

---

## 3.2.1. CẤU TRÚC CÁC BẢNG

### Bảng 1: users (Tài khoản người dùng)

| Tên trường | Kiểu dữ liệu | Ràng buộc | Mô tả |
|-----------|--------------|-----------|-------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Mã tài khoản |
| username | VARCHAR(50) | NOT NULL, UNIQUE | Tên đăng nhập |
| password | VARCHAR(255) | NOT NULL | Mật khẩu (bcrypt hash) |
| full_name | VARCHAR(100) | NOT NULL | Họ và tên |
| email | VARCHAR(100) | DEFAULT NULL | Email |
| role | ENUM('admin','hr','accountant','employee') | NOT NULL, DEFAULT 'hr' | Vai trò |
| employee_id | INT | DEFAULT NULL | Liên kết nhân viên |
| status | TINYINT(1) | NOT NULL, DEFAULT 1 | Trạng thái (1=active) |
| created_at | DATETIME | DEFAULT CURRENT_TIMESTAMP | Ngày tạo |
| updated_at | DATETIME | ON UPDATE CURRENT_TIMESTAMP | Ngày cập nhật |

### Bảng 2: departments (Phòng ban)

| Tên trường | Kiểu dữ liệu | Ràng buộc | Mô tả |
|-----------|--------------|-----------|-------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Mã phòng ban |
| name | VARCHAR(100) | NOT NULL | Tên phòng ban |
| manager_name | VARCHAR(100) | DEFAULT NULL | Tên trưởng phòng |
| phone | VARCHAR(20) | DEFAULT NULL | Số điện thoại |
| description | TEXT | DEFAULT NULL | Mô tả |
| created_at | DATETIME | DEFAULT CURRENT_TIMESTAMP | Ngày tạo |

### Bảng 3: positions (Chức vụ)

| Tên trường | Kiểu dữ liệu | Ràng buộc | Mô tả |
|-----------|--------------|-----------|-------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Mã chức vụ |
| name | VARCHAR(100) | NOT NULL | Tên chức vụ |
| department_id | INT | FK → departments(id), ON DELETE SET NULL | Phòng ban |
| base_salary | DECIMAL(15,0) | DEFAULT 0 | Lương cơ bản |
| description | TEXT | DEFAULT NULL | Mô tả |
| created_at | DATETIME | DEFAULT CURRENT_TIMESTAMP | Ngày tạo |

### Bảng 4: employees (Nhân viên)

| Tên trường | Kiểu dữ liệu | Ràng buộc | Mô tả |
|-----------|--------------|-----------|-------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Mã nhân viên (hệ thống) |
| employee_code | VARCHAR(20) | NOT NULL, UNIQUE | Mã nhân viên (NV0001) |
| full_name | VARCHAR(100) | NOT NULL | Họ và tên |
| gender | VARCHAR(10) | DEFAULT 'Nam' | Giới tính |
| birth_date | DATE | DEFAULT NULL | Ngày sinh |
| id_card | VARCHAR(20) | DEFAULT NULL | Số CCCD/CMND |
| phone | VARCHAR(20) | DEFAULT NULL | Số điện thoại |
| email | VARCHAR(100) | DEFAULT NULL | Email |
| address | TEXT | DEFAULT NULL | Địa chỉ |
| department_id | INT | FK → departments(id), ON DELETE SET NULL | Phòng ban |
| position_id | INT | FK → positions(id), ON DELETE SET NULL | Chức vụ |
| hire_date | DATE | DEFAULT NULL | Ngày vào làm |
| contract_type | VARCHAR(50) | DEFAULT NULL | Loại hợp đồng |
| base_salary | DECIMAL(15,0) | DEFAULT 0 | Lương cơ bản |
| bank_account | VARCHAR(30) | DEFAULT NULL | Số tài khoản NH |
| bank_name | VARCHAR(100) | DEFAULT NULL | Tên ngân hàng |
| status | VARCHAR(20) | DEFAULT 'Đang làm' | Trạng thái |
| created_at | DATETIME | DEFAULT CURRENT_TIMESTAMP | Ngày tạo |
| updated_at | DATETIME | ON UPDATE CURRENT_TIMESTAMP | Ngày cập nhật |

### Bảng 5: attendance (Chấm công)

| Tên trường | Kiểu dữ liệu | Ràng buộc | Mô tả |
|-----------|--------------|-----------|-------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Mã bản ghi |
| employee_id | INT | NOT NULL, FK → employees(id), ON DELETE CASCADE | Nhân viên |
| work_date | DATE | NOT NULL | Ngày làm việc |
| status | VARCHAR(20) | DEFAULT 'Đi làm' | Trạng thái (Đi làm/Đi muộn/Nghỉ phép/Vắng) |
| check_in | TIME | DEFAULT NULL | Giờ vào |
| check_out | TIME | DEFAULT NULL | Giờ ra |
| overtime_hours | DECIMAL(5,1) | DEFAULT 0 | Số giờ tăng ca |
| note | TEXT | DEFAULT NULL | Ghi chú |
| created_at | DATETIME | DEFAULT CURRENT_TIMESTAMP | Ngày tạo |

> UNIQUE KEY `uk_emp_date` (`employee_id`, `work_date`) — Mỗi nhân viên chỉ có 1 bản ghi/ngày.

### Bảng 6: allowances (Loại phụ cấp)

| Tên trường | Kiểu dữ liệu | Ràng buộc | Mô tả |
|-----------|--------------|-----------|-------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Mã phụ cấp |
| name | VARCHAR(100) | NOT NULL | Tên phụ cấp |
| default_amount | DECIMAL(15,0) | DEFAULT 0 | Mức phụ cấp mặc định |
| description | TEXT | DEFAULT NULL | Mô tả |
| created_at | DATETIME | DEFAULT CURRENT_TIMESTAMP | Ngày tạo |

### Bảng 7: employee_allowances (Gán phụ cấp cho NV)

| Tên trường | Kiểu dữ liệu | Ràng buộc | Mô tả |
|-----------|--------------|-----------|-------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Mã bản ghi |
| employee_id | INT | NOT NULL, FK → employees(id), ON DELETE CASCADE | Nhân viên |
| allowance_id | INT | NOT NULL, FK → allowances(id), ON DELETE CASCADE | Loại phụ cấp |
| amount | DECIMAL(15,0) | DEFAULT 0 | Số tiền thực tế |
| created_at | DATETIME | DEFAULT CURRENT_TIMESTAMP | Ngày tạo |

> UNIQUE KEY `uk_emp_allow` (`employee_id`, `allowance_id`) — Mỗi NV chỉ nhận 1 lần mỗi loại.

### Bảng 8: rewards (Khen thưởng / Kỷ luật)

| Tên trường | Kiểu dữ liệu | Ràng buộc | Mô tả |
|-----------|--------------|-----------|-------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Mã bản ghi |
| employee_id | INT | NOT NULL, FK → employees(id), ON DELETE CASCADE | Nhân viên |
| type | ENUM('Khen thưởng','Kỷ luật') | NOT NULL | Loại |
| reason | TEXT | NOT NULL | Lý do |
| amount | DECIMAL(15,0) | DEFAULT 0 | Số tiền |
| date | DATE | DEFAULT NULL | Ngày quyết định |
| decision_number | VARCHAR(50) | DEFAULT NULL | Số quyết định |
| created_at | DATETIME | DEFAULT CURRENT_TIMESTAMP | Ngày tạo |

### Bảng 9: salary (Bảng lương)

| Tên trường | Kiểu dữ liệu | Ràng buộc | Mô tả |
|-----------|--------------|-----------|-------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Mã bảng lương |
| employee_id | INT | NOT NULL, FK → employees(id), ON DELETE CASCADE | Nhân viên |
| month | INT | NOT NULL | Tháng |
| year | INT | NOT NULL | Năm |
| working_days | INT | DEFAULT 0 | Số ngày công chuẩn |
| actual_working_days | INT | DEFAULT 0 | Ngày công thực tế |
| base_salary | DECIMAL(15,0) | DEFAULT 0 | Lương cơ bản |
| total_allowance | DECIMAL(15,0) | DEFAULT 0 | Tổng phụ cấp |
| overtime_hours | DECIMAL(5,1) | DEFAULT 0 | Giờ tăng ca |
| overtime_pay | DECIMAL(15,0) | DEFAULT 0 | Tiền tăng ca |
| total_reward | DECIMAL(15,0) | DEFAULT 0 | Tổng thưởng |
| total_discipline | DECIMAL(15,0) | DEFAULT 0 | Tổng phạt |
| gross_salary | DECIMAL(15,0) | DEFAULT 0 | Tổng lương gộp |
| bhxh | DECIMAL(15,0) | DEFAULT 0 | BHXH (8%) |
| bhyt | DECIMAL(15,0) | DEFAULT 0 | BHYT (1.5%) |
| bhtn | DECIMAL(15,0) | DEFAULT 0 | BHTN (1%) |
| tax | DECIMAL(15,0) | DEFAULT 0 | Thuế TNCN |
| advance_salary | DECIMAL(15,0) | DEFAULT 0 | Tạm ứng |
| other_deduction | DECIMAL(15,0) | DEFAULT 0 | Khấu trừ khác |
| net_salary | DECIMAL(15,0) | DEFAULT 0 | Lương thực nhận |
| status | VARCHAR(20) | DEFAULT 'Chờ duyệt' | Trạng thái |
| approved_by | INT | DEFAULT NULL | Người duyệt |
| approved_at | DATETIME | DEFAULT NULL | Ngày duyệt |
| paid_at | DATETIME | DEFAULT NULL | Ngày thanh toán |
| created_at | DATETIME | DEFAULT CURRENT_TIMESTAMP | Ngày tạo |

> UNIQUE KEY `uk_emp_month` (`employee_id`, `month`, `year`) — Mỗi NV chỉ có 1 bảng lương/tháng.

### Bảng 10: salary_advance (Tạm ứng lương)

| Tên trường | Kiểu dữ liệu | Ràng buộc | Mô tả |
|-----------|--------------|-----------|-------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Mã phiếu tạm ứng |
| employee_id | INT | NOT NULL, FK → employees(id), ON DELETE CASCADE | Nhân viên |
| amount | DECIMAL(15,0) | NOT NULL | Số tiền tạm ứng |
| reason | TEXT | DEFAULT NULL | Lý do |
| month | INT | NOT NULL | Tháng |
| year | INT | NOT NULL | Năm |
| status | VARCHAR(20) | DEFAULT 'Chờ duyệt' | Trạng thái |
| approved_by | INT | DEFAULT NULL | Người duyệt |
| approved_at | DATETIME | DEFAULT NULL | Ngày duyệt |
| created_at | DATETIME | DEFAULT CURRENT_TIMESTAMP | Ngày tạo |

### Bảng 11: leaves (Nghỉ phép)

| Tên trường | Kiểu dữ liệu | Ràng buộc | Mô tả |
|-----------|--------------|-----------|-------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Mã đơn nghỉ phép |
| employee_id | INT | NOT NULL, FK → employees(id), ON DELETE CASCADE | Nhân viên |
| leave_type | VARCHAR(50) | NOT NULL, DEFAULT 'Nghỉ phép năm' | Loại nghỉ phép |
| start_date | DATE | NOT NULL | Ngày bắt đầu |
| end_date | DATE | NOT NULL | Ngày kết thúc |
| days | INT | NOT NULL, DEFAULT 1 | Số ngày nghỉ |
| reason | TEXT | DEFAULT NULL | Lý do |
| status | VARCHAR(20) | DEFAULT 'Chờ duyệt' | Trạng thái |
| approved_by | INT | DEFAULT NULL | Người duyệt |
| approved_at | DATETIME | DEFAULT NULL | Ngày duyệt |
| created_at | DATETIME | DEFAULT CURRENT_TIMESTAMP | Ngày tạo |

### Bảng 12: contracts (Hợp đồng lao động)

| Tên trường | Kiểu dữ liệu | Ràng buộc | Mô tả |
|-----------|--------------|-----------|-------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Mã hợp đồng |
| employee_id | INT | NOT NULL, FK → employees(id), ON DELETE CASCADE | Nhân viên |
| contract_number | VARCHAR(50) | DEFAULT NULL | Số hợp đồng |
| contract_type | VARCHAR(50) | NOT NULL | Loại hợp đồng |
| start_date | DATE | NOT NULL | Ngày bắt đầu |
| end_date | DATE | DEFAULT NULL | Ngày kết thúc |
| base_salary | DECIMAL(15,0) | DEFAULT 0 | Mức lương theo HĐ |
| note | TEXT | DEFAULT NULL | Ghi chú |
| status | VARCHAR(20) | DEFAULT 'Hiệu lực' | Trạng thái |
| created_at | DATETIME | DEFAULT CURRENT_TIMESTAMP | Ngày tạo |
| updated_at | DATETIME | DEFAULT NULL | Ngày cập nhật |

### Bảng 13: activity_log (Nhật ký hoạt động)

| Tên trường | Kiểu dữ liệu | Ràng buộc | Mô tả |
|-----------|--------------|-----------|-------|
| id | INT | PRIMARY KEY, AUTO_INCREMENT | Mã bản ghi |
| user_id | INT | NOT NULL, FK → users(id), ON DELETE CASCADE | Tài khoản |
| action | VARCHAR(255) | NOT NULL | Hành động |
| module | VARCHAR(50) | NOT NULL | Module |
| detail | TEXT | DEFAULT NULL | Chi tiết |
| ip_address | VARCHAR(45) | DEFAULT NULL | Địa chỉ IP |
| created_at | DATETIME | DEFAULT CURRENT_TIMESTAMP | Thời gian |

---

## 3.2.2. SƠ ĐỒ QUAN HỆ (ER DIAGRAM)

```plantuml
@startuml
!define TABLE(x) entity x << (T,#FFAAAA) >>
skinparam linetype ortho

TABLE(users) {
    * id : INT <<PK>>
    --
    username : VARCHAR(50) <<UNIQUE>>
    password : VARCHAR(255)
    full_name : VARCHAR(100)
    email : VARCHAR(100)
    role : ENUM
    employee_id : INT
    status : TINYINT
    created_at : DATETIME
    updated_at : DATETIME
}

TABLE(departments) {
    * id : INT <<PK>>
    --
    name : VARCHAR(100)
    manager_name : VARCHAR(100)
    phone : VARCHAR(20)
    description : TEXT
    created_at : DATETIME
}

TABLE(positions) {
    * id : INT <<PK>>
    --
    name : VARCHAR(100)
    department_id : INT <<FK>>
    base_salary : DECIMAL(15,0)
    description : TEXT
    created_at : DATETIME
}

TABLE(employees) {
    * id : INT <<PK>>
    --
    employee_code : VARCHAR(20) <<UNIQUE>>
    full_name : VARCHAR(100)
    gender : VARCHAR(10)
    birth_date : DATE
    id_card : VARCHAR(20)
    phone : VARCHAR(20)
    email : VARCHAR(100)
    address : TEXT
    department_id : INT <<FK>>
    position_id : INT <<FK>>
    hire_date : DATE
    contract_type : VARCHAR(50)
    base_salary : DECIMAL(15,0)
    bank_account : VARCHAR(30)
    bank_name : VARCHAR(100)
    status : VARCHAR(20)
    created_at : DATETIME
    updated_at : DATETIME
}

TABLE(attendance) {
    * id : INT <<PK>>
    --
    employee_id : INT <<FK>>
    work_date : DATE
    status : VARCHAR(20)
    check_in : TIME
    check_out : TIME
    overtime_hours : DECIMAL(5,1)
    note : TEXT
    created_at : DATETIME
}

TABLE(allowances) {
    * id : INT <<PK>>
    --
    name : VARCHAR(100)
    default_amount : DECIMAL(15,0)
    description : TEXT
    created_at : DATETIME
}

TABLE(employee_allowances) {
    * id : INT <<PK>>
    --
    employee_id : INT <<FK>>
    allowance_id : INT <<FK>>
    amount : DECIMAL(15,0)
    created_at : DATETIME
}

TABLE(rewards) {
    * id : INT <<PK>>
    --
    employee_id : INT <<FK>>
    type : ENUM
    reason : TEXT
    amount : DECIMAL(15,0)
    date : DATE
    decision_number : VARCHAR(50)
    created_at : DATETIME
}

TABLE(salary) {
    * id : INT <<PK>>
    --
    employee_id : INT <<FK>>
    month : INT
    year : INT
    working_days : INT
    actual_working_days : INT
    base_salary : DECIMAL(15,0)
    total_allowance : DECIMAL(15,0)
    overtime_hours : DECIMAL(5,1)
    overtime_pay : DECIMAL(15,0)
    total_reward : DECIMAL(15,0)
    total_discipline : DECIMAL(15,0)
    gross_salary : DECIMAL(15,0)
    bhxh : DECIMAL(15,0)
    bhyt : DECIMAL(15,0)
    bhtn : DECIMAL(15,0)
    tax : DECIMAL(15,0)
    advance_salary : DECIMAL(15,0)
    other_deduction : DECIMAL(15,0)
    net_salary : DECIMAL(15,0)
    status : VARCHAR(20)
    approved_by : INT
    approved_at : DATETIME
    paid_at : DATETIME
    created_at : DATETIME
}

TABLE(salary_advance) {
    * id : INT <<PK>>
    --
    employee_id : INT <<FK>>
    amount : DECIMAL(15,0)
    reason : TEXT
    month : INT
    year : INT
    status : VARCHAR(20)
    approved_by : INT
    approved_at : DATETIME
    created_at : DATETIME
}

TABLE(leaves) {
    * id : INT <<PK>>
    --
    employee_id : INT <<FK>>
    leave_type : VARCHAR(50)
    start_date : DATE
    end_date : DATE
    days : INT
    reason : TEXT
    status : VARCHAR(20)
    approved_by : INT
    approved_at : DATETIME
    created_at : DATETIME
}

TABLE(contracts) {
    * id : INT <<PK>>
    --
    employee_id : INT <<FK>>
    contract_number : VARCHAR(50)
    contract_type : VARCHAR(50)
    start_date : DATE
    end_date : DATE
    base_salary : DECIMAL(15,0)
    note : TEXT
    status : VARCHAR(20)
    created_at : DATETIME
    updated_at : DATETIME
}

TABLE(activity_log) {
    * id : INT <<PK>>
    --
    user_id : INT <<FK>>
    action : VARCHAR(255)
    module : VARCHAR(50)
    detail : TEXT
    ip_address : VARCHAR(45)
    created_at : DATETIME
}

' ==================== QUAN HỆ ====================
departments ||--o{ positions : "1 - N"
departments ||--o{ employees : "1 - N"
positions ||--o{ employees : "1 - N"

employees ||--o{ attendance : "1 - N"
employees ||--o{ leaves : "1 - N"
employees ||--o{ contracts : "1 - N"
employees ||--o{ rewards : "1 - N"
employees ||--o{ salary : "1 - N"
employees ||--o{ salary_advance : "1 - N"
employees ||--o{ employee_allowances : "1 - N"

allowances ||--o{ employee_allowances : "1 - N"

users ||--o{ activity_log : "1 - N"
@enduml
```

### Mô tả quan hệ giữa các bảng

| STT | Quan hệ | Mô tả | Loại |
|-----|---------|-------|------|
| 1 | departments → employees | Một phòng ban có nhiều nhân viên | 1 - N |
| 2 | departments → positions | Một phòng ban có nhiều chức vụ | 1 - N |
| 3 | positions → employees | Một chức vụ có nhiều nhân viên | 1 - N |
| 4 | employees → attendance | Một NV có nhiều bản ghi chấm công | 1 - N |
| 5 | employees → leaves | Một NV có nhiều đơn nghỉ phép | 1 - N |
| 6 | employees → contracts | Một NV có nhiều hợp đồng | 1 - N |
| 7 | employees → rewards | Một NV có nhiều khen thưởng/kỷ luật | 1 - N |
| 8 | employees → salary | Một NV có nhiều bảng lương (theo tháng) | 1 - N |
| 9 | employees → salary_advance | Một NV có nhiều phiếu tạm ứng | 1 - N |
| 10 | employees ↔ allowances | Quan hệ N-N thông qua employee_allowances | N - N |
| 11 | users → activity_log | Một tài khoản có nhiều nhật ký | 1 - N |

### Công thức tính lương

```
Lương theo ngày công = Lương cơ bản × (Ngày công thực tế / Ngày công chuẩn)
Tiền tăng ca         = Giờ tăng ca × (Lương cơ bản / (Ngày công chuẩn × 8)) × 1.5
Tổng thu nhập (Gross) = Lương theo ngày + Phụ cấp + Tăng ca + Thưởng - Phạt
BHXH                  = Lương cơ bản × 8%
BHYT                  = Lương cơ bản × 1.5%
BHTN                  = Lương cơ bản × 1%
Thu nhập chịu thuế    = Gross - BHXH - BHYT - BHTN
Thuế TNCN             = Tính theo biểu lũy tiến từng phần (giảm trừ 11 triệu)
Lương thực nhận (Net) = Gross - BHXH - BHYT - BHTN - Thuế - Tạm ứng
```

### Biểu thuế TNCN lũy tiến từng phần

| Bậc | Thu nhập chịu thuế / tháng | Thuế suất |
|-----|---------------------------|-----------|
| 1 | Đến 5 triệu | 5% |
| 2 | Trên 5 - 10 triệu | 10% |
| 3 | Trên 10 - 18 triệu | 15% |
| 4 | Trên 18 - 32 triệu | 20% |
| 5 | Trên 32 - 52 triệu | 25% |
| 6 | Trên 52 - 80 triệu | 30% |
| 7 | Trên 80 triệu | 35% |

> Giảm trừ gia cảnh bản thân: 11.000.000 đ/tháng
> Giảm trừ người phụ thuộc: 4.400.000 đ/người/tháng

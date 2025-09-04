<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm my-1">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">
            <img src="images/logo.png" alt="Logo" width="70" class="d-inline-block align-text-center">
            smart sensor nimt
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarTogglerDemo02"
            aria-controls="navbarTogglerDemo02" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarTogglerDemo02">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <!-- หน้าแรก -->
                <li class="nav-item">
                    <a class="nav-link <?php if ($page == 'index') echo 'active'; ?>" href="index.php">
                        <i class="bi bi-house"></i> หน้าแรก
                    </a>
                </li>

                <!-- ประวัติบริษัท -->
                <li class="nav-item">
                    <a class="nav-link <?php if ($page == 'history') echo 'active'; ?>" href="history.php">
                        <i class="bi bi-bank"></i> ประวัติบริษัท
                    </a>
                </li>

                <!-- รายการสินค้า -->
                <li class="nav-item">
                    <a class="nav-link <?php if ($page == 'product_list_for_customer') echo 'active'; ?>" href="product_list_for_customer.php">
                        <i class="bi bi-list-ul"></i> รายการสินค้า
                    </a>
                </li>

                <!-- ผู้ดูแลระบบ -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle <?php if ($page == 'admin') echo 'active'; ?>" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-card-checklist"></i> ผู้ดูแลระบบ
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="login.php">เข้าสู่ระบบ</a></li>
                        <li><a class="dropdown-item" href="product_list_for_admin.php">รายการสินค้า</a></li>
                        <li><a class="dropdown-item" href="product_add_form.php">เพิ่มสินค้า</a></li>
                        <li><a class="dropdown-item" href="logout.php">ออกจากระบบ</a></li>
                    </ul>
                </li>
            </ul>

            <!-- ปุ่มเข้าสู่ระบบ (ทั่วไป) -->
            <div class="d-flex">
                <a href="login.php" class="btn btn-outline-success">
                    <i class="bi bi-box-arrow-in-right"></i> เข้าสู่ระบบ
                </a>
            </div>
        </div>
    </div>
</nav>
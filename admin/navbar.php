<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <div class="d-flex align-items-center gap-2">
            <button type="button"
                    class="btn btn-admin-menu d-lg-none"
                    id="adminMenuToggle"
                    aria-label="Buka menu navigasi"
                    aria-expanded="false"
                    aria-controls="adminSidebar">
                <i class="bi bi-list"></i>
            </button>
            <a class="navbar-brand" href="<?= htmlspecialchars(function_exists('currentUserHomeUrl') ? currentUserHomeUrl() : (function_exists('adminUrl') ? adminUrl('index.php') : 'index.php')) ?>">
                <span class="brand-mark"><i class="bi bi-reception-4"></i></span>
                <span class="brand-text">E-Recepsionis</span>
            </a>
        </div>
        <div class="navbar-nav ms-auto flex-row align-items-center gap-2">
            <span class="navbar-user-pill">
                <i class="bi bi-person-circle text-secondary"></i>
                <span class="user-name"><?= htmlspecialchars($_SESSION['nama_lengkap'] ?? $_SESSION['username'] ?? 'Administrator') ?></span>
                <span class="role-tag"><?= htmlspecialchars(function_exists('currentUserRoleLabel') ? currentUserRoleLabel() : ucfirst((string) ($_SESSION['role'] ?? 'user'))) ?></span>
            </span>
            <a class="btn btn-logout btn-sm" href="<?= htmlspecialchars(function_exists('adminUrl') ? adminUrl('logout.php') : 'logout.php') ?>" title="Logout">
                <i class="bi bi-box-arrow-right"></i>
                <span class="logout-label">Logout</span>
            </a>
        </div>
    </div>
</nav>
<div class="admin-sidebar-backdrop" id="adminSidebarBackdrop" aria-hidden="true"></div>

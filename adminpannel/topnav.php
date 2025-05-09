<div class="top-nav">
    <div class="top-nav-left">
        <div class="search-container">
            
        </div>
    </div>
    
    <div class="top-nav-right">
               
        <div class="nav-item">
            <div class="admin-profile">
                <button class="profile-btn">
                    <span><?= htmlspecialchars($admin_name) ?></span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="dropdown-menu profile-menu">
                    <a href="profile.php" class="dropdown-item">
                        <i class="fas fa-user"></i> Profile
                    </a>
                    <a href="settings.php" class="dropdown-item">
                        <i class="fas fa-cog"></i> Settings
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="/comtech/adminpannel/logout.php" class="dropdown-item">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

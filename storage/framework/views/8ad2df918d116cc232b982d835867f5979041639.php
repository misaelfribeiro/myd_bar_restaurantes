<style>
 * {
 margin: 0;
 padding: 0;
 box-sizing: border-box;
 }
 body {
 font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
 background: #f5f7fa;
 overflow-x: hidden;
 }
 .top-bar {
 position: fixed;
 top: 0;
 left: 0;
 right: 0;
 height: 70px;
 background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
 color: white;
 display: flex;
 align-items: center;
 justify-content: space-between;
 padding: 0 20px;
 box-shadow: 0 2px 10px rgba(0,0,0,0.1);
 z-index: 1000;
 }
 .logo-section {
 display: flex;
 align-items: center;
 gap: 15px;
 }
 .logo-section h1 {
 font-size: 1.5rem;
 font-weight: 700;
 }
 .system-info {
 display: flex;
 align-items: center;
 gap: 20px;
 }
 .user-info {
 display: flex;
 align-items: center;
 gap: 10px;
 background: rgba(255,255,255,0.1);
 padding: 8px 15px;
 border-radius: 25px;
 cursor: pointer;
 transition: background 0.3s ease;
 }
 .user-info:hover {
 background: rgba(255,255,255,0.15);
 }
 .user-avatar {
 width: 35px;
 height: 35px;
 border-radius: 50%;
 background: rgba(255,255,255,0.2);
 display: flex;
 align-items: center;
 justify-content: center;
 }
 .quick-actions {
 display: flex;
 gap: 10px;
 }
 .quick-btn {
 width: 40px;
 height: 40px;
 border-radius: 50%;
 background: rgba(255,255,255,0.1);
 border: none;
 color: white;
 cursor: pointer;
 transition: all 0.3s ease;
 display: flex;
 align-items: center;
 justify-content: center;
 }
 .quick-btn:hover {
 background: rgba(255,255,255,0.2);
 transform: scale(1.1);
 }
 .sidebar {
 position: fixed;
 top: 70px;
 left: 0;
 width: 280px;
 height: calc(100vh - 70px);
 background: white;
 box-shadow: 2px 0 10px rgba(0,0,0,0.1);
 transition: all 0.3s ease;
 z-index: 999;
 overflow-y: scroll;
 overflow-x: hidden;
 }
 .sidebar::-webkit-scrollbar {
 width: 8px;
 }
 .sidebar::-webkit-scrollbar-track {
 background: #f1f1f1;
 }
 .sidebar::-webkit-scrollbar-thumb {
 background: #888;
 border-radius: 4px;
 }
 .sidebar::-webkit-scrollbar-thumb:hover {
 background: #555;
 }
 .sidebar.collapsed {
 width: 70px;
 }
 .sidebar-toggle {
 position: absolute;
 right: -15px;
 top: 20px;
 width: 30px;
 height: 30px;
 background: #667eea;
 color: white;
 border: none;
 border-radius: 50%;
 cursor: pointer;
 display: flex;
 align-items: center;
 justify-content: center;
 transition: all 0.3s ease;
 box-shadow: 0 2px 8px rgba(0,0,0,0.15);
 }
 .sidebar-toggle:hover {
 background: #5a6fd8;
 transform: scale(1.1);
 }
 .sidebar-header {
 padding: 20px;
 border-bottom: 1px solid #e9ecef;
 }
 .sidebar-title {
 font-size: 1.1rem;
 font-weight: 600;
 color: #333;
 transition: opacity 0.3s ease;
 }
 .sidebar.collapsed .sidebar-title {
 opacity: 0;
 }
 .menu {
 padding: 20px 0;
 }
 .menu-section {
 margin-bottom: 30px;
 }
 .menu-section-title {
 padding: 0 20px 10px;
 font-size: 0.85rem;
 font-weight: 600;
 color: #8e9297;
 text-transform: uppercase;
 letter-spacing: 1px;
 transition: opacity 0.3s ease;
 }
 .sidebar.collapsed .menu-section-title {
 opacity: 0;
 }
 .menu-item {
 display: flex;
 align-items: center;
 padding: 12px 20px;
 color: #555;
 text-decoration: none;
 transition: all 0.3s ease;
 position: relative;
 }
 .menu-item:hover {
 background: #f8f9fa;
 color: #667eea;
 text-decoration: none;
 }
 .menu-item.active {
 background: linear-gradient(90deg, rgba(102, 126, 234, 0.1), transparent);
 color: #667eea;
 border-right: 3px solid #667eea;
 }
 .menu-item i {
 width: 20px;
 text-align: center;
 margin-right: 15px;
 font-size: 1.1rem;
 }
 .menu-text {
 transition: opacity 0.3s ease;
 flex: 1;
 }
 .sidebar.collapsed .menu-text {
 opacity: 0;
 }
 .menu-badge {
 margin-left: auto;
 background: #dc3545;
 color: white;
 font-size: 0.75rem;
 padding: 2px 8px;
 border-radius: 10px;
 transition: opacity 0.3s ease;
 min-width: 18px;
 text-align: center;
 }
 .sidebar.collapsed .menu-badge {
 opacity: 0;
 }
 .main-content {
 margin-left: 280px;
 margin-top: 70px;
 padding: 30px;
 transition: margin-left 0.3s ease;
 min-height: calc(100vh - 70px);
 }
 .main-content.expanded {
 margin-left: 70px;
 }
 .page-header {
 margin-bottom: 30px;
 padding-bottom: 20px;
 border-bottom: 2px solid #e9ecef;
 }
 .page-title {
 font-size: 2rem;
 color: #333;
 margin-bottom: 10px;
 display: flex;
 align-items: center;
 gap: 15px;
 }
 .page-subtitle {
 color: #666;
 font-size: 1.1rem;
 }
 .stats-grid {
 display: grid;
 grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
 gap: 20px;
 margin-bottom: 30px;
 }
 .stat-card {
 background: white;
 padding: 25px;
 border-radius: 15px;
 box-shadow: 0 4px 20px rgba(0,0,0,0.08);
 transition: all 0.3s ease;
 border: 1px solid #e9ecef;
 position: relative;
 overflow: hidden;
 }
 .stat-card::before {
 content: '';
 position: absolute;
 top: 0;
 left: 0;
 right: 0;
 height: 3px;
 background: linear-gradient(135deg, #667eea, #764ba2);
 }
 .stat-card:hover {
 transform: translateY(-5px);
 box-shadow: 0 8px 30px rgba(0,0,0,0.12);
 }
 .stat-card-header {
 display: flex;
 justify-content: space-between;
 align-items: flex-start;
 margin-bottom: 15px;
 }
 .stat-icon {
 width: 50px;
 height: 50px;
 border-radius: 12px;
 display: flex;
 align-items: center;
 justify-content: center;
 font-size: 1.2rem;
 color: white;
 }
 .stat-icon.primary { background: linear-gradient(135deg, #667eea, #764ba2); }
 .stat-icon.success { background: linear-gradient(135deg, #28a745, #20c997); }
 .stat-icon.warning { background: linear-gradient(135deg, #ffc107, #fd7e14); }
 .stat-icon.info { background: linear-gradient(135deg, #17a2b8, #6f42c1); }
 .stat-icon.danger { background: linear-gradient(135deg, #dc3545, #c82333); }
 .stat-number {
 font-size: 2.2rem;
 font-weight: 700;
 color: #333;
 margin-bottom: 5px;
 }
 .stat-label {
 color: #666;
 font-size: 0.9rem;
 font-weight: 500;
 }
 .stat-change {
 font-size: 0.8rem;
 margin-top: 10px;
 display: flex;
 align-items: center;
 gap: 5px;
 }
 .stat-change.positive { color: #28a745; }
 .stat-change.negative { color: #dc3545; }
 .section-card {
 background: white;
 border-radius: 15px;
 box-shadow: 0 4px 20px rgba(0,0,0,0.08);
 overflow: hidden;
 margin-bottom: 30px;
 }
 .section-header {
 padding: 20px 25px;
 background: linear-gradient(135deg, #f8f9fa, #e9ecef);
 border-bottom: 1px solid #e9ecef;
 display: flex;
 align-items: center;
 justify-content: space-between;
 }
 .section-title {
 font-size: 1.2rem;
 font-weight: 600;
 color: #333;
 display: flex;
 align-items: center;
 gap: 10px;
 }
 .section-content {
 padding: 25px;
 }
 .alert {
 padding: 15px 20px;
 border-radius: 10px;
 margin-bottom: 20px;
 display: flex;
 align-items: center;
 gap: 10px;
 }
 .alert-success {
 background: #d4edda;
 color: #155724;
 border: 1px solid #c3e6cb;
 }
 .alert-error, .alert-danger {
 background: #f8d7da;
 color: #721c24;
 border: 1px solid #f5c6cb;
 }
 .alert-warning {
 background: #fff3cd;
 color: #856404;
 border: 1px solid #ffeaa7;
 }
 .alert-info {
 background: #d1ecf1;
 color: #0c5460;
 border: 1px solid #bee5eb;
 }
 .loading {
 opacity: 0.6;
 pointer-events: none;
 }
 .spinner {
 animation: spin 1s linear infinite;
 }
 @keyframes  spin {
 from { transform: rotate(0deg); }
 to { transform: rotate(360deg); }
 }
 @media (max-width: 1200px) {
 .stats-grid {
 grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
 }
 }
 @media (max-width: 768px) {
 .sidebar {
 transform: translateX(-100%);
 }
 .sidebar.mobile-open {
 transform: translateX(0);
 }
 .main-content {
 margin-left: 0;
 padding: 20px 15px;
 }
 .quick-actions {
 display: none;
 }
 .page-title {
 font-size: 1.5rem;
 }
 .stat-card {
 padding: 20px;
 }
 .stats-grid {
 grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
 gap: 15px;
 }
 }
 @keyframes  fadeIn {
 from { opacity: 0; transform: translateY(20px); }
 to { opacity: 1; transform: translateY(0); }
 }
 .fade-in {
 animation: fadeIn 0.6s ease forwards;
 }
 .dark-mode {
 background: #1a1a1a;
 color: #e9ecef;
 }
 .dark-mode .sidebar,
 .dark-mode .stat-card,
 .dark-mode .section-card {
 background: #2d2d2d;
 border-color: #404040;
 color: #e9ecef;
 }
 .dark-mode .top-bar {
 background: linear-gradient(135deg, #4a5568, #2d3748);
 }
 .dark-mode .section-header {
 background: linear-gradient(135deg, #2d3748, #4a5568);
 border-bottom-color: #404040;
 }
 .dark-mode .menu-item {
 color: #cbd5e0;
 }
 .dark-mode .menu-item:hover {
 background: #4a5568;
 color: #90cdf4;
 }
 .sidebar-overlay {
 position: fixed;
 top: 70px;
 left: 0;
 width: 100%;
 height: calc(100vh - 70px);
 background: rgba(0,0,0,0.5);
 display: none;
 z-index: 998;
 }
 @media (max-width: 768px) {
 .sidebar-overlay.active {
 display: block;
 }
 }
 .user-dropdown {
 position: relative;
 }
 .user-dropdown-content {
 position: absolute;
 top: 100%;
 right: 0;
 background: white;
 border-radius: 10px;
 box-shadow: 0 8px 30px rgba(0,0,0,0.15);
 padding: 10px 0;
 margin-top: 10px;
 min-width: 200px;
 display: none;
 z-index: 1001;
 }
 .user-dropdown.active .user-dropdown-content {
 display: block;
 animation: fadeIn 0.3s ease;
 }
 .user-dropdown-item {
 display: flex;
 align-items: center;
 gap: 10px;
 padding: 10px 20px;
 color: #333;
 text-decoration: none;
 transition: background 0.2s ease;
 }
 .user-dropdown-item:hover {
 background: #f8f9fa;
 color: #667eea;
 text-decoration: none;
 }
 .user-dropdown-divider {
 height: 1px;
 background: #e9ecef;
 margin: 8px 0;
 }
 .page-header {
 margin-bottom: 30px;
 padding-bottom: 20px;
 border-bottom: 2px solid #e9ecef;
 }
 .page-title {
 font-size: 2rem;
 font-weight: 700;
 color: #2d3748;
 margin-bottom: 5px;
 }
 .page-subtitle {
 color: #718096;
 font-size: 0.95rem;
 }
 .stats-card {
 background: rgba(255, 255, 255, 0.95);
 border-radius: 15px;
 padding: 20px;
 box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
 display: flex;
 align-items: center;
 gap: 15px;
 transition: all 0.3s ease;
 border: none;
 backdrop-filter: blur(10px);
 }
 .stats-card:hover {
 transform: translateY(-3px);
 box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
 }
 .stats-icon {
 width: 60px;
 height: 60px;
 border-radius: 12px;
 display: flex;
 align-items: center;
 justify-content: center;
 font-size: 1.5rem;
 color: white;
 flex-shrink: 0;
 }
 .stats-icon.bg-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
 .stats-icon.bg-success { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); }
 .stats-icon.bg-warning { background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%); }
 .stats-icon.bg-danger { background: linear-gradient(135deg, #dc3545 0%, #c82333 100%); }
 .stats-icon.bg-info { background: linear-gradient(135deg, #17a2b8 0%, #138496 100%); }
 .stats-content h3 {
 font-size: 1.8rem;
 font-weight: 700;
 color: #2d3748;
 margin-bottom: 5px;
 }
 .stats-content p {
 color: #718096;
 font-size: 0.9rem;
 margin-bottom: 0;
 }
 .filters-card {
 background: rgba(255, 255, 255, 0.95);
 border-radius: 15px;
 padding: 20px;
 box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
 backdrop-filter: blur(10px);
 }
 .search-box {
 position: relative;
 }
 .search-box i {
 position: absolute;
 left: 15px;
 top: 50%;
 transform: translateY(-50%);
 color: #718096;
 }
 .search-box input {
 padding-left: 45px;
 border: 1px solid #e2e8f0;
 border-radius: 10px;
 height: 45px;
 }
 .search-box input:focus {
 border-color: #667eea;
 box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
 }
 .form-select, .form-control {
 border: 1px solid #e2e8f0;
 border-radius: 10px;
 height: 45px;
 padding: 10px 15px;
 }
 .form-select:focus, .form-control:focus {
 border-color: #667eea;
 box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
 }
 .btn {
 border-radius: 10px;
 padding: 10px 20px;
 font-weight: 600;
 transition: all 0.3s ease;
 border: none;
 }
 .btn-primary {
 background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
 color: white;
 }
 .btn-primary:hover {
 transform: translateY(-2px);
 box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
 }
 .btn-outline-primary {
 border: 2px solid #667eea;
 color: #667eea;
 background: transparent;
 }
 .btn-outline-primary:hover {
 background: #667eea;
 color: white;
 }
 .card {
 border: none;
 border-radius: 15px;
 box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
 background: rgba(255, 255, 255, 0.95);
 backdrop-filter: blur(10px);
 margin-bottom: 20px;
 }
 .card-header {
 background: rgba(248, 249, 250, 0.8);
 border-bottom: 1px solid #e9ecef;
 padding: 15px 20px;
 border-radius: 15px 15px 0 0 !important;
 }
 .card-body {
 padding: 20px;
 }
 .card-footer {
 background: rgba(248, 249, 250, 0.5);
 border-top: 1px solid #e9ecef;
 padding: 15px 20px;
 border-radius: 0 0 15px 15px !important;
 }
 .empty-state {
 text-align: center;
 padding: 60px 20px;
 background: rgba(255, 255, 255, 0.95);
 border-radius: 15px;
 margin: 40px 0;
 }
 .empty-state i {
 font-size: 4rem;
 color: #cbd5e0;
 margin-bottom: 20px;
 }
 .empty-state h3 {
 color: #2d3748;
 margin-bottom: 10px;
 }
 .empty-state p {
 color: #718096;
 margin-bottom: 20px;
 }
 .badge {
 padding: 6px 12px;
 border-radius: 8px;
 font-weight: 600;
 font-size: 0.85rem;
 }
 .table {
 border-collapse: separate;
 border-spacing: 0;
 }
 .table thead th {
 background: rgba(248, 249, 250, 0.95);
 color: #2d3748;
 font-weight: 600;
 border-bottom: 2px solid #e2e8f0;
 padding: 15px;
 }
 .table tbody tr {
 transition: all 0.2s ease;
 }
 .table tbody tr:hover {
 background: rgba(102, 126, 234, 0.05);
 }
 .table tbody td {
 padding: 15px;
 border-bottom: 1px solid #e2e8f0;
 vertical-align: middle;
 }
 .dropdown-menu {
 border: none;
 border-radius: 12px;
 box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
 padding: 8px;
 }
 .dropdown-item {
 border-radius: 8px;
 padding: 10px 15px;
 transition: all 0.2s ease;
 }
 .dropdown-item:hover {
 background: rgba(102, 126, 234, 0.1);
 color: #667eea;
 }
 .dropdown-divider {
 margin: 8px 0;
 border-top: 1px solid #e9ecef;
 }
 @media (max-width: 768px) {
 .page-title {
 font-size: 1.5rem;
 }
 .stats-card {
 margin-bottom: 15px;
 }
 .filters-card {
 margin-bottom: 20px;
 }
 .card {
 margin-bottom: 15px;
 }
 }
</style>
 
 <?php /**PATH C:\xampp\htdocs\myd_bar_restaurantes\resources\views/layouts/partials/styles.blade.php ENDPATH**/ ?>
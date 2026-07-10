
<!-- Sidebar -->
<nav id="sidebar" class="sidebar">
    <div class="sidebar-header text-center">
        @if(Auth::guard('super_admin')->check())
            <img src="{{ asset('assets/images/logo.png') }}" class="logo-img" alt="Logo">
        @else
            @if(Auth::user()->organisation && Auth::user()->organisation->logo)
                <img src="{{ asset('storage/' . Auth::user()->organisation->logo) }}" class="logo-img" alt="Logo">
            @else
                <img src="{{ asset('assets/images/logo.png') }}" class="logo-img" alt="Logo">
            @endif
            <div class="mt-2">
                <small class="text-white ">{{ Auth::user()->organisation->name }}</small>
            </div>
        @endif
    </div>
    
    <ul class="list-unstyled components">
        <li class="{{ request()->is('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" title="Dashboard"><i class="fas fa-home me-2"></i> <span class="menu-text">Dashboard</span></a>
        </li>
        @if(Auth::guard('super_admin')->check() && !Auth::guard('web')->check())
            <!-- Super Admin Menu -->
            <li class="{{ request()->is('organisations*') ? 'active' : '' }}">
                <a href="{{ route('admin.organisations.index') }}" title="Organisations"><i class="fas fa-building me-2"></i> <span class="menu-text">Organisations</span></a>
            </li>
            <li class="{{ request()->is('plans*') ? 'active' : '' }}">
                <a href="#" title="Subscription Plans"><i class="fas fa-layer-group me-2"></i> <span class="menu-text">Subscription Plans</span></a>
            </li>
            <li class="{{ request()->is('users*') ? 'active' : '' }}">
                <a href="#" title="System Users"><i class="fas fa-users-cog me-2"></i> <span class="menu-text">System Users</span></a>
            </li>
            <li class="{{ request()->is('settings*') ? 'active' : '' }}">
                <a href="{{ route('admin.settings.index') }}" title="Settings"><i class="fas fa-cog me-2"></i> <span class="menu-text">Settings</span></a>
            </li>
        @else
            <!-- Organisation/Employee Menu -->
            <li class="{{ request()->is('leads*') ? 'active' : '' }}">
                <a href="{{ route('leads.index') }}" title="Leads"><i class="fas fa-user-tie me-2"></i> <span class="menu-text">Leads</span></a>
            </li>
            <li class="{{ request()->is('quotations*') ? 'active' : '' }}">
                <a href="{{ route('quotations.index') }}" title="Quotations"><i class="fas fa-file-invoice-dollar me-2"></i> <span class="menu-text">Quotations</span></a>
            </li>
            <li class="{{ request()->is('projects*') ? 'active' : '' }}">
                <a href="{{ route('projects.index') }}" title="Projects"><i class="fas fa-project-diagram me-2"></i> <span class="menu-text">Projects</span></a>
            </li>
            <li class="{{ request()->is('customers*') ? 'active' : '' }}">
                <a href="{{ route('customers.index') }}" title="Customers"><i class="fas fa-users me-2"></i> <span class="menu-text">Customers</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center"
                data-bs-toggle="collapse"
                data-bs-target="#reportsMenu"
                role="button"
                aria-expanded="{{ request()->is('reports*') ? 'true' : 'false' }}">
                    <i class="fas fa-file-invoice me-2"></i>
                    <span class="menu-text ps-1">
                        Reports
                    </span>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>

                <ul class="collapse list-unstyled {{ request()->is('reports*') ? 'show' : '' }}"
                    id="reportsMenu">
                    <li>
                        <a href="{{ url('reports/revenue') }}"
                        class="nav-link ps-5 {{ request()->is('reports/revenue') ? 'active' : '' }}" title="Revenue">
                            <span class="menu-text">Revenue</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('reports/expense') }}"
                        class="nav-link ps-5 {{ request()->is('reports/expense') ? 'active' : '' }}" title="Expense Report">
                            <span class="menu-text">Expense Report</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('reports/financial-statement') }}"
                        class="nav-link ps-5 {{ request()->is('reports/financial-statement') ? 'active' : '' }}" title="Financial Statement">
                            <span class="menu-text">Financial Statement</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="{{ request()->is('expenses*') ? 'active' : '' }}">
                <a href="{{ route('expenses.index') }}" title="Expenses"><i class="fas fa-wallet me-2"></i> <span class="menu-text">Expenses</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center"
                    data-bs-toggle="collapse"
                    data-bs-target="#hrmMenu"
                    role="button"
                    aria-expanded="{{ request()->is('employees*') || request()->is('payrolls*') ? 'true' : 'false' }}">
                    <i class="fas fa-id-card me-2"></i>
                    <span class="menu-text ps-1">
                        HRM
                    </span>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>

                <ul class="collapse list-unstyled {{ request()->is('employees*') || request()->is('payrolls*') ? 'show' : '' }}"
                    id="hrmMenu">
                    <li>
                        <a href="{{ route('employees.index') }}"
                            class="nav-link ps-5 {{ request()->is('employees*') ? 'active' : '' }}" title="Employees">
                            <span class="menu-text">Employees</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('attendance.index') }}"
                            class="nav-link ps-5 {{ request()->is('attendance*') ? 'active' : '' }}" title="Attendance">
                            <span class="menu-text">Attendance</span>
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('payrolls.index') }}"
                            class="nav-link ps-5 {{ request()->is('payrolls*') ? 'active' : '' }}" title="Payroll">
                            <span class="menu-text">Payroll</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="{{ request()->is('settings*') ? 'active' : '' }}">
                <a href="{{ route('settings.index') }}" title="Settings"><i class="fas fa-cog me-2"></i> <span class="menu-text">Settings</span></a>
            </li>
        @endif

    </ul>

    @if(Auth::guard('super_admin')->check() && !Auth::guard('web')->check())
    <div class="sidebar-footer">
        <a href="javascript:void(0)" onclick="document.getElementById('logout-form').submit()" title="Logout">
            <i class="fas fa-sign-out-alt me-2"></i> <span class="menu-text">Logout</span>
        </a>
        <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">@csrf</form>
    </div>

    @else
    <div class="sidebar-footer">
        <a href="javascript:void(0)" onclick="document.getElementById('logout-form').submit()" title="Logout">
            <i class="fas fa-sign-out-alt me-2"></i> <span class="menu-text">Logout</span>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
    </div>
    @endif
</nav>
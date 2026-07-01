
<!-- Sidebar -->
<nav id="sidebar" class="sidebar">
    <div class="sidebar-header text-center">
        @if(Auth::user()->organisation && Auth::user()->organisation->logo)
            <img src="{{ asset('storage/' . Auth::user()->organisation->logo) }}" class="logo-img" alt="Logo">
        @else
            <h4 class="m-0">{{ config('app.name') }}</h4>
        @endif
        <div class="mt-2">
            <small class="text-white ">{{ Auth::user()->role === 'super_admin' ? 'System Admin' : (Auth::user()->organisation->name ?? '') }}</small>
        </div>
    </div>
    
    <ul class="list-unstyled components">
        <li class="{{ request()->is('dashboard') ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}"><i class="fas fa-home me-2"></i> Dashboard</a>
        </li>
        
        @if(Auth::check() && Auth::user()->role === 'super_admin')
            <!-- Super Admin Menu -->
            <li class="{{ request()->is('organisations*') ? 'active' : '' }}">
                <a href="{{ route('organisations.index') }}"><i class="fas fa-building me-2"></i> Organisations</a>
            </li>
            <li class="{{ request()->is('plans*') ? 'active' : '' }}">
                <a href="#"><i class="fas fa-layer-group me-2"></i> Subscription Plans</a>
            </li>
            <li class="{{ request()->is('users*') ? 'active' : '' }}">
                <a href="{{ route('users.index') }}"><i class="fas fa-users-cog me-2"></i> System Users</a>
            </li>
        @else
            <!-- Organisation/Employee Menu -->
            <li class="{{ request()->is('leads*') ? 'active' : '' }}">
                <a href="{{ route('leads.index') }}"><i class="fas fa-user-tie me-2"></i> Leads</a>
            </li>
            <li class="{{ request()->is('projects*') ? 'active' : '' }}">
                <a href="{{ route('projects.index') }}"><i class="fas fa-project-diagram me-2"></i> Projects</a>
            </li>
            <li class="{{ request()->is('quotations*') ? 'active' : '' }}">
                <a href="{{ route('quotations.index') }}"><i class="fas fa-file-invoice-dollar me-2"></i> Quotations</a>
            </li>
            <li class="{{ request()->is('customers*') ? 'active' : '' }}">
                <a href="{{ route('customers.index') }}"><i class="fas fa-users me-2"></i> Customers</a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center"
                data-bs-toggle="collapse"
                data-bs-target="#reportsMenu"
                role="button"
                aria-expanded="{{ request()->is('reports*') ? 'true' : 'false' }}">
                    <span>
                        <i class="fas fa-file-invoice me-2"></i>
                        Reports
                    </span>
                    <i class="fas fa-chevron-down"></i>
                </a>

                <ul class="collapse list-unstyled {{ request()->is('reports*') ? 'show' : '' }}"
                    id="reportsMenu">
                    <li>
                        <a href="{{ url('reports/revenue') }}"
                        class="nav-link ps-5 {{ request()->is('reports/revenue') ? 'active' : '' }}">
                            Revenue
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('reports/expense') }}"
                        class="nav-link ps-5 {{ request()->is('reports/expense') ? 'active' : '' }}">
                            Expense Report
                        </a>
                    </li>
                    <li>
                        <a href="{{ url('reports/financial-statement') }}"
                        class="nav-link ps-5 {{ request()->is('reports/financial-statement') ? 'active' : '' }}">
                            Financial Statement
                        </a>
                    </li>
                </ul>
            </li>
            <li class="{{ request()->is('expenses*') ? 'active' : '' }}">
                <a href="{{ route('expenses.index') }}"><i class="fas fa-wallet me-2"></i> Expenses</a>
            </li>
            <li class="nav-item">
                <a class="nav-link d-flex justify-content-between align-items-center"
                    data-bs-toggle="collapse"
                    data-bs-target="#hrmMenu"
                    role="button"
                    aria-expanded="{{ request()->is('employees*') || request()->is('payrolls*') ? 'true' : 'false' }}">
                    <span>
                        <i class="fas fa-id-card me-2"></i>
                        HRM
                    </span>
                    <i class="fas fa-chevron-down"></i>
                </a>

                <ul class="collapse list-unstyled {{ request()->is('employees*') || request()->is('payrolls*') ? 'show' : '' }}"
                    id="hrmMenu">
                    <li>
                        <a href="{{ route('employees.index') }}"
                            class="nav-link ps-5 {{ request()->is('employees*') ? 'active' : '' }}">
                            Employees
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('payrolls.index') }}"
                            class="nav-link ps-5 {{ request()->is('payrolls*') ? 'active' : '' }}">
                            Payroll
                        </a>
                    </li>
                </ul>
            </li>
        @endif

        <li class="{{ request()->is('settings*') ? 'active' : '' }}">
            <a href="{{ route('settings.index') }}"><i class="fas fa-cog me-2"></i> Settings</a>
        </li>
    </ul>

    <div class="sidebar-footer">
        <a href="javascript:void(0)" onclick="document.getElementById('logout-form').submit()">
            <i class="fas fa-sign-out-alt me-2"></i> Logout
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">@csrf</form>
    </div>
</nav>
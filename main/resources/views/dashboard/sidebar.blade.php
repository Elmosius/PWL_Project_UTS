<div class="sidebar border border-right col-md-3 col-lg-2 p-0 bg-body-tertiary">
    <div class="offcanvas-md offcanvas-end bg-body-tertiary" tabindex="-1" id="sidebarMenu"
         aria-labelledby="sidebarMenuLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="sidebarMenuLabel">Company name</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebarMenu"
                    aria-label="Close"></button>
        </div>
        <div class="offcanvas-body d-md-flex flex-column p-0 pt-lg-3 overflow-y-auto">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 text-dark {{Request::is('/') ? 'active': ''}}" aria-current="page" href="/">
                        <svg class="bi">
                            <span class="bi bi-house"></span>
                        </svg>
                        Home
                    </a>
                </li>
                @can('kaprodi')
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 text-dark {{Request::is('dashboard/matakuliah') ? 'active': ''}}" href="/dashboard/mata-kuliah">
                        <svg class="bi">
                            <i class="bi bi-file-post"></i>
                        </svg>
                        Mata Kuliah
                    </a>
                </li>
                @endcan
                @can('admin')
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2 text-dark {{Request::is('dashboard/users') ? 'active': ''}}" href="/dashboard/users">
                            <svg class="bi">
                                <i class="bi bi-file-post"></i>
                            </svg>
                            Users
                        </a>
                    </li>
                @endcan
            </ul>
            <hr class="my-3">
            <ul class="nav flex-column mb-auto">
                <li class="nav-item">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a class="nav-link d-flex align-items-center gap-2 text-dark" href="route('logout')"
                           onclick="event.preventDefault();
                                                this.closest('form').submit();">
                            <svg class="bi">
                                <i class="bi bi-door-closed"></i>
                            </svg>
                            {{ __('Log Out') }}
                        </a>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</div>

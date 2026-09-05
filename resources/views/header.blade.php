<nav class="header-inner">

    {{-- Left Side --}}
    <div class="header-left">

        <span class="welcome-text">
            Hi, {{ auth()->user()->name }}
            <span class="user-role">
                ({{ auth()->user()->role }})
            </span>
        </span>

    </div>


    {{-- Right Side --}}
    <div class="header-actions">

        {{-- Search --}}
        <div class="dropdown">

            <button type="button" class="header-icon-btn" id="searchDropdownBtn" data-bs-toggle="dropdown"
                aria-expanded="false" title="Search">
                <i class="fa fa-search"></i>
            </button>


            <div class="dropdown-menu dropdown-menu-end search-dropdown" aria-labelledby="searchDropdownBtn">
                <div class="p-2">
                    <input type="text" class="form-control form-control-sm" placeholder="Search Orders or Products"
                        id="searchInputBox" autocomplete="off">
                </div>
            </div>

        </div>


        {{-- Messages --}}
        <button type="button" class="header-icon-btn" title="Messages">
            <i class="fa fa-commenting"></i>
        </button>


        {{-- Notifications --}}
        <div class="dropdown">

            <button type="button" class="header-icon-btn notification-btn" data-bs-toggle="dropdown"
                aria-expanded="false" title="Notifications">

                <i class="fa fa-bell"></i>

                @if ($unreadCount)
                    <span class="notification-count">
                        {{ $unreadCount }}
                    </span>
                @endif

            </button>


            <ul class="dropdown-menu dropdown-menu-end notification-dropdown">

                @forelse ($notifications as $notification)

                    <li>
                        <div class="dropdown-item small {{ !$notification->is_read ? 'fw-bold' : '' }}">

                            <div>
                                {{ $notification->message }}
                            </div>

                            <div class="text-muted small">
                                {{ $notification->created_at->diffForHumans() }}
                            </div>

                        </div>
                    </li>

                @empty

                    <li>
                        <span class="dropdown-item text-muted">
                            No notifications
                        </span>
                    </li>

                @endforelse

            </ul>

        </div>


        {{-- User --}}
        <div class="dropdown">

            <button type="button" class="header-icon-btn" id="userDropdownMenu" data-bs-toggle="dropdown"
                aria-expanded="false" title="User Menu">
                <i class="fa-solid fa-user"></i>
            </button>


            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdownMenu">

                <li>
                    <a class="dropdown-item" href="{{ route('profile.show', ['id' => auth()->user()->id]) }}">
                        <i class="fa-solid fa-user-pen me-2"></i>
                        Edit Profile
                    </a>
                </li>


                <li>
                    <hr class="dropdown-divider">
                </li>


                <li>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>

                    <a class="dropdown-item" href="#"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fa-solid fa-right-from-bracket me-2"></i>
                        Logout
                    </a>

                </li>

            </ul>

        </div>


        {{-- Company Settings --}}
        <div class="dropdown">

            <button type="button" class="company-settings-btn dropdown-toggle" id="companySettingDropdown"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-gear me-1"></i>
                Company Setting
            </button>


            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="companySettingDropdown">

                <li>
                    <a class="dropdown-item" href="{{ route('company.index') }}">
                        <i class="fa-solid fa-building me-2"></i>
                        Company Profile
                    </a>
                </li>


                <li>
                    <a class="dropdown-item" href="{{ route('settings.general') }}">
                        <i class="fa-solid fa-percent me-2"></i>
                        Tax Settings
                    </a>
                </li>

            </ul>

        </div>

    </div>

</nav>


@push('scripts')

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            /* =========================================================
               SEARCH
            ========================================================= */

            let typingTimer;
            const debounceDelay = 2000;

            const inputBox = document.getElementById('searchInputBox');
            const dropdownBtn = document.getElementById('searchDropdownBtn');

            if (inputBox) {

                inputBox.addEventListener('input', function () {

                    clearTimeout(typingTimer);

                    typingTimer = setTimeout(() => {

                        const query = inputBox.value.trim();

                        if (!query) {
                            return;
                        }

                        console.log('Searching for:', query);

                        /*
                         * Add your actual search logic here.
                         */

                        const dropdown =
                            bootstrap.Dropdown.getInstance(dropdownBtn);

                        if (dropdown) {
                            dropdown.hide();
                        }

                    }, debounceDelay);

                });


                inputBox.addEventListener('blur', function () {
                    clearTimeout(typingTimer);
                });

            }


            /* =========================================================
               NOTIFICATIONS
            ========================================================= */

            document
                .querySelectorAll('.notification-btn')
                .forEach(function (button) {

                    button.addEventListener(
                        'shown.bs.dropdown',
                        function () {

                            const csrfToken =
                                document
                                    .querySelector('meta[name="csrf-token"]')
                                    ?.getAttribute('content');

                            if (!csrfToken) {
                                return;
                            }

                            fetch('/notifications/read', {
                                method: 'POST',

                                headers: {
                                    'X-CSRF-TOKEN': csrfToken,
                                    'Accept': 'application/json'
                                }
                            });

                        }
                    );

                });

        });
    </script>

@endpush
<header>
    <nav class="d-flex justify-content-between align-items-center w-100 container-fluid">
        <div class="gap-2 d-flex align-items-center">
            <div class="dropdown searchBox positon-relative d-inline-block" id="searchDropdownBtn"
                data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa fa-search" aria-hidden="true"></i>
            </div>

            <!-- Dropdown Menu with Input -->
            <div class="dropdown-menu p-0 position-relative" aria-labelledby="searchDropdownBtn"
                style="min-width: 250px;">
                <input type="text" class="form-control form-control-sm" placeholder="Search Orders or Products"
                    id="searchInputBox">
            </div>
            <div class="msgBox">
                <i class="fa fa-commenting" aria-hidden="true"></i>
            </div>
            <div class="dropdown notificationBox position-relative d-inline-block">
                <i class="fa fa-bell" data-bs-toggle="dropdown"></i>

                @if($unreadCount)
                    <span class="count">{{ $unreadCount }}</span>
                @endif

                <ul class="dropdown-menu dropdown-menu-end p-2" style="width:300px">
                    @forelse($notifications as $notification)
                        <li class="dropdown-item small {{ !$notification->is_read ? 'fw-bold' : '' }}">
                            {{ $notification->message }}
                            <div class="text-muted small">
                                {{ $notification->created_at->diffForHumans() }}
                            </div>
                        </li>
                    @empty
                        <li class="dropdown-item text-muted">No notifications</li>
                    @endforelse
                </ul>
            </div>

            <div class="dropdown userDropdown position-relative d-inline-block p-0">
                <button class="m-0 btn btn-light" type="button" id="userDropdownMenu" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    <i class="fa-solid fa-user"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdownMenu">
                    <li>
                        <a class="dropdown-item" href="{{ route('profile.show', ['id' => auth()->user()->id]) }}">
                            Edit Profile
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                        <a class="dropdown-item" href="#"
                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            Logout
                        </a>
                    </li>
                </ul>
            </div>
            <div class="dropdown lang-dropdown position-relative d-inline-block">
                <button class="btn btn-light dropdown-toggle" type="button" id="companySettingDropdown"
                    data-bs-toggle="dropdown" aria-expanded="false">
                    ⚙️ Company Setting
                </button>
                <ul class="dropdown-menu" aria-labelledby="companySettingDropdown">
                    <li><a class="dropdown-item" href="{{ route('company.index') }}">Company Profile</a></li>
                    <li><a class="dropdown-item" href="{{ route('settings.general') }}">Tax Settings</a></li>
                </ul>
            </div>
        </div>



    </nav>
</header>

@push('scripts')
    <script>
        let typingTimer;
        const debounceDelay = 2000; // 2 seconds

        const inputBox = document.getElementById('searchInputBox');
        const dropdownBtn = document.getElementById('searchDropdownBtn');

        inputBox.addEventListener('input', function () {
            clearTimeout(typingTimer); // Clear the previous timer

            typingTimer = setTimeout(() => {
                const query = this.value.trim();
                if (query !== '') {
                    alert('Searching for: ' + query);

                    // Hide the dropdown
                    bootstrap.Dropdown.getInstance(dropdownBtn).hide();
                }
            }, debounceDelay);
        });

        // Optional: Clear timer if user leaves the input
        inputBox.addEventListener('blur', () => {
            clearTimeout(typingTimer);
        });


        document.querySelectorAll('.notificationBox').forEach(function (dropdown) {
            dropdown.addEventListener('shown.bs.dropdown', function () {
                fetch('/notifications/read', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document
                            .querySelector('meta[name="csrf-token"]')
                            .getAttribute('content')
                    }
                });
            });
        });


    </script>
@endpush
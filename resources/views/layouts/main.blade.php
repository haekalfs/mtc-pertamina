<!doctype html>
<!--[if gt IE 8]><!--> <html class="no-js" lang=""> <!--<![endif]-->
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>MTC Performance | Pertamina</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicon and Apple Touch Icon -->
    <link href="{{ asset('img/mtc-logo-1.jpg') }}" rel="icon">
    <link href="{{ asset('img/mtc-logo-1.jpg') }}" rel="apple-touch-icon">

    <script src="{{ asset('js/chartjs.js') }}"></script>

    <!-- CSS Libraries -->
    {{-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/normalize.css@8.0.0/normalize.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.1.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lykmapipo/themify-icons@0.1.2/css/themify-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/pixeden-stroke-7-icon@1.2.3/pe-icon-7-stroke/dist/pe-icon-7-stroke.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.2.0/css/flag-icon.min.css"> --}}

    <link rel="stylesheet" href="{{ asset('assets/css/normalize.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pe-icon-7-stroke.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/flag-icon.min.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/css/cs-skin-elastic.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/lib/chosen/chosen.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@3.9.0/dist/fullcalendar.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

    <!-- Fonts -->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800' rel='stylesheet' type='text/css'>

    <!-- DataTables CSS -->
    <link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">

    <!-- jQuery -->
    <script src="{{ asset('js/jquery.min.js') }}"></script>

    <!-- DataTables JS -->
    <script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

    <!-- Page Level Custom Scripts -->
    <script src="{{ asset('js/demo/datatables-demo.js') }}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <!-- Bootstrap -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script src="https://cdn.canvasjs.com/canvasjs.min.js"></script>
    <!-- Moment.js -->
    <script src="{{ asset('js/moment.min.js') }}"></script>
    <!-- Include Date Range Picker CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <!-- Include Date Range Picker JS -->
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
</head>

<body>
    <!-- Left Panel -->
    <aside id="left-panel" class="left-panel">
        <div class="sidebar-footer">
            <a href="{{ route('profile.view') }}" class="sidebar-user mr-3 ml-3 mt-2 mb-2">
                <span class="sidebar-user-img mr-2">
                    <picture>
                        @if(Auth::user()->users_detail->profile_pic)
                        <img class="rounded-circle" alt="" src="{{ asset(Auth::user()->users_detail->profile_pic) }}">
                        @else
                        <img src="images/admin.jpg" alt="User name">
                        @endif
                    </picture>
                </span>
                <div class="sidebar-user-info" id="sidebar-info">
                    <span class="sidebar-user__title">
                        {{ explode(' ', Auth::user()->name)[0] }}
                    </span>
                    <span class="sidebar-user__subtitle font-weight-bold">{{ Auth::user()->users_detail->position->position_name }}</span>
                </div>
            </a>
        </div>
        <nav class="navbar navbar-expand-sm navbar-default">
            <div id="main-menu" class="main-menu collapse navbar-collapse">
                <ul class="nav navbar-nav">
                    <li class="menu-title">Dashboard</li><!-- /.menu-title -->
                    <li class="@yield('active-dashboard')">
                        <a href="{{ route('dashboard') }}"><i class="menu-icon fa fa-laptop"></i>Home </a>
                    </li>
                    <li class="menu-title">Main Menu</li><!-- /.menu-title -->
                    @usr_acc(101)
                    <li class="menu-item-has-children dropdown @yield('active-penlat') @yield('show-penlat')">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="menu-icon ti-layout-list-thumb-alt"></i> Penlat</a>
                        <ul class="sub-menu children dropdown-menu font-weight-normal @yield('show-penlat')">
                            <li><i class="fa fa-list-alt"></i><a href="{{ route('penlat') }}" class="@yield('penlat')">List Pelatihan</a></li>
                            <li><i class="fa fa-folder"></i><a href="{{ route('batch-penlat') }}" class="@yield('batch-penlat')">Batch Program</a></li>
                            <li><i class="fa fa-check-square-o"></i><a href="{{ route('tool-requirement-penlat') }}" class="@yield('tool-requirement-penlat')">Kebutuhan Alat Penlat</a></li>
                        </ul>
                    </li>
                    @endusr_acc
                    @usr_acc(101)
                    <li class="menu-item-has-children dropdown @yield('active-operation') @yield('show-operation')">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="menu-icon fa fa-cogs"></i> Operasi</a>
                        <ul class="sub-menu children dropdown-menu font-weight-normal @yield('show-operation')">
                            <li><i class="fa fa-tachometer"></i><a href="{{ route('operation') }}" class="@yield('operation')">Dashboard</a></li>
                            <li><i class="fa fa-users"></i><a href="{{ route('participant-infographics') }}" class="@yield('participant-infographics')">Infografis Peserta</a></li>
                            <li><i class="fa fa-fire-extinguisher"></i><a href="{{ route('tool-inventory') }}" class="@yield('tool-inventory')">Inventaris Alat</a></li>
                            <li><i class="fa fa-building-o"></i><a href="{{ route('room-inventory') }}" class="@yield('room-inventory')">Inventaris Ruangan</a></li>
                            <li><i class="fa fa-cog"></i><a href="{{ route('utility') }}" class="@yield('utility')">Utilitas</a></li>
                        </ul>
                    </li>
                    @endusr_acc
                    @usr_acc(103)
                    <li class="menu-item-has-children dropdown @yield('active-pd') @yield('show-pd')">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-chain"></i>Plan & Development</a>
                        <ul class="sub-menu children dropdown-menu font-weight-normal @yield('show-pd')">
                            <li><i class="fa fa-tachometer"></i><a href="{{ route('plan-dev') }}" class="@yield('plan-dev')">Dashboard</a></li>
                            <li><i class="fa fa-trophy"></i><a href="{{ route('feedback-report-main') }}" class="@yield('feedback-report')">Feedback Report</a></li>
                            <li><i class="fa fa-tag"></i><a href="{{ route('training-reference') }}" class="@yield('training-reference')">Referensi Pelatihan</a></li>
                            {{-- <li><i class="fa fa-cog"></i><a href="#">Monitoring Approval</a></li> --}}
                            <li><i class="fa fa-male"></i><a href="{{ route('instructor') }}" class="@yield('instructor')">Instruktur</a></li>
                            <li><i class="fa fa-certificate"></i><a href="{{ route('certificate-main') }}" class="@yield('certificate')">Certification</a></li>
                            <li><i class="fa fa-file-text"></i><a href="{{ route('monitoring-approval') }}" class="@yield('monitoring-approval')">Monitoring Approvals</a></li>
                            <li><i class="fa fa-warning"></i><a href="{{ route('regulation') }}" class="@yield('regulation')">Regulasi</a></li>
                        </ul>
                    </li>
                    @endusr_acc
                    @usr_acc(104)
                    <li class="menu-item-has-children dropdown @yield('active-marketing') @yield('show-marketing')">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-suitcase"></i>Marketing</a>
                        <ul class="sub-menu children dropdown-menu font-weight-normal @yield('show-marketing')">
                            <li><i class="menu-icon fa fa-tachometer"></i><a href="{{ route('marketing') }}" class="@yield('marketing')">Dashboard</a></li>
                            <li><i class="fa fa-bullhorn"></i><a href="{{ route('marketing-campaign') }}" class="@yield('marketing-campaign')">Marketing Campaign</a></li>
                            <li><i class="fa fa-instagram"></i><a href="{{ route('insight-socmed') }}" class="@yield('socmed')">Social Media Enggagement</a></li>
                            <li><i class="fa fa-sitemap"></i><a href="{{ route('company-agreement') }}" class="@yield('company-agreement')">Company Agreement</a></li>
                        </ul>
                    </li>
                    @endusr_acc
                    @usr_acc(102)
                    <li class="menu-item-has-children dropdown @yield('active-finance') @yield('show-finance')">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-money"></i>Finance</a>
                        <ul class="sub-menu children dropdown-menu font-weight-normal @yield('show-finance')">
                            <li><i class="fa fa-tachometer"></i><a href="{{ route('finance') }}" class="@yield('finance')">Dashboard</a></li>
                            <li><i class="fa fa-list-alt"></i><a href="{{ route('vendor-payment') }}" class="@yield('vendor-payment')">Pembayaran Vendor</a></li>
                            <li><i class="ti-stats-down"></i><a href="{{ route('profits') }}" class="@yield('cost')">Profits & Loss</a></li>
                        </ul>
                    </li>
                    @endusr_acc
                    @usr_acc(105)
                    <li class="menu-item-has-children dropdown @yield('active-kpi') @yield('show-kpi')">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-bar-chart-o"></i>KPI</a>
                        <ul class="sub-menu children dropdown-menu font-weight-normal @yield('show-kpi')">
                            <li><i class="fa fa-tachometer"></i><a href="{{ route('kpi') }}" class="@yield('kpi')">Dashboard</a></li>
                            <li><i class="fa fa-cog"></i><a href="{{ route('manage-kpi') }}" class="@yield('manage-kpi')">Manage KPI</a></li>
                            <li><i class="fa fa-file"></i><a href="{{ route('report-kpi') }}" class="@yield('report-kpi')">Laporan</a></li>
                        </ul>
                    </li>
                    @endusr_acc
                    @usr_acc(106)
                    <li class="menu-item-has-children dropdown @yield('active-akhlak') @yield('show-akhlak')">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-star-half-empty"></i>Akhlak</a>
                        <ul class="sub-menu children dropdown-menu font-weight-normal @yield('show-akhlak')">
                            <li><i class="ti-signal"></i><a href="{{ route('morning-briefing') }}" class="@yield('morning-briefing')">Morning Briefing</a></li>
                            <li><i class="ti-stats-up"></i><a href="{{ route('akhlak.achievements') }}" class="@yield('akhlak')">Pencapaian AKHLAK</a></li>
                            <li><i class="fa fa-file"></i><a href="{{ route('report-akhlak') }}" class="@yield('report-akhlak')">Laporan</a></li>
                        </ul>
                    </li>
                    @endusr_acc

                    @usr_acc(201)
                    <li class="menu-title">Settings</li><!-- /.menu-title -->
                    <li class="menu-item-has-children dropdown @yield('active-user') @yield('show-user')">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-users"></i>Users</a>
                        <ul class="sub-menu children dropdown-menu font-weight-normal @yield('show-user')">
                            <li><i class="menu-icon fa fa-sign-in"></i><a href="{{ route('manage.users') }}" class="@yield('manage-users')">Manage Users</a></li>
                            <li><i class="menu-icon fa fa-sign-in"></i><a href="{{ route('manage.dept.post') }}" class="@yield('manage-dept')">Dept. & Position</a></li>
                            <li><i class="menu-icon fa fa-sign-in"></i><a href="{{ route('manage.roles') }}" class="@yield('manage-roles')">Manage Roles</a></li>
                        </ul>
                    </li>
                    @endusr_acc

                    @usr_acc(202)
                    <li class="menu-item-has-children dropdown @yield('active-access') @yield('show-access')" style="padding-bottom: 15%;">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="menu-icon fa fa-ban"></i>User Access Control</a>
                        <ul class="sub-menu children dropdown-menu font-weight-normal @yield('show-access')">
                            <li><i class="menu-icon fa fa-ban"></i><a href="{{ route('manage.access') }}" class="@yield('manage-access')">Manage Access</a></li>
                            <li><i class="menu-icon fa fa-times-circle"></i><a href="{{ route('manage.request') }}" class="@yield('manage-request')">Manage Allowed Method</a></li>
                        </ul>
                    </li>
                    @endusr_acc
                </ul>
            </div><!-- /.navbar-collapse -->
        </nav>
    </aside>
    <!-- /#left-panel -->
    <!-- Right Panel -->
    <div id="right-panel" class="right-panel">
        <!-- Header-->
        <header id="header" class="header">
            <div class="top-left">
                <div class="navbar-header">
                    <img src="{{ asset('img/mtc-logo.png') }}" class="mb-2 mr-2" style="height: 40px; width: 40px;" /> <span class="font-weight-bold">MTC Performance</span>
                    <a id="menuToggle" class="menutoggle"><i class="fa fa-bars"></i></a>
                </div>
            </div>
            <div class="top-right">
                <div class="header-menu">
                    <div class="header-left">

                        <div class="dropdown for-notification">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="notification" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-bell"></i>
                                @if ($notifications->isEmpty() || $notificationsCount == 0)
                                @else
                                <span class="count bg-danger">
                                    <span class="badge badge-danger badge-counter">
                                        {{ $notificationsCount }}
                                    </span>
                                </span>
                                @endif
                            </button>
                            <div class="dropdown-menu" aria-labelledby="notification">
                                <p class="red">You have {{ $notificationsCount }} Notification(s)</p>
                                @foreach ($notifications as $notification)
                                    <a class="dropdown-item media" href="#">
                                        <i class="fa fa-check"></i>
                                        <p>{{ $notification->description }}</p>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        {{-- <div class="dropdown for-message">
                            <button class="btn btn-secondary dropdown-toggle" type="button" id="message" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-envelope"></i>
                                <span class="count bg-primary">1</span>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="message">
                                <p class="red">You have 1 Mails</p>
                                <a class="dropdown-item media" href="#">
                                    <span class="photo media-left"><img alt="avatar" src="images/avatar/1.jpg"></span>
                                    <div class="message media-body">
                                        <span class="name float-left">Haekal - CCD</span>
                                        <span class="time float-right">Just now</span>
                                        <p>Hello, this is an example msg</p>
                                    </div>
                                </a>
                            </div>
                        </div> --}}
                    </div>

                    <div class="user-area dropdown float-right">
                        <a href="#" class="dropdown-toggle active" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            @if(Auth::user()->users_detail->profile_pic)
                            <img class="user-avatar rounded-circle" style="width:38px; height:38px;" src="{{ asset(Auth::user()->users_detail->profile_pic) }}" alt="User Avatar">
                            @else
                            <img class="user-avatar rounded-circle" src="images/admin.jpg" alt="User Avatar">
                            @endif
                        </a>

                        <div class="user-menu dropdown-menu">
                            <a class="nav-link" href="{{ route('profile.view') }}"><i class="fa fa-user"></i>My Profile</a>

                            {{-- <a class="nav-link" href="#"><i class="fa fa- user"></i>Notifications</a> --}}
                            {{-- <a class="nav-link" href="#"><i class="fa fa- user"></i>Notifications <span class="count">13</span></a> --}}

                            {{-- <a class="nav-link" href="#"><i class="fa fa -cog"></i>Settings</a> --}}

                            <a class="nav-link" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-power-off"></i>Logout</a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                @csrf
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </header>
        @if(Cache::has('jobs_processing'))
            <div id="toastNotification" class="toast__container position-fixed bottom-0 right-0 p-3" style="z-index: 5; right: 0; bottom: 0;">
                <div class="toast toast--yellow">
                    <div class="toast__icon">
                    </div>
                    <div class="toast__content">
                        <p class="toast__type"><i class="fa fa-spinner fa-spin"></i> Loading</p>
                        <p class="toast__message">Data is being imported!! Please wait until the data is all processed.</p>
                    </div>
                </div>
            </div>
        @endif
        <!-- /#header -->
        @yield('breadcumbs')
        <!-- Content -->
        <div class="content">
            @yield('content')
        </div>
        <!-- /.content -->
        <div class="clearfix"></div>
        <!-- Footer -->
        <footer class="site-footer">
            <div class="footer-inner bg-white">
                <div class="row">
                    <div class="col-sm-6">
                        Copyright &copy; {{ date('Y') }} MTC Performance
                    </div>
                </div>
            </div>
        </footer>
    </div>
    <script src="{{ asset('js/custom.js') }}"></script>

    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@2.2.4/dist/jquery.min.js"></script>

    <!-- Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.4/dist/umd/popper.min.js"></script>
    <!-- Custom Scripts -->
    <script src="{{ asset('assets/js/main.js') }}"></script>
    <script src="{{ asset('assets/js/lib/chosen/chosen.jquery.min.js') }}"></script>

    <!-- FullCalendar -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.9.0/dist/fullcalendar.min.js"></script>
    <script src="{{ asset('assets/js/init/fullcalendar-init.js') }}"></script>

    <!-- Match Height -->
    <script src="https://cdn.jsdelivr.net/npm/jquery-match-height@0.7.2/dist/jquery.matchHeight.min.js"></script>

    <script>
        jQuery(document).ready(function() {
            jQuery(".standardSelect").chosen({
                disable_search_threshold: 10,
                no_results_text: "Oops, nothing found!",
                width: "100%"
            });
        });
        jQuery(document).ready(function(){
            jQuery('.toast__close').click(function(e){
                e.preventDefault();
                var parent = $(this).parent('.toast');
                parent.fadeOut("slow", function() { $(this).remove(); } );
            });
        });
    </script>
</body>
</html>

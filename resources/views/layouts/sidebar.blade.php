<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="">
        <div class="sidebar-brand-icon rotate-n-15">
            <i class="fas fa-laugh-wink"></i>
        </div>
        <div class="sidebar-brand-text mx-3">SONAS<sup>VB</sup></div>
    </a>

    @php
        $sub_url = Request::segment(1);
    @endphp

    <!-- Divider -->
    <hr class="sidebar-divider my-0">
    <div class="sidebar-heading mt-2" style="padding-top: 8px">
        HOME
    </div>
    <li class="nav-item @if($sub_url == 'home') active @endif">
        <a class="nav-link" href="{{ url('/home') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Home</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <div class="sidebar-heading">
        MENU SONAS
    </div>
    <li class="nav-item @if($sub_url == 'initial-so') active @endif">
        <a class="nav-link" href="{{ url('/initial-so') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Inital SO</span></a>
    </li>
    <li class="nav-item @if($sub_url == 'input-lokasi') active @endif">
        <a class="nav-link" href="{{ url('/input-lokasi') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Master Lokasi SO</span></a>
    </li>
    <li class="nav-item @if($sub_url == 'setting-jalur') active @endif">
        <a class="nav-link" href="{{ url('/setting-jalur') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Setting Jalur HH</span></a>
    </li>
    <li class="nav-item @if($sub_url == 'input-kkso') active @endif">
        <a class="nav-link" href="{{ url('/input-kkso') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Input KKSO</span></a>
    </li>
    <li class="nav-item @if($sub_url == 'proses-ba-so') active @endif">
        <a class="nav-link" href="{{ url('/proses-ba-so') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Proses BA SO</span></a>
    </li>
    <li class="nav-item @if($sub_url == 'report') active @endif">
        <a class="nav-link" href="{{ url('/report') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Report</span></a>
    </li>
    <li class="nav-item @if($sub_url == 'monitoring-so') active @endif">
        <a class="nav-link" href="{{ url('/monitoring-so') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Monitoring SO</span></a>
    </li>
    <li class="nav-item @if($sub_url == 'set-limit-so') active @endif">
        <a class="nav-link" href="{{ url('/set-limit-so') }}">
            <i class="fas fa-fw fa-tachometer-alt"></i>
            <span>Set Limit SO</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <!-- <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div> -->
</ul>

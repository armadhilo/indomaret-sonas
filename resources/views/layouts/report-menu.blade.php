<div class="card shadow mb-4">
    <div class="card-body">
    <p style="color: #012970; font-weight: 700; font-size: 1.2rem; text-align: center">Menu Report</p>
        <ul class="list-report">
            @php
                $sub_url = Request::segment(2);
            @endphp
            <li><a href="{{ url('/report/list-form-kkso') }}" class="@if($sub_url == 'list-form-kkso') active @endif">List Form KKSO</a></li>
            <li><a href="{{ url('/report/register-kkso') }}" class="@if($sub_url == 'register-kkso') active @endif">Register KKSO I</a></li>
            <li><a href="{{ url('/report/edit-list-kkso') }}" class="@if($sub_url == 'edit-list-kkso') active @endif">Edit List KKSO</a></li>
            <li><a href="{{ url('/report/register-kkso-2') }}" class="@if($sub_url == 'register-kkso-2') active @endif">Register KKSO II</a></li>
            <li><a href="{{ url('/report/perincian-baso') }}" class="@if($sub_url == 'perincian-baso') active @endif">Perincian BASO</a></li>
            <li><a href="{{ url('/report/ringkasan-baso') }}" class="@if($sub_url == 'ringkasan-baso') active @endif">Ringkasan BASO</a></li>
            <li><a href="{{ url('/report/daftar-item-adjustment') }}" class="@if($sub_url == 'daftar-item-adjustment') active @endif">Daftar Item yg sudah di Adjustment</a></li>
            <li><a href="{{ url('/report/daftar-kkso-acost-0') }}" class="@if($sub_url == 'daftar-kkso-acost-0') active @endif">Daftar KKSO dgn ACost 0</a></li>
            <li><a href="{{ url('/report/daftar-master-lokasi-so') }}" class="@if($sub_url == 'daftar-master-lokasi-so') active @endif">Daftar Master Lokasi SO</a></li>
            <li><a href="{{ url('/report/daftar-item-tidak-di-master') }}" class="@if($sub_url == 'daftar-item-tidak-di-master') active @endif">Daftar Item yg blm ada di Master</a></li>
            <li><a href="{{ url('/report/lokasi-rak-belum-di-so') }}" class="@if($sub_url == 'lokasi-rak-belum-di-so') active @endif">Lokasi RAK yang belum di SO</a></li>
            <li><a href="{{ url('/report/inquiry-plano-sonas') }}" class="@if($sub_url == 'inquiry-plano-sonas') active @endif">Inquiry Plano SONAS (Excel)</a></li>
            <li><a href="{{ url('/report/lpp-month-end') }}" class="@if($sub_url == 'lpp-month-end') active @endif">LPP Month End (Excel)</a></li>
            <li><a href="{{ url('/report/cetak-draft-lhso') }}" class="@if($sub_url == 'cetak-draft-lhso') active @endif">Cetak Draft LHSO</a></li>
            <li><a href="{{ url('/report/cetak-draft-sebelum-lhso') }}" class="@if($sub_url == 'cetak-draft-sebelum-lhso') active @endif">Cetak Draft Retur Sebelum LHSO</a></li>
            <li><a href="{{ url('/report/lokasi-so') }}" class="@if($sub_url == 'lokasi-so') active @endif">Lokasi SO</a></li>
        </ul>
    </div>
</div>
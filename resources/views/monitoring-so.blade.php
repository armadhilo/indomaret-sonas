@extends('layouts.master')
@section('title')
    <h1 class="pagetitle">MONITORING SO</h1>
@endsection

@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.css" rel="stylesheet" type="text/css"/>
<style>
summary {
    display: block;
    cursor: pointer;
    outline: 0; 
}

summary::-webkit-details-marker {
    display: none;
}

.tree-nav__item {
    display: block;
    white-space: nowrap;
    color: #ccc;
    position: relative;
}
.tree-nav__item.is-expandable::before {
    border-left: 1px solid #d5d5d5;
    content: "";
    height: 100%;
    left: 0.8rem;
    position: absolute;
    top: 2.4rem;
    height: calc(100% - 2.4rem);
}
.tree-nav__item .tree-nav__item {
    margin-left: 2.4rem;
}
.tree-nav__item.is-expandable[open] > .tree-nav__item-title::before {
    font-family: "ionicons";
    transform: rotate(90deg);
}
.tree-nav__item.is-expandable > .tree-nav__item-title {
    padding-left: 2.4rem;
}
.tree-nav__item.is-expandable > .tree-nav__item-title::before {
    position: absolute;
    will-change: transform;
    transition: transform 300ms ease;
    font-family: "ionicons";
    color: #000;
    font-size: 1.1rem;
    content: "\f125";
    left: 0;
    display: inline-block;
    width: 1.6rem;
    text-align: center;
}

.tree-nav__item-title {
    cursor: pointer;
    display: block;
    outline: 0;
    color: #000;
    font-size: 1.5rem;
    line-height: 3.2rem;
}
.tree-nav__item-title .icon {
    display: inline;
    padding-left: 1.6rem;
    margin-right: 0.8rem;
    color: #666;
    font-size: 1.4rem;
    position: relative;
}
.tree-nav__item-title .icon::before {
    top: 0;
    position: absolute;
    left: 0;
    display: inline-block;
    width: 1.6rem;
    text-align: center;
}

.tree-nav__item-title::-webkit-details-marker {
    display: none;
}

</style>
@endsection

@section('content')
    <script src="{{ url('js/home.js?time=') . rand() }}"></script>

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <nav class="tree-nav" id="tree_nav">
                            {{-- <details class="tree-nav__item is-expandable">
                                <summary class="tree-nav__item-title">The Realm of the Elderlings</summary>
                                <details class="tree-nav__item is-expandable">
                                    <details class="tree-nav__item is-expandable">
                                    </details>
                                
                                    <summary class="tree-nav__item-title">The Six Duchies</summary>
                                    
                                    <details class="tree-nav__item is-expandable">
                                        <summary class="tree-nav__item-title">The Fitz and the Fool Trilogy</summary>
                                        <div class="tree-nav__item">
                                        <a class="tree-nav__item-title"><i class="icon ion-ios-bookmarks"></i> FOOL'S ASSASSIN</a>
                                        <a class="tree-nav__item-title"><i class="icon ion-ios-book"></i> FOOL'S QUEST</a>
                                        <a class="tree-nav__item-title"><i class="icon ion-android-bookmark"></i> Assassin's Fate</a>
                                        </div>
                                    </details>
                                </details>
                            </details> --}}

                            
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('page-script')
<script>

    $(document).ready(function(){
        getMonitoring();
    });

    function getMonitoring(){
        $('#modal_loading').modal('show');
        $.ajax({
            url: `/monitoring-so/get-monitoring`,
            type: "GET",
            success: function(response) {
                setTimeout(function () { $('#modal_loading').modal('hide'); }, 500);
                let detailToko = '';
                let detailGudang;
                console.log(response);
                $.each(response.detail_toko, function(index, element) {
                    detailToko += `<summary class="tree-nav__item-title">${element.lso_koderak} : ${element.progress}</summary><details class="tree-nav__item is-expandable"></details>`;
                });
                // response.detailGudang.forEach(element => {
                //     detailToko += `<summary class="tree-nav__item-title">${element}</summary>`
                // });
                $("#tree_nav").append(`
                    <details class="tree-nav__item is-expandable">
                        <summary class="tree-nav__item-title">TOKO : ${response.toko.progress}</summary>
                        <details class="tree-nav__item is-expandable">
                            ${detailToko}
                        </details>
                        
                    </details>
                `);
            }, error: function(jqXHR, textStatus, errorThrown) {
                setTimeout(function () { $('#modal_loading').modal('hide'); }, 500);
                Swal.fire({
                    text: (jqXHR.responseJSON && jqXHR.responseJSON.code === 400)
                        ? jqXHR.responseJSON.message
                        : "Oops! Terjadi kesalahan segera hubungi tim IT (" + errorThrown + ")",
                    icon: "error"
                });
            }
        });
    }
</script>

@endpush

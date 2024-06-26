@extends('dashboard.master')

@section('content')
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
        <div
            class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center my-3 pt-3 ps-3 pb-2 dashboard rounded-1">
            <div>
                <h1>Buat Rencana Polling</h1>
            </div>
            @can('kaprodi')
                <div class="pe-3">
                    <a href="/dashboard/polling/create" class="btn btn-success d-flex gap-2">
                        <i class="bi bi-journal-plus"></i>
                        <span>Create Polling Baru</span>
                    </a>
                </div>
            @endcan
        </div>

        @if(session()->has('success'))
            <div class="alert alert-success" role="alert" id="myAlert">
                {{session('success')}}
            </div>
        @elseif(session()->has('errors'))
            <div class="alert alert-danger d-flex align-items-center" role="alert" id="myAlert">
                <div>
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    {{session('errors')}}
                </div>
            </div>
        @endif
        <div class="card bg-light-subtle shadow border-0 rounded-3">
            <div class="table-responsive small pt-3 px-3">
                <table class="table table-striped table-sm ">
                    <thead>
                    <tr>
                        <th scope="col">Nama Polling</th>
                        <th scope="col">Periode</th>
                        <th scope="col">Status</th>
                        @can('kaprodi')
                            <th scope="col">Edit</th>
                            <th scope="col">Delete</th>
                        @endcan
                    </tr>
                    @foreach($datas as $pol)
                        <tr>
                            <td>{{$pol->nama_polling}}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($pol->start_at)->format('d F Y') }}
                                - {{ \Carbon\Carbon::parse($pol->end_at)->format('d F Y') }}
                            </td>
                            <td>{{ $pol->is_active == 1 ? 'Aktif' : 'Tidak Aktif' }}</td>
                            @can('kaprodi')
                                <td>
                                    <a href="/dashboard/polling/{{$pol->id_polling}}/edit"
                                       class="btn btn-warning pt-0 pb-1 px-2">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                </td>
                                <td>
                                    <button class="btn btn-danger pt-0 pb-1 px-2"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteModal"
                                            data-id="{{$pol->id_polling}}">
                                        <i class="bi bi-trash3-fill"></i>
                                    </button>
                                </td>
                            @endcan
                        </tr>
                    </thead>
                    <tbody>
                    @endforeach
                </table>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="exampleModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Delete Polling</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="p-0 pb-1 m-0"> Apakah anda akan menghapus polling ini?</p>
                        <p class="fst-italic modal-keterangan">Data yang dihapus tidak bisa dikembalikan </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <form method="post" action="" class="d-inline" id="deleteForm">
                            @method('delete')
                            @csrf
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- End Modal -->
    </main>
@endsection

@section('js-tambahan')
    <script>
        $(document).ready(function () {
            $('#deleteModal').on('show.bs.modal', function (event) {
                let button = $(event.relatedTarget);
                let id = button.data('id');
                let formAction = "/dashboard/polling/" + id;
                $('#deleteForm').attr('action', formAction);
            });
        });
    </script>
@endsection


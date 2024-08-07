@extends('layouts.main')

@section('active-operation')
active font-weight-bold
@endsection

@section('show-operation')
show
@endsection

@section('tool-inventory')
font-weight-bold
@endsection

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="menu-icon fa fa-fire-extinguisher"></i> Tool Inventory</h1>
        <p class="mb-4">Unduh Pencapaian Akhlak.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
    </div>
</div>
<div class="overlay overlay-mid" style="display: none;"></div>

<div class="alert alert-danger alert-success-delete-mid" role="alert" style="display: none;">
</div>

<div class="alert alert-success alert-success-saving-mid" role="alert" style="display: none;">
    Your entry has been saved successfully.
</div>
@if ($message = Session::get('success'))
<div class="alert alert-success alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif

@if ($message = Session::get('failed'))
<div class="alert alert-danger alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif

@if ($message = Session::get('warning'))
<div class="alert alert-warning alert-block">
    <button type="button" class="close" data-dismiss="alert">×</button>
    <strong>{{ $message }}</strong>
</div>
@endif
<style>
    .drop-zone {
    border: 2px dashed #ccc;
    padding: 10px;
    text-align: center;
    cursor: pointer;
    transition: background-color 0.3s, transform 0.3s;
    /* Smooth transition for animations */
}

.drop-zone.dragging {
    background-color: #e0e0e0;
    transform: scale(1.05);
    animation: pulse 1s infinite;
    /* Adds a subtle scale effect while dragging */
}

.drop-zone.clicked {
    animation: clickEffect 0.5s ease-out;
    /* Triggers a quick animation when clicked */
}

@keyframes pulse {
    0% {
        transform: scale(1);
        border-color: #ccc;
    }
    50% {
        transform: scale(1.05);
        border-color: #007bff;
    }
    100% {
        transform: scale(1);
        border-color: #ccc;
    }
}

@keyframes clickEffect {
    0% {
        transform: scale(1);
        background-color: #e0e0e0;
    }
    50% {
        transform: scale(1.1);
        background-color: #d0d0d0;
    }
    100% {
        transform: scale(1);
        background-color: #e0e0e0;
    }
}
</style>
<div class="animated fadeIn zoom90">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-secondary" id="judul"><i class="menu-icon fa fa-fire-extinguisher"></i> Data Assets MTC</h6>
                    <div class="text-right">
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Register Tool</a>
                    </div>
                </div>
                <div class="row-toolbar mt-4 ml-2">
                    <div class="col">
                        <select style="max-width: 18%;" class="form-control" id="rowsPerPage">
                            <option value="-1">Show All</option>
                            @foreach($locations as $item)
                            <option value="{{ $item->id }}">{{ $item->description }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto text-right mr-2">
                        <input class="form-control" type="text" id="searchInput" placeholder="Search...">
                    </div>
                </div>
                <div class="card-body">
                    <div class="row zoom80 p-2">
                        @foreach($assets as $item)
                        <div class="col-md-6 mt-2">
                            <div class="card custom-card mb-3 shadow" style="background-color: #ffffff;">
                                <div class="row no-gutters">
                                    <div class="col-md-3 d-flex align-items-center justify-content-center" style="padding-left: 1em;">
                                        <img src="{{ asset($item->img->filepath) }}" style="height: 150px; width: 150px; border-radius: 15px;" class="card-img" alt="...">
                                    </div>
                                    <div class="col-md-9">
                                        <div class="card-body text-secondary">
                                            <div>
                                                <h5 class="card-title font-weight-bold">{{ $item->asset_name }}</h5>
                                                <ul class="ml-4">
                                                    <li class="card-text">Nomor Aset : {{ $item->asset_id }}</li>
                                                    <li class="card-text">Maker : {{ $item->asset_maker }}</li>
                                                    <li class="card-text">Kondisi Alat : {{ $item->asset_condition }}</li>
                                                    <li class="card-text">Stock : {{ $item->asset_stock }}</li>
                                                    <li class="card-text">Panduan Maintenance : <a href="#" class="text-secondary"><i class="fa fa-external-link fa-sm"></i>&nbsp;<i>View</i></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="card-icons">
                                            <span class="badge out-of-stock">OUT OF STOCK</span>
                                            <a href="#"><i class="fa fa-cog"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        <div class="col-md-12 text-center d-flex align-items-center justify-content-center mt-4">
                            <nav aria-label="Page navigation example">
                                <ul class="pagination">
                                  <li class="page-item"><a class="page-link" href="#">Previous</a></li>
                                  <li class="page-item"><a class="page-link" href="#">1</a></li>
                                  <li class="page-item"><a class="page-link" href="#">2</a></li>
                                  <li class="page-item"><a class="page-link" href="#">3</a></li>
                                  <li class="page-item"><a class="page-link" href="#">Next</a></li>
                                </ul>
                              </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="inputDataModal" tabindex="-1" role="dialog" aria-labelledby="inputDataModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header d-flex flex-row align-items-center justify-content-between">
                <h5 class="modal-title" id="inputDataModalLabel">Input Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="post" enctype="multipart/form-data" action="{{ route('kpis.store') }}">
                @csrf
                <div class="modal-body zoom80">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="kpi">Asset ID</label>
                            <input type="text" class="form-control" id="kpi" name="kpi" placeholder="Yearly Revenue..." required>
                        </div>
                        <div class="form-group">
                            <label for="target">Asset Name</label>
                            <input type="text" class="form-control" id="target" name="target" required>
                        </div>
                        <div class="form-group">
                            <label for="periode">Asset Maker</label>
                            <select class="form-control" id="periode" name="periode" required>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="periode">Asset Condition</label>
                            <select class="form-control" id="periode" name="periode" required>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="periode">Asset Stock</label>
                            <select class="form-control" id="periode" name="periode" required>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="periode">Asset Guidance</label>
                            <input type="file" class="form-control" id="target" name="target" required>
                        </div>
                        <label for="target">Asset Image <small>Optional</small></label>
                        <div class="form-group mb-0 drop-zone" id="drop_zone">
                            <span id="drop_zone_text">Paste or drag image here</span>
                            <input type="file" class="form-control-file underline-input d-none" name="picture" id="picture" accept="image/*">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-4 d-flex align-items-start">
                                        <img id="preview" style="max-width: 100px; max-height: 100px; display: none;">
                                        <button type="button" class="btn btn-sm btn-danger" id="discard" style="display: none;">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit Request</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    function displayFileName() {
        const input = document.getElementById('file');
        const label = document.getElementById('file-label');
        const file = input.files[0];
        if (file) {
            label.textContent = file.name;
        }
    }


    function addImagePreviewListener(fileInput, imgPreview, discardButton, dropZone, dropZoneText) {
        // Function to display the image preview and hide the text
        function displayImagePreview(src) {
            imgPreview.src = src;
            imgPreview.style.display = "block";
            discardButton.style.display = "inline-block";
            dropZoneText.style.display = "none";
        }

        // Handle file input change
        fileInput.addEventListener("change", function() {
            if (fileInput.files && fileInput.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    displayImagePreview(e.target.result);
                };
                reader.readAsDataURL(fileInput.files[0]);
            }
        });

        // Handle drag and drop
        dropZone.addEventListener("dragover", function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.add("dragging");
        });

        dropZone.addEventListener("dragleave", function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.remove("dragging");
        });

        dropZone.addEventListener("drop", function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.remove("dragging");

            if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                fileInput.files = e.dataTransfer.files;
                const event = new Event("change");
                fileInput.dispatchEvent(event);
            }
        });

        // Handle paste
        dropZone.addEventListener("paste", function(e) {
            const items = (e.clipboardData || e.originalEvent.clipboardData).items;
            for (let i = 0; i < items.length; i++) {
                if (items[i].type.indexOf("image") !== -1) {
                    const file = items[i].getAsFile();
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        displayImagePreview(e.target.result);
                    };
                    reader.readAsDataURL(file);
                }
            }
        });

        // Handle discard button
        discardButton.addEventListener("click", function() {
            imgPreview.src = "";
            imgPreview.style.display = "none";
            discardButton.style.display = "none";
            dropZoneText.style.display = "block";
            fileInput.value = ""; // Reset file input
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const dropZone = document.getElementById("drop_zone");
        const fileInput = document.getElementById("picture");
        const imgPreview = document.getElementById("preview");
        const discardButton = document.getElementById("discard");
        const dropZoneText = document.getElementById("drop_zone_text");

        dropZone.addEventListener("click", function() {
            dropZone.classList.add("clicked");
            setTimeout(() => {
                dropZone.classList.remove("clicked");
            }, 500);
        });

        dropZone.addEventListener("dragover", function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.add("dragging");
        });

        dropZone.addEventListener("dragleave", function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.remove("dragging");
        });

        dropZone.addEventListener("drop", function(e) {
            e.preventDefault();
            e.stopPropagation();
            dropZone.classList.remove("dragging");

            if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                fileInput.files = e.dataTransfer.files;
                const event = new Event("change");
                fileInput.dispatchEvent(event);
            }
        });

        addImagePreviewListener(fileInput, imgPreview, discardButton, dropZone, dropZoneText);
    });
</script>
@endsection


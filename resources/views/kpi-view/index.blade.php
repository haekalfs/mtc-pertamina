@extends('layouts.main')

@section('active-kpi')
active font-weight-bold
@endsection

@section('content')
<style>
    /* Form Labels */
    .modal-body label {
        font-weight: bold;
    }

    /* Form Inputs */
    .modal-body input[type="text"],
    .modal-body input[type="date"] {
        margin-bottom: 10px;
    }

    /* Radio Buttons */
    .radio-inline {
        margin-right: 50px;
    }
</style>
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
<div class="d-sm-flex align-items-center zoom90 justify-content-between">
    <div>
        <h1 class="h3 mb-2 font-weight-bold text-secondary"><i class="fa fa-users"></i> Dashboard KPI</h1>
        <p class="mb-4">Managing Access based on roles.</a></p>
    </div>
    <div class="d-sm-flex"> <!-- Add this div to wrap the buttons -->
        <select class="form-control" id="yearSelected" name="yearSelected" required onchange="redirectToPage()">
            @foreach (array_reverse($yearsBefore) as $year)
                <option value="{{ $year }}" @if ($year == $yearSelected) selected @endif>{{ $year }}</option>
            @endforeach
        </select>
    </div>
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
    .badge-container {
        display: flex;
        justify-content: space-around;
        align-items: center;
        margin-top: 20px;
        flex-wrap: wrap;
    }

    .badge-item {
        text-align: center;
        margin: 15px;
        width: 140px;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .badge-item img {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        box-shadow: 0 10px 12px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
    }

    .badge-item:hover img {
        transform: scale(1.1);
    }

    .badge-label {
        margin-top: 10px;
        font-size: 14px;
        font-weight: bold;
        color: #767676;
    }

    .progress-box-modified {
        padding: 20px;
        border-radius: 10px;
    }

    .progress {
        height: 25px;
        border-radius: 50px;
        overflow: hidden;
    }

    .progress-bar {
        background-size: 1rem 1rem;
    }
</style>
<div class="animated fadeIn zoom90">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="badge-container">
                    @foreach ($kpis as $kpi)
                        <div class="badge-item">
                            <div class="mx-auto d-block">
                                <a href="{{ route('pencapaian-kpi', $kpi->id) }}">
                                    <img class="align-self-center rounded-circle mb-2" alt="" src="{{ asset('img/kpi-default-logo.png') }}">
                                </a>
                            </div>
                            <div class="mx-auto d-block">
                                <small class="badge-label">{{ $kpi->indicator }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="progress-box-modified">
                    <h4 class="por-title mb-2">Realisasi Pencapaian Overall</h4>
                    <div class="progress mb-2">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $percentage ?? 0 }}%;" aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                            {{ $percentage ?? 0 }}%
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold" id="judul">List Indicators</h6>
                    <div class="text-right">
                        <a class="btn btn-primary btn-sm text-white" href="#" data-toggle="modal" data-target="#inputDataModal"><i class="menu-Logo fa fa-plus"></i> Create KPI</a>
                    </div>
                </div>
                <nav>
                    <div class="nav nav-tabs" id="nav-tab" role="tablist">
                        <a class="nav-item nav-link active" id="custom-nav-home-tab" data-toggle="tab" href="#custom-nav-home" role="tab" aria-controls="custom-nav-home" aria-selected="true"> Show Data</a>
                        <a class="nav-item nav-link" id="custom-nav-profile-tab" data-toggle="tab" href="#custom-nav-profile" role="tab" aria-controls="custom-nav-profile" aria-selected="false"> Show Charts</a>
                    </div>
                </nav>
                <div class="card-body">
                    <div class="tab-content" id="nav-tabContent">
                        <div class="tab-pane fade show active" id="custom-nav-home" role="tabpanel" aria-labelledby="custom-nav-home-tab">
                            <table  id="docLetter" class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Indicator</th>
                                        <th>Target</th>
                                        <th>Periode</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>@php $no = 1; @endphp
                                    @foreach ($indicators as $item)
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td>{{ $item->indicator }}</td>
                                        <td>{{ $item->target }}</td>
                                        <td>{{ $item->periode }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('preview-kpi', ['id' => $item->id]) }}" class="btn btn-outline-secondary btn-sm mr-2"><i class="ti-eye"></i> Preview</a>
                                            <a href="#" class="btn btn-outline-danger btn-sm btn-details" onclick="confirmDelete({{ $item->id }});"><i class="fa fa-ban"></i> Delete</a>
                                            <form id="delete-kpi-{{ $item->id }}" action="{{ route('kpi.destroy', $item->id) }}" method="POST" style="display: none;">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                        </div>
                        <div class="tab-pane fade" id="custom-nav-profile" role="tabpanel" aria-labelledby="custom-nav-profile-tab">
                            <div id="mainContainer">
                                <div class="row" id="chartsRow"></div>
                            </div>
                            <div class="text-right mb-4">
                                {{-- button or else --}}
                                <small><i class="ti-fullscreen"></i>
                                    <a href="#" onclick="toggleFullScreen('mainContainer')">&nbsp;<i>Fullscreen</i></a>
                                </small>
                            </div>
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
                <div class="modal-body">
                    <div class="col-md-12 zoom90">
                        <div class="form-group">
                            <label for="kpi">KPI</label>
                            <input type="text" class="form-control" id="kpi" name="kpi" placeholder="Yearly Revenue..." required>
                        </div>
                        <div class="form-group">
                            <label for="target">Target</label>
                            <input type="text" class="form-control" id="target" name="target" required>
                        </div>
                        <div class="form-group">
                            <label for="periode">Periode</label>
                            <select class="form-control" id="periode" name="periode" required>
                                @foreach (array_reverse($yearsBefore) as $year)
                                    <option value="{{ $year }}">{{ $year }}</option>
                                @endforeach
                            </select>
                        </div>
                        <label for="target">KPI Logo <small>Optional</small></label>
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

        const kpiData = @json($chartData);

        const chartsRow = document.getElementById('chartsRow');

        kpiData.forEach((kpi, index) => {
            // Create a new div for each KPI chart
            const colDiv = document.createElement('div');
            colDiv.className = 'col-lg-6 col-md-6 mb-2';

            const cardDiv = document.createElement('div');
            cardDiv.className = 'card';

            const cardBodyDiv = document.createElement('div');
            cardBodyDiv.className = 'card-body';

            const title = document.createElement('h5');
            title.className = 'card-title';
            title.innerText = kpi.title + " {{ $yearSelected }}";

            const canvas = document.createElement('canvas');
            canvas.id = 'chart' + index;
            canvas.onclick = function() {
                redirectToKpi(kpi.id);
            };

            // Append elements
            cardBodyDiv.appendChild(title);
            cardBodyDiv.appendChild(canvas);
            cardDiv.appendChild(cardBodyDiv);
            colDiv.appendChild(cardDiv);
            chartsRow.appendChild(colDiv);

            // Create the chart
            new Chart(canvas.getContext('2d'), {
                type: kpi.type,
                data: {
                    labels: kpi.labels,
                    datasets: [{
                        label: kpi.title + " {{ $yearSelected }}",
                        data: kpi.data,
                        backgroundColor: kpi.backgroundColor,
                        borderColor: kpi.borderColor,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    animation: {
                        duration: 1000,
                        easing: 'easeInOutBounce'
                    },
                    scales: {
                        yAxes: [{
                            ticks: {
                                beginAtZero: true,
                                min: 0
                            }
                        }]
                    }
                }
            });
        });

        window.redirectToKpi = function(kpiId) {
            const url = `/key-performance-indicators/achievements/${kpiId}`;
            window.location.href = url;
        };
    });

    function toggleFullScreen(elementId) {
        var element = document.getElementById(elementId);

        if (!document.fullscreenElement) {
            // Request fullscreen
            if (element.requestFullscreen) {
                element.requestFullscreen();
            } else if (element.mozRequestFullScreen) { // Firefox
                element.mozRequestFullScreen();
            } else if (element.webkitRequestFullscreen) { // Chrome, Safari and Opera
                element.webkitRequestFullscreen();
            } else if (element.msRequestFullscreen) { // IE/Edge
                element.msRequestFullscreen();
            }

            // Set scrolling styles
            element.style.overflow = 'auto'; // Enable scrolling if needed
        } else {
            // Exit fullscreen
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.mozCancelFullScreen) { // Firefox
                document.mozCancelFullScreen();
            } else if (document.webkitExitFullscreen) { // Chrome, Safari and Opera
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) { // IE/Edge
                document.msExitFullscreen();
            }

            // Reset scrolling styles
            element.style.overflow = ''; // Reset to default
        }
    }

    function confirmDelete(itemId) {
        event.preventDefault();
        swal({
            title: "Are you sure?",
            text: "Once deleted, you will not be able to recover this KPI!",
            Logo: "warning",
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                document.getElementById('delete-kpi-' + itemId).submit();
            }
        });
    }

    function redirectToPage() {
        var selectedOption = document.getElementById("yearSelected").value;
        var url = "{{ url('/key-performance-indicators/index') }}"; // Specify the base URL

        url += "/" + selectedOption;

        window.location.href = url; // Redirect to the desired page
    }
</script>
@endsection

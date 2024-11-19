@include('Admin.components.head', ['title' => 'UniOil Raffle Draw'])

<style>
    .form-select:focus {
        border-color: #ff2600;
        box-shadow: 0 0 0 0.25rem rgba(255, 136, 38, 0.25);
    }

    .form-select option {
        color: #fd7e14;
    }

    .form-select option:checked {
        background-color: #fd7e14;
        color: white;
    }

    .form-select option:hover {
        background-color: #e7f1ff;
    }
</style>

<body>
    <script src="{{ asset('./dist/js/demo-theme.min.js?1692870487') }}"></script>

    <div class="page">

        @include('Admin.components.header', ['active' => 'dashboard'])

        <div class="page-wrapper">
            <div class="page-header d-print-none">
                <div class="container-xl">
                    <div class="row g-2 align-items-center">
                        <div class="col">
                            <!-- Page pre-title -->
                            <div class="page-pretitle">
                                Overview
                            </div>
                            <h2 class="page-title">
                                Analytics Dashboard
                            </h2>
                        </div>
                        <!-- Page title actions -->
                        <div class="col-auto ms-auto d-print-none">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Page body -->
            <div class="page-body">
                <div class="container-xl">

                    <div class="row m-2">
                        {{-- Event Selection Card --}}
                        <div class="col-12">
                            <div class="card shadow">
                                <div class="card-body text-center">
                                    <h5 class="text-primary fw-bold mb-3">Select an Event to View Insights</h5>
                                    @php
                                        use App\Models\Event;
                                        $events = Event::all();
                                    @endphp
                                    <div class="form-group mx-auto" style="max-width: 400px;">
                                        <select class="form-select border-primary fw-semibold" 
                                                style="color: #ff3300;" 
                                                id="event-dropdown" 
                                                onchange="updateCharts(this.value)">
                                            <option selected disabled value="">Choose an Event</option>
                                            @if ($events->isEmpty())
                                                <option value="#" disabled>No events available</option>
                                            @else
                                                @foreach ($events as $event)
                                                    <option value="{{ $event->event_id }}">{{ $event->event_name }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row m-2">
                        {{-- DONUT CHART --}}
                        <div class="col-lg-6 col-xl-6">
                            <div class="card">
                                <div class="card-body">
                                    <h3 class="text-center">Product Type Breakdown</h3>
                                    <div id="chart-demo-pie"></div>
                                </div>
                            </div>
                        </div>
                    
                        {{-- BAR GRAPH --}}
                        <div class="col-lg-6 col-xl-6">
                            <div class="card">
                                <div class="card-body">
                                    <h3 class="text-center">Raffle Entries Issued by Product Type</h3>
                                    <div id="chart-tasks-overview1"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row m-2">
                        {{-- AREA CHART --}}
                        <div class="col-lg-6 col-xl-6">
                            <div class="card">
                                <div class="card-body">
                                    <h3 class="text-center">Raffle Entry Issuance Over Time</h3>
                                    <div id="chart-completion-tasks-10"></div>
                                </div>
                            </div>
                        </div>
                    
                        {{-- REGIONAL CLUSTER PARTICIPATION --}}
                        <div class="col-lg-6 col-xl-6">
                            <div class="card">
                                <div class="card-body">
                                    <h3 class="text-center">Regional Cluster Raffle Participation</h3>
                                    <div id="chart-tasks-overview"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        @include('Admin.components.footer')
    </div>
    </div>

    @include('Admin.components.scripts')


    <script src="/js/analytics.js"></script>


</body>

</html>

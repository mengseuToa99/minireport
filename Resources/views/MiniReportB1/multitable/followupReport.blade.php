@extends('minireportb1::layouts.master2')
@section('title', __('crm::lang.follow_ups'))
@section('content')


    <!-- Content Header (Page header) -->
    <section class="content-header no-print">
        <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">
            @isset($file_name)
                {{ $file_name }} <!-- Display the file name if it exists -->
            @else
                <h1 class="tw-text-xl md:tw-text-3xl tw-font-bold tw-text-black">@lang('crm::lang.follow_ups')</h1>
            @endisset
        </h1>
    </section>

    @if (!isset($file_name) || empty($file_name))
        @include('minireportb1::MiniReportB1.multitable.partials.dropdown')
    @endif

    <section class="content no-print">
        @component('components.filters', ['title' => __('report.filters')])
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('contact_id_filter', __('contact.contact') . ':') !!}
                        {!! Form::select('contact_id_filter', $contacts, null, [
                            'class' => 'form-control select2',
                            'form-control select2',
                            'style' => 'width: 100%;',
                            'id' => 'contact_id_filter',
                            'placeholder' => __('messages.all'),
                        ]) !!}
                    </div>
                </div>
                @if (auth()->user()->can('crm.access_all_schedule'))
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('assgined_to_filter', __('crm::lang.assgined') . ':') !!}
                            {!! Form::select('assgined_to_filter', $assigned_to, $default_user, [
                                'class' => 'form-control select2',
                                'form-control select2',
                                'style' => 'width: 100%;',
                                'id' => 'assgined_to_filter',
                                'placeholder' => __('messages.all'),
                            ]) !!}
                        </div>
                    </div>
                @endif
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('status_filter', __('sale.status') . ':') !!}
                        {!! Form::select('status_filter', $statuses, $default_status, [
                            'class' => 'form-control select2',
                            'form-control select2',
                            'style' => 'width: 100%;',
                            'id' => 'status_filter',
                            'placeholder' => __('messages.all'),
                        ]) !!}
                    </div>
                </div>
                <div class="clearfix">
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('schedule_type_filter', __('crm::lang.schedule_type') . ':') !!}
                        {!! Form::select('schedule_type_filter', $follow_up_types, null, [
                            'class' => 'form-control select2',
                            'form-control select2',
                            'style' => 'width: 100%;',
                            'id' => 'schedule_type_filter',
                            'placeholder' => __('messages.all'),
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('follow_up_date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('follow_up_date_range', null, [
                            'placeholder' => __('lang_v1.select_a_date_range'),
                            'class' => 'form-control',
                            'readonly',
                        ]) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('follow_up_by_filter', __('crm::lang.follow_up_by') . ':') !!}
                        {!! Form::select(
                            'follow_up_by_filter',
                            ['payment_status' => __('sale.payment_status'), 'orders' => __('restaurant.orders')],
                            null,
                            [
                                'class' => 'form-control select2',
                                'style' => 'width: 100%;',
                                'id' => 'follow_up_by_filter',
                                'placeholder' => __('messages.all'),
                            ],
                        ) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('followup_category_id_filter', __('crm::lang.followup_category') . ':') !!}
                        {!! Form::select('followup_category_id_filter', $followup_category, $default_followup_category_id, [
                            'class' => 'form-control select2',
                            'style' => 'width: 100%;',
                            'form-control select2',
                            'id' => 'followup_category_id_filter',
                            'placeholder' => __('messages.all'),
                        ]) !!}
                    </div>
                </div>
            </div>
        @endcomponent
        <div class="row">
            <div class="col-md-12">
                @component('components.widget', ['class' => 'box box-solid', 'title' => __('crm::lang.all_schedules')])
                    @slot('tool')
                        <div class="box-tools">
                        </div>
                        <input type="hidden" name="schedule_create_url" id="schedule_create_url"
                            value="{{ action([\Modules\Crm\Http\Controllers\ScheduleController::class, 'create']) }}">
                    @endslot
                    <div class="col-sm-12">
                        <div class="nav-tabs-custom">
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a href="#all_followup_tab" data-toggle="tab" aria-expanded="true"> @lang('crm::lang.follow_ups')</a>
                                </li>
                                <li>
                                    <a href="#recur_followup_tab" data-toggle="tab" aria-expanded="true"> @lang('crm::lang.recur_follow_ups')</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active" id="all_followup_tab">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="follow_up_table"
                                            style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th>@lang('messages.action')</th>
                                                    <th>
                                                        @lang('contact.contact')
                                                    </th>
                                                    <th>@lang('crm::lang.start_datetime')</th>
                                                    <th>@lang('crm::lang.end_datetime')</th>
                                                    <th>@lang('sale.status')</th>
                                                    <th>@lang('crm::lang.schedule_type')</th>
                                                    <th>@lang('crm::lang.followup_category')</th>
                                                    <th>@lang('lang_v1.assigned_to')</th>
                                                    <th>
                                                        @lang('crm::lang.description')
                                                    </th>
                                                    <th>
                                                        @lang('crm::lang.additional_info')
                                                    </th>
                                                    <th>@lang('crm::lang.title')</th>
                                                    <th>
                                                        @lang('lang_v1.added_by')
                                                    </th>
                                                    <th>
                                                        @lang('lang_v1.added_on')
                                                    </th>
                                                    <th>
                                                        Phone Number
                                                    </th>
                                                    <th>
                                                        Address
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody></tbody>
                                            <tfoot>
                                                <tr class="bg-gray font-17 footer-total text-center">
                                                    <td colspan="5">
                                                        <strong>@lang('sale.total'):</strong>
                                                    </td>
                                                    <td class="footer_follow_up_status_count"></td>
                                                    <td class="footer_follow_up_type_count"></td>
                                                    <td colspan="6"></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>

                                <div class="tab-pane" id="recur_followup_tab">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="recursive_follow_up_table"
                                            style="width: 100%">
                                            <thead>
                                                <tr>
                                                    <th>@lang('messages.action')</th>
                                                    <th>@lang('sale.status')</th>
                                                    <th>@lang('crm::lang.schedule_type')</th>
                                                    <th>@lang('crm::lang.followup_category')</th>
                                                    <th>@lang('crm::lang.follow_up_by')</th>
                                                    <th>@lang('crm::lang.in_days')</th>
                                                    <th>@lang('lang_v1.assigned_to')</th>
                                                    <th>
                                                        @lang('crm::lang.description')
                                                    </th>
                                                    <th>
                                                        @lang('crm::lang.additional_info')
                                                    </th>
                                                    <th>@lang('crm::lang.title')</th>
                                                    <th>
                                                        @lang('lang_v1.added_by')
                                                    </th>
                                                    <th>
                                                        @lang('lang_v1.added_on')
                                                    </th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endcomponent
            </div>
        </div>
    </section>

    <div class="modal fade schedule" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
    <div class="modal fade edit_schedule" tabindex="-1" role="dialog"></div>

    <div class="modal fade schedule_log_modal" tabindex="-1" role="dialog"></div>

    @include('crm::schedule.partial.advance_followup_modal')
@endsection

<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@section('javascript')
    <script src="{{ asset('modules/crm/js/crm.js?v=' . $asset_v) }}"></script>
    <script type="text/javascript">
        var visibleColumnNames = @json($visibleColumnNames ?? []); // This is correct
        var isViewMode = @json(isset($file_name));
        var tab = @json($tab ?? '');

        // Define tablename and reportName initially based on which tab is active on page load
        var initialTab = $('.nav-tabs li.active a').attr('href');
        var tablename = initialTab === '#recur_followup_tab' ? '#recursive_follow_up_table' : '#follow_up_table';
        var reportName = initialTab === '#recur_followup_tab' ? 'recursiveFollowupReport' : 'followupReport';


        // Update tablename and reportName when tabs are switched
        $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function(e) {
            var target = $(e.target).attr("href");
            if (target === '#recur_followup_tab') {
                tablename = '#recursive_follow_up_table';
                reportName = 'recursiveFollowupReport';
            } else {
                tablename = '#follow_up_table';
                reportName = 'followupReport';
            }
        });


        //hide tab
        if (isViewMode) {
            // Hide all tab links except the active one
            $('.nav-tabs li:not(.active)').hide();

            // Make the active tab unclickable
            $('.nav-tabs li.active a').css('cursor', 'default')
                .on('click', function(e) {
                    e.preventDefault();
                    return false;
                });

        }



        // show what tab to show 
        if (tab === 'followupReport') {
            // Remove active class from default tab
            document.querySelector('.nav-tabs li.active').classList.remove('active');
            document.querySelector('#all_followup_tab').classList.remove('active');

            // Add active class to followup tab
            document.querySelector('a[href="#all_followup_tab"]').parentElement.classList.add('active');
            document.querySelector('#all_followup_tab').classList.add('active');

        } else if (tab === 'recursiveFollowupReport') {
            // Remove active class from default tab
            document.querySelector('.nav-tabs li.active').classList.remove('active');
            document.querySelector('#all_followup_tab').classList.remove('active');

            // Add active class to recurring followup tab
            document.querySelector('a[href="#recur_followup_tab"]').parentElement.classList.add('active');
            document.querySelector('#recur_followup_tab').classList.add('active');
        }

        var filterCriteria = @json($filterCriteria ?? []);
        const dateFormat = moment_date_format; // Assuming this is defined globally
        const dateSeparator = ' - ';


        $(function() {
            $('#follow_up_date_range').daterangepicker(
                dateRangeSettings,
                function(start, end) {
                    $('#follow_up_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(
                        moment_date_format));
                    follow_up_datatable.ajax.reload();
                }
            );
            $('#follow_up_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#follow_up_date_range').val('');
                follow_up_datatable.ajax.reload();
            });
            $('#followup_category_id_filter').change(function() {
                follow_up_datatable.ajax.reload();
            })

            follow_up_datatable = $("#follow_up_table").DataTable({
                processing: true,
                serverSide: true,
                scrollY: "80vh",
                scrollX: true,
                scrollCollapse: true,
                ajax: {
                    url: "/crm/follow-ups",
                    data: function(d) {
                        d.contact_id = $("#contact_id_filter").val();
                        d.assgined_to = $("#assgined_to_filter").val();
                        d.status = $("#status_filter").val();
                        d.schedule_type = $("#schedule_type_filter").val();
                        d.follow_up_by = $("#follow_up_by_filter").val();
                        d.followup_category_id = $("#followup_category_id_filter").val();

                        if ($('#follow_up_date_range').val()) {
                            d.start_date_time = $('#follow_up_date_range').data('daterangepicker')
                                .startDate.format('YYYY-MM-DD');
                            d.end_date_time = $('#follow_up_date_range').data('daterangepicker').endDate
                                .format('YYYY-MM-DD');
                        }
                    }
                },
                columnDefs: [{
                    targets: [0, 7, 9],
                    orderable: false,
                    searchable: false,
                }, ],
                aaSorting: [
                    [2, 'desc']
                ],
                columns: [{
                        data: 'action',
                        name: 'action',
                        orderable: false, // Actions typically aren't orderable
                        searchable: false, // Actions typically aren't searchable
                        visible: isViewMode ? visibleColumnNames.includes("Action") ||
                            visibleColumnNames.includes("លំអិត") : true // Matches "លំអិត" (Detail)
                    },
                    {
                        data: 'contact',
                        name: 'contacts.name',
                        visible: isViewMode ? visibleColumnNames.includes("Customer") ||
                            visibleColumnNames.includes("អតិថិជន") :
                            true // Matches "អតិថិជន" (Customer)
                    },
                    {
                        data: 'start_datetime',
                        name: 'start_datetime',
                        visible: isViewMode ? visibleColumnNames.includes("Start Datetime") ||
                            visibleColumnNames.includes("Start Datetime") :
                            true // Matches "Start Datetime"
                    },
                    {
                        data: 'end_datetime',
                        name: 'end_datetime',
                        visible: isViewMode ? visibleColumnNames.includes("End Datetime") ||
                            visibleColumnNames.includes("End Datetime") : true // Matches "End Datetime"
                    },
                    {
                        data: 'status',
                        name: 'crm_schedules.status',
                        visible: isViewMode ? visibleColumnNames.includes("Status") ||
                            visibleColumnNames.includes("ស្ថានភាពប្រាក់") :
                            true // Matches "ស្ថានភាពប្រាក់" (Payment Status)
                    },
                    {
                        data: 'schedule_type',
                        name: 'schedule_type',
                        visible: isViewMode ? visibleColumnNames.includes("Follow Up Type") ||
                            visibleColumnNames.includes("Follow Up Type") :
                            true // Matches "Follow Up Type"
                    },
                    {
                        data: 'followup_category',
                        name: 'C.name',
                        visible: isViewMode ? visibleColumnNames.includes("Followup Category") ||
                            visibleColumnNames.includes("Followup Category") :
                            true // Matches "Followup Category"
                    },
                    {
                        data: 'users',
                        name: 'users',
                        visible: isViewMode ? visibleColumnNames.includes("Users") ||
                            visibleColumnNames.includes("តាមរយៈបុគ្គលិក") :
                            true // Matches "តាមរយៈបុគ្គលិក" (By Staff)
                    },
                    {
                        data: 'description',
                        name: 'description',
                        visible: isViewMode ? visibleColumnNames.includes("Description") ||
                            visibleColumnNames.includes("Description") : true // Matches "Description"
                    },
                    {
                        data: 'additional_info',
                        name: 'additional_info',
                        visible: isViewMode ? visibleColumnNames.includes("Additional Info") ||
                            visibleColumnNames.includes("Additional info") :
                            true // Matches "Additional info"
                    },
                    {
                        data: 'title',
                        name: 'title',
                        visible: isViewMode ? visibleColumnNames.includes("Title") ||
                            visibleColumnNames.includes("Title") : true // Matches "Title"
                    },
                    {
                        data: 'added_by',
                        name: 'added_by',
                        visible: isViewMode ? visibleColumnNames.includes("Added By") ||
                            visibleColumnNames.includes("ទិន្នន័យ") : true // Matches "ទិន្នន័យ" (Data)
                    },
                    {
                        data: 'added_on',
                        name: 'crm_schedules.created_at',
                        visible: isViewMode ? visibleColumnNames.includes("Added On") ||
                            visibleColumnNames.includes("ថ្ងៃខែ") : true // Matches "ថ្ងៃខែ" (Date)
                    },
                    {
                        data: 'phone_number',
                        name: 'contact.phone_number',
                        visible: isViewMode ? visibleColumnNames.includes("Phone Number") ||
                            visibleColumnNames.includes("Phone Number") : true // Matches "Phone Number"
                    },
                    {
                        data: 'address',
                        name: 'contact.address',
                        visible: isViewMode ? visibleColumnNames.includes("Address") ||
                            visibleColumnNames.includes("Address") : true // Matches "Address"
                    }
                ],
                "fnDrawCallback": function(oSettings) {
                    __show_date_diff_for_human($("#follow_up_table"));

                    $('a.view_schedule_log').click(function() {
                        getScheduleLog($(this).data('schedule_id'), true);
                    })
                },
                "footerCallback": function(row, data, start, end, display) {
                    $('.footer_follow_up_status_count').html(__count_status(data, 'status'));
                    $('.footer_follow_up_type_count').html(__count_status(data, 'schedule_type'));
                }
            });

            recursive_follow_up_table = $("#recursive_follow_up_table").DataTable({
                processing: true,
                serverSide: true,
                scrollY: "80vh",
                scrollX: true,
                scrollCollapse: true,
                ajax: {
                    url: "/crm/follow-ups",
                    data: function(d) {
                        d.assgined_to = $("#assgined_to_filter").val();
                        d.is_recursive = 1;
                    }
                },
                aaSorting: [
                    [2, 'desc'] // Sort by 'schedule_type' (index 2) in descending order
                ],
                columns: [{
                        data: 'action',
                        name: 'action',
                        orderable: false, // From columnDefs targets: [0, 6]
                        searchable: false, // From columnDefs targets: [0, 6]
                        visible: isViewMode ? visibleColumnNames.includes("Action") ||
                            visibleColumnNames.includes("លំអិត") : true // Matches "លំអិត" (Detail)
                    },
                    {
                        data: 'status',
                        name: 'crm_schedules.status',
                        visible: isViewMode ? visibleColumnNames.includes("Status") ||
                            visibleColumnNames.includes("ស្ថានភាពប្រាក់") :
                            true // Matches "ស្ថានភាពប្រាក់" (Payment Status)
                    },
                    {
                        data: 'schedule_type',
                        name: 'schedule_type',
                        visible: isViewMode ? visibleColumnNames.includes("Follow Up Type") ||
                            visibleColumnNames.includes("Follow Up Type") :
                            true // Matches "Follow Up Type"
                    },
                    {
                        data: 'followup_category',
                        name: 'C.name',
                        visible: isViewMode ? visibleColumnNames.includes("Followup Category") ||
                            visibleColumnNames.includes("Followup Category") :
                            true // Matches "Followup Category"
                    },
                    {
                        data: 'follow_up_by',
                        name: 'crm_schedules.follow_up_by',
                        visible: isViewMode ? visibleColumnNames.includes("Follow up by") ||
                            visibleColumnNames.includes("Follow up by") : true // Matches "Follow up by"
                    },
                    {
                        data: 'recursion_days',
                        name: 'crm_schedules.recursion_days',
                        visible: isViewMode ? visibleColumnNames.includes("In days") ||
                            visibleColumnNames.includes("In days") : true // Matches "In days"
                    },
                    {
                        data: 'users',
                        name: 'users',
                        orderable: false, // From columnDefs targets: [0, 6]
                        searchable: false, // From columnDefs targets: [0, 6]
                        visible: isViewMode ? visibleColumnNames.includes("Users") ||
                            visibleColumnNames.includes("តាមរយៈបុគ្គលិក") :
                            true // Matches "តាមរយៈបុគ្គលិក" (By Staff)
                    },
                    {
                        data: 'description',
                        name: 'description',
                        visible: isViewMode ? visibleColumnNames.includes("Description") ||
                            visibleColumnNames.includes("Description") : true // Matches "Description"
                    },
                    {
                        data: 'additional_info',
                        name: 'additional_info',
                        visible: isViewMode ? visibleColumnNames.includes("Additional info") ||
                            visibleColumnNames.includes("Additional info") :
                            true // Matches "Additional info"
                    },
                    {
                        data: 'title',
                        name: 'title',
                        visible: isViewMode ? visibleColumnNames.includes("Title") ||
                            visibleColumnNames.includes("Title") : true // Matches "Title"
                    },
                    {
                        data: 'added_by',
                        name: 'added_by',
                        visible: isViewMode ? visibleColumnNames.includes("Added By") ||
                            visibleColumnNames.includes("ទិន្នន័យ") : true // Matches "ទិន្នន័យ" (Data)
                    },
                    {
                        data: 'added_on',
                        name: 'crm_schedules.created_at',
                        visible: isViewMode ? visibleColumnNames.includes("Added On") ||
                            visibleColumnNames.includes("ថ្ងៃខែ") : true // Matches "ថ្ងៃខែ" (Date)
                    }
                ]
            });

            $(document).on('change',
                '#contact_id_filter, #assgined_to_filter, #status_filter, #schedule_type_filter, #follow_up_by_filter',
                function() {
                    follow_up_datatable.ajax.reload();
                });

            // Set default date from get parameter
            @if (!empty($default_start_date) && !empty($default_end_date))
                $('#follow_up_date_range').val({{ $default_start_date . ' - ' . $default_end_date }});
                $('#follow_up_date_range').data('daterangepicker').setStartDate('{{ $default_start_date }}');
                $('#follow_up_date_range').data('daterangepicker').setEndDate('{{ $default_end_date }}');
                follow_up_datatable.ajax.reload();
            @endif

        });


        //apply filter
        // Apply saved filters
        function applySavedFilters() {
            if (!isViewMode) return;

            console.log('Applying saved filters:', filterCriteria);

            // Determine which DataTable to reload based on the active tab
            const activeTab = $('.nav-tabs li.active a').attr('href');
            const datatable = activeTab === '#recur_followup_tab' ? recursive_follow_up_table : follow_up_datatable;

            // Apply date range filter
            if (filterCriteria.dateRange) {
                const [startDate, endDate] = filterCriteria.dateRange.split(dateSeparator);
                if (moment(startDate, dateFormat).isValid() && moment(endDate, dateFormat).isValid()) {
                    console.log('Setting date range:', filterCriteria.dateRange);
                    $('#follow_up_date_range').val(filterCriteria.dateRange);
                    $('#follow_up_date_range').data('daterangepicker').setStartDate(startDate);
                    $('#follow_up_date_range').data('daterangepicker').setEndDate(endDate);
                }
            }

            // Map filter keys to their corresponding DOM selectors
            const filterMap = {
                contactId: '#contact_id_filter', // Added missing contactId
                assignedTo: '#assgined_to_filter',
                status: '#status_filter',
                scheduleType: '#schedule_type_filter',
                dateRange: '#follow_up_date_range',
                followUpBy: '#follow_up_by_filter',
                followupCategoryId: '#followup_category_id_filter',
            };

            // Apply filters from filterCriteria to the DOM elements
            Object.entries(filterMap).forEach(([key, selector]) => {
                if (filterCriteria[key]) {
                    console.log(`Applying filter ${key}:`, filterCriteria[key]);
                    if (key === 'dateRange') {
                        // Date range is already handled above, skip triggering change
                    } else {
                        $(selector).val(filterCriteria[key]).trigger('change');
                    }
                }
            });

            // Manually reload the appropriate DataTable
            console.log('Reloading DataTable...');
            datatable.ajax.reload();
        }

        // Remove redundant initializeDateRangePicker and validateAndApplyDateRange functions
        // The existing date range picker initialization in $(function() {...}) is sufficient

        // Update document.ready to only call applySavedFilters
        $(document).ready(function() {
            applySavedFilters(); // Apply saved filters
        });

        // Initialize date range picker (required for date range filter)
        function initializeDateRangePicker() {
            $('#sell_list_filter_date_range').daterangepicker(
                dateRangeSettings, // Assuming this is defined globally
                function(start, end) {
                    const displayDate = start.format(dateFormat) + dateSeparator + end.format(dateFormat);
                    $(this).val(displayDate);
                    validateAndApplyDateRange(start, end);
                }
            ).on('cancel.daterangepicker', function(ev) {
                $(this).val('');
                sell_table.ajax.reload(); // Assuming sell_table is the DataTable instance
            });
        }

        // Validate and apply date range (helper function for date range picker)
        function validateAndApplyDateRange(start, end) {
            if (start.isValid() && end.isValid()) {
                sell_table.ajax.reload(); // Reload DataTable with new date range
            } else {
                toastr.error('Invalid date range selected');
                $(this).val('');
            }
        }

        // Call the function to apply filters when the page loads
        $(document).ready(function() {
            initializeDateRangePicker(); // Initialize date range picker
            applySavedFilters(); // Apply saved filters
        })
    </script>
@endsection

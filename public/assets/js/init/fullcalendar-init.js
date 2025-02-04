!function($) {
    "use strict";

    var CalendarApp = function() {
        this.$body = $("body")
        this.$modal = $('#event-modal'),
        this.$event = ('#external-events div.external-event'),
        this.$calendar = $('#calendar'),
        this.$saveCategoryBtn = $('.save-category'),
        this.$categoryForm = $('#add-category form'),
        this.$extEvents = $('#external-events'),
        this.$calendarObj = null
    };


    /* on drop */
    CalendarApp.prototype.onDrop = function (eventObj, date) {
        var $this = this;
        var originalEventObject = eventObj.data('eventObject');
        var $categoryClass = eventObj.attr('data-class');
        var copiedEventObject = $.extend({}, originalEventObject);
        copiedEventObject.start = date;
        if ($categoryClass)
            copiedEventObject['className'] = [$categoryClass];
        $this.$calendar.fullCalendar('renderEvent', copiedEventObject, true);
        if ($('#drop-remove').is(':checked')) {
            eventObj.remove();
        }
    },

    CalendarApp.prototype.enableDrag = function() {
        $(this.$event).each(function () {
            var eventObject = {
                title: $.trim($(this).text())
            };
            $(this).data('eventObject', eventObject);
            $(this).draggable({
                zIndex: 999,
                revert: true,
                revertDuration: 0
            });
        });
    }
    /* Initializing */
    CalendarApp.prototype.init = function() {
        this.enableDrag();

        var $this = this;
        $this.$calendarObj = $this.$calendar.fullCalendar({
            slotDuration: '00:15:00',
            minTime: '08:00:00',
            maxTime: '19:00:00',
            defaultView: 'month',
            handleWindowResize: true,
            height: $(window).height(),
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            editable: true,
            droppable: true,
            eventLimit: 2,
            selectable: true,
            displayEventTime: false,

            // Fetch events dynamically when navigating months/years
            events: function(start, end, timezone, callback) {
                let startDate = start.format('YYYY-MM-DD'); // First day of visible month
                let endDate = end.format('YYYY-MM-DD'); // Last day of visible month

                $.ajax({
                    url: '/api/get-penlat-batch-events',
                    type: 'GET',
                    data: { start_date: startDate, end_date: endDate }, // Pass date range
                    dataType: 'json',
                    contentType: 'application/json',
                    cache: false,
                    success: function(data) {
                        var events = data.map(event => ({
                            title: event.title,
                            start: moment(event.start).toDate(),
                            end: moment(event.end).toDate(),
                            className: event.className,
                            extendedProps: {
                                batch: event.batch // Ensure batch data is available
                            }
                        }));
                        callback(events);
                    },
                    error: function(xhr, status, error) {
                        console.error("Error fetching events:", error);
                    }
                });
            },

            eventRender: function(event, element) {
                let batch = event.title ? event.title : 'Unknown'; // Ensure batch data
                let date = moment(event.start).format('YYYY-MM-DD'); // Get event date

                // Predefined colors
                const colors = ['#2980b9', '#CC9900'];
                const randomColor = colors[new Date(event.start).getDate() % colors.length];

                // Modify event element
                element.css({
                    'background-color': randomColor,
                    'cursor': 'pointer',
                    'padding': '5px',
                    'border-radius': '5px',
                    'color': '#fff' // Ensure text is visible
                });

                element.html(`
                    <div class="event-container" data-batch="${batch}" data-date="${date}">
                        <div class="event-title"><strong>${event.title}</strong></div>
                    </div>
                `);
            },

            drop: function(date) { $this.onDrop($(this), date); }
        });
    },

    //init CalendarApp
    $.CalendarApp = new CalendarApp, $.CalendarApp.Constructor = CalendarApp

}(window.jQuery),

//initializing CalendarApp
function($) {
    "use strict";
    $.CalendarApp.init()
}(window.jQuery);

document.addEventListener('DOMContentLoaded', function () {
    let dataTable;

    document.addEventListener('click', function (event) {
        if (event.target.closest('.event-container')) {
            let eventEl = event.target.closest('.event-container');
            let batch = eventEl.dataset.batch;
            let date = eventEl.dataset.date;

            console.log('Clicked Event:', batch, date);

            // Destroy previous DataTable instance if exists
            if ($.fn.DataTable.isDataTable('#infografis-table')) {
                $('#infografis-table').DataTable().destroy();
            }

            // Initialize DataTable
            dataTable = $('#infografis-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/api/get-infografis-peserta',
                    type: 'GET',
                    data: { batch: batch } // Send batch as a parameter
                },
                columns: [
                    { data: 'tgl_pelaksanaan', title: 'Tgl Pelaksanaan' },
                    { data: 'nama_peserta', title: 'Nama Peserta' },
                    { data: 'batch', title: 'Batch' },
                    { data: 'jenis_pelatihan', title: 'Jenis Pelatihan' },
                    { data: 'kategori_program', title: 'Kategori Program' },
                    { data: 'harga_pelatihan', title: 'Harga Pelatihan' },
                    { data: 'realisasi', title: 'Realisasi' }
                ],
                lengthMenu: [10, 25, 50, 100],
                pageLength: 10,
                responsive: true,
                autoWidth: false
            });

            // Show the modal
            $('#event-modal').modal('show');
        }
    });
});

/*
 * AUTHOR: ALEXANDROS KOURTIS
 */

// css & scss

require('@fullcalendar/core/main.min.css');
require('@fullcalendar/daygrid/main.min.css');
require('@fullcalendar/timegrid/main.min.css');

// js

import {Calendar} from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import 'marker-clusterer/marker-clusterer.js';

// require('@fullcalendar/core/locales-all.min.js');

$(document).ready(function () {

    // Initializes events calendar
    if ($('#events-calendar').length) {
        var calendarEl = document.getElementById('events-calendar');

        var calendar = new Calendar(calendarEl, {
            plugins: ['interaction', dayGridPlugin, timeGridPlugin],
            defaultView: 'dayGridMonth',
            defaultDate: $('#events-calendar').data('default-date'),
            height: "auto",

            /*customButtons: {
             alleventscalendarlink: {
             text: 'All events calendar',
             click: function () {
             }
             }
             },*/
            header: {
                left: 'prev,next today alleventscalendarlink',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            buttonText: {
                today: "Today",
                day: "Day",
                week: "Week",
                month: "Month"
            },
            events: $('#events-calendar').data('events')
        });

        calendar.render();

    }

});
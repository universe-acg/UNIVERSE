document.addEventListener('DOMContentLoaded', function () {
    var schedule = JSON.parse(document.getElementById('booking-availability').textContent);
    var date = document.getElementById('athletic_booking_bookingDate');
    var duration = document.getElementById('athletic_booking_duration');
    var time = document.getElementById('athletic_booking_bookingTime');
    var status = document.getElementById('booking-status');
    if (!date || !duration || !time || !Array.isArray(schedule)) return;
    var parts = new Intl.DateTimeFormat('en', {timeZone: 'Europe/Athens', year: 'numeric', month: '2-digit', day: '2-digit'}).formatToParts(new Date());
    var part = function (type) { return parts.find(function (item) { return item.type === type; }).value; };
    var today = part('year') + '-' + part('month') + '-' + part('day');
    schedule = schedule.filter(function (day) { return day.dateOfDay >= today; });
    var allowedDurations = new Set(schedule.flatMap(function (day) { return day.durations.map(function (slot) { return String(slot.duration); }); }));
    Array.from(duration.options).forEach(function (option) { option.disabled = !allowedDurations.has(option.value); });
    if (!allowedDurations.has(duration.value)) duration.value = Array.from(allowedDurations)[0] || '';
    var dates = schedule.map(function (day) { return day.dateOfDay; }).sort();
    if (dates.length) { date.min = dates[0]; date.max = dates[dates.length - 1]; }
    function updateTimes() {
        var previous = time.value;
        var day = schedule.find(function (item) { return item.dateOfDay === date.value; });
        var slot = day?.durations.find(function (item) { return String(item.duration) === duration.value; });
        var times = slot?.startTimes || [];
        time.replaceChildren(new Option(date.value ? 'Select a start time' : 'Choose a date first', ''));
        times.forEach(function (value) { time.add(new Option(value, value)); });
        if (times.includes(previous)) time.value = previous;
        status.textContent = !date.value ? '' : times.length ? times.length + ' start times available.' : 'No times available for this date and duration. Choose another date or duration.';
    }
    date.addEventListener('change', updateTimes);
    duration.addEventListener('change', updateTimes);
    updateTimes();
});

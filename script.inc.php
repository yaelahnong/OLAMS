<script src="javascripts/bootstrap.min.js"></script>
<script src="javascripts/popper.min.js"></script>
<script src="javascripts/app.min.js"></script>
<script src="javascripts/custom.js"></script>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="http://momentjs.com/downloads/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.6.1/fullcalendar.min.js"></script>

<script>
  $('#calendar').fullCalendar({
  header: {
    left: 'prev, today',
    center: 'title',
    right: 'next',
  }
});

  $('#calendar').fullCalendar({
    weekends: true, 
});
$('#calendar').fullCalendar({
  defaultView: 'agendaWeek'
});

</script>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<script>
        $(document).ready(function () {
            $("#datepickerStart").datepicker();

            $("#datepickerFinish").datepicker();
        });
    </script>
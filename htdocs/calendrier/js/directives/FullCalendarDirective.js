planningPHPTourApp.directive('fullcalendar', function() {
    return {
        restrict: 'E',
        template: '<div id="calendar"></div>',
        scope: true,
        link: function (scope, el, attrs) {
            var config = {
                header: {
                    left: '',
                    center: '',
                    right: ''
                },
                year: parseInt($('body').data('event-from-year'), 10),
                month: parseInt($('body').data('event-from-month'), 10) - 1,
                date: parseInt($('body').data('event-from-date'), 10),
                defaultView: "agendaWeek",
                weekends:false,
                hiddenDays: [ 1, 2, 3, 6, 7 ],
                editable: false,
                allDaySlot:false,
                slotMinutes:15,
                firstHour:8,
                minTime:8,
                maxTime:19,
                slotEventOverlap: false,
                h: 850,
                timeFormat: 'HH:mm { - HH:mm}',
                columnFormat: {
                    week: 'dddd dd MMMM'
                },
                axisFormat: 'HH:mm',
                dayNames: ['Dimanche','Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi'],
                dayNamesShort: ['Dim','Lun','Mar','Mer','Jeu','Thu','Ven','Sam'],
                monthNames : ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
                eventClick: function(event, jsEvent, view) {
                    location.hash = "#" + event.id;
                    angular.element(".profile").removeClass('calendarSelect');
                    angular.element("#" + event.id).parent().addClass('calendarSelect');
                },
                eventRender: function(event, element) {
                },
                viewDisplay: function(calendarView) {
                    calendarView.setHeight(9999);
                },
                windowResize: function(view) {
                    $('#calendar').fullCalendar('option', 'height', angular.element('.agenda')[0].offsetHeight);
                }
            };

            angular.element('#calendar').fullCalendar(config);

            scope.$parent.$watch('events', function(events) {
                angular.element('#calendar').fullCalendar('addEventSource', scope.$parent.events ,'stick');
                scope.$parent.loadSelectedConf();
            });

            scope.$parent.$watch('hideSession', function() {
                scope.$parent.refreshView();
            });
        }
    }
});

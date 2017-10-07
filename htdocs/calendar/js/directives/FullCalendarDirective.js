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
                year: 2016,
                month: 09,
                date: 27,
                defaultView: "agendaWeek",
                weekends:false,
                hiddenDays: [ 1, 2, 3, 6, 7 ],
                editable: false,
                allDaySlot:false,
                slotMinutes:15,
                firstHour:9,
                minTime:9,
                maxTime:18,
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

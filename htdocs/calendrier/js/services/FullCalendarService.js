planningPHPTourApp.service('fullCalendarService', function(){
    this.changeClassEvent = function(id, className){
        var calendar = angular.element('#calendar');
        var confEvent = calendar.fullCalendar('clientEvents', id)[0];
        var currentClass = confEvent.className;
        confEvent.className = className;
        
        if (currentClass.lastIndexOf("filtredEvent") != -1) {
            confEvent.className += " filtredEvent";
        }

        calendar.fullCalendar('updateEvent', confEvent);
    };

    this.rerenderCalendar = function(){
        var calendar = angular.element('#calendar');
        calendar.fullCalendar( 'render' );
    };

    this.filtredEvent = function(id, isFiltred) {
        var calendar = angular.element('#calendar');
        var confEvent = calendar.fullCalendar('clientEvents', id)[0];

        if(confEvent != undefined) {
            if(typeof confEvent.className !== 'string') {
                confEvent.className = '';
            }

            if (isFiltred) {
                confEvent.className += " filtredEvent";
            } else {
                confEvent.className = confEvent.className.replace(" filtredEvent", ""); 
            }

            calendar.fullCalendar('updateEvent', confEvent);
        }
    }

    this.parseDate = function (dateString) {
        return $.fullCalendar.parseDate(dateString);
    }

    this.getEventList = function (confs) {
        var self = this;
        var calendarEvents = [];

        angular.forEach(confs, function(conf, key) {
            calendarEvents.push(self.makeEvent(conf));
        });

        return calendarEvents;
    }

    this.makeEvent = function (conf) {
        var newEvent = new Object();
        var eventDateStart = conf.date_start.replace("Z", "");
        var eventDateEnd = conf.date_end.replace("Z", "");

        newEvent.id = conf.id;
        newEvent.className = 'defaultEvent';
        newEvent.title = conf.salle +' : '+ conf.name;
        newEvent.start = this.parseDate(conf.date_start);
        newEvent.end = this.parseDate(conf.date_end);
        newEvent.allDay = false;
        newEvent.eventBorderColor = 'black';

        return newEvent;
    }
});
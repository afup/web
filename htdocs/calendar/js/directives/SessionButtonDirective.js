planningPHPTourApp.directive('sessionBtn', function() {
    return {
        restrict: 'E',
        replace: true,
        scope: {
            conf: "=conf"
        },
        template: ' <button id="btnParticipate" type="button" ng-class="btnClass" data-session="{{conf.id}}" class="btn btn-sm addEvent" ng-click="toggle()">{{btnText}}</button>',
        link: function (scope, el, attrs) {
            scope.addClass    = 'btn-primary';
            scope.removeClass = 'btn-danger';
            scope.addText     = 'Je participe !';
            scope.removeText  = 'Je ne participe plus.';

            scope.btnClass = scope.addClass;
            scope.btnText  = scope.addText;
            attrs['data-id'] = scope.conf.id;

            scope.toggle = function(){
                var btnClass = scope.addClass;
                var btnText  = scope.addText;

                if (el.html() == scope.addText) {
                    btnClass = scope.removeClass;
                    btnText  = scope.removeText;
                }

                scope.btnClass =  btnClass;
                    scope.btnText  = btnText;

                scope.$parent.toggleSession(scope.conf);
            };
        }
    }
});

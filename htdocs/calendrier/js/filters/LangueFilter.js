planningPHPTourApp.filter('langueFilter', [function () {
    return function (confs, selectedLang) {
        if (selectedLang != null) {
            var result = [];
            
            angular.forEach(confs, function (conf) {
                if (conf.lang == selectedLang) {
                    result.push(conf);
                };
            });

            return result;
        } 

        return confs;
    };
}]);
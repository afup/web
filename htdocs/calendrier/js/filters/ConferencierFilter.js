planningPHPTourApp.filter('conferencierFilter', [function () {
    return function (confs, selectedConferencier) {
        // Si un conférencié est sélectionné
        if (selectedConferencier != null) {
            var resutl = [];
            angular.forEach(confs, function (conf) {
                for (key in conf.conferenciers) {
                    var conferencier = conf.conferenciers[key];
                    // On ajoute au résultat le nom du conférencié sélectionné
                    if (conferencier.name == selectedConferencier) {resutl.push(conf)};
                };
            });
            return resutl;
        } else {
            // Sinon on retourne tous les conférenciers
            return confs;
        }
    };
}]);